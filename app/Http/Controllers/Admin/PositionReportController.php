<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Position;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Facades\Response;

class PositionReportController extends Controller
{
    // Mapping status dari URL ke database
    private $statusMap = [
        'active' => 1,
        'inactive' => 0,
    ];

    /**
     * Tampilkan halaman report
     */
    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $statusKey = $request->status ?? '';

        $statusValue = $this->statusMap[$statusKey] ?? null;

        $positions = Position::withCount('teachers')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->when(!is_null($statusValue), function ($q) use ($statusValue) {
                $q->where('status', $statusValue);
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.position.report', compact(
            'positions',
            'search',
            'statusKey'
        ));
    }

    /**
     * Export report
     */
    public function export(Request $request, $type)
    {
        $search = $request->search ?? '';
        $statusKey = $request->status ?? '';
        $statusValue = $this->statusMap[$statusKey] ?? null;

        $positions = Position::withCount('teachers')
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

            $pdf = Pdf::loadView('admin.position.pdf', [
                'positions' => $positions,
                'search' => $search,
                'statusKey' => $statusKey
            ]);

            return $pdf->download('position-report.pdf');
        }

        /**
         * EXPORT EXCEL / CSV
         */
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['No', 'Position', 'Status', 'Total Teachers']
        ], null, 'A1');

        foreach ($positions as $index => $item) {

            $sheet->fromArray([
                $index + 1,
                $item->name,
                $item->status == 1 ? 'Active' : 'Inactive',
                $item->teachers_count
            ], null, 'A' . ($index + 2));
        }

        $writer = $type === 'excel'
            ? new Xlsx($spreadsheet)
            : new Csv($spreadsheet);

        $filename = "position-report." . ($type === 'excel' ? 'xlsx' : 'csv');

        return Response::streamDownload(
            fn() => $writer->save('php://output'),
            $filename
        );
    }
}