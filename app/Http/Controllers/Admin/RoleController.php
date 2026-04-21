<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

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
        ], [
            'label.required' => 'Role label is required.',
            'name.required' => 'Role code is required.',
            'name.unique' => 'This role code already exists.',
            'status.required' => 'Please select a status.',
        ]);

        if (!Schema::hasTable('roles')) {
            return back()->withInput()->with('error', 'The roles table does not exist.');
        }

        if (!Schema::hasTable('permissions')) {
            return back()->withInput()->with('error', 'The permissions table does not exist.');
        }

        if (!Schema::hasTable('permission_role')) {
            return back()->withInput()->with('error', 'The permission_role pivot table does not exist.');
        }

        try {
            DB::beginTransaction();

            $role = Role::create([
                'label' => $validated['label'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);

            $role->permissions()->sync($validated['permissions'] ?? []);

            DB::commit();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Role created successfully.');
        } catch (QueryException $e) {
            DB::rollBack();

            Log::error('Role store database error', [
                'request_data' => $request->except(['_token']),
                'sql_message'  => $e->getMessage(),
                'line'         => $e->getLine(),
                'file'         => $e->getFile(),
            ]);

            $message = 'Database error while creating role.';

            if (str_contains(strtolower($e->getMessage()), 'duplicate')) {
                $message = 'Role code already exists. Please use another role code.';
            } elseif (str_contains(strtolower($e->getMessage()), 'permission_role')) {
                $message = 'permission_role pivot table is missing or has wrong columns.';
            } elseif (str_contains(strtolower($e->getMessage()), 'roles')) {
                $message = 'roles table structure is not matching the code.';
            }

            return back()->withInput()->with('error', $message);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Role store failed', [
                'request_data' => $request->except(['_token']),
                'message'      => $e->getMessage(),
                'line'         => $e->getLine(),
                'file'         => $e->getFile(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Role creation failed. Please check roles table, permissions table, and permission_role pivot table.');
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

        if (!Schema::hasTable('permission_role')) {
            return back()->withInput()->with('error', 'The permission_role pivot table does not exist.');
        }

        try {
            DB::beginTransaction();

            $role->update([
                'label' => $validated['label'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);

            $role->permissions()->sync($validated['permissions'] ?? []);

            DB::commit();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Role updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Role update failed', [
                'role_id' => $role->id,
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
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
            DB::beginTransaction();

            $role->admins()->detach();
            $role->permissions()->detach();
            $role->delete();

            DB::commit();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Role delete failed', [
                'role_id' => $role->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Role delete failed.');
        }
    }
}