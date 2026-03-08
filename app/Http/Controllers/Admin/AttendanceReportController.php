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
        $month  = $request->month ?? '';
        $year   = $request->year ?? '';
        $search = $request->search ?? '';
        $method = $request->method ?? '';

        $attendances = Attendance::with('teacher')
            ->when($month, fn($q) => $q->whereMonth('date', $month))
            ->when($year, fn($q) => $q->whereYear('date', $year))
            ->when($search, fn($q) => $q->whereHas('teacher', fn($t) => $t->where('name', 'like', "%$search%")))
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
            'method'
        ));
    }

    public function export(Request $request, $type)
    {
        $month  = $request->month ?? '';
        $year   = $request->year ?? '';
        $search = $request->search ?? '';
        $method = $request->method ?? '';

        $attendances = Attendance::with('teacher')
            ->when($month, fn($q) => $q->whereMonth('date', $month))
            ->when($year, fn($q) => $q->whereYear('date', $year))
            ->when($search, fn($q) => $q->whereHas('teacher', fn($t) => $t->where('name', 'like', "%$search%")))
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

        if ($type === 'pdf') {

            $pdf = Pdf::loadView('admin.attendance.pdf', compact(
                'attendances',
                'month',
                'year',
                'search',
                'method'
            ));

            return $pdf->download('attendance-report.pdf');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['No', 'Teacher', 'Date', 'Check In', 'Check Out', 'Method', 'Status']
        ], null, 'A1');

        foreach ($attendances as $index => $item) {

            $sheet->fromArray([
                $index + 1,
                $item->teacher->name ?? '-',
                $item->date,
                $item->check_in ?? '-',
                $item->check_out ?? '-',
                strtoupper($item->method_in ?? '-') . ' / ' . strtoupper($item->method_out ?? '-'),
                ucfirst($item->status)
            ], null, 'A' . ($index + 2));
        }

        $writer = $type === 'excel'
            ? new Xlsx($spreadsheet)
            : new Csv($spreadsheet);

        $filename = "attendance-report." . ($type === 'excel' ? 'xlsx' : 'csv');

        return Response::streamDownload(
            fn() => $writer->save('php://output'),
            $filename
        );
    }
}