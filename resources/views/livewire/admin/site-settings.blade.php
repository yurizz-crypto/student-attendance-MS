<div class="space-y-6">
    <div class="bg-surface rounded-3xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-lg font-bold text-navy">Branding & Theme</h3>
        <p class="text-sm text-gray-500 mt-1">Update the public-facing branding. Changes apply across the app.</p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Site Name</label>
                    <input type="text" wire:model.defer="siteName"
                        class="w-full rounded-xl border-gray-300 text-sm focus:ring-brand focus:border-brand" />
                    @error('siteName') <span class="text-error text-xs font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Logo</label>
                        <input type="file" wire:model="logo" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-brand/10 file:text-brand" />
                        @error('logo') <span class="text-error text-xs font-medium">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Favicon</label>
                        <input type="file" wire:model="favicon" accept="image/*,.ico"
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-brand/10 file:text-brand" />
                        @error('favicon') <span class="text-error text-xs font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Primary</label>
                    <input type="color" wire:model.defer="primaryColor"
                        class="w-full h-10 rounded-lg border border-gray-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Primary Hover</label>
                    <input type="color" wire:model.defer="primaryHoverColor"
                        class="w-full h-10 rounded-lg border border-gray-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Secondary</label>
                    <input type="color" wire:model.defer="secondaryColor"
                        class="w-full h-10 rounded-lg border border-gray-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Info</label>
                    <input type="color" wire:model.defer="infoColor"
                        class="w-full h-10 rounded-lg border border-gray-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Success</label>
                    <input type="color" wire:model.defer="successColor"
                        class="w-full h-10 rounded-lg border border-gray-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Warning</label>
                    <input type="color" wire:model.defer="warningColor"
                        class="w-full h-10 rounded-lg border border-gray-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Error</label>
                    <input type="color" wire:model.defer="errorColor"
                        class="w-full h-10 rounded-lg border border-gray-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Background</label>
                    <input type="color" wire:model.defer="backgroundColor"
                        class="w-full h-10 rounded-lg border border-gray-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Surface</label>
                    <input type="color" wire:model.defer="surfaceColor"
                        class="w-full h-10 rounded-lg border border-gray-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Text</label>
                    <input type="color" wire:model.defer="textColor"
                        class="w-full h-10 rounded-lg border border-gray-200" />
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="button" wire:click="saveBranding"
                class="px-6 py-2.5 rounded-xl bg-brand text-white text-sm font-bold hover:bg-brand-hover">
                Save Branding
            </button>
        </div>
    </div>

</div>