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
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', function ($row) {
                return '<input class="form-check-input form-check-input-light file-item-check mt-0"
                        type="checkbox" value="' . $row->id . '">';
            })

            ->editColumn('role', function ($row) {
                return ucfirst($row->getRoleNames()->first() ?? '-');
            })

            ->filterColumn('role', function ($query, $keyword) {
                $query->whereHas('roles', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            // ✅ NAME (pakai accessor)
            ->editColumn('name', function ($row) {
                return $row->name;
            })

            // ✅ AVATAR
            ->editColumn('image', function ($row) {
                $media = $row->getFirstMedia('image');

                if ($media) {
                    $imageTag = '<img src="' . $media->getUrl() . '"
                        class="img-fluid avatar-md rounded-circle">';
                } else {
                    $initial = strtoupper(substr($row->name ?? 'U', 0, 1));
                    $imageTag = '
                        <div class="avatar-sm">
                            <span class="avatar-title text-bg-info rounded-circle">
                                ' . $initial . '
                            </span>
                        </div>';
                }

                return '<div class="d-flex align-items-center">' . $imageTag . '</div>';
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at?->diffForHumans();
            })

            ->addColumn('status_label', function ($row) {
                $statusMap   = StatusUserEnum::getStatus();
                $statusValue = $row->status;
                $statusText  = $statusMap[$statusValue] ?? '-';
                $badgeClass  = $statusValue == StatusUserEnum::ACTIVE ? 'primary' : 'danger';

                return '<span class="badge bg-' . $badgeClass . '">' . $statusText . '</span>';
            })

            ->addColumn('action', function ($row) {
                $editUrl   = route('admin.user.edit', $row->id);
                $deleteUrl = route('admin.user.destroy', $row->id);

                return '
                    <a href="' . $editUrl . '"
                        class="btn btn-light btn-icon btn-sm rounded-circle"
                        data-bs-toggle="tooltip" title="Edit">
                        <i class="ti ti-edit fs-lg"></i>
                    </a>

                    <a href="javascript:void(0)"
                        data-id="' . $row->id . '"
                        data-url="' . $deleteUrl . '"
                        class="btn btn-light btn-icon btn-sm rounded-circle deleteBtn"
                        data-bs-toggle="tooltip" title="Delete">
                        <i class="ti ti-trash fs-lg"></i>
                    </a>
                ';
            })

            ->rawColumns(['checkbox', 'image', 'status_label', 'action']);
    }

    /**
     * Query source
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            ->where('system_reserve', 0)
            ->where('id', '!=', Auth::id());
    }

    /**
     * HTML Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user-table')

            ->addColumn([
                'data'       => 'checkbox',
                'title'      => '<input data-table-select-all class="form-check-input form-check-input-light" type="checkbox">',
                'orderable'  => false,
                'searchable' => false,
                'escape'     => false,
                'width'      => '10px',
            ])

            ->addColumn([
                'data'       => 'image',
                'title'      => 'Image',
                'orderable'  => false,
                'searchable' => false,
                'className'  => 'text-center',
                'width'      => '30px',
            ])

            ->addColumn([
                'data'       => 'name',
                'title'      => 'Name',
                'orderable'  => true,
                'searchable' => true,
            ])

            ->addColumn([
                'data'       => 'email',
                'title'      => 'Email',
                'orderable'  => true,
                'searchable' => true,
            ])

            ->addColumn([
                'data'       => 'role',
                'title'      => 'Role',
                'orderable'  => false,
                'searchable' => true,
            ])

            ->addColumn([
                'data'       => 'created_at',
                'title'      => 'Created At',
                'orderable'  => true,
                'searchable' => false,
            ])

            ->addColumn([
                'data'       => 'status_label',
                'title'      => 'Status',
                'orderable'  => false,
                'searchable' => false,
                'className'  => 'text-center',
                'width'      => '1%',
            ])

            ->addColumn([
                'data'       => 'status',
                'visible'    => false,
            ])

            ->addColumn([
                'data'       => 'action',
                'title'      => 'Action',
                'orderable'  => false,
                'searchable' => false,
                'className'  => 'text-center',
                'width'      => '5%',
            ])

            ->minifiedAjax()

            ->setTableAttribute('class', 'table table-striped dt-responsive align-middle mb-0')

            ->parameters([
                'pageLength'   => 10,
                'lengthChange' => false,
                'searching'   => true,
                'language'    => [
                    'emptyTable'  => 'No Records Found',
                    'zeroRecords' => 'No Records Found',
                ],
                'dom' => "<'row'<'col-sm-12'tr>>" .
                    "<'row'<'col-sm-5'i><'col-sm-7 d-flex justify-content-end'p>>",
                'drawCallback' => 'function() {
                    $(".action-table-data [data-bs-toggle=\'tooltip\']").tooltip();
                }',
            ]);
    }

    protected function filename(): string
    {
        return 'User_' . date('YmdHis');
    }
}