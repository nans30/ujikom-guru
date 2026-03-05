<?php

namespace App\DataTables;

use App\Models\Holiday;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class HolidayDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', function ($row) {
                return '<input class="form-check-input file-item-check" type="checkbox" value="' . $row->id . '">';
            })

            ->editColumn('name', function ($row) {
                return ucfirst($row->name);
            })

            ->editColumn('date', function ($row) {
                return \Carbon\Carbon::parse($row->date)->format('d M Y');
            })

            ->editColumn('status', function ($row) {
                if ($row->status == 1) {
                    return '<span class="badge bg-success">Active</span>';
                }

                return '<span class="badge bg-danger">Inactive</span>';
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at?->diffForHumans();
            })

            ->addColumn('action', function ($row) {

                $editUrl = route('admin.holiday.edit', $row->id);
                $deleteUrl = route('admin.holiday.destroy', $row->id);

                return '
                    <a href="' . $editUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle" data-bs-toggle="tooltip" title="Edit">
                        <i class="ti ti-edit fs-lg"></i>
                    </a>

                    <a href="javascript:void(0)" data-id="' . $row->id . '" data-url="' . $deleteUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle deleteBtn" data-bs-toggle="tooltip" title="Delete">
                        <i class="ti ti-trash fs-lg"></i>
                    </a>
                ';
            })

            ->rawColumns([
                'checkbox',
                'status',
                'action'
            ]);
    }

    public function query(Holiday $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('holiday-table')
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

            [
                'data' => 'checkbox',
                'title' => '<input type="checkbox" class="form-check-input" id="selectAll">',
                'orderable' => false,
                'searchable' => false
            ],

            [
                'data' => 'name',
                'title' => 'Name'
            ],

            [
                'data' => 'date',
                'title' => 'Holiday Date'
            ],

            [
                'data' => 'status',
                'title' => 'Status'
            ],

            [
                'data' => 'created_at',
                'title' => 'Created At'
            ],

            [
                'data' => 'action',
                'title' => 'Action',
                'orderable' => false,
                'searchable' => false
            ],
        ];
    }

    protected function filename(): string
    {
        return 'Holiday_' . date('YmdHis');
    }
}