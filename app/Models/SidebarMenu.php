<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class SidebarMenu extends Model
{
    protected $table = 'sidebar_menus';

    protected $fillable = [
        'title',
        'slug',
        'icon',
        'route_name',
        'active_pattern',
        'custom_url',
        'parent_id',
        'permission_name',
        'sort_order',
        'status',
    ];

    public function parent()
    {
        return $this->belongsTo(SidebarMenu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(SidebarMenu::class, 'parent_id')->orderBy('sort_order');
    }

    public function admins()
    {
        return $this->belongsToMany(
            AdminUser::class,
            'admin_sidebar_menu',
            'sidebar_menu_id',
            'admin_user_id'
        )->withTimestamps();
    }

    public function canBeSeenBy(AdminUser $admin): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->permission_name && !$admin->hasPermission($this->permission_name)) {
            return false;
        }

        return true;
    }

    public function getRouteNameFor(AdminUser $admin): ?string
    {
        if ($this->slug === 'dashboard') {
            return $admin->is_super_admin ? 'superadmin.dashboard' : 'admin.dashboard';
        }

        return $this->route_name;
    }

    public function getUrlFor(AdminUser $admin): string
    {
        $routeName = $this->getRouteNameFor($admin);

        if ($routeName && Route::has($routeName)) {
            return route($routeName);
        }

        if ($this->custom_url) {
            return $this->custom_url;
        }

        return 'javascript:void(0)';
    }

    public function isActiveFor(AdminUser $admin): bool
    {
        if ($this->slug === 'dashboard') {
            return $admin->is_super_admin
                ? request()->routeIs('superadmin.dashboard')
                : request()->routeIs('admin.dashboard');
        }

        if ($this->active_pattern) {
            return request()->routeIs($this->active_pattern);
        }

        if ($this->route_name) {
            return request()->routeIs($this->route_name);
        }

        return false;
    }
}