<div class="space-y-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-navy">Reporting System</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">Generate, save, and schedule system reports.</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-surface rounded-3xl shadow-sm border border-gray-100 overflow-hidden p-1 inline-flex max-w-md w-full">
        <button wire:click="$set('activeTab', 'generate')" class="flex-1 py-3 px-6 text-center font-bold text-sm rounded-2xl transition-all {{ $activeTab === 'generate' ? 'bg-brand text-white shadow-md' : 'text-gray-500 hover:text-navy hover:bg-gray-50' }}">
            Generate Report
        </button>
        <button wire:click="$set('activeTab', 'saved')" class="flex-1 py-3 px-6 text-center font-bold text-sm rounded-2xl transition-all {{ $activeTab === 'saved' ? 'bg-brand text-white shadow-md' : 'text-gray-500 hover:text-navy hover:bg-gray-50' }}">
            Saved & Scheduled
        </button>
    </div>

    @if($activeTab === 'generate')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left sidebar config -->
            <div class="lg:col-span-1 bg-surface p-8 rounded-3xl shadow-sm border border-gray-100 h-fit">
                <h2 class="text-xl font-bold text-navy mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Configuration
                </h2>
                
                <div class="space-y-4">
                    <!-- Report Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Report Type</label>
                        <select wire:model.live="reportType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand text-sm">
                            <option value="user_activity">User Activity Report</option>
                            <option value="transaction_summary">Transaction Summary</option>
                            <option value="audit_trail">Audit Trail</option>
                            <option value="system_usage">System Usage Statistics</option>
                            <option value="custom">Custom Report Builder</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Date Range</label>
                        <select wire:model.live="dateRange" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand text-sm">
                            <option value="today">Today</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month">This Month</option>
                            <option value="this_year">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>

                    @if($dateRange === 'custom')
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label>
                                <input type="date" wire:model="customStartDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-brand">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">End Date</label>
                                <input type="date" wire:model="customEndDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-brand">
                            </div>
                        </div>
                    @endif

                    <!-- Additional Filters -->
                    @if($reportType === 'user_activity')
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Filter Role</label>
                            <select wire:model="filterRole" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-brand text-sm">
                                <option value="">All Roles</option>
                                <option value="admin">Admin</option>
                                <option value="faculty">Faculty</option>
                                <option value="student">Student</option>
                            </select>
                        </div>
                    @endif

                    @if($reportType === 'transaction_summary' || $reportType === 'custom')
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Status Category</label>
                            <select wire:model="filterCategory" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-brand text-sm">
                                <option value="">All Statuses</option>
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="absent">Absent</option>
                                <option value="excused">Excused</option>
                            </select>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="pt-6 border-t border-gray-100 space-y-3">
                        <button wire:click="generateReport('pdf')" class="w-full flex items-center justify-center gap-2 bg-navy text-white py-3 px-4 rounded-2xl font-bold hover:bg-navy-light transition-all shadow-sm hover:shadow-md">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export to PDF
                        </button>
                        
                        <button wire:click="generateReport('excel')" class="w-full flex items-center justify-center gap-2 bg-success text-white py-3 px-4 rounded-2xl font-bold hover:bg-green-600 transition-all shadow-sm hover:shadow-md">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export to Excel
                        </button>
                        
                        <button wire:click="openSaveModal" class="w-full flex items-center justify-center gap-2 bg-white border-2 border-gray-100 text-navy py-3 px-4 rounded-2xl font-bold hover:border-brand hover:text-brand transition-all">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            Save Configuration
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right content area -->
            <div class="lg:col-span-2 bg-surface p-8 rounded-3xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold text-navy mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Preview & Statistics
                </h2>
                
                @if($reportType === 'system_usage')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        @foreach($systemStats as $stat)
                            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 hover:shadow-md transition-shadow">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">{{ $stat['Metric'] }}</p>
                                <p class="text-3xl font-black text-navy">{{ $stat['Value'] }}</p>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="h-64 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-center">
                        <!-- Placeholder for Chart.js if implemented in UI -->
                        <div class="text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            <p class="font-bold">Visual Charts</p>
                            <p class="text-xs mt-1">Rendered dynamically when exported to PDF.</p>
                        </div>
                    </div>
                @else
                    @if($previewData && count($previewData['rows']) > 0)
                        <div class="overflow-x-auto rounded-xl border border-gray-100">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50/50 border-b border-gray-100">
                                        @foreach($previewData['headers'] as $header)
                                            <th class="px-4 py-3 text-left font-bold text-gray-700 whitespace-nowrap">{{ $header }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($previewData['rows'] as $row)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            @foreach($row as $cell)
                                                <td class="px-4 py-3 text-gray-600 truncate max-w-[200px]">{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 text-center">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-400 bg-gray-50 px-3 py-1.5 rounded-lg">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Showing preview of up to 5 rows. Export to view all data.
                            </span>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-full min-h-[300px] text-gray-400">
                            <svg class="w-16 h-16 mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="font-medium text-gray-500">No data found for the selected filters.</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endif

    @if($activeTab === 'saved')
        <div class="bg-surface rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            @if($savedReports->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50">
                                <th class="px-6 py-4 text-left font-semibold text-gray-700">Report Name</th>
                                <th class="px-6 py-4 text-left font-semibold text-gray-700">Type</th>
                                <th class="px-6 py-4 text-left font-semibold text-gray-700">Schedule</th>
                                <th class="px-6 py-4 text-left font-semibold text-gray-700">Next Run</th>
                                <th class="px-6 py-4 text-center font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($savedReports as $report)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <button wire:click="toggleFavorite({{ $report->id }})" class="text-{{ $report->is_favorite ? 'yellow-400' : 'gray-300' }} hover:text-yellow-500">
                                                <svg class="w-5 h-5" fill="{{ $report->is_favorite ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                </svg>
                                            </button>
                                            <span class="font-semibold text-gray-900">{{ $report->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700 capitalize">{{ str_replace('_', ' ', $report->type) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2 py-1 rounded text-xs font-semibold {{ $report->schedule_frequency === 'none' ? 'bg-gray-100 text-gray-600' : 'bg-brand/10 text-brand' }}">
                                            {{ ucfirst($report->schedule_frequency) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 text-xs">
                                        {{ $report->next_run_at ? $report->next_run_at->format('Y-m-d H:i') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button wire:click="runSavedReport({{ $report->id }}, 'pdf')" class="p-1.5 text-error hover:bg-error/10 rounded" title="Download PDF">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </button>
                                            <button wire:click="runSavedReport({{ $report->id }}, 'excel')" class="p-1.5 text-success hover:bg-success/10 rounded" title="Download Excel">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </button>
                                            <button wire:click="deleteReport({{ $report->id }})" wire:confirm="Are you sure you want to delete this configuration?" class="p-1.5 text-gray-400 hover:text-error hover:bg-error/10 rounded" title="Delete">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    <p class="font-medium">No saved configurations yet.</p>
                    <p class="text-sm mt-1">Generate a report and click "Save Configuration" to see it here.</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Save Modal -->
    @if($showSaveModal)
        <div class="fixed inset-0 bg-navy/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-surface rounded-3xl shadow-xl max-w-md w-full animate-fade-in-up">
                <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-navy">Save Report Configuration</h3>
                    <button wire:click="$set('showSaveModal', false)" class="text-gray-400 hover:text-navy transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-8 space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Configuration Name</label>
                        <input type="text" wire:model="reportName" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-brand focus:border-brand text-sm transition-all" placeholder="e.g. Monthly Faculty Attendance">
                        @error('reportName') <span class="text-error text-xs font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center justify-center">
                                <input type="checkbox" wire:model="isFavorite" class="peer w-5 h-5 text-brand rounded border-gray-300 focus:ring-brand cursor-pointer">
                            </div>
                            <span class="text-sm font-bold text-navy group-hover:text-brand transition-colors">Mark as Favorite</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <label class="block text-sm font-bold text-navy mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Schedule Automations
                        </label>
                        <p class="text-xs font-medium text-gray-500 mb-3">Have this report automatically generated and emailed.</p>
                        
                        <select wire:model.live="scheduleFrequency" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-brand text-sm mb-4 transition-all">
                            <option value="none">Do not schedule (Manual only)</option>
                            <option value="daily">Daily (8:00 AM)</option>
                            <option value="weekly">Weekly (Monday 8:00 AM)</option>
                            <option value="monthly">Monthly (1st of month 8:00 AM)</option>
                        </select>

                        @if($scheduleFrequency !== 'none')
                            <div class="animate-fade-in-up">
                                <label class="block text-xs font-bold text-navy mb-1">Email Delivery To</label>
                                <input type="email" wire:model="emailTo" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-brand text-sm transition-all" placeholder="admin@example.com">
                                @error('emailTo') <span class="text-error text-xs font-medium">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>
                </div>
                <div class="px-8 py-6 bg-gray-50 rounded-b-3xl flex gap-3">
                    <button wire:click="$set('showSaveModal', false)" class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl text-gray-700 font-bold hover:bg-white hover:border-gray-300 transition-colors">Cancel</button>
                    <button wire:click="saveReport" class="flex-1 px-4 py-3 bg-brand text-white rounded-xl font-bold hover:bg-brand-hover shadow-sm hover:shadow-md transition-all">Save Config</button>
                </div>
            </div>
        </div>
    @endif
</div>
