<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BackupFailedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $backupType;

    public string $errorMessage;

    public function __construct(string $backupType, string $errorMessage)
    {
        $this->backupType = $backupType;
        $this->errorMessage = $errorMessage;
    }

    public function envelope(): Envelope
    {
        $typeLabel = ucfirst($this->backupType);

        return new Envelope(
            subject: "❌ Backup Failed: {$typeLabel} — ".now()->format('M d, Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.backup-failed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
