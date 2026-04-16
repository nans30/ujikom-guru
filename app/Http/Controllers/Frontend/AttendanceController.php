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

use App\Traits\AttendancePointTrait;

class AttendanceController extends Controller
{
    use AttendancePointTrait;
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
                // Tentukan menit telat & status secara otomatis
                $lateMinutes = $this->calculateLateMinutes($now->format('H:i:s'));
                $status = $lateMinutes > 0 ? 'telat' : 'hadir';

                // ==========================================
                // AUTO-USE TOKEN KOMPENSASI
                // ==========================================
                $usedToken = null;
                if ($lateMinutes > 0) {
                    $usedToken = \App\Models\TeacherToken::with('item')
                        ->where('teacher_id', $teacher->id)
                        ->where('status', 'AVAILABLE')
                        ->whereHas('item', function($q) {
                            $q->where('extra_minutes', '>', 0);
                        })
                        ->orderBy('id', 'asc')
                        ->first();

                    if ($usedToken) {
                        $extra = $usedToken->item->extra_minutes;
                        if ($extra >= $lateMinutes) {
                            $status = 'hadir';
                            $lateMinutes = 0;
                        } else {
                            // JIKA TELAT LEBIH DARI KAPASITAS ITEM: ABRESI KETENTUAN USER (Jangan pakai item, biar minus saja)
                            $usedToken = null; 
                        }
                    }
                }

                // ==========================================
                // KALKULASI POIN BERDASARKAN ATURAN (RULES)
                // ==========================================
                $pointData = $this->calculateAttendancePoints($now->format('H:i:s'), $status, $usedToken ? true : false);
                $pointsEarned = $pointData['points'];
                $matchingRulesDesc = $pointData['descriptions'];

                $attendance = Attendance::create([
                    'teacher_id'      => $teacher->id,
                    'date'            => $todayDate,
                    'check_in'        => $now,
                    'method_in'       => 'rfid',
                    'status'          => $status,
                    'photo_check_in'  => $photoPath,
                    'late_duration'   => $lateMinutes,
                    'point_earned'    => $pointsEarned, // Simpan poin di absensi
                    'is_token_used'   => $usedToken ? true : false,
                    'created_by_id'   => $teacher->user_id ?? 1 
                ]);

                // Update token menjadi terpakai
                if ($usedToken) {
                    $usedToken->update([
                        'status' => 'USED',
                        'used_at_attendance_id' => $attendance->id,
                        'used_at' => $now,
                    ]);
                }

                // Jika ada perubahan poin, update saldo guru dan catat di ledger
                if ($pointsEarned != 0) {
                    $teacher->point_balance += $pointsEarned;
                    $teacher->save();

                    \App\Models\PointLedger::create([
                        'teacher_id'       => $teacher->id,
                        'transaction_type' => $pointsEarned > 0 ? 'EARN' : 'PENALTY',
                        'amount'           => $pointsEarned,
                        'current_balance'  => $teacher->point_balance,
                        'description'      => 'Absen Masuk: ' . implode(', ', $matchingRulesDesc),
                    ]);
                }

                DB::commit();

                return response()->json([
                    'status'            => 'success',
                    'type'              => 'checkin',
                    'name'              => $teacher->name,
                    'time'              => $now->format('H:i:s'),
                    'attendance_status' => $status,
                    'is_token_used'     => $usedToken ? true : false,
                    'token_name'        => $usedToken ? $usedToken->item->item_name : null
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