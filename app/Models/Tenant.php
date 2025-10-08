<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $appends = ['tenant_url'];

    protected $fillable = ['email', 'name', 'tenancy_db_name'];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'email',
            'name',
            'tenancy_db_name',
        ];
    }

    public function getIncrementing()
    {
        return true;
    }

    public function tenantDetail()
    {
        return $this->hasOne(TenantDetail::class);
    }

    /**
     * Accessor for the tenant's full URL
     */
    public function getTenantUrlAttribute(): string
    {
        $domain = $this->domains->first()?->domain;

        return $domain ? tenant_route($domain, 'login') : '';
    }
}
