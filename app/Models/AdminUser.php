<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AdminUser extends Model
{
    protected $table = 'admin_users';

    protected $fillable = [
        'name',
        'user_id',
        'email',
        'password',
        'status',
        'is_super_admin',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_super_admin' => 'boolean',
    ];

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'admin_role_user',
            'admin_user_id',
            'role_id'
        )->withTimestamps();
    }

    public function getAllPermissionsAttribute(): Collection
    {
        $this->loadMissing('roles.permissions');

        return $this->roles
            ->where('status', 'active')
            ->flatMap(function ($role) {
                return $role->permissions->where('status', 'active');
            })
            ->unique('id')
            ->values();
    }

    public function hasRole(string $roleName): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        $this->loadMissing('roles');

        return $this->roles
            ->where('status', 'active')
            ->contains('name', $roleName);
    }

    public function hasPermission(string $permissionName): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        return $this->all_permissions->contains('name', $permissionName);
    }
}