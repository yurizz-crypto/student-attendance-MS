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

        {{-- Advanced filter row --}}
        <div class="flex flex-wrap items-center gap-3">
            <select wire:model.live="filterStatus" class="px-3 py-2 rounded-lg border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
            </select>
            <div class="flex items-center gap-2">
                <label class="text-xs text-gray-500 font-medium">From</label>
                <input type="date" wire:model.live="dateFrom" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand">
                <label class="text-xs text-gray-500 font-medium">To</label>
                <input type="date" wire:model.live="dateTo" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand">
            </div>
            @if($searchTerm || $filterRole || $filterStatus || $dateFrom || $dateTo)
                <button wire:click="$set('searchTerm',''); $set('filterRole',''); $set('filterStatus',''); $set('dateFrom',''); $set('dateTo','')"
                    class="px-3 py-2 text-xs text-error border border-error/30 rounded-lg hover:bg-error/5 transition-colors font-medium">
                    Clear Filters
                </button>
            @endif
            <div class="ml-auto flex items-center gap-2">
                {{-- Column visibility --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/></svg>
                        Columns
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 top-10 z-20 bg-surface rounded-lg border border-gray-200 shadow-lg p-3 w-44 space-y-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Show Columns</p>
                        @foreach(['name' => 'Name', 'identity_id' => 'Identity ID', 'email' => 'Email', 'role' => 'Role', 'status' => 'Status', 'created' => 'Created'] as $key => $label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="visibleColumns" value="{{ $key }}" class="w-4 h-4 rounded text-brand border-gray-300 focus:ring-brand">
                                <span class="text-sm text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                {{-- Export --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Export
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 top-10 z-20 bg-surface rounded-lg border border-gray-200 shadow-lg overflow-hidden w-40">
                        <button wire:click="exportExcel" @click="open=false" class="w-full px-4 py-2.5 text-sm text-left text-gray-700 hover:bg-gray-50 flex items-center gap-2 transition-colors">
                            <svg class="w-4 h-4 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Excel (.xlsx)
                        </button>
                        <button wire:click="exportPdf" @click="open=false" class="w-full px-4 py-2.5 text-sm text-left text-gray-700 hover:bg-gray-50 flex items-center gap-2 transition-colors border-t border-gray-100">
                            <svg class="w-4 h-4 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bulk action bar --}}
        @if(count($selectedIds) > 0)
            <div class="flex flex-wrap items-center gap-3 px-4 py-3 bg-brand/5 border border-brand/20 rounded-lg">
                <span class="text-sm font-semibold text-brand">{{ count($selectedIds) }} selected</span>
                <div class="flex gap-2 ml-auto flex-wrap">
                    <button wire:click="bulkUpdateStatus('active')" class="px-3 py-1.5 text-xs font-semibold text-success border border-success/30 rounded-lg hover:bg-success/10 transition-colors">Set Active</button>
                    <button wire:click="bulkUpdateStatus('inactive')" class="px-3 py-1.5 text-xs font-semibold text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">Set Inactive</button>
                    <button wire:click="exportExcel" class="px-3 py-1.5 text-xs font-semibold text-info border border-info/30 rounded-lg hover:bg-info/10 transition-colors">Export Selected</button>
                    <button wire:click="bulkDelete" wire:confirm="Move {{ count($selectedIds) }} user(s) to trash?" class="px-3 py-1.5 text-xs font-semibold text-error border border-error/30 rounded-lg hover:bg-error/10 transition-colors">Delete Selected</button>
                    <button wire:click="clearSelection" class="px-3 py-1.5 text-xs text-gray-500 hover:text-gray-700 transition-colors">Cancel</button>
                </div>
            </div>
        @endif
    </div>

    <!-- Table -->
    <div class="bg-surface rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/50">
                        <th class="px-4 py-4 w-10">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="w-4 h-4 rounded text-brand border-gray-300 focus:ring-brand cursor-pointer"
                                title="Select all on this page">
                        </th>
                        @if(in_array('name', $visibleColumns))
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors" wire:click="sort('first_name')">
                            <div class="flex items-center gap-1.5">
                                Name
                                @if($sortField === 'first_name')
                                    <svg class="w-3.5 h-3.5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                @endif
                            </div>
                        </th>
                        @endif
                        @if(in_array('identity_id', $visibleColumns))
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors" wire:click="sort('identity_id')">
                            <div class="flex items-center gap-1.5">
                                Identity ID
                                @if($sortField === 'identity_id')
                                    <svg class="w-3.5 h-3.5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                @endif
                            </div>
                        </th>
                        @endif
                        @if(in_array('email', $visibleColumns))
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors" wire:click="sort('email')">
                            <div class="flex items-center gap-1.5">
                                Email
                                @if($sortField === 'email')
                                    <svg class="w-3.5 h-3.5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                @endif
                            </div>
                        </th>
                        @endif
                        @if(in_array('role', $visibleColumns))
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors" wire:click="sort('role')">
                            <div class="flex items-center gap-1.5">
                                Role
                                @if($sortField === 'role')
                                    <svg class="w-3.5 h-3.5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                @endif
                            </div>
                        </th>
                        @endif
                        @if(in_array('status', $visibleColumns))
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors" wire:click="sort('status')">
                            <div class="flex items-center gap-1.5">
                                Status
                                @if($sortField === 'status')
                                    <svg class="w-3.5 h-3.5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                @endif
                            </div>
                        </th>
                        @endif
                        @if(in_array('created', $visibleColumns))
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors" wire:click="sort('created_at')">
                            <div class="flex items-center gap-1.5">
                                Created
                                @if($sortField === 'created_at')
                                    <svg class="w-3.5 h-3.5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                @endif
                            </div>
                        </th>
                        @endif
                        <th class="px-6 py-4 text-center font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors {{ in_array((string)$user->id, $selectedIds) ? 'bg-brand/5' : '' }}">
                            <td class="px-4 py-4">
                                <input type="checkbox"
                                    wire:click="toggleSelect('{{ $user->id }}')"
                                    @checked(in_array((string)$user->id, $selectedIds))
                                    class="w-4 h-4 rounded text-brand border-gray-300 focus:ring-brand cursor-pointer">
                            </td>
                            @if(in_array('name', $visibleColumns))
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
                            @endif
                            @if(in_array('identity_id', $visibleColumns))
                            <td class="px-6 py-4 text-gray-700">{{ $user->identity_id }}</td>
                            @endif
                            @if(in_array('email', $visibleColumns))
                            <td class="px-6 py-4 text-gray-700">{{ $user->email }}</td>
                            @endif
                            @if(in_array('role', $visibleColumns))
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                                    @if($user->role === 'admin') bg-error/10 text-error
                                    @elseif($user->role === 'faculty') bg-info/10 text-info
                                    @else bg-brand/10 text-brand @endif">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            @endif
                            @if(in_array('status', $visibleColumns))
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                                    @if($user->status === 'active') bg-success/10 text-success
                                    @elseif($user->status === 'inactive') bg-gray-100 text-gray-600
                                    @else bg-warning/10 text-warning @endif">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            @endif
                            @if(in_array('created', $visibleColumns))
                            <td class="px-6 py-4 text-gray-700">{{ $user->created_at->format('M d, Y') }}</td>
                            @endif
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
                                        {{-- Active: activity, device reset, edit, delete --}}
                                        <button
                                            wire:click="openActivityPanel({{ $user->id }})"
                                            class="p-2 text-brand hover:bg-brand/10 rounded-lg transition-colors"
                                            title="View Activity"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                        </button>
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

    <!-- Pagination & per-page -->
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <label class="text-sm text-gray-500">Rows per page:</label>
            <select wire:model.live="perPage" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="text-sm text-gray-500">
                Showing {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
            </span>
        </div>
        @if($users->hasPages())
            <div>{{ $users->links() }}</div>
        @endif
    </div>

    <!-- Add User Modal -->
    @if($showAddModal)
        <div
            class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4"
            x-data="{
                isDirty: false,
                draftKey: 'draft_create_user',
                saveDraft() {
                    const data = {
                        firstName: $wire.firstName,
                        middleName: $wire.middleName,
                        lastName: $wire.lastName,
                        identityId: $wire.identityId,
                        email: $wire.email,
                        role: $wire.role,
                        status: $wire.status,
                    };
                    localStorage.setItem(this.draftKey, JSON.stringify(data));
                    this.isDirty = true;
                },
                loadDraft() {
                    try {
                        const saved = localStorage.getItem(this.draftKey);
                        if (!saved) return;
                        const data = JSON.parse(saved);
                        if (data.firstName) $wire.set('firstName', data.firstName);
                        if (data.middleName) $wire.set('middleName', data.middleName);
                        if (data.lastName) $wire.set('lastName', data.lastName);
                        if (data.identityId) $wire.set('identityId', data.identityId);
                        if (data.email) $wire.set('email', data.email);
                        if (data.role) $wire.set('role', data.role);
                        if (data.status) $wire.set('status', data.status);
                        this.isDirty = true;
                    } catch(e) {}
                },
                clearDraft() {
                    localStorage.removeItem(this.draftKey);
                    this.isDirty = false;
                },
                confirmClose() {
                    if (!this.isDirty) { $wire.closeAddModal(); return; }
                    Swal.fire({
                        title: 'Unsaved Changes',
                        text: 'You have unsaved changes. Leave without saving?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Leave',
                        cancelButtonText: 'Keep Editing',
                        didOpen: (popup) => {
                            Object.assign(popup.style, { fontFamily: 'Instrument Sans, sans-serif', borderRadius: '20px', border: '1px solid #FDE68A' });
                            const confirm = popup.querySelector('.swal2-confirm');
                            if (confirm) Object.assign(confirm.style, { background: '#EB5757', border: 'none', borderRadius: '10px', fontFamily: 'Instrument Sans, sans-serif', fontWeight: '600', padding: '0.55rem 1.4rem', boxShadow: 'none' });
                            const cancel = popup.querySelector('.swal2-cancel');
                            if (cancel) Object.assign(cancel.style, { background: '#f1f5f9', color: '#0F172A', border: 'none', borderRadius: '10px', fontFamily: 'Instrument Sans, sans-serif', fontWeight: '600', padding: '0.55rem 1.4rem', boxShadow: 'none' });
                        }
                    }).then(result => { if (result.isConfirmed) { this.clearDraft(); $wire.closeAddModal(); } });
                }
            }"
            x-init="loadDraft()"
            @click="confirmClose()"
        >
            <div class="bg-surface rounded-lg shadow-lg max-w-md w-full max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-surface z-10">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-bold text-navy">Add New User</h3>
                        <span x-show="isDirty" x-cloak class="inline-flex items-center gap-1 text-xs text-warning font-medium">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="10"/></svg>
                            Draft saved
                        </span>
                    </div>
                    <button @click="confirmClose()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="store" class="p-6 space-y-4" @input.debounce.800ms="saveDraft()">
                    <!-- First Name -->
                    <div>
                        <label for="create-firstName" class="block text-sm font-semibold text-navy mb-2">
                            First Name <span class="text-error ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="create-firstName"
                            type="text"
                            wire:model.blur="firstName"
                            aria-required="true"
                            aria-describedby="create-firstName-error"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('firstName'), 'border-gray-200' => !$errors->has('firstName')])
                            placeholder="John"
                            autocomplete="given-name"
                        >
                        @error('firstName')
                            <p id="create-firstName-error" class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Middle Name -->
                    <div>
                        <label for="create-middleName" class="block text-sm font-semibold text-navy mb-2">
                            Middle Name <span class="text-xs font-normal text-gray-400">(Optional)</span>
                        </label>
                        <input
                            id="create-middleName"
                            type="text"
                            wire:model="middleName"
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-brand transition-colors"
                            placeholder="Michael"
                            autocomplete="additional-name"
                        >
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="create-lastName" class="block text-sm font-semibold text-navy mb-2">
                            Last Name <span class="text-error ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="create-lastName"
                            type="text"
                            wire:model.blur="lastName"
                            aria-required="true"
                            aria-describedby="create-lastName-error"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('lastName'), 'border-gray-200' => !$errors->has('lastName')])
                            placeholder="Doe"
                            autocomplete="family-name"
                        >
                        @error('lastName')
                            <p id="create-lastName-error" class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Identity ID -->
                    <div>
                        <label for="create-identityId" class="block text-sm font-semibold text-navy mb-2">
                            Identity ID <span class="text-error ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="create-identityId"
                            type="text"
                            wire:model.blur="identityId"
                            aria-required="true"
                            aria-describedby="create-identityId-error"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('identityId'), 'border-gray-200' => !$errors->has('identityId')])
                            placeholder="2026717212"
                        >
                        @error('identityId')
                            <p id="create-identityId-error" class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="create-email" class="block text-sm font-semibold text-navy mb-2">
                            Email <span class="text-error ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="create-email"
                            type="email"
                            wire:model.blur="email"
                            aria-required="true"
                            aria-describedby="create-email-error"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('email'), 'border-gray-200' => !$errors->has('email')])
                            placeholder="john@example.com"
                            autocomplete="email"
                            inputmode="email"
                        >
                        @error('email')
                            <p id="create-email-error" class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="create-password" class="block text-sm font-semibold text-navy mb-2">
                            Password <span class="text-error ml-0.5" aria-hidden="true">*</span>
                            <span class="text-xs font-normal text-gray-400 ml-1">min. 8 characters</span>
                        </label>
                        <input
                            id="create-password"
                            type="password"
                            wire:model.blur="password"
                            aria-required="true"
                            aria-describedby="create-password-error"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('password'), 'border-gray-200' => !$errors->has('password')])
                            placeholder="••••••••"
                            autocomplete="new-password"
                        >
                        @error('password')
                            <p id="create-password-error" class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="create-role" class="block text-sm font-semibold text-navy mb-2">
                            Role <span class="text-error ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <select
                            id="create-role"
                            wire:model.live="role"
                            aria-required="true"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error' => $errors->has('role'), 'border-gray-200' => !$errors->has('role')])
                        >
                            <option value="student">Student</option>
                            <option value="faculty">Faculty</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('role')
                            <p class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="create-status" class="block text-sm font-semibold text-navy mb-2">
                            Status <span class="text-error ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <select
                            id="create-status"
                            wire:model="status"
                            aria-required="true"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error' => $errors->has('status'), 'border-gray-200' => !$errors->has('status')])
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                        @error('status')
                            <p class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Permissions (Faculty only) -->
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

                    <!-- Required fields note -->
                    <p class="text-xs text-gray-400"><span class="text-error">*</span> Required fields</p>

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-2">
                        <button
                            type="button"
                            @click="confirmClose()"
                            class="flex-1 px-4 py-2 rounded-lg border border-gray-200 text-navy font-semibold hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="store"
                            class="flex-1 px-4 py-2 rounded-lg bg-brand text-white font-semibold hover:bg-brand-hover transition-colors flex items-center justify-center gap-2 disabled:opacity-70"
                            x-on:click="clearDraft()"
                        >
                            <svg wire:loading wire:target="store" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span wire:loading.remove wire:target="store">Create User</span>
                            <span wire:loading wire:target="store">Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Edit User Modal -->
    @if($showEditModal)
        <div
            class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4"
            x-data="{ isDirty: false, confirmClose() { if (!this.isDirty) { $wire.closeEditModal(); return; } Swal.fire({ title: 'Unsaved Changes', text: 'You have unsaved changes. Leave without saving?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Leave', cancelButtonText: 'Keep Editing', didOpen: (popup) => { Object.assign(popup.style, { fontFamily: 'Instrument Sans, sans-serif', borderRadius: '20px', border: '1px solid #FDE68A' }); const c = popup.querySelector('.swal2-confirm'); if (c) Object.assign(c.style, { background: '#EB5757', border: 'none', borderRadius: '10px', fontFamily: 'Instrument Sans, sans-serif', fontWeight: '600', padding: '0.55rem 1.4rem', boxShadow: 'none' }); const x = popup.querySelector('.swal2-cancel'); if (x) Object.assign(x.style, { background: '#f1f5f9', color: '#0F172A', border: 'none', borderRadius: '10px', fontFamily: 'Instrument Sans, sans-serif', fontWeight: '600', padding: '0.55rem 1.4rem', boxShadow: 'none' }); } }).then(r => { if (r.isConfirmed) $wire.closeEditModal(); }); } }"
            @click="confirmClose()"
        >
            <div class="bg-surface rounded-lg shadow-lg max-w-md w-full max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-surface z-10">
                    <h3 class="text-lg font-bold text-navy">Edit User</h3>
                    <button @click="confirmClose()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="update" class="p-6 space-y-4" @input="isDirty = true">
                    {{-- Optimistic lock conflict banner --}}
                    @if($lockConflict)
                        <div class="flex items-start gap-3 p-4 rounded-lg bg-error/10 border border-error/30" role="alert">
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
                        <label for="edit-firstName" class="block text-sm font-semibold text-navy mb-2">
                            First Name <span class="text-error ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="edit-firstName"
                            type="text"
                            wire:model.blur="firstName"
                            aria-required="true"
                            aria-describedby="edit-firstName-error"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('firstName'), 'border-gray-200' => !$errors->has('firstName')])
                            placeholder="John"
                            autocomplete="given-name"
                        >
                        @error('firstName')
                            <p id="edit-firstName-error" class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Middle Name -->
                    <div>
                        <label for="edit-middleName" class="block text-sm font-semibold text-navy mb-2">
                            Middle Name <span class="text-xs font-normal text-gray-400">(Optional)</span>
                        </label>
                        <input
                            id="edit-middleName"
                            type="text"
                            wire:model="middleName"
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-brand transition-colors"
                            placeholder="Michael"
                            autocomplete="additional-name"
                        >
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="edit-lastName" class="block text-sm font-semibold text-navy mb-2">
                            Last Name <span class="text-error ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="edit-lastName"
                            type="text"
                            wire:model.blur="lastName"
                            aria-required="true"
                            aria-describedby="edit-lastName-error"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('lastName'), 'border-gray-200' => !$errors->has('lastName')])
                            placeholder="Doe"
                            autocomplete="family-name"
                        >
                        @error('lastName')
                            <p id="edit-lastName-error" class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Identity ID -->
                    <div>
                        <label for="edit-identityId" class="block text-sm font-semibold text-navy mb-2">
                            Identity ID <span class="text-error ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="edit-identityId"
                            type="text"
                            wire:model.blur="identityId"
                            aria-required="true"
                            aria-describedby="edit-identityId-error"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('identityId'), 'border-gray-200' => !$errors->has('identityId')])
                            placeholder="ID123456"
                        >
                        @error('identityId')
                            <p id="edit-identityId-error" class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="edit-email" class="block text-sm font-semibold text-navy mb-2">
                            Email <span class="text-error ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="edit-email"
                            type="email"
                            wire:model.blur="email"
                            aria-required="true"
                            aria-describedby="edit-email-error"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('email'), 'border-gray-200' => !$errors->has('email')])
                            placeholder="john@example.com"
                            autocomplete="email"
                            inputmode="email"
                        >
                        @error('email')
                            <p id="edit-email-error" class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="edit-password" class="block text-sm font-semibold text-navy mb-2">
                            Password
                            <span class="text-xs font-normal text-gray-400 ml-1">Leave blank to keep current</span>
                        </label>
                        <input
                            id="edit-password"
                            type="password"
                            wire:model.blur="password"
                            aria-describedby="edit-password-error"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('password'), 'border-gray-200' => !$errors->has('password')])
                            placeholder="••••••••"
                            autocomplete="new-password"
                        >
                        @error('password')
                            <p id="edit-password-error" class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="edit-role" class="block text-sm font-semibold text-navy mb-2">
                            Role <span class="text-error ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <select
                            id="edit-role"
                            wire:model.live="role"
                            aria-required="true"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error' => $errors->has('role'), 'border-gray-200' => !$errors->has('role')])
                        >
                            <option value="student">Student</option>
                            <option value="faculty">Faculty</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('role')
                            <p class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="edit-status" class="block text-sm font-semibold text-navy mb-2">
                            Status <span class="text-error ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <select
                            id="edit-status"
                            wire:model="status"
                            aria-required="true"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error' => $errors->has('status'), 'border-gray-200' => !$errors->has('status')])
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                        @error('status')
                            <p class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Permissions (Faculty only) -->
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

                    <p class="text-xs text-gray-400"><span class="text-error">*</span> Required fields</p>

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-2">
                        <button
                            type="button"
                            @click="confirmClose()"
                            class="flex-1 px-4 py-2 rounded-lg border border-gray-200 text-navy font-semibold hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="update"
                            class="flex-1 px-4 py-2 rounded-lg bg-brand text-white font-semibold hover:bg-brand-hover transition-colors flex items-center justify-center gap-2 disabled:opacity-70"
                            x-on:click="isDirty = false"
                        >
                            <svg wire:loading wire:target="update" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span wire:loading.remove wire:target="update">Update User</span>
                            <span wire:loading wire:target="update">Saving...</span>
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

    @include('livewire.admin.partials.user-activity-panel')

</div>
