@extends('layouts.admin', ['title' => 'Holiday Report'])

@section('content')

@include('admin.partials.page-title', ['subtitle' => 'Report', 'title' => 'Holiday Report'])

<div class="card">
<div class="card-body">

<form method="GET" class="row mb-4">

    <!-- Search -->
    <div class="col-md-4">
        <input type="text"
               name="search"
               class="form-control"
               placeholder="Search Holiday..."
               value="{{ $search ?? '' }}">
    </div>

    <!-- Status Filter -->
    <div class="col-md-3">
        <select name="status" class="form-control">

            <option value="">-- Select Status --</option>

            <option value="active"
                {{ ($statusKey ?? '') == 'active' ? 'selected' : '' }}>
                Active
            </option>

            <option value="inactive"
                {{ ($statusKey ?? '') == 'inactive' ? 'selected' : '' }}>
                Inactive
            </option>

        </select>
    </div>

    <!-- Button -->
    <div class="col-md-5 text-end">

        <button type="submit" class="btn btn-primary">
            Filter
        </button>

        <a href="{{ route('admin.holiday.report') }}"
           class="btn btn-secondary">
           Reset
        </a>

    </div>

</form>


<!-- EXPORT BUTTON -->
<div class="mb-3">

    <a href="{{ route('admin.holiday.report.export', [
        'type' => 'excel',
        'search' => $search,
        'status' => $statusKey
    ]) }}" class="btn btn-success">
        Export Excel
    </a>

    <a href="{{ route('admin.holiday.report.export', [
        'type' => 'csv',
        'search' => $search,
        'status' => $statusKey
    ]) }}" class="btn btn-warning">
        Export CSV
    </a>

    <a href="{{ route('admin.holiday.report.export', [
        'type' => 'pdf',
        'search' => $search,
        'status' => $statusKey
    ]) }}" class="btn btn-danger">
        Export PDF
    </a>

</div>


<div class="table-responsive">

<table class="table table-bordered table-striped">

<thead>
<tr>
<th width="60">No</th>
<th>Holiday</th>
<th>Date</th>
<th>Status</th>
</tr>
</thead>

<tbody>

@forelse($holidays as $i => $holiday)

<tr>

<td>
{{ $holidays->firstItem() + $i }}
</td>

<td>
{{ $holiday->name }}
</td>

<td>
{{ \Carbon\Carbon::parse($holiday->date)->format('d M Y') }}
</td>

<td>

@if($holiday->status == 1)

<span class="badge bg-success">
Active
</span>

@else

<span class="badge bg-danger">
Inactive
</span>

@endif

</td>

</tr>

@empty

<tr>
<td colspan="4" class="text-center">
No holidays found
</td>
</tr>

@endforelse

</tbody>

</table>

</div>


@if($holidays->count())

<div class="d-flex justify-content-between align-items-center mt-3">

<div class="text-muted">
Showing {{ $holidays->firstItem() }}
to {{ $holidays->lastItem() }}
of {{ $holidays->total() }} entries
</div>

<div>
{{ $holidays->links('pagination::bootstrap-5') }}
</div>

</div>

@endif

</div>
</div>

@endsection