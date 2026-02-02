<?php

namespace App\DataTables;

use App\Models\Teacher;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class TeacherDataTable extends DataTable
{
    // =============================
    // DATATABLE
    // =============================
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))

            // ===== GLOBAL SEARCH (SERVER SIDE) =====
            ->filter(function ($query) {
                if ($search = request('search.value')) {
                    $query->where(function ($q) use ($search) {
                        $q->where('nip', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('nuptk', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('rfid_uid', 'like', "%{$search}%")
                            ->orWhere('tempat_lahir', 'like', "%{$search}%");
                    });
                }
            }, true)

            // ===== INDEX =====
            ->addIndexColumn()

            // ===== JENIS KELAMIN =====
            ->editColumn('jenis_kelamin', function ($row) {
                return match ($row->jenis_kelamin) {
                    'P' => 'Perempuan',
                    'L' => 'Laki-laki',
                    default => '-',
                };
            })

            // ===== STATUS =====
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
            })

            // ===== CREATED AT =====
            ->editColumn('created_at', function ($row) {
                return $row->created_at?->diffForHumans();
            })

            // ===== ACTION =====
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('admin.teacher.edit', $row->id) . '" 
                       class="btn btn-light btn-icon btn-sm rounded-circle" 
                       title="Edit">
                        <i class="ti ti-edit fs-lg"></i>
                    </a>

                    <button type="button"
                        data-id="' . $row->id . '"
                        data-url="' . route('admin.teacher.destroy', $row->id) . '"
                        class="btn btn-light btn-icon btn-sm rounded-circle deleteBtn"
                        title="Delete">
                        <i class="ti ti-trash fs-lg"></i>
                    </button>
                ';
            })

            ->rawColumns(['is_active', 'action']);
    }

    // =============================
    // QUERY
    // =============================
    public function query(Teacher $model): QueryBuilder
    {
        return $model->newQuery()->latest();
    }

    // =============================
    // HTML BUILDER
    // =============================
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('teacher-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->setTableAttribute('class', 'table table-striped dt-responsive align-middle mb-0')
            ->parameters([
                'processing'   => true,
                'serverSide'   => true,
                'pageLength'   => 10,
                'lengthChange' => false,
                'searching'    => true,
                'ordering'     => true,
                'paging'       => true,

                'language' => [
                    'emptyTable'  => 'No teachers found',
                    'zeroRecords' => 'No matching teachers found',
                ],
            ]);
    }

    // =============================
    // COLUMNS
    // =============================
    protected function getColumns(): array
    {
        return [
            [
                'data'       => 'DT_RowIndex',
                'title'      => 'No',
                'orderable'  => false,
                'searchable' => false,
                'width'      => '50px',
            ],
            ['data' => 'nip',           'title' => 'NIP'],
            ['data' => 'name',          'title' => 'Name'],
            ['data' => 'jenis_kelamin', 'title' => 'JK'],
            ['data' => 'email',         'title' => 'Email'],
            ['data' => 'rfid_uid',      'title' => 'RFID UID'],
            ['data' => 'is_active',     'title' => 'Status'],
        
            [
                'data'       => 'action',
                'title'      => 'Action',
                'orderable'  => false,
                'searchable' => false,
            ],
        ];
    }

    protected function filename(): string
    {
        return 'Teachers_' . date('YmdHis');
    }
}