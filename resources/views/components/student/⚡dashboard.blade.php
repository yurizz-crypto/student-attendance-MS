<?php

use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    // UI State
    public string $activeTab = 'scanner'; 

    // Form Properties
    public $attendanceCode;
    public $excuseDate;
    public $excuseReason;
    public $excuseFile;

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function submitCode()
    {
        // Backend Partner will handle validation and database entry here
        // dd($this->attendanceCode);
    }

    public function submitExcuse()
    {
        // Backend Partner will handle file storage and database entry here
        // dd($this->excuseDate, $this->excuseReason, $this->excuseFile);
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
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Welcome back, {{ auth()->user()->first_name }}!</h1>
            <p class="text-sm text-gray-500">Student ID: {{ auth()->user()->identity_id }} • BS Information Technology</p>
        </div>
        <div class="self-start sm:self-center">
            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Good Standing</span>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-6 shadow-sm">
            <div class="flex items-center space-x-2 text-xs sm:text-sm font-medium text-gray-500 mb-2">
                <svg class="h-4 w-4 text-indigo-500 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
                <span>Rate</span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['attendance_rate'] }}%</div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-6 shadow-sm">
            <div class="flex items-center space-x-2 text-xs sm:text-sm font-medium text-gray-500 mb-2">
                <div class="h-2 w-2 sm:h-3 sm:w-3 rounded-full bg-green-500"></div>
                <span>Present</span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['present'] }}</div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-6 shadow-sm">
            <div class="flex items-center space-x-2 text-xs sm:text-sm font-medium text-gray-500 mb-2">
                <div class="h-2 w-2 sm:h-3 sm:w-3 rounded-full bg-red-500"></div>
                <span>Absent</span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['absent'] }}</div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-6 shadow-sm">
            <div class="flex items-center space-x-2 text-xs sm:text-sm font-medium text-gray-500 mb-2">
                <div class="h-2 w-2 sm:h-3 sm:w-3 rounded-full bg-yellow-500"></div>
                <span>Late</span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['late'] }}</div>
        </div>
    </div>

    <div class="border-b border-gray-200 overflow-x-auto hide-scrollbar">
        <nav class="-mb-px flex space-x-8 min-w-max" aria-label="Tabs">
            <button wire:click="setTab('scanner')" 
                class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors {{ $activeTab === 'scanner' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Mark Attendance
            </button>
            <button wire:click="setTab('excuse')" 
                class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors {{ $activeTab === 'excuse' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Upload Excuse
            </button>
            <button wire:click="setTab('history')" 
                class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors {{ $activeTab === 'history' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Attendance History
            </button>
        </nav>
    </div>

    <div class="mt-6">
        @if($activeTab === 'scanner')
            <div class="mx-auto max-w-md rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8 relative overflow-hidden">
                <h3 class="mb-4 text-center text-xl font-bold text-gray-900">Session Check-In</h3>
                <p class="mb-6 text-center text-sm text-gray-500">Enter the 6-digit code provided by your instructor or scan the class QR code.</p>

                <form wire:submit.prevent="submitCode" class="space-y-4">
                    <div>
                        <label for="code" class="sr-only">6-Digit Code</label>
                        <input type="text" id="code" wire:model="attendanceCode"
                            class="block w-full rounded-lg border border-gray-300 px-4 py-4 text-center text-3xl font-mono tracking-[0.5em] text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 uppercase placeholder:text-gray-300"
                            placeholder="••••••" maxlength="6">
                    </div>
                    <button type="submit"
                        class="flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all">
                        Submit Code
                    </button>
                </form>

                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="bg-white px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Or Scan QR</span>
                    </div>
                </div>

                <button type="button"
                    class="flex w-full items-center justify-center gap-3 rounded-lg border-2 border-gray-200 bg-white px-4 py-3.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                    </svg>
                    Open Camera Scanner
                </button>
            </div>

        @elseif($activeTab === 'excuse')
            <div class="mx-auto max-w-2xl rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Upload Excuse Slip</h3>
                    <p class="mt-1 text-sm text-gray-500">Submit your medical certificate or excuse letter within 7 days of your absence.</p>
                </div>

                <form wire:submit.prevent="submitExcuse" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="missed_date" class="block text-sm font-medium text-gray-700">Date of Absence</label>
                            <input type="date" id="missed_date" wire:model="excuseDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                            <select id="reason" wire:model="excuseReason" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Select a reason...</option>
                                <option value="medical">Medical / Sick</option>
                                <option value="emergency">Family Emergency</option>
                                <option value="school_event">School Activity</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Supporting Document</label>
                        <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-300 px-6 py-10 hover:bg-gray-50 transition-colors">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                                </svg>
                                <div class="mt-4 flex text-sm leading-6 text-gray-600 justify-center">
                                    <label for="file-upload" class="relative cursor-pointer rounded-md font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                        <span>Upload a file</span>
                                        <input id="file-upload" name="file-upload" wire:model="excuseFile" type="file" class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs leading-5 text-gray-500">PNG, JPG, PDF up to 10MB</p>
                                
                                @if ($excuseFile)
                                    <p class="mt-2 text-sm text-green-600 font-medium">File attached: {{ $excuseFile->getClientOriginalName() }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="w-full sm:w-auto rounded-md bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            Submit Document
                        </button>
                    </div>
                </form>
            </div>

        @elseif($activeTab === 'history')
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-5 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Recent Attendance Records</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-sm font-semibold text-gray-900">Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Subject</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Time</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm text-gray-900 font-medium">Oct 24, 2026</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">IT 311 - Web Systems</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">10:00 AM</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Present</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm text-gray-900 font-medium">Oct 22, 2026</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">IT 311 - Web Systems</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">10:00 AM</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Absent</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm text-gray-900 font-medium">Oct 20, 2026</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">IT 311 - Web Systems</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">10:15 AM</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Late</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>