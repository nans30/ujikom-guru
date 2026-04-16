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

            // ===== IMAGE =====
            ->addColumn('image', function ($row) {
                if ($row->has_real_photo) {
                    $imageTag = '<img src="' . $row->photo . '"
                        class="img-fluid avatar-md rounded-circle">';
                } else {
                    $imageTag = '
                        <div class="avatar-sm">
                            <span class="avatar-title text-bg-info rounded-circle">
                                ' . $row->initial . '
                            </span>
                        </div>';
                }

                return '<div class="d-flex align-items-center justify-content-center">' . $imageTag . '</div>';
            })

            // ===== GLOBAL SEARCH =====
            ->filter(function ($query) {
                if ($search = request('search.value')) {
                    $query->where(function ($q) use ($search) {
                        $q->where('teachers.nip', 'like', "%{$search}%")
                            ->orWhere('teachers.name', 'like', "%{$search}%")
                            ->orWhere('teachers.nuptk', 'like', "%{$search}%")
                            ->orWhere('teachers.rfid_uid', 'like', "%{$search}%")
                            ->orWhere('teachers.tempat_lahir', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($u) use ($search) {
                                $u->where('email', 'like', "%{$search}%");
                            })
                            ->orWhereHas('position', function ($p) use ($search) {
                                $p->where('name', 'like', "%{$search}%");
                            });
                    });
                }
            }, true)

            // ===== INDEX =====
            ->addIndexColumn()

            // ===== EMAIL (DARI USERS) =====
            ->addColumn('email', fn($row) => $row->user?->email ?? '-')

            // ===== POSITION =====
            ->addColumn('position', fn($row) => $row->position?->name ?? '-')

            // ===== JENIS KELAMIN =====
            ->editColumn('jenis_kelamin', fn($row) => match ($row->jenis_kelamin) {
                'P' => 'Perempuan',
                'L' => 'Laki-laki',
                default => '-',
            })

            // ===== STATUS =====
            ->editColumn(
                'is_active',
                fn($row) =>
                $row->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>'
            )

            // ===== POINT BALANCE =====
            ->editColumn('point_balance', function ($row) {
                $class = $row->point_balance >= 0 ? 'text-success' : 'text-danger';
                return '<strong class="' . $class . '">' . number_format($row->point_balance) . ' Poin</strong>';
            })

            // ===== CREATED AT =====
            ->editColumn('created_at', fn($row) => $row->created_at?->diffForHumans())

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
            ->rawColumns(['image', 'is_active', 'point_balance', 'action']);
    }

    // =============================
    // QUERY
    // =============================
    public function query(Teacher $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['user', 'position']) // 🔥 Include position relasi
            ->latest();
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
            [
                'data'       => 'image',
                'title'      => 'Photo',
                'orderable'  => false,
                'searchable' => false,
                'className'  => 'text-center',
                'width'      => '50px',
            ],
            ['data' => 'nip',       'title' => 'NIP'],
            ['data' => 'name',      'title' => 'Name'],
            ['data' => 'position',  'title' => 'Position'], // Tambahan posisi
            ['data' => 'jenis_kelamin', 'title' => 'JK'],
            ['data' => 'email',     'title' => 'Email'], // dari users
            ['data' => 'rfid_uid',  'title' => 'RFID UID'],
            ['data' => 'point_balance', 'title' => 'Poin'],
            ['data' => 'is_active', 'title' => 'Status'],
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