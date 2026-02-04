<?php

namespace App\DataTables;

use App\Models\Approval;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class ApprovalDataTable extends DataTable
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

            ->editColumn(
                'teacher',
                fn($row) => $row->teacher?->name ?? '-'
            )

            ->editColumn(
                'date',
                fn($row) => $row->date?->format('d M Y')
            )

            ->editColumn(
                'type',
                fn($row) => $this->typeBadge($row->type)
            )

            ->editColumn(
                'status',
                fn($row) => $this->statusBadge($row->status)
            )

            ->addColumn(
                'proof',
                fn($row) => $this->proofButton($row->proof_file)
            )

            ->addColumn(
                'action',
                fn($row) => $this->actionButtons($row)
            )

            ->rawColumns(['type', 'status', 'proof', 'action']);
    }

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */
    public function query(Approval $model): QueryBuilder
    {
        return $model
            ->newQuery()
            ->with('teacher')
            ->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | HTML Builder
    |--------------------------------------------------------------------------
    */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('approval-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->setTableAttribute(
                'class',
                'table table-striped dt-responsive align-middle mb-0'
            )
            ->parameters([
                'pageLength'   => 10,
                'lengthChange' => false,
                'searching'    => true,
                'ordering'     => true,
                'responsive'   => true,
                'autoWidth'    => false,
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
                'title'      => '#',
                'orderable'  => false,
                'searchable' => false,
                'width'      => '40px',
            ],

            ['data' => 'teacher', 'title' => 'Teacher'],
            ['data' => 'date', 'title' => 'Date'],
            ['data' => 'type', 'title' => 'Type'],
            ['data' => 'reason', 'title' => 'Reason'],

            [
                'data'       => 'proof',
                'title'      => 'Proof',
                'orderable'  => false,
                'searchable' => false,
            ],

            ['data' => 'status', 'title' => 'Status'],

            [
                'data'       => 'action',
                'title'      => 'Action',
                'orderable'  => false,
                'searchable' => false,
                'width'      => '160px',
            ],
        ];
    }

    protected function filename(): string
    {
        return 'Approval_' . now()->format('YmdHis');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers (UI)
    |--------------------------------------------------------------------------
    */

    protected function typeBadge(string $type): string
    {
        return match ($type) {
            'sakit' => '
                <span class="badge bg-danger">
                    <i class="ti ti-heartbeat me-1"></i>Sakit
                </span>
            ',
            'izin' => '
                <span class="badge bg-warning text-dark">
                    <i class="ti ti-file-description me-1"></i>Izin
                </span>
            ',
            default => '-',
        };
    }

    protected function statusBadge(string $status): string
    {
        return match ($status) {
            'approved' => '
                <span class="badge bg-success">
                    <i class="ti ti-circle-check me-1"></i>Approved
                </span>
            ',
            'rejected' => '
                <span class="badge bg-danger">
                    <i class="ti ti-circle-x me-1"></i>Rejected
                </span>
            ',
            default => '
                <span class="badge bg-secondary">
                    <i class="ti ti-clock me-1"></i>Pending
                </span>
            ',
        };
    }

    protected function proofButton(?string $file): string
    {
        if (!$file) return '-';

        return '
            <a href="' . Storage::url($file) . '" 
               target="_blank"
               class="btn btn-light btn-icon btn-sm rounded-circle"
               title="View Proof">
                <i class="ti ti-eye"></i>
            </a>
        ';
    }

    protected function actionButtons($row): string
    {
        $detailUrl  = route('admin.approval.show', $row->id);

        if ($row->status !== 'pending') {
            return '
            <a href="' . $detailUrl . '" 
               class="btn btn-light btn-icon btn-sm rounded-circle"
               title="Detail">
                <i class="ti ti-info-circle"></i>
            </a>
        ';
        }

        $approveUrl = route('admin.approval.approve', $row->id);
        $rejectUrl  = route('admin.approval.reject', $row->id);

        return '
        <a href="' . $detailUrl . '" 
           class="btn btn-light btn-icon btn-sm rounded-circle me-1"
           title="Detail">
            <i class="ti ti-info-circle"></i>
        </a>

        <form action="' . $approveUrl . '" method="POST" class="d-inline">
            ' . csrf_field() . '
            <button class="btn btn-success btn-icon btn-sm rounded-circle me-1"
                    title="Approve">
                <i class="ti ti-check"></i>
            </button>
        </form>

        <form action="' . $rejectUrl . '" method="POST" class="d-inline">
            ' . csrf_field() . '
            <button class="btn btn-danger btn-icon btn-sm rounded-circle"
                    title="Reject">
                <i class="ti ti-x"></i>
            </button>
        </form>
    ';
    }
}