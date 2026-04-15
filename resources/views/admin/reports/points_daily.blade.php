@extends('layouts.admin', ['title' => 'Daily Point Report'])

@section('content')

@include('admin.partials.page-title', ['subtitle'=>'Report','title'=>'Daily Point Report'])

<div class="card">
    <div class="card-body">

        {{-- FILTER FORM --}}
        <form method="GET" class="row mb-4 align-items-end">
            <div class="col-md-3">
                <label class="form-label text-[10px] font-black uppercase tracking-widest text-gray-500">Select Date</label>
                <input type="date" name="date" class="form-control" value="{{ $date }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.reports.points.export', ['scope'=>'daily', 'date'=>$date, 'type'=>'excel']) }}" class="btn btn-success">Excel</a>
                <a href="{{ route('admin.reports.points.export', ['scope'=>'daily', 'date'=>$date, 'type'=>'csv']) }}" class="btn btn-warning">CSV</a>
                <a href="{{ route('admin.reports.points.export', ['scope'=>'daily', 'date'=>$date, 'type'=>'pdf']) }}" class="btn btn-danger">PDF</a>
            </div>
        </form>

        <hr class="mb-4">

        <h5 class="mb-4 text-center font-black uppercase tracking-widest">
            Data Transaksi Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}
        </h5>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr class="bg-light">
                        <th width="60">No</th>
                        <th>Time</th>
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
                            <td>{{ $i + 1 }}</td>
                            <td><span class="fw-bold">{{ $row->created_at->format('H:i:s') }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
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
                            <td><span class="text-muted small">{{ $row->description }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No transactions on this date</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

@endsection
