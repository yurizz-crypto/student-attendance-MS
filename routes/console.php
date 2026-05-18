<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Archive audit logs older than 30 days every day at midnight
Schedule::command('app:archive-audit-logs')->daily();

// Run scheduled reports hourly to check for any due reports
Schedule::command('app:run-scheduled-reports')->hourly();

// Database backup: weekly on Sundays at 2:00 AM
Schedule::command('app:backup-database')->weeklyOn(0, '02:00')->withoutOverlapping();

// File uploads backup: weekly on Sundays at 2:30 AM
Schedule::command('app:backup-files')->weeklyOn(0, '02:30')->withoutOverlapping();

// Full system backup: monthly on the 1st at 3:00 AM
Schedule::command('app:backup-full')->monthlyOn(1, '03:00')->withoutOverlapping();
