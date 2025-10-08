<?php

namespace App\Services;

use App\Repositories\Interfaces\TenantDetailsRepositoryInterface;
use App\Repositories\Interfaces\TenantRepositoryInterface;
use App\Repositories\Interfaces\TenantUserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TenantAuthService
{
    public function __construct(
        protected TenantRepositoryInterface $tenantRepository,
        protected TenantUserRepositoryInterface $tenantUserRepository,
        protected TenantDetailsRepositoryInterface $tenantDetailsRepository,
    ) {}

    /**
     * Handle tenant user login (email/password)
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $user = $this->tenantUserRepository->getByOne([], ['email' => $credentials['email']]);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->tokens()->where('expires_at', '<', now())->delete();

        $token = $user->createToken('tenant-token')->plainTextToken;
        $user->tokens()->latest()->first()->update(['expires_at' => now()->addHour()]);

        return [$token, $user];
    }

    /**
     * Validate a one-time token (used during first login or invite flow)
     */
    public function validateToken(string $token): array
    {
        $tenantUser = $this->tenantUserRepository->getByOne([], ['one_time_token' => $token]);

        if (!$tenantUser) {
            throw ValidationException::withMessages([
                'message' => ['Invalid or expired token.'],
            ]);
        }

        // Invalidate one-time token
        $tenantUser->update(['one_time_token' => null]);

        // Issue a normal API token
        $apiToken = $tenantUser->createToken('tenant-login')->plainTextToken;

        return [$apiToken, $tenantUser];
    }
}
