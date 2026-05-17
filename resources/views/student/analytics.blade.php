<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-navy tracking-tight">
                {{ __('Analytics & Reports') }}
            </h2>
            <div class="hidden sm:flex items-center gap-3">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Semester</span>
                <div class="bg-surface px-4 py-1.5 rounded-xl border border-gray-100 shadow-sm text-sm font-bold text-navy">
                    1st Semester 2026-2027
                </div>
            </div>
        </div>
    </x-slot>

    <div class="animate-fade-in-up pb-10">
        <livewire:student.analytics-dashboard />
    </div>
</x-app-layout>
