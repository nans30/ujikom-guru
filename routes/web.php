<?php

use App\Http\Controllers\Frontend\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\AttendanceController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::post('/attendance/scan', [AttendanceController::class, 'scan'])
    ->name('attendance.scan');
Route::get('/permission', [App\Http\Controllers\Frontend\PermissionController::class, 'index'])->name('permission.index');