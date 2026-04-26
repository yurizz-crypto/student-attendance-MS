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
                
                <livewire:faculty.dashboard />

            @endif

            @if(Auth::user()->role === 'student')
                
                <livewire:student.dashboard />

            @endif

        </div>
    </div>
</x-app-layout>