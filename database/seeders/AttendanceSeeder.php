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
    use \App\Traits\AttendancePointTrait;

    public function run(): void
    {
        $teachers = Teacher::all();
        $admin = User::first();

        if ($teachers->isEmpty() || !$admin) {
            $this->command->warn('Data Guru atau User kosong. Silakan seed Teacher & User terlebih dahulu.');
            return;
        }

        $adminId = $admin->id;

        // Siapkan array untuk menampung saldo poin tiap guru
        $teacherPoints = [];
        foreach ($teachers as $t) {
            $teacherPoints[$t->id] = 0; // Setup saldo awal 0. (Jika mau mempertahankan yang ada: $t->points)
        }

        // Rentang waktu: 1 tahun yang lalu sampai hari ini
        $startDate = Carbon::now()->subYear()->startOfDay();
        $endDate   = Carbon::now()->endOfDay();
        $period    = CarbonPeriod::create($startDate, '1 day', $endDate);

        $this->command->info('Memulai generate data absensi 1 tahun (Sabtu & Minggu dilewati) beserta Point Ledger...');

        // Optimaasi query rule: kita cache di awal jika memungkinkan (Trait memanggil manual, tapi seeder lebih cepat kalau logicnya dipanggil saja)
        // Kita gunakan Trait yang memanggil DB di dalam. Ini bisa agak lambat tapi seeder berjalan di background

        $totalLedgers = 0;

        foreach ($period as $date) {
            // Filter: Lewati jika hari Sabtu atau Minggu
            if ($date->isWeekend()) {
                continue; 
            }

            $attendances = [];
            $attendanceLogs = [];
            $pointLedgers = [];
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
                    // Mix between face_id and rfid, but default heavily to face_id
                    $methodIn = $methodOut = (rand(1, 10) > 2) ? 'face_id' : 'rfid';
                    
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

                // HITUNG POIN SEKARANG
                $timeStr = $checkIn ? $checkIn->format('H:i:s') : null;
                $pointResult = $this->calculateAttendancePoints($timeStr, $status, false);
                $points = $pointResult['points'];
                $descArray = $pointResult['descriptions'];

                if ($points != 0) {
                    $teacherPoints[$teacher->id] += $points;

                    $transactionType = $points > 0 ? 'EARN' : 'PENALTY';

                    $pointLedgers[] = [
                        'teacher_id' => $teacher->id,
                        'transaction_type' => $transactionType,
                        'amount' => $points,
                        'current_balance' => $teacherPoints[$teacher->id],
                        'description' => implode(', ', $descArray) . ' (Generate Seeder)',
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }
            }

            // Insert data per hari untuk menjaga performa memori
            if (!empty($attendances)) {
                Attendance::insertOrIgnore($attendances);
            }
            
            if (!empty($attendanceLogs)) {
                AttendanceLog::insert($attendanceLogs);
            }

            if (!empty($pointLedgers)) {
                \App\Models\PointLedger::insert($pointLedgers);
                $totalLedgers += count($pointLedgers);
            }
        }

        // UPDATE SEMUA SALDO GURU BERDASARKAN HASIL SEEDER KESELURUHAN
        $this->command->info("Memperbarui saldo poin guru di tabel teacher...");
        foreach ($teacherPoints as $id => $balance) {
            Teacher::where('id', $id)->update(['point_balance' => $balance]);
        }

        $this->command->info("Berhasil menyemai data absensi 1 tahun & memasukkan {$totalLedgers} baris Point Ledger!");
    }
}