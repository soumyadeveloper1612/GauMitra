<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!admin_can($permission)) {
            return redirect()->route(is_super_admin() ? 'superadmin.dashboard' : 'admin.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}