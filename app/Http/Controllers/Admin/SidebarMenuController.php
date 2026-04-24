<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\SidebarMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SidebarMenuController extends Controller
{
    public function index()
    {
        $menus = SidebarMenu::with('parent')
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.dashboard.sidebar-menu', compact('menus'));
    }

    public function create()
    {
        $parents = SidebarMenu::whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        $permissions = Permission::where('status', 'active')
            ->orderBy('module')
            ->orderBy('label')
            ->get()
            ->groupBy(fn ($item) => $item->module ?: 'General');

        return view('admin.dashboard.sidebar-menu-create', compact('parents', 'permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'                  => ['required', 'string', 'max:100'],
            'slug'                   => ['nullable', 'string', 'max:100', 'unique:sidebar_menus,slug'],
            'icon'                   => ['nullable', 'string', 'max:100'],
            'route_name'             => ['nullable', 'string', 'max:255'],
            'super_admin_route_name' => ['nullable', 'string', 'max:255'],
            'active_pattern'         => ['nullable', 'string', 'max:255'],
            'custom_url'             => ['nullable', 'string', 'max:255'],
            'parent_id'              => ['nullable', 'exists:sidebar_menus,id'],
            'permission_name'        => ['nullable', 'string', 'max:255'],
            'sort_order'             => ['nullable', 'integer', 'min:0'],
            'status'                 => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $data['slug'] = Str::slug($data['slug'] ?: $data['title'], '-');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        SidebarMenu::create($data);

        return redirect()
            ->route('admin.sidebar-menus.index')
            ->with('success', 'Sidebar menu created successfully.');
    }

    public function edit(SidebarMenu $sidebar_menu)
    {
        $parents = SidebarMenu::whereNull('parent_id')
            ->where('id', '!=', $sidebar_menu->id)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        $permissions = Permission::where('status', 'active')
            ->orderBy('module')
            ->orderBy('label')
            ->get()
            ->groupBy(fn ($item) => $item->module ?: 'General');

        return view('admin.dashboard.sidebar-menu-edit', [
            'menu' => $sidebar_menu,
            'sidebarMenu' => $sidebar_menu,
            'parents' => $parents,
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request, SidebarMenu $sidebar_menu)
    {
        $data = $request->validate([
            'title'                  => ['required', 'string', 'max:100'],
            'slug'                   => ['nullable', 'string', 'max:100', 'unique:sidebar_menus,slug,' . $sidebar_menu->id],
            'icon'                   => ['nullable', 'string', 'max:100'],
            'route_name'             => ['nullable', 'string', 'max:255'],
            'super_admin_route_name' => ['nullable', 'string', 'max:255'],
            'active_pattern'         => ['nullable', 'string', 'max:255'],
            'custom_url'             => ['nullable', 'string', 'max:255'],
            'parent_id'              => ['nullable', 'exists:sidebar_menus,id'],
            'permission_name'        => ['nullable', 'string', 'max:255'],
            'sort_order'             => ['nullable', 'integer', 'min:0'],
            'status'                 => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if (!empty($data['parent_id']) && (int) $data['parent_id'] === (int) $sidebar_menu->id) {
            return back()->withInput()->with('error', 'A menu cannot be its own parent.');
        }

        $data['slug'] = Str::slug($data['slug'] ?: $data['title'], '-');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $sidebar_menu->update($data);

        return redirect()
            ->route('admin.sidebar-menus.index')
            ->with('success', 'Sidebar menu updated successfully.');
    }

    public function destroy(SidebarMenu $sidebar_menu)
    {
        if ($sidebar_menu->children()->exists()) {
            return back()->with('error', 'Please delete child menus first.');
        }

        $sidebar_menu->admins()->detach();
        $sidebar_menu->delete();

        return redirect()
            ->route('admin.sidebar-menus.index')
            ->with('success', 'Sidebar menu deleted successfully.');
    }
}