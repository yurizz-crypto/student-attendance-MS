<?php

namespace App\Notifications;

use App\Models\AttendanceSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SessionStartedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $session;

    public function __construct(AttendanceSession $session)
    {
        $this->session = $session;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subjectCode = $this->session->classSection->subject->code ?? 'a class';

        return (new MailMessage)
            ->subject('Attendance Session Started')
            ->greeting('Hello '.$notifiable->first_name.',')
            ->line('A new attendance session has started for '.$subjectCode.'.')
            ->action('View Dashboard', url('/student/dashboard'))
            ->line('Please make sure to mark your attendance.');
    }

    public function toArray(object $notifiable): array
    {
        $subjectCode = $this->session->classSection->subject->code ?? 'Unknown';

        return [
            'type' => 'session_started',
            'message' => "A new attendance session has started for $subjectCode.",
            'session_id' => $this->session->id,
            'class_section_id' => $this->session->class_section_id,
        ];
    }
}
