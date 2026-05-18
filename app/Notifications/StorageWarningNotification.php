<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StorageWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly float $usagePercent,
        public readonly string $checkedAt
    ) {}

    /** @return array<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ System Warning: Storage Capacity at '.number_format($this->usagePercent, 1).'%')
            ->greeting('Storage Warning')
            ->line("Your system storage has reached **{$this->usagePercent}%** capacity, which exceeds the 85% threshold.")
            ->line("**Checked at:** {$this->checkedAt}")
            ->line('**Recommended cleanup actions:**')
            ->line('• Delete old backup files from the Backup Settings panel')
            ->line('• Clear temporary files from `storage/app/temp`')
            ->line('• Review and purge aged audit log attachments')
            ->action('Go to Backup Settings', url('/admin/backup-settings'))
            ->salutation('AttendanceMS System Monitor');
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'storage_warning',
            'title' => 'Storage Capacity Warning',
            'message' => "System storage is at {$this->usagePercent}%. Please clean up old files to free space.",
            'icon' => 'server',
        ];
    }
}
