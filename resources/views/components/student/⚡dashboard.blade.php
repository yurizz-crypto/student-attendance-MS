<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceRecord;

new class extends Component {
    use WithFileUploads;

    // UI State
    public string $activeTab = 'scanner'; 

    // Form Properties
    public $attendanceCode;
    public $excuseDate;
    public $excuseReason;
    public $excuseFile;

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function submitCode()
    {
        // To be implemented: Validate code against active sessions and mark present
        session()->flash('status', 'Code submission logic coming soon!');
    }

    public function submitExcuse()
    {
        // To be implemented: Upload file to storage and update record status
        session()->flash('status', 'Excuse submission logic coming soon!');
    }

    public function with(): array
    {
        $user = Auth::user();
        
        // Fetch all attendance records for the logged-in student
        $allRecords = AttendanceRecord::where('student_id', $user->id)->get();

        $present = $allRecords->where('status', 'present')->count();
        $late = $allRecords->where('status', 'late')->count();
        $absent = $allRecords->where('status', 'absent')->count();
        
        $totalClasses = $allRecords->count();
        
        // Calculate rate (treating late as present for the overall percentage, adjust as needed based on your policy)
        $rate = $totalClasses > 0 ? round((($present + $late) / $totalClasses) * 100) : 100;

        // Fetch the 5 most recent records and eager load relationships to avoid N+1 queries
        $recentRecords = AttendanceRecord::with(['session.classSection.subject'])
            ->where('student_id', $user->id)
            ->latest('created_at')
            ->take(5)
            ->get();

        return [
            'stats' => [
                'attendance_rate' => $rate,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
            ],
            'recentRecords' => $recentRecords,
            'student' => $user,
        ];
    }
}; ?>

