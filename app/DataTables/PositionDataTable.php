<?php

namespace App\DataTables;

use App\Models\Position;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class PositionDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()

            ->editColumn('name', fn($row) => ucfirst($row->name))

            ->editColumn(
                'status',
                fn($row) =>
                $row->status
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>'
            )

            ->editColumn(
                'created_at',
                fn($row) =>
                $row->created_at?->diffForHumans()
            )

            ->addColumn('action', function ($row) {

                $editUrl = route('admin.position.edit', $row->id);
                $deleteUrl = route('admin.position.destroy', $row->id);

                return '
                    <a href="' . $editUrl . '" 
                       class="btn btn-light btn-icon btn-sm rounded-circle" 
                       title="Edit">
                        <i class="ti ti-edit"></i>
                    </a>

                    <a href="javascript:void(0)" 
                       data-id="' . $row->id . '" 
                       data-url="' . $deleteUrl . '" 
                       class="btn btn-light btn-icon btn-sm rounded-circle deleteBtn"
                       title="Delete">
                        <i class="ti ti-trash"></i>
                    </a>
                ';
            })

            ->rawColumns(['status', 'action']);
    }

    public function query(Position $model): QueryBuilder
    {
        return $model->newQuery()->latest();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('position-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->setTableAttribute(
                'class',
                'table table-striped dt-responsive align-middle mb-0'
            )
            ->parameters([
                'pageLength'   => 10,
                'lengthChange' => true,
                'searching'    => true,
                'ordering'     => true,
                'responsive'   => true,
                'autoWidth'    => false,
                'paging'       => true,
                'dom'          => 'lrtip',

                'language' => [
                    'emptyTable' => 'No records found',
                ],
            ]);
    }

    protected function getColumns(): array
    {
        return [

            [
                'data' => 'DT_RowIndex',
                'title' => 'No',
                'orderable' => false,
                'searchable' => false,
                'width' => '40px'
            ],

            [
                'data'  => 'name',
                'title' => 'Name'
            ],

            [
                'data'  => 'status',
                'title' => 'Status'
            ],

            [
                'data'  => 'created_at',
                'title' => 'Created At'
            ],

            [
                'data'       => 'action',
                'title'      => 'Action',
                'orderable'  => false,
                'searchable' => false
            ],
        ];
    }

    protected function filename(): string
    {
        return 'Position_' . now()->format('YmdHis');
    }
}