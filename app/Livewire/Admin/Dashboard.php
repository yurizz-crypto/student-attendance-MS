<?php

namespace App\Livewire\Admin;

use App\Jobs\BackupDatabase;
use App\Models\AuditLog;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\User;
use App\Services\AuditService;
use App\Services\SystemHealthService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Dashboard extends Component
{
    public $stats = [];

    public $recentLogs = [];

    public $userStats = [];

    public $classList = [];

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
        $this->refreshData();
    }

    public function refreshData()
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

        // Get statistics (Updated to actually filter by date so the UI updates)
        $this->stats = [
            'total_users' => User::where('created_at', '>=', $startDate)->count(),
            'admin_users' => User::where('role', 'admin')->where('created_at', '>=', $startDate)->count(),
            'faculty_users' => User::where('role', 'faculty')->where('created_at', '>=', $startDate)->count(),
            'student_users' => User::where('role', 'student')->where('created_at', '>=', $startDate)->count(),
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
            BackupDatabase::dispatch();

            AuditService::log('database_backup_started', 'System', null, []);
            
            $this->dispatch('swal:success', title: 'Backup Started', message: 'The database backup is processing in the background.');
        } catch (\Exception $e) {
            $this->dispatch('swal:error', title: 'Error', message: 'Failed to start backup: ' . $e->getMessage());
        }
    }

    public function clearCache(): void
    {
        try {
            // Cleared safely without view:clear (which deletes livewire views mid-request)
            Artisan::call('cache:clear');
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