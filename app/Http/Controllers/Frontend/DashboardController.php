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
    // Jika di DB pakai Bahasa Indonesia (Senin, Selasa), gunakan ->translatedFormat('l')
    $todayName = Carbon::now()->isoFormat('dddd'); 

    // 1. Ambil absensi
    $todayAttendance = Attendance::where('teacher_id', $teacher->id)
        ->whereDate('date', $today)
        ->first();

    // 2. Ambil Jurnal
    $journals = Journal::with(['schedule'])
        ->where('teacher_id', $teacher->id)
        ->orderBy('date', 'desc')
        ->take(5)
        ->get();

    // 3. Hitung jadwal (Gunakan nama hari yang sesuai dengan isi kolom di DB)
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