<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Models\Approval;
use App\Models\Holiday;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | TEACHER
        |--------------------------------------------------------------------------
        */

        $totalTeachers = Teacher::count();
        $activeTeachers = Teacher::where('is_active', true)->count();


        /*
        |--------------------------------------------------------------------------
        | ATTENDANCE TODAY
        |--------------------------------------------------------------------------
        */

        $attendanceToday = Attendance::whereDate('date', $today)->count();

        $presentToday = Attendance::whereDate('date', $today)
            ->whereIn('status', ['hadir', 'telat'])
            ->count();

        $lateToday = Attendance::whereDate('date', $today)
            ->where('status', 'telat')
            ->count();

        $alphaToday = Attendance::whereDate('date', $today)
            ->where('status', 'alpha')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | APPROVAL
        |--------------------------------------------------------------------------
        */

        $approvalPending = Approval::where('status', 'pending')->count();
        $approvalApproved = Approval::where('status', 'approved')->count();
        $approvalRejected = Approval::where('status', 'rejected')->count();


        /*
        |--------------------------------------------------------------------------
        | HOLIDAY
        |--------------------------------------------------------------------------
        */

        $holidayThisYear = Holiday::whereYear('date', now()->year)->count();

        $holidayThisMonth = Holiday::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();


        /*
        |--------------------------------------------------------------------------
        | RFID VS MANUAL (DONUT CHART)
        |--------------------------------------------------------------------------
        */

        $rfidAttendance = Attendance::where('method_in', 'rfid')->count();

        $manualAttendance = Attendance::where('method_in', 'manual')->count();


        /*
        |--------------------------------------------------------------------------
        | CHART DATA 7 HARI
        |--------------------------------------------------------------------------
        */

        $dates = [];
        $hadir = [];
        $telat = [];
        $izin = [];
        $alpha = [];

        for ($i = 6; $i >= 0; $i--) {

            $date = Carbon::today()->subDays($i);

            $dates[] = $date->format('d M');

            $hadir[] = Attendance::whereDate('date', $date)
                ->where('status', 'hadir')->count();

            $telat[] = Attendance::whereDate('date', $date)
                ->where('status', 'telat')->count();

            $izin[] = Attendance::whereDate('date', $date)
                ->whereIn('status', ['izin', 'sakit', 'cuti'])->count();

            $alpha[] = Attendance::whereDate('date', $date)
                ->where('status', 'alpha')->count();
        }

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
            'dates',
            'hadir',
            'telat',
            'izin',
            'alpha'
        ));
    }
}