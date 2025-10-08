<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TenantAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\TenantAuthRequest;

class AuthController extends Controller
{
    public function __construct(
        protected TenantAuthService $tenantAuthService,
    ) {}

    /**
     * Handle user login request (email/password)
     */
    public function login(TenantAuthRequest $request): JsonResponse
    {
        try {
            [$token, $user] = $this->tenantAuthService->login($request->only('email', 'password'));
            $tenant = tenant();
            $domain = $tenant->domains()->first();

            $frontendDomain = env('FRONTEND_DOMAIN', null);
            $secure = app()->environment('production');
            return response()
            ->json([
                'status' => 'success',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'tenant' => [
                    'id' => $tenant->id,
                    'domain' => $domain['domain'],
                    'name' => $tenant->name,
                ],
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Validate a one-time token for tenant login
     */
    public function validateToken(Request $request): JsonResponse
    {
        $token = $request->token;

        if (!$token){
            return response()->json([
                'success' => false,
                'message' => 'Token is required',
            ], 422);
        }

        try {
            [$apiToken, $user] = $this->tenantAuthService->validateToken($token);
            $tenant = tenant();
            $domain = $tenant->domains()->first();

            return response()->json([
                'success' => true,
                'access_token' => $apiToken,
                'token_type'=> "Bearer",
                'user' => $user,
                'tenant' => [
                    'id' => $tenant->id,
                    'domain' => $domain['domain'],
                    'name' => $tenant->name,
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }
}
