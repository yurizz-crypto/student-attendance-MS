<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an activity/action to the audit trail.
     */
    public static function log(
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $changes = null,
        ?string $description = null,
        string $status = 'success'
    ): AuditLog {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'changes' => $changes,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'description' => $description,
            'status' => $status,
        ]);
    }

    /**
     * Log a model creation.
     */
    public static function logCreated(Model $model, ?array $attributes = null): AuditLog
    {
        return self::log(
            'created',
            get_class($model),
            $model->id,
            ['created' => $attributes ?? $model->getAttributes()],
            sprintf('%s #%d created', class_basename($model), $model->id)
        );
    }

    /**
     * Log a model update.
     */
    public static function logUpdated(Model $model, array $original, array $changes): AuditLog
    {
        $changesArray = [];
        foreach ($changes as $key => $value) {
            $changesArray[$key] = [
                'old' => $original[$key] ?? null,
                'new' => $value,
            ];
        }

        return self::log(
            'updated',
            get_class($model),
            $model->id,
            $changesArray,
            sprintf('%s #%d updated', class_basename($model), $model->id)
        );
    }

    /**
     * Log a model deletion.
     */
    public static function logDeleted(Model $model): AuditLog
    {
        return self::log(
            'deleted',
            get_class($model),
            $model->id,
            ['deleted' => $model->getAttributes()],
            sprintf('%s #%d deleted', class_basename($model), $model->id)
        );
    }

    /**
     * Log user login.
     */
    public static function logLogin(int $userId): AuditLog
    {
        return self::log(
            'login',
            \App\Models\User::class,
            $userId,
            null,
            'User logged in'
        );
    }

    /**
     * Log user logout.
     */
    public static function logLogout(int $userId): AuditLog
    {
        return self::log(
            'logout',
            \App\Models\User::class,
            $userId,
            null,
            'User logged out'
        );
    }

    /**
     * Get recent audit logs for dashboard.
     */
    public static function getRecentLogs(int $limit = 10)
    {
        return AuditLog::with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get audit logs for a specific model.
     */
    public static function getLogsForModel(string $modelType, int $modelId)
    {
        return AuditLog::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->with('user')
            ->latest()
            ->get();
    }

    /**
     * Get audit logs for a user.
     */
    public static function getLogsForUser(int $userId)
    {
        return AuditLog::where('user_id', $userId)
            ->with('user')
            ->latest()
            ->get();
    }

    /**
     * Get audit logs paginated.
     */
    public static function getPaginatedLogs(int $perPage = 15)
    {
        return AuditLog::with('user')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get statistics for dashboard.
     */
    public static function getStatistics()
    {
        return [
            'total_logs' => AuditLog::count(),
            'today_logs' => AuditLog::whereDate('created_at', today())->count(),
            'week_logs' => AuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'user_actions' => AuditLog::where('model_type', \App\Models\User::class)->count(),
            'created_actions' => AuditLog::where('action', 'created')->count(),
            'updated_actions' => AuditLog::where('action', 'updated')->count(),
            'deleted_actions' => AuditLog::where('action', 'deleted')->count(),
            'failed_actions' => AuditLog::where('status', 'failed')->count(),
        ];
    }
}
