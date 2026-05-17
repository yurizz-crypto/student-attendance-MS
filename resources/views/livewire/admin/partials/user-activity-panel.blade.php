{{-- User Activity Panel Slide-Over --}}
@if($showActivityPanel && $activityUser)
    <div class="fixed inset-0 bg-black/40 z-40" wire:click="closeActivityPanel"></div>

    <div class="fixed inset-y-0 right-0 z-50 w-full max-w-xl bg-surface shadow-2xl flex flex-col overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200 bg-brand/5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-brand/10 text-brand flex items-center justify-center font-bold text-lg">
                    {{ strtoupper(substr($activityUser->first_name, 0, 1) . substr($activityUser->last_name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">{{ $activityUser->first_name }} {{ $activityUser->last_name }}</h2>
                    <p class="text-sm text-gray-500">{{ $activityUser->email }} · <span class="capitalize">{{ $activityUser->role }}</span></p>
                </div>
            </div>
            <button wire:click="closeActivityPanel" class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto p-6 space-y-6">

            {{-- Quick stats --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-brand/5 border border-brand/10 rounded-xl p-4">
                    <p class="text-xs font-semibold text-brand uppercase tracking-wide">Total Logins</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $activityStats['total_logins'] ?? 0 }}</p>
                </div>
                <div class="bg-success/5 border border-success/10 rounded-xl p-4">
                    <p class="text-xs font-semibold text-success uppercase tracking-wide">Actions Today</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $activityStats['actions_today'] ?? 0 }}</p>
                </div>
                <div class="bg-info/5 border border-info/10 rounded-xl p-4">
                    <p class="text-xs font-semibold text-info uppercase tracking-wide">This Week</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $activityStats['actions_this_week'] ?? 0 }}</p>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Last Active</p>
                    <p class="text-sm font-semibold text-gray-900 mt-1">
                        {{ $activityUser->last_activity ? $activityUser->last_activity->diffForHumans() : 'Never' }}
                    </p>
                </div>
            </div>

            {{-- Device & Last Login Info --}}
            <div class="bg-surface border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Device &amp; Session Info
                    </h3>
                </div>
                <div class="divide-y divide-gray-100 text-sm">
                    <div class="px-4 py-3 flex justify-between items-start gap-4">
                        <span class="text-gray-500 shrink-0">Last Login IP</span>
                        <span class="font-mono text-gray-800 text-right">{{ $activityStats['last_login_ip'] ?? '—' }}</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between items-start gap-4">
                        <span class="text-gray-500 shrink-0">Last Login</span>
                        <span class="text-gray-800 text-right">
                            {{ isset($activityStats['last_login']) && $activityStats['last_login'] ? $activityStats['last_login']->format('M d, Y H:i') : '—' }}
                        </span>
                    </div>
                    <div class="px-4 py-3 flex justify-between items-start gap-4">
                        <span class="text-gray-500 shrink-0">Browser / Device</span>
                        <span class="text-gray-800 text-right text-xs max-w-[260px] truncate" title="{{ $activityStats['last_login_ua'] ?? '' }}">
                            {{ isset($activityStats['last_login_ua']) && $activityStats['last_login_ua']
                                ? \Illuminate\Support\Str::limit($activityStats['last_login_ua'], 60)
                                : '—' }}
                        </span>
                    </div>
                    @if($activityUser->role === 'student')
                    <div class="px-4 py-3 flex justify-between items-start gap-4">
                        <span class="text-gray-500 shrink-0">Device Fingerprint</span>
                        <span class="font-mono text-gray-800 text-right text-xs">
                            {{ $activityUser->device_fingerprint
                                ? \Illuminate\Support\Str::limit($activityUser->device_fingerprint, 24)
                                : 'Not bound' }}
                        </span>
                    </div>
                    @endif
                    <div class="px-4 py-3 flex justify-between items-center gap-4">
                        <span class="text-gray-500 shrink-0">Account Status</span>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                            @if($activityUser->status === 'active') bg-success/10 text-success
                            @elseif($activityUser->status === 'inactive') bg-gray-100 text-gray-600
                            @else bg-warning/10 text-warning @endif">
                            {{ ucfirst($activityUser->status ?? 'active') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Most Used Actions --}}
            @if($topFeatures->isNotEmpty())
            <div class="bg-surface border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        Most Used Actions
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($topFeatures as $feature)
                        @php $maxCount = $topFeatures->first()->count; @endphp
                        <div class="px-4 py-3">
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="text-sm text-gray-700 capitalize">{{ str_replace('_', ' ', $feature->action) }}</span>
                                <span class="text-xs font-semibold text-gray-500">{{ $feature->count }}×</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="bg-brand h-1.5 rounded-full" style="width: {{ $maxCount > 0 ? round(($feature->count / $maxCount) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Login History --}}
            <div class="bg-surface border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Login History
                        <span class="text-gray-400 font-normal ml-1">(last 20)</span>
                    </h3>
                </div>
                @if($loginHistory->isEmpty())
                    <div class="px-4 py-8 text-center text-sm text-gray-400">No login records yet.</div>
                @else
                    <div class="divide-y divide-gray-100 text-sm max-h-72 overflow-y-auto">
                        @foreach($loginHistory as $entry)
                            <div class="px-4 py-3 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0
                                    @if($entry->action === 'login') bg-success/10 text-success
                                    @elseif($entry->action === 'force_logout') bg-error/10 text-error
                                    @else bg-warning/10 text-warning @endif">
                                    @if($entry->action === 'login')
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                    @elseif($entry->action === 'force_logout')
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 capitalize">{{ str_replace('_', ' ', $entry->action) }}</p>
                                    <p class="text-xs text-gray-400">{{ $entry->ip_address ?? '—' }}</p>
                                </div>
                                <div class="text-xs text-gray-400 shrink-0 text-right">
                                    {{ $entry->created_at->format('M d, Y') }}<br>{{ $entry->created_at->format('H:i') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50 flex items-center justify-between gap-3">
            <button
                wire:click="forceLogout({{ $activityUser->id }})"
                wire:confirm="Force logout {{ $activityUser->first_name }} from all devices? They will need to log in again."
                class="px-4 py-2 bg-error/10 text-error border border-error/20 rounded-lg text-sm font-semibold hover:bg-error/20 transition-colors flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Force Logout All Devices
            </button>
            <button wire:click="closeActivityPanel" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-colors">
                Close
            </button>
        </div>
    </div>
@endif
