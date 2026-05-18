<?php

use Livewire\Component;
use Illuminate\Support\Str;
use App\Services\AuditService;

new class extends Component {
    // State for QR Generator
    public $selectedClass = '';
    public $generatedCode = null;
    public $generatedSessionId = null;
    public $generatedQrCode = null;
    public $expiryTime = 5; // 5 minutes default
    public $showQrModal = false;

    public $reviewingExcuseId = null;
    public $reviewingExcuse = null;

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
                    'remarks' => 'Excuse approved: ' . $this->reviewingExcuse->reason,
                ]);
            } elseif ($oldStatus === 'approved' && ($status === 'rejected' || $status === 'pending')) {
                // Revert to absent
                $record->update([
                    'status' => 'absent',
                    'remarks' => 'Excuse revoked. Originally marked absent.',
                ]);
            }
        }

        $this->reviewingExcuse->student->notify(new \App\Notifications\ExcuseProcessedNotification($this->reviewingExcuse));

        AuditService::log(
            'excuse_processed',
            \App\Models\Excuse::class,
            $this->reviewingExcuse->id,
            ['status' => $status, 'old_status' => $oldStatus],
            'Faculty processed excuse: ' . $status
        );

        session()->flash('status', 'Excuse has been ' . $status . '.');
        $this->closeReview();
    }

    public function generateCode()
    {
        $this->validate(['selectedClass' => 'required']);

        $class = \App\Models\ClassSection::where('faculty_id', auth()->id())->findOrFail($this->selectedClass);

        // Always generate a fresh, unique code — close any existing open session first.
        \App\Models\AttendanceSession::where('class_section_id', $class->id)
            ->whereDate('date', today())
            ->where('status', 'open')
            ->update(['status' => 'closed']);

        $code = strtoupper(\Illuminate\Support\Str::random(6));

        $session = \App\Models\AttendanceSession::create([
            'class_section_id' => $class->id,
            'date' => today(),
            'status' => 'open',
            'attendance_code' => $code,
            'start_time' => now()->format('H:i:s'),
            'end_time' => now()->addMinutes((int) $this->expiryTime)->format('H:i:s'),
        ]);

        $qrData = implode('|', [
            $session->id,
            $class->id,
            today()->format('Y-m-d'),
            auth()->id(),
        ]);
        $session->update(['qr_code_data' => $qrData]);

        $students = $class->enrollments()->with('student')->get()->pluck('student');
        \Illuminate\Support\Facades\Notification::send($students, new \App\Notifications\SessionStartedNotification($session));

        $this->generatedCode = $session->attendance_code;
        $this->generatedSessionId = $session->id;
        $this->generatedQrCode = (string) \SimpleSoftwareIO\QrCode\Facades\QrCode::size(256)->margin(1)->generate($qrData);
    }

    public function openQrModal()
    {
        $this->showQrModal = true;
    }

    public function closeQrModal()
    {
        $this->showQrModal = false;
    }

    public function with(): array
    {
        $facultyId = auth()->id();
        
        // Get faculty's classes, filtered by active semester
        $myClasses = \App\Models\ClassSection::where('faculty_id', $facultyId)
            ->whereHas('semester', function($q) {
                $q->where('is_active', true);
            })
            ->with('subject', 'semester')
            ->get();
            
        $classIds = $myClasses->pluck('id')->toArray();
        
        // Today's classes count
        $todayClassesCount = \App\Models\AttendanceSession::whereIn('class_section_id', $classIds)
            ->whereDate('date', today())
            ->count();
            
        // Active students count (unique students enrolled in faculty's classes)
        $activeStudentsCount = \App\Models\Enrollment::whereIn('class_section_id', $classIds)
            ->where('status', 'active')
            ->distinct('student_id')
            ->count('student_id');
            
        // Pending excuses count
        $pendingExcuses = \App\Models\Excuse::whereIn('class_section_id', $classIds)
            ->where('status', 'pending')
            ->with(['student', 'classSection.subject', 'session'])
            ->get();
            
        $pendingExcusesCount = $pendingExcuses->count();
            
        // Avg attendance rate
        $totalRecords = \App\Models\AttendanceRecord::whereHas('session', function($q) use ($classIds) {
                $q->whereIn('class_section_id', $classIds);
            })->count();
            
        $presentRecords = \App\Models\AttendanceRecord::whereHas('session', function($q) use ($classIds) {
                $q->whereIn('class_section_id', $classIds);
            })
            ->whereIn('status', ['present', 'late'])
            ->count();
            
        $avgAttendance = $totalRecords > 0 ? round(($presentRecords / $totalRecords) * 100) : 100;

        // Format my classes for the dropdown
        $formattedClasses = $myClasses->mapWithKeys(function($class) {
            $name = ($class->subject ? $class->subject->code : '') . ' - ' . $class->name;
            return [$class->id => $name];
        })->toArray();

        return [
            'stats' => [
                'today_classes' => $todayClassesCount,
                'active_students' => $activeStudentsCount,
                'pending_excuses' => $pendingExcusesCount,
                'avg_attendance' => $avgAttendance,
            ],
            'my_classes' => $formattedClasses,
            'pending_excuses_list' => $pendingExcuses
        ];
    }
}; ?>

