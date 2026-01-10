<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;
use App\Models\Tenant;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // InitializeTenancyByRequestData::$header = 'X-Tenant'; 
        // InitializeTenancyByRequestData::$queryParameter = 'tenant';

        // InitializeTenancyByRequestData::$onFail = function ($exception, $request, $next) {
        //     $tenant = Tenant::where('name', $request->header('X-Tenant'))->first();
        //     if (!$tenant) {
        //         $tenant = Tenant::where('name', $request->query('tenant'))->first();
        //     }
        //     if (!$tenant) {
        //         return response()->json(['error' => 'Tenant not found'], 404);
        //     }

        //     tenancy()->initialize($tenant->id);

        //     return $next($request); 
        // };
    }
}
