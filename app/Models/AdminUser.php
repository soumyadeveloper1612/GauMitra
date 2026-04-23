<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

        return $this->roles()->whereHas('permissions', function ($q) use ($permissionName) {
            $q->where('name', $permissionName)->where('status', 'active');
        })->exists();
    }

    public function assignedSidebarMenus()
    {
        $baseQuery = SidebarMenu::with([
                'children' => function ($q) {
                    $q->where('status', 'active')->orderBy('sort_order');
                }
            ])
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('sort_order');

        if ($this->is_super_admin) {
            return $baseQuery->get();
        }

        $assignedIds = $this->sidebarMenus()->pluck('sidebar_menus.id')->map(fn ($id) => (int) $id);

        $dashboardId = SidebarMenu::where('slug', 'dashboard')
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->value('id');

        if ($dashboardId) {
            $assignedIds->push((int) $dashboardId);
        }

        $assignedIds = $assignedIds->unique()->values();

        if ($assignedIds->isEmpty()) {
            return collect();
        }

        $menus = $baseQuery
            ->where(function ($q) use ($assignedIds) {
                $q->whereIn('id', $assignedIds)
                  ->orWhereHas('children', function ($childQuery) use ($assignedIds) {
                      $childQuery->whereIn('sidebar_menus.id', $assignedIds)
                                 ->where('status', 'active');
                  });
            })
            ->get();

        return $menus->map(function ($menu) use ($assignedIds) {
            if (!$assignedIds->contains((int) $menu->id)) {
                $filteredChildren = $menu->children->filter(function ($child) use ($assignedIds) {
                    return $assignedIds->contains((int) $child->id);
                })->values();

                $menu->setRelation('children', $filteredChildren);
            }

            return $menu;
        })->filter(function ($menu) use ($assignedIds) {
            return $assignedIds->contains((int) $menu->id) || $menu->children->isNotEmpty();
        })->values();
    }
}