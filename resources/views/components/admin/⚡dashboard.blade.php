<?php
use Livewire\Component;

new class extends Component {
    public function with(): array
    {
        return [
            'stats' => [
                'total_users' => 1250,
                'active_sessions' => 42,
                'system_health' => 98.2,
                'pending_alerts' => 12,
            ],
            'recent_activity' => [
                ['user' => 'Admin User', 'action' => 'Bulk Import', 'target' => 'BSIT 3A Class List', 'time' => '2 mins ago'],
                ['user' => 'Faculty Smith', 'action' => 'Generated Code', 'target' => 'IT 311', 'time' => '15 mins ago'],
                ['user' => 'System', 'action' => 'Auto-Archive', 'target' => 'Prev Semester Records', 'time' => '1 hour ago'],
            ]
        ];
    }
}; ?>

<div class="space-y-8 animate-fade-in-up">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-navy">System Administration</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">Global platform control and monitoring dashboard.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.reports') }}" wire:navigate>
                <x-secondary-button class="gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    System Report
                </x-secondary-button>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-surface p-6 rounded-3xl border border-gray-100 shadow-sm">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Users</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-navy">{{ number_format($stats['total_users']) }}</h3>
                <span class="text-success text-xs font-bold flex items-center gap-1">+2.4% <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7" /></svg></span>
            </div>
        </div>
        <div class="bg-surface p-6 rounded-3xl border border-gray-100 shadow-sm">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Live Sessions</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-navy">{{ $stats['active_sessions'] }}</h3>
                <div class="flex -space-x-2">
                    <div class="w-6 h-6 rounded-full bg-brand border-2 border-surface"></div>
                    <div class="w-6 h-6 rounded-full bg-info border-2 border-surface"></div>
                    <div class="w-6 h-6 rounded-full bg-gray-200 border-2 border-surface"></div>
                </div>
            </div>
        </div>
        <div class="bg-surface p-6 rounded-3xl border border-gray-100 shadow-sm">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">System Health</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-navy">{{ $stats['system_health'] }}%</h3>
                <div class="w-16 bg-gray-100 h-2 rounded-full overflow-hidden mb-2">
                    <div class="bg-success h-full" style="width: 98%"></div>
                </div>
            </div>
        </div>
        <div class="bg-surface p-6 rounded-3xl border border-error/20 shadow-sm">
            <p class="text-xs font-bold text-error uppercase tracking-widest mb-1">Critical Alerts</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-error">{{ $stats['pending_alerts'] }}</h3>
                <div class="w-8 h-8 rounded-lg bg-error/10 text-error flex items-center justify-center animate-pulse">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <h2 class="text-xl font-bold text-navy flex items-center gap-2">
                <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                Quick Management
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-surface border border-gray-100 p-8 rounded-3xl shadow-sm hover:shadow-md transition-all group">
                    <div class="w-14 h-14 bg-brand/10 text-brand rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-navy mb-2">User Management</h3>
                    <p class="text-sm text-gray-500 mb-6 leading-relaxed">Add new faculty, manage student enrollment, and verify identities.</p>
                    <a href="{{ route('admin.users') }}" wire:navigate class="block w-full">
                        <x-primary-button class="w-full">Manage Users</x-primary-button>
                    </a>
                </div>
                <div class="bg-surface border border-gray-100 p-8 rounded-3xl shadow-sm hover:shadow-md transition-all group">
                    <div class="w-14 h-14 bg-info/10 text-info rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-navy mb-2">Audit Trails</h3>
                    <p class="text-sm text-gray-500 mb-6 leading-relaxed">Review detailed logs of all security-sensitive actions and device bindings.</p>
                    <a href="{{ route('admin.audit-logs') }}" wire:navigate class="block w-full">
                        <x-secondary-button class="w-full">View Audit Logs</x-secondary-button>
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h2 class="text-lg font-bold text-navy">Global Activity</h2>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Real-time</span>
            </div>
            <div class="p-4 space-y-4 flex-1">
                @foreach($recent_activity as $activity)
                    <div class="p-4 border border-gray-100 rounded-2xl bg-gray-50/50 hover:bg-white transition-colors group">
                        <div class="flex justify-between items-start mb-1">
                            <p class="text-sm font-bold text-navy">{{ $activity['user'] }}</p>
                            <span class="text-[10px] font-bold text-gray-400">{{ $activity['time'] }}</span>
                        </div>
                        <p class="text-xs font-semibold text-brand mb-1">{{ $activity['action'] }}</p>
                        <p class="text-[11px] font-medium text-gray-500">{{ $activity['target'] }}</p>
                    </div>
                @endforeach
            </div>
            <div class="p-4 border-t border-gray-100">
                <button class="w-full text-center text-xs font-bold text-gray-400 hover:text-brand transition-colors">Clear All History</button>
            </div>
        </div>
    </div>
</div>