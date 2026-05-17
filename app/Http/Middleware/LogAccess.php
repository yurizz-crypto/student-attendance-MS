<?php

namespace App\Http\Middleware;

use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only log GET requests to prevent double logging with form submissions
        // which are usually logged by the controllers directly via logUpdated/logCreated.
        if ($request->isMethod('GET') && $request->user()) {

            // Exclude common AJAX requests or minor data fetches to prevent log spam
            if (! $request->ajax() && ! $request->wantsJson() && ! $request->routeIs('*.show', '*.export')) {

                $route = $request->route() ? $request->route()->getName() : $request->path();

                AuditService::log(
                    'viewed',
                    \Illuminate\Routing\Route::class,
                    null,
                    ['path' => $request->path(), 'route' => $route],
                    'Accessed page: /'.ltrim($request->path(), '/')
                );
            }
        }

        return $next($request);
    }
}
