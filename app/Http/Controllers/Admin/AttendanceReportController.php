<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $month  = $request->month ? intval($request->month) : null;
        $year   = $request->year  ? intval($request->year)  : null;
        $search = $request->search ?? '';

        $attendances = Attendance::select(
            'teacher_id',
            DB::raw('COUNT(CASE WHEN status="hadir" THEN 1 END) as hadir_count'),
            DB::raw('COUNT(CASE WHEN status="telat" THEN 1 END) as telat_count'),
            DB::raw('COUNT(CASE WHEN status="izin" THEN 1 END) as izin_count'),
            DB::raw('COUNT(CASE WHEN status="sakit" THEN 1 END) as sakit_count'),
            DB::raw('COUNT(CASE WHEN status="cuti" THEN 1 END) as cuti_count'),
            DB::raw('COUNT(CASE WHEN status="alpha" THEN 1 END) as alpha_count')
        )
            ->with('teacher')
            ->when($year, fn($q) => $q->whereYear('date', $year))
            ->when($month, fn($q) => $q->whereMonth('date', $month))
            ->when($search, fn($q) => $q->whereHas('teacher', fn($t) => $t->where('name', 'like', "%$search%")))
            ->groupBy('teacher_id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.attendance.report', compact('attendances', 'month', 'year', 'search'));
    }

    public function export(Request $request, $type)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $month  = $request->month ? intval($request->month) : null;
        $year   = $request->year  ? intval($request->year)  : null;
        $search = $request->search ?? '';

        if (empty($year)) {
            return redirect()->back()->with('error', 'Silakan pilih tahun sebelum export.');
        }

        $attendances = Attendance::select(
            'teacher_id',
            DB::raw('COUNT(CASE WHEN status="hadir" THEN 1 END) as hadir_count'),
            DB::raw('COUNT(CASE WHEN status="telat" THEN 1 END) as telat_count'),
            DB::raw('COUNT(CASE WHEN status="izin" THEN 1 END) as izin_count'),
            DB::raw('COUNT(CASE WHEN status="sakit" THEN 1 END) as sakit_count'),
            DB::raw('COUNT(CASE WHEN status="cuti" THEN 1 END) as cuti_count'),
            DB::raw('COUNT(CASE WHEN status="alpha" THEN 1 END) as alpha_count')
        )
            ->with('teacher')
            ->when($year, fn($q) => $q->whereYear('date', $year))
            ->when($month, fn($q) => $q->whereMonth('date', $month))
            ->when($search, fn($q) => $q->whereHas('teacher', fn($t) => $t->where('name', 'like', "%$search%")))
            ->groupBy('teacher_id')
            ->get();

        try {
            if ($type === 'pdf') {
                $pdf = Pdf::loadView('admin.attendance.pdf', compact('attendances', 'month', 'year', 'search'))
                    ->setPaper('a4', 'portrait')->setOptions([
                        'isHtml5ParserEnabled' => true,
                        'isRemoteEnabled' => true,
                    ]);

                $filename = "attendance-report-" . ($month ?? 'all') . "-{$year}.pdf";
                return $pdf->download($filename);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray([
                ['No', 'Teacher', 'Hadir', 'Telat', 'Izin', 'Sakit', 'Cuti', 'Alpha']
            ], null, 'A1');

            foreach ($attendances as $i => $item) {
                $sheet->fromArray([
                    $i + 1,
                    $item->teacher->name ?? '-',
                    $item->hadir_count,
                    $item->telat_count,
                    $item->izin_count,
                    $item->sakit_count,
                    $item->cuti_count,
                    $item->alpha_count
                ], null, 'A' . ($i + 2));
            }

            $writer = $type === 'excel' ? new Xlsx($spreadsheet) : new Csv($spreadsheet);
            $filename = "attendance-report-" . ($month ?? 'all') . "-{$year}." . ($type === 'excel' ? 'xlsx' : 'csv');

            return Response::streamDownload(fn() => $writer->save('php://output'), $filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal generate ' . $type . ' : ' . $e->getMessage());
        }
    }
}