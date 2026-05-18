<?php

namespace App\Notifications;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentScannedAttendanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $student;

    public $record;

    public function __construct(User $student, AttendanceRecord $record)
    {
        $this->student = $student;
        $this->record = $record;
    }

    public function via(object $notifiable): array
    {
        $channels = $notifiable->getNotificationChannels('system');
        $channels[] = 'broadcast';

        return array_unique($channels);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subjectCode = $this->record->session->classSection->subject->code ?? 'a class';

        return (new MailMessage)
            ->subject('Student Attendance Scanned: '.$subjectCode)
            ->greeting('Hello '.$notifiable->first_name.',')
            ->line($this->student->first_name.' '.$this->student->last_name.' scanned attendance for '.$subjectCode.' at '.$this->record->scanned_at->format('H:i'))
            ->action('View Records', url('/faculty/attendance'));
    }

    public function toArray(object $notifiable): array
    {
        $subjectCode = $this->record->session->classSection->subject->code ?? 'Unknown';

        return [
            'type' => 'success',
            'message' => "{$this->student->first_name} {$this->student->last_name} scanned attendance for $subjectCode.",
            'record_id' => $this->record->id,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $subjectCode = $this->record->session->classSection->subject->code ?? 'Unknown';

        return new BroadcastMessage([
            'type' => 'success',
            'message' => "{$this->student->first_name} {$this->student->last_name} scanned attendance for $subjectCode.",
        ]);
    }
}
