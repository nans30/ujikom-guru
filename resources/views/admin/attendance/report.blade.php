@extends('layouts.admin', ['title' => 'Attendance Report'])

@section('content')

@include('admin.partials.page-title', ['subtitle'=>'Report','title'=>'Attendance Report'])

<div class="card">
    <div class="card-body">

        {{-- FILTER FORM --}}
        <form method="GET" class="row mb-4">

            <!-- Month -->
            <div class="col-md-2">
                <select name="month" class="form-control">
                    <option value="">-- Month --</option>
                    @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" {{ ($month ?? '') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Year -->
            <div class="col-md-2">
                <select name="year" class="form-control">
                    <option value="">-- Year --</option>
                    @for($y=date('Y');$y>=2020;$y--)
                        <option value="{{ $y }}" {{ ($year ?? '') == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Status -->
            <div class="col-md-2">
                <select name="status" class="form-control">
                    <option value="">-- Status --</option>
                    <option value="hadir" {{ ($status ?? '')=='hadir'?'selected':'' }}>Hadir</option>
                    <option value="telat" {{ ($status ?? '')=='telat'?'selected':'' }}>Telat</option>
                    <option value="izin" {{ ($status ?? '')=='izin'?'selected':'' }}>Izin</option>
                    <option value="sakit" {{ ($status ?? '')=='sakit'?'selected':'' }}>Sakit</option>
                    <option value="cuti" {{ ($status ?? '')=='cuti'?'selected':'' }}>Cuti</option>
                    <option value="alpha" {{ ($status ?? '')=='alpha'?'selected':'' }}>Alpha</option>
                </select>
            </div>

            <!-- Search Teacher -->
            <div class="col-md-3">
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Search Teacher..."
                       value="{{ $search ?? '' }}">
            </div>

            <!-- Method Filter -->
            <div class="col-md-1">
                <select name="method" class="form-control">
                    <option value="">Method</option>
                    <option value="rfid" {{ ($method ?? '') == 'rfid' ? 'selected' : '' }}>RFID</option>
                    <option value="manual" {{ ($method ?? '') == 'manual' ? 'selected' : '' }}>Manual</option>
                    <option value="none" {{ ($method ?? '') == 'none' ? 'selected' : '' }}>None</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-md-2 text-end">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.attendance.report') }}" class="btn btn-secondary">Reset</a>
            </div>

        </form>

        {{-- EXPORT BUTTONS --}}
        <div class="mb-3">
            <a href="{{ route('admin.attendance.report.export', ['type'=>'excel','month'=>$month,'year'=>$year,'search'=>$search,'method'=>$method,'status'=>$status]) }}"
               class="btn btn-success">Export Excel</a>

            <a href="{{ route('admin.attendance.report.export', ['type'=>'csv','month'=>$month,'year'=>$year,'search'=>$search,'method'=>$method,'status'=>$status]) }}"
               class="btn btn-warning">Export CSV</a>

            <a href="{{ route('admin.attendance.report.export', ['type'=>'pdf','month'=>$month,'year'=>$year,'search'=>$search,'method'=>$method,'status'=>$status]) }}"
               class="btn btn-danger">Export PDF</a>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="60">No</th>
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
                            <td>{{ $attendances->firstItem() + $i }}</td>
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

        {{-- PAGINATION --}}
        @if($attendances->count())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }} of {{ $attendances->total() }} entries
                </div>
                <div>
                    {{ $attendances->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif

    </div>
</div>

@endsection