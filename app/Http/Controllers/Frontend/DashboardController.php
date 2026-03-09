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

        // Ambil data guru yang terhubung dengan user login
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan untuk akun ini.');
        }

        $today = Carbon::today()->toDateString();
        $todayName = Carbon::now()->format('l'); // Menghasilkan "Monday", "Tuesday", dst.

        // 1. Ambil data absensi asli hari ini
        $todayAttendance = Attendance::where('teacher_id', $teacher->id)
            ->whereDate('date', $today)
            ->first();

        // 2. Ambil data jurnal asli milik guru ini
        $journals = Journal::with(['schedule'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        // 3. Hitung jadwal mengajar hari ini
        $todaySchedulesCount = Schedule::where('teacher_id', $teacher->id)
            ->where('day_of_week', $todayName)
            ->where('status', 1)
            ->count();

        return view('frontend.dashboards.index', compact(
            'todayAttendance',
            'journals',
            'todaySchedulesCount',
            'teacher'
        ));
    }
}