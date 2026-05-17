<div>
    <!-- Filters -->
    <div class="mb-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex-1 flex gap-3">
                <div class="relative flex-1">
                    <input
                        type="text"
                        wire:model.live="search"
                        placeholder="Search by user name, description, or model..."
                        class="w-full px-4 py-2 pl-10 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent"
                    >
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <select
                    wire:model.live="filterAction"
                    class="px-4 py-2 rounded-lg border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent"
                >
                    <option value="">All Actions</option>
                    <option value="created">Created</option>
                    <option value="updated">Updated</option>
                    <option value="deleted">Deleted</option>
                    <option value="viewed">Viewed</option>
                    <option value="login">Login</option>
                    <option value="logout">Logout</option>
                    <option value="excuse_submitted">Excuse Submitted</option>
                    <option value="excuse_processed">Excuse Processed</option>
                </select>

                <select
                    wire:model.live="filterUser"
                    class="px-4 py-2 rounded-lg border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent"
                >
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-surface rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/50">
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 cursor-pointer hover:bg-gray-100" wire:click="sort('created_at')">
                            <div class="flex items-center gap-2">
                                Date/Time
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.5a1 1 0 10-2 0v5.5z" />
                                </svg>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">User</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Action</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Model</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Description</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-gray-700">
                                <div class="text-sm font-medium">{{ $log->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-brand/10 text-brand flex items-center justify-center font-bold text-xs">
                                        @if($log->user)
                                            {{ strtoupper(substr($log->user->first_name, 0, 1) . substr($log->user->last_name, 0, 1)) }}
                                        @else
                                            SYS
                                        @endif
                                    </div>
                                    <span class="text-gray-700">
                                        {{ $log->user?->first_name }} {{ $log->user?->last_name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold
                                    @switch($log->action)
                                        @case('created')
                                            bg-success/10 text-success
                                            @break
                                        @case('updated')
                                            bg-info/10 text-info
                                            @break
                                        @case('deleted')
                                            bg-error/10 text-error
                                            @break
                                        @case('login')
                                            bg-success/10 text-success
                                            @break
                                        @case('logout')
                                            bg-warning/10 text-warning
                                            @break
                                        @default
                                            bg-gray-100 text-gray-700
                                    @endswitch
                                ">
                                    {{ $log->getActionLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                {{ $log->getModelDisplayName() }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-sm max-w-xs">
                                {{ $log->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 rounded text-xs font-semibold
                                    @if($log->status === 'success')
                                        bg-success/10 text-success
                                    @else
                                        bg-error/10 text-error
                                    @endif
                                ">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-700 text-sm font-mono">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="font-medium">No audit logs found</p>
                                <p class="text-sm mt-1">Try adjusting your filters</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($logs->hasPages())
        <div class="mt-6">
            {{ $logs->links() }}
        </div>
    @endif
</div>
