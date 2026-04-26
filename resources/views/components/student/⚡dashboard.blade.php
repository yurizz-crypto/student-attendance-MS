<?php

use Livewire\Component;

new class extends Component {
    // 1. Backend Logic
    public string $activeTab = 'scanner'; 

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function with(): array
    {
        return [
            'stats' => [
                'attendance_rate' => 85,
                'present' => 34,
                'absent' => 6,
                'late' => 2,
            ]
        ];
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Welcome back, {{ auth()->user()->first_name }}!</h1>
            <p class="text-sm text-gray-500">Student ID: {{ auth()->user()->identity_id }} • BS Information Technology</p>
        </div>
        <div class="hidden sm:block">
            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Good Standing</span>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center space-x-2 text-sm font-medium text-gray-500 mb-2">
                <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
                <span>Overall Rate</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['attendance_rate'] }}%</div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center space-x-2 text-sm font-medium text-gray-500 mb-2">
                <div class="h-3 w-3 rounded-full bg-green-500"></div>
                <span>Classes Present</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['present'] }}</div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center space-x-2 text-sm font-medium text-gray-500 mb-2">
                <div class="h-3 w-3 rounded-full bg-red-500"></div>
                <span>Classes Absent</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['absent'] }}</div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center space-x-2 text-sm font-medium text-gray-500 mb-2">
                <div class="h-3 w-3 rounded-full bg-yellow-500"></div>
                <span>Classes Late</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['late'] }}</div>
        </div>
    </div>

    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="setTab('scanner')" 
                class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium {{ $activeTab === 'scanner' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Mark Attendance
            </button>
            <button wire:click="setTab('excuse')" 
                class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium {{ $activeTab === 'excuse' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Upload Excuse
            </button>
            <button wire:click="setTab('history')" 
                class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium {{ $activeTab === 'history' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Attendance History
            </button>
        </nav>
    </div>

    <div class="mt-6">
        @if($activeTab === 'scanner')
            <div class="rounded-xl border border-gray-200 bg-white p-8 text-center shadow-sm">
                <h3 class="text-lg font-bold text-gray-900">QR Scanner Loading...</h3>
            </div>
        @elseif($activeTab === 'excuse')
            <div class="rounded-xl border border-gray-200 bg-white p-8 text-center shadow-sm">
                <h3 class="text-lg font-bold text-gray-900">Excuse Form Loading...</h3>
            </div>
        @elseif($activeTab === 'history')
            <div class="rounded-xl border border-gray-200 bg-white p-8 text-center shadow-sm">
                <h3 class="text-lg font-bold text-gray-900">History Table Loading...</h3>
            </div>
        @endif
    </div>
</div>