<?php

use Livewire\Component;
use App\Models\AttendanceRecord;
use App\Models\ClassSection;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

new class extends Component {
    public $filterSemesterId = '';
    public $filterClassId = '';

    public function mount()
    {
        $activeSemester = \App\Models\Semester::where('is_active', true)->first();
        if ($activeSemester) {
            $this->filterSemesterId = $activeSemester->id;
        }
    }

    public function with(): array
    {
        $facultyId = Auth::id();
        
        $semesters = \App\Models\Semester::orderBy('id', 'desc')->get();
        $classesQuery = ClassSection::where('faculty_id', $facultyId)->with('subject');
        
        if ($this->filterSemesterId) {
            $classesQuery->where('semester_id', $this->filterSemesterId);
        }
        
        $availableClasses = $classesQuery->get();
        $classIds = $availableClasses->pluck('id')->toArray();

        if ($this->filterClassId) {
            $classIds = [$this->filterClassId];
        }

        // Get records
        $records = AttendanceRecord::whereHas('session', function($q) use ($classIds) {
            $q->whereIn('class_section_id', $classIds);
        })
        ->with(['student', 'session'])
        ->get();

        $presentCount = $records->where('status', 'present')->count();
        $absentCount = $records->where('status', 'absent')->count();
        $excusedCount = $records->where('status', 'excused')->count();
        $lateCount = $records->where('status', 'late')->count();
        
        $attendedCount = $presentCount + $lateCount + $excusedCount;
        $totalCount = $records->count();
        
        $attendanceRate = $totalCount > 0 ? round(($attendedCount / $totalCount) * 100) : 100;

        // Group by month for chart (average attendance per month)
        $monthlyStats = [];
        foreach ($records as $record) {
            $month = Carbon::parse($record->session->date)->format('M Y');
            if (!isset($monthlyStats[$month])) {
                $monthlyStats[$month] = ['total' => 0, 'attended' => 0];
            }
            $monthlyStats[$month]['total']++;
            if (in_array($record->status, ['present', 'late', 'excused'])) {
                $monthlyStats[$month]['attended']++;
            }
        }
        
        $chartLabels = array_keys($monthlyStats);
        $chartData = [];
        foreach ($monthlyStats as $month => $stat) {
            $chartData[] = $stat['total'] > 0 ? round(($stat['attended'] / $stat['total']) * 100) : 0;
        }

        // At risk students (attendance < 80%)
        $studentsData = [];
        foreach ($records as $record) {
            $sId = $record->student_id;
            if (!isset($studentsData[$sId])) {
                $studentsData[$sId] = [
                    'student' => $record->student,
                    'total' => 0,
                    'attended' => 0,
                    'absent' => 0
                ];
            }
            $studentsData[$sId]['total']++;
            if (in_array($record->status, ['present', 'late', 'excused'])) {
                $studentsData[$sId]['attended']++;
            } elseif ($record->status === 'absent') {
                $studentsData[$sId]['absent']++;
            }
        }

        $atRiskStudents = [];
        foreach ($studentsData as $data) {
            $rate = $data['total'] > 0 ? round(($data['attended'] / $data['total']) * 100) : 100;
            if ($rate < 80 && $data['total'] > 0) {
                $data['rate'] = $rate;
                $atRiskStudents[] = $data;
            }
        }

        usort($atRiskStudents, function($a, $b) {
            return $a['rate'] <=> $b['rate'];
        });

        return [
            'semesters' => $semesters,
            'availableClasses' => $availableClasses,
            'stats' => [
                'rate' => $attendanceRate,
                'absent' => $absentCount,
                'atRiskCount' => count($atRiskStudents),
                'totalRecords' => $totalCount
            ],
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'atRiskStudents' => array_slice($atRiskStudents, 0, 10), // Show top 10 worst
        ];
    }
}; ?>

