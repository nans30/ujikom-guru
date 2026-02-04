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

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Approval Detail</h3>
                    <a href="{{ route('admin.approval.index') }}"
                       class="btn btn-secondary btn-sm">
                        <i class="ti ti-arrow-left me-1"></i>
                        Back
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">

                        {{-- Teacher --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teacher</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ $approval->teacher?->name }}"
                                   readonly>
                        </div>

                        {{-- Date --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ $approval->date?->format('d M Y') }}"
                                   readonly>
                        </div>

                        {{-- Type --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ ucfirst($approval->type) }}"
                                   readonly>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ ucfirst($approval->status) }}"
                                   readonly>
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
                                    <a href="{{ asset('storage/' . $approval->proof_file) }}"
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
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
