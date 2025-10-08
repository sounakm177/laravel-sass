<?php

namespace App\Models;

class Domains extends BaseModel
{
    protected $fillable = [
        'domain',
        'tenant_id',
    ];
}
