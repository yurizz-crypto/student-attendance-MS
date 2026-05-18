<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-2xl text-navy tracking-tight">Notifications</h2>
                @if($unreadCount > 0)
                    <p class="text-sm text-gray-500 mt-0.5">{{ $unreadCount }} unread</p>
                @endif
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @if($unreadCount > 0)
                    <button wire:click="markAllAsRead"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-brand bg-brand/10 hover:bg-brand/20 rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Mark all read
                    </button>
                @endif
                <button wire:click="deleteAllRead"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-error bg-error/10 hover:bg-error/20 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete read
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5 animate-fade-in-up">

        {{-- Filter tabs --}}
        <div class="flex gap-1 p-1 bg-surface border border-gray-100 rounded-xl w-fit shadow-sm">
            <button wire:click="$set('filter', 'all')"
                class="px-4 py-1.5 text-sm font-semibold rounded-lg transition-all {{ $filter === 'all' ? 'bg-brand text-white shadow-sm' : 'text-gray-500 hover:text-navy' }}">
                All
            </button>
            <button wire:click="$set('filter', 'unread')"
                class="px-4 py-1.5 text-sm font-semibold rounded-lg transition-all {{ $filter === 'unread' ? 'bg-brand text-white shadow-sm' : 'text-gray-500 hover:text-navy' }}">
                Unread
                @if($unreadCount > 0)
                    <span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold rounded-full {{ $filter === 'unread' ? 'bg-white/20' : 'bg-error/10 text-error' }}">{{ $unreadCount }}</span>
                @endif
            </button>
        </div>

        {{-- Notifications list --}}
        <div class="bg-surface border border-gray-100 rounded-3xl shadow-sm overflow-hidden">
            @forelse($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $type = $notification->data['type'] ?? 'system';

                    $iconBg = match($type) {
                        'warning' => 'bg-warning/10 text-warning',
                        'critical', 'security_alert' => 'bg-error/10 text-error',
                        'success' => 'bg-success/10 text-success',
                        'excuse_submitted', 'excuse_processed' => 'bg-info/10 text-info',
                        default => 'bg-brand/10 text-brand',
                    };

                    $iconPath = match($type) {
                        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
                        'critical', 'security_alert' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    };
                @endphp

                <div wire:key="notif-{{ $notification->id }}"
                    class="relative group flex items-start gap-4 px-6 py-5 border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-colors {{ $isUnread ? 'bg-brand/5' : '' }}">

                    {{-- Unread indicator --}}
                    @if($isUnread)
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand rounded-r-full"></div>
                    @endif

                    {{-- Icon --}}
                    <div class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center {{ $iconBg }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $iconPath !!}</svg>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        @if(!empty($notification->data['title']))
                            <p class="text-sm font-bold text-navy {{ $isUnread ? '' : 'font-semibold' }}">
                                {{ $notification->data['title'] }}
                            </p>
                        @endif
                        <p class="text-sm text-gray-700 {{ $isUnread ? 'font-medium' : '' }} mt-0.5">
                            {{ $notification->data['message'] ?? 'New notification received' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1.5">
                            {{ $notification->created_at->diffForHumans() }} &bull; {{ $notification->created_at->format('M d, Y g:i A') }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="shrink-0 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        @if($isUnread)
                            <button wire:click="markAsRead('{{ $notification->id }}')"
                                title="Mark as read"
                                class="p-2 text-brand hover:bg-brand/10 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endif
                        <button wire:click="deleteNotification('{{ $notification->id }}')"
                            title="Delete"
                            class="p-2 text-error hover:bg-error/10 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <p class="text-base font-semibold text-gray-500">
                        {{ $filter === 'unread' ? 'No unread notifications' : 'All caught up!' }}
                    </p>
                    <p class="text-sm text-gray-400 mt-1">You have no notifications to show.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($notifications->hasPages())
            <div class="mt-2">
                {{ $notifications->links() }}
            </div>
        @endif

    </div>
</div>
