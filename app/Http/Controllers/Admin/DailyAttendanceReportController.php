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
use Carbon\Carbon;

class DailyAttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $start_date = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date   = $request->end_date ?? Carbon::now()->format('Y-m-d');
        $search     = $request->search ?? '';

        $attendances = Attendance::with('teacher')
            ->whereBetween('date', [$start_date, $end_date])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('teacher', function ($t) use ($search) {
                    $t->where('name', 'like', "%$search%");
                });
            })
            ->orderBy('date', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.attendance.daily-report', compact('attendances', 'start_date', 'end_date', 'search'));
    }

    public function export(Request $request, $type)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $start_date = $request->start_date;
        $end_date   = $request->end_date;
        $search     = $request->search ?? '';

        if (empty($start_date) || empty($end_date)) {
            return redirect()->back()->with('error', 'Silakan tentukan range tanggal sebelum export.');
        }

        $attendances = Attendance::with('teacher')
            ->whereBetween('date', [$start_date, $end_date])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('teacher', function ($t) use ($search) {
                    $t->where('name', 'like', "%$search%");
                });
            })
            ->orderBy('date', 'desc')
            ->get();

        try {
            if ($type === 'pdf') {
                $pdf = Pdf::loadView('admin.attendance.daily-pdf', compact('attendances', 'start_date', 'end_date', 'search'))
                    ->setPaper('a4', 'landscape')->setOptions([
                        'isHtml5ParserEnabled' => true,
                        'isRemoteEnabled' => true,
                    ]);

                $filename = "daily-attendance-{$start_date}-to-{$end_date}.pdf";
                return $pdf->download($filename);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray([
                ['No', 'Tanggal', 'Guru', 'Status', 'Jam Masuk', 'Jam Pulang', 'Durasi Telat', 'Keterangan']
            ], null, 'A1');

            foreach ($attendances as $i => $item) {
                $countIn  = $item->check_in ? $item->check_in->format('H:i:s') : '-';
                $countOut = $item->check_out ? $item->check_out->format('H:i:s') : '-';

                $sheet->fromArray([
                    $i + 1,
                    $item->date->format('Y-m-d'),
                    $item->teacher->name ?? '-',
                    ucfirst($item->status),
                    $countIn,
                    $countOut,
                    $item->late_duration ?? '-',
                    $item->reason ?? '-'
                ], null, 'A' . ($i + 2));
            }

            $writer = $type === 'excel' ? new Xlsx($spreadsheet) : new Csv($spreadsheet);
            $ext = $type === 'excel' ? 'xlsx' : 'csv';
            $filename = "daily-attendance-{$start_date}-to-{$end_date}.{$ext}";

            return Response::streamDownload(fn() => $writer->save('php://output'), $filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal generate ' . $type . ' : ' . $e->getMessage());
        }
    }
}
