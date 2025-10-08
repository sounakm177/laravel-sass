<?php

namespace App\Services;

use App\Repositories\Interfaces\TenantRepositoryInterface;
use App\Models\Subscription;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription as StripeSubscription;
use Stripe\PaymentMethod;
use Stripe\Exception\ApiErrorException;
use Exception;
use App\Models\TenantPaymentMethod;

class StripeSubscriptionService
{
    public function __construct(
        protected TenantRepositoryInterface $tenantRepository
    ) {
        Stripe::setApiKey(config('stripe.secret'));
    }

    /**
     * Create Stripe customer if not exists
     */
    public function createOrGetCustomer($tenant)
    {
        if ($tenant->stripe_customer_id) {
            return $tenant->stripe_customer_id;
        }

        $customer = Customer::create([
            'email' => $tenant->email,
            'name'  => $tenant->name,
        ]);

        return $customer->id;
    }

    /**
     * Attach payment method to customer
     */
    public function attachPaymentMethod($stripeCustomerId, $paymentMethodId)
    {
        $paymentMethod = PaymentMethod::retrieve($paymentMethodId);

        $paymentMethod->attach(['customer' => $stripeCustomerId]);

        Customer::update($stripeCustomerId, [
            'invoice_settings' => ['default_payment_method' => $paymentMethodId],
        ]);

        return $paymentMethod;
    }

    public function addPaymentMethod($tenantId, $paymentMethodId)
    {
        $tenant = $this->tenantRepository->getById($tenantId);

        // Create Stripe customer if not exists
        $stripeCustomerId = $this->createOrGetCustomer($tenant);

        $paymentMethod = PaymentMethod::retrieve($paymentMethodId);

        // 3. Attach PM to customer (only if not already attached)
        if ($paymentMethod->customer !== $stripeCustomerId) {
            $paymentMethod->attach(['customer' => $stripeCustomerId]);
        }

        // 4. Update customer default_payment_method
        Customer::update($stripeCustomerId, [
            'invoice_settings' => ['default_payment_method' => $paymentMethod->id],
        ]);


        // Store in DB
        $card = TenantPaymentMethod::create([
            'tenant_id' => $tenant->id,
            'stripe_payment_method_id' => $paymentMethod->id,
            'stripe_customer_id' => $stripeCustomerId,
            'brand' => $paymentMethod->card->brand ?? null,
            'last4' => $paymentMethod->card->last4 ?? null,
            'exp_month' => $paymentMethod->card->exp_month ?? null,
            'exp_year' => $paymentMethod->card->exp_year ?? null,
            'is_default' => true, // new card becomes default
        ]);

        // Make all other cards non-default
        TenantPaymentMethod::where('tenant_id', $tenant->id)
            ->where('id', '!=', $card->id)
            ->update(['is_default' => false]);

        return $card;
    }


    /**
     * Create a new subscription
     */
    public function createSubscription($tenantId, $priceId, $userId = null)
    {
        $tenant = $this->tenantRepository->getById($tenantId);

        // Get default card
        $defaultCard = TenantPaymentMethod::where('tenant_id', $tenant->id)
            ->where('is_default', true)
            ->first();

        if (!$defaultCard) {
            throw new Exception('No default payment method found for this tenant.');
        }

        $stripeCustomerId = $this->createOrGetCustomer($tenant);

        $this->attachPaymentMethod($stripeCustomerId, $defaultCard->stripe_payment_method_id);

        $stripeSubscription = StripeSubscription::create([
            'customer' => $stripeCustomerId,
            'items' => [['price' => $priceId]],
            'expand' => ['latest_invoice.payment_intent'],
            'payment_behavior' => 'default_incomplete',
        ]);

        // Store subscription in DB
        return Subscription::create([
            'tenant_id' => $tenant->id,
            'user_id' => $userId,
            'stripe_subscription_id' => $stripeSubscription->id,
            'stripe_customer_id' => $stripeCustomerId,
            'stripe_price_id' => $priceId,
            'status' => $stripeSubscription->status,
            'current_period_start' => isset($stripeSubscription->current_period_start)
                ? date('Y-m-d H:i:s', $stripeSubscription->current_period_start)
                : null,
            'current_period_end' => isset($stripeSubscription->current_period_end)
                ? date('Y-m-d H:i:s', $stripeSubscription->current_period_end)
                : null,
            'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end ?? false,
            'canceled_at' => $stripeSubscription->canceled_at ?? null,
        ]);
    }


