<?php

use App\Http\Controllers\ProfileController;
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
        
        Route::get('/classes', function () {
            return view('faculty.classes');
        })->name('classes');

        Route::get('/attendance', function () {
            return view('faculty.attendance');
        })->name('attendance');
        
    });

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        Route::get('/users', function () {
            return view('admin.users');
        })->name('users');

        // Add this route for Audit Logs
        Route::get('/audit-logs', function () {
            return view('admin.audit-logs');
        })->name('audit-logs');
        
    });

require __DIR__.'/auth.php';