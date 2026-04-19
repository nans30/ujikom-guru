<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Models\Approval;
use App\Models\Holiday;
use App\Models\Assessment;
use App\Models\Categorie;
use App\Models\AssessmentDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        /* --- TEACHER & ATTENDANCE STATS --- */
        $totalTeachers = Teacher::count();
        $activeTeachers = Teacher::where('is_active', true)->count();
        $attendanceToday = Attendance::whereDate('date', $today)->count();
        $presentToday = Attendance::whereDate('date', $today)->whereIn('status', ['hadir', 'telat'])->count();
        $lateToday = Attendance::whereDate('date', $today)->where('status', 'telat')->count();
        $alphaToday = Attendance::whereDate('date', $today)->where('status', 'alpha')->count();

        /* --- APPROVAL & HOLIDAY --- */
        $approvalPending = Approval::where('status', 'pending')->count();
        $approvalApproved = Approval::where('status', 'approved')->count();
        $approvalRejected = Approval::where('status', 'rejected')->count();
        $holidayThisYear = Holiday::whereYear('date', now()->year)->count();
        $holidayThisMonth = Holiday::whereMonth('date', now()->month)->whereYear('date', now()->year)->count();

        /* --- ATTENDANCE TREND (7 DAYS) --- */
        $rfidAttendance = Attendance::where('method_in', 'rfid')->count();
        $manualAttendance = Attendance::where('method_in', 'manual')->count();
        $faceIdAttendance = Attendance::where('method_in', 'face_id')->count();
        $dates = [];
        $hadir = [];
        $telat = [];
        $izin = [];
        $alpha = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dates[] = $date->format('d M');
            $dayStats = Attendance::whereDate('date', $date)->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
            $hadir[] = $dayStats['hadir'] ?? 0;
            $telat[] = $dayStats['telat'] ?? 0;
            $izin[]  = ($dayStats['izin'] ?? 0) + ($dayStats['sakit'] ?? 0) + ($dayStats['cuti'] ?? 0);
            $alpha[] = $dayStats['alpha'] ?? 0;
        }

        /* --- ASSESSMENT RADAR & BAR --- */
        $categories = Categorie::where('status', true)->get();
        $radarLabels = [];
        $radarData = [];
        foreach ($categories as $cat) {
            $radarLabels[] = $cat->name;
            $radarData[] = round(AssessmentDetail::where('category_id', $cat->id)->avg('score') ?? 0, 2);
        }

        $topAssessments = Assessment::with('evaluatee')->where('status', 1)
            ->withSum('details as total_score', 'score')
            ->orderBy('total_score', 'desc')->take(10)->get();

        $barTeacherNames = [];
        $barTotalScores = [];
        foreach ($topAssessments as $item) {
            $barTeacherNames[] = $item->evaluatee->name ?? 'Unknown';
            $barTotalScores[] = $item->total_score ?? 0;
        }

        /* --- NEW: ASSESSMENT TREND (6 MONTHS) --- */
        $assessmentTrendLabels = [];
        $assessmentTrendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $assessmentTrendLabels[] = $monthDate->format('M Y');
            $avgMonth = AssessmentDetail::whereHas('assessment', function ($q) use ($monthDate) {
                $q->whereMonth('assessment_date', $monthDate->month)->whereYear('assessment_date', $monthDate->year)->where('status', 1);
            })->avg('score') ?? 0;
            $assessmentTrendData[] = round($avgMonth, 2);
        }

        $currentAcYear = '2023/2024';
        $currentSem = '1';
        $assessedCount = Assessment::where('academic_year', $currentAcYear)->where('semester', $currentSem)->distinct('evaluatee_id')->count();
        $assessmentProgress = $activeTeachers > 0 ? round(($assessedCount / $activeTeachers) * 100) : 0;

        return view('admin.dashboard.index', compact(
            'totalTeachers',
            'activeTeachers',
            'attendanceToday',
            'presentToday',
            'lateToday',
            'alphaToday',
            'approvalPending',
            'approvalApproved',
            'approvalRejected',
            'holidayThisYear',
            'holidayThisMonth',
            'rfidAttendance',
            'manualAttendance',
            'faceIdAttendance',
            'approvalPending',
            'dates',
            'hadir',
            'telat',
            'izin',
            'alpha',
            'radarLabels',
            'radarData',
            'barTeacherNames',
            'barTotalScores',
            'assessmentProgress',
            'currentAcYear',
            'currentSem',
            'assessmentTrendLabels',
            'assessmentTrendData'
        ));
    }
}
