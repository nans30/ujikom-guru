@extends('layouts.admin', ['title' => 'Laporan Guru'])

@section('content')

@include('admin.partials.page-title', ['subtitle' => 'Report', 'title' => 'Laporan Guru'])

<div class="card">
<div class="card-body">

<form method="GET" class="row mb-3">

<div class="col-md-3">
<select name="position_id" class="form-control">
<option value="">-- Pilih Posisi --</option>

@foreach($positions as $position)
<option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
{{ $position->name }}
</option>
@endforeach

</select>
</div>

<div class="col-md-3">
<select name="is_active" class="form-control">
<option value="">-- Status Aktif --</option>
<option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
<option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
</select>
</div>

<div class="col-md-3">
<input type="text"
name="name"
class="form-control"
placeholder="Cari nama..."
value="{{ request('name') }}">
</div>

<div class="col-md-3 text-end">

<button class="btn btn-primary">
<i class="ti ti-filter"></i> Filter
</button>

<a href="{{ route('admin.teacher.report.export', array_merge(request()->all(), ['type'=>'excel'])) }}"
class="btn btn-success">
<i class="ti ti-file-check"></i> Excel
</a>

<a href="{{ route('admin.teacher.report.export', array_merge(request()->all(), ['type'=>'csv'])) }}"
class="btn btn-warning">
<i class="ti ti-file-text"></i> CSV
</a>

<a href="{{ route('admin.teacher.report.export', array_merge(request()->all(), ['type'=>'pdf'])) }}"
class="btn btn-danger">
<i class="ti ti-file-pdf"></i> PDF
</a>

<a href="{{ route('admin.teacher.report') }}" class="btn btn-secondary">
<i class="ti ti-refresh"></i> Reset
</a>

</div>

</form>

<div class="table-responsive">

<table class="table table-bordered">

<thead>
<tr>
<th width="60">No</th>
<th>NIP</th>
<th>Nama</th>
<th>Jabatan / Posisi</th>
<th>Jenis Kelamin</th>
<th>Dibuat Oleh</th>
<th>Status</th>
<th>Dibuat Pada</th>
</tr>
</thead>

<tbody>

@forelse($teachers as $index => $teacher)

<tr>

<td>
{{ $teachers->firstItem() + $index }}
</td>

<td>{{ $teacher->nip }}</td>

<td>{{ $teacher->name }}</td>

<td>{{ $teacher->position?->name ?? '-' }}</td>

<td>
{{ $teacher->jenis_kelamin == 'L' ? 'Laki-laki' : ($teacher->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
</td>

<td>{{ $teacher->createdBy?->name ?? '-' }}</td>

<td>
{{ $teacher->is_active ? 'Aktif' : 'Tidak Aktif' }}
</td>

<td>
{{ $teacher->created_at?->format('d-m-Y H:i') }}
</td>

</tr>

@empty

<tr>
<td colspan="8" class="text-center">
Tidak ada data guru
</td>
</tr>

@endforelse

</tbody>

</table>

</div>

<div class="mt-3 d-flex justify-content-center">

{{ $teachers->onEachSide(1)->links('pagination::bootstrap-5') }}

</div>

</div>
</div>

@endsection