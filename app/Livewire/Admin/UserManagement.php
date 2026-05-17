<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

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
        $this->showImportModal = true;
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->importFile = null;
    }

    public function importUsers()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt|max:5120', // Max 5MB
        ]);

        $filePath = $this->importFile->getRealPath();
        $file = fopen($filePath, 'r');
        $headers = fgetcsv($file);

        $importedCount = 0;
        $skippedCount = 0;

        while (($row = fgetcsv($file)) !== false) {
            // Check if row matches header count
            if (count($row) !== count($headers)) {
                $skippedCount++;

                continue;
            }

            $data = array_combine($headers, $row);

            // Basic validation for required fields
            if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['role'])) {
                $skippedCount++;

                continue;
            }

            // Check if email or identity_id already exists
            $exists = User::where('email', $data['email'])
                ->orWhere('identity_id', $data['identity_id'] ?? '')
                ->exists();

            if ($exists) {
                $skippedCount++;

                continue;
            }

            User::create([
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'],
                'identity_id' => $data['identity_id'] ?? null,
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? 'password123'),
                'role' => in_array($data['role'], ['admin', 'faculty', 'student']) ? $data['role'] : 'student',
            ]);

            $importedCount++;
        }

        fclose($file);

        AuditService::log(
            'imported',
            User::class,
            null,
            ['imported_count' => $importedCount, 'skipped_count' => $skippedCount],
            "Admin imported {$importedCount} users via CSV (Skipped {$skippedCount} rows)"
        );

        $this->closeImportModal();
        $this->resetPage();

        $this->dispatch('swal:success', title: 'Import Complete', message: "Successfully imported {$importedCount} users. Skipped {$skippedCount} invalid or duplicate rows.");
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
