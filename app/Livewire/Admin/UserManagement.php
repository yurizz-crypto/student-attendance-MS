<?php

namespace App\Livewire\Admin;

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

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedFilterRole()
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

    public function getUsers()
    {
        return User::when($this->searchTerm, function ($query) {
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
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->resetForm();
    }

    public function openEditModal($userId)
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

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function openDeleteModal($userId)
    {
        $this->deleteConfirmUserId = $userId;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteConfirmUserId = null;
    }

    public function openImportModal()
    {
        $this->importFile = null;
        $this->importStep = 1;
        $this->validRows = [];
        $this->invalidRows = [];
        $this->importProgress = 0;
        $this->totalValidRows = 0;
        $this->showImportModal = true;
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importStep = 1;
        $this->validRows = [];
        $this->invalidRows = [];
    }

    public function previewImport()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120', // Max 5MB
        ]);

        $rows = Excel::toArray(new UsersImport, $this->importFile)[0];

        $this->validRows = [];
        $this->invalidRows = [];

        foreach ($rows as $index => $row) {
            $rowIndex = $index + 2; // +1 for 0-index, +1 for header

            // Check if required fields exist
            if (empty($row['first_name']) || empty($row['last_name']) || empty($row['email']) || empty($row['role'])) {
                $this->invalidRows[] = [
                    'row_index' => $rowIndex,
                    'data' => $row,
                    'error' => 'Missing required fields (first_name, last_name, email, role).',
                ];

                continue;
            }

            // Check role validity
            if (! in_array(strtolower($row['role']), ['admin', 'faculty', 'student'])) {
                $this->invalidRows[] = [
                    'row_index' => $rowIndex,
                    'data' => $row,
                    'error' => 'Invalid role. Must be admin, faculty, or student.',
                ];

                continue;
            }

            // Check duplicates in DB
            $exists = User::where('email', $row['email'])
                ->orWhere(function ($query) use ($row) {
                    if (! empty($row['identity_id'])) {
                        $query->where('identity_id', $row['identity_id']);
                    } else {
                        // This condition will always be false, preventing empty string matches
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

            // Check duplicates within the file itself
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
        $this->importStep = 2; // Move to preview step
    }

    public function processImportChunk()
    {
        if ($this->importProgress >= $this->totalValidRows) {
            $this->finalizeImport();

            return;
        }

        $chunkSize = 50;
        $chunk = array_slice($this->validRows, $this->importProgress, $chunkSize);

        foreach ($chunk as $row) {
            User::create([
                'first_name' => $row['first_name'],
                'middle_name' => $row['middle_name'] ?? null,
                'last_name' => $row['last_name'],
                'identity_id' => $row['identity_id'] ?? null,
                'email' => $row['email'],
                'password' => Hash::make($row['password'] ?? 'password123'),
                'role' => strtolower($row['role']),
            ]);
            $this->importProgress++;
        }
    }

    public function finalizeImport()
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

    public function store()
    {
        $validated = $this->validate([
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

        // Log the creation
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

        $this->dispatch('user-created', message: 'User created successfully');
        $this->closeAddModal();
        $this->resetPage();
    }

    public function update()
    {
        $validated = $this->validate([
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

        // Store original values for audit log
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

        // Log the update
        $changedFields = [];
        foreach ($updateData as $key => $value) {
            if ($key !== 'password' && $originalValues[$key] !== $value) {
                $changedFields[$key] = $value;
            } elseif ($key === 'password' && $this->password) {
                $changedFields[$key] = '***changed***';
            }
        }
        if (! empty($changedFields)) {
            AuditService::logUpdated($user, $originalValues, $changedFields);
        }

        $this->dispatch('user-updated', message: 'User updated successfully');
        $this->closeEditModal();
    }

    public function destroy()
    {
        $user = User::findOrFail($this->deleteConfirmUserId);

        // Prevent deleting the currently authenticated user
        if ($user->id === auth()->id()) {
            $this->dispatch('error', message: 'Cannot delete your own account');
            $this->closeDeleteModal();

            return;
        }

        // Log the deletion before deleting
        AuditService::logDeleted($user);

        $user->delete();

        $this->dispatch('user-deleted', message: 'User deleted successfully');
        $this->closeDeleteModal();
        $this->resetPage();
    }

    public function resetDevice($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->role !== 'student') {
            $this->dispatch('error', message: 'Device reset is only applicable to students.');

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

        $this->dispatch('user-updated', message: 'Device binding has been reset.');
    }

    public function resetForm()
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
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.user-management', [
            'users' => $this->getUsers(),
        ]);
    }
}
