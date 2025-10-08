<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTenantAuthenticated
{
    public function handle(Request $request, Closure $next, $guard = 'tenant')
    {
        if (!Auth::guard($guard)->check()) {
            return response()->json([
                'message' => 'Unauthenticated.',
                'error' => 'Please login to access this resource.'
            ], 401);
        }

        return $next($request);
    }
}
