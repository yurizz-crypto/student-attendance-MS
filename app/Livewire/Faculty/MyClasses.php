<?php

namespace App\Livewire\Faculty;

use App\Exceptions\StaleRecordException;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MyClasses extends Component
{
    use WithFileUploads, WithPagination;

    public $searchTerm = '';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    // Form fields
    public $classId = null;

    public $subjectName = '';

    public $className = '';

    public $scheduleDetails = '';

    // Student enrollment fields
    public $selectedClassId = null;

    public $studentSearchTerm = '';

    public $studentToEnroll = null;

    public $enrolledStudents = [];

    // Modal states
    public $showCreateModal = false;

    public $showEditModal = false;

    public $showDeleteModal = false;

    public $showEnrollModal = false;

    public $showImportModal = false;

    public $importFile;

    public $deleteConfirmClassId = null;

    /** @var int Captured lock_version for optimistic locking */
    public $lockVersion = 0;

    public $lockConflict = false;

    /** @var array<string, mixed> Cascade info for delete modal */
    public $cascadeInfo = [];

    public $filterSemesterId = '';

    public $showManageSemestersModal = false;

    public $isCreatingSemester = false;

    public $newSemesterName = '';

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedFilterSemesterId()
    {
        $this->resetPage();
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

    public function getClasses()
    {
        return ClassSection::where('faculty_id', Auth::id())
            ->with(['subject', 'semester'])
            ->when($this->filterSemesterId, function ($query) {
                return $query->where('semester_id', $this->filterSemesterId);
            })
            ->when($this->searchTerm, function ($query) {
                $search = $this->searchTerm;

                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('subject', function ($sq) use ($search) {
                            $sq->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function openImportModal()
    {
        $this->importFile = null;
        $this->showImportModal = true;
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->importFile = null;
    }

    public function importClasses()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt|max:5120', // Max 5MB
        ]);

        $activeSemester = Semester::where('faculty_id', Auth::id())
            ->where('is_active', true)
            ->first();

        if (! $activeSemester) {
            $this->dispatch('swal:error',
                title: 'No Active Semester',
                message: 'You must create and activate a semester first before importing classes.'
            );

            return;
        }

        $filePath = $this->importFile->getRealPath();
        $file = fopen($filePath, 'r');
        $headers = fgetcsv($file);

        // Guard: empty file
        if (empty($headers)) {
            fclose($file);
            $this->dispatch('swal:error', title: 'Invalid File', message: 'The CSV file is empty. Please upload a file with the correct format.');

            return;
        }

        // Normalize headers (trim whitespace, lowercase)
        $headers = array_map(fn ($h) => strtolower(trim($h)), $headers);

        // Guard: wrong headers
        $requiredColumns = ['subject_name', 'class_name', 'student_identity_id'];
        $missingColumns = array_diff($requiredColumns, $headers);

        if (! empty($missingColumns)) {
            fclose($file);
            $this->dispatch('swal:error',
                title: 'Wrong CSV Format',
                message: 'Missing required columns: '.implode(', ', $missingColumns).'. Expected: subject_name, class_name, schedule_details, student_identity_id.'
            );

            return;
        }

        $classesCreated = 0;
        $studentsEnrolled = 0;
        $skippedRows = 0;

        // Group rows by class to process unique classes first, then enrollments
        $classesData = [];

        while (($rawRow = fgetcsv($file)) !== false) {
            if (count($rawRow) !== count($headers)) {
                $skippedRows++;

                continue;
            }

            $data = array_combine($headers, $rawRow);

            if (empty($data['subject_name']) || empty($data['class_name']) || empty($data['student_identity_id'])) {
                $skippedRows++;

                continue;
            }

            $classKey = $data['subject_name'].'|'.$data['class_name'].'|'.($data['schedule_details'] ?? '');

            if (! isset($classesData[$classKey])) {
                $classesData[$classKey] = [
                    'subject_name' => $data['subject_name'],
                    'class_name' => $data['class_name'],
                    'schedule_details' => $data['schedule_details'] ?? '',
                    'student_ids' => [],
                ];
            }

            $classesData[$classKey]['student_ids'][] = $data['student_identity_id'];
        }

        fclose($file);

        // Guard: no processable data rows
        if (empty($classesData)) {
            $this->dispatch('swal:warning',
                title: 'Nothing to Import',
                message: "All {$skippedRows} rows were skipped due to missing required fields. Check your CSV file."
            );

            return;
        }

        foreach ($classesData as $classGroup) {
            // Auto-create or find subject
            $subject = Subject::firstOrCreate(
                ['name' => $classGroup['subject_name']],
                ['code' => strtoupper(substr(str_replace(' ', '', $classGroup['subject_name']), 0, 5)).'-'.rand(100, 999)]
            );

            // Create or find class
            $class = ClassSection::firstOrCreate([
                'subject_id' => $subject->id,
                'semester_id' => $activeSemester->id,
                'faculty_id' => Auth::id(),
                'name' => $classGroup['class_name'],
            ], [
                'schedule_details' => $classGroup['schedule_details'],
            ]);

            if ($class->wasRecentlyCreated) {
                $classesCreated++;
            }

            // Enroll students
            foreach ($classGroup['student_ids'] as $identityId) {
                $student = User::where('identity_id', $identityId)->where('role', 'student')->first();
                if ($student) {
                    $enrollment = Enrollment::firstOrCreate([
                        'class_section_id' => $class->id,
                        'student_id' => $student->id,
                    ], [
                        'status' => 'active',
                    ]);

                    if ($enrollment->wasRecentlyCreated) {
                        $studentsEnrolled++;
                    } else {
                        $skippedRows++; // Already enrolled
                    }
                } else {
                    $skippedRows++; // Student not found
                }
            }
        }

        AuditService::log(
            'imported',
            ClassSection::class,
            null,
            ['classes_created' => $classesCreated, 'students_enrolled' => $studentsEnrolled],
            "Faculty imported {$classesCreated} classes and enrolled {$studentsEnrolled} students via CSV"
        );

        $this->closeImportModal();
        $this->resetPage();

        $this->dispatch('swal:success', title: 'Import Complete', message: "Created {$classesCreated} new classes and successfully enrolled {$studentsEnrolled} students. Skipped {$skippedRows} invalid or duplicate rows.");
    }

    public function openManageSemesters()
    {
        $this->showManageSemestersModal = true;
        $this->isCreatingSemester = false;
        $this->newSemesterName = '';
    }

    public function closeManageSemesters()
    {
        $this->showManageSemestersModal = false;
        $this->isCreatingSemester = false;
    }

    public function toggleCreateSemester()
    {
        $this->isCreatingSemester = ! $this->isCreatingSemester;
        $this->newSemesterName = '';
    }

    public function createSemester()
    {
        $this->validate([
            'newSemesterName' => 'required|string|max:255',
        ]);

        $activeSemester = Semester::where('faculty_id', Auth::id())
            ->where('is_active', true)
            ->first();

        if ($activeSemester) {
            $this->addError('newSemesterName', "You cannot create a new semester while '{$activeSemester->name}' is still active.");

            return;
        }

        Semester::firstOrCreate([
            'name' => $this->newSemesterName,
            'faculty_id' => Auth::id(),
        ]);

        $this->isCreatingSemester = false;
        $this->newSemesterName = '';
        $this->dispatch('swal:success', title: 'Success', message: 'Semester created successfully!');
    }

    public function markSemesterDone($semesterId)
    {
        $semester = Semester::where('faculty_id', Auth::id())->findOrFail($semesterId);
        $semester->update(['is_active' => false]);
        $this->dispatch('swal:success', title: 'Success', message: 'Semester marked as done!');
    }

    public function openEditModal($classId): void
    {
        $class = ClassSection::where('faculty_id', Auth::id())->findOrFail($classId);

        $this->classId = $class->id;
        $this->subjectName = $class->subject ? $class->subject->name : '';
        $this->className = $class->name;
        $this->scheduleDetails = $class->schedule_details ?? '';
        $this->lockVersion = $class->lock_version ?? 0;
        $this->lockConflict = false;

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function closeClassModal()
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        } elseif ($this->showEditModal) {
            $this->closeEditModal();
        }
    }

    public function openDeleteModal($classId): void
    {
        $class = ClassSection::withCount('enrollments')->where('faculty_id', Auth::id())->findOrFail($classId);
        $this->deleteConfirmClassId = $classId;
        $this->cascadeInfo = [
            'name' => $class->name,
            'enrollments' => $class->enrollments_count,
        ];
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deleteConfirmClassId = null;
        $this->cascadeInfo = [];
    }

    public function openEnrollModal($classId)
    {
        $this->selectedClassId = $classId;
        $this->studentSearchTerm = '';
        $this->studentToEnroll = null;
        $this->enrolledStudents = Enrollment::where('class_section_id', $classId)
            ->with('student')
            ->get()
            ->toArray();
        $this->showEnrollModal = true;
    }

    public function closeEnrollModal()
    {
        $this->showEnrollModal = false;
        $this->selectedClassId = null;
        $this->studentToEnroll = null;
        $this->enrolledStudents = [];
    }

    public function getAvailableStudents()
    {
        if (! $this->studentSearchTerm) {
            return collect([]);
        }

        // Get students that are not already enrolled
        $enrolledIds = Enrollment::where('class_section_id', $this->selectedClassId)
            ->pluck('student_id')
            ->toArray();

        return User::where('role', 'student')
            ->where(function ($query) {
                $search = $this->studentSearchTerm;
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('identity_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->whereNotIn('id', $enrolledIds)
            ->limit(10)
            ->get();
    }

    public function selectStudent($studentId)
    {
        $this->studentToEnroll = User::find($studentId);
    }

    public function enrollStudent()
    {
        if (! $this->studentToEnroll || ! $this->selectedClassId) {
            return;
        }

        // Check if already enrolled
        $existing = Enrollment::where('class_section_id', $this->selectedClassId)
            ->where('student_id', $this->studentToEnroll->id)
            ->exists();

        if ($existing) {
            $this->addError('enrollment', 'Student is already enrolled in this class.');

            return;
        }

        $enrollment = Enrollment::create([
            'class_section_id' => $this->selectedClassId,
            'student_id' => $this->studentToEnroll->id,
            'status' => 'active',
        ]);

        // Log the enrollment
        AuditService::log(
            'enrolled_student',
            ClassSection::class,
            $this->selectedClassId,
            ['student_id' => $this->studentToEnroll->id],
            "Student {$this->studentToEnroll->first_name} {$this->studentToEnroll->last_name} enrolled in class"
        );

        $this->studentToEnroll = null;
        $this->studentSearchTerm = '';
        $this->openEnrollModal($this->selectedClassId);
        $this->dispatch('swal:success', title: 'Success', message: 'Student enrolled successfully!');
    }

    public function unenrollStudent($studentId)
    {
        $enrollment = Enrollment::where('class_section_id', $this->selectedClassId)
            ->where('student_id', $studentId)
            ->firstOrFail();

        $student = $enrollment->student;

        $enrollment->delete();

        // Log the unenrollment
        AuditService::log(
            'unenrolled_student',
            ClassSection::class,
            $this->selectedClassId,
            ['student_id' => $studentId],
            "Student {$student->first_name} {$student->last_name} unenrolled from class"
        );

        $this->openEnrollModal($this->selectedClassId);
        $this->dispatch('swal:success', title: 'Success', message: 'Student unenrolled successfully!');
    }

    public function store()
    {
        $validated = $this->validate([
            'subjectName' => 'required|string|max:255',
            'className' => 'required|string|max:255',
            'scheduleDetails' => 'required|string|max:255',
        ]);

        $activeSemester = Semester::where('faculty_id', Auth::id())
            ->where('is_active', true)
            ->first();

        if (! $activeSemester) {
            $this->addError('general', "You must create an active semester first before creating a class. Click 'Manage Semesters' to create one.");

            return;
        }

        // Auto-create or find subject
        $subject = Subject::firstOrCreate(
            ['name' => $this->subjectName],
            ['code' => strtoupper(substr(str_replace(' ', '', $this->subjectName), 0, 5)).'-'.rand(100, 999)]
        );

        $class = ClassSection::create([
            'subject_id' => $subject->id,
            'semester_id' => $activeSemester->id,
            'faculty_id' => Auth::id(),
            'name' => $this->className,
            'schedule_details' => $this->scheduleDetails,
        ]);

        // Log the creation
        AuditService::logCreated($class, [
            'subject_id' => $class->subject_id,
            'name' => $class->name,
            'schedule_details' => $class->schedule_details,
        ]);

        $this->closeCreateModal();
        $this->resetPage();
        $this->dispatch('swal:success', title: 'Success', message: 'Class created successfully!');
    }

    public function update(): void
    {
        $this->validate([
            'subjectName' => 'required|string|max:255',
            'className' => 'required|string|max:255',
            'scheduleDetails' => 'required|string|max:255',
        ]);

        $class = ClassSection::where('faculty_id', Auth::id())->findOrFail($this->classId);

        // Optimistic locking check
        try {
            $class->checkLockVersion($this->lockVersion);
        } catch (StaleRecordException $e) {
            $this->lockConflict = true;
            $this->dispatch('swal:error', title: 'Edit Conflict', message: 'This class was modified by another user. Please close and reopen the edit form.');

            return;
        }

        $original = $class->getAttributes();

        // Auto-create or find subject
        $subject = Subject::firstOrCreate(
            ['name' => $this->subjectName],
            ['code' => strtoupper(substr(str_replace(' ', '', $this->subjectName), 0, 5)).'-'.rand(100, 999)]
        );

        $class->update([
            'subject_id' => $subject->id,
            'name' => $this->className,
            'schedule_details' => $this->scheduleDetails,
        ]);

        $class->incrementLockVersion();

        $changes = [];
        if ($original['subject_id'] != $subject->id) {
            $changes['subject_id'] = ['old' => $original['subject_id'], 'new' => $subject->id];
        }
        if ($original['name'] != $this->className) {
            $changes['name'] = ['old' => $original['name'], 'new' => $this->className];
        }
        if ($original['schedule_details'] != $this->scheduleDetails) {
            $changes['schedule_details'] = ['old' => $original['schedule_details'], 'new' => $this->scheduleDetails];
        }

        if (! empty($changes)) {
            AuditService::logUpdated($class, $original, $changes);
        }

        $this->closeEditModal();
        $this->resetPage();
        $this->dispatch('swal:success', title: 'Class Updated', message: 'Class has been updated successfully.');
    }

    public function delete()
    {
        if (! $this->deleteConfirmClassId) {
            return;
        }

        $class = ClassSection::where('faculty_id', Auth::id())->findOrFail($this->deleteConfirmClassId);

        // Log the deletion
        AuditService::logDeleted($class);

        $class->delete();

        $this->closeDeleteModal();
        $this->resetPage();
        $this->dispatch('swal:success', title: 'Success', message: 'Class deleted successfully!');
    }

    public function resetForm(): void
    {
        $this->classId = null;
        $this->subjectName = '';
        $this->className = '';
        $this->scheduleDetails = '';
        $this->lockVersion = 0;
        $this->lockConflict = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.faculty.my-classes', [
            'classes' => $this->getClasses(),
            'semesters' => Semester::where('faculty_id', Auth::id())->get(),
            'availableStudents' => $this->getAvailableStudents(),
        ]);
    }
}
