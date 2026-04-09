<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('admin_id')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

 public function login(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
            'password' => 'required|string',
        ], [
            'user_id.required' => 'User ID is required',
            'password.required' => 'Password is required',
        ]);

        $admin = AdminUser::where('user_id', $request->user_id)
            ->where('status', 'active')
            ->first();

        if (!$admin) {
            return back()
                ->with('error', 'User not found or inactive')
                ->withInput();
        }

        if (!Hash::check($request->password, $admin->password)) {
            return back()
                ->with('error', 'Invalid password')
                ->withInput();
        }

        $request->session()->regenerate();

        session([
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'admin_user_id' => $admin->user_id,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Login successful');
    }


    public function logout(Request $request)
    {
        $request->session()->forget(['admin_id', 'admin_name', 'admin_user_id']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }
}