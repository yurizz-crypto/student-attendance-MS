<?php

namespace App\Notifications;

use App\Models\AttendanceSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MarkedAbsentNotification extends Notification implements ShouldQueue
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
        $date = $this->session->date->format('M d, Y');

        return (new MailMessage)
            ->subject('Marked Absent: '.$subjectCode)
            ->greeting('Hello '.$notifiable->first_name.',')
            ->line('You have been marked absent for '.$subjectCode.' on '.$date.'.')
            ->action('Submit an Excuse', url('/student/dashboard'))
            ->line('If you have a valid reason, please submit an excuse via your dashboard.');
    }

    public function toArray(object $notifiable): array
    {
        $subjectCode = $this->session->classSection->subject->code ?? 'Unknown';

        return [
            'type' => 'marked_absent',
            'message' => "You have been marked absent for $subjectCode.",
            'session_id' => $this->session->id,
        ];
    }
}
