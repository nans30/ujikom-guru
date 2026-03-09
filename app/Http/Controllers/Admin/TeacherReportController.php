<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Position;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TeacherReportController extends Controller
{
    // ================================
    // VIEW REPORT
    // ================================
    public function index(Request $request)
    {
        $query = Teacher::with(['position', 'createdBy']);

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $teachers = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $positions = Position::all();

        return view('admin.teacher.report', compact('teachers', 'positions'));
    }

    // ================================
    // EXPORT (PDF / EXCEL / CSV)
    // ================================
    public function export(Request $request)
    {
        $type = $request->type ?? 'pdf';

        $query = Teacher::with(['position', 'createdBy']);

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $teachers = $query->latest()->get();

        // ===== PDF =====
        if ($type === 'pdf') {
            $pdf = Pdf::loadView('admin.teacher.report_pdf', compact('teachers'))
                ->setPaper('a4', 'portrait');

            $fileName = 'Laporan_Guru_' . now()->format('YmdHis') . '.pdf';
            return $pdf->download($fileName);
        }

        // ===== EXCEL / CSV =====
        if ($type === 'excel' || $type === 'csv') {

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header
            $sheet->fromArray([
                ['NIP', 'Nama', 'Jabatan / Posisi', 'Jenis Kelamin', 'Dibuat Oleh', 'Status', 'Dibuat Pada']
            ], null, 'A1');

            $rowNumber = 2;
            foreach ($teachers as $t) {
                $sheet->fromArray([
                    $t->nip,
                    $t->name,
                    $t->position?->name ?? '-',
                    $t->jenis_kelamin === 'L' ? 'Laki-laki' : ($t->jenis_kelamin === 'P' ? 'Perempuan' : '-'),
                    $t->createdBy?->name ?? '-',
                    $t->is_active ? 'Aktif' : 'Tidak Aktif',
                    $t->created_at?->format('d-m-Y H:i'),
                ], null, 'A' . $rowNumber);

                $rowNumber++;
            }

            $fileName = 'Laporan_Guru_' . now()->format('YmdHis') . '.' . ($type === 'excel' ? 'xlsx' : 'csv');
            $writer = $type === 'excel' ? new Xlsx($spreadsheet) : new Csv($spreadsheet);

            $response = new StreamedResponse(function () use ($writer) {
                $writer->save('php://output');
            });

            $response->headers->set('Content-Type', $type === 'excel'
                ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                : 'text/csv');

            $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;
        }

        abort(404);
    }
}