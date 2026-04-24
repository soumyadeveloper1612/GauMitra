<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SidebarMenu extends Model
{
    protected $table = 'sidebar_menus';

    protected $fillable = [
        'title',
        'slug',
        'icon',
        'route_name',
        'super_admin_route_name',
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
        return $this->hasMany(SidebarMenu::class, 'parent_id')
            ->where('status', 'active')
            ->orderBy('sort_order');
    }

    public function admins()
    {
        return $this->belongsToMany(AdminUser::class, 'admin_sidebar_menu', 'sidebar_menu_id', 'admin_user_id')
            ->withTimestamps();
    }

    public function canBeSeenBy(?AdminUser $admin): bool
    {
        if (!$admin) {
            return false;
        }

        if ($this->status !== 'active') {
            return false;
        }

        if ($admin->is_super_admin) {
            return true;
        }

        if (empty($this->permission_name)) {
            return true;
        }

        return $admin->hasPermission($this->permission_name);
    }

    public function getUrlFor(?AdminUser $admin): string
    {
        if (!empty($this->custom_url)) {
            return $this->custom_url;
        }

        $routeName = $this->route_name;

        if ($admin && $admin->is_super_admin && !empty($this->super_admin_route_name)) {
            $routeName = $this->super_admin_route_name;
        }

        if (!empty($routeName) && Route::has($routeName)) {
            return route($routeName);
        }

        return 'javascript:void(0)';
    }

    public function isActiveFor(?AdminUser $admin): bool
    {
        $patterns = [];

        if (!empty($this->active_pattern)) {
            $patterns = array_map('trim', explode(',', $this->active_pattern));
        }

        if (!empty($this->route_name)) {
            $patterns[] = $this->route_name;
        }

        if ($admin && $admin->is_super_admin && !empty($this->super_admin_route_name)) {
            $patterns[] = $this->super_admin_route_name;
        }

        foreach ($patterns as $pattern) {
            if ($pattern && request()->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    }
}