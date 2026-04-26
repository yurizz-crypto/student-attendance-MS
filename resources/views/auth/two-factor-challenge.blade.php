<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Please confirm access to your account by entering the authentication code sent to your email address.') }}
    </div>

    <form method="POST" action="{{ route('two-factor.login') }}">
        @csrf

        <div>
            <x-input-label for="code" :value="__('6-Digit Code')" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" required autofocus autocomplete="one-time-code" maxlength="6" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Verify') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>