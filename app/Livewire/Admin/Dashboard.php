<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\User;
use App\Services\AuditService;
use App\Services\SystemHealthService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use ZipArchive;

class Dashboard extends Component
{
    public $stats = [];

    public $recentLogs = [];

    public $userStats = [];

    public $classList = [];

    // New variables for Comprehensive Dashboard
    public $dateRange = 'month'; // 'today', 'week', 'month', 'year'

    public $chartDataRegistrations = [];

    public $chartDataTransactions = [];

    public $systemHealth = [];

    public function mount()
    {
        $this->loadData();
    }

    public function updatedDateRange()
    {
        $this->loadData();
        // Dispatch browser event to re-render charts
        $this->dispatch('update-charts',
            registrations: $this->chartDataRegistrations,
            transactions: $this->chartDataTransactions
        );
    }

    public function loadData()
    {
        // Apply Date Range Filter
        $startDate = match ($this->dateRange) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(), // 'month'
        };

        // Get statistics
        $this->stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'faculty_users' => User::where('role', 'faculty')->count(),
            'student_users' => User::where('role', 'student')->count(),
            'total_classes' => ClassSection::count(),
            'total_subjects' => Subject::count(),
        ];

        // Get audit statistics filtered by date
        $this->stats['total_logs'] = AuditLog::count();
        $this->stats['today_logs'] = AuditLog::whereDate('created_at', today())->count();
        $this->stats['created_actions'] = AuditLog::where('action', 'created')->where('created_at', '>=', $startDate)->count();
        $this->stats['updated_actions'] = AuditLog::where('action', 'updated')->where('created_at', '>=', $startDate)->count();
        $this->stats['deleted_actions'] = AuditLog::where('action', 'deleted')->where('created_at', '>=', $startDate)->count();

        // Get recent audit logs
        $this->recentLogs = AuditLog::with('user')->latest()->limit(10)->get();

        // Get user creation statistics
        $this->userStats = [
            'created_today' => User::whereDate('created_at', today())->count(),
            'created_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()])->count(),
            'created_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        // Get classes list
        $this->classList = ClassSection::with('subject', 'faculty')->limit(5)->get();

        // System Health
        $this->systemHealth = SystemHealthService::getMetrics();

        // Prepare Chart Data
        $this->prepareChartData($startDate);
    }

    private function prepareChartData($startDate)
    {
        // Example: Group registrations by day over the selected period
        // For simplicity, we'll just group by date (Y-m-d)
        $registrations = User::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $transactions = AuditLog::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Format for Chart.js
        $this->chartDataRegistrations = [
            'labels' => $registrations->pluck('date')->toArray(),
            'data' => $registrations->pluck('count')->toArray(),
        ];

        $this->chartDataTransactions = [
            'labels' => $transactions->pluck('date')->toArray(),
            'data' => $transactions->pluck('count')->toArray(),
        ];
    }

    public function backupDatabase(): void
    {
        try {
            $backupDir = storage_path('backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $backupFile = $backupDir . '/backup_' . now()->format('Y-m-d_His') . '.sql';
            
            // Get database configuration
            $dbHost = config('database.connections.mysql.host') ?? '127.0.0.1';
            $dbUser = config('database.connections.mysql.username') ?? 'root';
            $dbPass = config('database.connections.mysql.password') ?? '';
            $dbName = config('database.connections.mysql.database') ?? config('database.connections.sqlite.database');

            // Create backup using mysqldump
            $command = "mysqldump --host={$dbHost} --user={$dbUser}";
            if ($dbPass) {
                $command .= " --password={$dbPass}";
            }
            $command .= " {$dbName} > {$backupFile}";

            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($backupFile)) {
                // Compress the backup
                $zipFile = $backupDir . '/backup_' . now()->format('Y-m-d_His') . '.zip';
                $zip = new ZipArchive();
                
                if ($zip->open($zipFile, ZipArchive::CREATE)) {
                    $zip->addFile($backupFile, basename($backupFile));
                    $zip->close();
                    unlink($backupFile); // Remove uncompressed backup
                    
                    AuditService::log('database_backup', 'System', null, ['backup_file' => basename($zipFile)]);
                    $this->dispatch('swal:success', title: 'Success', message: 'Database backed up successfully!');
                } else {
                    $this->dispatch('swal:error', title: 'Error', message: 'Failed to compress backup.');
                }
            } else {
                $this->dispatch('swal:error', title: 'Error', message: 'Failed to create database backup.');
            }
        } catch (\Exception $e) {
            $this->dispatch('swal:error', title: 'Error', message: 'Backup failed: ' . $e->getMessage());
        }
    }

    public function clearCache(): void
    {
        try {
            // Clear all cache stores
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');

            // Additional cache clearing
            Cache::flush();

            AuditService::log('cache_cleared', 'System', null, ['timestamp' => now()]);
            $this->dispatch('swal:success', title: 'Success', message: 'Cache cleared successfully!');
        } catch (\Exception $e) {
            $this->dispatch('swal:error', title: 'Error', message: 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
