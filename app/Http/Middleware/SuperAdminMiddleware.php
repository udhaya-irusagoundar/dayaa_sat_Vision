<?php

namespace App\Http\Middleware;

use Closure;

class SuperAdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!session()->has('superadmin_logged')) {
            return redirect()->route('superadmin.login')
                ->with('error', 'Please login to continue');
        }
        return $next($request);
    }
}