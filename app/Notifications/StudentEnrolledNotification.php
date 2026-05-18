<?php

namespace App\Notifications;

use App\Models\ClassSection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentEnrolledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $classSection;

    public function __construct(ClassSection $classSection)
    {
        $this->classSection = $classSection;
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
            ->subject('Enrolled in Class: '.$this->classSection->subject->code)
            ->greeting('Hello '.$notifiable->first_name.',')
            ->line('You have been enrolled in '.$this->classSection->subject->name.' ('.$this->classSection->section_code.').')
            ->action('View Dashboard', url('/student/dashboard'))
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'success',
            'message' => 'You were enrolled in '.$this->classSection->subject->code.' ('.$this->classSection->section_code.').',
            'class_section_id' => $this->classSection->id,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'success',
            'message' => 'You were enrolled in '.$this->classSection->subject->code.' ('.$this->classSection->section_code.').',
        ]);
    }
}
