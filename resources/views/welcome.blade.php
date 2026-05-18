<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteSettings?->site_name ?? config('app.name', 'Student Attendance MS') }}</title>

    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    @if(!empty($siteSettings?->favicon_url))
        <link rel="icon" href="{{ $siteSettings->favicon_url }}" type="image/x-icon">
    @endif
    <style>
        :root {
            --color-primary:
                {{ $siteSettings?->primary_color ?? '#084924' }}
            ;
            --color-primary-hover:
                {{ $siteSettings?->primary_hover_color ?? '#053319' }}
            ;
            --color-secondary:
                {{ $siteSettings?->secondary_color ?? '#FDC601' }}
            ;
            --color-info:
                {{ $siteSettings?->info_color ?? '#2F80ED' }}
            ;
            --color-success:
                {{ $siteSettings?->success_color ?? '#27AE60' }}
            ;
            --color-warning:
                {{ $siteSettings?->warning_color ?? '#E2B93B' }}
            ;
            --color-error:
                {{ $siteSettings?->error_color ?? '#EB5757' }}
            ;
            --color-background:
                {{ $siteSettings?->background_color ?? '#F8FAFC' }}
            ;
            --color-surface:
                {{ $siteSettings?->surface_color ?? '#FFFFFF' }}
            ;
            --color-text-main:
                {{ $siteSettings?->text_color ?? '#0F172A' }}
            ;
        }
    </style>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            brand: {
                                DEFAULT: 'var(--color-primary)',
                                hover: 'var(--color-primary-hover)',
                            },
                            navy: 'var(--color-secondary)',
                            base: 'var(--color-background)',
                            surface: 'var(--color-surface)',
                        },
                        fontFamily: {
                            sans: ['Instrument Sans', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
    @endif
</head>

<body
    class="antialiased bg-base text-navy font-sans flex flex-col min-h-screen selection:bg-brand selection:text-white">

    <header class="bg-surface border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-brand rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-md overflow-hidden">
                        @if(!empty($siteSettings?->logo_url))
                            <img src="{{ $siteSettings->logo_url }}" alt="Logo" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($siteSettings?->site_name ?? 'A', 0, 1)) }}
                        @endif
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-navy">
                        {{ $siteSettings?->site_name ?? 'AttendanceMS' }}
                    </span>
                </div>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-2 sm:gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="text-sm font-semibold text-navy hover:text-brand transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-sm font-semibold text-navy hover:text-brand px-3 py-2 transition-colors">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="text-sm font-semibold bg-brand text-white hover:bg-brand-hover px-5 py-2.5 rounded-lg shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </div>
    </header>

    <main
        class="flex-grow flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 py-20 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-surface to-base">
        <div class="text-center max-w-4xl mx-auto">
            <div
                class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-brand/10 text-brand text-sm font-semibold mb-8">
                <span class="flex h-2 w-2 rounded-full bg-brand animate-pulse"></span>
                Streamlined Class Management
            </div>

            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-navy mb-8 leading-tight">
                Modernizing <br />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand to-brand-hover">
                    Student Attendance
                </span>
            </h1>

            <p class="text-lg md:text-xl text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                A fast, reliable, and intuitive platform designed to help educators track attendance, manage classes
                effortlessly, and generate comprehensive reports in seconds.
            </p>

            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="w-full sm:w-auto px-8 py-4 text-base font-semibold text-white bg-brand hover:bg-brand-hover rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="w-full sm:w-auto px-8 py-4 text-base font-semibold text-white bg-brand hover:bg-brand-hover rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                        Get Started
                </a @endauth </div>
            </div>
    </main>

    <section id="features" class="py-24 bg-surface border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-navy">Designed for Efficiency</h2>
                <p class="mt-4 text-gray-600 max-w-2xl mx-auto">Everything you need to run your classroom smoothly
                    without the administrative overhead.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
                <div class="p-8 rounded-3xl bg-base border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 bg-brand/10 text-brand rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-navy mb-3">Real-time Tracking</h3>
                    <p class="text-gray-600 leading-relaxed">Record attendance instantly with a modern interface. Data
                        syncs immediately across the platform.</p>
                </div>

                <div class="p-8 rounded-3xl bg-base border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 bg-brand/10 text-brand rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-navy mb-3">Advanced Analytics</h3>
                    <p class="text-gray-600 leading-relaxed">Generate detailed reports and monitor student attendance
                        trends over time with visual dashboards.</p>
                </div>

                <div class="p-8 rounded-3xl bg-base border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 bg-brand/10 text-brand rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-navy mb-3">User Management</h3>
                    <p class="text-gray-600 leading-relaxed">Secure role-based access for administrators, faculty, and
                        students to keep data strictly protected.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-surface border-t border-gray-100 py-10 mt-auto">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-gray-500 text-sm font-medium">
                &copy; {{ date('Y') }} {{ config('app.name', 'Student Attendance MS') }}. All rights reserved.
            </div>
            <div class="text-gray-400 text-sm flex gap-3 font-medium">
                <span>Version 1.0.0</span>
            </div>
        </div>
    </footer>

</body>

</html>