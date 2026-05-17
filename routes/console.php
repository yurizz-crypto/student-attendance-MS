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
