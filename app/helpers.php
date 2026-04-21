<?php

use App\Models\AdminUser;

if (!function_exists('current_admin')) {
    function current_admin(): ?AdminUser
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

if (!function_exists('is_super_admin')) {
    function is_super_admin(): bool
    {
        $admin = current_admin();

        return $admin?->is_super_admin === true;
    }
}

if (!function_exists('admin_can')) {
    function admin_can(string $permission): bool
    {
        $admin = current_admin();

        if (!$admin) {
            return false;
        }

        return $admin->hasPermission($permission);
    }
}