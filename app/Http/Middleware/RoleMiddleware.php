<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        // Support multiple roles passed as 'role1|role2' or 'role1,role2'
        $allowed = preg_split('/[|,]/', $role);

        // Always allow true admins (role 'admin')
        if ($user->role === 'admin') {
            return $next($request);
        }

        if (! in_array($user->role, $allowed, true)) {
            abort(403);
        }

        return $next($request);
    }
}

