<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\AttendanceLog;
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
        $today = now('Asia/Jakarta');
        $todayDate = $today->toDateString();

        // Cek holiday resmi
        $holiday = Holiday::whereDate('date', $todayDate)->first();

        // Cek weekend
        $isWeekend = in_array($today->dayOfWeekIso, [6, 7]); // 6 = Sabtu, 7 = Minggu

        return response()->json([
            'is_holiday'       => $holiday ? true : ($isWeekend ? true : false),
            'type'             => $holiday ? 'holiday' : ($isWeekend ? 'weekend' : 'none'),
            'name'             => $holiday->name ?? ($isWeekend ? ($today->dayOfWeekIso == 6 ? 'Sabtu' : 'Minggu') : null),
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

            $request->validate([
                'uid'   => 'required|string',
                'photo' => 'required|file|image|max:2048'
            ]);

            $now       = now('Asia/Jakarta');
            $todayDate = $now->toDateString();

            // Cek holiday resmi
            $holiday = Holiday::whereDate('date', $todayDate)->first();

            // Cek weekend
            $isWeekend = in_array($now->dayOfWeekIso, [6, 7]);

            if ($holiday || $isWeekend) {
                DB::rollBack();

                $message = $holiday
                    ? 'Hari ini libur resmi: ' . $holiday->name
                    : 'Hari ini libur: ' . ($now->dayOfWeekIso == 6 ? 'Sabtu' : 'Minggu');

                return response()->json([
                    'status'  => 'warning',
                    'message' => $message,
                    'type'    => $holiday ? 'holiday' : 'weekend'
                ]);
            }

            // Cari guru
            $uid = strtolower(trim($request->uid));
            $teacher = Teacher::whereRaw('LOWER(rfid_uid) = ?', [$uid])->first();

            if (!$teacher) {
                DB::rollBack();

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Kartu tidak dikenal'
                ]);
            }

            // Anti double tap (5 detik)
            $lastLog = AttendanceLog::where('teacher_id', $teacher->id)
                ->latest('scan_time')
                ->lockForUpdate()
                ->first();

            if ($lastLog && $lastLog->scan_time->diffInSeconds($now) < 5) {
                DB::rollBack();

                return response()->json([
                    'status'  => 'warning',
                    'message' => 'Tunggu beberapa detik sebelum scan lagi'
                ]);
            }

            // Simpan foto
            $photoPath = $request->file('photo')->store('attendance/photos', 'public');

            // Simpan log
            AttendanceLog::create([
                'teacher_id' => $teacher->id,
                'scan_time'  => $now,
            ]);

            // Cek absensi hari ini
            $attendance = Attendance::where('teacher_id', $teacher->id)
                ->whereDate('date', $todayDate)
                ->lockForUpdate()
                ->first();

            // CHECK IN
            if (!$attendance) {
                $lateLimit = $now->copy()->setTime(8, 0, 0);
                $status = $now->gt($lateLimit) ? 'telat' : 'hadir';

                Attendance::create([
                    'teacher_id'      => $teacher->id,
                    'date'            => $todayDate,
                    'check_in'        => $now,
                    'method_in'       => 'rfid',
                    'status'          => $status,
                    'photo_check_in'  => $photoPath,
                    'created_by_id'   => 1
                ]);

                DB::commit();

                return response()->json([
                    'status'            => 'success',
                    'type'              => 'checkin',
                    'name'              => $teacher->name,
                    'time'              => $now->format('H:i:s'),
                    'attendance_status' => $status
                ]);
            }

            // CHECK OUT
            if (!$attendance->check_out) {
                $checkoutLimit = $now->copy()->setTime(8, 0, 0);

                if ($now->lt($checkoutLimit)) {
                    DB::rollBack();

                    return response()->json([
                        'status'       => 'error',
                        'message'      => 'Belum waktunya absen pulang',
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
                    'status'            => 'success',
                    'type'              => 'checkout',
                    'name'              => $teacher->name,
                    'time'              => $now->format('H:i:s'),
                    'attendance_status' => 'pulang'
                ]);
            }

            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Anda sudah absen hari ini'
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Server error'
            ], 500);
        }
    }
}