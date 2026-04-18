<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'label',
        'description',
        'status',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id')
            ->withTimestamps();
    }

    public function admins()
    {
        return $this->belongsToMany(AdminUser::class, 'admin_role_user', 'role_id', 'admin_user_id')
            ->withTimestamps();
    }
}