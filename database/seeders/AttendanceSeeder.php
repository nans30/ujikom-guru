<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceLog;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = Teacher::all();
        $admin = User::first();

        if ($teachers->isEmpty() || !$admin) {
            $this->command->warn('Data Guru atau User kosong. Silakan seed Teacher & User terlebih dahulu.');
            return;
        }

        $adminId = $admin->id;

        // Rentang waktu: 1 tahun yang lalu sampai hari ini
        $startDate = Carbon::now()->subYear()->startOfDay();
        $endDate   = Carbon::now()->endOfDay();
        $period    = CarbonPeriod::create($startDate, '1 day', $endDate);

        $this->command->info('Memulai generate data absensi 1 tahun (Sabtu & Minggu dilewati)...');

        foreach ($period as $date) {
            // Filter: Lewati jika hari Sabtu atau Minggu
            if ($date->isWeekend()) {
                continue; 
            }

            $attendances = [];
            $attendanceLogs = [];
            $timestamp = $date->copy()->setTime(17, 0, 0)->toDateTimeString();

            // Ambil sampel 30 guru per hari agar data tidak terlalu bengkak
            $sampleTeachers = $teachers->shuffle()->take(30);

            foreach ($sampleTeachers as $teacher) {
                $rand = rand(1, 100);
                $status = 'hadir';
                
                // Logika probabilitas status
                if ($rand > 85 && $rand <= 90) $status = 'telat';
                elseif ($rand > 90 && $rand <= 95) $status = 'sakit';
                elseif ($rand > 95 && $rand <= 98) $status = 'izin';
                elseif ($rand > 98) $status = 'alpha';

                $checkIn = $checkOut = $methodIn = $methodOut = $lateDuration = $reason = null;

                if (in_array($status, ['hadir', 'telat'])) {
                    $methodIn = $methodOut = 'rfid';
                    
                    if ($status === 'hadir') {
                        // Datang jam 06:15 - 06:59
                        $checkIn = $date->copy()->setTime(6, rand(15, 59), rand(0, 59));
                    } else {
                        // Datang jam 07:01 - 07:45 (Telat)
                        $checkIn = $date->copy()->setTime(7, rand(1, 45), rand(0, 59));
                        $lateDuration = $checkIn->diffInMinutes($date->copy()->setTime(7, 0, 0));
                    }
                    
                    // Pulang jam 15:00 - 16:30
                    $checkOut = $date->copy()->setTime(rand(15, 16), rand(0, 30), rand(0, 59));

                    // Log Check-In
                    $attendanceLogs[] = [
                        'teacher_id' => $teacher->id,
                        'scan_time'  => $checkIn->toDateTimeString(),
                        'device_id'  => 'DEVICE-FRONT-01',
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                    
                    // Log Check-Out
                    $attendanceLogs[] = [
                        'teacher_id' => $teacher->id,
                        'scan_time'  => $checkOut->toDateTimeString(),
                        'device_id'  => 'DEVICE-FRONT-01',
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                } elseif (in_array($status, ['izin', 'sakit'])) {
                    $reason = ($status === 'sakit') ? 'Sakit demam/flu' : 'Ada urusan keluarga';
                }

                $attendances[] = [
                    'teacher_id'      => $teacher->id,
                    'date'            => $date->format('Y-m-d'),
                    'check_in'        => $checkIn ? $checkIn->toDateTimeString() : null,
                    'check_out'       => $checkOut ? $checkOut->toDateTimeString() : null,
                    'method_in'       => $methodIn,
                    'method_out'      => $methodOut,
                    'photo_check_in'  => null,
                    'photo_check_out' => null,
                    'status'          => $status,
                    'reason'          => $reason,
                    'proof_file'      => null,
                    'late_duration'   => $lateDuration,
                    'created_by_id'   => $adminId,
                    'created_at'      => $timestamp,
                    'updated_at'      => $timestamp,
                ];
            }

            // Insert data per hari untuk menjaga performa memori
            if (!empty($attendances)) {
                Attendance::insertOrIgnore($attendances);
            }
            
            if (!empty($attendanceLogs)) {
                AttendanceLog::insert($attendanceLogs);
            }
        }

        $this->command->info('Berhasil menyemai data absensi 1 tahun (Hanya hari kerja)!');
    }
}