<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-navy tracking-tight">
            {{ __('Backup Settings') }}
        </h2>
    </x-slot>

    <div class="animate-fade-in-up">
        <livewire:admin.backup-settings />
    </div>
</x-app-layout>
