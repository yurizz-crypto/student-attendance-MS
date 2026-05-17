<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function with(): array
    {
        return [
            'unreadNotifications' => Auth::user()->unreadNotifications,
        ];
    }
}; ?>

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button @click="open = !open" class="relative p-2 text-gray-400 hover:text-brand transition-colors rounded-full hover:bg-gray-100 focus:outline-none">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        @if($unreadNotifications->count() > 0)
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-error rounded-full ring-2 ring-surface animate-pulse"></span>
        @endif
    </button>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 transform"
         x-transition:enter-end="opacity-100 scale-100 transform"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100 transform"
         x-transition:leave-end="opacity-0 scale-95 transform"
         class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50 origin-top-right"
         style="display: none;">
        
        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="font-bold text-navy">Notifications</h3>
            @if($unreadNotifications->count() > 0)
                <button wire:click="markAllAsRead" class="text-xs font-bold text-brand hover:text-brand-hover">Mark all read</button>
            @endif
        </div>

        <div class="max-h-[28rem] overflow-y-auto hide-scrollbar">
            @forelse($unreadNotifications as $notification)
                <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors flex gap-4 cursor-pointer group" wire:click="markAsRead('{{ $notification->id }}')">
                    <div class="mt-1">
                        @if($notification->data['type'] === 'session_started')
                            <div class="w-8 h-8 rounded-full bg-brand/10 text-brand flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            </div>
                        @elseif($notification->data['type'] === 'marked_absent')
                            <div class="w-8 h-8 rounded-full bg-error/10 text-error flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </div>
                        @elseif($notification->data['type'] === 'excuse_submitted')
                            <div class="w-8 h-8 rounded-full bg-warning/10 text-warning flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            </div>
                        @elseif($notification->data['type'] === 'excuse_processed')
                            @if(($notification->data['status'] ?? '') === 'approved')
                                <div class="w-8 h-8 rounded-full bg-success/10 text-success flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-full bg-error/10 text-error flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </div>
                            @endif
                        @else
                            <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 line-clamp-2">
                            {{ $notification->data['message'] ?? 'New notification received.' }}
                        </p>
                        <p class="text-xs font-medium text-gray-400 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                        <div class="w-2 h-2 rounded-full bg-brand"></div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    <p class="text-sm font-medium">You're all caught up!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
