<?php

namespace App\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class RoleDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', function ($row) {
                return '<input class="form-check-input form-check-input-light file-item-check mt-0" type="checkbox" value="' . $row->id . '">';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->diffForHumans();
            })
            ->editColumn('updated_at', function ($row) {
                return $row->updated_at->diffForHumans();
            })
            ->editColumn('name', function ($row) {
                return ucfirst($row->name);
            })
            ->editColumn('action', function ($row) {
                $deleteUrl = route('admin.role.destroy', $row->id);
                $editUrl = route('admin.role.edit', $row->id);
                return '<a href="' . $editUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                            <i class="ti ti-edit fs-lg"></i>
                        </a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" data-url="' . $deleteUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle deleteBtn" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                            <i class="ti ti-trash fs-lg"></i>
                        </a>';
            })
            ->rawColumns(['checkbox', 'created_at', 'status', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Role $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('role-table')
            ->addColumn(['data' => 'checkbox', 'title' => '<input data-table-select-all class="form-check-input form-check-input-light" type="checkbox" id="select-all-files">', 'orderable' => false, 'searchable' => false, 'width' => '10px', 'escape' => false])
            ->addColumn(['data' => 'name', 'title' =>'Name', 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'created_at', 'title' => 'Created At', 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'updated_at', 'title' => 'Updated At', 'orderable' => true, 'searchable' => false])
            ->addColumn(['data' => 'action', 'title' => 'Actions', 'orderable' => false, 'searchable' => false])
            ->minifiedAjax()
            ->setTableAttribute('class', 'table table-striped dt-responsive align-middle mb-0')

            ->parameters([
                'language' => [
                    'emptyTable' => 'No Records Found',
                    'infoEmpty' => '',
                    'zeroRecords' => 'No Records Found',
                ],
                'pageLength' => 10,
                'lengthChange' => false,
                'searching' => true,
                'dom' => "<'row'<'col-sm-12'tr>>" .
                    "<'row'<'col-sm-5'i><'col-sm-7 d-flex justify-content-end'p>>",
                'drawCallback' => 'function(settings) {
                    if (settings._iRecordsDisplay === 0) {
                        $(settings.nTableWrapper).find(".dataTables_paginate").hide();
                    } else {
                        $(settings.nTableWrapper).find(".dataTables_paginate").show();
                    }
                    feather.replace();
                }',
                'initComplete' => 'function(settings, json) {
                    $(\'.dataTables_filter\').appendTo(\'#tableSearch\');
                    $(\'.dataTables_filter\').appendTo(\'.search-input\');
                }',
                    "drawCallback" => 'function() {
                    $(".action-table-data [data-bs-toggle=\'tooltip\'], .action-table-data [title]").tooltip();
                }',
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Role_' . date('YmdHis');
    }
}