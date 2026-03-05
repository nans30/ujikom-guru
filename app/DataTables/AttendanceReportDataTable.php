<?php

namespace App\DataTables;

use App\Models\Attendance;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class AttendanceReportDataTable extends DataTable
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

            ->rawColumns(['status']);
    }

    /*
    |--------------------------------------------------------------------------
    | Query (Filter Bulan / Tahun / Guru)
    |--------------------------------------------------------------------------
    */
    public function query(Attendance $model): QueryBuilder
    {
        $query = $model
            ->newQuery()
            ->with('teacher');

        // Filter bulan
        if (request()->filled('month')) {
            $query->whereMonth('date', request('month'));
        }

        // Filter tahun
        if (request()->filled('year')) {
            $query->whereYear('date', request('year'));
        }

        // Filter guru
        if (request()->filled('teacher_id')) {
            $query->where('teacher_id', request('teacher_id'));
        }

        return $query->latest('date');
    }

    /*
    |--------------------------------------------------------------------------
    | HTML Builder
    |--------------------------------------------------------------------------
    */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('attendance-report-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->setTableAttribute(
                'class',
                'table table-bordered table-striped align-middle'
            )
            ->parameters([
                'pageLength'   => 25,
                'lengthChange' => true,
                'searching'    => true,
                'ordering'     => true,
                'responsive'   => true,
                'autoWidth'    => false,
                'order'        => [[2, 'desc']],
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

            ['data' => 'teacher', 'title' => 'Teacher'],
            ['data' => 'date', 'title' => 'Date'],
            ['data' => 'check_in', 'title' => 'Check In'],
            ['data' => 'check_out', 'title' => 'Check Out'],
            ['data' => 'method', 'title' => 'Method', 'orderable' => false],
            ['data' => 'status', 'title' => 'Status', 'orderable' => false],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Status Badge
    |--------------------------------------------------------------------------
    */
    protected function statusBadge(string $status): string
    {
        return match ($status) {
            'hadir' => '<span class="badge bg-success">Hadir</span>',
            'telat' => '<span class="badge bg-warning text-dark">Telat</span>',
            'izin'  => '<span class="badge bg-info text-dark">Izin</span>',
            'sakit' => '<span class="badge bg-danger">Sakit</span>',
            'cuti'  => '<span class="badge bg-primary">Cuti</span>',
            default => '<span class="badge bg-secondary">Alpha</span>',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Export Filename
    |--------------------------------------------------------------------------
    */
    protected function filename(): string
    {
        return 'Attendance_Report_' . now()->format('YmdHis');
    }
}