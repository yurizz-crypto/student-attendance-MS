<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-navy">Welcome Back</h2>
        <p class="text-sm text-gray-500 mt-2">Please enter your credentials to access your account.</p>
    </div>

    {{-- Security Alert Banner --}}
    @if(session('captcha_required'))
        <div class="mb-5 flex items-start gap-3 p-4 rounded-xl bg-red-50 border border-red-200">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="font-semibold text-red-700 text-sm">Security Alert</p>
                <p class="text-xs text-red-600 mt-0.5">Multiple failed login attempts detected. A security verification is required. Administrators have been notified.</p>
            </div>
        </div>
    @endif

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
            
            <div class="relative mt-1">
                <x-text-input id="password" class="block w-full pr-10" type="password" name="password" required autocomplete="current-password" />
                <button type="button" onclick="togglePassword('password', 'eye-password', 'eye-slash-password')" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                    <svg id="eye-password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <svg id="eye-slash-password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Math CAPTCHA (shown after ≥3 failed attempts) --}}
        @if(session('captcha_required'))
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 space-y-3">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <label for="captcha_answer" class="text-sm font-semibold text-amber-800">Security Verification Required</label>
                </div>
                <p class="text-sm font-medium text-amber-700">{{ session('captcha_question', 'What is 5 + 3?') }}</p>
                <input
                    id="captcha_answer"
                    type="number"
                    name="captcha_answer"
                    value="{{ old('captcha_answer') }}"
                    class="w-full px-4 py-2 rounded-lg border border-amber-300 bg-white focus:outline-none focus:ring-2 focus:ring-amber-400 text-sm"
                    placeholder="Enter your answer"
                    autocomplete="off"
                >
                <x-input-error :messages="$errors->get('captcha_answer')" class="mt-1" />
            </div>
        @endif

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

    <script>
        function togglePassword(inputId, eyeId, eyeSlashId) {
            const input = document.getElementById(inputId);
            const eyeIcon = document.getElementById(eyeId);
            const eyeSlashIcon = document.getElementById(eyeSlashId);

            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }
    </script>
</x-guest-layout>