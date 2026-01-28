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
            ->addColumn('checkbox', fn($row) => '<input class="form-check-input file-item-check" type="checkbox" value="' . $row->id . '">')
            ->editColumn('created_at', fn($row) => $row->created_at?->diffForHumans())
            ->editColumn('title', fn($row) => ucfirst($row->title))
            // ->editColumn('status', function ($row) {
            //     return view('admin.partials.toggle', [
            //         'toggle' => $row,
            //         'name' => 'status',
            //         'route' => 'teacher.status',
            //         'value' => $row->status,
            //     ]);
            // })
            ->editColumn('action', function ($row) {
                $editUrl = route('admin.teacher.edit', $row->id);
                $deleteUrl = route('admin.teacher.destroy', $row->id);

                return '
                    <a href="' . $editUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle" data-bs-toggle="tooltip" title="Edit">
                        <i class="ti ti-edit fs-lg"></i>
                    </a>
                    <a href="javascript:void(0)" data-id="' . $row->id . '" data-url="' . $deleteUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle deleteBtn" data-bs-toggle="tooltip" title="Delete">
                        <i class="ti ti-trash fs-lg"></i>
                    </a>
                ';
            })
            ->rawColumns(['checkbox', 'created_at', 'status', 'action']);
    }

    public function query(Teacher $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('teacher-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->setTableAttribute('class', 'table table-striped dt-responsive align-middle mb-0')
            ->parameters([
                'pageLength' => 10,
                'lengthChange' => false,
                'searching' => true,
                'language' => [
                    'emptyTable' => 'No records found',
                    'zeroRecords' => 'No matching records found',
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
            ['data' => 'checkbox', 'title' => '<input type="checkbox" data-table-select-all class="form-check-input">', 'orderable' => false, 'searchable' => false, 'escape' => false],
            ['data' => 'name', 'title' => 'Name'],
            ['data' => 'created_at', 'title' =>"Created At"],
            // ['data' => 'status', 'title' => "Status"],
            ['data' => 'action', 'title' => "Action", 'orderable' => false, 'searchable' => false],
        ];
    }

    protected function filename(): string
    {
        return 'Teacher_' . date('YmdHis');
    }
}