<?php

use App\Http\Controllers\Frontend\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\AttendanceController;
use App\Http\Controllers\Frontend\PermissionController;
use App\Http\Controllers\Frontend\JournalController; // Tambahkan import ini
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Frontend\DashboardController;

Route::get('/', [HomeController::class, 'index']);

// Attendance Routes (Public/Semi-Public)
Route::get('/attendance', [AttendanceController::class, 'index'])
    ->name('attendance.index');

Route::post('/attendance/scan', [AttendanceController::class, 'scan'])
    ->name('attendance.scan');

Route::get('/attendance/holiday-check', [AttendanceController::class, 'checkHoliday'])
    ->name('attendance.holiday.check');

// Protected Routes (Must Login)
Route::middleware(['auth'])->group(function () {
    // Permission
    Route::get('/permission', [PermissionController::class, 'index'])->name('permission.index');
    Route::post('/permission', [PermissionController::class, 'store'])->name('permission.store');

    // Journal (Guru)
    // Menggunakan resource agar otomatis punya route index, create, store, dll.
    Route::resource('journal', JournalController::class)->names([
        'index'   => 'journal.index',
        'create'  => 'journal.create',
        'store'   => 'journal.store',
        'edit'    => 'journal.edit',
        'update'  => 'journal.update',
        'destroy' => 'journal.destroy',
    ]);
    // Statistic (Guru)
    Route::get('/statistics', [\App\Http\Controllers\Frontend\StatisticController::class, 'index'])->name('statistic.index');

    // Calendar
    Route::get('/calendar', [\App\Http\Controllers\Frontend\CalendarController::class, 'index'])->name('calendar.index');

    Route::get('/dashboards', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Guru)
    Route::get('/profile', [\App\Http\Controllers\Frontend\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Frontend\ProfileController::class, 'update'])->name('profile.update');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
