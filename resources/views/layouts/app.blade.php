<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $siteSettings?->site_name ?? config('app.name', 'StudentAMS') }}</title>

    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    @if(!empty($siteSettings?->favicon_url))
        <link rel="icon" href="{{ $siteSettings->favicon_url }}" type="image/x-icon">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Session lifetime for timeout monitor (in seconds) --}}
    <meta name="session-lifetime" content="{{ config('session.lifetime') * 60 }}">

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

        .swal2-confirm:focus,
        .swal2-cancel:focus,
        .swal2-deny:focus {
            box-shadow: none !important;
            outline: none !important;
        }

        button.swal2-confirm {
            box-shadow: none !important;
        }

        .swal2-branded-toast.swal2-popup {
            padding: 0.75rem 1rem !important;
        }

        .swal2-branded-toast .swal2-title {
            font-size: 0.9rem !important;
            font-weight: 700 !important;
            margin: 0 !important;
        }

        .swal2-branded-toast .swal2-html-container {
            font-size: 0.8rem !important;
            margin: 0.1rem 0 0 !important;
            color: #475569 !important;
        }

        .swal2-branded-toast .swal2-icon {
            margin: 0 0.5rem 0 0 !important;
            width: 1.75rem !important;
            height: 1.75rem !important;
        }

        .swal2-branded-toast .swal2-icon .swal2-icon-content {
            font-size: 1.1rem !important;
        }

        .swal2-branded-toast .swal2-close {
            color: #94a3b8 !important;
            font-size: 1.1rem !important;
            top: 0.5rem !important;
            right: 0.5rem !important;
        }

        .swal2-branded-toast .swal2-close:hover {
            color: #0F172A !important;
        }

        .swal2-branded-dialog.swal2-popup .swal2-icon {
            margin: 0 auto 1rem !important;
        }

        .swal2-branded-dialog.swal2-popup .swal2-actions {
            margin-top: 1.5rem !important;
            gap: 0.5rem !important;
        }
    </style>
</head>

