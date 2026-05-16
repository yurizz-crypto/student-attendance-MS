<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserManagement extends Component
{
    use WithPagination;

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

    // Modal states
    public $showAddModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
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
        ]);

        User::create([
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'last_name' => $this->lastName,
            'identity_id' => $this->identityId,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
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
        ]);

        $user = User::findOrFail($this->userId);

        $updateData = [
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'last_name' => $this->lastName,
            'identity_id' => $this->identityId,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if ($this->password) {
            $updateData['password'] = Hash::make($this->password);
        }

        $user->update($updateData);

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

        $user->delete();

        $this->dispatch('user-deleted', message: 'User deleted successfully');
        $this->closeDeleteModal();
        $this->resetPage();
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
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.user-management', [
            'users' => $this->getUsers(),
        ]);
    }
}
