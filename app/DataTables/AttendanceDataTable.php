<?php

namespace App\DataTables;

use App\Models\Attendance;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class AttendanceDataTable extends DataTable
{
    /*
    |--------------------------------------------------------------------------
    | DataTable Logic
    |--------------------------------------------------------------------------
    */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()

            ->addColumn(
                'teacher',
                fn($row) => $row->teacher?->name ?? '-'
            )

            ->editColumn(
                'date',
                fn($row) => $row->date?->format('d M Y')
            )

            ->editColumn(
                'check_in',
                fn($row) => $row->check_in?->format('H:i') ?? '-'
            )

            ->editColumn(
                'check_out',
                fn($row) => $row->check_out?->format('H:i') ?? '-'
            )

            ->addColumn('photo_check_in', function ($row) {
                if (!$row->photo_check_in) return '-';

                return '
                    <img src="' . asset('storage/' . $row->photo_check_in) . '" 
                         class="rounded border"
                         style="height:40px; width:40px; object-fit:cover"
                         onclick="window.open(this.src)">
                ';
            })

            ->addColumn('photo_check_out', function ($row) {
                if (!$row->photo_check_out) return '-';

                return '
                    <img src="' . asset('storage/' . $row->photo_check_out) . '" 
                         class="rounded border"
                         style="height:40px; width:40px; object-fit:cover"
                         onclick="window.open(this.src)">
                ';
            })

            ->editColumn(
                'method',
                fn($row) =>
                strtoupper($row->method_in ?? '-') . ' / ' .
                    strtoupper($row->method_out ?? '-')
            )

            ->editColumn(
                'status',
                fn($row) => $this->statusBadge($row->status)
            )

            ->editColumn(
                'created_at',
                fn($row) => $row->created_at?->diffForHumans()
            )

            ->addColumn(
                'action',
                fn($row) => $this->actionButtons($row)
            )

            ->rawColumns([
                'status',
                'action',
                'photo_check_in',
                'photo_check_out',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Source
    |--------------------------------------------------------------------------
    */
    public function query(Attendance $model): QueryBuilder
    {
        return $model
            ->newQuery()
            ->with('teacher')
            ->latest('date');
    }

    /*
    |--------------------------------------------------------------------------
    | HTML Builder (Frontend Table Config)
    |--------------------------------------------------------------------------
    */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('attendance-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->setTableAttribute(
                'class',
                'table table-hover align-middle mb-0'
            )
            ->parameters([
                'pageLength'   => 10,
                'lengthChange' => true,
                'searching'    => true,
                'ordering'     => true,
                'responsive'   => true,
                'autoWidth'    => false,
                'order'        => [[2, 'desc']], // Urutkan berdasarkan kolom Date
                'language' => [
                    'search' => '',
                    'searchPlaceholder' => 'Search attendance...',
                ]
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Columns Definitions
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

            ['data' => 'teacher', 'title' => 'Teacher'],
            ['data' => 'date', 'title' => 'Date'],
            ['data' => 'check_in', 'title' => 'In'],
            ['data' => 'photo_check_in', 'title' => 'Photo In', 'orderable' => false],
            ['data' => 'check_out', 'title' => 'Out'],
            ['data' => 'photo_check_out', 'title' => 'Photo Out', 'orderable' => false],
            ['data' => 'method', 'title' => 'Method', 'orderable' => false],
            ['data' => 'status', 'title' => 'Status', 'orderable' => false],
            ['data' => 'action', 'title' => 'Action', 'orderable' => false, 'width' => '100px'],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | UI Component: Status Badge
    |--------------------------------------------------------------------------
    */
    protected function statusBadge(string $status): string
    {
        return match ($status) {
            'hadir' => '
                <span class="badge bg-success">
                    <i class="ti ti-check me-1"></i>Hadir
                </span>
            ',
            'telat' => '
                <span class="badge bg-warning text-dark">
                    <i class="ti ti-clock me-1"></i>Telat
                </span>
            ',
            'izin' => '
                <span class="badge bg-info text-dark">
                    <i class="ti ti-file-description me-1"></i>Izin
                </span>
            ',
            'sakit' => '
                <span class="badge bg-danger">
                    <i class="ti ti-pill me-1"></i>Sakit
                </span>
            ',
            'cuti' => '
                <span class="badge bg-primary">
                    <i class="ti ti-calendar-off me-1"></i>Cuti
                </span>
            ',
            default => '
                <span class="badge bg-secondary">
                    <i class="ti ti-x me-1"></i>Alpha
                </span>
            ',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | UI Component: Action Buttons (Unlocked)
    |--------------------------------------------------------------------------
    */
    protected function actionButtons($row): string
    {
        $editUrl   = route('admin.attendance.edit', $row->id);
        $deleteUrl = route('admin.attendance.destroy', $row->id);

        return '
            <div class="d-flex gap-2">
                <a href="' . $editUrl . '" 
                   class="btn btn-light-primary btn-icon btn-sm rounded-circle"
                   data-bs-toggle="tooltip"
                   title="Edit Data">
                    <i class="ti ti-edit fs-5"></i>
                </a>

                <button type="button" 
                   data-id="' . $row->id . '" 
                   data-url="' . $deleteUrl . '" 
                   class="btn btn-light-danger btn-icon btn-sm rounded-circle deleteBtn"
                   data-bs-toggle="tooltip"
                   title="Delete Data">
                    <i class="ti ti-trash fs-5"></i>
                </button>
            </div>
        ';
    }

    protected function filename(): string
    {
        return 'Attendance_' . now()->format('YmdHis');
    }
}