<?php

namespace App\DataTables;

use App\Models\Schedule;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class ScheduleDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with('teacher')))
            ->addColumn('checkbox', fn($row) => '<input class="form-check-input file-item-check" type="checkbox" value="' . $row->id . '">')
            ->editColumn('created_at', fn($row) => $row->created_at?->diffForHumans())
            ->addColumn('teacher_name', fn($row) => $row->teacher?->name ?? '-')
            ->addColumn('subject', fn($row) => ucfirst($row->subject))
            ->addColumn('class_name', fn($row) => $row->class_name ?? '-')
            ->addColumn('day_of_week', fn($row) => $row->day_of_week)
            ->addColumn('start_time', fn($row) => $row->start_time)
            ->addColumn('end_time', fn($row) => $row->end_time)
            ->addColumn('action', function ($row) {
                $editUrl = route('admin.schedule.edit', $row->id);
                $deleteUrl = route('admin.schedule.destroy', $row->id);

                return '
                    <a href="' . $editUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle" data-bs-toggle="tooltip" title="Edit">
                        <i class="ti ti-edit fs-lg"></i>
                    </a>
                    <a href="javascript:void(0)" data-id="' . $row->id . '" data-url="' . $deleteUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle deleteBtn" data-bs-toggle="tooltip" title="Delete">
                        <i class="ti ti-trash fs-lg"></i>
                    </a>
                ';
            })
            ->rawColumns(['checkbox', 'action']);
    }

    public function query(Schedule $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('schedule-table')
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
                    feather.replace();
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
            ['data' => 'checkbox', 'title' => '<input type="checkbox" id="check-all">', 'orderable' => false, 'searchable' => false],
            ['data' => 'teacher_name', 'title' => 'Guru'],
            ['data' => 'subject', 'title' => 'Mata Pelajaran'],
            ['data' => 'class_name', 'title' => 'Kelas'],
            ['data' => 'day_of_week', 'title' => 'Hari'],
            ['data' => 'start_time', 'title' => 'Jam Mulai'],
            ['data' => 'end_time', 'title' => 'Jam Selesai'],
            ['data' => 'status', 'title' => 'Status'],
            ['data' => 'created_at', 'title' => "Created At"],
            ['data' => 'action', 'title' => "Action", 'orderable' => false, 'searchable' => false],
        ];
    }

    protected function filename(): string
    {
        return 'Schedule_' . date('YmdHis');
    }
}