@extends('layouts.admin', ['title' => 'Daily Attendance Report'])

@section('content')

@include('admin.partials.page-title', ['subtitle'=>'Report','title'=>'Daily Attendance Report'])

<div class="card">
    <div class="card-body">

        {{-- FILTER FORM --}}
        <form method="GET" class="row mb-4 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Search Teacher</label>
                <input type="text" name="search" class="form-control" placeholder="Search Teacher..." value="{{ $search }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.attendance.daily.report') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        {{-- EXPORT BUTTONS --}}
        <div class="mb-3">
            <a href="{{ route('admin.attendance.daily.report.export', ['type'=>'excel','start_date'=>$start_date,'end_date'=>$end_date,'search'=>$search]) }}"
                class="btn btn-success">Export Excel</a>

            <a href="{{ route('admin.attendance.daily.report.export', ['type'=>'csv','start_date'=>$start_date,'end_date'=>$end_date,'search'=>$search]) }}"
                class="btn btn-warning">Export CSV</a>

            <a href="{{ route('admin.attendance.daily.report.export', ['type'=>'pdf','start_date'=>$start_date,'end_date'=>$end_date,'search'=>$search]) }}"
                class="btn btn-danger">Export PDF</a>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Tanggal</th>
                        <th>Guru</th>
                        <th>Status</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Telat</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $i => $row)
                    <tr>
                        <td>{{ ($attendances->currentPage() - 1) * $attendances->perPage() + $i + 1 }}</td>
                        <td>{{ $row->date->format('d M Y') }}</td>
                        <td>{{ $row->teacher->name ?? '-' }}</td>
                        <td>
                            @php
                            $badge = match($row->status) {
                            'hadir' => 'bg-success',
                            'telat' => 'bg-warning',
                            'izin', 'sakit', 'cuti' => 'bg-info',
                            'alpha' => 'bg-danger',
                            default => 'bg-secondary'
                            };
                            @endphp
                            <span class="badge {{ $badge }}">{{ ucfirst($row->status) }}</span>
                        </td>
                        <td>{{ $row->check_in ? $row->check_in->format('H:i') : '-' }}</td>
                        <td>{{ $row->check_out ? $row->check_out->format('H:i') : '-' }}</td>
                        <td>{{ $row->late_duration ?? '-' }}</td>
                        <td>{{ $row->reason ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No records found for this period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($attendances->count())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
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