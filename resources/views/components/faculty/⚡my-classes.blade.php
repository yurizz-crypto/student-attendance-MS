<?php
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public function with(): array
    {
        // Fetch classes taught by the logged-in user, eager load the subject, and count real students
        return [
            'classes' => Auth::user()
                ->classesTaught()
                ->with('subject')
                ->withCount('enrollments')
                ->get()
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
        @forelse($classes as $class)
            <div class="bg-surface rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group flex flex-col relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-brand"></div>
                
                <div class="p-6 sm:p-8 flex-1">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-3 py-1 rounded-lg bg-brand/10 text-brand text-xs font-bold uppercase tracking-wider">
                            {{ $class->subject->code }}
                        </span>
                        <div class="flex items-center gap-1.5 text-xs font-bold text-success">
                            <span class="w-2 h-2 rounded-full bg-success"></span>
                            ACTIVE
                        </div>
                    </div>

                    <h4 class="text-xl font-extrabold text-navy leading-tight group-hover:text-brand transition-colors mb-2">
                        {{ $class->name }}
                    </h4>
                    
                    <div class="space-y-3 mt-6">
                        <div class="flex items-center gap-3 text-gray-500">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:text-brand/70 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <span class="text-sm font-semibold tracking-tight">{{ $class->schedule_details ?? 'TBA' }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-8 mt-8 border-t border-gray-50">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Enrolled</p>
                            <p class="text-xl font-black text-navy">{{ $class->enrollments_count }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Attendance</p>
                            <p class="text-xl font-black text-gray-300">--%</p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between group-hover:bg-gray-50 transition-colors">
                    {{-- Dynamically link to the specific class attendance page --}}
                    <a href="{{ route('faculty.attendance.show', $class->id) }}" class="text-sm font-bold text-navy hover:text-brand flex items-center gap-2 transition-colors">
                        Manage Class
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-surface p-12 rounded-3xl border border-gray-100 text-center">
                <p class="text-gray-500 font-medium">You do not have any active classes assigned.</p>
            </div>
        @endforelse
    </div>
</div>