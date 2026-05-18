<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // If the user is not logged in, or their role doesn't match the required role, block them
        if (! $request->user() || $request->user()->role !== $role) {
            abort(403, 'Unauthorized action. You do not have permission to access this page.');
        }

        return $next($request);
    }
}
