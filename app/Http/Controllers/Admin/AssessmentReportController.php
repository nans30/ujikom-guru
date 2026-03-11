<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssessmentReportController extends Controller
{
    // ================================
    // VIEW REPORT
    // ================================
    public function index(Request $request)
    {
        // Parameter pencarian & filter
        $academicYear = $request->academic_year;
        $statusDinilai = $request->status_dinilai; // 'sudah' atau 'belum'
        $search = $request->search;

        $query = Teacher::with('position')->where('is_active', true);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $teachers = $query->get()->map(function ($teacher) use ($academicYear) {
            // Jika filter tahun akademik ada, cari assessment sesuai tahun, jika tidak cari yang terbaru
            $assessmentQuery = $teacher->assessments();
            if ($academicYear) {
                $assessmentQuery->where('academic_year', $academicYear);
            }
            $latestAssessment = $assessmentQuery->latest('assessment_date')->first();

            $teacher->latest_assessment = $latestAssessment;
            $teacher->status_penilaian = $latestAssessment ? 'Sudah Dinilai' : 'Belum Dinilai';
            return $teacher;
        });

        // Filter by Status Penilaian setelah mapping
        if ($statusDinilai) {
            if ($statusDinilai === 'sudah') {
                $teachers = $teachers->where('status_penilaian', 'Sudah Dinilai');
            } elseif ($statusDinilai === 'belum') {
                $teachers = $teachers->where('status_penilaian', 'Belum Dinilai');
            }
        }

        // Pagination secara manual karena collection sudah diambil all
        $perPage = 10;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $items = $teachers->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedTeachers = new \Illuminate\Pagination\LengthAwarePaginator($items, $teachers->count(), $perPage, $currentPage, [
            'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
            'query' => $request->query()
        ]);

        // List unik tahun akademik untuk dropdown filter
        $academicYears = Assessment::select('academic_year')->distinct()->pluck('academic_year');

        return view('admin.assessment.report', compact('paginatedTeachers', 'academicYears', 'academicYear', 'statusDinilai', 'search'));
    }

    // ================================
    // VIEW REPORT DETAIL
    // ================================
    public function show($id, Request $request)
    {
        $teacher = Teacher::with('position')->findOrFail($id);
        $academicYear = $request->academic_year;

        $assessmentQuery = $teacher->assessments()->with(['details.category', 'evaluator']);
        if ($academicYear) {
            $assessmentQuery->where('academic_year', $academicYear);
        }

        $assessment = $assessmentQuery->latest('assessment_date')->first();

        // Data for Radar Chart
        $categories = \App\Models\Categorie::where('status', true)->orderBy('id')->get();
        $radarLabels = [];
        $radarData = [];
        
        foreach ($categories as $cat) {
            $radarLabels[] = $cat->name;
            $score = 0;
            if ($assessment) {
                // Find if the teacher was evaluated on this category
                $detail = $assessment->details->firstWhere('category_id', $cat->id);
                if ($detail) {
                    $score = $detail->score;
                }
            }
            $radarData[] = $score;
        }

        // Data for Feedback History
        $feedbacks = $teacher->assessments()
            ->with('evaluator')
            ->whereNotNull('general_notes')
            ->orderBy('assessment_date', 'desc')
            ->take(5)
            ->get();

        return view('admin.assessment.detail', compact('teacher', 'assessment', 'radarLabels', 'radarData', 'feedbacks', 'academicYear'));
    }

    // ================================
    // EXPORT (PDF / EXCEL / CSV)
    // ================================
    public function export(Request $request, $type)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $academicYear = $request->academic_year;
        $statusDinilai = $request->status_dinilai;
        $search = $request->search;

        $query = Teacher::with('position')->where('is_active', true);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $teachers = $query->get()->map(function ($teacher) use ($academicYear) {
            $assessmentQuery = $teacher->assessments();
            if ($academicYear) {
                $assessmentQuery->where('academic_year', $academicYear);
            }
            $latestAssessment = $assessmentQuery->latest('assessment_date')->first();

            $teacher->latest_assessment = $latestAssessment;
            $teacher->status_penilaian = $latestAssessment ? 'Sudah Dinilai' : 'Belum Dinilai';
            return $teacher;
        });

        if ($statusDinilai) {
            if ($statusDinilai === 'sudah') {
                $teachers = $teachers->where('status_penilaian', 'Sudah Dinilai');
            } elseif ($statusDinilai === 'belum') {
                $teachers = $teachers->where('status_penilaian', 'Belum Dinilai');
            }
        }

        // ===== PDF =====
        if ($type === 'pdf') {
            $pdf = Pdf::loadView('admin.assessment.report_pdf', compact('teachers', 'academicYear'))
                ->setPaper('a4', 'landscape')->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                ]);

            $fileName = 'Laporan_Asesmen_' . ($academicYear ?? 'Semua_Periode') . '_' . now()->format('YmdHis') . '.pdf';
            return $pdf->download($fileName);
        }

        // ===== EXCEL / CSV =====
        if ($type === 'excel' || $type === 'csv') {

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header
            $sheet->fromArray([
                ['No', 'Nama Guru', 'NIP', 'Jabatan', 'Status Penilaian', 'Evaluator', 'Periode', 'Tanggal Penilaian', 'Total Skor', 'Catatan']
            ], null, 'A1');

            $rowNumber = 2;
            foreach ($teachers as $index => $t) {
                $assmnt = $t->latest_assessment;
                $sheet->fromArray([
                    $index + 1,
                    $t->name,
                    $t->nip ?? '-',
                    $t->position?->name ?? '-',
                    $t->status_penilaian,
                    $assmnt?->evaluator?->name ?? '-',
                    $assmnt?->period ?? '-',
                    $assmnt?->assessment_date?->format('d-m-Y') ?? '-',
                    $assmnt?->total_score ?? '-',
                    $assmnt?->general_notes ?? '-',
                ], null, 'A' . $rowNumber);

                $rowNumber++;
            }

            $fileName = 'Laporan_Asesmen_' . ($academicYear ?? 'Semua_Periode') . '_' . now()->format('YmdHis') . '.' . ($type === 'excel' ? 'xlsx' : 'csv');
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