<div class="space-y-8 animate-fade-in-up">
    @if (session()->has('status'))
        <div class="p-4 bg-brand/10 border border-brand/20 text-brand rounded-xl font-bold">
            {{ session('status') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-navy">Welcome back, {{ $student->first_name }}!</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">Student ID: {{ $student->identity_id }}</p>
        </div>
        <div class="bg-surface px-4 py-2 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-success animate-pulse"></div>
            <span class="text-sm font-semibold text-navy">Live Data Sync</span>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-info/10 text-info flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h2.625L7.5 9.25l3.25 7.5L14.375 13H21" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Overall Rate</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['attendance_rate'] }}%</p>
            </div>
        </div>

        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-success/10 text-success flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Present</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['present'] }}</p>
            </div>
        </div>

        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-error/10 text-error flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Absent</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['absent'] }}</p>
            </div>
        </div>

        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-warning/10 text-warning flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Late</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['late'] }}</p>
            </div>
        </div>
    </div>

    {{-- Form Sections Removed for Brevity (Scanner, Manual, Excuse) --}}
    {{-- You can keep your existing tab design here --}}
    <div class="bg-surface rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-100 bg-gray-50/50 px-2 sm:px-6 pt-2 overflow-x-auto hide-scrollbar">
            <button wire:click="setTab('scanner')" class="px-6 py-4 text-sm font-bold border-b-2 transition-all whitespace-nowrap {{ $activeTab === 'scanner' ? 'border-brand text-brand' : 'border-transparent text-gray-400 hover:text-navy hover:border-gray-300' }}">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" /></svg>
                    QR Scanner
                </div>
            </button>
            <button wire:click="setTab('manual')" class="px-6 py-4 text-sm font-bold border-b-2 transition-all whitespace-nowrap {{ $activeTab === 'manual' ? 'border-brand text-brand' : 'border-transparent text-gray-400 hover:text-navy hover:border-gray-300' }}">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>
                    Manual Entry
                </div>
            </button>
            <button wire:click="setTab('excuse')" class="px-6 py-4 text-sm font-bold border-b-2 transition-all whitespace-nowrap {{ $activeTab === 'excuse' ? 'border-brand text-brand' : 'border-transparent text-gray-400 hover:text-navy hover:border-gray-300' }}">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    Submit Excuse
                </div>
            </button>
        </div>

        <div class="p-6 sm:p-8">
            @if($activeTab === 'scanner')
                <div class="flex flex-col items-center justify-center py-8">
                    <div class="w-64 h-64 bg-gray-50 border-2 border-dashed border-gray-300 rounded-3xl flex flex-col items-center justify-center relative overflow-hidden mb-6 group">
                        <div class="absolute inset-0 bg-brand/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                        </svg>
                        <p class="text-sm font-medium text-gray-500">Camera preview will appear here</p>
                    </div>
                    <button class="bg-brand text-white px-8 py-3 rounded-xl font-semibold shadow-sm hover:bg-brand-hover transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-brand">
                        Start Scanner
                    </button>
                </div>
            @endif

            @if($activeTab === 'manual')
                <div class="max-w-md mx-auto py-4">
                    <form wire:submit="submitCode" class="space-y-5">
                        <div>
                            <label for="code" class="block text-sm font-semibold text-navy">Class Attendance Code</label>
                            <input type="text" wire:model="attendanceCode" id="code" class="mt-2 block w-full rounded-xl border-0 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand sm:text-sm sm:leading-6 bg-gray-50" placeholder="e.g. A7X9-B2" required>
                        </div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-brand hover:bg-brand-hover transition-colors">
                            Submit Code
                        </button>
                    </form>
                </div>
            @endif

            @if($activeTab === 'excuse')
                <div class="max-w-2xl mx-auto py-4">
                    <form wire:submit="submitExcuse" class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="date" class="block text-sm font-semibold text-navy">Date of Absence</label>
                                <input type="date" wire:model="excuseDate" id="date" class="mt-2 block w-full rounded-xl border-0 py-2.5 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-brand sm:text-sm bg-gray-50" required>
                            </div>
                            <div>
                                <label for="file" class="block text-sm font-semibold text-navy">Supporting Document</label>
                                <input type="file" wire:model="excuseFile" id="file" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-brand/10 file:text-brand" required>
                            </div>
                        </div>
                        <div>
                            <label for="reason" class="block text-sm font-semibold text-navy">Reason for Absence</label>
                            <textarea wire:model="excuseReason" id="reason" rows="4" class="mt-2 block w-full rounded-xl border-0 py-3 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-brand sm:text-sm bg-gray-50" required></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="py-2.5 px-6 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-brand hover:bg-brand-hover transition-colors">
                                Submit for Review
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <div>
        <h2 class="text-xl font-bold text-navy mb-4">Recent Records</h2>
        <div class="bg-surface rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Subject</th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($recentRecords as $record)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm text-navy font-semibold">
                                    {{ $record->session->date->format('M d, Y') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600 group-hover:text-navy transition-colors">
                                    {{ $record->session->classSection->subject->code ?? 'N/A' }} - {{ $record->session->classSection->name ?? 'N/A' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @if($record->status === 'present')
                                        <span class="inline-flex items-center rounded-lg bg-success/10 px-2.5 py-1 text-xs font-bold text-success ring-1 ring-inset ring-success/20">Present</span>
                                    @elseif($record->status === 'absent')
                                        <span class="inline-flex items-center rounded-lg bg-error/10 px-2.5 py-1 text-xs font-bold text-error ring-1 ring-inset ring-error/20">Absent</span>
                                    @elseif($record->status === 'late')
                                        <span class="inline-flex items-center rounded-lg bg-warning/10 px-2.5 py-1 text-xs font-bold text-warning ring-1 ring-inset ring-warning/20">Late</span>
                                    @else
                                        <span class="inline-flex items-center rounded-lg bg-info/10 px-2.5 py-1 text-xs font-bold text-info ring-1 ring-inset ring-info/20">{{ ucfirst($record->status) }}</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $record->remarks ?? '--' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-sm font-medium text-gray-400">
                                    No attendance records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>