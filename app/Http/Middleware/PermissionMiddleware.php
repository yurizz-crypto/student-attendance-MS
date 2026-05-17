<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // If the user is not logged in, or they do not have the required permission
        if (! $request->user() || ! $request->user()->hasPermission($permission)) {
            abort(403, 'Unauthorized action. You do not have permission to access this module.');
        }

        return $next($request);
    }
}
