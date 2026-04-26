<nav x-data="{ open: false }" class="bg-surface border-b border-gray-100 shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group transition-transform hover:scale-105">
                        <div class="w-9 h-9 bg-brand rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-sm group-hover:bg-brand-hover transition-colors">
                            A
                        </div>
                        <span class="text-xl font-extrabold tracking-tight text-navy hidden sm:block">
                            Attendance<span class="text-brand">MS</span>
                        </span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:flex h-full">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(Auth::user()->role === 'faculty')
                        <x-nav-link :href="route('faculty.classes')" :active="request()->routeIs('faculty.classes')">
                            {{ __('My Classes') }}
                        </x-nav-link>
                        <x-nav-link :href="route('faculty.attendance')" :active="request()->routeIs('faculty.attendance')">
                            {{ __('Attendance Records') }}
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->role === 'admin')
                        <x-nav-link href="#">
                            {{ __('Manage Users') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-1.5 border border-gray-200 rounded-xl text-sm font-semibold text-navy bg-gray-50/50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-1 transition-all duration-200">
                            <div class="w-7 h-7 rounded-lg bg-brand/10 text-brand flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name, 0, 1)) }}
                            </div>
                            
                            <div class="hidden md:block">{{ Auth::user()->first_name }}</div>

                            <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="font-medium hover:text-brand">
                            {{ __('Profile Settings') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="font-medium text-error hover:text-error hover:bg-error/5">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-gray-400 hover:text-brand hover:bg-brand/5 focus:outline-none focus:bg-brand/5 focus:text-brand transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-100 bg-gray-50/50">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(Auth::user()->role === 'faculty')
                <x-responsive-nav-link :href="route('faculty.classes')" :active="request()->routeIs('faculty.classes')">
                    {{ __('My Classes') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('faculty.attendance')" :active="request()->routeIs('faculty.attendance')">
                    {{ __('Attendance Records') }}
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->role === 'admin')
                <x-responsive-nav-link href="#">
                    {{ __('Manage Users') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-4 border-t border-gray-200 bg-surface">
            <div class="px-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand/10 text-brand flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name, 0, 1)) }}
                </div>
                <div>
                    <div class="font-bold text-base text-navy">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->identity_id ?? Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-4 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile Settings') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="text-error hover:text-error hover:bg-error/5 border-l-transparent hover:border-error">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>