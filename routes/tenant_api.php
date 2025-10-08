<?php

use App\Http\Controllers\Tenant\AuthController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\SubscriptionController;
use App\Http\Controllers\Tenant\PaymentMethodController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureTenantAuthenticated;


// All tenant-related routes go here (no tenancy middleware here)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/validate-token', [AuthController::class, 'validateToken']);

Route::middleware(['auth:tenant', 'check.token.expiry', EnsureTenantAuthenticated::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Subscription
    Route::prefix('subscriptions')->group(function () {
        Route::post('/create', [SubscriptionController::class, 'create']);
        Route::post('/cancel', [SubscriptionController::class, 'cancel']);
        Route::post('/upgrade', [SubscriptionController::class, 'upgrade']);
        Route::get('/current', [SubscriptionController::class, 'current']);
    });

    // Payment Methods
    Route::prefix('payment-methods')->group(function () {
        Route::post('/add', [PaymentMethodController::class, 'addCard']);
        Route::get('/list', [PaymentMethodController::class, 'listCards']);
        Route::post('/set-default', [PaymentMethodController::class, 'setDefaultCard']);
    });
});


