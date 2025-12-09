<?php

namespace App\Http\Middleware;

use Closure;

class AdminAuth
{
    public function handle($request, Closure $next)
    {
        if (!session('is_admin')) {
            return redirect('/login');
        }
        return $next($request);
    }
}