<div class="space-y-6">
    <!-- Filters -->
    <div class="p-6 rounded-3xl border border-gray-100 bg-surface flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
        <div>
            <h3 class="text-lg font-bold text-navy">Class Analytics</h3>
            <p class="text-sm text-gray-500 font-medium">Monitor attendance trends and identify at-risk students.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <select wire:model.live="filterSemesterId" class="rounded-xl border-gray-300 text-sm focus:ring-brand focus:border-brand font-medium text-navy shadow-sm">
                <option value="">All Semesters</option>
                @foreach($semesters as $semester)
                    <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterClassId" class="rounded-xl border-gray-300 text-sm focus:ring-brand focus:border-brand font-medium text-navy shadow-sm max-w-[200px]">
                <option value="">All Classes</option>
                @foreach($availableClasses as $class)
                    <option value="{{ $class->id }}">{{ $class->subject->code ?? 'N/A' }} - {{ $class->name }}</option>
                @endforeach
            </select>
            
            @if($filterClassId)
                <a href="{{ route('faculty.classes.export', ['classSection' => $filterClassId, 'format' => 'csv']) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-1 transition-all">
                    <svg class="w-4 h-4 mr-2 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    CSV
                </a>
                <a href="{{ route('faculty.classes.export', ['classSection' => $filterClassId, 'format' => 'pdf']) }}" target="_blank" class="inline-flex items-center justify-center px-4 py-2 bg-brand text-white border border-transparent rounded-xl text-sm font-semibold shadow-sm hover:bg-brand-hover focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-1 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    PDF
                </a>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-brand/10 text-brand flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Avg. Attendance</p>
                <p class="text-2xl font-bold {{ $stats['rate'] < 80 ? 'text-error' : 'text-brand' }}">{{ $stats['rate'] }}%</p>
            </div>
        </div>
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-error/10 text-error flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Absences</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['absent'] }}</p>
            </div>
        </div>
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">At-Risk Students</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['atRiskCount'] }}</p>
            </div>
        </div>
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Records</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['totalRecords'] }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Trend Chart -->
        <div class="lg:col-span-2 bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm">
            <h3 class="text-lg font-bold text-navy mb-4">Average Attendance Trend (%)</h3>
            <div class="relative h-72 w-full" 
                 x-data="{ 
                    chart: null,
                    init() {
                        this.renderChart();
                        $watch('$wire.chartData', () => {
                            this.renderChart();
                        });
                    },
                    renderChart() {
                        if (typeof Chart === 'undefined') return;
                        if (this.chart) this.chart.destroy();
                        
                        const ctx = this.$refs.canvas.getContext('2d');
                        this.chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: {{ json_encode($chartLabels) }},
                                datasets: [{
                                    label: 'Attendance Rate (%)',
                                    data: {{ json_encode($chartData) }},
                                    borderColor: '#4f46e5',
                                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#4f46e5',
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: { min: 0, max: 100 }
                                },
                                plugins: {
                                    legend: { display: false }
                                }
                            }
                        });
                    }
                 }">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

        <!-- At-Risk Students -->
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col">
            <h3 class="text-lg font-bold text-navy mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                At-Risk Students (< 80%)
            </h3>
            <div class="flex-1 overflow-y-auto pr-2 space-y-4 max-h-72">
                @forelse($atRiskStudents as $atRisk)
                    <div class="bg-error/5 rounded-2xl p-4 border border-error/10 flex items-center justify-between">
                        <div>
                            <p class="font-bold text-navy">{{ $atRisk['student']->first_name }} {{ $atRisk['student']->last_name }}</p>
                            <p class="text-xs text-gray-500 font-medium">{{ $atRisk['student']->identity_id ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-error/10 text-error">
                                {{ $atRisk['rate'] }}%
                            </span>
                            <p class="text-xs text-error font-medium mt-1">{{ $atRisk['absent'] }} Absences</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-success/50 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-bold text-gray-600">All good!</p>
                        <p class="text-xs text-gray-500 mt-1">No students below 80% attendance.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
