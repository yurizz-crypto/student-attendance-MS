<div class="space-y-8" x-data="dashboardCharts({
    registrations: {{ json_encode($chartDataRegistrations) }},
    transactions: {{ json_encode($chartDataTransactions) }}
})">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-navy">Dashboard Overview</h2>
            <p class="text-sm text-gray-500">System performance and activity summary.</p>
        </div>
        <div class="flex items-center gap-3">
            <select wire:model.live="dateRange"
                class="rounded-lg border-gray-300 text-sm focus:ring-brand focus:border-brand">
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="year">This Year</option>
            </select>

            <button type="button" wire:click="refreshData"
                class="p-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200" title="Refresh Data">
                <svg wire:loading.class="animate-spin" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.users') }}"
            class="flex items-center gap-3 p-4 bg-brand/5 rounded-lg border border-brand/10 hover:bg-brand/10 transition-colors group">
            <div class="p-2 bg-brand text-white rounded-lg group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <span class="font-medium text-brand text-sm">Add User</span>
        </a>
        <a href="{{ route('admin.audit-logs') }}"
            class="flex items-center gap-3 p-4 bg-info/5 rounded-lg border border-info/10 hover:bg-info/10 transition-colors group">
            <div class="p-2 bg-info text-white rounded-lg group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <span class="font-medium text-info text-sm">View Logs</span>
        </a>
        <a href="{{ route('admin.backup-settings') }}"
            class="flex items-center gap-3 p-4 bg-success/5 rounded-lg border border-success/10 hover:bg-success/10 transition-colors group">
            <div class="p-2 bg-success text-white rounded-lg group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <span class="font-medium text-success-dark text-sm">Backup Settings</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Total Users</p>
                    <p class="text-3xl font-bold text-navy mt-2">{{ number_format($stats['total_users']) }}</p>
                </div>
                <div class="p-2 bg-brand/10 rounded-lg text-brand">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 20 20" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zm-7-4a6 6 0 110-12 6 6 0 010 12z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-6 relative overflow-hidden">
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Active Now</p>
                    <p class="text-3xl font-bold text-success mt-2">
                        <span class="inline-block w-3 h-3 bg-success rounded-full animate-pulse mr-2 mb-1"></span>
                        {{ number_format($systemHealth['active_now']) }}
                    </p>
                </div>
                <div class="p-2 text-black">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Avg Load Time</p>
                    <p class="text-3xl font-bold text-navy mt-2">{{ $systemHealth['avg_response_time'] }}</p>
                </div>
                <div class="p-2 bg-info/10 rounded-lg text-info">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Error Rate</p>
                    <p
                        class="text-3xl font-bold {{ (float) $systemHealth['error_rate'] > 1 ? 'text-error' : 'text-navy' }} mt-2">
                        {{ $systemHealth['error_rate'] }}</p>
                </div>
                <div class="p-2 bg-error/10 rounded-lg text-error">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">New Registrations</h3>
            <div class="relative h-64" wire:ignore>
                <canvas id="registrationsChart"></canvas>
            </div>
        </div>

        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">System Activity</h3>
            <div class="relative h-64" wire:ignore>
                <canvas id="transactionsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm p-6">
            <h3 class="font-bold text-navy mb-4">System Health</h3>

            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Storage Usage</span>
                        <span class="font-semibold text-gray-900">{{ $systemHealth['storage_usage'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-brand h-2 rounded-full" style="width: {{ $systemHealth['storage_usage'] }}">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-gray-100 rounded text-gray-500">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Database Size</p>
                            <p class="text-sm font-bold text-gray-900">{{ $systemHealth['database_size'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-gray-100 rounded text-gray-500">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Server Uptime</p>
                            <p class="text-sm font-bold text-gray-900">{{ $systemHealth['uptime'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="lg:col-span-2 bg-surface rounded-lg border border-gray-200 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-bold text-navy">Recent Live Activity</h3>
                <span class="flex h-3 w-3 relative">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-brand"></span>
                </span>
            </div>

            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentLogs as $log)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <span class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded-full bg-brand/10 text-brand flex items-center justify-center font-bold text-[10px]">
                                            {{ $log->user ? substr($log->user->first_name, 0, 1) : 'S' }}
                                        </div>
                                        <span
                                            class="font-medium text-gray-900">{{ $log->user?->first_name ?? 'System' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide
                                            {{ $log->action === 'deleted' || $log->status === 'failed' ? 'bg-error/10 text-error' : 'bg-gray-100 text-gray-600' }}
                                        ">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-gray-600 truncate max-w-[200px]" title="{{ $log->description }}">
                                    {{ $log->description }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">No recent activity found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-gray-200 bg-gray-50/50 text-center">
                <a href="{{ route('admin.audit-logs') }}"
                    class="text-xs font-semibold text-brand hover:text-brand-hover">
                    View All Audit Logs →
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardCharts', ({ registrations, transactions }) => ({
            regChart: null,
            transChart: null,

            init() {
                this.waitForChart();

                window.addEventListener('update-charts', (event) => {
                    const payload = event.detail;
                    if (payload) {
                        const newRegs = payload.registrations || registrations;
                        const newTrans = payload.transactions || transactions;
                        this.updateCharts(newRegs, newTrans);
                    }
                });
            },

            waitForChart(retries = 20) {
                if (typeof Chart === 'undefined') {
                    if (retries <= 0) { return; }
                    setTimeout(() => this.waitForChart(retries - 1), 100);
                    return;
                }
                this.initCharts();
            },

            initCharts() {
                const regCanvas = document.getElementById('registrationsChart');
                const transCanvas = document.getElementById('transactionsChart');
                if (!regCanvas || !transCanvas) { return; }
                const regCtx = regCanvas.getContext('2d');
                this.regChart = new Chart(regCtx, {
                    type: 'bar',
                    data: {
                        labels: registrations.labels,
                        datasets: [{
                            label: 'New Users',
                            data: registrations.data,
                            backgroundColor: '#4F46E5', // brand color
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                    }
                });

                const transCtx = transCanvas.getContext('2d');
                this.transChart = new Chart(transCtx, {
                    type: 'line',
                    data: {
                        labels: transactions.labels,
                        datasets: [{
                            label: 'System Actions',
                            data: transactions.data,
                            borderColor: '#10B981', // success color
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                    }
                });
            },

            updateCharts(newRegs, newTrans) {
                if (this.regChart && newRegs && newRegs.labels) {
                    this.regChart.data.labels = newRegs.labels;
                    this.regChart.data.datasets[0].data = newRegs.data;
                    this.regChart.update();
                }
                if (this.transChart && newTrans && newTrans.labels) {
                    this.transChart.data.labels = newTrans.labels;
                    this.transChart.data.datasets[0].data = newTrans.data;
                    this.transChart.update();
                }
            }
        }));
    });
</script>