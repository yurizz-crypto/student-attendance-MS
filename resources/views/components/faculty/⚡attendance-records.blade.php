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
                ['name' => 'Seth Laurence Bongo', 'id' => 'STUD-001', 'status' => 'Present', 'time' => '10:00 AM'],
                ['name' => 'Yuri Salise', 'id' => 'STUD-002', 'status' => 'Present', 'time' => '10:02 AM'],
                ['name' => 'John Doe', 'id' => 'STUD-003', 'status' => 'Absent', 'time' => '--'],
                ['name' => 'Jane Smith', 'id' => 'STUD-004', 'status' => 'Late', 'time' => '10:15 AM'],
            ]
        ];
    }
}; ?>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-200 space-y-4 sm:space-y-0 sm:flex sm:items-center sm:justify-between">
        <div class="flex flex-col sm:flex-row gap-4 flex-1">
            <div class="w-full sm:max-w-xs">
                <label for="search" class="sr-only">Search</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg>
                    </div>
                    <input wire:model.live="search" type="search" class="block w-full rounded-md border-0 py-2 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Search student name or ID...">
                </div>
            </div>
            <select wire:model.live="filterClass" class="block w-full sm:w-auto rounded-md border-0 py-2 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                <option value="">All Classes</option>
                <option value="IT 311">IT 311 - Web Systems</option>
                <option value="CS 102">CS 102 - Data Structures</option>
            </select>
            <input wire:model.live="filterDate" type="date" class="block w-full sm:w-auto rounded-md border-0 py-2 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
        
        <div class="flex gap-3">
            <button wire:click="export" class="inline-flex items-center gap-x-2 rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <svg class="-ml-0.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                Export
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-sm font-semibold text-gray-900">Student</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Student ID</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Time Logged</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-6"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($records as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm font-medium text-gray-900">{{ $record['name'] }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $record['id'] }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $record['time'] }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            @if($record['status'] === 'Present')
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Present</span>
                            @elseif($record['status'] === 'Late')
                                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Late</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Absent</span>
                            @endif
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900">Edit<span class="sr-only">, {{ $record['name'] }}</span></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>