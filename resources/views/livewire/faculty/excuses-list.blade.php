<?php

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $filterStatus = 'all'; // all, pending, approved, rejected
    public $filterSemesterId = '';
    public $filterClassId = '';

    public function mount()
    {
        $activeSemester = \App\Models\Semester::where('is_active', true)->first();
        if ($activeSemester) {
            $this->filterSemesterId = $activeSemester->id;
        }
    }

    public $reviewingExcuseId = null;
    public $reviewingExcuse = null;

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterSemesterId()
    {
        $this->resetPage();
        $this->filterClassId = '';
    }

    public function updatingFilterClassId()
    {
        $this->resetPage();
    }

    public function reviewExcuse($id)
    {
        $this->reviewingExcuseId = $id;
        $this->reviewingExcuse = \App\Models\Excuse::with('student', 'classSection.subject', 'session')->find($id);
    }

    public function closeReview()
    {
        $this->reset(['reviewingExcuseId', 'reviewingExcuse']);
    }

    public function processExcuse($status)
    {
        if (!$this->reviewingExcuse) return;

        $oldStatus = $this->reviewingExcuse->status;
        
        if ($oldStatus === $status) {
            $this->closeReview();
            return;
        }

        $this->reviewingExcuse->update(['status' => $status]);

        $record = \App\Models\AttendanceRecord::where('attendance_session_id', $this->reviewingExcuse->attendance_session_id)
            ->where('student_id', $this->reviewingExcuse->student_id)
            ->first();
        
        if ($record) {
            if ($status === 'approved') {
                $record->update([
                    'status' => 'excused',
                    'remarks' => 'Excuse approved: ' . $this->reviewingExcuse->reason
                ]);
            } elseif ($oldStatus === 'approved' && ($status === 'rejected' || $status === 'pending')) {
                // Revert to absent
                $record->update([
                    'status' => 'absent',
                    'remarks' => 'Excuse revoked. Originally marked absent.'
                ]);
            }
        }

        session()->flash('status', 'Excuse has been ' . $status . '.');
        $this->closeReview();
    }

    public function with(): array
    {
        $facultyId = auth()->id();
        
        $semesters = \App\Models\Semester::orderBy('id', 'desc')->get();

        $classesQuery = \App\Models\ClassSection::where('faculty_id', $facultyId)->with('subject');
        
        if ($this->filterSemesterId) {
            $classesQuery->where('semester_id', $this->filterSemesterId);
        }
        
        $availableClasses = $classesQuery->get();
        $classIds = $availableClasses->pluck('id')->toArray();

        $query = \App\Models\Excuse::whereIn('class_section_id', $classIds)
            ->with(['student', 'classSection.subject', 'session'])
            ->orderBy('created_at', 'desc');

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterClassId) {
            $query->where('class_section_id', $this->filterClassId);
        }

        $baseQuery = \App\Models\Excuse::whereIn('class_section_id', $classIds);
        if ($this->filterClassId) {
            $baseQuery->where('class_section_id', $this->filterClassId);
        }

        return [
            'semesters' => $semesters,
            'availableClasses' => $availableClasses,
            'excuses' => $query->paginate(10),
            'stats' => [
                'total' => (clone $baseQuery)->count(),
                'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
                'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
                'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            ]
        ];
    }
}; ?>

