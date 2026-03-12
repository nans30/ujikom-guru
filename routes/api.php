<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\JournalController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('assessments', AssessmentController::class);
    Route::apiResource('attendances', AttendanceController::class);
    Route::apiResource('journals', JournalController::class);
    
    // Debugging endpoint
    Route::any('/debug', function (Request $request) {
        // Tampilan khusus debugging (mirip dd di Laravel web)
        dd('debuging', $request->all());
    });
});
