<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Always allow Admin to pass through any role check
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return $next($request);
        }
        
        // If no specific roles are required, allow access
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has a role
        if (!$user->userRole) {
            abort(403, 'Access denied. No role assigned.');
        }

        // Check if user's role is in the allowed roles
        if (in_array($user->userRole->name, $roles)) {
            return $next($request);
        }

        // If not authorized, show 403 error
        abort(403, 'Access denied. Insufficient permissions.');
    }
}
