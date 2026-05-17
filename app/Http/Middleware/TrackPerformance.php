<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackPerformance
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $durationMs = round(($endTime - $startTime) * 1000);

        // Keep a rolling average in cache
        $currentAvg = Cache::get('sys_avg_response_time', 0);
        $totalReqs = Cache::get('sys_total_requests', 0);

        $newReqs = $totalReqs + 1;
        // Simple rolling average prioritizing recent requests a bit
        if ($totalReqs == 0) {
            $newAvg = $durationMs;
        } else {
            // Keep the last 1000 requests window roughly
            $weight = min($newReqs, 1000);
            $newAvg = (($currentAvg * ($weight - 1)) + $durationMs) / $weight;
        }

        Cache::put('sys_avg_response_time', round($newAvg), now()->addHours(24));
        Cache::put('sys_total_requests', $newReqs, now()->addHours(24));

        // Track errors (500-level)
        if ($response->getStatusCode() >= 500) {
            Cache::increment('sys_total_errors');
        }

        return $response;
    }
}
