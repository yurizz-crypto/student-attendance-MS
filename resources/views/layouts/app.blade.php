<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <style>
            .swal2-confirm:focus,
            .swal2-cancel:focus,
            .swal2-deny:focus { box-shadow: none !important; outline: none !important; }
            button.swal2-confirm { box-shadow: none !important; }
            .swal2-branded-toast.swal2-popup { padding: 0.75rem 1rem !important; }
            .swal2-branded-toast .swal2-title { font-size: 0.9rem !important; font-weight: 700 !important; margin: 0 !important; }
            .swal2-branded-toast .swal2-html-container { font-size: 0.8rem !important; margin: 0.1rem 0 0 !important; color: #475569 !important; }
            .swal2-branded-toast .swal2-icon { margin: 0 0.5rem 0 0 !important; width: 1.75rem !important; height: 1.75rem !important; }
            .swal2-branded-toast .swal2-icon .swal2-icon-content { font-size: 1.1rem !important; }
            .swal2-branded-toast .swal2-close { color: #94a3b8 !important; font-size: 1.1rem !important; top: 0.5rem !important; right: 0.5rem !important; }
            .swal2-branded-toast .swal2-close:hover { color: #0F172A !important; }
            .swal2-branded-dialog.swal2-popup .swal2-icon { margin: 0 auto 1rem !important; }
            .swal2-branded-dialog.swal2-popup .swal2-actions { margin-top: 1.5rem !important; gap: 0.5rem !important; }
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
                    swalDialog.fire({
                        icon: 'error',
                        title: data.title,
                        text: data.message,
                        confirmButtonText: 'Dismiss',
                        didOpen: (popup) => {
                            Object.assign(popup.style, { border: '1px solid #FECACA' });
                            const confirm = popup.querySelector('.swal2-confirm');
                            if (confirm) {
                                Object.assign(confirm.style, { background: '#EB5757' });
                            }
                        },
                    });
                });

                window.addEventListener('swal:warning', (event) => {
                    const data = event.detail;
                    swalDialog.fire({
                        icon: 'warning',
                        title: data.title,
                        text: data.message,
                        confirmButtonText: 'OK',
                        didOpen: (popup) => {
                            Object.assign(popup.style, { border: '1px solid #FDE68A' });
                            const confirm = popup.querySelector('.swal2-confirm');
                            if (confirm) {
                                Object.assign(confirm.style, { background: '#E2B93B', color: '#0F172A' });
                            }
                        },
                    });
                });
            });
        </script>
    </body>
</html>