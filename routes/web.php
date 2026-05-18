<?php

use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Faculty\AttendanceController;
use App\Http\Controllers\Faculty\ExportController as FacultyExportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\ExportController as StudentExportController;
use App\Http\Controllers\Student\QrScanController;
use App\Livewire\Admin\Reports\Index;
use App\Livewire\Admin\UserManagement;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/otp/verify', [OtpController::class, 'show'])->name('otp.verify');
    Route::post('/otp/verify', [OtpController::class, 'store'])->name('otp.store');
    Route::post('/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');
});

Route::middleware(['auth', 'verified', 'otp', 'log_access'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

});

Route::middleware(['auth', 'verified', 'otp', 'log_access', 'role:faculty'])
    ->prefix('faculty')
    ->name('faculty.')
    ->group(function () {

        Route::get('/classes', function () {
            return view('faculty.classes');
        })->name('classes');

        Route::get('/attendance', function () {
            return view('faculty.attendance');
        })->name('attendance');

        Route::get('/excuses', function () {
            return view('faculty.excuses');
        })->name('excuses');

        Route::get('/analytics', function () {
            return view('faculty.analytics');
        })->name('analytics');

        Route::get('/classes/{classSection}/attendance', [AttendanceController::class, 'show'])
            ->name('attendance.show');

        Route::post('/classes/{classSection}/attendance', [AttendanceController::class, 'store'])
            ->name('attendance.store');

        Route::get('/classes/{classSection}/export', [FacultyExportController::class, 'export'])
            ->name('classes.export');

    });

Route::middleware(['auth', 'verified', 'otp', 'log_access', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('student.dashboard');
        })->name('dashboard');

        Route::get('/analytics', function () {
            return view('student.analytics');
        })->name('analytics');

        Route::get('/qr-scan', [QrScanController::class, 'show'])->name('qr-scan.show');
        Route::post('/qr-scan', [QrScanController::class, 'scan'])->name('qr-scan.process');

        Route::get('/export', [StudentExportController::class, 'export'])->name('export');

    });

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::middleware(['auth', 'verified', 'otp', 'log_access', 'permission:manage_users'])->group(function () {
            Route::get('/users', UserManagement::class)->name('users');
            Route::get('/reports', Index::class)->name('reports');
        });

        // Other admin routes can use a general admin permission, or just require admin role
        Route::middleware(['auth', 'verified', 'otp', 'log_access', 'role:admin'])->group(function () {
            Route::get('/audit-logs', function () {
                return view('admin.audit-logs');
            })->name('audit-logs');

            Route::get('/trash', function () {
                return view('admin.trash');
            })->name('trash');

            Route::get('/backup-settings', function () {
                return view('admin.backup-settings');
            })->name('backup-settings');
        });

    });

require __DIR__.'/auth.php';
