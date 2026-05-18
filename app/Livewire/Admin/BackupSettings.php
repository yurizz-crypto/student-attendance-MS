<?php

namespace App\Livewire\Admin;

use App\Models\BackupLog;
use App\Services\AuditService;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

class BackupSettings extends Component
{
    public string $activeTab = 'history';

    // Schedule configuration (display only — actual schedule is in routes/console.php)
    public array $scheduleConfig = [
        'database' => ['frequency' => 'weekly', 'day' => 'Sunday', 'time' => '02:00'],
        'files' => ['frequency' => 'weekly', 'day' => 'Sunday', 'time' => '02:30'],
        'full' => ['frequency' => 'monthly', 'day' => '1st', 'time' => '03:00'],
    ];

    public bool $isRunning = false;

    public function runBackup(string $type): void
    {
        $this->isRunning = true;

        try {
            $commandMap = [
                'database' => 'app:backup-database',
                'files' => 'app:backup-files',
                'full' => 'app:backup-full',
            ];

            if (! isset($commandMap[$type])) {
                $this->dispatch('swal:error', title: 'Error', message: 'Invalid backup type.');

                return;
            }

            Artisan::call($commandMap[$type], ['--manual' => true]);

            AuditService::log('manual_backup_triggered', 'System', null, ['type' => $type]);

            $this->dispatch('swal:success',
                title: 'Backup Started',
                message: ucfirst($type).' backup completed. Check your email for results.'
            );
        } catch (\Exception $e) {
            $this->dispatch('swal:error', title: 'Backup Failed', message: $e->getMessage());
        } finally {
            $this->isRunning = false;
        }
    }

    public function verifyBackup(int $backupLogId): void
    {
        $log = BackupLog::find($backupLogId);

        if (! $log) {
            $this->dispatch('swal:error', title: 'Error', message: 'Backup record not found.');

            return;
        }

        if ($log->status !== 'success') {
            $this->dispatch('swal:error', title: 'Verification Failed', message: 'This backup did not complete successfully.');

            return;
        }

        if (! $log->file_path || ! file_exists($log->file_path)) {
            $this->dispatch('swal:error', title: 'File Missing', message: 'The backup file no longer exists on the server. It may have been deleted by the retention policy or moved.');

            return;
        }

        $size = filesize($log->file_path);
        $formattedSize = $log->formatted_size;

        $this->dispatch('swal:success',
            title: '✅ Backup Verified',
            message: "File exists and is readable. Size: {$formattedSize}."
        );
    }

    public function deleteBackup(int $backupLogId): void
    {
        $log = BackupLog::find($backupLogId);

        if (! $log) {
            return;
        }

        if ($log->file_path && file_exists($log->file_path)) {
            unlink($log->file_path);
        }

        $log->delete();

        AuditService::log('backup_deleted', 'BackupLog', $backupLogId, []);

        $this->dispatch('swal:success', title: 'Deleted', message: 'Backup record removed.');
    }

    public function render()
    {
        $backupHistory = BackupLog::orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => BackupLog::count(),
            'success' => BackupLog::where('status', 'success')->count(),
            'failed' => BackupLog::where('status', 'failed')->count(),
            'last_database' => BackupLog::where('type', 'database')->where('status', 'success')->latest()->first(),
            'last_files' => BackupLog::where('type', 'files')->where('status', 'success')->latest()->first(),
            'last_full' => BackupLog::where('type', 'full')->where('status', 'success')->latest()->first(),
        ];

        return view('livewire.admin.backup-settings', compact('backupHistory', 'stats'));
    }
}
