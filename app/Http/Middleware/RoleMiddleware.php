<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role, $permission = null)
    {
        if (!$request->user()->hasRole($role)) {
            abort(401);
        }

        if ($permission !== null && !$request->user()->can($permission)) {
            abort(401);
        }

        return $next($request);
    }
}
