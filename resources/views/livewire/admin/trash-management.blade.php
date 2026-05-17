<div>
    <!-- Tab Navigation -->
    <div class="mb-6 flex gap-1 border-b border-gray-200">
        <button
            wire:click="$set('activeTab', 'users')"
            class="px-5 py-3 text-sm font-semibold border-b-2 transition-colors
                {{ $activeTab === 'users' ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
        >
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Deleted Users
                @if($trashedUsers->total() > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-error/10 text-error">
                        {{ $trashedUsers->total() }}
                    </span>
                @endif
            </div>
        </button>
        <button
            wire:click="$set('activeTab', 'classes')"
            class="px-5 py-3 text-sm font-semibold border-b-2 transition-colors
                {{ $activeTab === 'classes' ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
        >
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Deleted Classes
                @if($trashedClasses->total() > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-error/10 text-error">
                        {{ $trashedClasses->total() }}
                    </span>
                @endif
            </div>
        </button>
        <button
            wire:click="$set('activeTab', 'sessions')"
            class="px-5 py-3 text-sm font-semibold border-b-2 transition-colors
                {{ $activeTab === 'sessions' ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
        >
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Deleted Sessions
                @if($trashedSessions->total() > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-error/10 text-error">
                        {{ $trashedSessions->total() }}
                    </span>
                @endif
            </div>
        </button>
    </div>

    <!-- Users Tab -->
    @if($activeTab === 'users')
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50/50">
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Name</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Email</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Role</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Deleted At</th>
                            <th class="px-6 py-4 text-center font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($trashedUsers as $user)
                            <tr class="hover:bg-gray-50/50 transition-colors opacity-75">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-error/10 text-error flex items-center justify-center font-bold text-sm">
                                            {{ strtoupper(substr($user->first_name, 0, 1).substr($user->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-700">{{ $user->first_name }} {{ $user->last_name }}</div>
                                            <div class="text-xs text-gray-400">{{ $user->identity_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">{{ $user->deleted_at->format('M d, Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
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
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <p class="font-medium">No deleted users</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($trashedUsers->hasPages())
            <div class="mt-4">{{ $trashedUsers->links() }}</div>
        @endif
    @endif

    <!-- Classes Tab -->
    @if($activeTab === 'classes')
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50/50">
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Class</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Subject</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Faculty</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Deleted At</th>
                            <th class="px-6 py-4 text-center font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($trashedClasses as $class)
                            <tr class="hover:bg-gray-50/50 transition-colors opacity-75">
                                <td class="px-6 py-4 font-semibold text-gray-700">{{ $class->name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $class->subject?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $class->faculty?->first_name }} {{ $class->faculty?->last_name }}</td>
                                <td class="px-6 py-4 text-gray-500 text-xs">{{ $class->deleted_at->format('M d, Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            wire:click="restoreClass({{ $class->id }})"
                                            wire:confirm="Restore this class?"
                                            class="p-2 text-success hover:bg-success/10 rounded-lg transition-colors"
                                            title="Restore class"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </button>
                                        <button
                                            wire:click="forceDeleteClass({{ $class->id }})"
                                            wire:confirm="Permanently delete this class? This CANNOT be undone."
                                            class="p-2 text-error hover:bg-error/10 rounded-lg transition-colors"
                                            title="Permanently delete"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <p class="font-medium">No deleted classes</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($trashedClasses->hasPages())
            <div class="mt-4">{{ $trashedClasses->links() }}</div>
        @endif
    @endif

    <!-- Sessions Tab -->
    @if($activeTab === 'sessions')
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50/50">
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Class</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Date</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Deleted At</th>
                            <th class="px-6 py-4 text-center font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($trashedSessions as $session)
                            <tr class="hover:bg-gray-50/50 transition-colors opacity-75">
                                <td class="px-6 py-4 text-gray-700">{{ $session->classSection?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $session->date->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">{{ $session->deleted_at->format('M d, Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            wire:click="restoreSession({{ $session->id }})"
                                            wire:confirm="Restore this session?"
                                            class="p-2 text-success hover:bg-success/10 rounded-lg transition-colors"
                                            title="Restore session"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </button>
                                        <button
                                            wire:click="forceDeleteSession({{ $session->id }})"
                                            wire:confirm="Permanently delete this session? This CANNOT be undone."
                                            class="p-2 text-error hover:bg-error/10 rounded-lg transition-colors"
                                            title="Permanently delete"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <p class="font-medium">No deleted sessions</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($trashedSessions->hasPages())
            <div class="mt-4">{{ $trashedSessions->links() }}</div>
        @endif
    @endif

</div>

