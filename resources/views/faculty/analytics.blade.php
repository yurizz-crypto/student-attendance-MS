<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-navy tracking-tight">
                {{ __('Analytics & Reports') }}
            </h2>
        </div>
    </x-slot>

    <div class="animate-fade-in-up pb-10">
        <livewire:faculty.analytics-dashboard />
    </div>
</x-app-layout>
