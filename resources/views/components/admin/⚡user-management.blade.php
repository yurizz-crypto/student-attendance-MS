<?php
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

new class extends Component {
    use WithPagination;

    // Search and Filters
    public $search = '';
    public $roleFilter = '';

    // Form Data
    public $userId;
    public $first_name = '';
    public $middle_name = '';
    public $last_name = '';
    public $identity_id = '';
    public $email = '';
    public $role = 'student';
    public $password = '';

    // Modal State
    public $isModalOpen = false;
    public $isEditMode = false;
    public $isDeleteModalOpen = false;
    public $userToDelete = null;

    // Reset pagination when searching or filtering
    public function updatingSearch() { $this->resetPage(); }
    public function updatingRoleFilter() { $this->resetPage(); }

    public function openAddModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $user = User::findOrFail($id);
        
        $this->userId = $user->id;
        $this->first_name = $user->first_name;
        $this->middle_name = $user->middle_name;
        $this->last_name = $user->last_name;
        $this->identity_id = $user->identity_id;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = ''; // Leave blank so we only update if typed
        
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['userId', 'first_name', 'middle_name', 'last_name', 'identity_id', 'email', 'role', 'password']);
        $this->resetValidation();
    }

    public function saveUser()
    {
        // 1. Dynamic Validation Rules
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'role' => 'required|in:student,faculty,admin',
            // Identity ID and Email must be unique, UNLESS we are editing the same user
            'identity_id' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($this->userId)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->userId)],
        ];

        // Password is required for new users, but optional when editing
        if (!$this->isEditMode) {
            $rules['password'] = 'required|min:8';
        } else {
            $rules['password'] = 'nullable|min:8';
        }

        $validated = $this->validate($rules);

        // 2. Hash Password if provided
        if (!empty($this->password)) {
            $validated['password'] = Hash::make($this->password);
        } else {
            unset($validated['password']); // Don't overwrite existing password with empty string
        }

        // 3. Save to Database
        User::updateOrCreate(
            ['id' => $this->userId],
            $validated
        );

        $this->closeModal();
        session()->flash('status', $this->isEditMode ? 'User successfully updated.' : 'New user successfully created.');
    }

    public function confirmDelete($id)
    {
        $this->userToDelete = $id;
        $this->isDeleteModalOpen = true;
    }

    public function deleteUser()
    {
        // Prevent admins from deleting themselves
        if ($this->userToDelete === auth()->id()) {
            session()->flash('error', 'You cannot delete your own admin account.');
            $this->isDeleteModalOpen = false;
            return;
        }

        User::findOrFail($this->userToDelete)->delete();
        $this->isDeleteModalOpen = false;
        $this->userToDelete = null;
        
        session()->flash('status', 'User successfully deleted.');
    }

    public function with(): array
    {
        $query = User::query();

        // Apply Search Filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
                  ->orWhere('identity_id', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        // Apply Role Filter
        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        // Get global counts (ignoring search filters so the top cards always show total system data)
        return [
            'users' => $query->latest()->paginate(10),
            'stats' => [
                'total' => User::count(),
                'students' => User::where('role', 'student')->count(),
                'faculty' => User::where('role', 'faculty')->count(),
                'admins' => User::where('role', 'admin')->count(),
            ]
        ];
    }
}; ?>

