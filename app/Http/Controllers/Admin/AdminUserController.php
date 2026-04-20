<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = AdminUser::with('roles')->latest()->paginate(10);

        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        $roles = Role::where('status', 'active')->orderBy('label')->get();

        return view('admin.admins.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'required|string|max:100|unique:admin_users,user_id',
            'password' => 'required|string|min:6',
            'status' => 'required|in:active,inactive',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        DB::transaction(function () use ($request) {
            $admin = AdminUser::create([
                'name' => $request->name,
                'user_id' => $request->user_id,
                'password' => Hash::make($request->password),
                'status' => $request->status,
                'is_super_admin' => admin_user()?->is_super_admin ? (bool)$request->is_super_admin : false,
            ]);

            $admin->roles()->sync($request->roles ?? []);
        });

        return redirect()->route('admin.admins.index')->with('success', 'Admin created successfully.');
    }

    public function edit(AdminUser $admin)
    {
        $roles = Role::where('status', 'active')->orderBy('label')->get();
        $admin->load('roles');

        return view('admin.admins.edit', compact('admin', 'roles'));
    }

    public function update(Request $request, AdminUser $admin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => ['required', 'string', 'max:100', Rule::unique('admin_users', 'user_id')->ignore($admin->id)],
            'password' => 'nullable|string|min:6',
            'status' => 'required|in:active,inactive',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($admin->id == session('admin_id') && $request->status === 'inactive') {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        if ($admin->is_super_admin && !admin_user()?->is_super_admin) {
            return back()->with('error', 'Only super admin can edit another super admin.');
        }

        DB::transaction(function () use ($request, $admin) {
            $data = [
                'name' => $request->name,
                'user_id' => $request->user_id,
                'status' => $request->status,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if (admin_user()?->is_super_admin) {
                $data['is_super_admin'] = (bool)$request->is_super_admin;
            }

            $admin->update($data);
            $admin->roles()->sync($request->roles ?? []);
        });

        return redirect()->route('admin.admins.index')->with('success', 'Admin updated successfully.');
    }

    public function destroy(AdminUser $admin)
    {
        if ($admin->id == session('admin_id')) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($admin->is_super_admin) {
            return back()->with('error', 'Super admin cannot be deleted from here.');
        }

        $admin->roles()->detach();
        $admin->delete();

        return redirect()->route('admin.admins.index')->with('success', 'Admin deleted successfully.');
    }
}