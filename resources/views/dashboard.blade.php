<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('CISC Attendance Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Welcome back, {{ Auth::user()->first_name }}! ({{ ucfirst(Auth::user()->role) }})
                </div>
            </div>

            @if(Auth::user()->role === 'faculty')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                        <h3 class="text-lg font-bold mb-2">Generate Session Code</h3>
                        <p class="text-sm text-gray-600 mb-4">Create a unique 6-digit code and QR code for your current class session.</p>
                        <x-primary-button>Generate New QR Code</x-primary-button>
                    </div>

                    <div class="bg-white p-6 shadow-sm sm:rounded-lg border-l-4 border-yellow-500">
                        <h3 class="text-lg font-bold mb-2">Pending Excuse Slips</h3>
                        <p class="text-sm text-gray-600 mb-4">Review medical certificates and excuse letters from students.</p>
                        <x-secondary-button>Review Documents</x-secondary-button>
                    </div>
                </div>
            @endif

            @if(Auth::user()->role === 'student')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg border-l-4 border-green-500">
                        <h3 class="text-lg font-bold mb-2">Mark Attendance</h3>
                        <p class="text-sm text-gray-600 mb-4">Enter the 6-digit code provided by your instructor or scan the QR code.</p>
                        <x-text-input placeholder="Enter 6-digit code" class="w-full mb-3"/>
                        <x-primary-button class="w-full justify-center">Submit Code</x-primary-button>
                        <div class="mt-3 text-center">
                            <span class="text-xs text-gray-500">or</span>
                        </div>
                        <x-secondary-button class="w-full justify-center mt-2">Open QR Scanner</x-secondary-button>
                    </div>

                    <div class="bg-white p-6 shadow-sm sm:rounded-lg border-l-4 border-red-500">
                        <h3 class="text-lg font-bold mb-2">Upload Excuse Slip</h3>
                        <p class="text-sm text-gray-600 mb-4">Submit your medical certificate within 7 days of your absence.</p>
                        <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                        <x-primary-button class="mt-4">Upload Document</x-primary-button>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>