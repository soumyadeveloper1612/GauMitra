<?php

use App\Models\AdminUser;

if (!function_exists('admin_user')) {
    function admin_user(): ?AdminUser
    {
        static $admin = null;
        static $loaded = false;

        if ($loaded) {
            return $admin;
        }

        $loaded = true;

        if (!session()->has('admin_id')) {
            return null;
        }

        $admin = AdminUser::with('roles.permissions')->find(session('admin_id'));

        return $admin;
    }
}

if (!function_exists('admin_can')) {
    function admin_can(string $permission): bool
    {
        $admin = admin_user();

        if (!$admin) {
            return false;
        }

        return $admin->hasPermission($permission);
    }
}

if (!function_exists('is_super_admin')) {
    function is_super_admin(): bool
    {
        $admin = admin_user();

        return $admin ? (bool) $admin->is_super_admin : false;
    }
}