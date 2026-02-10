<?php

use App\Http\Controllers\Frontend\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\AttendanceController;
use App\Http\Controllers\Frontend\PermissionController;
use App\Http\Controllers\Auth\LoginController;
Route::get('/', [HomeController::class, 'index']);
Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::post('/attendance/scan', [AttendanceController::class, 'scan'])
    ->name('attendance.scan');
Route::middleware(['auth'])->group(function () {
    Route::get('/permission', [PermissionController::class, 'index'])->name('permission.index');
    Route::post('/permission', [PermissionController::class, 'store'])->name('permission.store');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});