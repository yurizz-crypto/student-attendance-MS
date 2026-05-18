<?php

namespace App\Notifications;

use App\Models\AttendanceSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClassSessionCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $session;

    public function __construct(AttendanceSession $session)
    {
        $this->session = $session;
    }

    public function via(object $notifiable): array
    {
        $channels = $notifiable->getNotificationChannels('system');
        $channels[] = 'broadcast';

        return array_unique($channels);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Attendance Session: '.$this->session->classSection->subject->code)
            ->greeting('Hello '.$notifiable->first_name.',')
            ->line('An attendance session has started for '.$this->session->classSection->subject->name.'.')
            ->action('Scan QR Code', url('/student/qr-scan'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'info',
            'message' => 'Attendance session started for '.$this->session->classSection->subject->code,
            'session_id' => $this->session->id,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'info',
            'message' => 'Attendance session started for '.$this->session->classSection->subject->code,
        ]);
    }
}
