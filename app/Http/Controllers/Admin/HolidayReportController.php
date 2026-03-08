<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Facades\Response;

class HolidayReportController extends Controller
{
    private $statusMap = [
        'active' => 1,
        'inactive' => 0,
    ];

    /**
     * Halaman Report
     */
    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $statusKey = $request->status ?? '';

        $statusValue = $this->statusMap[$statusKey] ?? null;

        $holidays = Holiday::query()

            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })

            ->when(!is_null($statusValue), function ($q) use ($statusValue) {
                $q->where('status', $statusValue);
            })

            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.holiday.report', compact(
            'holidays',
            'search',
            'statusKey'
        ));
    }

    /**
     * Export Report
     */
    public function export(Request $request, $type)
    {
        $search = $request->search ?? '';
        $statusKey = $request->status ?? '';

        $statusValue = $this->statusMap[$statusKey] ?? null;

        $holidays = Holiday::query()

            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })

            ->when(!is_null($statusValue), function ($q) use ($statusValue) {
                $q->where('status', $statusValue);
            })

            ->latest()
            ->get();


        /**
         * EXPORT PDF
         */
        if ($type === 'pdf') {

            $pdf = Pdf::loadView('admin.holiday.pdf', [
                'holidays' => $holidays,
                'search' => $search,
                'statusKey' => $statusKey
            ]);

            return $pdf->download('holiday-report.pdf');
        }


        /**
         * EXPORT EXCEL / CSV
         */

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['No', 'Holiday', 'Date', 'Status']
        ], null, 'A1');

        foreach ($holidays as $i => $holiday) {

            $sheet->fromArray([
                $i + 1,
                $holiday->name,
                $holiday->date,
                $holiday->status == 1 ? 'Active' : 'Inactive'
            ], null, 'A' . ($i + 2));
        }

        $writer = $type === 'excel'
            ? new Xlsx($spreadsheet)
            : new Csv($spreadsheet);

        $filename = "holiday-report." . ($type === 'excel' ? 'xlsx' : 'csv');

        return Response::streamDownload(
            fn() => $writer->save('php://output'),
            $filename
        );
    }
}
