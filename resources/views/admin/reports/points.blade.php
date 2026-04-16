@extends('layouts.admin', ['title' => 'Point Transaction Report'])

@section('content')

@include('admin.partials.page-title', ['subtitle'=>'Report','title'=>'Point Transaction Report'])

<div class="card">
    <div class="card-body">

        {{-- FILTER FORM --}}
        <form method="GET" class="row mb-4 gy-2">

            <!-- Teacher -->
            <div class="col-md-3">
                <label class="form-label text-[10px] font-black uppercase tracking-widest text-gray-500">Teacher</label>
                <select name="teacher_id" class="form-control select2">
                    <option value="">-- All Teachers --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Type -->
            <div class="col-md-2">
                <label class="form-label text-[10px] font-black uppercase tracking-widest text-gray-500">Type</label>
                <select name="type" class="form-control">
                    <option value="">-- All Types --</option>
                    <option value="EARN" {{ request('type') == 'EARN' ? 'selected' : '' }}>EARN (Income)</option>
                    <option value="SPEND" {{ request('type') == 'SPEND' ? 'selected' : '' }}>SPEND (Withdrawal)</option>
                    <option value="PENALTY" {{ request('type') == 'PENALTY' ? 'selected' : '' }}>PENALTY (Deduction)</option>
                </select>
            </div>

            <!-- Start Date -->
            <div class="col-md-2">
                <label class="form-label text-[10px] font-black uppercase tracking-widest text-gray-500">From</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>

            <!-- End Date -->
            <div class="col-md-2">
                <label class="form-label text-[10px] font-black uppercase tracking-widest text-gray-500">To</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>

            <!-- Buttons -->
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="{{ route('admin.reports.points') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr class="bg-light">
                        <th width="60">No</th>
                        <th>Date & Time</th>
                        <th>Teacher</th>
                        <th>Type</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Balance After</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledgers as $i => $row)
                        <tr>
                            <td>{{ ($ledgers->currentPage() - 1) * $ledgers->perPage() + $i + 1 }}</td>
                            <td>
                                <div class="fw-bold">{{ $row->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $row->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs rounded-circle overflow-hidden border border-light d-flex align-items-center justify-content-center bg-white">
                                        @if($row->teacher->has_real_photo)
                                            <img src="{{ $row->teacher->photo }}" class="w-100 h-100 object-cover" width="30">
                                        @else
                                            <span class="avatar-title text-bg-info w-100 h-100 d-flex align-items-center justify-content-center fw-black" style="font-size: 10px;">
                                                {{ $row->teacher->initial }}
                                            </span>
                                        @endif
                                    </div>
                                    <span class="fw-medium">{{ $row->teacher->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                @if($row->transaction_type == 'EARN')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 uppercase font-black text-[9px]">EARN</span>
                                @elseif($row->transaction_type == 'SPEND')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 uppercase font-black text-[9px]">SPEND</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 uppercase font-black text-[9px]">PENALTY</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold {{ $row->amount > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $row->amount > 0 ? '+' : '' }}{{ number_format($row->amount, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-bold text-dark">
                                {{ number_format($row->current_balance, 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="text-muted">{{ $row->description }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ti ti-database-off fs-1 d-block mb-2"></i>
                                    No point transactions found
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($ledgers->count())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Showing {{ $ledgers->firstItem() }} to {{ $ledgers->lastItem() }} of {{ $ledgers->total() }} entries
                </div>
                <div>
                    {{ $ledgers->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif

    </div>
</div>

@endsection

@push('styles')
<style>
    .avatar-xs {
        height: 2rem;
        width: 2rem;
    }
    .badge {
        letter-spacing: 0.05em;
    }
</style>
@endpush
