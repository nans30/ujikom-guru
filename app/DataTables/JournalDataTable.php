<?php

namespace App\DataTables;

use App\Models\Journal;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Carbon\Carbon;

class JournalDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', fn($row) => '<input class="form-check-input file-item-check" type="checkbox" value="' . $row->id . '">')
            ->addColumn('teacher', fn($row) => $row->teacher?->name ?? '-')
            ->addColumn('schedule', function ($row) {
                if (!$row->schedule) return '-';

                // Format waktu dari start_time sampai end_time
                $startTime = $row->schedule->start_time ? Carbon::parse($row->schedule->start_time)->format('H:i') : '--:--';
                $endTime = $row->schedule->end_time ? Carbon::parse($row->schedule->end_time)->format('H:i') : '--:--';

                return "<strong>{$row->schedule->subject}</strong><br>" .
                    "<small class='text-muted'>{$row->schedule->class_name} | {$startTime} - {$endTime}</small>";
            })
            ->editColumn('created_at', fn($row) => $row->created_at?->format('d M Y H:i'))
            ->editColumn('action', function ($row) {
                $editUrl = route('admin.journal.edit', $row->id);
                $deleteUrl = route('admin.journal.destroy', $row->id);

                return '
                    <a href="' . $editUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle" data-bs-toggle="tooltip" title="Edit">
                        <i class="ti ti-edit fs-lg"></i>
                    </a>
                    <a href="javascript:void(0)" data-id="' . $row->id . '" data-url="' . $deleteUrl . '" class="btn btn-light btn-icon btn-sm rounded-circle deleteBtn" data-bs-toggle="tooltip" title="Delete">
                        <i class="ti ti-trash fs-lg"></i>
                    </a>
                ';
            })
            ->rawColumns(['checkbox', 'schedule', 'action']); // Tambahkan 'schedule' ke rawColumns karena pakai HTML (<strong>/<small>)
    }

    public function query(Journal $model): QueryBuilder
    {
        return $model->newQuery()
            ->select('journals.*')
            ->join('schedules', 'journals.schedule_id', '=', 'schedules.id')
            ->with(['teacher', 'schedule'])
            ->orderBy('schedules.start_time', 'asc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('journal-table')
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
            ['data' => 'checkbox', 'title' => '', 'orderable' => false, 'searchable' => false],
            ['data' => 'teacher', 'title' => 'Teacher'],
            ['data' => 'schedule', 'title' => 'Subject & Schedule Info'],
            ['data' => 'created_at', 'title' => "Input Date"],
            ['data' => 'action', 'title' => "Action", 'orderable' => false, 'searchable' => false],
        ];
    }

    protected function filename(): string
    {
        return 'Journal_' . date('YmdHis');
    }
}