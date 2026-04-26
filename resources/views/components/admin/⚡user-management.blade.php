<?php
use Livewire\Component;

new class extends Component {
    public $search = '';
    public $roleFilter = '';

    public function with(): array
    {
        return [
            'users' => [
                ['name' => 'Dr. Alice Winston', 'id' => 'FAC-7721', 'role' => 'Faculty', 'status' => 'Active', 'email' => 'winston.a@university.edu'],
                ['name' => 'Seth Laurence Bongo', 'id' => 'STUD-001', 'role' => 'Student', 'status' => 'Active', 'email' => 'bongo.s@student.edu'],
                ['name' => 'Markus Thorne', 'id' => 'ADMIN-05', 'role' => 'Admin', 'status' => 'Active', 'email' => 'thorne.m@it.edu'],
            ]
        ];
    }
}; ?>

<div class="space-y-6">
    <div class="bg-surface p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col md:flex-row gap-4 items-end">
        <div class="flex-1 w-full">
            <x-input-label for="search" value="Search Users" />
            <x-text-input wire:model.live="search" placeholder="Name, ID, or Email..." class="w-full" />
        </div>
        <div class="w-full md:w-48">
            <x-input-label for="role" value="Filter Role" />
            <select wire:model.live="roleFilter" class="w-full rounded-xl border-gray-300 bg-gray-50 text-navy py-2.5 px-4 focus:border-brand focus:ring-brand shadow-sm sm:text-sm">
                <option value="">All Roles</option>
                <option value="admin">Admins</option>
                <option value="faculty">Faculty</option>
                <option value="student">Students</option>
            </select>
        </div>
        <x-primary-button class="gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
            Add User
        </x-primary-button>
    </div>

    <div class="bg-surface rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50/50 text-xs font-bold text-gray-500 uppercase tracking-widest">
                <tr>
                    <th class="py-5 pl-8 pr-3 text-left">User Profile</th>
                    <th class="px-3 py-5 text-left">Identity ID</th>
                    <th class="px-3 py-5 text-left">Role</th>
                    <th class="px-3 py-5 text-left">Status</th>
                    <th class="py-5 pl-3 pr-8 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="py-5 pl-8 pr-3 whitespace-nowrap">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-navy/5 text-navy flex items-center justify-center font-bold text-sm group-hover:bg-brand group-hover:text-white transition-all">
                                    {{ strtoupper(substr($user['name'], 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-navy">{{ $user['name'] }}</p>
                                    <p class="text-xs font-medium text-gray-400">{{ $user['email'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-5 whitespace-nowrap text-sm font-bold text-gray-500">{{ $user['id'] }}</td>
                        <td class="px-3 py-5 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $user['role'] === 'Admin' ? 'bg-navy text-white' : ($user['role'] === 'Faculty' ? 'bg-brand/10 text-brand' : 'bg-info/10 text-info') }}">
                                {{ $user['role'] }}
                            </span>
                        </td>
                        <td class="px-3 py-5 whitespace-nowrap">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-success">
                                <span class="w-1.5 h-1.5 rounded-full bg-success"></span>
                                {{ $user['status'] }}
                            </div>
                        </td>
                        <td class="py-5 pl-3 pr-8 whitespace-nowrap text-right">
                            <div class="flex justify-end gap-2">
                                <button class="p-2 rounded-lg text-gray-400 hover:text-brand hover:bg-brand/5 transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button class="p-2 rounded-lg text-gray-400 hover:text-error hover:bg-error/5 transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>