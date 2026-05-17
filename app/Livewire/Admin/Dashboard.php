<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\User;
use App\Services\SystemHealthService;
use Livewire\Component;

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

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
