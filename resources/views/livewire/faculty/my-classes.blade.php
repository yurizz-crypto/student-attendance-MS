<div>
    <!-- Toolbar -->
    <div class="mb-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex-1 flex gap-3 flex-wrap">
                <div class="relative flex-1 min-w-[200px]">
                    <input
                        type="text"
                        wire:model.live="searchTerm"
                        placeholder="Search classes or subjects..."
                        class="w-full px-4 py-2 pl-10 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent"
                    >
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <select
                    wire:model.live="filterSemesterId"
                    class="px-4 py-2 rounded-lg border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent"
                >
                    <option value="">All Semesters</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                <button
                    wire:click="openManageSemesters"
                    class="w-full sm:w-auto px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-50 transition-colors flex items-center gap-2 justify-center sm:justify-start whitespace-nowrap"
                >
                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Manage Semesters
                </button>
                <button
                    wire:click="openCreateModal"
                    class="w-full sm:w-auto px-4 py-2 bg-brand text-white rounded-lg font-semibold text-sm hover:bg-brand-hover transition-colors flex items-center gap-2 justify-center sm:justify-start whitespace-nowrap"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Class
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-surface rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/50">
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors" wire:click="sort('name')">
                            <div class="flex items-center gap-2">
                                Class Name
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.5a1 1 0 10-2 0v5.5z" />
                                </svg>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Subject</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Semester</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Schedule</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 text-center">Students</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors" wire:click="sort('created_at')">
                            Created
                        </th>
                        <th class="px-6 py-4 text-center font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($classes as $class)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-brand/10 text-brand flex items-center justify-center font-bold text-sm">
                                        {{ strtoupper(substr($class->name, 0, 1)) }}
                                    </div>
                                    <div class="font-semibold text-gray-900">{{ $class->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $class->subject?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-700">
                                <span class="inline-flex px-2.5 py-1 rounded-md text-xs font-semibold {{ $class->is_active ? 'bg-success/10 text-success' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $class->semester?->name ?? 'N/A' }}
                                    @if(!$class->is_active) (Done) @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-700 text-xs">
                                @if($class->schedule_details)
                                    {{ $class->schedule_details }}
                                @else
                                    <span class="text-gray-400">Not set</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-info/10 text-info">
                                    {{ $class->enrollments()->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $class->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        wire:click="openEnrollModal({{ $class->id }})"
                                        title="Manage students"
                                        class="p-2 text-success hover:bg-success/10 rounded-lg transition-colors"
                                    >
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 16a6 6 0 016 6v2h5v-2a6 6 0 00-9-5.697" />
                                        </svg>
                                    </button>
                                    <a
                                        href="{{ route('faculty.attendance.show', $class->id) }}"
                                        title="View attendance"
                                        class="p-2 text-warning hover:bg-warning/10 rounded-lg transition-colors"
                                    >
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 12a5 5 0 1010 0A5 5 0 007 12z" />
                                        </svg>
                                    </a>
                                    <button
                                        wire:click="openEditModal({{ $class->id }})"
                                        title="Edit class"
                                        class="p-2 text-info hover:bg-info/10 rounded-lg transition-colors"
                                    >
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="openDeleteModal({{ $class->id }})"
                                        title="Delete class"
                                        class="p-2 text-error hover:bg-error/10 rounded-lg transition-colors"
                                    >
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z" />
                                </svg>
                                <p class="font-medium">No classes found</p>
                                <p class="text-sm mt-1">Create your first class to get started</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($classes->hasPages())
        <div class="mt-6">
            {{ $classes->links() }}
        </div>
    @endif

    <!-- Create/Edit Class Modal -->
    @if($showCreateModal || $showEditModal)
        <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4" wire:click="closeClassModal">
            <div class="bg-surface rounded-lg shadow-lg max-w-md w-full" @click.stop>
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-navy">
                        @if($showCreateModal)
                            Create New Class
                        @else
                            Edit Class
                        @endif
                    </h3>
                    <button wire:click="closeClassModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="@if($showCreateModal) store @else update @endif" class="p-6 space-y-4">
                    @error('general')
                        <div class="bg-error/10 border border-error/20 text-error px-4 py-3 rounded-lg text-sm font-medium">
                            {{ $message }}
                        </div>
                    @enderror

                    <!-- Subject -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Subject</label>
                        <input
                            type="text"
                            wire:model="subjectName"
                            class="w-full px-4 py-2 rounded-lg border @error('subjectName') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="e.g., Data Structures"
                        >
                        @error('subjectName')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Class Name -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Class Name</label>
                        <input
                            type="text"
                            wire:model="className"
                            class="w-full px-4 py-2 rounded-lg border @error('className') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="e.g., Section A"
                        >
                        @error('className')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Schedule Details -->
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Schedule Details</label>
                        <input
                            type="text"
                            wire:model="scheduleDetails"
                            class="w-full px-4 py-2 rounded-lg border @error('scheduleDetails') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                            placeholder="e.g., Mon, Wed, Fri 10:00 AM"
                        >
                        @error('scheduleDetails')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex flex-col sm:flex-row gap-2 pt-4">
                        <button
                            type="button"
                            wire:click="closeClassModal"
                            class="w-full sm:flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg font-semibold hover:bg-gray-200 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="w-full sm:flex-1 px-4 py-2 bg-brand text-white rounded-lg font-semibold hover:bg-brand-hover transition-colors"
                        >
                            @if($showCreateModal)
                                Create
                            @else
                                Update
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Class Modal -->
    @if($showDeleteModal && $deleteConfirmClassId)
        <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4" wire:click="closeDeleteModal">
            <div class="bg-surface rounded-lg shadow-lg max-w-sm w-full" @click.stop>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-navy">Delete Class</h3>
                </div>

                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-lg bg-error/10 text-error flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2m0-2v-2m0 0h2m-2 0h-2" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Are you sure?</p>
                            <p class="text-sm text-gray-500">This action cannot be undone. All enrollments will be removed.</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2">
                        <button
                            wire:click="closeDeleteModal"
                            class="w-full sm:flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg font-semibold hover:bg-gray-200 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            wire:click="delete"
                            class="w-full sm:flex-1 px-4 py-2 bg-error text-white rounded-lg font-semibold hover:bg-error-hover transition-colors"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Enrollment Modal -->
    @if($showEnrollModal && $selectedClassId)
        <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4" wire:click="closeEnrollModal">
            <div class="bg-surface rounded-lg shadow-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-surface z-10">
                    <h3 class="text-lg font-bold text-navy">Manage Students</h3>
                    <button wire:click="closeEnrollModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Search & Enroll Section -->
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-900">Add Students</h4>
                        
                        <div class="relative">
                            <input
                                type="text"
                                wire:model.live="studentSearchTerm"
                                placeholder="Search students by name, ID, or email..."
                                class="w-full px-4 py-2 pl-10 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent"
                            >
                            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <!-- Available Students List -->
                        @if(count($availableStudents) > 0)
                            <div class="border border-gray-200 rounded-lg max-h-48 overflow-y-auto">
                                @foreach($availableStudents as $student)
                                    <button
                                        wire:click="selectStudent({{ $student->id }})"
                                        type="button"
                                        class="w-full px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 text-left transition-colors flex items-center justify-between"
                                    >
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $student->identity_id }} • {{ $student->email }}</div>
                                        </div>
                                        <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        @elseif($studentSearchTerm)
                            <div class="text-center py-6 text-gray-500">
                                <p class="text-sm">No students found matching your search</p>
                            </div>
                        @endif

                        <!-- Selected Student to Enroll -->
                        @if($studentToEnroll)
                            <div class="bg-brand/5 border border-brand/20 rounded-lg p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $studentToEnroll->first_name }} {{ $studentToEnroll->last_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $studentToEnroll->identity_id }} • {{ $studentToEnroll->email }}</div>
                                    </div>
                                    <button
                                        wire:click="$set('studentToEnroll', null)"
                                        type="button"
                                        class="text-gray-400 hover:text-gray-600"
                                    >
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                @error('enrollment')
                                    <p class="text-error text-xs">{{ $message }}</p>
                                @enderror

                                <button
                                    wire:click="enrollStudent"
                                    type="button"
                                    class="w-full px-4 py-2 bg-brand text-white rounded-lg font-semibold hover:bg-brand-hover transition-colors"
                                >
                                    Enroll Student
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Enrolled Students Section -->
                    <div class="space-y-4 border-t border-gray-200 pt-6">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-gray-900">Enrolled Students ({{ count($enrolledStudents) }})</h4>
                        </div>

                        @if(count($enrolledStudents) > 0)
                            <div class="space-y-2">
                                @foreach($enrolledStudents as $enrollment)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $enrollment['student']['first_name'] }} {{ $enrollment['student']['last_name'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $enrollment['student']['identity_id'] }}</div>
                                        </div>
                                        <button
                                            wire:click="unenrollStudent({{ $enrollment['student_id'] }})"
                                            type="button"
                                            class="p-2 text-error hover:bg-error/10 rounded-lg transition-colors"
                                            title="Remove student"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-gray-200">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="font-medium">No students enrolled yet</p>
                                <p class="text-sm mt-1">Add students to get started</p>
                            </div>
                        @endif
                        <!-- Close Button -->
                        <div class="flex gap-2 border-t border-gray-200 pt-4 mt-4">
                            <button
                                wire:click="closeEnrollModal"
                                class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg font-semibold hover:bg-gray-200 transition-colors"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Manage Semesters Modal -->
    @if($showManageSemestersModal)
        <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4" wire:click="closeManageSemesters">
            <div class="bg-surface rounded-lg shadow-lg max-w-lg w-full max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-surface z-10">
                    <h3 class="text-lg font-bold text-navy">Manage Semesters</h3>
                    <button wire:click="closeManageSemesters" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <p class="text-sm text-gray-500 mb-4">Marking a semester as done will hide its classes from active views like the dashboard and new attendance sessions.</p>
                    
                    @if(count($semesters) > 0)
                        <div class="space-y-3">
                            @foreach($semesters as $semester)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl {{ $semester->is_active ? 'bg-white' : 'bg-gray-50' }}">
                                    <div>
                                        <div class="font-bold text-navy flex items-center gap-2">
                                            {{ $semester->name }}
                                            @if($semester->is_active)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-success/10 text-success">Active</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Done</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">Created {{ $semester->created_at->format('M d, Y') }}</div>
                                    </div>
                                    @if($semester->is_active)
                                        <button
                                            wire:click="markSemesterDone({{ $semester->id }})"
                                            wire:confirm="Are you sure you want to mark this semester as done? You won't be able to undo this or create new classes for it."
                                            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition-colors"
                                        >
                                            Mark as Done
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p>You haven't created any semesters yet.</p>
                        </div>
                    @endif

                    @if($isCreatingSemester)
                        <div class="mt-4 p-4 border border-gray-200 rounded-xl bg-gray-50 space-y-3">
                            <div>
                                <label class="block text-sm font-semibold text-navy mb-1">Semester Name</label>
                                <input
                                    type="text"
                                    wire:model="newSemesterName"
                                    class="w-full px-3 py-2 rounded-lg border @error('newSemesterName') border-error @else border-gray-200 @enderror text-sm focus:outline-none focus:ring-2 focus:ring-brand"
                                    placeholder="e.g., 1st Semester 2026-2027"
                                >
                                @error('newSemesterName')
                                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex gap-2 justify-end">
                                <button
                                    wire:click="toggleCreateSemester"
                                    class="px-3 py-1.5 text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg text-sm font-semibold transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    wire:click="createSemester"
                                    class="px-3 py-1.5 bg-brand text-white hover:bg-brand-hover rounded-lg text-sm font-semibold transition-colors"
                                >
                                    Save
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-gray-200 mt-6 flex justify-between items-center">
                        <button
                            wire:click="toggleCreateSemester"
                            class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors flex items-center gap-2"
                        >
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Semester
                        </button>
                        <button
                            wire:click="closeManageSemesters"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition-colors"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
