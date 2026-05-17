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
                    wire:click="openImportModal"
                    class="w-full sm:w-auto px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-50 transition-colors flex items-center gap-2 justify-center sm:justify-start whitespace-nowrap"
                >
                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import Classes
                </button>
                <button wire:click="exportExcel"
                    class="w-full sm:w-auto px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-50 transition-colors flex items-center gap-2 justify-center sm:justify-start whitespace-nowrap">
                    <svg class="w-5 h-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export Excel
                </button>
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

    <!-- Pagination & per-page -->
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <label class="text-sm text-gray-500">Rows per page:</label>
            <select wire:model.live="perPage" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="text-sm text-gray-500">
                Showing {{ $classes->firstItem() ?? 0 }}–{{ $classes->lastItem() ?? 0 }} of {{ $classes->total() }} results
            </span>
        </div>
        @if($classes->hasPages())
            <div>{{ $classes->links() }}</div>
        @endif
    </div>

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

                <form
                    wire:submit="@if($showCreateModal) store @else update @endif"
                    class="p-6 space-y-4"
                    x-data="{
                        isDirty: false,
                        draftKey: 'draft_class_form',
                        saveDraft() {
                            const data = { subjectName: $wire.subjectName, className: $wire.className, scheduleDetails: $wire.scheduleDetails };
                            localStorage.setItem(this.draftKey, JSON.stringify(data));
                            this.isDirty = true;
                        },
                        loadDraft() {
                            if (!$wire.showCreateModal) return;
                            try {
                                const saved = localStorage.getItem(this.draftKey);
                                if (!saved) return;
                                const data = JSON.parse(saved);
                                if (data.subjectName) $wire.set('subjectName', data.subjectName);
                                if (data.className) $wire.set('className', data.className);
                                if (data.scheduleDetails) $wire.set('scheduleDetails', data.scheduleDetails);
                                this.isDirty = true;
                            } catch(e) {}
                        },
                        clearDraft() { localStorage.removeItem(this.draftKey); this.isDirty = false; },
                    }"
                    x-init="loadDraft()"
                    @input.debounce.800ms="saveDraft()"
                >
                    {{-- Lock conflict banner (edit mode only) --}}
                    @if($showEditModal && $lockConflict)
                        <div class="flex items-start gap-3 p-4 rounded-lg bg-error/10 border border-error/30" role="alert">
                            <svg class="w-5 h-5 text-error flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="font-semibold text-error text-sm">Edit Conflict Detected</p>
                                <p class="text-xs text-gray-600 mt-1">This class was modified by another user. Please close and reopen the form to load the latest data.</p>
                            </div>
                        </div>
                    @endif
                    @error('general')
                        <div class="bg-error/10 border border-error/20 text-error px-4 py-3 rounded-lg text-sm font-medium" role="alert">
                            {{ $message }}
                        </div>
                    @enderror

                    <!-- Subject -->
                    <div>
                        <label for="class-subject" class="block text-sm font-semibold text-navy mb-2">
                            Subject <span class="text-error ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="class-subject"
                            type="text"
                            wire:model.blur="subjectName"
                            aria-required="true"
                            aria-describedby="class-subject-error"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('subjectName'), 'border-gray-200' => !$errors->has('subjectName')])
                            placeholder="e.g., Data Structures"
                        >
                        @error('subjectName')
                            <p id="class-subject-error" class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Class Name -->
                    <div>
                        <label for="class-name" class="block text-sm font-semibold text-navy mb-2">
                            Class Name <span class="text-error ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="class-name"
                            type="text"
                            wire:model.blur="className"
                            aria-required="true"
                            aria-describedby="class-name-error"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('className'), 'border-gray-200' => !$errors->has('className')])
                            placeholder="e.g., Section A"
                        >
                        @error('className')
                            <p id="class-name-error" class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Schedule Details -->
                    <div>
                        <label for="class-schedule" class="block text-sm font-semibold text-navy mb-2">
                            Schedule Details
                            <span class="text-xs font-normal text-gray-400">(Optional)</span>
                        </label>
                        <input
                            id="class-schedule"
                            type="text"
                            wire:model="scheduleDetails"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('scheduleDetails'), 'border-gray-200' => !$errors->has('scheduleDetails')])
                            placeholder="e.g., Mon, Wed, Fri 10:00 AM"
                        >
                        @error('scheduleDetails')
                            <p class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <p class="text-xs text-gray-400"><span class="text-error">*</span> Required fields</p>

                    <!-- Draft indicator -->
                    <div x-show="isDirty" x-cloak class="flex items-center gap-1.5 text-xs text-warning">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="10"/></svg>
                        Draft auto-saved
                    </div>

                    <!-- Submit Button -->
                    <div class="flex flex-col sm:flex-row gap-2 pt-2">
                        <button
                            type="button"
                            wire:click="closeClassModal"
                            class="w-full sm:flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg font-semibold hover:bg-gray-200 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="store, update"
                            class="w-full sm:flex-1 px-4 py-2 bg-brand text-white rounded-lg font-semibold hover:bg-brand-hover transition-colors flex items-center justify-center gap-2 disabled:opacity-70"
                            x-on:click="clearDraft()"
                        >
                            <svg wire:loading wire:target="store, update" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            @if($showCreateModal)
                                <span wire:loading.remove wire:target="store">Create</span>
                                <span wire:loading wire:target="store">Creating...</span>
                            @else
                                <span wire:loading.remove wire:target="update">Update</span>
                                <span wire:loading wire:target="update">Saving...</span>
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

                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto rounded-full bg-error/10">
                        <svg class="w-6 h-6 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <div class="text-center">
                        <p class="font-semibold text-gray-800 mb-1">Delete &ldquo;{{ $cascadeInfo['name'] ?? 'this class' }}&rdquo;?</p>
                        <p class="text-sm text-gray-500">This class will be soft-deleted and can be restored by an admin.</p>
                    </div>

                    {{-- Cascade warning if there are enrollments --}}
                    @if(!empty($cascadeInfo) && $cascadeInfo['enrollments'] > 0)
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-warning/10 border border-warning/30">
                            <svg class="w-5 h-5 text-warning flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="text-sm">
                                <p class="font-semibold text-warning">Cascade Warning</p>
                                <p class="mt-0.5 text-gray-600">{{ $cascadeInfo['enrollments'] }} enrolled student(s) will lose access to this class.</p>
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row gap-2">
                        <button
                            wire:click="closeDeleteModal"
                            class="w-full sm:flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg font-semibold hover:bg-gray-200 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            wire:click="delete"
                            class="w-full sm:flex-1 px-4 py-2 bg-error text-white rounded-lg font-semibold hover:bg-red-700 transition-colors"
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
                                            <div class="font-semibold text-gray-900 flex items-center gap-2">
                                                {{ $student->first_name }} {{ $student->last_name }}
                                                @if($student->status !== 'active')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $student->status === 'suspended' ? 'bg-error/10 text-error' : 'bg-gray-200 text-gray-700' }}">
                                                        {{ $student->status }}
                                                    </span>
                                                @endif
                                            </div>
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
                                        <div class="font-semibold text-gray-900 flex items-center gap-2">
                                            {{ $studentToEnroll->first_name }} {{ $studentToEnroll->last_name }}
                                            @if($studentToEnroll->status !== 'active')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $studentToEnroll->status === 'suspended' ? 'bg-error/10 text-error' : 'bg-gray-200 text-gray-700' }}">
                                                    {{ $studentToEnroll->status }}
                                                </span>
                                            @endif
                                        </div>
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
                                            <div class="font-semibold text-gray-900 flex items-center gap-2">
                                                {{ $enrollment['student']['first_name'] }} {{ $enrollment['student']['last_name'] }}
                                                @if(isset($enrollment['student']['status']) && $enrollment['student']['status'] !== 'active')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $enrollment['student']['status'] === 'suspended' ? 'bg-error/10 text-error' : 'bg-gray-200 text-gray-700' }}">
                                                        {{ $enrollment['student']['status'] }}
                                                    </span>
                                                @endif
                                            </div>
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

    <!-- Import Classes Modal -->
    @if($showImportModal)
        <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4" wire:click="closeImportModal">
            <div class="bg-surface rounded-lg shadow-lg max-w-md w-full" @click.stop>
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-navy">Import Classes & Students (CSV)</h3>
                    <button wire:click="closeImportModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="importClasses" class="p-6 space-y-4">
                    <div class="bg-blue-50 text-blue-800 p-4 rounded-lg text-sm mb-4 border border-blue-100">
                        <p class="font-bold mb-1">CSV Format Required:</p>
                        <p class="font-mono text-xs mb-2 break-all">subject_name,class_name,schedule_details,student_identity_id</p>
                        <ul class="list-disc pl-4 space-y-1 text-xs">
                            <li>Includes header row</li>
                            <li>The class will be assigned to your <strong>Active Semester</strong></li>
                            <li>Students must already exist in the system</li>
                        </ul>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2">Upload CSV File</label>
                        <input
                            type="file"
                            wire:model="importFile"
                            accept=".csv"
                            class="w-full px-4 py-2 rounded-lg border @error('importFile') border-error @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-brand"
                        >
                        <div wire:loading wire:target="importFile" class="text-sm text-gray-500 mt-2">Uploading...</div>
                        @error('importFile')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button
                            type="button"
                            wire:click="closeImportModal"
                            class="flex-1 px-4 py-2 rounded-lg border border-gray-200 text-navy font-semibold hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="flex-1 px-4 py-2 rounded-lg bg-brand text-white font-semibold hover:bg-brand-hover transition-colors flex items-center justify-center gap-2"
                            wire:loading.attr="disabled"
                            wire:target="importClasses"
                        >
                            <span wire:loading.remove wire:target="importClasses">Import</span>
                            <span wire:loading wire:target="importClasses">Importing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
