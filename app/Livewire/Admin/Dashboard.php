<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\AuditLog;
use App\Services\AuditService;
use Livewire\Component;

class Dashboard extends Component
{
    public $stats = [];
    public $recentLogs = [];
    public $userStats = [];
    public $classList = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Get statistics
        $this->stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'faculty_users' => User::where('role', 'faculty')->count(),
            'student_users' => User::where('role', 'student')->count(),
            'total_classes' => \App\Models\ClassSection::count(),
            'total_subjects' => \App\Models\Subject::count(),
        ];

        // Get audit statistics
        $auditStats = AuditService::getStatistics();
        $this->stats = array_merge($this->stats, $auditStats);

        // Get recent audit logs
        $this->recentLogs = AuditService::getRecentLogs(10);

        // Get user creation statistics (this week)
        $this->userStats = [
            'created_today' => User::whereDate('created_at', today())->count(),
            'created_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()])->count(),
            'created_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        // Get classes list
        $this->classList = \App\Models\ClassSection::with('subject', 'faculty')->limit(5)->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
