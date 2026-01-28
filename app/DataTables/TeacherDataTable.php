<?php

namespace App\DataTables;

use App\Models\Teacher;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class TeacherDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            // ===== NO URUT =====
            ->addIndexColumn()

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
                $editUrl   = route('admin.teacher.edit', $row->id);
                $deleteUrl = route('admin.teacher.destroy', $row->id);

                return '
                    <a href="' . $editUrl . '" 
                       class="btn btn-light btn-icon btn-sm rounded-circle" 
                       title="Edit">
                        <i class="ti ti-edit fs-lg"></i>
                    </a>

                    <button type="button"
                        data-id="' . $row->id . '"
                        data-url="' . $deleteUrl . '"
                        class="btn btn-light btn-icon btn-sm rounded-circle deleteBtn"
                        title="Delete">
                        <i class="ti ti-trash fs-lg"></i>
                    </button>
                ';
            })

            ->rawColumns([
                'is_active',
                'action',
            ]);
    }

    public function query(Teacher $model): QueryBuilder
    {
        return $model->newQuery()->latest();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('teacher-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->setTableAttribute('class', 'table table-striped dt-responsive align-middle mb-0')
            ->parameters([
                'pageLength'   => 10,
                'lengthChange' => false,
                'searching'   => true,
                'ordering'    => true,
                'language'    => [
                    'emptyTable'  => 'No teachers found',
                    'zeroRecords' => 'No matching teachers found',
                ],
                'dom' => "<'row'<'col-sm-12'tr>>" .
                    "<'row'<'col-sm-5'i><'col-sm-7 d-flex justify-content-end'p>>",
                'drawCallback' => 'function() {
                    feather.replace();
                    $(".deleteBtn").tooltip();
                }',
                'initComplete' => 'function() {
                    $(".dataTables_filter").appendTo(".search-input");
                }',
            ]);
    }

    protected function getColumns(): array
    {
        return [
            // ===== NO =====
            [
                'data'       => 'DT_RowIndex',
                'title'      => 'No',
                'orderable'  => false,
                'searchable' => false,
                'width'      => '50px',
            ],

            ['data' => 'nip',        'title' => 'NIP'],
            ['data' => 'name',       'title' => 'Name'],
            ['data' => 'email',      'title' => 'Email'],
            ['data' => 'rfid_uid',   'title' => 'RFID UID'],
            ['data' => 'is_active',  'title' => 'Status'],
            ['data' => 'created_at', 'title' => 'Created At'],

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