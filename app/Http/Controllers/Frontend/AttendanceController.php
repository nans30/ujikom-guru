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

    /**
     * RFID SCAN API
     */
    public function scan(Request $request)
    {
        DB::beginTransaction();

        try {

            /* ===============================
               VALIDATION
            =============================== */
            $request->validate([
                'uid'   => 'required|string',
                'photo' => 'required|file|image|max:2048'
            ]);

            $uid = strtolower(trim($request->uid));

            Log::info('RFID scan request', [
                'uid' => $uid,
                'ip'  => $request->ip()
            ]);

            /* ===============================
               CEK GURU
            =============================== */
            $teacher = Teacher::whereRaw('LOWER(rfid_uid) = ?', [$uid])->first();

            if (!$teacher) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'Kartu tidak dikenal'
                ]);
            }

            $now   = now();
            $todayStart = now()->startOfDay();
            $todayEnd   = now()->endOfDay();

            /* ===============================
               CEK ABSENSI HARI INI (SUPER FIX)
            =============================== */
            $attendance = Attendance::where('teacher_id', $teacher->id)
                ->whereBetween('date', [$todayStart, $todayEnd])
                ->lockForUpdate()
                ->first();

            /* ===============================
               UPLOAD FOTO
            =============================== */
            $photoPath = $request->file('photo')
                ->store('attendance/photos', 'public');

            /* ===============================
               STATUS HADIR / TELAT
            =============================== */
            $lateLimit = now()->setTime(20, 0);
            $status = $now->gt($lateLimit) ? 'telat' : 'hadir';

            /* ===============================
               CHECK IN
            =============================== */
            if (!$attendance) {

                $attendance = Attendance::create([
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

            /* ===============================
               SUDAH ABSEN
            =============================== */
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah absen check-in/check-out hari ini'
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Attendance error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Server error'
            ], 500);
        }
    }
}