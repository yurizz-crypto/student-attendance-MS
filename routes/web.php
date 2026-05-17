<?php

use App\Http\Controllers\Faculty\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\QrScanController;
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

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

});

Route::middleware(['auth', 'verified', 'role:faculty'])
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

        Route::get('/classes/{classSection}/attendance', [AttendanceController::class, 'show'])
            ->name('attendance.show');

        Route::post('/classes/{classSection}/attendance', [AttendanceController::class, 'store'])
            ->name('attendance.store');

    });

Route::middleware(['auth', 'verified', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        Route::get('/qr-scan', [QrScanController::class, 'show'])->name('qr-scan.show');
        Route::post('/qr-scan', [QrScanController::class, 'scan'])->name('qr-scan.process');

    });

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/users', function () {
            return view('admin.users');
        })->name('users');

        Route::get('/audit-logs', function () {
            return view('admin.audit-logs');
        })->name('audit-logs');

    });

require __DIR__.'/auth.php';