<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Excuses</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow cursor-pointer {{ $filterStatus === 'pending' ? 'ring-2 ring-error' : '' }}" wire:click="$set('filterStatus', 'pending')">
            <div class="w-12 h-12 rounded-2xl bg-error/10 text-error flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Pending</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['pending'] }}</p>
            </div>
        </div>
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow cursor-pointer {{ $filterStatus === 'approved' ? 'ring-2 ring-success' : '' }}" wire:click="$set('filterStatus', 'approved')">
            <div class="w-12 h-12 rounded-2xl bg-success/10 text-success flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Approved</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['approved'] }}</p>
            </div>
        </div>
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow cursor-pointer {{ $filterStatus === 'rejected' ? 'ring-2 ring-gray-400' : '' }}" wire:click="$set('filterStatus', 'rejected')">
            <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Rejected</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['rejected'] }}</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-surface rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-navy">Excuses Management</h3>
                <p class="text-sm text-gray-500 font-medium">Review and process student excuses for your classes.</p>
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

                <select wire:model.live="filterStatus" class="rounded-xl border-gray-300 text-sm focus:ring-brand focus:border-brand font-medium text-navy shadow-sm">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50/50 text-gray-500 text-xs uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4">Student</th>
                        <th class="px-6 py-4">Class & Date Missed</th>
                        <th class="px-6 py-4">Reason Summary</th>
                        <th class="px-6 py-4">Submitted On</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($excuses as $excuse)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand/10 text-brand flex items-center justify-center font-bold">
                                        {{ substr($excuse->student->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-navy">{{ $excuse->student->first_name }} {{ $excuse->student->last_name }}</p>
                                        <p class="text-xs text-gray-500 font-medium">{{ $excuse->student->identity_id ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-navy">{{ $excuse->classSection->subject->code ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 font-medium">{{ $excuse->session->date->format('M d, Y') }} ({{ $excuse->session->start_time }})</p>
                            </td>
                            <td class="px-6 py-4 max-w-[200px] truncate text-gray-600 font-medium" title="{{ $excuse->reason }}">
                                {{ Str::limit($excuse->reason, 30) }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 font-medium">
                                {{ $excuse->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($excuse->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-error/10 text-error">Pending</span>
                                @elseif($excuse->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-success/10 text-success">Approved</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">Rejected</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="reviewExcuse({{ $excuse->id }})" class="text-brand hover:text-brand-hover text-sm font-bold bg-brand/5 hover:bg-brand/10 px-3 py-1.5 rounded-lg transition-colors">
                                    {{ $excuse->status === 'pending' ? 'Review' : 'View Details' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-medium text-gray-500">No excuses found for this filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($excuses->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                {{ $excuses->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>

    <!-- Excuse Review Modal (Reused) -->
    @if($reviewingExcuse)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy/50 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-3xl w-full max-w-lg shadow-xl overflow-hidden animate-fade-in-up">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-navy">Review Excuse</h3>
                    <button wire:click="closeReview" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="p-6 sm:p-8 space-y-6">
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Student Details</p>
                        <p class="text-lg font-bold text-navy">{{ $reviewingExcuse->student->first_name }} {{ $reviewingExcuse->student->last_name }}</p>
                        <p class="text-sm font-medium text-gray-500">{{ $reviewingExcuse->student->identity_id ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Class & Session</p>
                        <p class="text-base font-bold text-navy">{{ $reviewingExcuse->classSection->subject->code ?? 'Unknown' }} - {{ $reviewingExcuse->classSection->name ?? 'Unknown' }}</p>
                        <p class="text-sm font-medium text-gray-500">{{ $reviewingExcuse->session->date->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Reason</p>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $reviewingExcuse->reason }}</p>
                        </div>
                    </div>
                    @if($reviewingExcuse->file_path)
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Supporting Document</p>
                        <div class="flex gap-2">
                            <a href="{{ Storage::url($reviewingExcuse->file_path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-brand/10 text-brand rounded-xl text-sm font-bold hover:bg-brand/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                View File
                            </a>
                            <a href="{{ Storage::url($reviewingExcuse->file_path) }}" download class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                Download
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="p-6 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row gap-3 justify-end items-center">
                    @if($reviewingExcuse->status !== 'pending')
                        <span class="text-sm font-medium text-gray-500 mr-auto">Current Status: <strong class="capitalize">{{ $reviewingExcuse->status }}</strong></span>
                    @endif
                    
                    <button wire:click="closeReview" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-bold hover:bg-gray-100 transition-colors sm:mr-2">
                        Close
                    </button>

                    @if($reviewingExcuse->status !== 'rejected')
                    <button wire:click="processExcuse('rejected')" class="px-6 py-2.5 rounded-xl border border-error text-error font-bold hover:bg-error/10 transition-colors">
                        {{ $reviewingExcuse->status === 'approved' ? 'Revoke & Reject' : 'Reject' }}
                    </button>
                    @endif

                    @if($reviewingExcuse->status !== 'approved')
                    <button wire:click="processExcuse('approved')" class="px-6 py-2.5 rounded-xl border border-transparent bg-brand text-white font-bold hover:bg-brand-hover transition-colors shadow-sm">
                        Approve
                    </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
