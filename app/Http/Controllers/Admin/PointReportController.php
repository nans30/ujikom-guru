<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointLedger;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Facades\Response;

class PointReportController extends Controller
{
    /**
     * History Transaksi (Global)
     */
    public function index(Request $request)
    {
        $teacherId = $request->get('teacher_id');
        $type = $request->get('type');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $ledgers = PointLedger::with('teacher')
            ->when($teacherId, function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })
            ->when($type, function ($q) use ($type) {
                $q->where('transaction_type', $type);
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereDate('created_at', '<=', $endDate);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $teachers = Teacher::orderBy('name', 'asc')->get();

        return view('admin.reports.points', compact('ledgers', 'teachers'));
    }

    /**
     * Laporan Harian
     */
    public function daily(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        
        $ledgers = PointLedger::with('teacher')
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.reports.points_daily', compact('ledgers', 'date'));
    }

    /**
     * Laporan Bulanan (Summary)
     */
    public function monthly(Request $request)
    {
        $month = intval($request->get('month', date('n')));
        $year = intval($request->get('year', date('Y')));

        $summary = PointLedger::select(
            'teacher_id',
            DB::raw("SUM(CASE WHEN transaction_type = 'EARN' THEN amount ELSE 0 END) as total_earned"),
            DB::raw("SUM(CASE WHEN transaction_type = 'SPEND' THEN ABS(amount) ELSE 0 END) as total_spent"),
            DB::raw("SUM(CASE WHEN transaction_type = 'PENALTY' THEN ABS(amount) ELSE 0 END) as total_penalty")
        )
            ->with('teacher')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy('teacher_id')
            ->get();

        return view('admin.reports.points_monthly', compact('summary', 'month', 'year'));
    }

    /**
     * Export Unified
     */
    public function export(Request $request, $scope, $type)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        if ($scope === 'daily') {
            $date = $request->get('date', date('Y-m-d'));
            $data = PointLedger::with('teacher')->whereDate('created_at', $date)->orderBy('created_at', 'asc')->get();
            $title = "Point Daily Report - " . $date;
            $view = 'admin.reports.points_daily_pdf';
            $compactArr = compact('data', 'date');
        } else {
            $month = intval($request->get('month', date('n')));
            $year = intval($request->get('year', date('Y')));
            $data = PointLedger::select(
                'teacher_id',
                DB::raw("SUM(CASE WHEN transaction_type = 'EARN' THEN amount ELSE 0 END) as total_earned"),
                DB::raw("SUM(CASE WHEN transaction_type = 'SPEND' THEN ABS(amount) ELSE 0 END) as total_spent"),
                DB::raw("SUM(CASE WHEN transaction_type = 'PENALTY' THEN ABS(amount) ELSE 0 END) as total_penalty")
            )
                ->with('teacher')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->groupBy('teacher_id')
                ->get();
            $title = "Point Monthly Summary - " . $month . "-" . $year;
            $view = 'admin.reports.points_monthly_pdf';
            $compactArr = compact('data', 'month', 'year');
        }

        if ($type === 'pdf') {
            $pdf = Pdf::loadView($view, $compactArr)->setPaper('a4', 'portrait');
            return $pdf->download($title . ".pdf");
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if ($scope === 'daily') {
            $sheet->fromArray([['No', 'Time', 'Teacher', 'Type', 'Amount', 'BalanceAfter', 'Description']], null, 'A1');
            foreach ($data as $i => $row) {
                $sheet->fromArray([
                    $i + 1,
                    $row->created_at->format('H:i:s'),
                    $row->teacher->name ?? '-',
                    $row->transaction_type,
                    $row->amount,
                    $row->current_balance,
                    $row->description
                ], null, 'A' . ($i + 2));
            }
        } else {
            $sheet->fromArray([['No', 'Teacher', 'Total Earned', 'Total Spent', 'Total Penalty', 'Net (Monthly)']], null, 'A1');
            foreach ($data as $i => $row) {
                $sheet->fromArray([
                    $i + 1,
                    $row->teacher->name ?? '-',
                    $row->total_earned,
                    $row->total_spent,
                    $row->total_penalty,
                    $row->total_earned - $row->total_spent - $row->total_penalty
                ], null, 'A' . ($i + 2));
            }
        }

        $writer = $type === 'excel' ? new Xlsx($spreadsheet) : new Csv($spreadsheet);
        $filename = $title . "." . ($type === 'excel' ? 'xlsx' : 'csv');

        return Response::streamDownload(fn() => $writer->save('php://output'), $filename);
    }
}
