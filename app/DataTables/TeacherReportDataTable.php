<?php

namespace App\DataTables;

use App\Models\Teacher;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class TeacherReportDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('position', fn($row) => $row->position?->name ?? '-')
            ->editColumn('jenis_kelamin', fn($row) => match ($row->jenis_kelamin) {
                'P' => 'Perempuan',
                'L' => 'Laki-laki',
                default => '-',
            })
            ->editColumn(
                'is_active',
                fn($row) =>
                $row->is_active
                    ? '<span class="badge bg-success">Aktif</span>'
                    : '<span class="badge bg-danger">Tidak Aktif</span>'
            )
            ->addColumn('created_by', fn($row) => $row->createdBy?->name ?? '-')
            ->editColumn('created_at', fn($row) => $row->created_at?->format('d-m-Y H:i'))
            ->rawColumns(['is_active']);
    }

    public function query(Teacher $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['position', 'createdBy']);

        if ($position = request('position_id')) {
            $query->where('position_id', $position);
        }

        if (request()->filled('is_active')) {
            $query->where('is_active', request('is_active'));
        }

        if ($name = request('name')) {
            $query->where('name', 'like', "%{$name}%");
        }

        return $query->latest();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('teacher-report-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->setTableAttribute('class', 'table table-striped dt-responsive nowrap align-middle')
            ->parameters([
                'processing' => true,
                'serverSide' => true,
                'pageLength' => 10,
                'lengthChange' => true,
                'searching' => true,
                'ordering' => true,
                'responsive' => true,
                'autoWidth' => false,
                'language' => [
                    'emptyTable' => 'Tidak ada data guru',
                    'zeroRecords' => 'Guru tidak ditemukan',
                ],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            ['data' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false, 'width' => '50px'],
            ['data' => 'nip', 'title' => 'NIP'],
            ['data' => 'name', 'title' => 'Nama'],
            ['data' => 'position', 'title' => 'Jabatan / Posisi'],
            ['data' => 'jenis_kelamin', 'title' => 'Jenis Kelamin'],
            ['data' => 'created_by', 'title' => 'Dibuat Oleh'],
            ['data' => 'is_active', 'title' => 'Status'],
            ['data' => 'created_at', 'title' => 'Dibuat Pada'],
        ];
    }

    protected function filename(): string
    {
        return 'Laporan_Guru_' . now()->format('YmdHis');
    }
}