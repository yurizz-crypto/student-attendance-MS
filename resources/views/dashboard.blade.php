<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-2xl text-navy tracking-tight">
                {{ __('Platform Dashboard') }}
            </h2>
            <div class="px-4 py-1.5 rounded-xl bg-brand/10 text-brand font-bold text-xs uppercase tracking-widest border border-brand/10 shadow-sm hidden sm:block">
                {{ Auth::user()->role }} Access
            </div>
        </div>
    </x-slot>

    <div class="animate-fade-in-up pb-10">
        
        @if(Auth::user()->role === 'admin')
            <livewire:admin.dashboard />
        @endif

        @if(Auth::user()->role === 'faculty')
            <livewire:faculty.dashboard />
        @endif

        @if(Auth::user()->role === 'student')
            <livewire:student.dashboard />
        @endif

    </div>
</x-app-layout>