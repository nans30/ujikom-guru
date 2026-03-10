<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Journal;
use App\Models\Schedule;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        // --- FILTER LOGIC ---
        // Ambil dari request, jika tidak ada gunakan bulan/tahun sekarang
        $selectedMonth = $request->get('month', Carbon::now()->month);
        $selectedYear = $request->get('year', Carbon::now()->year);

        // --- 1. Attendance Statistics for Selected Period ---
        $attendancesThisMonth = Attendance::where('teacher_id', $teacher->id)
            ->whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonth)
            ->get();

        $attendanceStats = [
            'hadir' => $attendancesThisMonth->where('status', 'hadir')->count(),
            'telat' => $attendancesThisMonth->where('status', 'telat')->count(),
            'izin'  => $attendancesThisMonth->where('status', 'izin')->count(),
            'sakit' => $attendancesThisMonth->where('status', 'sakit')->count(),
            'cuti'  => $attendancesThisMonth->where('status', 'cuti')->count(),
            'alpha' => $attendancesThisMonth->where('status', 'alpha')->count(),
            'total' => $attendancesThisMonth->count(),
            'terlambat_durasi' => $attendancesThisMonth->sum('late_duration'),
        ];

        // --- 2. Recent Attendances (Tetap tampilkan 5 terbaru tanpa terbatas filter bulan) ---
        $recentAttendances = Attendance::where('teacher_id', $teacher->id)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        // --- 3. Journal Statistics for Selected Period ---
        $journalsThisMonth = Journal::where('teacher_id', $teacher->id)
            ->whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonth)
            ->get();

        $journalStats = [
            'total'     => $journalsThisMonth->count(),
            'published' => $journalsThisMonth->where('status', 1)->count(),
            'draft'     => $journalsThisMonth->where('status', 0)->count(),
        ];

        // --- 4. Today's Schedule (Selalu hari ini) ---
        $todayName = Carbon::now()->format('D');
        $todaySchedules = Schedule::where('teacher_id', $teacher->id)
            ->where('day_of_week', $todayName)
            ->where('status', 1)
            ->orderBy('start_time', 'asc')
            ->get();

        return view('frontend.statistic.index', compact(
            'teacher',
            'attendanceStats',
            'recentAttendances',
            'journalStats',
            'todaySchedules',
            'selectedMonth',
            'selectedYear'
        ));
    }
}
