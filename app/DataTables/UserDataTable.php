<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;
use App\Enums\StatusUserEnum;
use Illuminate\Support\Facades\Auth;

class UserDataTable extends DataTable
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
            ->editColumn('role', function ($row) {
                return ucfirst($row->getRoleNames()->first());
            })
            ->filterColumn('role', function ($query, $keyword) {
                $query->whereHas('roles', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->editColumn('first_name', function ($row) {
                return ucfirst($row->first_name) . ' ' . ucfirst($row->last_name);
            })
            ->editColumn('Image', function ($row) {
                if ($row->first_name) {
                    $media = $row->getFirstMedia('image');
                    if ($media) {
                        $imageTag = '<img src="' . $media->getUrl() . '" class="img-fluid avatar-md rounded-circle">';
                    } else {
                        $initial = strtoupper(substr($row->first_name, 0, 1));
                        $imageTag = '<div class="avatar-sm"><span class="avatar-title text-bg-info rounded-circle">' . $initial . '</span></div>';
                    }
                } else {
                    $imageTag = '<div class="avatar-sm"><span class="avatar-title text-bg-info rounded-circle">NA</span></div>';
                }

                return '<div class="d-flex align-items-center">' . $imageTag . '</div>';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->diffForHumans();
            })
            ->editColumn('status_label', function ($row) {
                $statusMap = StatusUserEnum::getStatus();
                $statusValue = $row->status;
                $statusText = $statusMap[$statusValue] ?? '-';
                $badgeClass = $statusValue == StatusUserEnum::ACTIVE ? 'primary' : 'danger';
                return '<span class="badge bg-' . $badgeClass . '">' . $statusText . '</span>';
            })
            ->editColumn('action', function ($row) {
                $deleteUrl = route('admin.user.destroy', $row->id);
                $editUrl = route('admin.user.edit', $row->id);
                return '<a href="' . $editUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                            <i class="ti ti-edit fs-lg"></i>
                        </a>
                        <a href="javascript:void(0)" data-id="' . $row->id . '" data-url="' . $deleteUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle deleteBtn" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                            <i class="ti ti-trash fs-lg"></i>
                        </a>';
            })
            ->rawColumns(['checkbox', 'created_at', 'action', 'Image', 'status_label']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->where('system_reserve', 0)->where('id', '!=', Auth::id());
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user-table')
            ->addColumn(['data' => 'checkbox', 'title' => '<input data-table-select-all class="form-check-input form-check-input-light" type="checkbox" id="select-all-files">', 'orderable' => false, 'searchable' => false, 'width' => '10px', 'escape' => false])
            ->addColumn(['data' => 'Image', 'title' => 'Image', 'orderable' => false, 'searchable' => false, 'width' => '30px', 'className' => 'text-center'])
            ->addColumn(['data' => 'first_name', 'title' => 'Name', 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'email', 'title' => 'Email', 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'role', 'title' => 'Role', 'orderable' => false, 'searchable' => true])
            ->addColumn(['data' => 'created_at', 'title' => 'Created at', 'orderable' => true, 'searchable' => false])
            ->addColumn(['data' => 'status_label', 'title' => 'Status', 'orderable' => false, 'searchable' => false, 'className' => 'text-center', 'width' => '1%'])
            ->addColumn(['data' => 'status', 'title' => 'Status', 'orderable' => false, 'searchable' => true, 'className' => 'text-center', 'visible' => false])
            ->addColumn(['data' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'className' => 'action-table-data text-center', 'width' => '5%'])
            ->minifiedAjax()
            ->setTableAttribute('class', 'table table-striped dt-responsive align-middle mb-0')
            ->parameters([
                'language' => [
                    'emptyTable'   => 'No Records Found',
                    'infoEmpty'    => '',
                    'zeroRecords'  => 'No Records Found',
                ],
                'drawCallback' => 'function(settings) {
                    if (settings._iRecordsDisplay === 0) {
                        $(settings.nTableWrapper).find(".dataTables_paginate").hide();
                    } else {
                        $(settings.nTableWrapper).find(".dataTables_paginate").show();
                    }
                }',
                'pageLength' => 10,
                'lengthChange' => false,
                'searching' => true,
                'dom' => "<'row'<'col-sm-12'tr>>" .
                    "<'row'<'col-sm-5'i><'col-sm-7 d-flex justify-content-end'p>>",
                "initComplete" => 'function(settings, json) {
                    console.log(json);
                    $(\'.dataTables_filter\').appendTo(\'#tableSearch\');
                    $(\'.dataTables_filter\').appendTo(\'.search-input\');

                    // Roles Filter
                    if (json.data && Array.isArray(json.data)) {
                        var dataSet = new Set();
                        json.data.forEach(function(row) {
                            if (row.role && row.role !== "-") {
                                dataSet.add(row.role);
                            }
                        });
                        var $dataSelect = $("#filter-roles");
                        $dataSelect.empty();
                        $dataSelect.append("<option value=\"\">Role</option>");
                        dataSet.forEach(function(name) {
                            $dataSelect.append(\'<option value="\' + name + \'">\' + name + \'</option>\');
                        });
                    }
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
        return 'User_' . date('YmdHis');
    }
}
