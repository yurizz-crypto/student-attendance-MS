<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Faculty\AttendanceController;
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

Route::middleware(['auth', 'verified'])
    ->prefix('faculty')
    ->name('faculty.')
    ->group(function () {
        
        // This keeps your existing static classes page
        Route::get('/classes', function () {
            return view('faculty.classes');
        })->name('classes');

        // Dynamically loads the specific class using the AttendanceController
        Route::get('/classes/{classSection}/attendance', [AttendanceController::class, 'show'])
            ->name('attendance.show');

        // Handles the submission of the attendance form
        Route::post('/classes/{classSection}/attendance', [AttendanceController::class, 'store'])
            ->name('attendance.store');
            
    });

Route::middleware(['auth', 'verified'])
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