<?php

namespace App\Notifications;

use App\Models\Excuse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExcuseSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $excuse;

    public function __construct(Excuse $excuse)
    {
        $this->excuse = $excuse;
    }

    public function via(object $notifiable): array
    {
        $channels = $notifiable->getNotificationChannels('system');
        $channels[] = 'broadcast';

        return array_unique($channels);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $studentName = $this->excuse->student->first_name.' '.$this->excuse->student->last_name;
        $subjectCode = $this->excuse->classSection->subject->code ?? 'a class';

        return (new MailMessage)
            ->subject('New Excuse Submitted: '.$subjectCode)
            ->greeting('Hello '.$notifiable->first_name.',')
            ->line($studentName.' has submitted an excuse for '.$subjectCode.'.')
            ->action('Review Excuse', url('/faculty/excuses'))
            ->line('Please review and process this excuse.');
    }

    public function toArray(object $notifiable): array
    {
        $studentName = $this->excuse->student->first_name.' '.$this->excuse->student->last_name;
        $subjectCode = $this->excuse->classSection->subject->code ?? 'Unknown';

        return [
            'type' => 'excuse_submitted',
            'message' => "$studentName submitted an excuse for $subjectCode.",
            'excuse_id' => $this->excuse->id,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $studentName = $this->excuse->student->first_name.' '.$this->excuse->student->last_name;
        $subjectCode = $this->excuse->classSection->subject->code ?? 'Unknown';

        return new BroadcastMessage([
            'type' => 'excuse_submitted',
            'message' => "$studentName submitted an excuse for $subjectCode.",
        ]);
    }
}
