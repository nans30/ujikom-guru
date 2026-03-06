<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Position;
use Illuminate\Http\Request;
use PDF; // barryvdh/laravel-dompdf
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

        $teachers = $query->latest()->get();
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
            $pdf = PDF::loadView('admin.teacher.report_pdf', compact('teachers'));
            return $pdf->download('Laporan_Guru_' . now()->format('YmdHis') . '.pdf');
        }

        // ===== EXCEL / CSV =====
        if ($type === 'excel' || $type === 'csv') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header
            $sheet->fromArray([['NIP', 'Nama', 'Jabatan / Posisi', 'Jenis Kelamin', 'Dibuat Oleh', 'Status', 'Dibuat Pada']], null, 'A1');

            // Data
            $rowNumber = 2;
            foreach ($teachers as $t) {
                $sheet->fromArray([
                    $t->nip,
                    $t->name,
                    $t->position?->name ?? '-',
                    $t->jenis_kelamin == 'L' ? 'Laki-laki' : ($t->jenis_kelamin == 'P' ? 'Perempuan' : '-'),
                    $t->createdBy?->name ?? '-',
                    $t->is_active ? 'Aktif' : 'Tidak Aktif',
                    $t->created_at?->format('d-m-Y H:i'),
                ], null, 'A' . $rowNumber);
                $rowNumber++;
            }

            if ($type === 'excel') {
                $writer = new Xlsx($spreadsheet);
                $fileName = 'Laporan_Guru_' . now()->format('YmdHis') . '.xlsx';
            } else {
                $writer = new Csv($spreadsheet);
                $fileName = 'Laporan_Guru_' . now()->format('YmdHis') . '.csv';
            }

            $response = new StreamedResponse(function () use ($writer) {
                $writer->save('php://output');
            });

            $response->headers->set('Content-Type', $type === 'excel' ?
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;
        }

        abort(404);
    }
}