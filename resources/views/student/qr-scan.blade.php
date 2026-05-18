<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-navy tracking-tight">
                {{ __('Scan QR Code') }}
            </h2>
            <div
                class="bg-surface px-4 py-1.5 rounded-xl border border-gray-100 shadow-sm text-sm font-bold text-navy flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-success animate-pulse"></span>
                Ready to Scan
            </div>
        </div>
    </x-slot>

    <div class="animate-fade-in-up pb-10">
        <div class="max-w-2xl mx-auto">
            <!-- QR Scanner Container -->
            <div class="bg-surface rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-8">
                    <!-- Camera View -->
                    <div class="mb-6">
                        <video id="qr-video"
                            style="border: 1px solid #ccc; border-radius: 8px; width: 100%; max-width: 500px; margin: 0 auto; display: block;" />
                    </div>

                    <!-- Instructions -->
                    <div class="text-center space-y-4 mb-6">
                        <p class="text-gray-600">
                            Point your camera at the QR code displayed by your instructor to mark yourself present.
                        </p>

                        <div class="bg-info/5 border border-info/20 rounded-lg p-4">
                            <p class="text-sm text-info font-medium">
                                💡 Tip: Make sure your camera has good lighting and the QR code is clearly visible.
                            </p>
                        </div>
                    </div>

                    <!-- Start/Stop Button -->
                    <div class="flex gap-3 justify-center mb-6">
                        <button id="start-scanner" onclick="startScanner()"
                            class="px-6 py-3 bg-brand text-white rounded-lg font-semibold hover:bg-brand-hover transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Start Scanner
                        </button>

                        <button id="stop-scanner" onclick="stopScanner()"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors flex items-center gap-2 hidden">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 4a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H8a2 2 0 01-2-2V4zM14 4a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2h-4a2 2 0 01-2-2V4z" />
                            </svg>
                            Stop Scanner
                        </button>
                    </div>


                </div>
            </div>

            <!-- Alternative: Manual QR Entry -->
            <div class="mt-6 bg-surface rounded-lg border border-gray-200 shadow-sm overflow-hidden p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Scan Manually</h3>
                <p class="text-sm text-gray-600 mb-4">
                    If you can't use the camera, you can manually enter the QR code data below:
                </p>

                <form id="manual-scan-form" onsubmit="handleManualScan(event)" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">QR Code Data</label>
                        <input type="text" id="qr-data-input" placeholder="Paste the QR code data here"
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-brand">
                    </div>

                    <button type="submit"
                        class="w-full px-4 py-2 bg-brand text-white rounded-lg font-semibold hover:bg-brand-hover transition-colors">
                        Submit
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
        let scanner = null;
        let isScanning = false;
        const video = document.getElementById('qr-video');
        const startBtn = document.getElementById('start-scanner');
        const stopBtn = document.getElementById('stop-scanner');

        async function startScanner() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' }
                });

                video.srcObject = stream;
                video.style.display = 'block';
                isScanning = true;

                startBtn.classList.add('hidden');
                stopBtn.classList.remove('hidden');

                video.onloadedmetadata = function () {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;

                    function scanQRCode() {
                        if (!isScanning) return;

                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imageData.data, imageData.width, imageData.height);

                        if (code) {
                            processQRCode(code.data);
                        } else {
                            requestAnimationFrame(scanQRCode);
                        }
                    }

                    scanQRCode();
                };
            } catch (error) {
                showError('Unable to access camera: ' + error.message);
            }
        }

        function stopScanner() {
            isScanning = false;
            if (video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
            }
            video.style.display = 'none';

            startBtn.classList.remove('hidden');
            stopBtn.classList.add('hidden');
        }

        function processQRCode(qrData) {
            stopScanner();

            fetch('{{ route("student.qr-scan.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ qr_data: qrData })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccess(data.message, data.data);
                    } else {
                        showError(data.message);
                    }
                })
                .catch(error => {
                    showError('Error processing QR code: ' + error.message);
                    startBtn.classList.remove('hidden');
                    stopBtn.classList.add('hidden');
                });
        }

        function handleManualScan(event) {
            event.preventDefault();
            const qrData = document.getElementById('qr-data-input').value.trim();

            if (!qrData) {
                showError('Please enter the QR code data');
                return;
            }

            processQRCode(qrData);
        }

        function showSuccess(message, data) {
            let msg = message;
            if (data) {
                msg = message + ` (${data.subject} - ${data.className})`;
            }
            window.dispatchEvent(new CustomEvent('swal:success', {
                detail: {
                    title: 'Success!',
                    message: msg
                }
            }));
        }

        function showError(message) {
            window.dispatchEvent(new CustomEvent('swal:error', {
                detail: {
                    title: 'Error',
                    message: message
                }
            }));
        }
    </script>
</x-app-layout>