<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Public Routes
Route::get('/', function () {
    return view('welcome');
});

// 2. Shared Authenticated Routes (Dashboard & Profile)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // The main dashboard (Acts as a traffic controller for all roles)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile Management
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

});

// 3. Faculty Specific Routes
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

// 4. Admin Specific Routes
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        Route::get('/users', function () {
            return view('admin.users');
        })->name('users');
        
    });

require __DIR__.'/auth.php';