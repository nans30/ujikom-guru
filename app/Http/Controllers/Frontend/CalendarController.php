<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Holiday;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        // --- FILTER LOGIC ---
        // Get month and year from request, default to current month/year
        $selectedMonth = $request->get('month', Carbon::now()->month);
        $selectedYear = $request->get('year', Carbon::now()->year);

        // Validasi input tahun & bulan
        $selectedMonth = (int) $selectedMonth;
        $selectedYear = (int) $selectedYear;
        if ($selectedMonth < 1 || $selectedMonth > 12) $selectedMonth = Carbon::now()->month;
        if ($selectedYear < 2000 || $selectedYear > 2100) $selectedYear = Carbon::now()->year;

        // --- 1. Create Calendar Date Object ---
        $dateObj = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
        $daysInMonth = $dateObj->daysInMonth;

        // Find which day of the week the month starts on (1 = Monday, 7 = Sunday)
        // Carbon isoWeekday(): 1 (monday) to 7 (sunday)
        $startDayOfWeek = $dateObj->isoWeekday();

        // --- 2. Fetch Data for the Month ---

        // Attendances
        $attendances = Attendance::where('teacher_id', $teacher->id)
            ->whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonth)
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        // Holidays
        $holidays = Holiday::where('status', 1)
            ->whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonth)
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        // --- 3. Build Calendar Grid ---
        $calendarGrid = [];
        $dayCounter = 1;

        // Total cells in a grid (e.g. 5 weeks x 7 days = 35, or 6x7 = 42)
        // We'll calculate exactly how many rows we need.
        $totalCells = ceil(($daysInMonth + $startDayOfWeek - 1) / 7) * 7;

        for ($i = 1; $i <= $totalCells; $i++) {
            if ($i < $startDayOfWeek || $dayCounter > $daysInMonth) {
                // Empty cell (padding at start or end of month)
                $calendarGrid[] = [
                    'day' => null,
                    'is_weekend' => false,
                    'attendance' => null,
                    'holiday' => null,
                    'date_string' => null
                ];
            } else {
                // Actual day cell
                $currentDate = Carbon::create($selectedYear, $selectedMonth, $dayCounter);
                $dateString = $currentDate->format('Y-m-d');
                $isWeekend = $currentDate->isWeekend(); // Saturday or Sunday

                $calendarGrid[] = [
                    'day' => $dayCounter,
                    'is_weekend' => $isWeekend,
                    'attendance' => $attendances->get($dateString),
                    'holiday' => $holidays->get($dateString),
                    'date_string' => $dateString
                ];
                $dayCounter++;
            }
        }

        // --- 4. Navigation Links Data ---
        $prevMonth = $dateObj->copy()->subMonth();
        $nextMonth = $dateObj->copy()->addMonth();

        return view('frontend.calendar.index', compact(
            'teacher',
            'selectedMonth',
            'selectedYear',
            'dateObj',
            'calendarGrid',
            'prevMonth',
            'nextMonth'
        ));
    }
}
