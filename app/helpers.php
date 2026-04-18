<?php

use App\Models\AdminUser;

if (!function_exists('admin_user')) {
    function admin_user(): ?AdminUser
    {
        static $admin = null;

        if ($admin !== null) {
            return $admin;
        }

        $adminId = session('admin_id');

        if (!$adminId) {
            return null;
        }

        $admin = AdminUser::with('roles.permissions')->find($adminId);

        return $admin;
    }
}

if (!function_exists('admin_can')) {
    function admin_can(string $permission): bool
    {
        $admin = admin_user();

        return $admin ? $admin->hasPermission($permission) : false;
    }
}

if (!function_exists('admin_has_role')) {
    function admin_has_role(string $role): bool
    {
        $admin = admin_user();

        return $admin ? $admin->hasRole($role) : false;
    }
}