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

class BackupDatabaseCommand extends Command
{
    protected $signature = 'app:backup-database {--manual : Triggered manually by an admin}';

    protected $description = 'Backup the PostgreSQL database and email it to administrators';

    public function handle(): int
    {
        $this->info('Starting database backup...');

        $backupLog = BackupLog::create([
            'type' => 'database',
            'status' => 'pending',
            'notes' => $this->option('manual') ? 'Manual backup triggered' : 'Scheduled weekly backup',
            'expires_at' => now()->addDays(30),
        ]);

        try {
            $backupDir = storage_path('backups');
            if (! is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $timestamp = now()->format('Y-m-d_His');
            $sqlFile = $backupDir."/db_backup_{$timestamp}.sql";
            $zipFile = $backupDir."/db_backup_{$timestamp}.zip";

            // Build pg_dump command
            $host = config('database.connections.pgsql.host', '127.0.0.1');
            $port = config('database.connections.pgsql.port', '5432');
            $user = config('database.connections.pgsql.username', 'postgres');
            $pass = config('database.connections.pgsql.password', 'notorious');
            $name = config('database.connections.pgsql.database');

            // pg_dump path — check common locations
            $pgDump = $this->findPgDump();

            // Set PGPASSWORD via putenv so exec() child process inherits it
            // This works on both Windows and Linux — avoids SET/export shell quirks
            if ($pass) {
                putenv("PGPASSWORD={$pass}");
            }

            $command = "\"{$pgDump}\" --host={$host} --port={$port} --username={$user} --file=\"{$sqlFile}\" {$name}";

            exec($command, $output, $returnCode);

            if ($returnCode !== 0 || ! file_exists($sqlFile)) {
                throw new \RuntimeException('pg_dump failed with return code: '.$returnCode.'. Output: '.implode("\n", $output));
            }

            // Zip the SQL file
            $zip = new ZipArchive;
            if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
                throw new \RuntimeException('Failed to create zip archive.');
            }
            $zip->addFile($sqlFile, basename($sqlFile));
            $zip->close();
            unlink($sqlFile);

            $backupLog->update([
                'status' => 'success',
                'file_path' => $zipFile,
                'file_size' => file_exists($zipFile) ? filesize($zipFile) : null,
            ]);

            $this->info("Database backup saved: {$zipFile}");
            $this->applyRetentionPolicy('database');
            $this->notifyAdmins($backupLog, 'success');

            return self::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Database backup failed', ['error' => $e->getMessage()]);

            $backupLog->update([
                'status' => 'failed',
                'notes' => $e->getMessage(),
            ]);

            $this->error('Database backup failed: '.$e->getMessage());
            $this->notifyAdmins($backupLog, 'failed', $e->getMessage());

            return self::FAILURE;
        }
    }

    private function findPgDump(): string
    {
        // Check if pg_dump is in PATH
        $which = PHP_OS_FAMILY === 'Windows' ? 'where pg_dump' : 'which pg_dump';
        exec($which, $out, $code);

        if ($code === 0 && ! empty($out[0])) {
            return trim($out[0]);
        }

        // Common Windows paths
        $windowsPaths = [
            'C:\Program Files\PostgreSQL\17\bin\pg_dump.exe',
            'C:\Program Files\PostgreSQL\16\bin\pg_dump.exe',
            'C:\Program Files\PostgreSQL\15\bin\pg_dump.exe',
            'C:\Program Files\PostgreSQL\14\bin\pg_dump.exe',
            'C:\Program Files\PostgreSQL\13\bin\pg_dump.exe',
        ];

        foreach ($windowsPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return 'pg_dump'; // Fallback — may fail if not in PATH
    }

    /**
     * Delete backups older than 30 days for this type.
     */
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
                    Mail::to($admin->email)->send(new BackupFailedMail('database', $error));
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send backup notification email', ['error' => $e->getMessage()]);
        }
    }
}
