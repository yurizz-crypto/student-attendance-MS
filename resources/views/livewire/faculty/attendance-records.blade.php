<div>
    <!-- Toolbar -->
    <div class="mb-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex-1 flex gap-3 flex-wrap">
                <div class="relative flex-1 min-w-[200px]">
                    <input
                        type="text"
                        wire:model.live="searchTerm"
                        placeholder="Search by class name..."
                        class="w-full px-4 py-2 pl-10 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent"
                    >
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <select
                    wire:model.live="filterStatus"
                    class="px-4 py-2 rounded-lg border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent"
                >
                    <option value="">All Status</option>
                    <option value="open">Open</option>
                    <option value="closed">Closed</option>
                </select>

                <select
                    wire:model.live="filterClassId"
                    class="px-4 py-2 rounded-lg border border-gray-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent min-w-[150px]"
                >
                    <option value="">All Classes</option>
                    @foreach($facultyClasses as $class)
                        <option value="{{ $class->id }}">
                            {{ $class->name }} - {{ $class->subject->code ?? '' }}
                            @if(!$class->is_active) (Done: {{ $class->semester?->name }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <button
                wire:click="openSessionModal"
                class="w-full sm:w-auto px-4 py-2 bg-brand text-white rounded-lg font-semibold text-sm hover:bg-brand-hover transition-colors flex items-center gap-2 justify-center sm:justify-start whitespace-nowrap"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Session
            </button>
        </div>

        {{-- Second row: date range + export --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <label class="text-xs text-gray-500 font-medium">From</label>
                <input type="date" wire:model.live="dateFrom" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand">
                <label class="text-xs text-gray-500 font-medium">To</label>
                <input type="date" wire:model.live="dateTo" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand">
            </div>
            @if($searchTerm || $filterStatus || $filterClassId || $dateFrom || $dateTo)
                <button wire:click="$set('searchTerm',''); $set('filterStatus',''); $set('filterClassId',''); $set('dateFrom',''); $set('dateTo','')"
                    class="px-3 py-2 text-xs text-error border border-error/30 rounded-lg hover:bg-error/5 transition-colors font-medium">
                    Clear Filters
                </button>
            @endif
            <div class="ml-auto">
                <button wire:click="exportExcel"
                    class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export Excel
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
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Class</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Subject</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700 cursor-pointer hover:bg-gray-100 transition-colors" wire:click="sort('date')">
                            <div class="flex items-center gap-2">
                                Date
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.5a1 1 0 10-2 0v5.5z" />
                                </svg>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center font-semibold text-gray-700">Time</th>
                        <th class="px-6 py-4 text-center font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-center font-semibold text-gray-700">Records</th>
                        <th class="px-6 py-4 text-center font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900 flex items-center gap-2">
                                    {{ $session->classSection->name }}
                                    @if(!$session->classSection->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600">
                                            Done: {{ $session->classSection->semester?->name }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $session->classSection->subject->name }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $session->date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-center text-gray-700">
                                @if($session->start_time && $session->end_time)
                                    {{ $session->start_time }} - {{ $session->end_time }}
                                @elseif($session->start_time)
                                    {{ $session->start_time }}
                                @else
                                    <span class="text-gray-400">Not recorded</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center">
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                                        @if($session->status === 'open')
                                            bg-success/10 text-success
                                        @else
                                            bg-gray-100 text-gray-700
                                        @endif
                                    ">
                                        <span class="w-2 h-2 rounded-full mr-1.5 @if($session->status === 'open')bg-success @else bg-gray-400 @endif"></span>
                                        {{ ucfirst($session->status) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-700">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-info/10 text-info">
                                    {{ $session->records()->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    @if($session->attendance_code)
                                        <div class="flex items-center bg-gray-100 rounded-lg px-2 py-1 mr-2" title="Manual Attendance Code">
                                            <span class="text-xs font-mono font-bold text-gray-700 select-all">{{ $session->attendance_code }}</span>
                                            <button onclick="navigator.clipboard.writeText('{{ $session->attendance_code }}'); window.dispatchEvent(new CustomEvent('swal:success', {detail: {title: 'Copied!', message: 'Attendance code copied to clipboard'}}));" class="ml-2 text-gray-400 hover:text-brand transition-colors" title="Copy Code">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                    <button
                                        wire:click="openQrModal({{ $session->id }})"
                                        title="Show QR Code"
                                        class="p-2 text-purple-600 hover:bg-purple-600/10 rounded-lg transition-colors"
                                    >
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="openRecordModal({{ $session->id }})"
                                        title="Record attendance"
                                        class="p-2 text-info hover:bg-info/10 rounded-lg transition-colors"
                                    >
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    @if($session->status === 'open')
                                        <button
                                            wire:click="closeSession({{ $session->id }})"
                                            title="Close session"
                                            class="p-2 text-warning hover:bg-warning/10 rounded-lg transition-colors"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    @endif
                                    <button
                                        wire:click="deleteSession({{ $session->id }})"
                                        title="Delete session"
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
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z" />
                                </svg>
                                <p class="font-medium">No attendance sessions found</p>
                                <p class="text-sm mt-1">Create a new session to start recording attendance</p>
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
                Showing {{ $sessions->firstItem() ?? 0 }}–{{ $sessions->lastItem() ?? 0 }} of {{ $sessions->total() }} results
            </span>
        </div>
        @if($sessions->hasPages())
            <div>{{ $sessions->links() }}</div>
        @endif
    </div>

    <!-- Create Session Modal -->
    @if($showSessionModal)
        <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4" wire:click="closeSessionModal">
            <div class="bg-surface rounded-lg shadow-lg max-w-md w-full" @click.stop>
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-navy">Create New Session</h3>
                    <button wire:click="closeSessionModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="createSession" class="p-6 space-y-4">
                    <!-- Class -->
                    <div>
                        <label for="session-class" class="block text-sm font-semibold text-navy mb-2">
                            Select Class <span class="text-error ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <select
                            id="session-class"
                            wire:model.blur="classId"
                            aria-required="true"
                            aria-describedby="session-class-error"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('classId'), 'border-gray-200' => !$errors->has('classId')])
                        >
                            <option value="">Select a class</option>
                            @foreach($activeFacultyClasses as $class)
                                <option value="{{ $class->id }}">{{ $class->name }} - {{ $class->subject->name }}</option>
                            @endforeach
                        </select>
                        @error('classId')
                            <p id="session-class-error" class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div>
                        <label for="session-date" class="block text-sm font-semibold text-navy mb-2">
                            Date <span class="text-error ml-0.5" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="session-date"
                            type="date"
                            wire:model.blur="attendanceDate"
                            aria-required="true"
                            aria-describedby="session-date-error"
                            @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('attendanceDate'), 'border-gray-200' => !$errors->has('attendanceDate')])
                        >
                        @error('attendanceDate')
                            <p id="session-date-error" class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Time Frame -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="session-start" class="block text-sm font-semibold text-navy mb-2">
                                Start Time <span class="text-error ml-0.5" aria-hidden="true">*</span>
                            </label>
                            <input
                                id="session-start"
                                type="time"
                                wire:model="startTime"
                                aria-required="true"
                                @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('startTime'), 'border-gray-200' => !$errors->has('startTime')])
                            >
                            @error('startTime')
                                <p class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div>
                            <label for="session-end" class="block text-sm font-semibold text-navy mb-2">
                                End Time <span class="text-error ml-0.5" aria-hidden="true">*</span>
                            </label>
                            <input
                                id="session-end"
                                type="time"
                                wire:model="endTime"
                                aria-required="true"
                                @class(['w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-brand transition-colors', 'border-error bg-error/5' => $errors->has('endTime'), 'border-gray-200' => !$errors->has('endTime')])
                            >
                            @error('endTime')
                                <p class="text-error text-xs mt-1 flex items-center gap-1" role="alert">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <p class="text-xs text-gray-400"><span class="text-error">*</span> Required fields</p>

                    <!-- Submit Button -->
                    <div class="flex flex-col sm:flex-row gap-2 pt-2">
                        <button
                            type="button"
                            wire:click="closeSessionModal"
                            class="w-full sm:flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg font-semibold hover:bg-gray-200 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="createSession"
                            class="w-full sm:flex-1 px-4 py-2 bg-brand text-white rounded-lg font-semibold hover:bg-brand-hover transition-colors flex items-center justify-center gap-2 disabled:opacity-70"
                        >
                            <svg wire:loading wire:target="createSession" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span wire:loading.remove wire:target="createSession">Create</span>
                            <span wire:loading wire:target="createSession">Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Record Attendance Modal -->
    @if($showRecordModal && $selectedSessionId)
        <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4" wire:click="closeRecordModal">
            <div class="bg-surface rounded-lg shadow-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-surface z-10">
                    <h3 class="text-lg font-bold text-navy">Record Attendance</h3>
                    <button wire:click="closeRecordModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    @if(count($studentAttendance) > 0)
                        <div class="space-y-4">
                            @foreach($studentAttendance as $index => $record)
                                <div class="border border-gray-200 rounded-lg p-4 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-semibold text-gray-900 flex items-center gap-2">
                                                {{ $record['student_name'] }}
                                                @if(isset($record['student_status']) && $record['student_status'] !== 'active')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $record['student_status'] === 'suspended' ? 'bg-error/10 text-error' : 'bg-gray-200 text-gray-700' }}">
                                                        {{ $record['student_status'] }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $record['student_identity'] }}</div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <!-- Status -->
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                                            <select
                                                wire:change="updateAttendanceStatus({{ $index }}, $event.target.value)"
                                                value="{{ $record['status'] }}"
                                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand"
                                            >
                                                <option value="present" @if($record['status'] === 'present') selected @endif>Present</option>
                                                <option value="absent" @if($record['status'] === 'absent') selected @endif>Absent</option>
                                                <option value="late" @if($record['status'] === 'late') selected @endif>Late</option>
                                                <option value="excused" @if($record['status'] === 'excused') selected @endif>Excused</option>
                                            </select>
                                        </div>

                                        <!-- Remarks -->
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Remarks</label>
                                            <input
                                                type="text"
                                                wire:change="updateAttendanceRemark({{ $index }}, $event.target.value)"
                                                value="{{ $record['remarks'] }}"
                                                placeholder="Optional remarks"
                                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand"
                                            >
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2 mt-6 border-t border-gray-200 pt-4">
                            <button
                                wire:click="closeRecordModal"
                                class="w-full sm:flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg font-semibold hover:bg-gray-200 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                wire:click="saveAttendance"
                                class="w-full sm:flex-1 px-4 py-2 bg-brand text-white rounded-lg font-semibold hover:bg-brand-hover transition-colors"
                            >
                                Save Attendance
                            </button>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>No students enrolled in this class yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- QR Code Modal -->
    @if($showQrModal && $sessionQrCode)
        <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4" wire:click="closeQrModal">
            <div class="bg-surface rounded-lg shadow-lg max-w-md w-full" @click.stop>
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-navy">Attendance QR Code</h3>
                    <button wire:click="closeQrModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div class="flex justify-center bg-white p-4 rounded-lg border border-gray-200">
                        {!! $sessionQrCode !!}
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-3">
                            Students can scan this QR code to mark themselves present in this attendance session.
                        </p>
                        
                        @php
                            $currentSession = \App\Models\AttendanceSession::find($selectedSessionId);
                        @endphp
                        @if($currentSession && $currentSession->attendance_code)
                        <div class="inline-flex flex-col items-center p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <span class="text-xs text-gray-500 font-medium mb-1">Or use manual code:</span>
                            <div class="flex items-center gap-2">
                                <span class="text-xl font-mono font-bold text-navy tracking-wider select-all">{{ $currentSession->attendance_code }}</span>
                                <button onclick="navigator.clipboard.writeText('{{ $currentSession->attendance_code }}'); window.dispatchEvent(new CustomEvent('swal:success', {detail: {title: 'Copied!', message: 'Attendance code copied to clipboard'}}));" class="p-1.5 text-gray-400 hover:text-brand bg-white rounded-lg border border-gray-200 hover:border-brand shadow-sm transition-all" title="Copy Code">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="button"
                            onclick="window.print()"
                            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg font-semibold hover:bg-gray-200 transition-colors flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print
                        </button>
                        <button
                            wire:click="closeQrModal"
                            class="flex-1 px-4 py-2 bg-brand text-white rounded-lg font-semibold hover:bg-brand-hover transition-colors"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
