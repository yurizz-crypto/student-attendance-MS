<?php
use Livewire\Component;

new class extends Component {
    public $search = '';
    public $filterClass = '';
    public $filterDate = '';

    public function export()
    {
        // Backend logic for exporting to Excel/PDF will go here
    }

    public function with(): array
    {
        return [
            'records' => [
                ['name' => 'Seth Laurence Bongo', 'id' => 'STUD-001', 'status' => 'Present', 'time' => '10:00 AM', 'avatar' => 'SB'],
                ['name' => 'Yuri Salise', 'id' => 'STUD-002', 'status' => 'Present', 'time' => '10:02 AM', 'avatar' => 'YS'],
                ['name' => 'John Doe', 'id' => 'STUD-003', 'status' => 'Absent', 'time' => '--', 'avatar' => 'JD'],
                ['name' => 'Jane Smith', 'id' => 'STUD-004', 'status' => 'Late', 'time' => '10:15 AM', 'avatar' => 'JS'],
            ],
            'classes' => ['IT 311', 'CS 102', 'IT 312']
        ];
    }
}; ?>

<div class="space-y-6">
    <div class="bg-surface p-6 rounded-3xl border border-gray-100 shadow-sm">
        <div class="flex flex-col lg:flex-row gap-4 items-end">
            <div class="w-full lg:flex-1">
                <x-input-label for="search" :value="__('Search Student')" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <x-text-input wire:model.live="search" id="search" class="block w-full pl-11" placeholder="Name or ID number..." />
                </div>
            </div>

            <div class="w-full sm:w-1/2 lg:w-48">
                <x-input-label for="filterClass" :value="__('Class')" />
                <select wire:model.live="filterClass" id="filterClass" class="block w-full rounded-xl border-gray-300 bg-gray-50 text-navy py-2.5 px-4 focus:border-brand focus:ring-brand shadow-sm transition-colors sm:text-sm">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class }}">{{ $class }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-1/2 lg:w-48">
                <x-input-label for="filterDate" :value="__('Date')" />
                <x-text-input wire:model.live="filterDate" id="filterDate" type="date" class="block w-full" />
            </div>

            <x-secondary-button wire:click="export" class="w-full lg:w-auto h-[46px] flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                Export
            </x-secondary-button>
        </div>
    </div>

    <div class="bg-surface rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID Number</th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Time In</th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="relative py-4 pl-3 pr-6 text-right">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($records as $record)
                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-brand/10 text-brand flex items-center justify-center font-bold text-xs">
                                        {{ $record['avatar'] }}
                                    </div>
                                    <div class="text-sm font-bold text-navy group-hover:text-brand transition-colors">
                                        {{ $record['name'] }}
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-gray-500">
                                {{ $record['id'] }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-gray-500">
                                {{ $record['time'] }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4">
                                @if($record['status'] === 'Present')
                                    <span class="inline-flex items-center rounded-lg bg-success/10 px-2.5 py-1 text-xs font-bold text-success ring-1 ring-inset ring-success/20">Present</span>
                                @elseif($record['status'] === 'Late')
                                    <span class="inline-flex items-center rounded-lg bg-warning/10 px-2.5 py-1 text-xs font-bold text-warning ring-1 ring-inset ring-warning/20">Late</span>
                                @else
                                    <span class="inline-flex items-center rounded-lg bg-error/10 px-2.5 py-1 text-xs font-bold text-error ring-1 ring-inset ring-error/20">Absent</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                                <button class="text-gray-400 hover:text-navy transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <p class="text-sm font-medium text-gray-400">No attendance records found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>