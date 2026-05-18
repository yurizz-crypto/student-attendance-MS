<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationPreferences extends Component
{
    public $preferences = [];

    public function mount()
    {
        $user = Auth::user();

        // Load existing or set defaults
        $this->preferences = $user->notification_preferences ?? [
            'system' => ['database' => true],
            'warning' => ['database' => true, 'mail' => true],
            'critical' => ['database' => true, 'mail' => true],
        ];

        // Ensure all categories exist
        foreach (['system', 'warning', 'critical'] as $cat) {
            if (! isset($this->preferences[$cat])) {
                $this->preferences[$cat] = ['database' => true, 'mail' => ($cat !== 'system')];
            }
        }
    }

    public function save(): void
    {
        $user = Auth::user();
        $user->notification_preferences = $this->preferences;
        $user->save();

        $this->dispatch('swal:success', title: 'Saved!', message: 'Notification preferences updated.');
    }

    public function render()
    {
        return view('livewire.profile.notification-preferences');
    }
}
