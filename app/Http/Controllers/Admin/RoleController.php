<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['permissions', 'admins'])
            ->latest()
            ->paginate(10);

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
        $normalizedName = Str::slug($request->input('name') ?: $request->input('label'), '_');
        $request->merge(['name' => $normalizedName]);

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')],
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $role = Role::create([
                    'label' => $validated['label'],
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'status' => $validated['status'],
                ]);

                $role->permissions()->sync($validated['permissions'] ?? []);
            });

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Role created successfully.');
        } catch (\Throwable $e) {
            Log::error('Role store failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Role creation failed. Please check pivot tables and role uniqueness.');
        }
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
        if ($role->name === 'super_admin') {
            return back()->with('error', 'Super Admin role should not be modified from here.');
        }

        $normalizedName = Str::slug($request->input('name') ?: $request->input('label'), '_');
        $request->merge(['name' => $normalizedName]);

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        try {
            DB::transaction(function () use ($validated, $role) {
                $role->update([
                    'label' => $validated['label'],
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'status' => $validated['status'],
                ]);

                $role->permissions()->sync($validated['permissions'] ?? []);
            });

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Role updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Role update failed', [
                'role_id' => $role->id,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Role update failed. Please check pivot tables and role uniqueness.');
        }
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'super_admin') {
            return back()->with('error', 'Super Admin role cannot be deleted.');
        }

        try {
            DB::transaction(function () use ($role) {
                $role->admins()->detach();
                $role->permissions()->detach();
                $role->delete();
            });

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Role delete failed', [
                'role_id' => $role->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Role delete failed.');
        }
    }
}