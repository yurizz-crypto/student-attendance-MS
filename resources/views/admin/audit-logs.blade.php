<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-navy tracking-tight">
            {{ __('System Audit Logs') }}
        </h2>
    </x-slot>

    <div class="animate-fade-in-up">
        <livewire:admin.audit-logs />
    </div>
</x-app-layout>