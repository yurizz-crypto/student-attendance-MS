<div class="space-y-8">
    <!-- Key Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users Card -->
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Total Users</p>
                    <p class="text-3xl font-bold text-navy">{{ number_format($stats['total_users']) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Active users in system</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-brand/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Faculty Users Card -->
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Faculty</p>
                    <p class="text-3xl font-bold text-navy">{{ number_format($stats['faculty_users']) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Teaching staff</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-info/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Student Users Card -->
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Students</p>
                    <p class="text-3xl font-bold text-navy">{{ number_format($stats['student_users']) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Enrolled students</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-success/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17.25c0 5.25 3.07 9.386 7.5 11.386m0-13c5.5 0 10 4.745 10 10.25 0 5.25-3.07 9.386-7.5 11.386m0 0A21.75 21.75 0 0015.75 23.75" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Classes Card -->
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Classes</p>
                    <p class="text-3xl font-bold text-navy">{{ number_format($stats['total_classes']) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Active classes</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-warning/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17.25c0 5.25 3.07 9.386 7.5 11.386m0-13c5.5 0 10 4.745 10 10.25 0 5.25-3.07 9.386-7.5 11.386" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Activity Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Today's Activity -->
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-6">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Today's Activity</p>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-700">Total Actions</span>
                    <span class="text-lg font-bold text-navy">{{ $stats['today_logs'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-700">Created</span>
                    <span class="text-lg font-semibold text-success">{{ $stats['created_actions'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-700">Updated</span>
                    <span class="text-lg font-semibold text-info">{{ $stats['updated_actions'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-700">Deleted</span>
                    <span class="text-lg font-semibold text-error">{{ $stats['deleted_actions'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- User Creation Stats -->
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-6">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">New Users</p>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-700">Today</span>
                    <span class="text-lg font-bold text-navy">{{ $userStats['created_today'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-700">This Week</span>
                    <span class="text-lg font-bold text-navy">{{ $userStats['created_this_week'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-700">This Month</span>
                    <span class="text-lg font-bold text-navy">{{ $userStats['created_this_month'] }}</span>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-6">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">System Status</p>
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-success"></span>
                    <span class="text-sm text-gray-700">Database</span>
                    <span class="ml-auto text-xs font-semibold text-success">Connected</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-success"></span>
                    <span class="text-sm text-gray-700">Audit Logs</span>
                    <span class="ml-auto text-xs font-semibold text-success">Active</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-success"></span>
                    <span class="text-sm text-gray-700">Total Logs</span>
                    <span class="ml-auto text-xs font-semibold text-navy">{{ number_format($stats['total_logs']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Log -->
    <div class="bg-surface rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
            <h3 class="font-bold text-navy">Recent System Activity</h3>
            <p class="text-xs text-gray-500 mt-1">Latest 10 actions in the system</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/50">
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">User</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Action</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Model</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Description</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentLogs as $log)
                        <tr class="hover:bg-gray-50/50 transition-colors">
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
                                        {{ $log->user?->first_name ?? 'System' }}
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
                            <td class="px-6 py-4 text-gray-600 text-sm">
                                {{ $log->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                <span class="text-xs">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="font-medium">No activity yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50 text-center">
            <a href="{{ route('admin.audit-logs') }}" class="text-sm font-semibold text-brand hover:text-brand-hover transition-colors">
                View All Activity →
            </a>
        </div>
    </div>
</div>
