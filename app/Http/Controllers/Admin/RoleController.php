<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['permissions', 'admins'])->latest()->paginate(10);

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::where('status', 'active')
            ->orderBy('module')
            ->orderBy('label')
            ->get()
            ->groupBy('module');

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'name' => 'nullable|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($request) {
            $role = Role::create([
                'label' => $request->label,
                'name' => $request->filled('name') ? Str::slug($request->name, '_') : Str::slug($request->label, '_'),
                'description' => $request->description,
                'status' => $request->status,
            ]);

            $role->permissions()->sync($request->permissions ?? []);
        });

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $role->load('permissions');

        $permissions = Permission::where('status', 'active')
            ->orderBy('module')
            ->orderBy('label')
            ->get()
            ->groupBy('module');

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'name' => ['nullable', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($role->name === 'super_admin') {
            return back()->with('error', 'Super Admin role should not be modified from here.');
        }

        DB::transaction(function () use ($request, $role) {
            $role->update([
                'label' => $request->label,
                'name' => $request->filled('name') ? Str::slug($request->name, '_') : Str::slug($request->label, '_'),
                'description' => $request->description,
                'status' => $request->status,
            ]);

            $role->permissions()->sync($request->permissions ?? []);
        });

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'super_admin') {
            return back()->with('error', 'Super Admin role cannot be deleted.');
        }

        $role->admins()->detach();
        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}