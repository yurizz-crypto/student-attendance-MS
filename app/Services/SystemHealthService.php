<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SystemHealthService
{
    public static function getMetrics()
    {
        return [
            'uptime' => self::getUptime(),
            'database_size' => self::getDatabaseSize(),
            'storage_usage' => self::getStorageUsage(),
            'avg_response_time' => self::getAverageResponseTime(),
            'error_rate' => self::getErrorRate(),
            'active_now' => User::where('last_activity', '>=', now()->subMinutes(5))->count(),
        ];
    }

    private static function getUptime()
    {
        // Try reading /proc/uptime for Linux servers
        if (file_exists('/proc/uptime')) {
            $contents = file_get_contents('/proc/uptime');
            $uptimeSecs = (int) explode(' ', $contents)[0];
            $days = floor($uptimeSecs / 86400);
            $hours = floor(($uptimeSecs % 86400) / 3600);

            return "{$days}d {$hours}h";
        }

        // Fallback for local Windows environment
        return 'Local Dev';
    }

    private static function getDatabaseSize()
    {
        try {
            // PostgreSQL size query
            $dbName = config('database.connections.pgsql.database');
            $result = DB::select('SELECT pg_size_pretty(pg_database_size(?)) as size', [$dbName]);

            return $result[0]->size ?? 'Unknown';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    private static function getStorageUsage()
    {
        try {
            $path = storage_path();
            $total = disk_total_space($path);
            $free = disk_free_space($path);
            $used = $total - $free;

            $percentage = $total > 0 ? round(($used / $total) * 100, 1) : 0;

            return $percentage.'%';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    private static function getAverageResponseTime()
    {
        // We track this in Cache via TrackPerformance middleware
        return Cache::get('sys_avg_response_time', 0).'ms';
    }

    private static function getErrorRate()
    {
        // Calculate based on recent requests
        $totalReqs = Cache::get('sys_total_requests', 1);
        if ($totalReqs === 0) {
            $totalReqs = 1;
        }
        $errors = Cache::get('sys_total_errors', 0);

        $rate = round(($errors / $totalReqs) * 100, 2);

        return $rate.'%';
    }
}