<div class="space-y-8 animate-fade-in-up">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-navy">Faculty Overview</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">Instructor ID: {{ auth()->user()->identity_id ?? 'N/A' }} <span class="mx-2">•</span> College of Information Sciences and Computing</p>
        </div>
        <div class="bg-surface px-4 py-2 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-success animate-pulse"></div>
            <span class="text-sm font-semibold text-navy">Live System Active</span>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-info/10 text-info flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Today's Classes</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['today_classes'] }}</p>
            </div>
        </div>

        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-brand/10 text-brand flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Active Students</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['active_students'] }}</p>
            </div>
        </div>

        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-error/10 text-error flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Pending Excuses</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['pending_excuses'] }}</p>
            </div>
        </div>

        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-success/10 text-success flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h2.625L7.5 9.25l3.25 7.5L14.375 13H21" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Avg Attendance</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['avg_attendance'] }}%</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-surface rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-bold text-navy">Start Attendance Session</h2>
                    <p class="text-sm text-gray-500 font-medium">Generate a secure code for your current class.</p>
                </div>
                <div class="w-10 h-10 bg-brand/10 text-brand rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" /></svg>
                </div>
            </div>

            <div class="p-6 sm:p-8 flex-1 flex flex-col md:flex-row gap-8">
                <div class="flex-1 space-y-5">
                    <form wire:submit="generateCode" class="space-y-5">
                        <div>
                            <label for="class" class="block text-sm font-semibold text-navy">Select Class</label>
                            <select wire:model="selectedClass" id="class" class="mt-2 block w-full rounded-xl border-0 py-3 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-brand sm:text-sm sm:leading-6 bg-gray-50 font-medium">
                                <option value="">-- Choose a scheduled class --</option>
                                @foreach($my_classes as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('selectedClass') <span class="text-error text-xs font-medium mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="expiry" class="block text-sm font-semibold text-navy">Code Validity Duration</label>
                            <select wire:model="expiryTime" id="expiry" class="mt-2 block w-full rounded-xl border-0 py-3 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-brand sm:text-sm sm:leading-6 bg-gray-50 font-medium">
                                <option value="5">5 Minutes</option>
                                <option value="10">10 Minutes</option>
                                <option value="15">15 Minutes</option>
                                <option value="30">30 Minutes</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full mt-2 py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-brand hover:bg-brand-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand transition-all transform hover:-translate-y-0.5">
                            Generate Code & QR
                        </button>
                    </form>
                </div>

                <div class="flex-1 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center p-6 relative overflow-hidden min-h-[250px]">
                    @if($generatedCode)
                        <div class="text-center animate-fade-in-up">
                            <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-2">Attendance Code</p>
                            <div class="bg-white px-8 py-4 rounded-2xl shadow-sm border border-gray-100 mb-4">
                                <h3 class="text-4xl md:text-5xl font-black text-brand tracking-[0.25em] ml-2">{{ $generatedCode }}</h3>
                            </div>
                            <p class="text-sm font-medium text-gray-500">Share this code or project the QR code.</p>
                            
                            <div class="mt-4 flex gap-2 justify-center">
                                <button wire:click="openQrModal" class="text-xs font-bold text-navy bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition-colors shadow-sm flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Show QR Code
                                </button>
                                <button onclick="copyDashboardCode('{{ $generatedCode }}')"
                                    class="text-xs font-bold text-white bg-brand px-3 py-1.5 rounded-lg hover:bg-brand-hover transition-colors shadow-sm flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    Copy Code
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-sm font-medium">Select a class and generate<br/>a code to display it here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-3xl border border-gray-100 shadow-sm flex flex-col h-full overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h2 class="text-lg font-bold text-navy">Action Required</h2>
                <span class="bg-error/10 text-error ring-1 ring-error/20 py-1 px-2.5 rounded-full text-xs font-bold">{{ $stats['pending_excuses'] }} Pending</span>
            </div>
            
            <div class="flex-1 p-4 space-y-3 overflow-y-auto">
                @forelse($pending_excuses_list as $excuse)
                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-2xl hover:bg-gray-50 transition-colors group">
                    <div>
                        <p class="text-sm font-bold text-navy">{{ $excuse->student->first_name }} {{ $excuse->student->last_name }}</p>
                        <p class="text-xs font-medium text-gray-500 mt-0.5">{{ $excuse->classSection->subject->code ?? 'Unknown' }} • {{ Str::limit($excuse->reason ?? 'No reason provided', 50) }}</p>
                    </div>
                    <button wire:click="reviewExcuse({{ $excuse->id }})" class="text-brand hover:text-brand-hover text-sm font-bold bg-brand/5 px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-all">
                        Review
                    </button>
                </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-sm text-gray-500 font-medium">No pending excuses.</p>
                    </div>
                @endforelse
            </div>

            <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                <a href="{{ route('faculty.excuses') }}" class="block w-full text-center text-sm font-bold text-gray-600 hover:text-brand transition-colors">
                    View All Excuses &rarr;
                </a>
            </div>
        </div>
        
    </div>

    <!-- Excuse Review Modal -->
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

    <!-- QR Code Modal (Dashboard) -->
    @if($showQrModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click="closeQrModal">
            <div class="bg-white rounded-3xl shadow-xl max-w-sm w-full overflow-hidden animate-fade-in-up" @click.stop>
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-navy">Attendance QR Code</h3>
                    <button wire:click="closeQrModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-center bg-white p-4 rounded-xl border border-gray-200">
                        @if($generatedQrCode)
                            {!! $generatedQrCode !!}
                        @else
                            <div class="flex flex-col items-center gap-2 text-gray-400 py-8">
                                <svg class="w-12 h-12 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                <span class="text-sm">Generating QR code...</span>
                            </div>
                        @endif
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 mb-3">Students scan this QR code or use the manual code below.</p>
                        <div class="inline-flex flex-col items-center p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <span class="text-xs text-gray-500 font-medium mb-1">Manual Code:</span>
                            <div class="flex items-center gap-2">
                                <span class="text-2xl font-black font-mono text-brand tracking-[0.2em]">{{ $generatedCode }}</span>
                                <button onclick="copyDashboardCode('{{ $generatedCode }}')" class="p-1.5 text-gray-400 hover:text-brand bg-white rounded-lg border border-gray-200 hover:border-brand shadow-sm transition-all" title="Copy Code">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="window.print()" class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-xl font-semibold hover:bg-gray-200 transition-colors flex items-center justify-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Print
                        </button>
                        <button wire:click="closeQrModal" class="flex-1 px-4 py-2 bg-brand text-white rounded-xl font-semibold hover:bg-brand-hover transition-colors text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
    function copyDashboardCode(code) {
        function onSuccess() {
            window.dispatchEvent(new CustomEvent('swal:success', {
                detail: { title: 'Copied!', message: 'Attendance code copied to clipboard' }
            }));
        }
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(code).then(onSuccess).catch(function() { fallbackDashboardCopy(code, onSuccess); });
        } else {
            fallbackDashboardCopy(code, onSuccess);
        }
    }
    function fallbackDashboardCopy(text, onSuccess) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        try { document.execCommand('copy'); if (onSuccess) onSuccess(); } catch(e) {}
        document.body.removeChild(ta);
    }
    </script>
</div>