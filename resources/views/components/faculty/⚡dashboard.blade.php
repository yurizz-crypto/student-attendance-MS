<?php

use Livewire\Component;
use Illuminate\Support\Str;

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

<div class="space-y-8 animate-fade-in-up">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-navy">Faculty Overview</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">Instructor ID: {{ auth()->user()->identity_id ?? 'N/A' }} <span class="mx-2">•</span> College of Information Sciences and Computing</p>
        </div>
        <div class="bg-surface px-4 py-2 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-success animate-pulse"></div>
            <span class="text-sm font-semibold text-navy">Live System Active</span>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-info/10 text-info flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Today's Classes</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['today_classes'] }}</p>
            </div>
        </div>

        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-brand/10 text-brand flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Active Students</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['active_students'] }}</p>
            </div>
        </div>

        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-error/10 text-error flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Pending Excuses</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['pending_excuses'] }}</p>
            </div>
        </div>

        <div class="bg-surface rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-success/10 text-success flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h2.625L7.5 9.25l3.25 7.5L14.375 13H21" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Avg Attendance</p>
                <p class="text-2xl font-bold text-navy">{{ $stats['avg_attendance'] }}%</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-surface rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-bold text-navy">Start Attendance Session</h2>
                    <p class="text-sm text-gray-500 font-medium">Generate a secure code for your current class.</p>
                </div>
                <div class="w-10 h-10 bg-brand/10 text-brand rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" /></svg>
                </div>
            </div>

            <div class="p-6 sm:p-8 flex-1 flex flex-col md:flex-row gap-8">
                <div class="flex-1 space-y-5">
                    <form wire:submit="generateCode" class="space-y-5">
                        <div>
                            <label for="class" class="block text-sm font-semibold text-navy">Select Class</label>
                            <select wire:model="selectedClass" id="class" class="mt-2 block w-full rounded-xl border-0 py-3 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-brand sm:text-sm sm:leading-6 bg-gray-50 font-medium">
                                <option value="">-- Choose a scheduled class --</option>
                                @foreach($my_classes as $class)
                                    <option value="{{ $class }}">{{ $class }}</option>
                                @endforeach
                            </select>
                            @error('selectedClass') <span class="text-error text-xs font-medium mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="expiry" class="block text-sm font-semibold text-navy">Code Validity Duration</label>
                            <select wire:model="expiryTime" id="expiry" class="mt-2 block w-full rounded-xl border-0 py-3 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-brand sm:text-sm sm:leading-6 bg-gray-50 font-medium">
                                <option value="1">1 Hour</option>
                                <option value="2">2 Hours</option>
                                <option value="3">3 Hours</option>
                                <option value="24">24 Hours</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full mt-2 py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-brand hover:bg-brand-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand transition-all transform hover:-translate-y-0.5">
                            Generate Code & QR
                        </button>
                    </form>
                </div>

                <div class="flex-1 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center p-6 relative overflow-hidden min-h-[250px]">
                    @if($generatedCode)
                        <div class="text-center animate-fade-in-up">
                            <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-2">Attendance Code</p>
                            <div class="bg-white px-8 py-4 rounded-2xl shadow-sm border border-gray-100 mb-4">
                                <h3 class="text-4xl md:text-5xl font-black text-brand tracking-[0.25em] ml-2">{{ $generatedCode }}</h3>
                            </div>
                            <p class="text-sm font-medium text-gray-500">Share this code or project the QR code.</p>
                            
                            <div class="mt-4 flex gap-2 justify-center">
                                <button class="text-xs font-bold text-navy bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                                    Show QR Code
                                </button>
                                <button class="text-xs font-bold text-white bg-brand px-3 py-1.5 rounded-lg hover:bg-brand-hover transition-colors shadow-sm">
                                    Copy Code
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-sm font-medium">Select a class and generate<br/>a code to display it here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-3xl border border-gray-100 shadow-sm flex flex-col h-full overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h2 class="text-lg font-bold text-navy">Action Required</h2>
                <span class="bg-error/10 text-error ring-1 ring-error/20 py-1 px-2.5 rounded-full text-xs font-bold">{{ $stats['pending_excuses'] }} Pending</span>
            </div>
            
            <div class="flex-1 p-4 space-y-3 overflow-y-auto">
                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-2xl hover:bg-gray-50 transition-colors group">
                    <div>
                        <p class="text-sm font-bold text-navy">Seth Laurence Bongo</p>
                        <p class="text-xs font-medium text-gray-500 mt-0.5">IT 311 • Medical Certificate</p>
                    </div>
                    <button class="text-brand hover:text-brand-hover text-sm font-bold bg-brand/5 px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-all">
                        Review
                    </button>
                </div>
                
                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-2xl hover:bg-gray-50 transition-colors group">
                    <div>
                        <p class="text-sm font-bold text-navy">John Doe</p>
                        <p class="text-xs font-medium text-gray-500 mt-0.5">CS 102 • School Activity</p>
                    </div>
                    <button class="text-brand hover:text-brand-hover text-sm font-bold bg-brand/5 px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-all">
                        Review
                    </button>
                </div>

                @if($stats['pending_excuses'] === 0)
                    <div class="text-center py-8">
                        <p class="text-sm text-gray-500 font-medium">No pending excuses.</p>
                    </div>
                @endif
            </div>

            <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                <button class="w-full text-center text-sm font-bold text-gray-600 hover:text-brand transition-colors">
                    View All Excuses &rarr;
                </button>
            </div>
        </div>
        
    </div>
</div>