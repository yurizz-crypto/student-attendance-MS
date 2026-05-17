<?php

namespace App\Livewire\Admin;

use App\Exceptions\StaleRecordException;
use App\Exports\UserImportErrorExport;
use App\Imports\UsersImport;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class UserManagement extends Component
{
    use WithFileUploads, WithPagination;

    public $searchTerm = '';

    public $filterRole = '';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $showTrashed = false;

    // Form fields
    public $userId = null;

    public $firstName = '';

    public $middleName = '';

    public $lastName = '';

    public $identityId = '';

    public $email = '';

    public $password = '';

    public $role = 'student';

    public $status = 'active';

    public $permissions = [];

    /** @var int Captured lock_version for optimistic locking */
    public $lockVersion = 0;

    public $lockConflict = false;

    // Modal states
    public $showAddModal = false;

    public $showEditModal = false;

    public $showDeleteModal = false;

    public $showImportModal = false;

    public $importFile;

    public $importStep = 1;

    public $validRows = [];

    public $invalidRows = [];

    public $importProgress = 0;

    public $totalValidRows = 0;

    public $deleteConfirmUserId = null;

    /** @var array<string, mixed> Info about cascading effects for the user being deleted */
    public $cascadeInfo = [];

    public function updatedSearchTerm(): void
    {
        $this->resetPage();
    }

    public function updatedFilterRole(): void
    {
        $this->resetPage();
    }

    public function toggleTrashed(): void
    {
        $this->showTrashed = ! $this->showTrashed;
        $this->resetPage();
    }

    public function sort($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getUsers()
    {
        $query = User::when($this->showTrashed, fn ($q) => $q->onlyTrashed())
            ->when($this->searchTerm, function ($query) {
                $search = $this->searchTerm;

                return $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('identity_id', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($this->filterRole, function ($query) {
                return $query->where('role', $this->filterRole);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(10);
    }

    public function openAddModal(): void
    {
        $this->resetForm();
        $this->showAddModal = true;
    }

    public function closeAddModal(): void
    {
        $this->showAddModal = false;
        $this->resetForm();
    }

    public function openEditModal($userId): void
    {
        $user = User::findOrFail($userId);

        $this->userId = $user->id;
        $this->firstName = $user->first_name;
        $this->middleName = $user->middle_name;
        $this->lastName = $user->last_name;
        $this->identityId = $user->identity_id;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->role;
        $this->status = $user->status ?? 'active';
        $this->permissions = $user->permissions ?? [];
        $this->lockVersion = $user->lock_version ?? 0;
        $this->lockConflict = false;

        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function openDeleteModal($userId): void
    {
        $user = User::withCount(['enrollments', 'attendanceRecords'])->findOrFail($userId);

        $this->deleteConfirmUserId = $userId;
        $this->cascadeInfo = [
            'name' => $user->first_name.' '.$user->last_name,
            'enrollments' => $user->enrollments_count,
            'attendance_records' => $user->attendance_records_count,
        ];
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deleteConfirmUserId = null;
        $this->cascadeInfo = [];
    }

    public function openImportModal(): void
    {
        $this->importFile = null;
        $this->importStep = 1;
        $this->validRows = [];
        $this->invalidRows = [];
        $this->importProgress = 0;
        $this->totalValidRows = 0;
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importStep = 1;
        $this->validRows = [];
        $this->invalidRows = [];
    }

    public function previewImport(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        $rows = Excel::toArray(new UsersImport, $this->importFile)[0];

        // Abort immediately if the file has no rows or completely wrong headers
        if (empty($rows)) {
            $this->dispatch('swal:error', title: 'Invalid File', message: 'The file is empty. Please upload a file with the correct format.');

            return;
        }

        $requiredColumns = ['first_name', 'last_name', 'identity_id', 'email', 'role'];
        $fileColumns = array_keys($rows[0]);
        $missingColumns = array_diff($requiredColumns, $fileColumns);

        if (! empty($missingColumns)) {
            $this->dispatch('swal:error',
                title: 'Wrong File Format',
                message: 'Missing required columns: '.implode(', ', $missingColumns).'. Expected: first_name, middle_name, last_name, identity_id, email, password, role.'
            );

            return;
        }

        $this->validRows = [];
        $this->invalidRows = [];

        foreach ($rows as $index => $row) {
            $rowIndex = $index + 2;

            if (empty($row['first_name']) || empty($row['last_name']) || empty($row['email']) || empty($row['role']) || empty($row['identity_id'])) {
                $missing = [];
                if (empty($row['first_name'])) {
                    $missing[] = 'first_name';
                }
                if (empty($row['last_name'])) {
                    $missing[] = 'last_name';
                }
                if (empty($row['identity_id'])) {
                    $missing[] = 'identity_id';
                }
                if (empty($row['email'])) {
                    $missing[] = 'email';
                }
                if (empty($row['role'])) {
                    $missing[] = 'role';
                }

                $this->invalidRows[] = [
                    'row_index' => $rowIndex,
                    'data' => $row,
                    'error' => 'Missing required fields: '.implode(', ', $missing).'. Expected columns: first_name, last_name, identity_id, email, role.',
                ];

                continue;
            }

            if (! in_array(strtolower($row['role']), ['admin', 'faculty', 'student'])) {
                $this->invalidRows[] = [
                    'row_index' => $rowIndex,
                    'data' => $row,
                    'error' => 'Invalid role. Must be admin, faculty, or student.',
                ];

                continue;
            }

            $exists = User::where('email', $row['email'])
                ->orWhere(function ($query) use ($row) {
                    if (! empty($row['identity_id'])) {
                        $query->where('identity_id', $row['identity_id']);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                })->exists();

            if ($exists) {
                $this->invalidRows[] = [
                    'row_index' => $rowIndex,
                    'data' => $row,
                    'error' => 'Duplicate email or identity_id in database.',
                ];

                continue;
            }

            $duplicateInFile = collect($this->validRows)->contains(function ($validRow) use ($row) {
                return $validRow['email'] === $row['email'] || (! empty($row['identity_id']) && $validRow['identity_id'] === $row['identity_id']);
            });

            if ($duplicateInFile) {
                $this->invalidRows[] = [
                    'row_index' => $rowIndex,
                    'data' => $row,
                    'error' => 'Duplicate email or identity_id within the file.',
                ];

                continue;
            }

            $this->validRows[] = $row;
        }

        $this->totalValidRows = count($this->validRows);
        $this->importStep = 2;
    }

    public function processImportChunk(): void
    {
        if ($this->importProgress >= $this->totalValidRows) {
            $this->finalizeImport();

            return;
        }

        $chunkSize = 50;
        $chunk = array_slice($this->validRows, $this->importProgress, $chunkSize);

        foreach ($chunk as $row) {
            // Guard: identity_id must not be null (DB NOT NULL constraint)
            if (empty($row['identity_id'])) {
                $this->dispatch('swal:error', title: 'Import Error', message: "Row skipped: identity_id is missing for user {$row['email']}.");
                $this->importProgress++;

                continue;
            }

            User::create([
                'first_name' => $row['first_name'],
                'middle_name' => $row['middle_name'] ?? null,
                'last_name' => $row['last_name'],
                'identity_id' => $row['identity_id'],
                'email' => $row['email'],
                'password' => Hash::make($row['password'] ?? 'password123'),
                'role' => strtolower($row['role']),
            ]);
            $this->importProgress++;
        }
    }

    public function finalizeImport(): void
    {
        AuditService::log(
            'imported',
            User::class,
            null,
            ['imported_count' => $this->totalValidRows, 'skipped_count' => count($this->invalidRows)],
            "Admin imported {$this->totalValidRows} users via Excel/CSV (Skipped ".count($this->invalidRows).' rows)'
        );

        $this->closeImportModal();
        $this->resetPage();

        $this->dispatch('swal:success', title: 'Import Complete', message: "Successfully imported {$this->totalValidRows} users. Skipped ".count($this->invalidRows).' invalid rows.');
    }

    public function downloadErrorReport()
    {
        return Excel::download(new UserImportErrorExport($this->invalidRows), 'import_errors_'.now()->format('Ymd_His').'.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function store(): void
    {
        $this->validate([
            'firstName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'lastName' => 'required|string|max:255',
            'identityId' => 'required|string|max:255|unique:users,identity_id',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,faculty,student',
            'status' => 'required|in:active,inactive,suspended',
            'permissions' => 'nullable|array',
        ]);

        $user = User::create([
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'last_name' => $this->lastName,
            'identity_id' => $this->identityId,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'status' => $this->status,
            'permissions' => $this->permissions,
        ]);

        AuditService::logCreated($user, [
            'first_name' => $user->first_name,
            'middle_name' => $user->middle_name,
            'last_name' => $user->last_name,
            'identity_id' => $user->identity_id,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            'permissions' => $user->permissions,
        ]);

        $this->closeAddModal();
        $this->resetPage();
        $this->dispatch('swal:success', title: 'User Created', message: "User {$user->first_name} {$user->last_name} has been created successfully.");
    }

    public function update(): void
    {
        $this->validate([
            'firstName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'lastName' => 'required|string|max:255',
            'identityId' => "required|string|max:255|unique:users,identity_id,{$this->userId}",
            'email' => "required|email|max:255|unique:users,email,{$this->userId}",
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,faculty,student',
            'status' => 'required|in:active,inactive,suspended',
            'permissions' => 'nullable|array',
        ]);

        $user = User::findOrFail($this->userId);

        // Optimistic locking check
        try {
            $user->checkLockVersion($this->lockVersion);
        } catch (StaleRecordException $e) {
            $this->lockConflict = true;
            $this->dispatch('swal:error', title: 'Edit Conflict', message: 'This record was modified by another user. Please close and reopen the edit form to get the latest data.');

            return;
        }

        $originalValues = $user->getOriginal();

        $updateData = [
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'last_name' => $this->lastName,
            'identity_id' => $this->identityId,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'permissions' => $this->permissions,
        ];

        if ($this->password) {
            $updateData['password'] = Hash::make($this->password);
        }

        $user->update($updateData);
        $user->incrementLockVersion();

        $changedFields = [];
        foreach ($updateData as $key => $value) {
            if ($key !== 'password' && ($originalValues[$key] ?? null) !== $value) {
                $changedFields[$key] = $value;
            } elseif ($key === 'password' && $this->password) {
                $changedFields[$key] = '***changed***';
            }
        }

        if (! empty($changedFields)) {
            AuditService::logUpdated($user, $originalValues, $changedFields);
        }

        $this->closeEditModal();
        $this->dispatch('swal:success', title: 'User Updated', message: "User {$user->first_name} {$user->last_name} has been updated successfully.");
    }

    public function destroy(): void
    {
        $user = User::findOrFail($this->deleteConfirmUserId);

        if ($user->id === auth()->id()) {
            $this->dispatch('swal:error', title: 'Error', message: 'Cannot delete your own account.');
            $this->closeDeleteModal();

            return;
        }

        $userName = $user->first_name.' '.$user->last_name;

        // Log with cascade info before soft deleting
        $enrollmentCount = $user->enrollments()->count();
        $warning = $enrollmentCount > 0 ? "had {$enrollmentCount} enrollment(s)" : '';
        AuditService::logDeleted($user);

        $user->delete(); // Soft delete

        $this->closeDeleteModal();
        $this->resetPage();
        $this->dispatch('swal:success', title: 'User Deleted', message: "User {$userName} has been moved to trash.");
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restoreUser($userId): void
    {
        $user = User::onlyTrashed()->findOrFail($userId);
        $user->restore();

        AuditService::logRestored($user);

        $this->dispatch('swal:success', title: 'User Restored', message: "User {$user->first_name} {$user->last_name} has been restored.");
    }

    /**
     * Permanently delete a soft-deleted user.
     */
    public function forceDeleteUser($userId): void
    {
        $user = User::onlyTrashed()->findOrFail($userId);
        $userName = $user->first_name.' '.$user->last_name;

        AuditService::logForceDeleted($user);

        $user->forceDelete();

        $this->resetPage();
        $this->dispatch('swal:success', title: 'Permanently Deleted', message: "User {$userName} has been permanently deleted.");
    }

    public function resetDevice($userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->role !== 'student') {
            $this->dispatch('swal:error', title: 'Error', message: 'Device reset is only applicable to students.');

            return;
        }

        $user->update(['device_fingerprint' => null]);

        AuditService::log(
            'device_reset',
            User::class,
            $user->id,
            null,
            'Admin reset device binding for student '.$user->first_name.' '.$user->last_name
        );

        $this->dispatch('swal:success', title: 'Device Reset', message: 'Device binding has been reset.');
    }

    public function resetForm(): void
    {
        $this->userId = null;
        $this->firstName = '';
        $this->middleName = '';
        $this->lastName = '';
        $this->identityId = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'student';
        $this->status = 'active';
        $this->permissions = [];
        $this->lockVersion = 0;
        $this->lockConflict = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.user-management', [
            'users' => $this->getUsers(),
        ]);
    }
}
