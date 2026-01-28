<?php

namespace App\DataTables;

use App\Models\Attendance;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class AttendanceDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn() // <-- NO
            ->editColumn('teacher', fn($row) => $row->teacher?->name ?? '-')
            ->editColumn('date', fn($row) => $row->date?->format('d M Y'))
            ->editColumn('check_in', fn($row) => $row->check_in?->format('H:i') ?? '-')
            ->editColumn('check_out', fn($row) => $row->check_out?->format('H:i') ?? '-')
            ->editColumn(
                'method',
                fn($row) =>
                strtoupper($row->method_in ?? '-') . ' / ' . strtoupper($row->method_out ?? '-')
            )
            ->editColumn(
                'status',
                fn($row) =>
                '<span class="badge bg-' . $this->statusColor($row->status) . '">' .
                    ucfirst($row->status) .
                    '</span>'
            )
            ->editColumn('created_at', fn($row) => $row->created_at?->diffForHumans())
            ->addColumn('action', function ($row) {
                $editUrl = route('admin.attendance.edit', $row->id);
                $deleteUrl = route('admin.attendance.destroy', $row->id);

                return '
                    <a href="' . $editUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle" title="Edit">
                        <i class="ti ti-edit fs-lg"></i>
                    </a>
                    <a href="javascript:void(0)" 
                       data-id="' . $row->id . '" 
                       data-url="' . $deleteUrl . '" 
                       class="btn btn-light btn-icon btn-sm rounded-circle deleteBtn" 
                       title="Delete">
                        <i class="ti ti-trash fs-lg"></i>
                    </a>
                ';
            })
            ->rawColumns(['status', 'action']);
    }

    public function query(Attendance $model): QueryBuilder
    {
        return $model->newQuery()->with('teacher');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('attendance-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->setTableAttribute('class', 'table table-striped dt-responsive align-middle mb-0')
            ->parameters([
                'pageLength' => 10,
                'lengthChange' => false,
                'searching' => true,
                'order' => [[1, 'desc']],
                'language' => [
                    'emptyTable' => 'No records found',
                    'zeroRecords' => 'No matching records found',
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
                'searchable' => false
            ],
            [
                'data' => 'teacher',
                'title' => 'Teacher',
                'orderable' => false, // 🔴 penting
                'searchable' => false
            ],
            [
                'data' => 'date',
                'title' => 'Date'
            ],
            [
                'data' => 'check_in',
                'title' => 'Check In'
            ],
            [
                'data' => 'check_out',
                'title' => 'Check Out'
            ],
            [
                'data' => 'method',
                'title' => 'Method',
                'orderable' => false,
                'searchable' => false
            ],
            [
                'data' => 'status',
                'title' => 'Status',
                'orderable' => false,
                'searchable' => false
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

    protected function statusColor(string $status): string
    {
        return match ($status) {
            'hadir' => 'success',
            'telat' => 'warning',
            'izin', 'sakit' => 'info',
            default => 'danger',
        };
    }

    protected function filename(): string
    {
        return 'Attendance_' . date('YmdHis');
    }
}