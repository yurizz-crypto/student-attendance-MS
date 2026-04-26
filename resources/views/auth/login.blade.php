<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-navy">Welcome Back</h2>
        <p class="text-sm text-gray-500 mt-2">Please enter your credentials to access your account.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="identity_id" :value="__('Student / Faculty ID Number')" />
            <x-text-input id="identity_id" class="block mt-1 w-full" type="text" name="identity_id" :value="old('identity_id')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('identity_id')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" class="mb-0" />
                @if (Route::has('password.request'))
                    <a class="text-sm font-semibold text-brand hover:text-brand-hover transition-colors" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block">
            <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-brand shadow-sm focus:ring-brand focus:ring-offset-0 cursor-pointer" name="remember">
                <span class="ms-2 text-sm font-medium text-gray-600 group-hover:text-navy transition-colors">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        @if (Route::has('register'))
            <p class="text-center text-sm text-gray-500 mt-6 pt-6 border-t border-gray-100">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-semibold text-brand hover:text-brand-hover transition-colors">Sign up</a>
            </p>
        @endif
    </form>
</x-guest-layout>