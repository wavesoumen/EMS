<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:1') means only users with role level <= 1 (Admin) can access.
     * middleware('role:3') means level <= 3 (Admin, HR, Manager) can access.
     */
    public function handle(Request $request, Closure $next, string $maxLevel): Response
    {
        $user = $request->user();

        if (!$user || !$user->role) {
            abort(403, 'Unauthorized');
        }

        if ($user->role->level > (int) $maxLevel) {
            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
