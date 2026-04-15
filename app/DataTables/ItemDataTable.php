<?php

namespace App\DataTables;

use App\Models\Item;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class ItemDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', fn($row) => '<input class="form-check-input file-item-check" type="checkbox" value="' . $row->id . '">')

            // Nama Item
            ->editColumn('item_name', fn($row) => '<strong>' . ucfirst($row->item_name) . '</strong>')

            // Harga Poin (Warna Biru agar kontras)
            ->editColumn('point_cost', function ($row) {
                return '<span class="badge bg-label-primary text-dark fw-bold">' . number_format($row->point_cost) . ' Poin</span>';
            })

            // Stok / Limit
            ->editColumn('stock_limit', function ($row) {
                return $row->stock_limit ? $row->stock_limit . ' Kali' : '<span class="text-muted">Tanpa Batas</span>';
            })

            // Status (Aktif/Nonaktif)
            ->editColumn('status', function ($row) {
                $class = $row->status == 1 ? 'bg-success' : 'bg-danger';
                $text = $row->status == 1 ? 'Aktif' : 'Nonaktif';
                return '<span class="badge ' . $class . '">' . $text . '</span>';
            })

            ->editColumn('created_at', fn($row) => $row->created_at?->diffForHumans())

            ->editColumn('action', function ($row) {
                $editUrl = route('admin.item.edit', $row->id);
                $deleteUrl = route('admin.item.destroy', $row->id);

                return '
                    <a href="' . $editUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle" data-bs-toggle="tooltip" title="Edit">
                        <i class="ti ti-edit fs-lg"></i>
                    </a>
                    <a href="javascript:void(0)" data-id="' . $row->id . '" data-url="' . $deleteUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle deleteBtn" data-bs-toggle="tooltip" title="Delete">
                        <i class="ti ti-trash fs-lg"></i>
                    </a>
                ';
            })
            ->rawColumns(['checkbox', 'item_name', 'point_cost', 'stock_limit', 'status', 'action']);
    }

    public function query(Item $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('item-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->setTableAttribute('class', 'table table-striped dt-responsive align-middle mb-0')
            ->parameters([
                'pageLength' => 10,
                'lengthChange' => false,
                'searching' => true,
                'language' => [
                    'emptyTable' => 'Belum ada item reward',
                    'zeroRecords' => 'Item tidak ditemukan',
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
            ['data' => 'item_name', 'title' => 'Nama Item'],
            ['data' => 'extra_minutes', 'title' => 'Dispensasi'],
            ['data' => 'point_cost', 'title' => 'Harga Poin'],
            ['data' => 'stock_limit', 'title' => 'Batas Tukar'],
            ['data' => 'status', 'title' => 'Status'],
            ['data' => 'created_at', 'title' => "Dibuat"],
            ['data' => 'action', 'title' => "Aksi", 'orderable' => false, 'searchable' => false],
        ];
    }

    protected function filename(): string
    {
        return 'Item_' . date('YmdHis');
    }
}
