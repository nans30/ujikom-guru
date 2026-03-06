<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        $startDate = Carbon::now()->subYear()->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $period = CarbonPeriod::create($startDate, '1 day', $endDate);

        $this->command->info('Memulai generate dan insert data absensi (proses berjalan hari demi hari)...');

        // Loop per hari
        foreach ($period as $dateRaw) {
            // Pastikan format tanggal murni Carbon object
            $date = Carbon::parse($dateRaw);

            if ($date->isWeekend()) {
                continue;
            }

            $attendances = [];
            $attendanceLogs = [];
            $now = now()->toDateTimeString();

            // Loop per guru untuk hari tersebut
            foreach ($teachers as $teacher) {
                $rand = rand(1, 100);

                $status = 'hadir';
                if ($rand > 85 && $rand <= 90) $status = 'telat';
                elseif ($rand > 90 && $rand <= 95) $status = 'sakit';
                elseif ($rand > 95 && $rand <= 98) $status = 'izin';
                elseif ($rand > 98) $status = 'alpha';

                $checkIn = null;
                $checkOut = null;
                $methodIn = null;
                $methodOut = null;
                $lateDuration = null;
                $reason = null;

                if (in_array($status, ['hadir', 'telat'])) {
                    $methodIn = 'rfid';
                    $methodOut = 'rfid';

                    if ($status === 'hadir') {
                        $checkIn = $date->copy()->setTime(rand(6, 6), rand(15, 59), rand(0, 59));
                    } else { // telat
                        $checkIn = $date->copy()->setTime(rand(7, 7), rand(1, 45), rand(0, 59));
                        $lateDuration = $checkIn->diffInMinutes($date->copy()->setTime(7, 0, 0));
                    }

                    $checkOut = $date->copy()->setTime(rand(15, 16), rand(0, 30), rand(0, 59));

                    // Log Check-In
                    $attendanceLogs[] = [
                        'teacher_id' => $teacher->id,
                        'scan_time'  => $checkIn->toDateTimeString(), // Harus diconvert ke string untuk mass insert
                        'device_id'  => 'DEVICE-FRONT-01',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    // Log Check-Out
                    $attendanceLogs[] = [
                        'teacher_id' => $teacher->id,
                        'scan_time'  => $checkOut->toDateTimeString(), // Harus diconvert ke string untuk mass insert
                        'device_id'  => 'DEVICE-FRONT-01',
                        'created_at' => $now,
                        'updated_at' => $now,
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
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }

            // Insert per hari di dalam loop! Jauh lebih ringan untuk memori.
            // insertOrIgnore dipakai supaya kalau kamu re-run seeder, data yang sama tidak bentrok (unique constraint)
            Attendance::insertOrIgnore($attendances);

            if (!empty($attendanceLogs)) {
                AttendanceLog::insert($attendanceLogs);
            }
        }

        $this->command->info('Berhasil menyemai data absensi selama 1 tahun!');
    }
}