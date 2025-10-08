<?php

namespace App\Services;

use App\Repositories\Interfaces\TenantDetailsRepositoryInterface;
use App\Repositories\Interfaces\TenantRepositoryInterface;
use App\Repositories\Interfaces\TenantUserRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TenantService
{
    public function __construct(
        protected TenantRepositoryInterface $tenantRepository,
        protected TenantUserRepositoryInterface $tenantUserRepository,
        protected TenantDetailsRepositoryInterface $tenantDetailsRepository,
    ) {}

    public function createTenant(array $data)
    {
        $name = $data['first_name'].' '.$data['last_name'] ?? explode('@', $data['email'])[0];

        $tenant = $this->tenantRepository->create(['email' => $data['email'], 'name' => $data['company_name'], 'tenancy_db_name' => config('database.connections.mysql.database').'_'.$data['company_name']])->get();

        $baseDomain = config('tenancy.central_domains')[0] ?? 'localhost';

        $domain = $tenant->domains()->create([
            'domain' => "{$tenant->name}.{$baseDomain}",
        ]);

        $tenantDetails = $this->tenantDetailsRepository->create([
            'tenant_id' => $tenant->id,
            'logo' => $data['logo'],
            'address' => $data['address'],
        ])->get();

        $tenantUser = $tenant->run(function () use ($data) {
            $tenantName = str_replace(' ', '_', $data['company_name']);
            $originalPath = "tenants_logo/{$tenantName}/".basename($data['logo']);
            $targetFolder = "company/{$tenantName}/logo";
            $targetPath = "{$targetFolder}/".basename($data['logo']);

            $imageContents = Storage::disk('central_public')->get($originalPath);
            Storage::disk('public')->makeDirectory($targetFolder);
            Storage::disk('public')->put($targetPath, $imageContents);

            return $this->tenantUserRepository->create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'image' => $targetPath,
                'email' => $data['email'],
                'password' => $data['password'],
                'one_time_token' => Str::random(64),
            ])->get();
        });

        // Generate API token
        $token = $tenant->run(function () use ($tenantUser) {
            $token = $tenantUser->createToken('tenant-token')->plainTextToken;
            $tenantUser->tokens()->latest()->first()->update([
                'expires_at' => now()->addHour(),
            ]);
            return $token;
        });

        $tenantUserData = $tenantUser->only(['id', 'first_name', 'last_name', 'email', 'image', 'one_time_token']);

        return [
            'tenant'       => $tenant,
            'domain'       => $domain,
            'tenantUser'   => $tenantUserData,
            'token' => $token,
        ];
    }

}
