<?php

namespace App\Notifications;

use App\Models\Excuse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExcuseProcessedNotification extends Notification implements ShouldQueue
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
        $subjectCode = $this->excuse->classSection->subject->code ?? 'a class';
        $status = ucfirst($this->excuse->status);

        return (new MailMessage)
            ->subject('Excuse '.$status.': '.$subjectCode)
            ->greeting('Hello '.$notifiable->first_name.',')
            ->line('Your excuse for '.$subjectCode.' has been '.$this->excuse->status.'.')
            ->action('View Dashboard', url('/student/dashboard'));
    }

    public function toArray(object $notifiable): array
    {
        $subjectCode = $this->excuse->classSection->subject->code ?? 'Unknown';

        return [
            'type' => 'excuse_processed',
            'message' => "Your excuse for $subjectCode was {$this->excuse->status}.",
            'excuse_id' => $this->excuse->id,
            'status' => $this->excuse->status,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $subjectCode = $this->excuse->classSection->subject->code ?? 'Unknown';

        return new BroadcastMessage([
            'type' => 'excuse_processed',
            'message' => "Your excuse for $subjectCode was {$this->excuse->status}.",
        ]);
    }
}
