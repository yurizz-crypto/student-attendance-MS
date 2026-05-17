<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class BackupDatabase implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 600; // 10 minutes

    public function handle(): void
    {
        try {
            $backupDir = storage_path('backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $backupFile = $backupDir . '/backup_' . now()->format('Y-m-d_His') . '.sql';
            
            // Get database configuration
            $dbHost = config('database.connections.mysql.host') ?? '127.0.0.1';
            $dbUser = config('database.connections.mysql.username') ?? 'root';
            $dbPass = config('database.connections.mysql.password') ?? '';
            $dbName = config('database.connections.mysql.database') ?? config('database.connections.sqlite.database');

            // Create backup using mysqldump
            $command = "mysqldump --host={$dbHost} --user={$dbUser}";
            if ($dbPass) {
                $command .= " --password={$dbPass}";
            }
            $command .= " {$dbName} > {$backupFile}";

            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($backupFile)) {
                // Compress the backup
                $zipFile = $backupDir . '/backup_' . now()->format('Y-m-d_His') . '.zip';
                $zip = new ZipArchive();
                
                if ($zip->open($zipFile, ZipArchive::CREATE)) {
                    $zip->addFile($backupFile, basename($backupFile));
                    $zip->close();
                    unlink($backupFile); // Remove uncompressed backup
                    
                    Log::info('Database backup completed successfully', ['backup_file' => basename($zipFile)]);
                } else {
                    throw new \Exception('Failed to compress backup');
                }
            } else {
                throw new \Exception('Failed to create database backup');
            }
        } catch (\Exception $e) {
            Log::error('Database backup failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Database backup job failed permanently', [
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
