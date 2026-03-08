@extends('layouts.admin', ['title' => 'Position Report'])

@section('content')

@include('admin.partials.page-title', ['subtitle'=>'Report','title'=>'Position Report'])

<div class="card">
<div class="card-body">

<form method="GET" class="row mb-4">

    <!-- Search -->
    <div class="col-md-4">
        <input type="text"
               name="search"
               class="form-control"
               placeholder="Search Position..."
               value="{{ $search ?? '' }}">
    </div>

    <!-- Status -->
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

        <a href="{{ route('admin.position.report') }}"
           class="btn btn-secondary">
           Reset
        </a>

    </div>

</form>

<!-- EXPORT BUTTON -->
<div class="mb-3">

    <a href="{{ route('admin.position.report.export', [
        'type'=>'excel',
        'search'=>$search,
        'status'=>$statusKey
    ]) }}" class="btn btn-success">
        Export Excel
    </a>

    <a href="{{ route('admin.position.report.export', [
        'type'=>'csv',
        'search'=>$search,
        'status'=>$statusKey
    ]) }}" class="btn btn-warning">
        Export CSV
    </a>

    <a href="{{ route('admin.position.report.export', [
        'type'=>'pdf',
        'search'=>$search,
        'status'=>$statusKey
    ]) }}" class="btn btn-danger">
        Export PDF
    </a>

</div>


<div class="table-responsive">

<table class="table table-bordered table-striped">

<thead>
<tr>
<th width="60">No</th>
<th>Position</th>
<th>Status</th>
<th>Total Teachers</th>
</tr>
</thead>

<tbody>

@forelse($positions as $i => $pos)

<tr>

<td>
{{ $positions->firstItem() + $i }}
</td>

<td>
{{ $pos->name }}
</td>

<td>

@if($pos->status == 1)
<span class="badge bg-success">
Active
</span>
@else
<span class="badge bg-danger">
Inactive
</span>
@endif

</td>

<td>
{{ $pos->teachers_count }}
</td>

</tr>

@empty

<tr>
<td colspan="4" class="text-center">
No positions found
</td>
</tr>

@endforelse

</tbody>

</table>

</div>


@if($positions->count())

<div class="d-flex justify-content-between align-items-center mt-3">

<div class="text-muted">
Showing {{ $positions->firstItem() }}
to {{ $positions->lastItem() }}
of {{ $positions->total() }} entries
</div>

<div>
{{ $positions->links('pagination::bootstrap-5') }}
</div>

</div>

@endif


</div>
</div>

@endsection