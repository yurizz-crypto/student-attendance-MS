<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-4">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Total Backups</p>
            <p class="text-2xl font-bold text-navy mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-4">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Successful</p>
            <p class="text-2xl font-bold text-success mt-1">{{ $stats['success'] }}</p>
        </div>
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-4">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Failed</p>
            <p class="text-2xl font-bold text-error mt-1">{{ $stats['failed'] }}</p>
        </div>
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-4">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Retention Policy</p>
            <p class="text-2xl font-bold text-navy mt-1">30 days</p>
        </div>
    </div>

    {{-- Manual Backup Triggers --}}
    <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-6">
        <h3 class="font-bold text-navy mb-1">One-Click Manual Backup</h3>
        <p class="text-sm text-gray-500 mb-5">Trigger a backup immediately. You will receive an email when it completes.</p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Database --}}
            <div class="border border-gray-200 rounded-lg p-4 flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-brand/10 rounded-lg text-brand">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">Database</p>
                        <p class="text-xs text-gray-500">PostgreSQL dump → ZIP</p>
                    </div>
                </div>
                @if($stats['last_database'])
                    <p class="text-xs text-gray-400">Last: {{ $stats['last_database']->created_at->diffForHumans() }}</p>
                @else
                    <p class="text-xs text-gray-400">No backup yet</p>
                @endif
                <button type="button" wire:click="runBackup('database')" wire:loading.attr="disabled"
                    class="w-full py-2 px-4 bg-brand text-white text-sm font-semibold rounded-lg hover:bg-brand-hover transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
                    <svg wire:loading wire:target="runBackup('database')" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="runBackup('database')">Backup Database</span>
                    <span wire:loading wire:target="runBackup('database')">Running...</span>
                </button>
            </div>

            {{-- Files --}}
            <div class="border border-gray-200 rounded-lg p-4 flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-info/10 rounded-lg text-info">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">File Uploads</p>
                        <p class="text-xs text-gray-500">storage/app/public → ZIP</p>
                    </div>
                </div>
                @if($stats['last_files'])
                    <p class="text-xs text-gray-400">Last: {{ $stats['last_files']->created_at->diffForHumans() }}</p>
                @else
                    <p class="text-xs text-gray-400">No backup yet</p>
                @endif
                <button type="button" wire:click="runBackup('files')" wire:loading.attr="disabled"
                    class="w-full py-2 px-4 bg-info text-white text-sm font-semibold rounded-lg hover:bg-info/90 transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
                    <svg wire:loading wire:target="runBackup('files')" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="runBackup('files')">Backup Files</span>
                    <span wire:loading wire:target="runBackup('files')">Running...</span>
                </button>
            </div>

            {{-- Full --}}
            <div class="border border-gray-200 rounded-lg p-4 flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-warning/10 rounded-lg text-warning">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">Full System</p>
                        <p class="text-xs text-gray-500">DB + Files → Compressed archive</p>
                    </div>
                </div>
                @if($stats['last_full'])
                    <p class="text-xs text-gray-400">Last: {{ $stats['last_full']->created_at->diffForHumans() }}</p>
                @else
                    <p class="text-xs text-gray-400">No backup yet</p>
                @endif
                <button type="button" wire:click="runBackup('full')" wire:loading.attr="disabled"
                    class="w-full py-2 px-4 bg-warning text-white text-sm font-semibold rounded-lg hover:bg-warning/90 transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
                    <svg wire:loading wire:target="runBackup('full')" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="runBackup('full')">Full Backup</span>
                    <span wire:loading wire:target="runBackup('full')">Running...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Schedule --}}
    <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-6">
        <h3 class="font-bold text-navy mb-1">Backup Schedule</h3>
        <p class="text-sm text-gray-500 mb-5">Automated schedules run via Laravel Scheduler. Ensure <code class="bg-gray-100 px-1 rounded text-xs">php artisan schedule:run</code> is configured as a cron job.</p>

        <div class="space-y-3">
            <div class="flex items-center justify-between p-4 rounded-lg bg-gray-50 border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-brand/10 rounded-lg text-brand">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Database Backup</p>
                        <p class="text-xs text-gray-500">Email attachment (ZIP)</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex px-2 py-1 rounded text-xs font-bold bg-brand/10 text-brand uppercase">Weekly</span>
                    <p class="text-xs text-gray-500 mt-1">Every Sunday at 2:00 AM</p>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 rounded-lg bg-gray-50 border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-info/10 rounded-lg text-info">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">File Uploads Backup</p>
                        <p class="text-xs text-gray-500">Email attachment (ZIP)</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex px-2 py-1 rounded text-xs font-bold bg-info/10 text-info uppercase">Weekly</span>
                    <p class="text-xs text-gray-500 mt-1">Every Sunday at 2:30 AM</p>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 rounded-lg bg-gray-50 border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-warning/10 rounded-lg text-warning">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Full System Backup</p>
                        <p class="text-xs text-gray-500">Email attachment (Compressed archive)</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex px-2 py-1 rounded text-xs font-bold bg-warning/10 text-warning uppercase">Monthly</span>
                    <p class="text-xs text-gray-500 mt-1">1st of every month at 3:00 AM</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Backup History Table --}}
    <div class="bg-surface rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
            <h3 class="font-bold text-navy">Backup History</h3>
            <span class="text-xs text-gray-500">30-day retention — backups auto-expire</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/50">
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Type</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Size</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Expires</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Notes</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($backupHistory as $log)
                        <tr class="hover:bg-gray-50 transition-colors" wire:key="backup-{{ $log->id }}">
                            <td class="px-6 py-3 text-gray-700">
                                <div class="font-medium">{{ $log->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $log->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex px-2 py-1 rounded text-xs font-bold uppercase
                                    @if($log->type === 'database') bg-brand/10 text-brand
                                    @elseif($log->type === 'files') bg-info/10 text-info
                                    @else bg-warning/10 text-warning @endif
                                ">{{ $log->type }}</span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-semibold
                                    @if($log->status === 'success') bg-success/10 text-success
                                    @elseif($log->status === 'failed') bg-error/10 text-error
                                    @else bg-gray-100 text-gray-600 @endif
                                ">
                                    @if($log->status === 'success') ✅ @elseif($log->status === 'failed') ❌ @else ⏳ @endif
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $log->formatted_size }}</td>
                            <td class="px-6 py-3 text-gray-500 text-xs">
                                @if($log->expires_at)
                                    {{ $log->expires_at->format('M d, Y') }}
                                    @if($log->expires_at->isPast())
                                        <span class="block text-error font-semibold">Expired</span>
                                    @else
                                        <span class="block text-gray-400">in {{ $log->expires_at->diffForHumans() }}</span>
                                    @endif
                                @else —
                                @endif
                            </td>
                            <td class="px-6 py-3 text-gray-500 text-xs max-w-[180px] truncate" title="{{ $log->notes }}">
                                {{ $log->notes ?? '—' }}
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($log->status === 'success')
                                    <button type="button" wire:click="verifyBackup({{ $log->id }})"
                                        class="p-1.5 rounded text-info hover:bg-info/10 transition-colors" title="Verify integrity">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                    @endif
                                    <button type="button" wire:click="deleteBackup({{ $log->id }})"
                                        wire:confirm="Delete this backup record? This cannot be undone."
                                        class="p-1.5 rounded text-error hover:bg-error/10 transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <p class="font-medium">No backup history found</p>
                                <p class="text-sm mt-1">Run your first backup using the buttons above.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($backupHistory->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                {{ $backupHistory->links() }}
            </div>
        @endif
    </div>
</div>
