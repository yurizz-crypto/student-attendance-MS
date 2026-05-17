<div>
    <!-- Toolbar -->
    <div class="mb-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex-1 flex gap-3">
                <div class="relative flex-1">
                    <input
                        type="text"
                        wire:model.live="searchTerm"
                        placeholder="Search by name, ID, or email..."
                        class="w-full px-4 py-2 pl-10 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent"
                    >
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <select
                    wire:model.live="filterRole"
                    class="px-4 py-2 rounded-lg border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent"
                >
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="faculty">Faculty</option>
                    <option value="student">Student</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button
                    wire:click="toggleTrashed"
                    class="px-4 py-2 border rounded-lg font-semibold text-sm transition-colors flex items-center gap-2
                        {{ $showTrashed ? 'bg-error/10 border-error text-error' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' }}"
                    title="{{ $showTrashed ? 'Show active users' : 'Show deleted users' }}"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ $showTrashed ? 'View Active' : 'View Trash' }}
                </button>
                <button
                    wire:click="openImportModal"
                    class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-50 transition-colors flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import Users
                </button>
                @if(!$showTrashed)
                <button
                    wire:click="openAddModal"
                    class="px-4 py-2 bg-brand text-white rounded-lg font-semibold text-sm hover:bg-brand-hover transition-colors flex items-center gap-2 justify-center sm:justify-start"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add User
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-surface rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/50">
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors" wire:click="sort('first_name')">
                            <div class="flex items-center gap-2">
                                Name
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.5a1 1 0 10-2 0v5.5z" />
                                </svg>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors" wire:click="sort('identity_id')">
                            Identity ID
                        </th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors" wire:click="sort('email')">
                            Email
                        </th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors" wire:click="sort('role')">
                            Role
                        </th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors" wire:click="sort('status')">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors" wire:click="sort('created_at')">
                            Created
                        </th>
                        <th class="px-6 py-4 text-center font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-brand/10 text-brand flex items-center justify-center font-bold text-sm">
                                        {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        @if($user->middle_name)
                                            <div class="text-xs text-gray-500">{{ $user->middle_name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $user->identity_id }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                                    @if($user->role === 'admin')
                                        bg-error/10 text-error
                                    @elseif($user->role === 'faculty')
                                        bg-info/10 text-info
                                    @else
                                        bg-brand/10 text-brand
                                    @endif
                                ">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                                    @if($user->status === 'active')
                                        bg-success/10 text-success
                                    @elseif($user->status === 'inactive')
                                        bg-gray-100 text-gray-600
                                    @else
                                        bg-warning/10 text-warning
                                    @endif
                                ">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    @if($showTrashed)
                                        {{-- Trashed: restore or force-delete --}}
                                        <button
                                            wire:click="restoreUser({{ $user->id }})"
                                            wire:confirm="Restore this user?"
                                            class="p-2 text-success hover:bg-success/10 rounded-lg transition-colors"
                                            title="Restore user"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </button>
                                        <button
                                            wire:click="forceDeleteUser({{ $user->id }})"
                                            wire:confirm="Permanently delete this user? This CANNOT be undone."
                                            class="p-2 text-error hover:bg-error/10 rounded-lg transition-colors"
                                            title="Permanently delete"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @else
                                        {{-- Active: device reset, edit, delete --}}
                                        @if($user->role === 'student' && !empty($user->device_fingerprint))
                                            <button
                                                wire:click="resetDevice({{ $user->id }})"
                                                wire:confirm="Are you sure you want to reset the device binding for this student?"
                                                class="p-2 text-warning hover:bg-warning/10 rounded-lg transition-colors"
                                                title="Reset Device Binding"
                                            >
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        @endif
                                        <button
                                            wire:click="openEditModal({{ $user->id }})"
                                            class="p-2 text-info hover:bg-info/10 rounded-lg transition-colors"
                                            title="Edit user"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            wire:click="openDeleteModal({{ $user->id }})"
                                            class="p-2 text-error hover:bg-error/10 rounded-lg transition-colors"
                                            title="Delete user"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="font-medium">No users found</p>
                                <p class="text-sm mt-1">Try adjusting your search or filter</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @endif

    <!-- Add User Modal -->
    @if($showAddModal)
        <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4" wire:click="closeAddModal">
            <div class="bg-surface rounded-lg shadow-lg max-w-md w-full" @click.stop>
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-navy">Add New User</h3>
                    <button wire:click="closeAddModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="store" class="p-6 space-y-4">
                    <!-- First Name -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">First Name</label>
                        <input
                            type="text"
                            wire:model="firstName"
                            class="w-full px-4 py-2 rounded-lg border @error('firstName') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="John"
                        >
                        @error('firstName')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Middle Name -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Middle Name (Optional)</label>
                        <input
                            type="text"
                            wire:model="middleName"
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="Michael"
                        >
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Last Name</label>
                        <input
                            type="text"
                            wire:model="lastName"
                            class="w-full px-4 py-2 rounded-lg border @error('lastName') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="Doe"
                        >
                        @error('lastName')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Identity ID -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Identity ID</label>
                        <input
                            type="text"
                            wire:model="identityId"
                            class="w-full px-4 py-2 rounded-lg border @error('identityId') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="2026717212"
                        >
                        @error('identityId')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Email</label>
                        <input
                            type="email"
                            wire:model="email"
                            class="w-full px-4 py-2 rounded-lg border @error('email') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="john@example.com"
                        >
                        @error('email')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Password</label>
                        <input
                            type="password"
                            wire:model="password"
                            class="w-full px-4 py-2 rounded-lg border @error('password') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="••••••••"
                        >
                        @error('password')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Role</label>
                        <select
                            wire:model.live="role"
                            class="w-full px-4 py-2 rounded-lg border @error('role') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                        >
                            <option value="student">Student</option>
                            <option value="faculty">Faculty</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('role')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Status</label>
                        <select
                            wire:model="status"
                            class="w-full px-4 py-2 rounded-lg border @error('status') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                        @error('status')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Permissions (Only for Faculty) -->
                    @if($role === 'faculty')
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-semibold text-navy mb-3">Additional Permissions</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3">
                                    <input type="checkbox" wire:model="permissions" value="manage_users" class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand">
                                    <span class="text-sm text-gray-700">Manage Users (Dean/President level access)</span>
                                </label>
                            </div>
                        </div>
                    @endif

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-4">
                        <button
                            type="button"
                            wire:click="closeAddModal"
                            class="flex-1 px-4 py-2 rounded-lg border border-gray-200 text-navy font-semibold hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="flex-1 px-4 py-2 rounded-lg bg-brand text-white font-semibold hover:bg-brand-hover transition-colors"
                        >
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Edit User Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4" wire:click="closeEditModal">
            <div class="bg-surface rounded-lg shadow-lg max-w-md w-full" @click.stop>
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-navy">Edit User</h3>
                    <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="update" class="p-6 space-y-4">
                    {{-- Optimistic lock conflict banner --}}
                    @if($lockConflict)
                        <div class="flex items-start gap-3 p-4 rounded-lg bg-error/10 border border-error/30">
                            <svg class="w-5 h-5 text-error flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="font-semibold text-error text-sm">Edit Conflict Detected</p>
                                <p class="text-xs text-gray-600 mt-1">This record was modified by another user. Please close and reopen the form to load the latest data.</p>
                            </div>
                        </div>
                    @endif
                    <!-- First Name -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">First Name</label>
                        <input
                            type="text"
                            wire:model="firstName"
                            class="w-full px-4 py-2 rounded-lg border @error('firstName') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="John"
                        >
                        @error('firstName')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Middle Name -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Middle Name (Optional)</label>
                        <input
                            type="text"
                            wire:model="middleName"
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="Michael"
                        >
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Last Name</label>
                        <input
                            type="text"
                            wire:model="lastName"
                            class="w-full px-4 py-2 rounded-lg border @error('lastName') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="Doe"
                        >
                        @error('lastName')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Identity ID -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Identity ID</label>
                        <input
                            type="text"
                            wire:model="identityId"
                            class="w-full px-4 py-2 rounded-lg border @error('identityId') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="ID123456"
                        >
                        @error('identityId')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Email</label>
                        <input
                            type="email"
                            wire:model="email"
                            class="w-full px-4 py-2 rounded-lg border @error('email') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="john@example.com"
                        >
                        @error('email')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Password (Leave blank to keep current)</label>
                        <input
                            type="password"
                            wire:model="password"
                            class="w-full px-4 py-2 rounded-lg border @error('password') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="••••••••"
                        >
                        @error('password')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Role</label>
                        <select
                            wire:model.live="role"
                            class="w-full px-4 py-2 rounded-lg border @error('role') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                        >
                            <option value="student">Student</option>
                            <option value="faculty">Faculty</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('role')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Status</label>
                        <select
                            wire:model="status"
                            class="w-full px-4 py-2 rounded-lg border @error('status') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                        @error('status')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Permissions (Only for Faculty) -->
                    @if($role === 'faculty')
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-semibold text-navy mb-3">Additional Permissions</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3">
                                    <input type="checkbox" wire:model="permissions" value="manage_users" class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand">
                                    <span class="text-sm text-gray-700">Manage Users (Dean/President level access)</span>
                                </label>
                            </div>
                        </div>
                    @endif

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-4">
                        <button
                            type="button"
                            wire:click="closeEditModal"
                            class="flex-1 px-4 py-2 rounded-lg border border-gray-200 text-navy font-semibold hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="flex-1 px-4 py-2 rounded-lg bg-brand text-white font-semibold hover:bg-brand-hover transition-colors"
                        >
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete User Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4" wire:click="closeDeleteModal">
            <div class="bg-surface rounded-lg shadow-lg max-w-md w-full" @click.stop>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-navy">Delete User</h3>
                </div>

                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto rounded-full bg-error/10">
                        <svg class="w-6 h-6 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <div class="text-center">
                        <p class="font-semibold text-gray-800 mb-1">Move {{ $cascadeInfo['name'] ?? 'this user' }} to Trash?</p>
                        <p class="text-sm text-gray-500">This user will be soft-deleted and can be restored later from the Trash view.</p>
                    </div>

                    {{-- Cascade warning --}}
                    @if(!empty($cascadeInfo) && ($cascadeInfo['enrollments'] > 0 || $cascadeInfo['attendance_records'] > 0))
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-warning/10 border border-warning/30">
                            <svg class="w-5 h-5 text-warning flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="text-sm">
                                <p class="font-semibold text-warning">Cascade Warning</p>
                                <ul class="mt-1 text-gray-600 space-y-0.5">
                                    @if($cascadeInfo['enrollments'] > 0)
                                        <li>• {{ $cascadeInfo['enrollments'] }} enrollment(s) will be hidden</li>
                                    @endif
                                    @if($cascadeInfo['attendance_records'] > 0)
                                        <li>• {{ $cascadeInfo['attendance_records'] }} attendance record(s) will be hidden</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-3 pt-2">
                        <button
                            type="button"
                            wire:click="closeDeleteModal"
                            class="flex-1 px-4 py-2 rounded-lg border border-gray-200 text-navy font-semibold hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            wire:click="destroy"
                            class="flex-1 px-4 py-2 rounded-lg bg-error text-white font-semibold hover:bg-red-700 transition-colors"
                        >
                            Move to Trash
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Import Users Modal -->
    @if($showImportModal)
        <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4" wire:click="closeImportModal">
            <div class="bg-surface rounded-lg shadow-lg max-w-md w-full" @click.stop>
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-navy">Import Users (CSV)</h3>
                    <button wire:click="closeImportModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    @if($importStep === 1)
                        <form wire:submit="previewImport" class="space-y-4">
                            <div class="bg-blue-50 text-blue-800 p-4 rounded-lg text-sm mb-4 border border-blue-100">
                                <p class="font-bold mb-1">Excel/CSV Format Required:</p>
                                <p class="font-mono text-xs mb-2 break-all">first_name,middle_name,last_name,identity_id,email,role,password</p>
                                <ul class="list-disc pl-4 space-y-1 text-xs">
                                    <li>Includes header row</li>
                                    <li>Role must be: admin, faculty, or student</li>
                                    <li>Existing emails/identity IDs will be skipped</li>
                                </ul>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-navy mb-2">Upload Excel or CSV File</label>
                                <input
                                    type="file"
                                    wire:model="importFile"
                                    accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                    class="w-full px-4 py-2 rounded-lg border @error('importFile') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                                >
                                <div wire:loading wire:target="importFile" class="text-sm text-gray-500 mt-2">Uploading...</div>
                                @error('importFile')
                                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex gap-3 pt-4">
                                <button
                                    type="button"
                                    wire:click="closeImportModal"
                                    class="flex-1 px-4 py-2 rounded-lg border border-gray-200 text-navy font-semibold hover:bg-gray-50 transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="flex-1 px-4 py-2 rounded-lg bg-brand text-white font-semibold hover:bg-brand-hover transition-colors flex items-center justify-center gap-2"
                                    wire:loading.attr="disabled"
                                    wire:target="previewImport"
                                >
                                    <span wire:loading.remove wire:target="previewImport">Preview</span>
                                    <span wire:loading wire:target="previewImport">Processing...</span>
                                </button>
                            </div>
                        </form>
                    @elseif($importStep === 2)
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 bg-gray-50">
                                <div>
                                    <h4 class="font-bold text-gray-900">Ready to Import</h4>
                                    <p class="text-sm text-gray-600">{{ $totalValidRows }} rows are valid and will be imported.</p>
                                </div>
                                <div class="w-12 h-12 rounded-full bg-success/10 flex items-center justify-center">
                                    <span class="text-success font-bold text-lg">{{ $totalValidRows }}</span>
                                </div>
                            </div>

                            @if(count($invalidRows) > 0)
                                <div class="flex items-center justify-between p-4 rounded-lg border border-error bg-error/5">
                                    <div>
                                        <h4 class="font-bold text-error">Errors Found</h4>
                                        <p class="text-sm text-gray-600">{{ count($invalidRows) }} rows have errors or are duplicates.</p>
                                    </div>
                                    <button 
                                        wire:click="downloadErrorReport"
                                        class="px-3 py-1.5 bg-white border border-error text-error rounded font-semibold text-xs hover:bg-error hover:text-white transition-colors"
                                    >
                                        Download Report
                                    </button>
                                </div>
                            @endif

                            <div class="flex gap-3 pt-4">
                                <button
                                    type="button"
                                    wire:click="closeImportModal"
                                    class="flex-1 px-4 py-2 rounded-lg border border-gray-200 text-navy font-semibold hover:bg-gray-50 transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    wire:click="$set('importStep', 3)"
                                    class="flex-1 px-4 py-2 rounded-lg bg-brand text-white font-semibold hover:bg-brand-hover transition-colors"
                                    @if($totalValidRows === 0) disabled @endif
                                >
                                    Start Import
                                </button>
                            </div>
                        </div>
                    @elseif($importStep === 3)
                        <div class="space-y-6 py-6" wire:poll.500ms="processImportChunk">
                            <div class="text-center">
                                <h4 class="text-lg font-bold text-navy mb-2">Importing Users...</h4>
                                <p class="text-sm text-gray-600 mb-6">Please do not close this window.</p>
                                
                                <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                                    <div class="bg-brand h-4 rounded-full transition-all duration-300" style="width: {{ $totalValidRows > 0 ? ($importProgress / $totalValidRows) * 100 : 0 }}%"></div>
                                </div>
                                <p class="text-sm font-semibold text-gray-700">{{ $importProgress }} / {{ $totalValidRows }} rows processed</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>

