<?php

use App\Models\AdminUser;

if (!function_exists('current_admin')) {
    function current_admin(): ?AdminUser
    {
        static $adminLoaded = false;
        static $admin = null;

        if ($adminLoaded) {
            return $admin;
        }

        $adminLoaded = true;

        $adminId = session('admin_id');

        if (!$adminId) {
            return null;
        }

        $admin = AdminUser::with('roles.permissions', 'sidebarMenus')->find($adminId);

        return $admin;
    }
}

if (!function_exists('is_super_admin')) {
    function is_super_admin(): bool
    {
        $admin = current_admin();

        return (bool) ($admin?->is_super_admin);
    }
}

if (!function_exists('admin_can')) {
    function admin_can(?string $permissionName): bool
    {
        $admin = current_admin();

        if (!$admin) {
            return false;
        }

        if ($admin->is_super_admin) {
            return true;
        }

        if (empty($permissionName)) {
            return true;
        }

        return $admin->hasPermission($permissionName);
    }
}