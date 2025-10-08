<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StripeSubscriptionService;
use App\Models\TenantPaymentMethod;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
    protected $stripeService;

    public function __construct(StripeSubscriptionService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Add a new card for tenant
     */
    public function addCard(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        // $tenant = Auth::user()->tenant; // assuming user belongs to tenant
        $paymentMethod = $this->stripeService->addCardToTenant(1, $request->payment_method_id);

        return response()->json([
            'success' => true,
            'payment_method' => $paymentMethod,
        ]);
    }

    /**
     * List all cards of tenant
     */
    public function listCards(Request $request)
    {
        $tenant = Auth::user()->tenant;

        $cards = $this->stripeService->listTenantCards($tenant->id);

        return response()->json([
            'success' => true,
            'cards' => $cards,
        ]);
    }

    /**
     * Set default card
     */
    public function setDefaultCard(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        $tenant = Auth::user()->tenant;

        $paymentMethod = $this->stripeService->setDefaultCard($tenant->id, $request->payment_method_id);

        return response()->json([
            'success' => true,
            'default_payment_method' => $paymentMethod,
        ]);
    }
}
