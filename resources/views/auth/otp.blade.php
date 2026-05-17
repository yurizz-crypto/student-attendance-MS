<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing in! Before getting started, could you verify your login by entering the 6-digit code we just emailed to you?') }}
    </div>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('otp.store') }}">
        @csrf

        <div>
            <x-input-label for="otp" :value="__('OTP Code')" />
            <x-text-input id="otp" class="block mt-1 w-full text-center tracking-widest text-lg" type="text" name="otp" required autofocus placeholder="123456" maxlength="6" pattern="[0-9]{6}" />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-brand border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-hover focus:bg-brand-hover active:bg-brand-hover focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Verify OTP') }}
            </button>
        </div>
    </form>
    
    <div class="mt-4 border-t border-gray-200 pt-4">
        <form method="POST" action="{{ route('otp.resend') }}">
            @csrf
            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Resend OTP Code') }}
            </button>
        </form>
    </div>
</x-guest-layout>
