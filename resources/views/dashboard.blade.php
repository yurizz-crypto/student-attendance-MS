<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('CISC Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 font-medium">
                    Welcome, {{ Auth::user()->first_name }}! You are logged in as: <span class="uppercase font-bold text-indigo-600">{{ Auth::user()->role }}</span>
                </div>
            </div>

            @if(Auth::user()->role === 'admin')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg border-t-4 border-purple-500">
                        <h3 class="text-lg font-bold mb-2">System Setup</h3>
                        <p class="text-sm text-gray-600 mb-4">Bulk import class lists via Excel/CSV at the start of the semester.</p>
                        <x-primary-button class="w-full justify-center">Import Students</x-primary-button>
                    </div>

                    <div class="bg-white p-6 shadow-sm sm:rounded-lg border-t-4 border-red-500">
                        <h3 class="text-lg font-bold mb-2">Critical Alerts</h3>
                        <p class="text-sm text-gray-600 mb-4">Monitor students who have reached 3+ absences or dropped below 75%.</p>
                        <x-danger-button class="w-full justify-center">View Alerts (12 Pending)</x-danger-button>
                    </div>

                    <div class="bg-white p-6 shadow-sm sm:rounded-lg border-t-4 border-gray-800">
                        <h3 class="text-lg font-bold mb-2">Audit Logs</h3>
                        <p class="text-sm text-gray-600 mb-4">View system-wide actions, device bindings, and security events.</p>
                        <x-secondary-button class="w-full justify-center">Access Logs</x-secondary-button>
                    </div>
                </div>
            @endif

            @if(Auth::user()->role === 'faculty')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg border-t-4 border-indigo-500 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold mb-2">Generate Session Code</h3>
                            <p class="text-sm text-gray-600 mb-4">Create a unique 6-digit code and QR code for your current class session. Valid for 2 hours.</p>
                        </div>
                        <x-primary-button class="justify-center">Generate New QR Code</x-primary-button>
                    </div>

                    <div class="bg-white p-6 shadow-sm sm:rounded-lg border-t-4 border-yellow-500 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold mb-2">Pending Excuse Slips</h3>
                            <p class="text-sm text-gray-600 mb-4">Review uploaded medical certificates and excuse letters from students.</p>
                        </div>
                        <x-secondary-button class="justify-center">Review Documents (3 New)</x-secondary-button>
                    </div>
                </div>
            @endif

            @if(Auth::user()->role === 'student')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg border-t-4 border-green-500">
                        <h3 class="text-lg font-bold mb-2">Mark Attendance</h3>
                        <p class="text-sm text-gray-600 mb-4">Enter the 6-digit code provided by your instructor or scan the QR code.</p>
                        
                        <form class="space-y-3">
                            <x-text-input placeholder="Enter 6-digit code" class="w-full text-center tracking-widest font-mono text-xl"/>
                            <x-primary-button class="w-full justify-center">Submit Code</x-primary-button>
                        </form>
                        
                        <div class="my-3 text-center flex items-center justify-center">
                            <div class="border-t border-gray-300 flex-grow"></div>
                            <span class="px-3 text-xs text-gray-500 uppercase">or</span>
                            <div class="border-t border-gray-300 flex-grow"></div>
                        </div>
                        
                        <x-secondary-button class="w-full justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                              <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                            </svg>
                            Open QR Scanner
                        </x-secondary-button>
                    </div>

                    <div class="bg-white p-6 shadow-sm sm:rounded-lg border-t-4 border-orange-500">
                        <h3 class="text-lg font-bold mb-2">Upload Excuse Slip</h3>
                        <p class="text-sm text-gray-600 mb-4">Submit your medical certificate within 7 days of your absence.</p>
                        
                        <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10">
                            <div class="text-center">
                                <div class="mt-4 flex text-sm leading-6 text-gray-600 justify-center">
                                    <label for="file-upload" class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                        <span>Upload a file</span>
                                        <input id="file-upload" name="file-upload" type="file" class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs leading-5 text-gray-600">PDF, PNG, JPG up to 10MB</p>
                            </div>
                        </div>
                        <x-primary-button class="mt-4 w-full justify-center">Submit Document</x-primary-button>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>