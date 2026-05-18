<?php

namespace App\Mail;

use App\Models\BackupLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BackupCompletedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public BackupLog $backupLog;

    public function __construct(BackupLog $backupLog)
    {
        $this->backupLog = $backupLog;
    }

    public function envelope(): Envelope
    {
        $typeLabel = ucfirst($this->backupLog->type);

        return new Envelope(
            subject: "✅ Backup Completed: {$typeLabel} — ".now()->format('M d, Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.backup-completed',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $filePath = $this->backupLog->file_path;

        // Only attach if file exists and is under 20 MB
        if ($filePath && file_exists($filePath) && filesize($filePath) < 20 * 1024 * 1024) {
            return [
                Attachment::fromPath($filePath)
                    ->withMime('application/zip'),
            ];
        }

        return [];
    }
}
