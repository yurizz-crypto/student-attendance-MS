<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SecurityAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $identityId,
        public readonly string $ipAddress,
        public readonly int $attemptCount,
        public readonly string $attemptedAt
    ) {}

    /** @return array<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🔐 Security Alert: Multiple Failed Login Attempts')
            ->greeting('Security Alert')
            ->line("**{$this->attemptCount} consecutive failed login attempts** have been detected on your system.")
            ->line("**Identity ID:** `{$this->identityId}`")
            ->line("**IP Address:** `{$this->ipAddress}`")
            ->line("**Time:** {$this->attemptedAt}")
            ->line('A CAPTCHA challenge has been activated to prevent automated access.')
            ->line('If this was not you, please review your system security immediately.')
            ->action('View Audit Logs', url('/admin/audit-logs'))
            ->salutation('AttendanceMS Security System');
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'security_alert',
            'title' => 'Security Alert: Multiple Failed Login Attempts',
            'message' => "{$this->attemptCount} failed login attempts detected for Identity ID: {$this->identityId} from IP {$this->ipAddress}.",
            'icon' => 'shield-exclamation',
        ];
    }
}
