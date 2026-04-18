<?php

namespace App\Http\Middleware;

use App\Models\AdminUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Please login first.');
        }

        $admin = AdminUser::find(session('admin_id'));

        if (!$admin || $admin->status !== 'active') {
            $request->session()->forget([
                'admin_id',
                'admin_name',
                'admin_user_id',
                'admin_is_super_admin',
            ]);

            return redirect()->route('admin.login')->with('error', 'Your session has expired or account is inactive.');
        }

        return $next($request);
    }
}