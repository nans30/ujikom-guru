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
                    <option value="">-- All Months --</option>
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
                    <option value="">-- Select Year --</option>
                    @for($y=date('Y');$y>=2020;$y--)
                        <option value="{{ $y }}" {{ ($year ?? '') == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
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

            <!-- Buttons -->
            <div class="col-md-5 text-end">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.attendance.report') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        {{-- EXPORT BUTTONS --}}
        <div class="mb-3">
            <a href="{{ route('admin.attendance.report.export', ['type'=>'excel','month'=>$month,'year'=>$year,'search'=>$search]) }}"
               class="btn btn-success">Export Excel</a>

            <a href="{{ route('admin.attendance.report.export', ['type'=>'csv','month'=>$month,'year'=>$year,'search'=>$search]) }}"
               class="btn btn-warning">Export CSV</a>

            <a href="{{ route('admin.attendance.report.export', ['type'=>'pdf','month'=>$month,'year'=>$year,'search'=>$search]) }}"
               class="btn btn-danger">Export PDF</a>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Teacher</th>
                        <th>Hadir</th>
                        <th>Telat</th>
                        <th>Izin</th>
                        <th>Sakit</th>
                        <th>Cuti</th>
                        <th>Alpha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $i => $row)
                        <tr>
                            <td>{{ ($attendances->currentPage() - 1) * $attendances->perPage() + $i + 1 }}</td>
                            <td>{{ $row->teacher->name ?? '-' }}</td>
                            <td>{{ $row->hadir_count }}</td>
                            <td>{{ $row->telat_count }}</td>
                            <td>{{ $row->izin_count }}</td>
                            <td>{{ $row->sakit_count }}</td>
                            <td>{{ $row->cuti_count }}</td>
                            <td>{{ $row->alpha_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No attendance found</td>
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