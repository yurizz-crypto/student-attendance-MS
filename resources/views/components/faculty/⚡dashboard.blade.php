<?php

use Livewire\Component;

new class extends Component {
    // State for QR Generator
    public $selectedClass = '';
    public $generatedCode = null;
    public $expiryTime = 2; // 2 hours default

    public function generateCode()
    {
        // Require a class to be selected first
        $this->validate(['selectedClass' => 'required']);
        
        // Backend Partner will generate the actual DB record here.
        // For now, we mock a random 6-digit code.
        $this->generatedCode = strtoupper(Str::random(6));
    }

    public function with(): array
    {
        return [
            'stats' => [
                'today_classes' => 3,
                'active_students' => 142,
                'pending_excuses' => 5,
                'avg_attendance' => 89,
            ],
            'my_classes' => [
                'IT 311 - Web Systems',
                'CS 102 - Data Structures',
                'IT 312 - Software Engineering'
            ]
        ];
    }
}; ?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Faculty Overview</h1>
            <p class="text-sm text-gray-500">Instructor ID: {{ auth()->user()->identity_id }} • College of Information Sciences and Computing</p>
        </div>
        <div class="self-start sm:self-center">
            <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-600/20">{{ date('l, F j, Y') }}</span>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-6 shadow-sm border-l-4 border-l-blue-500">
            <p class="text-xs sm:text-sm font-medium text-gray-500 mb-1">Today's Classes</p>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['today_classes'] }}</div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-6 shadow-sm border-l-4 border-l-green-500">
            <p class="text-xs sm:text-sm font-medium text-gray-500 mb-1">Active Students</p>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['active_students'] }}</div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-6 shadow-sm border-l-4 border-l-yellow-500">
            <p class="text-xs sm:text-sm font-medium text-gray-500 mb-1">Pending Excuses</p>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['pending_excuses'] }}</div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-6 shadow-sm border-l-4 border-l-purple-500">
            <p class="text-xs sm:text-sm font-medium text-gray-500 mb-1">Avg Attendance</p>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['avg_attendance'] }}%</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Start Class Session</h3>
            
            <form wire:submit.prevent="generateCode" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Class</label>
                    <select wire:model="selectedClass" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">-- Choose a class --</option>
                        @foreach($my_classes as $class)
                            <option value="{{ $class }}">{{ $class }}</option>
                        @endforeach
                    </select>
                    @error('selectedClass') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Session Duration</label>
                    <select wire:model="expiryTime" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="1">1 Hour</option>
                        <option value="2">2 Hours</option>
                        <option value="3">3 Hours</option>
                    </select>
                </div>

                <button type="submit" class="w-full justify-center rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all">
                    Generate Access Code & QR
                </button>
            </form>

            @if($generatedCode)
                <div class="mt-6 pt-6 border-t border-gray-200 text-center animate-in fade-in duration-300">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Active Session Code</p>
                    <div class="text-5xl font-mono font-bold text-indigo-600 tracking-[0.2em] mb-4">
                        {{ $generatedCode }}
                    </div>
                    
                    <div class="mx-auto w-48 h-48 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <p class="text-xs text-red-500 font-medium">Expires in {{ $expiryTime }} hours</p>
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Pending Excuse Slips</h3>
                <span class="bg-red-100 text-red-700 py-1 px-2.5 rounded-full text-xs font-bold">{{ $stats['pending_excuses'] }} Action Required</span>
            </div>
            
            <div class="flex-1 space-y-3">
                <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Seth Laurence Bongo</p>
                        <p class="text-xs text-gray-500">IT 311 • Medical Certificate</p>
                    </div>
                    <button class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Review</button>
                </div>
                <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">John Doe</p>
                        <p class="text-xs text-gray-500">CS 102 • School Activity</p>
                    </div>
                    <button class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Review</button>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200">
                <button class="w-full text-center text-sm font-medium text-gray-600 hover:text-indigo-600">
                    View All Document Reviews &rarr;
                </button>
            </div>
        </div>

    </div>
</div>