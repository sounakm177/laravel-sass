<?php

namespace App\Models\Tenant;

use App\Models\BaseModel;

class TenantRoleUser extends BaseModel
{
    protected $table = 'tenant_role_user';

    protected $fillable = [
        'tenant_user_id',
        'tenant_role_id',
    ];

    public function tenantUser()
    {
        return $this->belongsTo(TenantUser::class);
    }

    public function tenantRole()
    {
        return $this->belongsTo(TenantRole::class);
    }
}