    /**
     * Cancel subscription
     */
    public function cancelSubscription($subscriptionId, $atPeriodEnd = true)
    {
        $subscription = Subscription::findOrFail($subscriptionId);

        $stripeSubscription = StripeSubscription::retrieve($subscription->stripe_subscription_id);
        $stripeSubscription->cancel([
            'invoice_now' => false,
            'prorate' => false,
        ]);

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'cancel_at_period_end' => $atPeriodEnd,
        ]);

        return $subscription;
    }

    /**
     * Upgrade/Downgrade subscription
     */
    public function updateSubscription($subscriptionId, $newPriceId)
    {
        $subscription = Subscription::findOrFail($subscriptionId);

        $stripeSubscription = StripeSubscription::retrieve($subscription->stripe_subscription_id);

        $itemId = $stripeSubscription->items->data[0]->id;

        $updatedStripeSubscription = StripeSubscription::update($stripeSubscription->id, [
            'items' => [
                ['id' => $itemId, 'price' => $newPriceId],
            ],
            'proration_behavior' => 'create_prorations',
        ]);

        $subscription->update([
            'stripe_price_id' => $newPriceId,
            'status' => $updatedStripeSubscription->status,
            'current_period_start' => date('Y-m-d H:i:s', $updatedStripeSubscription->current_period_start),
            'current_period_end' => date('Y-m-d H:i:s', $updatedStripeSubscription->current_period_end),
        ]);

        return $subscription;
    }

    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook($payload, $sigHeader, $endpointSecret)
    {
        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            $data = $event->data->object;

            switch ($event->type) {
                case 'invoice.payment_succeeded':
                    $this->updateSubscriptionStatusByCustomer($data->customer, 'active');
                    break;
                case 'invoice.payment_failed':
                    $this->updateSubscriptionStatusByCustomer($data->customer, 'past_due');
                    break;
                case 'customer.subscription.deleted':
                    $this->updateSubscriptionStatusByCustomer($data->customer, 'canceled');
                    break;
            }

            return true;

        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            throw new Exception('Webhook signature verification failed.');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Update subscription status by Stripe customer ID
     */
    private function updateSubscriptionStatusByCustomer($stripeCustomerId, $status)
    {
        $subscription = Subscription::where('stripe_customer_id', $stripeCustomerId)
            ->latest()
            ->first();

        if ($subscription) {
            $subscription->update(['status' => $status]);
        }
    }


    /**
     * Add card to tenant
     */
    public function addCardToTenant($tenantId = 1, $paymentMethodId)
    {
        $tenant = $this->tenantRepository->getById($tenantId);

        $stripeCustomerId = $this->createOrGetCustomer($tenant);

        $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
        $paymentMethod->attach(['customer' => $stripeCustomerId]);

        Customer::update($stripeCustomerId, [
            'invoice_settings' => ['default_payment_method' => $paymentMethodId],
        ]);

        $card = TenantPaymentMethod::create([
            'tenant_id' => $tenantId,
            'stripe_customer_id' => $stripeCustomerId,
            'stripe_payment_method_id' => $paymentMethod->id,
            'brand' => $paymentMethod->card->brand,
            'last4' => $paymentMethod->card->last4,
            'exp_month' => $paymentMethod->card->exp_month,
            'exp_year' => $paymentMethod->card->exp_year,
            'is_default' => true,
        ]);

        TenantPaymentMethod::where('tenant_id', $tenantId)
            ->where('id', '!=', $card->id)
            ->update(['is_default' => false]);

        return $card;
    }

    /**
     * List all cards
     */
    public function listTenantCards($tenantId)
    {
        return TenantPaymentMethod::where('tenant_id', $tenantId)
            ->orderByDesc('is_default')
            ->get();
    }

    /**
     * Set default card
     */
    public function setDefaultCard($tenantId, $paymentMethodId)
    {
        $card = TenantPaymentMethod::where('tenant_id', $tenantId)
            ->where('stripe_payment_method_id', $paymentMethodId)
            ->firstOrFail();

        TenantPaymentMethod::where('tenant_id', $tenantId)
            ->update(['is_default' => false]);

        $card->update(['is_default' => true]);

        // Update Stripe default payment method
        Customer::update($card->stripe_customer_id, [
            'invoice_settings' => ['default_payment_method' => $paymentMethodId],
        ]);

        return $card;
    }

}



// sumana barui
// 9:20 PM
// $request->validate([
//             'payment_method_id' => 'required|string',
//         ]);

//         $user = Auth::user();

//         try {
//             if (!$user->stripe_id) {
//                 $stripeCustomer = \Stripe\Customer::create([
//                     'name' => $user->userDetails->first_name,
//                 ]);

//                 $user->update(['stripe_id' => $stripeCustomer->id]);
//             }

//             $user->addPaymentMethod($request->input('payment_method_id'));

//             $stripePaymentMethod = StripePaymentMethod::retrieve($request->input('payment_method_id'));
//             $stripePaymentMethod->attach(['customer' => $user->stripe_id]);
//             PaymentMethod::create([
//                 'user_id' => $user->id,
//                 'stripe_id' => $stripePaymentMethod->id,
//                 'card_brand' => $stripePaymentMethod->card->brand,
//                 'card_last_four' => $stripePaymentMethod->card->last4,
//                 'card_exp_month' => $stripePaymentMethod->card->exp_month,
//                 'card_exp_year' => $stripePaymentMethod->card->exp_year,
//                 'billing_name' => $stripePaymentMethod->billing_details->name,
//             ]);
// imm-kvmz-hob