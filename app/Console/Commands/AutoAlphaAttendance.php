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

        DB::beginTransaction();

        try {

            $teachers = Teacher::select('id')->get();

            $alphaCount = 0;

            foreach ($teachers as $teacher) {

                // cek apakah sudah punya absensi hari ini
                $exists = Attendance::where('teacher_id', $teacher->id)
                    ->whereDate('date', $today)
                    ->exists();

                // kalau tidak ada sama sekali → alpha
                if (!$exists) {

                    Attendance::create([
                        'teacher_id'    => $teacher->id,
                        'date'          => $today,
                        'check_in'      => null,
                        'check_out'     => null,
                        'method_in'     => null,
                        'method_out'    => null,
                        'status'        => 'alpha',
                        'created_by_id' => 1, // system
                    ]);

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