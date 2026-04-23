<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\SidebarMenu;
use Illuminate\Http\Request;

class MenuAccessController extends Controller
{
    public function index()
    {
        $admins = AdminUser::with(['roles', 'sidebarMenus'])
            ->orderByDesc('is_super_admin')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.menu-access.index', compact('admins'));
    }

    public function edit(AdminUser $admin)
    {
        $menus = SidebarMenu::with([
                'children' => function ($q) {
                    $q->where('status', 'active')->orderBy('sort_order');
                }
            ])
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        $selectedMenuIds = $admin->sidebarMenus()
            ->pluck('sidebar_menus.id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        return view('admin.menu-access.edit', compact('admin', 'menus', 'selectedMenuIds'));
    }

    public function update(Request $request, AdminUser $admin)
    {
        if ($admin->is_super_admin) {
            return redirect()
                ->route('admin.menu-access.index')
                ->with('success', 'Super Admin always sees all active menus. Manual assignment is not required.');
        }

        $data = $request->validate([
            'menu_ids'   => 'nullable|array',
            'menu_ids.*' => 'integer|exists:sidebar_menus,id',
        ]);

        $menuIds = collect($data['menu_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($menuIds->isNotEmpty()) {
            $parentIds = SidebarMenu::whereIn('id', $menuIds)
                ->whereNotNull('parent_id')
                ->pluck('parent_id')
                ->map(fn ($id) => (int) $id);

            $menuIds = $menuIds->merge($parentIds)->unique()->values();
        }

        $validMenuIds = SidebarMenu::whereIn('id', $menuIds)->pluck('id')->toArray();

        $admin->sidebarMenus()->sync($validMenuIds);

        return redirect()
            ->route('admin.menu-access.index')
            ->with('success', 'Menu access updated successfully.');
    }
}