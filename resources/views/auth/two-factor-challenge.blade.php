<x-guest-layout>
    <div class="text-center mb-6">
        <div class="w-16 h-16 bg-brand/10 text-brand rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-navy">Two-Factor Auth</h2>
        <p class="text-sm text-gray-500 mt-2 leading-relaxed">
            {{ __('Please confirm access to your account by entering the authentication code sent to your email address.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="code" :value="__('6-Digit Code')" class="text-center" />
            <x-text-input id="code" class="block mt-1 w-full text-center tracking-[0.5em] font-bold text-lg" type="text" name="code" required autofocus autocomplete="one-time-code" maxlength="6" placeholder="------" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="pt-4">
            <x-primary-button class="w-full justify-center py-3">
                {{ __('Verify Device') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>