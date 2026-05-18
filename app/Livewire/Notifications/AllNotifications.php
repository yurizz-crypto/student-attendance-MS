<?php

namespace App\Livewire\Notifications;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AllNotifications extends Component
{
    use WithPagination;

    public string $filter = 'all';

    public int $perPage = 15;

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    #[On('echo-private:App.Models.User.{userId},.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated')]
    public function notifyNew(): void
    {
        $this->resetPage();
    }

    public int $userId;

    public function mount(): void
    {
        $this->userId = Auth::id();
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = Auth::user()->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->dispatch('swal:success', title: 'Done!', message: 'All notifications marked as read.');
    }

    public function deleteNotification(string $notificationId): void
    {
        $notification = Auth::user()->notifications()->find($notificationId);

        if ($notification) {
            $notification->delete();
        }
    }

    public function deleteAllRead(): void
    {
        Auth::user()->readNotifications()->delete();
        $this->dispatch('swal:success', title: 'Done!', message: 'All read notifications deleted.');
    }

    public function getNotificationsProperty()
    {
        $query = Auth::user()->notifications();

        if ($this->filter === 'unread') {
            $query = Auth::user()->unreadNotifications();
        }

        return $query->paginate($this->perPage);
    }

    public function getUnreadCountProperty(): int
    {
        return Auth::user()->unreadNotifications()->count();
    }

    public function render()
    {
        return view('livewire.notifications.all-notifications', [
            'notifications' => $this->notifications,
            'unreadCount' => $this->unreadCount,
        ])->layout('layouts.app');
    }
}
