<?php

namespace App\Models;

class Role extends BaseModel
{
    protected $fillable = ['user_id', 'role_id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role')
            ->withTimestamps();
    }
}
