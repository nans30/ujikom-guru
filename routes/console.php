<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


// ==============================
// AUTO ALPHA ATTENDANCE
// ==============================
Schedule::command('app:auto-alpha-attendance')
    ->dailyAt('08:27')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();// jam pulang sekolah