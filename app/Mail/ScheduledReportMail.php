<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScheduledReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $reportConfig;

    public $pdfContent;

    /**
     * Create a new message instance.
     */
    public function __construct($reportConfig, $pdfContent)
    {
        $this->reportConfig = $reportConfig;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Automated System Report: '.$this->reportConfig->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.scheduled-report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, $this->reportConfig->name.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
