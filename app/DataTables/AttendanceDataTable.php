<?php

namespace App\DataTables;

use App\Models\Attendance;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class AttendanceDataTable extends DataTable
{
    /**
     * Build DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()

            // Perbaikan kolom Teacher agar bisa di-search berdasarkan Nama
            ->editColumn('teacher', function ($row) {
                return $row->teacher?->name ?? '-';
            })

            ->editColumn('date', function ($row) {
                return $row->date?->format('d M Y');
            })

            ->editColumn('check_in', function ($row) {
                return $row->check_in?->format('H:i') ?? '-';
            })

            ->editColumn('check_out', function ($row) {
                return $row->check_out?->format('H:i') ?? '-';
            })

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

            // Perbaikan tampilan kolom Method
            ->editColumn('method', function ($row) {
                $in = strtoupper($row->method_in ?? '-');
                $out = strtoupper($row->method_out ?? '-');
                return "$in / $out";
            })

            ->editColumn('status', function ($row) {
                return $this->statusBadge($row->status);
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at?->diffForHumans();
            })

            /* |----------------------------------------------------------------------
            | FIX SEARCH ERROR: Mengarahkan pencarian ke kolom database yang benar
            |----------------------------------------------------------------------
            */
            // Agar saat search "Yayat", DataTables mencari ke tabel teachers kolom name
            ->filterColumn('teacher', function ($query, $keyword) {
                $query->whereHas('teacher', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            // Agar saat search "RFID", DataTables mencari ke method_in atau method_out
            ->filterColumn('method', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('method_in', 'like', "%{$keyword}%")
                        ->orWhere('method_out', 'like', "%{$keyword}%");
                });
            })

            ->rawColumns([
                'status',
                'photo_check_in',
                'photo_check_out',
            ]);
    }

    /**
     * Get query source of dataTable.
     */
    public function query(Attendance $model): QueryBuilder
    {
        return $model->newQuery()
            ->with('teacher') // Eager load relasi teacher
            ->latest('date');
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('attendance-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->setTableAttribute('class', 'table table-hover align-middle mb-0')
            ->parameters([
                'pageLength'   => 10,
                'lengthChange' => true,
                'searching'    => true,
                'ordering'     => true,
                'responsive'   => true,
                'autoWidth'    => false,
                'order'        => [[2, 'desc']],
                'language'     => [
                    'search' => '',
                    'searchPlaceholder' => 'Search attendance...',
                ]
            ]);
    }

    /**
     * Get the dataTable columns definition.
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
            // Pastikan 'name' di sini sama dengan yang digunakan di filterColumn
            ['data' => 'teacher', 'name' => 'teacher.name', 'title' => 'Teacher'],
            ['data' => 'date', 'title' => 'Date'],
            ['data' => 'check_in', 'title' => 'In'],
            ['data' => 'photo_check_in', 'title' => 'Photo In', 'orderable' => false, 'searchable' => false],
            ['data' => 'check_out', 'title' => 'Out'],
            ['data' => 'photo_check_out', 'title' => 'Photo Out', 'orderable' => false, 'searchable' => false],
            ['data' => 'method', 'title' => 'Method', 'orderable' => false],
            ['data' => 'status', 'title' => 'Status', 'orderable' => false],
        ];
    }

    /**
     * UI Component: Status Badge
     */
    protected function statusBadge(string $status): string
    {
        return match ($status) {
            'hadir' => '<span class="badge bg-success"><i class="ti ti-check me-1"></i>Hadir</span>',
            'telat' => '<span class="badge bg-warning text-dark"><i class="ti ti-clock me-1"></i>Telat</span>',
            'izin'  => '<span class="badge bg-info text-dark"><i class="ti ti-file-description me-1"></i>Izin</span>',
            'sakit' => '<span class="badge bg-danger"><i class="ti ti-pill me-1"></i>Sakit</span>',
            'cuti'  => '<span class="badge bg-primary"><i class="ti ti-calendar-off me-1"></i>Cuti</span>',
            default => '<span class="badge bg-secondary"><i class="ti ti-x me-1"></i>Alpha</span>',
        };
    }



    protected function filename(): string
    {
        return 'Attendance_' . now()->format('YmdHis');
    }
}