<body class="font-sans antialiased text-navy bg-base">
    <div class="min-h-screen">
        @include('layouts.navigation')

        @isset($header)
            <header class="bg-surface shadow-sm border-b border-gray-100">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>

    <script>
        const swalBase = {
            fontFamily: 'Instrument Sans, sans-serif',
            background: '#FFFFFF',
            color: '#0F172A',
            borderRadius: '16px',
            padding: '1.5rem',
        };

        const swalToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            timer: 3500,
            timerProgressBar: true,
            showConfirmButton: false,
            showCloseButton: true,
            customClass: {
                popup: 'swal2-branded-toast',
                closeButton: 'swal2-branded-close',
            },
            didOpen: (toast) => {
                Object.assign(toast.style, {
                    fontFamily: swalBase.fontFamily,
                    background: swalBase.background,
                    color: swalBase.color,
                    borderRadius: '14px',
                    boxShadow: '0 4px 24px 0 rgba(15,23,42,0.10)',
                    border: '1px solid #E2E8F0',
                    padding: '0.85rem 1.1rem',
                    minWidth: '300px',
                    maxWidth: '420px',
                });
                const bar = toast.querySelector('.swal2-timer-progress-bar');
                if (bar) {
                    bar.style.background = toast.__swalProgressColor || '#084924';
                }
            },
        });

        const swalDialog = Swal.mixin({
            customClass: {
                popup: 'swal2-branded-dialog',
                title: 'swal2-branded-title',
                htmlContainer: 'swal2-branded-text',
                confirmButton: 'swal2-branded-confirm',
                cancelButton: 'swal2-branded-cancel',
            },
            didOpen: (popup) => {
                Object.assign(popup.style, {
                    fontFamily: swalBase.fontFamily,
                    background: swalBase.background,
                    color: swalBase.color,
                    borderRadius: '20px',
                    boxShadow: '0 8px 40px 0 rgba(15,23,42,0.14)',
                    border: '1px solid #E2E8F0',
                    padding: '2rem',
                });
                const title = popup.querySelector('.swal2-title');
                if (title) {
                    Object.assign(title.style, { fontWeight: '700', fontSize: '1.15rem', color: '#0F172A' });
                }
                const text = popup.querySelector('.swal2-html-container');
                if (text) {
                    Object.assign(text.style, { fontSize: '0.9rem', color: '#475569', marginTop: '0.25rem' });
                }
                const confirm = popup.querySelector('.swal2-confirm');
                if (confirm) {
                    Object.assign(confirm.style, {
                        background: confirm.__swalBtnColor || '#084924',
                        border: 'none',
                        borderRadius: '10px',
                        fontFamily: swalBase.fontFamily,
                        fontWeight: '600',
                        fontSize: '0.9rem',
                        padding: '0.55rem 1.4rem',
                        boxShadow: 'none',
                    });
                }
            },
        });

        document.addEventListener('livewire:init', () => {
            // Changed from Livewire.on to window.addEventListener for proper Livewire 3 support
            window.addEventListener('swal:success', (event) => {
                const data = event.detail;
                const toast = swalToast.fire({ icon: 'success', title: data.title, text: data.message });
                toast.then && swalToast._popup && (swalToast._popup.__swalProgressColor = '#27AE60');
                Swal.getPopup() && (Swal.getPopup().__swalProgressColor = '#27AE60');
                const bar = document.querySelector('.swal2-timer-progress-bar');
                if (bar) { bar.style.background = '#27AE60'; }
            });

            window.addEventListener('swal:error', (event) => {
                const data = event.detail;
                const toast = swalToast.fire({ icon: 'error', title: data.title, text: data.message });
                toast.then && swalToast._popup && (swalToast._popup.__swalProgressColor = '#EB5757');
                Swal.getPopup() && (Swal.getPopup().__swalProgressColor = '#EB5757');
                const bar = document.querySelector('.swal2-timer-progress-bar');
                if (bar) { bar.style.background = '#EB5757'; }
            });

            window.addEventListener('swal:warning', (event) => {
                const data = event.detail;
                const toast = swalToast.fire({ icon: 'warning', title: data.title, text: data.message });
                toast.then && swalToast._popup && (swalToast._popup.__swalProgressColor = '#E2B93B');
                Swal.getPopup() && (Swal.getPopup().__swalProgressColor = '#E2B93B');
                const bar = document.querySelector('.swal2-timer-progress-bar');
                if (bar) { bar.style.background = '#E2B93B'; }
            });
        });

        // ─── Session Timeout Monitor ───────────────────────────────────────────
        (function () {
            const lifetimeMeta = document.querySelector('meta[name="session-lifetime"]');
            if (!lifetimeMeta) { return; }

            const SESSION_LIFETIME_MS = parseInt(lifetimeMeta.content, 10) * 1000;
            const WARN_BEFORE_MS = 5 * 60 * 1000; // warn 5 minutes before expiry
            const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            let lastActivity = Date.now();
            let warningShown = false;
            let countdownInterval = null;

            // Track any user interaction
            ['click', 'keydown', 'mousemove', 'touchstart', 'scroll'].forEach(evt => {
                document.addEventListener(evt, () => { lastActivity = Date.now(); }, { passive: true });
            });

            function resetTimer() {
                lastActivity = Date.now();
                warningShown = false;
                clearInterval(countdownInterval);
            }

            function showCountdown() {
                if (warningShown) { return; }
                warningShown = true;

                let remaining = Math.ceil(WARN_BEFORE_MS / 1000); // seconds

                Swal.fire({
                    title: '⏱ Session Expiring Soon',
                    html: `<p class="text-sm text-gray-600">Your session will expire in <strong id="session-countdown">${remaining}</strong> seconds due to inactivity.</p><p class="text-xs text-gray-400 mt-1">Click "Stay Logged In" to extend your session.</p>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Stay Logged In',
                    cancelButtonText: 'Log Out',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    customClass: { popup: 'swal2-branded-dialog' },
                    didOpen: (popup) => {
                        Object.assign(popup.style, {
                            fontFamily: 'Instrument Sans, sans-serif',
                            borderRadius: '20px',
                            border: '1px solid #FDE68A',
                            padding: '2rem',
                        });
                        const c = popup.querySelector('.swal2-confirm');
                        if (c) Object.assign(c.style, { background: '#084924', border: 'none', borderRadius: '10px', fontFamily: 'Instrument Sans, sans-serif', fontWeight: '600', padding: '0.55rem 1.4rem', boxShadow: 'none' });
                        const x = popup.querySelector('.swal2-cancel');
                        if (x) Object.assign(x.style, { background: '#f1f5f9', color: '#0F172A', border: 'none', borderRadius: '10px', fontFamily: 'Instrument Sans, sans-serif', fontWeight: '600', padding: '0.55rem 1.4rem', boxShadow: 'none' });

                        countdownInterval = setInterval(() => {
                            remaining--;
                            const el = document.getElementById('session-countdown');
                            if (el) { el.textContent = remaining; }
                            if (remaining <= 0) {
                                clearInterval(countdownInterval);
                                Swal.close();
                                window.location.href = '{{ route("login") }}';
                            }
                        }, 1000);
                    },
                    willClose: () => { clearInterval(countdownInterval); },
                }).then(result => {
                    if (result.isConfirmed) {
                        // POST keepalive to refresh session
                        fetch('{{ route("keep-alive") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                        }).then(() => { resetTimer(); });
                    } else {
                        window.location.href = '{{ route("logout") }}';
                    }
                });
            }

            // Poll every 10 seconds
            setInterval(() => {
                const idle = Date.now() - lastActivity;
                if (idle >= SESSION_LIFETIME_MS) {
                    // Session has expired — redirect
                    clearInterval(countdownInterval);
                    window.location.href = '{{ route("login") }}';
                } else if (idle >= SESSION_LIFETIME_MS - WARN_BEFORE_MS) {
                    showCountdown();
                } else {
                    warningShown = false;
                }
            }, 10000);
        })();
    </script>
</body>

</html>