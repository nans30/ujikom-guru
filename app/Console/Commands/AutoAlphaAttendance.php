<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Teacher;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;

class AutoAlphaAttendance extends Command
{
    /**
     * php artisan app:auto-alpha-attendance
     */
    protected $signature = 'app:auto-alpha-attendance';

    protected $description = 'Auto set alpha for teachers without attendance today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Start auto alpha checking...');

        $today = now('Asia/Jakarta')->toDateString();

        // Cari aturan poin untuk alpha
        $alphaRule = \App\Models\Point::where('name', 'alpha')
            ->where('status', 1)
            ->first();

        $pointsToDeduct = $alphaRule ? $alphaRule->point_modifier : -10; // Default -10 jika aturan tidak ada

        DB::beginTransaction();

        try {

            $teachers = Teacher::select('id', 'point_balance')->get();

            $alphaCount = 0;

            foreach ($teachers as $teacher) {

                // cek apakah sudah punya absensi hari ini
                $exists = Attendance::where('teacher_id', $teacher->id)
                    ->whereDate('date', $today)
                    ->exists();

                // kalau tidak ada sama sekali → alpha
                if (!$exists) {

                    $attendance = Attendance::create([
                        'teacher_id'    => $teacher->id,
                        'date'          => $today,
                        'check_in'      => null,
                        'check_out'     => null,
                        'method_in'     => null,
                        'method_out'    => null,
                        'status'        => 'alpha',
                        'created_by_id' => 1, // system
                    ]);

                    // Potong Poin
                    if ($pointsToDeduct != 0) {
                        $teacher->point_balance += $pointsToDeduct; // modifier biasanya negatif, misal -10
                        $teacher->save();

                        \App\Models\PointLedger::create([
                            'teacher_id'       => $teacher->id,
                            'transaction_type' => 'PENALTY',
                            'amount'           => $pointsToDeduct,
                            'current_balance'  => $teacher->point_balance,
                            'description'      => 'Alpha: Tidak ada keterangan absensi hari ini (' . $today . ')',
                        ]);
                    }

                    $alphaCount++;
                }
            }

            DB::commit();

            $this->info("Done. {$alphaCount} teacher(s) marked as alpha.");
        } catch (\Throwable $e) {

            DB::rollBack();

            $this->error($e->getMessage());
        }
    }
}