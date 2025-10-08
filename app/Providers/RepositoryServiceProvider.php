<?php

namespace App\Providers;

use App\Repositories\AdminRoleRepository;
use App\Repositories\Interfaces\AdminRoleRepositoryInterface;
use App\Repositories\Interfaces\TenantDetailsRepositoryInterface;
use App\Repositories\Interfaces\TenantRepositoryInterface;
use App\Repositories\Interfaces\TenantUserIndexRepositoryInterface;
use App\Repositories\Interfaces\TenantUserRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\SubscriptionRepositoryInterface;
use App\Repositories\TenantDetailsRepository;
use App\Repositories\TenantRepository;
use App\Repositories\TenantUserIndexRepository;
use App\Repositories\TenantUserRepository;
use App\Repositories\UserRepository;
use App\Repositories\SubscriptionRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
        TenantRepositoryInterface::class => TenantRepository::class,
        TenantUserRepositoryInterface::class => TenantUserRepository::class,
        TenantDetailsRepositoryInterface::class => TenantDetailsRepository::class,
        AdminRoleRepositoryInterface::class => AdminRoleRepository::class,
        TenantUserIndexRepositoryInterface::class => TenantUserIndexRepository::class,
        SubscriptionRepositoryInterface::class => SubscriptionRepository::class
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
