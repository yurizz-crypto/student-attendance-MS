<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Student Attendance MS') }}</title>

        <link rel="dns-prefetch" href="https://fonts.bunny.net">
        <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-navy bg-base antialiased selection:bg-brand selection:text-white">
        
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-[20%] -right-[10%] w-[70vw] h-[70vw] rounded-full bg-brand/5 blur-[100px]"></div>
            <div class="absolute -bottom-[20%] -left-[10%] w-[60vw] h-[60vw] rounded-full bg-secondary/5 blur-[100px]"></div>
        </div>

        <div class="relative z-10 min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            
            <div class="mb-8 text-center animate-fade-in-up">
                <a href="/" class="inline-flex flex-col items-center gap-4 group">
                    <div class="w-16 h-16 bg-brand rounded-2xl flex items-center justify-center text-white font-bold text-3xl shadow-lg group-hover:scale-105 group-hover:bg-brand-hover group-hover:shadow-xl transition-all duration-300">
                        A
                    </div>
                    <span class="text-3xl font-extrabold tracking-tight text-navy">
                        Attendance<span class="text-brand">MS</span>
                    </span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-10 bg-surface shadow-xl shadow-brand/5 border border-gray-100 overflow-hidden sm:rounded-3xl">
                {{ $slot }}
            </div>
            
            <div class="mt-10 text-sm font-medium text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.name', 'Student Attendance MS') }}.
            </div>
        </div>
    </body>
</html>