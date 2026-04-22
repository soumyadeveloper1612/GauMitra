<?php

use App\Models\AdminUser;

if (!function_exists('auth_admin')) {
    function auth_admin()
    {
        $adminId = session('admin_id');

        if (!$adminId) {
            return null;
        }

        return AdminUser::with('roles.permissions')->find($adminId);
    }
}

if (!function_exists('is_super_admin')) {
    function is_super_admin(): bool
    {
        $admin = auth_admin();

        if (!$admin) {
            return false;
        }

        if ((int) $admin->is_super_admin === 1) {
            return true;
        }

        return $admin->roles->contains(function ($role) {
            return $role->name === 'super_admin' && $role->status === 'active';
        });
    }
}

if (!function_exists('admin_can')) {
    function admin_can(string $permissionName): bool
    {
        if (is_super_admin()) {
            return true;
        }

        $admin = auth_admin();

        if (!$admin) {
            return false;
        }

        foreach ($admin->roles as $role) {
            if ($role->status !== 'active') {
                continue;
            }

            foreach ($role->permissions as $permission) {
                if ($permission->status === 'active' && $permission->name === $permissionName) {
                    return true;
                }
            }
        }

        return false;
    }
}