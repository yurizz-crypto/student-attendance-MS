<?php

namespace App\Console\Commands;

use App\Mail\BackupCompletedMail;
use App\Mail\BackupFailedMail;
use App\Models\BackupLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use ZipArchive;

class BackupFilesCommand extends Command
{
    protected $signature = 'app:backup-files {--manual : Triggered manually by an admin}';

    protected $description = 'Backup uploaded files and email a notification to administrators';

    public function handle(): int
    {
        $this->info('Starting file uploads backup...');

        $backupLog = BackupLog::create([
            'type' => 'files',
            'status' => 'pending',
            'notes' => $this->option('manual') ? 'Manual backup triggered' : 'Scheduled weekly backup (Sundays)',
            'expires_at' => now()->addDays(30),
        ]);

        try {
            $backupDir = storage_path('backups');
            if (! is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $timestamp = now()->format('Y-m-d_His');
            $zipFile = $backupDir."/files_backup_{$timestamp}.zip";

            $uploadPath = storage_path('app/public');
            if (! is_dir($uploadPath)) {
                // No uploads directory — create an empty zip with a readme
                $uploadPath = null;
            }

            $zip = new ZipArchive;
            if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
                throw new \RuntimeException('Failed to create zip archive.');
            }

            if ($uploadPath) {
                $this->addDirectoryToZip($zip, $uploadPath, 'public');
                $this->info("Added {$uploadPath} to archive.");
            } else {
                $zip->addFromString('README.txt', 'No uploaded files found at the time of backup.');
            }

            $zip->close();

            $backupLog->update([
                'status' => 'success',
                'file_path' => $zipFile,
                'file_size' => file_exists($zipFile) ? filesize($zipFile) : null,
            ]);

            $this->info("Files backup saved: {$zipFile}");
            $this->applyRetentionPolicy('files');
            $this->notifyAdmins($backupLog, 'success');

            return self::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Files backup failed', ['error' => $e->getMessage()]);

            $backupLog->update([
                'status' => 'failed',
                'notes' => $e->getMessage(),
            ]);

            $this->error('Files backup failed: '.$e->getMessage());
            $this->notifyAdmins($backupLog, 'failed', $e->getMessage());

            return self::FAILURE;
        }
    }

    private function addDirectoryToZip(ZipArchive $zip, string $dirPath, string $zipBasePath): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dirPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relativePath = $zipBasePath.'/'.str_replace($dirPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                $zip->addFile($file->getPathname(), $relativePath);
            }
        }
    }

    private function applyRetentionPolicy(string $type): void
    {
        $expired = BackupLog::where('type', $type)
            ->where('status', 'success')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expired as $log) {
            if ($log->file_path && file_exists($log->file_path)) {
                unlink($log->file_path);
            }
            $log->delete();
        }

        if ($expired->count() > 0) {
            $this->info("Retention policy applied: {$expired->count()} old backup(s) deleted.");
        }
    }

    private function notifyAdmins(BackupLog $log, string $status, string $error = ''): void
    {
        try {
            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                if ($status === 'success') {
                    Mail::to($admin->email)->send(new BackupCompletedMail($log));
                } else {
                    Mail::to($admin->email)->send(new BackupFailedMail('files', $error));
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send backup notification email', ['error' => $e->getMessage()]);
        }
    }
}