<div class="space-y-6 animate-fade-in-up">
    
    {{-- Success/Error Messages --}}
    @if (session()->has('status'))
        <div class="p-4 bg-success/10 border border-success/20 text-success rounded-xl font-bold">
            {{ session('status') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 bg-error/10 border border-error/20 text-error rounded-xl font-bold">
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-brand/10 text-brand flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Users</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['total'] }}</p>
            </div>
        </div>

        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-info/10 text-info flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Students</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['students'] }}</p>
            </div>
        </div>

        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-warning/10 text-warning flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Faculty</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['faculty'] }}</p>
            </div>
        </div>

        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-error/10 text-error flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Admins</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['admins'] }}</p>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="bg-surface p-4 sm:p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col lg:flex-row gap-4 justify-between items-center">
        <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" class="block w-full pl-10 pr-3 py-2 border-gray-300 rounded-xl focus:ring-brand focus:border-brand sm:text-sm bg-gray-50 font-medium" placeholder="Search by name, ID, or email...">
            </div>
            
            <select wire:model.live="roleFilter" class="block w-full sm:w-48 py-2 px-3 border-gray-300 rounded-xl focus:ring-brand focus:border-brand sm:text-sm bg-gray-50 font-medium text-gray-600">
                <option value="">All Roles</option>
                <option value="student">Students</option>
                <option value="faculty">Faculty</option>
                <option value="admin">Administrators</option>
            </select>
        </div>

        <button wire:click="openAddModal" class="w-full sm:w-auto bg-brand text-white px-6 py-2.5 rounded-xl font-bold shadow-sm hover:bg-brand-hover transition-colors flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add New User
        </button>
    </div>

    {{-- Users Table --}}
    <div class="bg-surface rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Name / ID</th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Contact</th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                        <th scope="col" class="py-4 pl-3 pr-6 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-brand/10 text-brand flex items-center justify-center font-bold text-sm uppercase">
                                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-navy group-hover:text-brand transition-colors">
                                            {{ $user->full_name }}
                                        </div>
                                        <div class="text-xs font-semibold text-gray-400">
                                            ID: {{ $user->identity_id }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4">
                                <div class="text-sm font-medium text-gray-600">{{ $user->email }}</div>
                                <div class="text-xs font-semibold text-gray-400">Joined: {{ $user->created_at->format('M Y') }}</div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4">
                                @if($user->role === 'student')
                                    <span class="inline-flex items-center rounded-lg bg-info/10 px-2.5 py-1 text-xs font-bold text-info ring-1 ring-inset ring-info/20">Student</span>
                                @elseif($user->role === 'faculty')
                                    <span class="inline-flex items-center rounded-lg bg-warning/10 px-2.5 py-1 text-xs font-bold text-warning ring-1 ring-inset ring-warning/20">Faculty</span>
                                @else
                                    <span class="inline-flex items-center rounded-lg bg-error/10 px-2.5 py-1 text-xs font-bold text-error ring-1 ring-inset ring-error/20">Admin</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <button wire:click="openEditModal({{ $user->id }})" class="p-2 text-gray-400 hover:text-brand hover:bg-brand/10 rounded-lg transition-colors" title="Edit User">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $user->id }})" class="p-2 text-gray-400 hover:text-error hover:bg-error/10 rounded-lg transition-colors" title="Delete User">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                                <p class="text-sm font-medium text-gray-500">No users found matching your criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Add / Edit User Modal --}}
    <div x-data="{ show: @entangle('isModalOpen') }" x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-navy/20 backdrop-blur-sm p-4 overflow-y-auto" style="display: none;">
        <div x-show="show" x-transition.opacity.duration.200ms @click.away="$wire.closeModal()" class="bg-surface rounded-3xl w-full max-w-2xl shadow-xl border border-gray-100 overflow-hidden my-8">
            <form wire:submit="saveUser">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-navy">{{ $isEditMode ? 'Edit User Profile' : 'Register New User' }}</h3>
                    <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-navy transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="first_name" :value="__('First Name')" />
                            <x-text-input wire:model="first_name" id="first_name" class="block mt-1 w-full bg-gray-50" type="text" required />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="middle_name" :value="__('Middle Name')" />
                            <x-text-input wire:model="middle_name" id="middle_name" class="block mt-1 w-full bg-gray-50" type="text" placeholder="Optional" />
                            <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="last_name" :value="__('Last Name')" />
                            <x-text-input wire:model="last_name" id="last_name" class="block mt-1 w-full bg-gray-50" type="text" required />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="identity_id" :value="__('ID Number')" />
                            <x-text-input wire:model="identity_id" id="identity_id" class="block mt-1 w-full bg-gray-50" type="text" required />
                            <x-input-error :messages="$errors->get('identity_id')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="role" :value="__('System Role')" />
                            <select wire:model="role" id="role" class="block mt-1 w-full border-gray-300 rounded-xl focus:ring-brand focus:border-brand sm:text-sm bg-gray-50 py-2.5 font-medium text-navy">
                                <option value="student">Student</option>
                                <option value="faculty">Faculty</option>
                                <option value="admin">Administrator</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input wire:model="email" id="email" class="block mt-1 w-full bg-gray-50" type="email" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="password" value="{{ $isEditMode ? __('New Password (leave blank to keep current)') : __('Password') }}" />
                            <x-text-input wire:model="password" id="password" class="block mt-1 w-full bg-gray-50" type="password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="px-8 py-5 border-t border-gray-100 bg-gray-50/50 flex justify-end gap-3">
                    <button type="button" wire:click="closeModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-navy hover:bg-gray-200 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="bg-brand text-white px-6 py-2.5 rounded-xl font-bold shadow-sm hover:bg-brand-hover transition-colors">
                        {{ $isEditMode ? 'Save Changes' : 'Create User' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-data="{ show: @entangle('isDeleteModalOpen') }" x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-navy/20 backdrop-blur-sm p-4" style="display: none;">
        <div x-show="show" x-transition.opacity.duration.200ms @click.away="$wire.set('isDeleteModalOpen', false)" class="bg-surface rounded-3xl w-full max-w-md shadow-xl border border-error/20 overflow-hidden relative">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-error"></div>
            
            <div class="p-8 text-center sm:text-left flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <div class="w-12 h-12 rounded-full bg-error/10 text-error flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-navy">Delete User Account</h3>
                    <p class="mt-2 text-sm text-gray-500 font-medium leading-relaxed">Are you absolutely sure you want to delete this user? All of their data, enrollments, and attendance records will be permanently removed. This action cannot be undone.</p>
                </div>
            </div>
            
            <div class="px-8 py-5 border-t border-gray-100 bg-gray-50/50 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button type="button" wire:click="$set('isDeleteModalOpen', false)" class="w-full sm:w-auto px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-navy hover:bg-gray-200 rounded-xl transition-colors">
                    Cancel
                </button>
                <button type="button" wire:click="deleteUser" class="w-full sm:w-auto bg-error text-white px-6 py-2.5 rounded-xl font-bold shadow-sm hover:bg-error/90 transition-colors">
                    Permanently Delete
                </button>
            </div>
        </div>
    </div>
</div>