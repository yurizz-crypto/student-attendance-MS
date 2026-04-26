<?php
use Livewire\Component;

new class extends Component {
    public function with(): array
    {
        return [
            'classes' => [
                ['id' => 1, 'code' => 'IT 311', 'name' => 'Web Systems and Technologies', 'schedule' => 'MWF 10:00 AM - 11:00 AM', 'students' => 45, 'attendance' => 92, 'room' => 'Lab 3C'],
                ['id' => 2, 'code' => 'CS 102', 'name' => 'Data Structures', 'schedule' => 'TTh 1:00 PM - 2:30 PM', 'students' => 38, 'attendance' => 88, 'room' => 'Lab 2A'],
                ['id' => 3, 'code' => 'IT 312', 'name' => 'Software Engineering', 'schedule' => 'MWF 2:00 PM - 3:00 PM', 'students' => 42, 'attendance' => 95, 'room' => 'Room 404'],
            ]
        ];
    }
}; ?>

<div class="space-y-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Active Courses</h3>
            <p class="text-sm text-gray-500">Manage your assigned classes for the current semester.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($classes as $class)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col transition hover:shadow-md">
                <div class="p-6 flex-grow">
                    <div class="flex justify-between items-start mb-4">
                        <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-600/20">{{ $class['code'] }}</span>
                        <span class="text-sm font-medium text-gray-500">{{ $class['room'] }}</span>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-2 leading-tight">{{ $class['name'] }}</h4>
                    <p class="text-sm text-gray-500 mb-6 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        {{ $class['schedule'] }}
                    </p>
                    
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                        <div>
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Students</p>
                            <p class="text-xl font-bold text-gray-900">{{ $class['students'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Avg Attendance</p>
                            <p class="text-xl font-bold {{ $class['attendance'] >= 90 ? 'text-green-600' : 'text-yellow-600' }}">{{ $class['attendance'] }}%</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <button class="w-full text-center text-sm font-semibold text-indigo-600 hover:text-indigo-900">
                        Manage Class &rarr;
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>