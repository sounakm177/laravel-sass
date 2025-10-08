<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TenantController;

Route::post('/tenants', [TenantController::class, 'store'])->name('tenant.store');

Route::middleware('identify.tenant')
->group(function () {
   require __DIR__ . '/tenant_api.php';
});

