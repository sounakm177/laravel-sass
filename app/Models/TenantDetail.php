<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class TenantDetail extends BaseModel
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'logo', 'address'];

    public function getCompanyLogoUrlAttribute()
    {
        if (! $this->logo) {
            return asset('default-user.png');
        }

        return asset('storage/'.$this->logo);
    }
}
