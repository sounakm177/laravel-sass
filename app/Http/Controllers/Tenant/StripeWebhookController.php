<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StripeSubscriptionService;

class StripeWebhookController extends Controller
{
    public function __construct(
        protected StripeSubscriptionService $stripeService,
    ) {}

    public function handle(Request $request)
    {
        $payload = $request->getContent();

        return $this->stripeService->handleWebhook($payload);
    }
}
