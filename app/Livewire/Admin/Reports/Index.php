<?php

namespace App\Livewire\Admin\Reports;

use App\Exports\DynamicReportExport;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\AuditLog;
use App\Models\ClassSection;
use App\Models\ReportConfiguration;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    public $activeTab = 'generate';

    // Form fields
    public $reportType = 'user_activity';

    public $dateRange = 'this_month'; // today, this_week, this_month, this_year, custom

    public $customStartDate;

    public $customEndDate;

    public $filterRole = '';

    public $filterCategory = '';

    // Save report fields
    public $showSaveModal = false;

    public $reportName = '';

    public $scheduleFrequency = 'none';

    public $emailTo = '';

    public $isFavorite = false;

    public function render()
    {
        $savedReports = ReportConfiguration::where('user_id', auth()->id())->get();
        $favorites = $savedReports->where('is_favorite', true);

        // Fetch basic stats for System Usage view (if selected)
        $systemStats = [];
        if ($this->reportType === 'system_usage') {
            $systemStats = $this->getSystemUsageStats();
        }

        $previewData = null;
        if ($this->reportType !== 'system_usage') {
            // Get up to 5 rows for preview without crashing the browser
            $previewData = $this->getReportData(5);
        }

        return view('livewire.admin.reports.index', [
            'savedReports' => $savedReports,
            'favorites' => $favorites,
            'systemStats' => $systemStats,
            'previewData' => $previewData,
        ]);
    }

    public function generateReport($format)
    {
        $data = $this->getReportData();

        $filename = $this->reportType.'_'.now()->format('Y_m_d_His');

        if ($format === 'excel') {
            return Excel::download(new DynamicReportExport($data['rows'], $data['headers']), $filename.'.xlsx');
        } elseif ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.pdf.report', [
                'type' => $this->reportType,
                'data' => $data,
                'date' => now()->format('F d, Y'),
                'generatedBy' => auth()->user()->first_name.' '.auth()->user()->last_name,
            ]);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename.'.pdf');
        }
    }

    public function openSaveModal()
    {
        $this->reportName = ucwords(str_replace('_', ' ', $this->reportType)).' Report';
        $this->showSaveModal = true;
    }

    public function saveReport()
    {
        $this->validate([
            'reportName' => 'required|string|max:255',
            'scheduleFrequency' => 'required|in:none,daily,weekly,monthly',
            'emailTo' => 'nullable|email|required_unless:scheduleFrequency,none',
        ]);

        ReportConfiguration::create([
            'user_id' => auth()->id(),
            'name' => $this->reportName,
            'type' => $this->reportType,
            'filters' => [
                'dateRange' => $this->dateRange,
                'customStartDate' => $this->customStartDate,
                'customEndDate' => $this->customEndDate,
                'filterRole' => $this->filterRole,
                'filterCategory' => $this->filterCategory,
            ],
            'is_favorite' => $this->isFavorite,
            'schedule_frequency' => $this->scheduleFrequency,
            'email_to' => $this->emailTo,
            'next_run_at' => $this->scheduleFrequency !== 'none' ? $this->calculateNextRun($this->scheduleFrequency) : null,
        ]);

        $this->showSaveModal = false;
        $this->dispatch('swal:success', title: 'Saved!', message: 'Report configuration saved successfully.');
    }

    private function calculateNextRun($frequency)
    {
        return match ($frequency) {
            'daily' => now()->addDay()->startOfDay()->addHours(8), // 8 AM tomorrow
            'weekly' => now()->addWeek()->startOfWeek()->addHours(8), // 8 AM next Monday
            'monthly' => now()->addMonth()->startOfMonth()->addHours(8), // 8 AM 1st of next month
            default => null,
        };
    }

    public function runSavedReport($id, $format)
    {
        $config = ReportConfiguration::findOrFail($id);

        // Temporarily set filters to the saved ones
        $originalFilters = [
            'reportType' => $this->reportType,
            'dateRange' => $this->dateRange,
            'customStartDate' => $this->customStartDate,
            'customEndDate' => $this->customEndDate,
            'filterRole' => $this->filterRole,
            'filterCategory' => $this->filterCategory,
        ];

        $this->reportType = $config->type;
        $this->dateRange = $config->filters['dateRange'] ?? 'this_month';
        $this->customStartDate = $config->filters['customStartDate'] ?? null;
        $this->customEndDate = $config->filters['customEndDate'] ?? null;
        $this->filterRole = $config->filters['filterRole'] ?? '';
        $this->filterCategory = $config->filters['filterCategory'] ?? '';

        $response = $this->generateReport($format);

        // Restore original filters
        foreach ($originalFilters as $key => $val) {
            $this->$key = $val;
        }

        return $response;
    }

    public function toggleFavorite($id)
    {
        $config = ReportConfiguration::findOrFail($id);
        $config->update(['is_favorite' => ! $config->is_favorite]);
    }

    public function deleteReport($id)
    {
        ReportConfiguration::findOrFail($id)->delete();
        $this->dispatch('swal:success', title: 'Deleted', message: 'Report configuration deleted.');
    }

    public function getReportDataForCommand()
    {
        return $this->getReportData();
    }

    private function getReportData($limit = null)
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        return match ($this->reportType) {
            'user_activity' => $this->getUserActivityData($startDate, $endDate, $limit),
            'transaction_summary' => $this->getTransactionData($startDate, $endDate, $limit),
            'audit_trail' => $this->getAuditData($startDate, $endDate, $limit),
            'system_usage' => ['headers' => ['Metric', 'Value'], 'rows' => $this->getSystemUsageStats()],
            'custom' => $this->getCustomData($startDate, $endDate, $limit),
        };
    }

    private function getStartDate()
    {
        return match ($this->dateRange) {
            'today' => now()->startOfDay(),
            'this_week' => now()->startOfWeek(),
            'this_month' => now()->startOfMonth(),
            'this_year' => now()->startOfYear(),
            'custom' => $this->customStartDate ? Carbon::parse($this->customStartDate)->startOfDay() : now()->startOfMonth(),
            default => now()->startOfMonth(),
        };
    }

    private function getEndDate()
    {
        return match ($this->dateRange) {
            'today' => now()->endOfDay(),
            'this_week' => now()->endOfWeek(),
            'this_month' => now()->endOfMonth(),
            'this_year' => now()->endOfYear(),
            'custom' => $this->customEndDate ? Carbon::parse($this->customEndDate)->endOfDay() : now()->endOfMonth(),
            default => now()->endOfMonth(),
        };
    }

    private function getUserActivityData($start, $end, $limit = null)
    {
        $query = User::query()->withCount(['attendanceRecords', 'auditLogs']);

        if ($this->filterRole) {
            $query->where('role', $this->filterRole);
        }

        if ($limit) {
            $query->limit($limit);
        }

        $users = $query->get();

        $rows = $users->map(function ($u) {
            return [
                'Name' => $u->first_name.' '.$u->last_name,
                'Role' => ucfirst($u->role),
                'Email' => $u->email,
                'Attendance Logs' => $u->attendance_records_count,
                'System Actions' => $u->audit_logs_count,
                'Last Active' => $u->last_activity ? $u->last_activity->format('Y-m-d H:i') : 'Never',
            ];
        })->toArray();

        return [
            'headers' => ['Name', 'Role', 'Email', 'Attendance Logs', 'System Actions', 'Last Active'],
            'rows' => $rows,
        ];
    }

    private function getTransactionData($start, $end, $limit = null)
    {
        // Transactions = Attendance Records + Excuses
        $query = AttendanceRecord::whereBetween('created_at', [$start, $end])->with('student', 'session.classSection.subject');

        if ($this->filterCategory) {
            $query->where('status', $this->filterCategory);
        }

        if ($limit) {
            $query->limit($limit);
        }

        $records = $query->get();

        $rows = $records->map(function ($r) {
            return [
                'Date' => $r->created_at->format('Y-m-d H:i'),
                'Student' => $r->student->first_name.' '.$r->student->last_name,
                'Class' => $r->session->classSection->subject->code ?? 'N/A',
                'Status' => ucfirst($r->status),
            ];
        })->toArray();

        return [
            'headers' => ['Date', 'Student', 'Class', 'Status'],
            'rows' => $rows,
        ];
    }

    private function getAuditData($start, $end, $limit = null)
    {
        $query = AuditLog::whereBetween('created_at', [$start, $end])->with('user');

        $actualLimit = $limit ?: 500; // Limit to prevent memory issues
        $logs = $query->latest()->limit($actualLimit)->get();

        $rows = $logs->map(function ($l) {
            return [
                'Date' => $l->created_at->format('Y-m-d H:i:s'),
                'User' => $l->user ? $l->user->first_name.' '.$l->user->last_name : 'System',
                'Action' => $l->action,
                'Module' => class_basename($l->auditable_type),
                'Description' => $l->description,
            ];
        })->toArray();

        return [
            'headers' => ['Date', 'User', 'Action', 'Module', 'Description'],
            'rows' => $rows,
        ];
    }

    private function getSystemUsageStats()
    {
        return [
            ['Metric' => 'Total Users', 'Value' => User::count()],
            ['Metric' => 'Total Active Students', 'Value' => User::where('role', 'student')->where('status', 'active')->count()],
            ['Metric' => 'Total Faculty', 'Value' => User::where('role', 'faculty')->count()],
            ['Metric' => 'Total Classes', 'Value' => ClassSection::count()],
            ['Metric' => 'Total Attendance Sessions', 'Value' => AttendanceSession::count()],
            ['Metric' => 'Total Attendance Records', 'Value' => AttendanceRecord::count()],
            ['Metric' => 'Overall Present Rate', 'Value' => $this->calculatePresentRate().'%'],
        ];
    }

    private function calculatePresentRate()
    {
        $total = AttendanceRecord::count();
        if ($total === 0) {
            return 100;
        }

        $present = AttendanceRecord::whereIn('status', ['present', 'late', 'excused'])->count();

        return round(($present / $total) * 100, 1);
    }

    private function getCustomData($start, $end, $limit = null)
    {
        // Simple custom report that dumps all attendance with custom filter
        return $this->getTransactionData($start, $end, $limit);
    }
}
