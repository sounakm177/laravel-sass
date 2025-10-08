<?php

namespace App\Models;

class UserRole extends BaseModel
{
    protected $table = 'user_role';

    protected $fillable = ['user_id', 'role_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
