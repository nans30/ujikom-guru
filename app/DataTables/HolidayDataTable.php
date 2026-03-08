<?php

namespace App\DataTables;

use App\Models\Holiday;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class HolidayDataTable extends DataTable
{
    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()

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

            ->rawColumns([
                'status',
                'action'
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */
    public function query(Holiday $model): QueryBuilder
    {
        return $model
            ->newQuery()
            ->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | HTML Builder
    |--------------------------------------------------------------------------
    */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('holiday-table')
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

    /*
    |--------------------------------------------------------------------------
    | Columns
    |--------------------------------------------------------------------------
    */
    protected function getColumns(): array
    {
        return [

            [
                'data'       => 'DT_RowIndex',
                'title'      => 'No',
                'orderable'  => false,
                'searchable' => false,
                'width'      => '40px',
            ],

            [
                'data'  => 'name',
                'title' => 'Name'
            ],

            [
                'data'  => 'date',
                'title' => 'Holiday Date'
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

    /*
    |--------------------------------------------------------------------------
    | Filename Export
    |--------------------------------------------------------------------------
    */
    protected function filename(): string
    {
        return 'Holiday_' . now()->format('YmdHis');
    }
}