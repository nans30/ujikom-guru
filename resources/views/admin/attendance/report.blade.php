@extends('layouts.admin', ['title' => 'Attendance Report'])

@section('content')
@include('admin.partials.page-title', ['subtitle'=>'Report','title'=>'Attendance Report'])

<div class="card">
    <div class="card-body">

        <form method="GET" class="row mb-4">

            <div class="col-md-3">
                <select name="month" class="form-control">
                    <option value="">-- Select Month --</option>
                    @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" {{ ($month ?? '') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-3">
                <select name="year" class="form-control">
                    <option value="">-- Select Year --</option>
                    @for($y=date('Y');$y>=2020;$y--)
                        <option value="{{ $y }}" {{ ($year ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search Teacher..." value="{{ $search ?? '' }}">
            </div>

            <div class="col-md-3 text-end">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.attendance.report') }}" class="btn btn-secondary">Reset</a>
            </div>

        </form>

        <div class="mb-3">
            <a href="{{ route('admin.attendance.report.export', ['type'=>'excel','month'=>$month,'year'=>$year,'search'=>$search]) }}" class="btn btn-success">Export Excel</a>
            <a href="{{ route('admin.attendance.report.export', ['type'=>'csv','month'=>$month,'year'=>$year,'search'=>$search]) }}" class="btn btn-warning">Export CSV</a>
            <a href="{{ route('admin.attendance.report.export', ['type'=>'pdf','month'=>$month,'year'=>$year,'search'=>$search]) }}" class="btn btn-danger">Export PDF</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Teacher</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $i => $row)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $row->teacher->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->date)->format('d-m-Y') }}</td>
                            <td>{{ ucfirst($row->status) }}</td>
                            <td>{{ $row->check_in ?? '-' }}</td>
                            <td>{{ $row->check_out ?? '-' }}</td>
                            <td>{{ strtoupper($row->method_in ?? '-') }} / {{ strtoupper($row->method_out ?? '-') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No attendance found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection