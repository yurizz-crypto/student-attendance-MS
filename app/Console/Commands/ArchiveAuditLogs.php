<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('audit:archive')]
#[Description('Archive audit logs older than 90 days to a file and remove them from the database.')]
class ArchiveAuditLogs extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoffDate = now()->subDays(90);
        $logsToArchive = AuditLog::where('created_at', '<', $cutoffDate)->get();

        if ($logsToArchive->isEmpty()) {
            $this->info('No audit logs older than 90 days found.');

            return;
        }

        $archivePath = storage_path('logs/archives');
        if (! file_exists($archivePath)) {
            mkdir($archivePath, 0755, true);
        }

        $filename = 'audit_archive_'.now()->format('Y_m_d_His').'.json';

        file_put_contents(
            $archivePath.'/'.$filename,
            $logsToArchive->toJson(JSON_PRETTY_PRINT)
        );

        $count = $logsToArchive->count();

        // Delete the archived logs from the database
        AuditLog::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Archived and deleted {$count} old audit logs.");
    }
}
