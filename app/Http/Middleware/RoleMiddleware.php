<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Load user roles if not loaded
        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }
        
        // Check if user has the required role
        if (!$user->hasRole($role)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}