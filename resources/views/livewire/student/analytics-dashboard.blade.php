<?php

use Livewire\Component;
use App\Models\AttendanceRecord;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

new class extends Component {
    public function with(): array
    {
        $studentId = Auth::id();
        
        $activeSemester = \App\Models\Semester::where('is_active', true)->first();
        
        $records = AttendanceRecord::where('student_id', $studentId)
            ->whereHas('session.classSection', function ($q) use ($activeSemester) {
                if ($activeSemester) {
                    $q->where('semester_id', $activeSemester->id);
                }
            })
            ->with('session.classSection.subject')
            ->get();

        $presentCount = $records->where('status', 'present')->count();
        $absentCount = $records->where('status', 'absent')->count();
        $excusedCount = $records->where('status', 'excused')->count();
        $lateCount = $records->where('status', 'late')->count();
        
        $attendedCount = $presentCount + $lateCount + $excusedCount;
        $totalCount = $records->count();
        
        $attendanceRate = $totalCount > 0 ? round(($attendedCount / $totalCount) * 100) : 100;

        $monthlyData = [];
        foreach ($records as $record) {
            $month = Carbon::parse($record->session->date)->format('M Y');
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = ['present' => 0, 'absent' => 0];
            }
            if ($record->status === 'absent') {
                $monthlyData[$month]['absent']++;
            } else {
                $monthlyData[$month]['present']++;
            }
        }
        
        $chartLabels = array_keys($monthlyData);
        $chartPresentData = array_column($monthlyData, 'present');
        $chartAbsentData = array_column($monthlyData, 'absent');

        $subjectBreakdown = [];
        $enrollments = Enrollment::where('student_id', $studentId)
            ->whereHas('classSection', function($q) use ($activeSemester) {
                if ($activeSemester) $q->where('semester_id', $activeSemester->id);
            })
            ->with('classSection.subject')
            ->get();
            
        foreach ($enrollments as $enrollment) {
            $classId = $enrollment->class_section_id;
            $classRecords = $records->where('session.class_section_id', $classId);
            $cTotal = $classRecords->count();
            $cAttended = $classRecords->whereIn('status', ['present', 'late', 'excused'])->count();
            $cAbsent = $classRecords->where('status', 'absent')->count();
            
            $rate = $cTotal > 0 ? round(($cAttended / $cTotal) * 100) : 100;
            
            $subjectBreakdown[] = [
                'subject' => $enrollment->classSection->subject->code ?? 'N/A',
                'name' => $enrollment->classSection->subject->name ?? 'Unknown',
                'section' => $enrollment->classSection->name,
                'rate' => $rate,
                'absences' => $cAbsent,
                'total' => $cTotal,
            ];
        }

        return [
            'stats' => [
                'rate' => $attendanceRate,
                'present' => $presentCount + $lateCount,
                'absent' => $absentCount,
                'excused' => $excusedCount,
            ],
            'chartLabels' => $chartLabels,
            'chartPresentData' => $chartPresentData,
            'chartAbsentData' => $chartAbsentData,
            'subjectBreakdown' => $subjectBreakdown,
            'isAtRisk' => $attendanceRate < 80 && $totalCount > 0,
        ];
    }
}; ?>

<div class="space-y-6">
    <!-- Low Attendance Alert -->
    @if($isAtRisk)
        <div class="bg-error/10 border-l-4 border-error p-4 rounded-r-2xl animate-fade-in">
            <div class="flex items-start">
                <div class="shrink-0">
                    <svg class="h-6 w-6 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-error">Low Attendance Warning</h3>
                    <div class="mt-1 text-sm text-error/80 font-medium">
                        <p>Your overall attendance has dropped below 80%. Consistent absences may affect your academic standing. Please ensure you attend upcoming classes or submit valid excuses for past absences.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="flex justify-end">
        <a href="{{ route('student.export') }}" target="_blank" class="inline-flex items-center justify-center px-6 py-2.5 bg-brand text-white border border-transparent rounded-xl text-sm font-bold shadow-sm hover:bg-brand-hover focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-1 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
            Download PDF Report
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-brand/10 text-brand flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Attendance Rate</p>
                <p class="text-2xl font-bold {{ $stats['rate'] < 80 ? 'text-error' : 'text-brand' }}">{{ $stats['rate'] }}%</p>
            </div>
        </div>
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-success/10 text-success flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Present</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['present'] }}</p>
            </div>
        </div>
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-error/10 text-error flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Absent</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['absent'] }}</p>
            </div>
        </div>
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Excused</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['excused'] }}</p>
            </div>
        </div>
    </div>

    <!-- Charts & Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Trend Chart -->
        <div class="lg:col-span-2 bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm">
            <h3 class="text-lg font-bold text-navy mb-4">Monthly Attendance Trend</h3>
            <div class="relative h-72 w-full" 
                 x-data="{ 
                    init() {
                        if (typeof Chart === 'undefined') return;
                        const ctx = this.$refs.canvas.getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: {{ json_encode($chartLabels) }},
                                datasets: [
                                    {
                                        label: 'Present/Excused',
                                        data: {{ json_encode($chartPresentData) }},
                                        backgroundColor: '#10b981',
                                        borderRadius: 6,
                                    },
                                    {
                                        label: 'Absent',
                                        data: {{ json_encode($chartAbsentData) }},
                                        backgroundColor: '#ef4444',
                                        borderRadius: 6,
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: { beginAtZero: true, stacked: true },
                                    x: { stacked: true }
                                },
                                plugins: {
                                    legend: { position: 'bottom' }
                                }
                            }
                        });
                    }
                 }">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

        <!-- Subject Breakdown -->
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col">
            <h3 class="text-lg font-bold text-navy mb-4">Subject Breakdown</h3>
            <div class="flex-1 overflow-y-auto pr-2 space-y-4 max-h-72">
                @forelse($subjectBreakdown as $subject)
                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-bold text-navy">{{ $subject['subject'] }}</p>
                                <p class="text-xs text-gray-500 font-medium">{{ $subject['section'] }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $subject['rate'] < 80 ? 'bg-error/10 text-error' : 'bg-success/10 text-success' }}">
                                {{ $subject['rate'] }}%
                            </span>
                        </div>
                        <div class="flex justify-between text-sm font-medium">
                            <span class="text-gray-500">Absences: <span class="text-error font-bold">{{ $subject['absences'] }}</span>/{{ $subject['total'] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-sm text-gray-500">No active classes found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
