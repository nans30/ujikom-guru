<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Journal;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $today = Carbon::today()->toDateString();

        // Pastikan day_of_week di DB cocok dengan format ini. 
        // Menggunakan format('D') menghasilkan format 3 huruf bahasa inggris: Mon, Tue, Wed sesuai data DB
        $todayName = Carbon::now()->format('D');

        // 1. Ambil absensi
        $todayAttendance = Attendance::with(['usedToken.item'])->where('teacher_id', $teacher->id)
            ->whereDate('date', $today)
            ->first();

        // 2. Ambil Jurnal
        $journals = Journal::with(['schedule'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        // 3. Ambil jadwal hari ini (bukan sekadar count)
        $todaySchedules = Schedule::where('teacher_id', $teacher->id)
            ->where('day_of_week', $todayName)
            ->where('status', 1)
            ->orderBy('start_time', 'asc')
            ->get();

        // Check journal status for each schedule
        $todaySchedules->map(function ($schedule) use ($today) {
            $schedule->has_journal = Journal::where('schedule_id', $schedule->id)
                ->where('date', $today)
                ->exists();
            return $schedule;
        });

        $todaySchedulesCount = $todaySchedules->count();

        // Count available tokens
        $availableTokensCount = \App\Models\TeacherToken::where('teacher_id', $teacher->id)
            ->where('status', 'AVAILABLE')
            ->count();

        return view('frontend.dashboards.index', compact(
            'todayAttendance',
            'journals',
            'todaySchedules',
            'todaySchedulesCount',
            'availableTokensCount',
            'teacher'
        ));
    }
}
