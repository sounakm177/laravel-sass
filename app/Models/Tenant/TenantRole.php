<?php

namespace App\Models\Tenant;

use App\Models\BaseModel;

class TenantRole extends BaseModel
{
    protected $table = 'tenant_roles';

    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(TenantUser::class, 'tenant_role_user')
            ->withTimestamps();
    }

    public function tenantRoleUsers()
    {
        return $this->hasMany(TenantRoleUser::class);
    }
}
