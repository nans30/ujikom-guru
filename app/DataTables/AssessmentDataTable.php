<?php

namespace App\DataTables;

use App\Models\Teacher;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class AssessmentDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', fn($row) => '<input class="form-check-input file-item-check" type="checkbox" value="' . ($row->assessment_id ?? 0) . '">')
            ->editColumn('name', fn($row) => $row->name)
            ->editColumn('evaluator', fn($row) => $row->evaluator_name ?? '-')
            ->addColumn('period', function ($row) {
                if (!$row->semester) return '-';
                $semesterLabel = $row->semester == '1' ? 'Ganjil' : 'Genap';
                return "Semester {$row->semester} ({$semesterLabel}) - {$row->academic_year}";
            })
            ->editColumn('assessment_date', fn($row) => $row->assessment_date ? date('d M Y', strtotime($row->assessment_date)) : '-')
            ->editColumn('status', function ($row) {
                if (!$row->assessment_status) {
                    return '<span class="badge bg-danger">Belum Dinilai</span>';
                }
                $class = $row->assessment_status == 1 ? 'bg-warning' : 'bg-success';
                $text = $row->assessment_status == 1 ? 'Draft' : 'Final';
                return '<span class="badge ' . $class . '">' . $text . '</span>';
            })
            ->addColumn('action', function ($row) {
                if (!$row->assessment_id) {
                    // AMBIL FILTER SAAT INI UNTUK DIOPER KE FORM CREATE
                    $createUrl = route('admin.assessment.create', [
                        'teacher_id'    => $row->id,
                        'semester'      => request('semester'),
                        'academic_year' => request('academic_year')
                    ]);

                    return '
                        <div class="d-flex justify-content-end">
                            <a href="' . $createUrl . '" class="btn btn-primary btn-icon btn-sm rounded-circle" data-bs-toggle="tooltip" title="Tambah Penilaian">
                                <i class="ti ti-plus fs-lg"></i>
                            </a>
                        </div>';
                }

                $showUrl = route('admin.assessment.show', $row->assessment_id);
                $deleteUrl = route('admin.assessment.destroy', $row->assessment_id);

                $editBtn = '';
                if ($row->assessment_status == 1) {
                    $editUrl = route('admin.assessment.edit', $row->assessment_id);
                    $editBtn = '
                        <a href="' . $editUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle" data-bs-toggle="tooltip" title="Edit">
                            <i class="ti ti-edit fs-lg"></i>
                        </a>';
                }

                return '
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="' . $showUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle" data-bs-toggle="tooltip" title="View Detail">
                            <i class="ti ti-eye fs-lg"></i>
                        </a>
                        ' . $editBtn . '
                        <a href="javascript:void(0)" data-id="' . $row->assessment_id . '" data-url="' . $deleteUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle deleteBtn" data-bs-toggle="tooltip" title="Delete">
                            <i class="ti ti-trash fs-lg"></i>
                        </a>
                    </div>';
            })
            ->filterColumn('assessment_date', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(a.assessment_date, '%d %b %Y') like ?", ["%$keyword%"]);
            })
            ->filterColumn('evaluator', function ($query, $keyword) {
                $query->whereHas('evaluator', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->rawColumns(['checkbox', 'status', 'action']);
    }

    public function query(Teacher $model): QueryBuilder
    {
        $semester = request('semester');
        $academicYear = request('academic_year');
        $status = request('status');

        return $model->newQuery()
            ->select([
                'teachers.id',
                'teachers.name',
                'a.id as assessment_id',
                'a.status as assessment_status',
                'a.assessment_date',
                'a.semester',
                'a.academic_year',
                'u.name as evaluator_name'
            ])
            ->leftJoin('assessments as a', function ($join) use ($semester, $academicYear) {
                $join->on('teachers.id', '=', 'a.evaluatee_id')
                    ->whereNull('a.deleted_at');

                if ($semester) $join->where('a.semester', $semester);
                if ($academicYear) $join->where('a.academic_year', $academicYear);
            })
            ->leftJoin('users as u', 'u.id', '=', 'a.evaluator_id')
            ->when($status, function ($q) use ($status) {
                if ($status == 'belum') {
                    return $q->whereNull('a.id');
                }
                return $q->where('a.status', $status);
            });
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('assessment-table')
            ->columns($this->getColumns())
            ->minifiedAjax('', '
                data.semester = $("#filter-semester").val(); 
                data.academic_year = $("#filter-academic-year").val();
                data.status = $("#filter-status").val();
            ')
            ->setTableAttribute('class', 'table table-striped dt-responsive align-middle mb-0')
            ->parameters([
                'pageLength' => 10,
                'dom' => "<'row'<'col-sm-12'tr>>" . "<'row'<'col-sm-5'i><'col-sm-7 d-flex justify-content-end'p>>",
                'drawCallback' => 'function() {
                    if (typeof feather !== "undefined") { feather.replace(); }
                    $("[data-bs-toggle=\'tooltip\']").tooltip();
                }',
            ]);
    }

    protected function getColumns(): array
    {
        return [
            ['data' => 'checkbox', 'title' => '', 'orderable' => false, 'searchable' => false, 'width' => '40px'],
            ['data' => 'name', 'title' => 'Nama Guru/Staf', 'searchable' => true],
            ['data' => 'evaluator', 'name' => 'u.name', 'title' => 'Penilai', 'searchable' => true],
            ['data' => 'period', 'title' => 'Periode', 'orderable' => false, 'searchable' => false],
            ['data' => 'assessment_date', 'name' => 'a.assessment_date', 'title' => 'Tanggal', 'searchable' => true],
            ['data' => 'status', 'name' => 'a.status', 'title' => 'Status', 'class' => 'text-center', 'searchable' => true],
            ['data' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false, 'class' => 'text-end'],
        ];
    }

    protected function filename(): string
    {
        return 'Assessment_' . date('YmdHis');
    }
}
