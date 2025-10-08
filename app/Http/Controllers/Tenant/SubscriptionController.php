<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StripeSubscriptionService;
use Illuminate\Support\Facades\Auth;
use Exception;

class SubscriptionController extends Controller
{
    public function __construct(
        protected StripeSubscriptionService $stripeService
    ) {}

    /**
     * Create a new subscription for the tenant
     */
    public function create(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|integer',
            'price_id' => 'required|string',
            'payment_method_id' => 'required|string',
        ]);

        try {
            $subscription = $this->stripeService->createSubscription(
                $request->tenant_id,
                $request->price_id,
                $request->payment_method_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription created successfully',
                'subscription' => $subscription,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Cancel an active subscription
     */
    public function cancel(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|integer',
        ]);

        try {
            $subscription = $this->stripeService->cancelSubscription($request->tenant_id);

            return response()->json([
                'success' => true,
                'message' => 'Subscription canceled successfully',
                'subscription' => $subscription,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Upgrade or change subscription plan
     */
    public function upgrade(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|integer',
            'new_price_id' => 'required|string',
        ]);

        try {
            $subscription = $this->stripeService->updateSubscription(
                $request->tenant_id,
                $request->new_price_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription upgraded successfully',
                'subscription' => $subscription,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get current tenant subscription
     */
    public function current(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|integer',
        ]);

        try {
            $tenantId = $request->tenant_id;
            $subscription = $this->stripeService->getCurrentSubscription($tenantId);

            return response()->json([
                'success' => true,
                'subscription' => $subscription,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
