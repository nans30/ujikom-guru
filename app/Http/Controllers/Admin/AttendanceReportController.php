<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Facades\Response;

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month;
        $year  = $request->year;

        $query = Attendance::with('teacher');

        if ($month) $query->whereMonth('date', $month);
        if ($year) $query->whereYear('date', $year);

        $attendances = $query->latest()->get();

        // Sesuai view kamu
        return view('admin.attendance.report', compact('attendances', 'month', 'year'));
    }

    public function export(Request $request, $type)
    {
        $month = $request->month;
        $year  = $request->year;

        $attendances = Attendance::with('teacher')
            ->when($month, fn($q) => $q->whereMonth('date', $month))
            ->when($year, fn($q) => $q->whereYear('date', $year))
            ->latest()
            ->get();

        // ----------------------------
        // PDF
        // ----------------------------
        if ($type === 'pdf') {
            $pdf = Pdf::loadView('admin.attendance.pdf', compact('attendances', 'month', 'year'));
            return $pdf->download('attendance-report.pdf');
        }

        // ----------------------------
        // Excel XLSX
        // ----------------------------
        if ($type === 'excel') {
            $spreadsheet = $this->generateSpreadsheet($attendances);
            $writer = new Xlsx($spreadsheet);

            $filename = 'attendance-report.xlsx';
            return Response::streamDownload(fn() => $writer->save('php://output'), $filename);
        }

        // ----------------------------
        // CSV
        // ----------------------------
        if ($type === 'csv') {
            $spreadsheet = $this->generateSpreadsheet($attendances);
            $writer = new Csv($spreadsheet);

            $filename = 'attendance-report.csv';
            return Response::streamDownload(fn() => $writer->save('php://output'), $filename);
        }

        abort(404);
    }

    // ----------------------------
    // Helper untuk generate Spreadsheet
    // ----------------------------
    private function generateSpreadsheet($attendances)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Teacher');
        $sheet->setCellValue('C1', 'Date');
        $sheet->setCellValue('D1', 'Check In');
        $sheet->setCellValue('E1', 'Check Out');
        $sheet->setCellValue('F1', 'Method');
        $sheet->setCellValue('G1', 'Status');

        foreach ($attendances as $index => $item) {
            $sheet->setCellValue('A' . ($index + 2), $index + 1);
            $sheet->setCellValue('B' . ($index + 2), $item->teacher->name ?? '-');
            $sheet->setCellValue('C' . ($index + 2), $item->date);
            $sheet->setCellValue('D' . ($index + 2), $item->check_in ?? '-');
            $sheet->setCellValue('E' . ($index + 2), $item->check_out ?? '-');
            $sheet->setCellValue('F' . ($index + 2), strtoupper($item->method_in ?? '-') . ' / ' . strtoupper($item->method_out ?? '-'));
            $sheet->setCellValue('G' . ($index + 2), ucfirst($item->status));
        }

        return $spreadsheet;
    }
}