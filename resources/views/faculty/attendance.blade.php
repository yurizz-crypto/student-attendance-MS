<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-bold text-2xl text-navy tracking-tight">
                {{ __('Attendance Records') }}
            </h2>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Active Session</span>
                <div class="bg-surface px-4 py-1.5 rounded-xl border border-gray-100 shadow-sm text-sm font-bold text-navy flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-success animate-pulse"></span>
                    Live Reporting
                </div>
            </div>
        </div>
    </x-slot>

    <div class="animate-fade-in-up pb-10">
        <livewire:faculty.attendance-records />
    </div>
</x-app-layout>