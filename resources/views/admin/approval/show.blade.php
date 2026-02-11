@extends('layouts.admin', ['title' => 'Approval Detail'])

@section('content')
@include('admin.partials.page-title', [
    'subtitle' => 'Approval',
    'title' => 'Approval Detail'
])

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Approval Detail</h3>
                    <a href="{{ route('admin.approval.index') }}"
                       class="btn btn-secondary btn-sm">
                        <i class="ti ti-arrow-left me-1"></i>
                        Back
                    </a>
                </div>

                {{-- BODY --}}
                <div class="card-body">
                    <div class="row">

                        {{-- Teacher --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teacher</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ $approval->teacher?->name ?? '-' }}"
                                   readonly>
                        </div>

                        {{-- Date Range --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ $approval->start_date->format('d M Y') }}
                                   @if($approval->start_date != $approval->end_date)
                                       - {{ $approval->end_date->format('d M Y') }}
                                   @endif"
                                   readonly>
                        </div>

                        {{-- Type --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type</label>
                            <div>
                                @switch($approval->type)
                                    @case('sakit')
                                        <span class="badge bg-danger">
                                            <i class="ti ti-heartbeat me-1"></i>Sakit
                                        </span>
                                        @break
                                    @case('izin')
                                        <span class="badge bg-warning text-dark">
                                            <i class="ti ti-file-description me-1"></i>Izin
                                        </span>
                                        @break
                                    @case('cuti')
                                        <span class="badge bg-info text-dark">
                                            <i class="ti ti-calendar-off me-1"></i>Cuti
                                        </span>
                                        @break
                                    @case('dinas')
                                        <span class="badge bg-primary text-white">
                                            <i class="ti ti-building-factory me-1"></i>Dinas
                                        </span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">-</span>
                                @endswitch
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <div>
                                @switch($approval->status)
                                    @case('approved')
                                        <span class="badge bg-success">
                                            <i class="ti ti-circle-check me-1"></i>Approved
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger">
                                            <i class="ti ti-circle-x me-1"></i>Rejected
                                        </span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">
                                            <i class="ti ti-clock me-1"></i>Pending
                                        </span>
                                @endswitch
                            </div>
                        </div>

                        {{-- Reason --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Reason</label>
                            <textarea class="form-control"
                                      rows="3"
                                      readonly>{{ $approval->reason }}</textarea>
                        </div>

                        {{-- Proof File --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Proof File</label>

                            @if ($approval->proof_file)
                                <div>
                                    <a href="{{ Storage::url($approval->proof_file) }}"
                                       target="_blank"
                                       class="btn btn-info btn-sm">
                                        <i class="ti ti-eye me-1"></i>
                                        View Proof
                                    </a>
                                </div>
                            @else
                                <p class="text-muted mb-0">No proof uploaded</p>
                            @endif
                        </div>

                        {{-- Approved Info --}}
                        @if ($approval->approved_at)
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Approved At</label>
                                <input type="text"
                                       class="form-control"
                                       value="{{ $approval->approved_at->format('d M Y H:i') }}"
                                       readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Approved By</label>
                                <input type="text"
                                       class="form-control"
                                       value="{{ $approval->approver?->name ?? '-' }}"
                                       readonly>
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
