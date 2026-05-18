<?php

namespace App\Livewire\Faculty;

use App\Exports\AttendanceSessionsExport;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\ClassSection;
use App\Notifications\ClassSessionCreatedNotification;
use App\Services\AttendanceService;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceRecords extends Component
{
    use WithPagination;

    public $classId = null;

    public $searchTerm = '';

    public $filterStatus = '';

    public $filterClassId = '';

    public $sortField = 'date';

    public $sortDirection = 'desc';

    public $perPage = 10;

    public $dateFrom = '';

    public $dateTo = '';

    // Form fields
    public $attendanceDate = '';

    public $startTime = '';

    public $endTime = '';

    public $sessionId = null;

    public $studentAttendance = [];

    // Modal states
    public $showSessionModal = false;

    public $showRecordModal = false;

    public $showQrModal = false;

    public $selectedSessionId = null;

    public $sessionQrCode = null;

    public function mount()
    {
        $this->attendanceDate = today()->format('Y-m-d');
        $this->startTime = '08:00';
        $this->endTime = '10:00';
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterClassId()
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function exportExcel(): mixed
    {
        $classIds = ClassSection::where('faculty_id', Auth::id())->pluck('id');

        $sessions = AttendanceSession::whereIn('class_section_id', $classIds)
            ->when($this->filterClassId, fn ($q) => $q->where('class_section_id', $this->filterClassId))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('date', '<=', $this->dateTo))
            ->with('classSection.subject')
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        return Excel::download(
            new AttendanceSessionsExport($sessions),
            'attendance_sessions_'.now()->format('Y-m-d_His').'.xlsx'
        );
    }

    // ── Inline blur validation ────────────────────────────────────────────────

    public function updatedClassId(): void
    {
        $this->validateOnly('classId', ['classId' => 'required|exists:class_sections,id']);
    }

    public function updatedAttendanceDate(): void
    {
        $this->validateOnly('attendanceDate', ['attendanceDate' => 'required|date']);
    }

    public function sort($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getFacultyClasses()
    {
        return ClassSection::where('faculty_id', Auth::id())
            ->with('subject')
            ->get();
    }

    public function getActiveFacultyClasses()
    {
        return ClassSection::where('faculty_id', Auth::id())
            ->whereHas('semester', function ($q) {
                $q->where('is_active', true);
            })
            ->with('subject')
            ->get();
    }

    public function getAttendanceSessions()
    {
        $classIds = ClassSection::where('faculty_id', Auth::id())->pluck('id');

        return AttendanceSession::whereIn('class_section_id', $classIds)
            ->when($this->filterClassId, fn ($q) => $q->where('class_section_id', $this->filterClassId))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('date', '<=', $this->dateTo))
            ->when($this->searchTerm, function ($q) {
                $search = $this->searchTerm;

                return $q->whereHas('classSection', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            })
            ->with('classSection.subject')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function openSessionModal()
    {
        $this->attendanceDate = today()->format('Y-m-d');
        $this->startTime = '08:00';
        $this->endTime = '10:00';
        $this->classId = null;
        $this->showSessionModal = true;
    }

    public function closeSessionModal()
    {
        $this->showSessionModal = false;
        $this->classId = null;
    }

    public function createSession()
    {
        $this->validate([
            'attendanceDate' => 'required|date',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
            'classId' => 'required|exists:class_sections,id',
        ]);

        // Verify faculty owns this class
        $class = ClassSection::where('faculty_id', Auth::id())
            ->findOrFail($this->classId);

        // Check if session already exists for this date
        $existing = AttendanceSession::where('class_section_id', $class->id)
            ->whereDate('date', $this->attendanceDate)
            ->exists();

        if ($existing) {
            $this->addError('attendanceDate', 'Session already exists for this date and class.');

            return;
        }

        // Generate QR code data
        $qrData = implode('|', [
            // We'll set the session ID after creation
            'temp',
            $class->id,
            $this->attendanceDate,
            Auth::id(),
        ]);

        $session = AttendanceSession::create([
            'class_section_id' => $class->id,
            'date' => $this->attendanceDate,
            'status' => 'open',
            'attendance_code' => strtoupper(Str::random(6)),
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'qr_code_data' => null, // Will be set after
        ]);

        // Notify enrolled students
        $students = $class->students;
        foreach ($students as $student) {
            $student->notify(new ClassSessionCreatedNotification($session));
        }

        // Now generate the final QR data with the session ID
        $finalQrData = implode('|', [
            $session->id,
            $class->id,
            $this->attendanceDate,
            Auth::id(),
        ]);

        $session->update(['qr_code_data' => $finalQrData]);

        // Log session creation
        AuditService::log(
            'created_attendance_session',
            AttendanceSession::class,
            $session->id,
            ['date' => $session->date, 'class_id' => $class->id],
            "Attendance session created for {$class->name}"
        );

        $this->closeSessionModal();
        $this->resetPage();
        $this->dispatch('swal:success', title: 'Success', message: 'Attendance session created!');
    }

    public function openRecordModal($sessionId)
    {
        $session = AttendanceSession::find($sessionId);

        if (
            ! $session || ! ClassSection::where('faculty_id', Auth::id())
                ->where('id', $session->class_section_id)
                ->exists()
        ) {
            abort(403);
        }

        $this->selectedSessionId = $sessionId;
        $this->loadAttendanceRecords($sessionId);
        $this->showRecordModal = true;
    }

    public function closeRecordModal()
    {
        $this->showRecordModal = false;
        $this->selectedSessionId = null;
        $this->studentAttendance = [];
    }

    public function loadAttendanceRecords($sessionId)
    {
        $session = AttendanceSession::with('classSection.enrollments.student')
            ->findOrFail($sessionId);

        $this->studentAttendance = [];

        // Get all enrolled students
        foreach ($session->classSection->enrollments as $enrollment) {
            $record = AttendanceRecord::where('attendance_session_id', $sessionId)
                ->where('student_id', $enrollment->student_id)
                ->first();

            $this->studentAttendance[] = [
                'student_id' => $enrollment->student_id,
                'student_name' => $enrollment->student->first_name.' '.$enrollment->student->last_name,
                'student_identity' => $enrollment->student->identity_id,
                'student_status' => $enrollment->student->status,
                'status' => $record?->status ?? 'absent',
                'remarks' => $record?->remarks ?? '',
                'record_id' => $record?->id,
            ];
        }
    }

    public function updateAttendanceStatus($index, $status)
    {
        if (isset($this->studentAttendance[$index])) {
            $this->studentAttendance[$index]['status'] = $status;
        }
    }

    public function updateAttendanceRemark($index, $remark)
    {
        if (isset($this->studentAttendance[$index])) {
            $this->studentAttendance[$index]['remarks'] = $remark;
        }
    }

    public function saveAttendance()
    {
        if (! $this->selectedSessionId || empty($this->studentAttendance)) {
            return;
        }

        $session = AttendanceSession::findOrFail($this->selectedSessionId);

        // Verify faculty owns this class
        if (
            ! ClassSection::where('faculty_id', Auth::id())
                ->where('id', $session->class_section_id)
                ->exists()
        ) {
            abort(403);
        }

        // Prepare student data for the service
        $studentData = array_map(function ($record) {
            return [
                'student_id' => $record['student_id'],
                'status' => $record['status'],
                'remarks' => $record['remarks'] ?: null,
            ];
        }, $this->studentAttendance);

        // Use the service to save attendance
        $service = new AttendanceService;
        $service->recordBulkAttendance(
            $session->classSection,
            $session->date->format('Y-m-d'),
            $studentData
        );

        // Log the attendance recording
        AuditService::log(
            'recorded_attendance',
            AttendanceSession::class,
            $session->id,
            ['students_count' => count($studentData)],
            "Attendance recorded for session on {$session->date->format('M d, Y')}"
        );

        // Close the session
        $session->update([
            'status' => 'closed',
            'end_time' => now()->format('H:i:s'),
        ]);

        $this->closeRecordModal();
        $this->resetPage();
        $this->dispatch('swal:success', title: 'Success', message: 'Attendance saved successfully!');
    }

    public function openQrModal($sessionId)
    {
        $session = AttendanceSession::find($sessionId);

        if (
            ! $session || ! ClassSection::where('faculty_id', Auth::id())
                ->where('id', $session->class_section_id)
                ->exists()
        ) {
            abort(403);
        }

        $this->selectedSessionId = $sessionId;

        // Generate QR code from stored data
        $qrData = $session->qr_code_data ?? implode('|', [
            $session->id,
            $session->class_section_id,
            $session->date->format('Y-m-d'),
            Auth::id(),
        ]);

        $this->sessionQrCode = $this->generateQrCode($qrData);
        $this->showQrModal = true;
    }

    public function closeQrModal()
    {
        $this->showQrModal = false;
        $this->selectedSessionId = null;
        $this->sessionQrCode = null;
    }

    private function generateQrCode($data)
    {
        return (string) QrCode::size(256)->margin(1)->generate($data);
    }

    public function closeSession($sessionId)
    {
        $session = AttendanceSession::findOrFail($sessionId);

        // Verify faculty owns this class
        if (
            ! ClassSection::where('faculty_id', Auth::id())
                ->where('id', $session->class_section_id)
                ->exists()
        ) {
            abort(403);
        }

        $session->update([
            'status' => 'closed',
            'end_time' => now()->format('H:i:s'),
        ]);

        // Log the session closure
        AuditService::log(
            'closed_attendance_session',
            AttendanceSession::class,
            $session->id,
            [],
            'Attendance session closed'
        );

        $this->resetPage();
        $this->dispatch('swal:success', title: 'Success', message: 'Session closed successfully!');
    }

    public function deleteSession($sessionId)
    {
        $session = AttendanceSession::findOrFail($sessionId);

        // Verify faculty owns this class
        if (
            ! ClassSection::where('faculty_id', Auth::id())
                ->where('id', $session->class_section_id)
                ->exists()
        ) {
            abort(403);
        }

        // Log the deletion
        AuditService::logDeleted($session);

        $session->delete();

        $this->resetPage();
        $this->dispatch('swal:success', title: 'Success', message: 'Session deleted!');
    }

    public function render()
    {
        return view('livewire.faculty.attendance-records', [
            'sessions' => $this->getAttendanceSessions(),
            'facultyClasses' => $this->getFacultyClasses(),
            'activeFacultyClasses' => $this->getActiveFacultyClasses(),
        ]);
    }
}
