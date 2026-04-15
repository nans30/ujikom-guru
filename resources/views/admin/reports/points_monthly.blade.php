@extends('layouts.admin', ['title' => 'Monthly Point Summary'])

@section('content')

@include('admin.partials.page-title', ['subtitle'=>'Report','title'=>'Monthly Point Summary'])

<div class="card">
    <div class="card-body">

        {{-- FILTER FORM --}}
        <form method="GET" class="row mb-4 align-items-end gy-2">
            <div class="col-md-2">
                <label class="form-label text-[10px] font-black uppercase tracking-widest text-gray-500">Month</label>
                <select name="month" class="form-control">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-[10px] font-black uppercase tracking-widest text-gray-500">Year</label>
                <select name="year" class="form-control">
                    @for($y=date('Y'); $y>=2020; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.reports.points.export', ['scope'=>'monthly', 'month'=>$month, 'year'=>$year, 'type'=>'excel']) }}" class="btn btn-success">Excel</a>
                <a href="{{ route('admin.reports.points.export', ['scope'=>'monthly', 'month'=>$month, 'year'=>$year, 'type'=>'csv']) }}" class="btn btn-warning">CSV</a>
                <a href="{{ route('admin.reports.points.export', ['scope'=>'monthly', 'month'=>$month, 'year'=>$year, 'type'=>'pdf']) }}" class="btn btn-danger">PDF</a>
            </div>
        </form>

        <hr class="mb-4">

        <h5 class="mb-4 text-center font-black uppercase tracking-widest text-primary">
            Rekap Poin: {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
        </h5>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr class="bg-light text-[11px] font-black uppercase tracking-tighter">
                        <th width="60" class="text-center">No</th>
                        <th>Teacher</th>
                        <th class="text-end text-success">Total Earned (+)</th>
                        <th class="text-end text-primary">Total Spent (-)</th>
                        <th class="text-end text-danger">Total Penalty (-)</th>
                        <th class="text-end bg-light fw-bold">Net Change</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($summary as $i => $row)
                        @php
                            $net = $row->total_earned - $row->total_spent - $row->total_penalty;
                        @endphp
                        <tr>
                            <td class="text-center font-bold text-muted">{{ $i + 1 }}</td>
                            <td>
                                <div class="fw-black text-dark tracking-tight">{{ $row->teacher->name ?? '-' }}</div>
                            </td>
                            <td class="text-end fw-bold text-green-600">
                                {{ number_format($row->total_earned, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-bold text-blue-600">
                                {{ number_format($row->total_spent, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-bold text-red-600">
                                {{ number_format($row->total_penalty, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-black {{ $net >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $net > 0 ? '+' : '' }}{{ number_format($net, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted italic">No records for this period</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

@endsection
