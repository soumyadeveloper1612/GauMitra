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

    protected $casts = [
        'is_super_admin' => 'boolean',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'admin_role_user', 'admin_user_id', 'role_id');
    }

    public function sidebarMenus()
    {
        return $this->belongsToMany(SidebarMenu::class, 'admin_sidebar_menu', 'admin_user_id', 'sidebar_menu_id');
    }

    public function hasPermission(?string $permissionName): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        if (empty($permissionName)) {
            return true;
        }

        $hasRolePermission = $this->roles()
            ->whereHas('permissions', function ($q) use ($permissionName) {
                $q->where('name', $permissionName)
                  ->where('status', 'active');
            })
            ->exists();

        if ($hasRolePermission) {
            return true;
        }

        return $this->sidebarMenus()
            ->where('sidebar_menus.status', 'active')
            ->where('sidebar_menus.permission_name', $permissionName)
            ->exists();
    }

    public function hasMenuAccess(?SidebarMenu $menu): bool
    {
        if (!$menu) {
            return false;
        }

        if ($menu->status !== 'active') {
            return false;
        }

        if ($this->is_super_admin) {
            return true;
        }

        $assignedIds = $this->sidebarMenus()
            ->pluck('sidebar_menus.id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($assignedIds->contains((int) $menu->id)) {
            return true;
        }

        if ($menu->parent_id && $assignedIds->contains((int) $menu->parent_id)) {
            return true;
        }

        return false;
    }

    public function assignedSidebarMenus()
    {
        $baseQuery = SidebarMenu::whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('sort_order');

        if ($this->is_super_admin) {
            return $baseQuery->with([
                'children' => function ($q) {
                    $q->where('status', 'active')->orderBy('sort_order');
                }
            ])->get();
        }

        $selectedIds = $this->sidebarMenus()
            ->where('sidebar_menus.status', 'active')
            ->pluck('sidebar_menus.id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $dashboardId = SidebarMenu::where('slug', 'dashboard')
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->value('id');

        if ($dashboardId) {
            $selectedIds->push((int) $dashboardId);
        }

        $selectedIds = $selectedIds->unique()->values();

        if ($selectedIds->isEmpty()) {
            return collect();
        }

        $selectedMenus = SidebarMenu::whereIn('id', $selectedIds)->get(['id', 'parent_id']);

        $parentIds = $selectedMenus->pluck('parent_id')
            ->filter()
            ->map(fn ($id) => (int) $id);

        $topLevelIds = $selectedMenus->whereNull('parent_id')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->merge($parentIds)
            ->unique()
            ->values();

        return SidebarMenu::with([
                'children' => function ($q) use ($selectedIds) {
                    $q->where('status', 'active')
                      ->whereIn('id', $selectedIds)
                      ->orderBy('sort_order');
                }
            ])
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->whereIn('id', $topLevelIds)
            ->orderBy('sort_order')
            ->get();
    }
}