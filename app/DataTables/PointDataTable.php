<?php

namespace App\DataTables;

use App\Models\Point;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class PointDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', fn($row) => '<input class="form-check-input file-item-check" type="checkbox" value="' . $row->id . '">')

            ->editColumn('name', fn($row) => ucfirst($row->name))

            // Menampilkan Logika dengan teks Hitam (text-dark)
            ->addColumn('logic', function ($row) {
                // Saya tambahkan class 'text-dark' agar teks jam tidak putih/samar
                return '<span class="badge bg-label-info text-dark fw-bold">' . $row->condition_operator . ' ' . $row->condition_value . '</span>';
            })

            // Menampilkan Poin dengan warna (Hijau jika +, Merah jika -)
            ->editColumn('point_modifier', function ($row) {
                $class = $row->point_modifier >= 0 ? 'text-success' : 'text-danger';
                $prefix = $row->point_modifier >= 0 ? '+' : '';
                return '<strong class="' . $class . '">' . $prefix . $row->point_modifier . ' Poin</strong>';
            })

            ->editColumn('created_at', fn($row) => $row->created_at?->diffForHumans())

            ->editColumn('action', function ($row) {
                $editUrl = route('admin.point.edit', $row->id);
                $deleteUrl = route('admin.point.destroy', $row->id);

                return '
                    <a href="' . $editUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle" data-bs-toggle="tooltip" title="Edit">
                        <i class="ti ti-edit fs-lg"></i>
                    </a>
                    <a href="javascript:void(0)" data-id="' . $row->id . '" data-url="' . $deleteUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle deleteBtn" data-bs-toggle="tooltip" title="Delete">
                        <i class="ti ti-trash fs-lg"></i>
                    </a>
                ';
            })
            ->rawColumns(['checkbox', 'logic', 'point_modifier', 'action']);
    }

    public function query(Point $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('point-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->setTableAttribute('class', 'table table-striped dt-responsive align-middle mb-0')
            ->parameters([
                'pageLength' => 10,
                'lengthChange' => false,
                'searching' => true,
                'language' => [
                    'emptyTable' => 'No records found',
                    'zeroRecords' => 'No matching records found',
                ],
                'dom' => "<'row'<'col-sm-12'tr>>" .
                    "<'row'<'col-sm-5'i><'col-sm-7 d-flex justify-content-end'p>>",
                'drawCallback' => 'function() {
                    if (typeof feather !== "undefined") feather.replace();
                    $(".deleteBtn").tooltip();
                }',
                'initComplete' => 'function() {
                    $(".dataTables_filter").appendTo(".search-input");
                }',
            ]);
    }

    protected function getColumns(): array
    {
        return [
            ['data' => 'checkbox', 'title' => '', 'orderable' => false, 'searchable' => false, 'width' => '40px'],
            ['data' => 'name', 'title' => 'Nama Aturan'],
            ['data' => 'logic', 'title' => 'Kondisi'],
            ['data' => 'point_modifier', 'title' => 'Poin'],
            ['data' => 'created_at', 'title' => "Dibuat"],
            ['data' => 'action', 'title' => "Aksi", 'orderable' => false, 'searchable' => false],
        ];
    }

    protected function filename(): string   
    {
        return 'Point_' . date('YmdHis');
    }
}
