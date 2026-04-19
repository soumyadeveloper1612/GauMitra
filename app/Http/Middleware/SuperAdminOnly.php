<?php

namespace App\Http\Middleware;

use App\Models\AdminUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Please login first.');
        }

        $admin = AdminUser::find(session('admin_id'));

        if (!$admin || $admin->status !== 'active') {
            return redirect()->route('admin.login')->with('error', 'Your session has expired.');
        }

        if (!(bool) $admin->is_super_admin) {
            return redirect()->route('admin.dashboard')->with('error', 'Only Super Admin can access this section.');
        }

        return $next($request);
    }
}