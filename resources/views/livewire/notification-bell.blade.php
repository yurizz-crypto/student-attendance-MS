<div x-data="{ open: false }" class="relative" @click.outside="open = false">
    {{-- Bell Button --}}
    <button @click="open = !open" type="button" class="relative inline-flex items-center justify-center p-2 rounded-xl text-gray-500 hover:text-brand hover:bg-brand/5 focus:outline-none transition-colors duration-200">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-1.5 right-1.5 flex items-center justify-center min-w-[1.25rem] h-5 px-1 text-[10px] font-bold text-white bg-error border-2 border-surface rounded-full shadow-sm">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Polling (every 10s) — keeps count fresh without requiring WebSocket --}}
    <span wire:poll.10s="loadNotifications" class="hidden"></span>

    {{-- Dropdown Panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         class="absolute right-0 mt-2 w-80 sm:w-96 bg-surface rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden origin-top-right"
         style="display: none;">

        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/80 flex items-center justify-between">
            <h3 class="text-sm font-bold text-navy">Notifications</h3>
            @if($unreadCount > 0)
                <button type="button" wire:click.prevent.stop="markAllAsRead" class="text-xs font-semibold text-brand hover:text-brand-hover transition-colors">
                    Mark all as read
                </button>
            @endif
        </div>

        {{-- Notifications List --}}
        <div class="max-h-[28rem] overflow-y-auto divide-y divide-gray-50">
            @forelse($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $type = $notification->data['type'] ?? 'system';

                    $iconClass = match($type) {
                        'warning' => 'bg-warning/10 text-warning',
                        'critical', 'security_alert' => 'bg-error/10 text-error',
                        'success' => 'bg-success/10 text-success',
                        'excuse_submitted', 'excuse_processed' => 'bg-info/10 text-info',
                        default => 'bg-brand/10 text-brand'
                    };

                    $iconSvg = match($type) {
                        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
                        'critical', 'security_alert' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    };
                @endphp

                <div wire:key="bell-notif-{{ $notification->id }}"
                    class="relative flex items-start gap-3 p-4 hover:bg-gray-50/60 transition-colors {{ $isUnread ? 'bg-brand/[0.04]' : '' }}">

                    {{-- Unread left bar --}}
                    @if($isUnread)
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand rounded-r-full"></div>
                    @endif

                    {{-- Icon --}}
                    <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $iconClass }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $iconSvg !!}</svg>
                    </div>

                    {{-- Message + time --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-800 leading-snug {{ $isUnread ? 'font-semibold' : 'font-medium' }}">
                            {{ $notification->data['message'] ?? 'New notification received' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>

                    {{-- Action buttons — always visible, NOT hover-only --}}
                    <div class="shrink-0 flex flex-col gap-1">
                        @if($isUnread)
                            <button type="button"
                                wire:click.prevent.stop="markAsRead('{{ $notification->id }}')"
                                wire:loading.attr="disabled"
                                title="Mark as read"
                                class="p-1.5 text-brand hover:bg-brand/10 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endif
                        <button type="button"
                            wire:click.prevent.stop="deleteNotification('{{ $notification->id }}')"
                            wire:loading.attr="disabled"
                            title="Delete"
                            class="p-1.5 text-gray-400 hover:text-error hover:bg-error/10 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <p class="text-sm font-medium">All caught up!</p>
                    <p class="text-xs mt-1">You have no new notifications.</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if($notifications->count() > 0)
            <div class="px-4 py-2.5 border-t border-gray-100 bg-gray-50/80 text-center">
                <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-brand hover:text-brand-hover transition-colors">View all notifications</a>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('notification-received', () => {
            if (typeof swalToast !== 'undefined') {
                swalToast.fire({ icon: 'info', title: 'New Notification', text: 'You have a new notification.' });
                const bar = document.querySelector('.swal2-timer-progress-bar');
                if (bar) { bar.style.background = '#3B82F6'; }
            }
        });
    });
</script>
