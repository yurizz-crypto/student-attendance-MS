<?php
use Livewire\Component;
use App\Models\ClassSection;

new class extends Component {
    public ClassSection $classSection;
    public $search = '';

    public function with(): array
    {
        // Start with all enrolled students
        $enrollments = $this->classSection->enrollments;

        // Allow real-time Livewire searching
        if (!empty($this->search)) {
            $enrollments = $enrollments->filter(function ($enrollment) {
                return stripos($enrollment->student->full_name, $this->search) !== false ||
                       stripos($enrollment->student->identity_id, $this->search) !== false;
            });
        }

        return [
            'enrollments' => $enrollments
        ];
    }
}; ?>

<form method="POST" action="{{ route('faculty.attendance.store', $classSection->id) }}" class="space-y-6">
    @csrf

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
                <x-input-label for="attendance_date" :value="__('Attendance Date')" />
                {{-- Date input required by the backend --}}
                <x-text-input id="attendance_date" name="attendance_date" type="date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required class="block w-full" />
                <x-input-error :messages="$errors->get('attendance_date')" class="mt-2" />
            </div>

            <x-primary-button type="submit" class="w-full lg:w-auto h-[46px] flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Save Attendance
            </x-primary-button>
        </div>
    </div>

    <div class="bg-surface rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID Number</th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-3 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Remarks (Optional)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($enrollments as $index => $enrollment)
                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            
                            {{-- We need a hidden field to pass the student's ID to the backend array --}}
                            <input type="hidden" name="students[{{ $index }}][student_id]" value="{{ $enrollment->student_id }}">

                            <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-brand/10 text-brand flex items-center justify-center font-bold text-xs uppercase">
                                        {{ substr($enrollment->student->first_name, 0, 1) }}{{ substr($enrollment->student->last_name, 0, 1) }}
                                    </div>
                                    <div class="text-sm font-bold text-navy group-hover:text-brand transition-colors">
                                        {{ $enrollment->student->full_name }}
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-gray-500">
                                {{ $enrollment->student->identity_id }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4">
                                {{-- Status Dropdown mapped to validation rules --}}
                                <select name="students[{{ $index }}][status]" class="block w-full rounded-xl border-gray-300 bg-gray-50 text-navy py-2 px-3 focus:border-brand focus:ring-brand shadow-sm sm:text-sm font-bold">
                                    <option value="present">Present</option>
                                    <option value="late">Late</option>
                                    <option value="absent">Absent</option>
                                    <option value="excused">Excused</option>
                                </select>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4">
                                <x-text-input name="students[{{ $index }}][remarks]" type="text" class="block w-full py-1.5 text-sm" placeholder="Add note..." />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center">
                                <p class="text-sm font-medium text-gray-400">No students are currently enrolled in this class.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</form>