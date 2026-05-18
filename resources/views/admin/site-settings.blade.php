<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-navy tracking-tight">
            {{ __('Site Settings') }}
        </h2>
    </x-slot>

    <div class="animate-fade-in-up">
        <livewire:admin.site-settings />
    </div>
</x-app-layout>