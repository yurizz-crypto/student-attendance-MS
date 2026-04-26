<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-navy transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-extrabold text-2xl text-navy tracking-tight">
                {{ __('User Management') }}
            </h2>
        </div>
    </x-slot>

    <div class="animate-fade-in-up">
        <livewire:admin.user-management />
    </div>
</x-app-layout>