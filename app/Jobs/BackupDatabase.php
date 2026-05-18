<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class BackupDatabase implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 600; // 10 minutes

    public function handle(): void
    {
        $result = Artisan::call('app:backup-database', ['--manual' => true]);

        if ($result !== 0) {
            throw new \RuntimeException('Database backup command failed with exit code: '.$result);
        }

        Log::info('Database backup job completed successfully via Artisan command.');
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Database backup job failed permanently', [
            'exception' => $exception->getMessage(),
        ]);
    }
}
