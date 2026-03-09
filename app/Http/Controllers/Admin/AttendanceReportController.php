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

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $month  = $request->month ? intval($request->month) : null;
        $year   = $request->year  ? intval($request->year)  : null;
        $search = $request->search ?? '';
        $method = $request->method ?? '';
        $status = $request->status ?? '';

        $attendances = Attendance::with('teacher')
            ->when($month, fn($q) => $q->whereMonth('date', $month))
            ->when($year, fn($q) => $q->whereYear('date', $year))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when(
                $search,
                fn($q) => $q->whereHas(
                    'teacher',
                    fn($t) => $t->where('name', 'like', "%$search%")
                )
            )
            ->when($method, function ($q) use ($method) {
                if ($method === 'none') {
                    $q->whereNull('method_in')->whereNull('method_out');
                } else {
                    $q->where(function ($query) use ($method) {
                        $query->where('method_in', $method)
                            ->orWhere('method_out', $method);
                    });
                }
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.attendance.report', compact(
            'attendances',
            'month',
            'year',
            'search',
            'method',
            'status'
        ));
    }

    public function export(Request $request, $type)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $month  = $request->month ? intval($request->month) : null;
        $year   = $request->year  ? intval($request->year)  : null;
        $search = $request->search ?? '';
        $method = $request->method ?? '';
        $status = $request->status ?? '';

        // Wajib pilih bulan & tahun untuk semua tipe export
        if (empty($month) || empty($year)) {
            return redirect()->back()
                ->with('error', 'Silakan pilih bulan dan tahun sebelum export.');
        }

        $attendances = Attendance::with('teacher')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->when($status, fn($q) => $q->where('status', $status))
            ->when(
                $search,
                fn($q) => $q->whereHas(
                    'teacher',
                    fn($t) => $t->where('name', 'like', "%$search%")
                )
            )
            ->when($method, function ($q) use ($method) {
                if ($method === 'none') {
                    $q->whereNull('method_in')->whereNull('method_out');
                } else {
                    $q->where(function ($query) use ($method) {
                        $query->where('method_in', $method)
                            ->orWhere('method_out', $method);
                    });
                }
            })
            ->latest()
            ->get();

        try {
            if ($type === 'pdf') {
                $pdf = Pdf::loadView('admin.attendance.pdf', compact(
                    'attendances',
                    'month',
                    'year',
                    'search',
                    'method',
                    'status'
                ))->setPaper('a4', 'portrait')
                    ->setOptions([
                        'isHtml5ParserEnabled' => true,
                        'isRemoteEnabled' => true,
                    ]);

                $filename = 'attendance-report-' . $month . '-' . $year . '.pdf';
                return $pdf->download($filename);
            }

            // Excel / CSV
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray([['No', 'Teacher', 'Date', 'Check In', 'Check Out', 'Method', 'Status']], null, 'A1');

            foreach ($attendances as $index => $item) {
                $sheet->fromArray([
                    $index + 1,
                    $item->teacher->name ?? '-',
                    \Carbon\Carbon::parse($item->date)->format('d-m-Y'),
                    $item->check_in ?? '-',
                    $item->check_out ?? '-',
                    strtoupper((string)($item->method_in ?? '-')) . ' / ' . strtoupper((string)($item->method_out ?? '-')),
                    ucfirst($item->status)
                ], null, 'A' . ($index + 2));
            }

            $writer = $type === 'excel' ? new Xlsx($spreadsheet) : new Csv($spreadsheet);
            $filename = "attendance-report-" . $month . "-" . $year . "." . ($type === 'excel' ? 'xlsx' : 'csv');

            return Response::streamDownload(fn() => $writer->save('php://output'), $filename);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal generate ' . $type . ' : ' . $e->getMessage());
        }
    }
}