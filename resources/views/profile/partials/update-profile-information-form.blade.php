<section>
    <header>
        <h2 class="text-xl font-bold text-navy">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-500 font-medium">
            {{ __("Update your account's personal information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="flex items-center gap-6 pb-4">
            <div class="relative shrink-0">
                @if($user->avatar_path)
                    <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="Avatar" class="w-20 h-20 rounded-2xl object-cover border border-gray-200">
                @else
                    <div class="w-20 h-20 rounded-2xl bg-brand/10 text-brand flex items-center justify-center font-bold text-2xl border border-brand/20">
                        {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <x-input-label for="avatar" :value="__('Profile Photo (Optional)')" />
                <input type="file" id="avatar" name="avatar" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand/10 file:text-brand hover:file:bg-brand/20 cursor-pointer" />
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                <p class="text-xs text-gray-500 mt-1">Recommended: Square image, max 2MB.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <x-input-label for="first_name" :value="__('First Name')" />
                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $user->first_name)" required autofocus autocomplete="given-name" />
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>

            <div>
                <x-input-label for="last_name" :value="__('Last Name')" />
                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" required autocomplete="family-name" />
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <x-input-label for="middle_name" :value="__('Middle Name (Optional)')" />
                <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" :value="old('middle_name', $user->middle_name)" autocomplete="additional-name" />
                <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
            </div>

            <div>
                <x-input-label for="identity_id" :value="__('Student/Faculty ID')" />
                <x-text-input id="identity_id" name="identity_id" type="text" class="mt-1 block w-full" :value="old('identity_id', $user->identity_id)" required />
                <x-input-error class="mt-2" :messages="$errors->get('identity_id')" />
            </div>
        </div>

        <div class="max-w-2xl">
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-3 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-brand hover:text-brand-hover rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand transition-colors">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-3 font-semibold text-sm text-success bg-success/10 px-4 py-2 rounded-xl border border-success/20 inline-block">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4">
            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-semibold text-success flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>