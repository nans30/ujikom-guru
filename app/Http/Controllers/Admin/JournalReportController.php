<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Journal;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class JournalReportController extends Controller
{
    public function index(Request $request)
    {
        $month  = $request->month ? intval($request->month) : null;
        $year   = $request->year  ? intval($request->year)  : null;
        $search = $request->search ?? '';

        $journals = Journal::select(
            'teacher_id',
            DB::raw('COUNT(*) as total_count'),
            DB::raw('COUNT(CASE WHEN status=1 THEN 1 END) as published_count'),
            DB::raw('COUNT(CASE WHEN status=0 THEN 1 END) as draft_count')
        )
            ->with('teacher')
            ->when($year, fn($q) => $q->whereYear('date', $year))
            ->when($month, fn($q) => $q->whereMonth('date', $month))
            ->when($search, fn($q) => $q->whereHas('teacher', fn($t) => $t->where('name', 'like', "%$search%")))
            ->groupBy('teacher_id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.journal.report', compact('journals', 'month', 'year', 'search'));
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

        $journals = Journal::select(
            'teacher_id',
            DB::raw('COUNT(*) as total_count'),
            DB::raw('COUNT(CASE WHEN status=1 THEN 1 END) as published_count'),
            DB::raw('COUNT(CASE WHEN status=0 THEN 1 END) as draft_count')
        )
            ->with('teacher')
            ->when($year, fn($q) => $q->whereYear('date', $year))
            ->when($month, fn($q) => $q->whereMonth('date', $month))
            ->when($search, fn($q) => $q->whereHas('teacher', fn($t) => $t->where('name', 'like', "%$search%")))
            ->groupBy('teacher_id')
            ->get();

        try {
            if ($type === 'pdf') {
                $pdf = Pdf::loadView('admin.journal.pdf', compact('journals', 'month', 'year', 'search'))
                    ->setPaper('a4', 'portrait')->setOptions([
                        'isHtml5ParserEnabled' => true,
                        'isRemoteEnabled' => true,
                    ]);

                $filename = "journal-report-" . ($month ?? 'all') . "-{$year}.pdf";
                return $pdf->download($filename);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray([
                ['No', 'Guru', 'Total Jurnal', 'Terbit (Published)', 'Draf (Draft)']
            ], null, 'A1');

            foreach ($journals as $i => $item) {
                $sheet->fromArray([
                    $i + 1,
                    $item->teacher->name ?? '-',
                    $item->total_count,
                    $item->published_count,
                    $item->draft_count
                ], null, 'A' . ($i + 2));
            }

            $writer = $type === 'excel' ? new Xlsx($spreadsheet) : new Csv($spreadsheet);
            $filename = "journal-report-" . ($month ?? 'all') . "-{$year}." . ($type === 'excel' ? 'xlsx' : 'csv');

            return Response::streamDownload(fn() => $writer->save('php://output'), $filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal generate ' . $type . ' : ' . $e->getMessage());
        }
    }
}
