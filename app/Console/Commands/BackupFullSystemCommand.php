<?php

namespace App\Console\Commands;

use App\Mail\BackupCompletedMail;
use App\Mail\BackupFailedMail;
use App\Models\BackupLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use ZipArchive;

class BackupFullSystemCommand extends Command
{
    protected $signature = 'app:backup-full {--manual : Triggered manually by an admin}';

    protected $description = 'Perform a full system backup (database + files) and email a compressed archive';

    public function handle(): int
    {
        $this->info('Starting full system backup...');

        $backupLog = BackupLog::create([
            'type' => 'full',
            'status' => 'pending',
            'notes' => $this->option('manual') ? 'Manual full backup triggered' : 'Scheduled monthly backup',
            'expires_at' => now()->addDays(30),
        ]);

        try {
            $backupDir = storage_path('backups');
            if (! is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $timestamp = now()->format('Y-m-d_His');
            $fullZipFile = $backupDir."/full_backup_{$timestamp}.zip";

            // Run database backup first (produces a zip in storage/backups)
            $this->info('Step 1/2: Backing up database...');
            Artisan::call('app:backup-database', ['--manual' => $this->option('manual')]);

            // Get the latest database backup zip
            $dbLog = BackupLog::where('type', 'database')
                ->where('status', 'success')
                ->orderBy('created_at', 'desc')
                ->first();

            // Run files backup
            $this->info('Step 2/2: Backing up files...');
            Artisan::call('app:backup-files', ['--manual' => $this->option('manual')]);

            $filesLog = BackupLog::where('type', 'files')
                ->where('status', 'success')
                ->orderBy('created_at', 'desc')
                ->first();

            // Combine into a single full-system zip
            $zip = new ZipArchive;
            if ($zip->open($fullZipFile, ZipArchive::CREATE) !== true) {
                throw new \RuntimeException('Failed to create full system zip archive.');
            }

            if ($dbLog && $dbLog->file_path && file_exists($dbLog->file_path)) {
                $zip->addFile($dbLog->file_path, 'database/'.basename($dbLog->file_path));
            } else {
                $zip->addFromString('database/README.txt', 'Database backup was not available.');
            }

            if ($filesLog && $filesLog->file_path && file_exists($filesLog->file_path)) {
                $zip->addFile($filesLog->file_path, 'files/'.basename($filesLog->file_path));
            } else {
                $zip->addFromString('files/README.txt', 'Files backup was not available.');
            }

            // Add a manifest
            $manifest = [
                'generated_at' => now()->toIso8601String(),
                'type' => 'full_system',
                'components' => [
                    'database' => $dbLog ? 'included' : 'failed',
                    'files' => $filesLog ? 'included' : 'failed',
                ],
            ];
            $zip->addFromString('MANIFEST.json', json_encode($manifest, JSON_PRETTY_PRINT));
            $zip->close();

            $backupLog->update([
                'status' => 'success',
                'file_path' => $fullZipFile,
                'file_size' => file_exists($fullZipFile) ? filesize($fullZipFile) : null,
            ]);

            $this->info("Full system backup saved: {$fullZipFile}");
            $this->applyRetentionPolicy('full');
            $this->notifyAdmins($backupLog, 'success');

            return self::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Full system backup failed', ['error' => $e->getMessage()]);

            $backupLog->update([
                'status' => 'failed',
                'notes' => $e->getMessage(),
            ]);

            $this->error('Full system backup failed: '.$e->getMessage());
            $this->notifyAdmins($backupLog, 'failed', $e->getMessage());

            return self::FAILURE;
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
                    Mail::to($admin->email)->send(new BackupFailedMail('full', $error));
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send backup notification email', ['error' => $e->getMessage()]);
        }
    }
}
