<?php
use Livewire\Component;

new class extends Component {
    public function with(): array
    {
        return [
            'classes' => [
                ['id' => 1, 'code' => 'IT 311', 'name' => 'Web Systems and Technologies', 'schedule' => 'MWF 10:00 AM - 11:00 AM', 'students' => 45, 'attendance' => 92, 'room' => 'Lab 3C', 'status' => 'active'],
                ['id' => 2, 'code' => 'CS 102', 'name' => 'Data Structures', 'schedule' => 'TTh 1:00 PM - 2:30 PM', 'students' => 38, 'attendance' => 88, 'room' => 'Lab 2A', 'status' => 'active'],
                ['id' => 3, 'code' => 'IT 312', 'name' => 'Software Engineering', 'schedule' => 'MWF 2:00 PM - 3:00 PM', 'students' => 42, 'attendance' => 95, 'room' => 'Room 404', 'status' => 'active'],
            ]
        ];
    }
}; ?>

<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-navy">Active Courses</h3>
            <p class="text-sm text-gray-500 font-medium mt-1">You are currently assigned to {{ count($classes) }} active sections.</p>
        </div>
        <x-secondary-button class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25" /></svg>
            Sort by Schedule
        </x-secondary-button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($classes as $class)
            <div class="bg-surface rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group flex flex-col relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-brand"></div>
                
                <div class="p-6 sm:p-8 flex-1">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-3 py-1 rounded-lg bg-brand/10 text-brand text-xs font-bold uppercase tracking-wider">
                            {{ $class['code'] }}
                        </span>
                        <div class="flex items-center gap-1.5 text-xs font-bold text-success">
                            <span class="w-2 h-2 rounded-full bg-success"></span>
                            {{ strtoupper($class['status']) }}
                        </div>
                    </div>

                    <h4 class="text-xl font-extrabold text-navy leading-tight group-hover:text-brand transition-colors mb-2">
                        {{ $class['name'] }}
                    </h4>
                    
                    <div class="space-y-3 mt-6">
                        <div class="flex items-center gap-3 text-gray-500">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:text-brand/70 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <span class="text-sm font-semibold tracking-tight">{{ $class['schedule'] }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-500">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:text-brand/70 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                            </div>
                            <span class="text-sm font-semibold tracking-tight">{{ $class['room'] }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-8 mt-8 border-t border-gray-50">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Enrolled</p>
                            <p class="text-xl font-black text-navy">{{ $class['students'] }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Attendance</p>
                            <p class="text-xl font-black {{ $class['attendance'] >= 90 ? 'text-success' : 'text-warning' }}">
                                {{ $class['attendance'] }}%
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between group-hover:bg-gray-50 transition-colors">
                    <button class="text-sm font-bold text-navy hover:text-brand flex items-center gap-2 transition-colors">
                        Manage Class
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                    </button>
                    <button class="p-2 rounded-lg text-gray-400 hover:text-navy hover:bg-white transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>