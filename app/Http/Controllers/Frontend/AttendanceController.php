<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Models\Holiday;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('frontend.attendance.index');
    }

    /*
    ===============================
    CEK HARI LIBUR (API)
    ===============================
    */
    public function checkHoliday()
    {
        $today = now('Asia/Jakarta')->toDateString();

        $holiday = Holiday::whereDate('date', $today)->first();

        return response()->json([
            'is_holiday' => $holiday ? true : false,
            'name' => $holiday->name ?? null
        ]);
    }

    /*
    ===============================
    SCAN RFID
    ===============================
    */
    public function scan(Request $request)
    {
        DB::beginTransaction();

        try {

            /*
            ===============================
            VALIDASI
            ===============================
            */
            $request->validate([
                'uid'   => 'required|string',
                'photo' => 'required|file|image|max:2048'
            ]);

            /*
            ===============================
            CEK HARI LIBUR
            ===============================
            */
            $today = now('Asia/Jakarta')->toDateString();

            $holiday = Holiday::whereDate('date', $today)->first();

            if ($holiday) {

                DB::rollBack();

                return response()->json([
                    'status' => 'warning',
                    'message' => 'Hari ini libur: ' . $holiday->name
                ]);
            }

            /*
            ===============================
            CARI GURU
            ===============================
            */
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

            /*
            ===============================
            CEK ABSENSI HARI INI
            ===============================
            */
            $attendance = Attendance::where('teacher_id', $teacher->id)
                ->whereDate('date', $now->toDateString())
                ->lockForUpdate()
                ->first();

            /*
            ===============================
            SIMPAN FOTO
            ===============================
            */
            $photoPath = $request->file('photo')
                ->store('attendance/photos', 'public');

            /*
            ===============================
            CHECK IN
            ===============================
            */
            if (!$attendance) {

                $lateLimit = $now->copy()->setTime(11, 0, 0);
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

            /*
            ===============================
            CHECK OUT
            ===============================
            */
            if (!$attendance->check_out) {

                $checkoutLimit = $now->copy()->setTime(10, 0, 0);

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