<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-navy tracking-tight">
            {{ __('Profile Settings') }}
        </h2>
    </x-slot>

    <div class="space-y-8 animate-fade-in-up pb-10">
        
        <div class="p-6 sm:p-8 bg-surface shadow-sm rounded-3xl border border-gray-100">
            <div class="max-w-3xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-surface shadow-sm rounded-3xl border border-gray-100">
            <div class="max-w-3xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-surface shadow-sm rounded-3xl border border-error/20 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-error"></div>
            
            <div class="max-w-3xl pl-2 sm:pl-0">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
        
    </div>
</x-app-layout>