<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantName = $request->header('X-Tenant');

        // If not in header, check query parameter
        if (!$tenantName) {
            $tenantName = $request->query('tenant');
        }

        // Find tenant in DB
        $tenant = Tenant::where('name', $tenantName)->first();

        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant not found'
            ], 404);
        }

        // Initialize tenancy
        tenancy()->initialize($tenant->id);
        Config::set('auth.defaults.guard', 'tenant');

        // Continue request
        return $next($request);
    }
}
