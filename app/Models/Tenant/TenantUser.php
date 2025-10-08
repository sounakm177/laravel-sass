<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class TenantUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['first_name', 'last_name', 'email', 'image', 'phone_no', 'password', 'password_updated_at', 'status', 'created_by', 'one_time_token'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setPasswordAttribute($value)
    {
        if (Hash::needsRehash($value)) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    public function roles()
    {
        return $this->belongsToMany(TenantRole::class, 'tenant_role_user')
            ->withTimestamps();
    }

    public function hasRole($roleName)
    {
        foreach ($this->roles as $role) {
            if ($role->name == $roleName) {
                return true;
            }
        }

        return false;
    }

    public function tenantRoleUsers()
    {
        return $this->hasMany(TenantRoleUser::class);
    }

    public function hasUpdatedPassword(): bool
    {
        return ! is_null($this->password_updated_at);
    }
}
