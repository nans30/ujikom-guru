@extends('layouts.admin', ['title' => 'Attendance Report'])

@section('content')

@include('admin.partials.page-title', ['subtitle' => 'Report', 'title' => 'Attendance Report'])

<div class="card">
    <div class="card-body">

        <form method="GET" class="row mb-4">

            <div class="col-md-3">
                <select name="month" class="form-control">
                    <option value="">-- Select Month --</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-3">
                <select name="year" class="form-control">
                    <option value="">-- Select Year --</option>
                    @for ($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-3">
                <button class="btn btn-primary">Filter</button>
            </div>

            <div class="col-md-3 text-end">
                <a href="{{ route('admin.attendance.report.export', ['type'=>'excel','month'=>request('month'),'year'=>request('year')]) }}"
                   class="btn btn-success">Export Excel</a>

                <a href="{{ route('admin.attendance.report.export', ['type'=>'csv','month'=>request('month'),'year'=>request('year')]) }}"
                   class="btn btn-warning">Export CSV</a>

                <a href="{{ route('admin.attendance.report.export', ['type'=>'pdf','month'=>request('month'),'year'=>request('year')]) }}"
                   class="btn btn-danger">Export PDF</a>
            </div>

        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Teacher</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $row)
                    <tr>
                        <td>{{ $row->teacher->name ?? '-' }}</td>
                        <td>{{ $row->date }}</td>
                        <td>{{ $row->status }}</td>
                        <td>{{ $row->check_in }}</td>
                        <td>{{ $row->check_out }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

@endsection