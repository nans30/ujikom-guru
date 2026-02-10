<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Attendance;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('frontend.attendance.index');
    }

    public function scan(Request $request)
    {
        DB::beginTransaction();

        try {

            $request->validate([
                'uid'   => 'required|string',
                'photo' => 'required|file|image|max:2048'
            ]);

            $uid = strtolower(trim($request->uid));

            $teacher = Teacher::whereRaw('LOWER(rfid_uid) = ?', [$uid])->first();

            if (!$teacher) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kartu tidak dikenal'
                ]);
            }

            $now = now('Asia/Jakarta');

            $attendance = Attendance::where('teacher_id', $teacher->id)
                ->whereDate('date', $now->toDateString())
                ->lockForUpdate()
                ->first();


            $photoPath = $request->file('photo')
                ->store('attendance/photos', 'public');

            /* ===============================
               CHECK IN
            =============================== */
            if (!$attendance) {

                $lateLimit = $now->copy()->setTime(7, 0, 0);
                $status = $now->gt($lateLimit) ? 'telat' : 'hadir';

                Attendance::create([
                    'teacher_id'      => $teacher->id,
                    'date'            => $now,
                    'check_in'        => $now,
                    'method_in'       => 'rfid',
                    'status'          => $status,
                    'photo_check_in'  => $photoPath,
                    'created_by_id'   => 1
                ]);

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'type'   => 'checkin',
                    'name'   => $teacher->name,
                    'time'   => $now->format('H:i:s'),
                    'attendance_status' => $status
                ]);
            }

            /* ===============================
               CHECK OUT
            =============================== */
            if (!$attendance->check_out) {

                // ⭐ FIX DI SINI
                $checkoutLimit = $now->copy()->setTime(14, 0, 0);

                Log::info([
                    'now' => $now->format('H:i:s'),
                    'limit' => $checkoutLimit->format('H:i:s')
                ]);

                if ($now->lt($checkoutLimit)) {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Belum waktunya absen pulang',
                        'allowed_time' => $checkoutLimit->format('H:i')
                    ]);
                }

                $attendance->update([
                    'check_out'       => $now,
                    'method_out'      => 'rfid',
                    'photo_check_out' => $photoPath
                ]);

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'type'   => 'checkout',
                    'name'   => $teacher->name,
                    'time'   => $now->format('H:i:s'),
                    'attendance_status' => 'pulang'
                ]);
            }

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah absen hari ini'
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Server error'
            ], 500);
        }
    }
}