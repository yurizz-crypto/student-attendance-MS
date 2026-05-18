<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $alertLevel; // 'warning', 'critical', 'info'

    public $alertMessage;

    public function __construct(string $alertLevel, string $alertMessage)
    {
        $this->alertLevel = $alertLevel;
        $this->alertMessage = $alertMessage;
    }

    public function via(object $notifiable): array
    {
        $category = in_array($this->alertLevel, ['warning', 'critical']) ? $this->alertLevel : 'system';
        $channels = $notifiable->getNotificationChannels($category);
        $channels[] = 'broadcast';

        return array_unique($channels);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('System Alert ('.strtoupper($this->alertLevel).')')
            ->greeting('Hello Administrator,')
            ->line('A system alert has been triggered:')
            ->line($this->alertMessage)
            ->action('View Dashboard', url('/admin/dashboard'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->alertLevel,
            'message' => $this->alertMessage,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => $this->alertLevel,
            'message' => $this->alertMessage,
        ]);
    }
}
