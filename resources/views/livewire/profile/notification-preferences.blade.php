<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Notification Preferences') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Choose how you want to be notified for different types of alerts.') }}
        </p>
    </header>

    <form wire:submit="save" class="mt-6 space-y-6">
        
        <div class="space-y-4 border border-gray-100 rounded-xl overflow-hidden divide-y divide-gray-100">
            
            {{-- System Notifications --}}
            <div class="p-4 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">System Notifications</h3>
                    <p class="text-xs text-gray-500 mt-0.5">General updates, enrollment, and class sessions.</p>
                </div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="preferences.system.database" class="rounded border-gray-300 text-brand shadow-sm focus:ring-brand" disabled checked title="In-app notifications are required for system alerts">
                        <span class="text-sm text-gray-700">In-App</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="preferences.system.mail" class="rounded border-gray-300 text-brand shadow-sm focus:ring-brand">
                        <span class="text-sm text-gray-700">Email</span>
                    </label>
                </div>
            </div>

            {{-- Warning Alerts --}}
            <div class="p-4 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Warning Alerts</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Excuses processed, low stock (if applicable), etc.</p>
                </div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="preferences.warning.database" class="rounded border-gray-300 text-brand shadow-sm focus:ring-brand">
                        <span class="text-sm text-gray-700">In-App</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="preferences.warning.mail" class="rounded border-gray-300 text-brand shadow-sm focus:ring-brand">
                        <span class="text-sm text-gray-700">Email</span>
                    </label>
                </div>
            </div>

            {{-- Critical Alerts --}}
            <div class="p-4 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Critical Alerts</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Security breaches, system errors.</p>
                </div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="preferences.critical.database" class="rounded border-gray-300 text-brand shadow-sm focus:ring-brand" disabled checked title="In-app notifications are required for critical alerts">
                        <span class="text-sm text-gray-700">In-App</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="preferences.critical.mail" class="rounded border-gray-300 text-brand shadow-sm focus:ring-brand">
                        <span class="text-sm text-gray-700">Email</span>
                    </label>
                </div>
            </div>

        </div>

        <div class="flex items-center gap-4 mt-6">
            <x-primary-button>{{ __('Save Preferences') }}</x-primary-button>
        </div>
    </form>
</section>
