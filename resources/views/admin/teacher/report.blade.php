@extends('layouts.admin', ['title' => 'Laporan Guru'])

@section('content')

@include('admin.partials.page-title', ['subtitle'=>'Report','title'=>'Laporan Guru'])

<div class="card">
    <div class="card-body">

        {{-- FILTER FORM --}}
        <form method="GET" class="row mb-4">

            <!-- Posisi -->
            <div class="col-md-2">
                <select name="position_id" class="form-control">
                    <option value="">-- Pilih Posisi --</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                            {{ $position->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Aktif -->
            <div class="col-md-2">
                <select name="is_active" class="form-control">
                    <option value="">-- Status Aktif --</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>

            <!-- Nama Guru -->
            <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="Cari Guru..." value="{{ request('name') }}">
            </div>

            <!-- Metode (opsional, kalau perlu) -->
         

            <!-- Buttons -->
            <div class="col-md-2 text-end">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.teacher.report') }}" class="btn btn-secondary">Reset</a>
            </div>

        </form>

        {{-- EXPORT BUTTONS --}}
        <div class="mb-3">
            <a href="{{ route('admin.teacher.report.export', array_merge(request()->all(), ['type'=>'excel'])) }}" class="btn btn-success">Export Excel</a>
            <a href="{{ route('admin.teacher.report.export', array_merge(request()->all(), ['type'=>'csv'])) }}" class="btn btn-warning">Export CSV</a>
            <a href="{{ route('admin.teacher.report.export', array_merge(request()->all(), ['type'=>'pdf'])) }}" class="btn btn-danger">Export PDF</a>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
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
                    @forelse($teachers as $i => $teacher)
                        <tr>
                            <td>{{ $teachers->firstItem() + $i }}</td>
                            <td>{{ $teacher->nip }}</td>
                            <td>{{ $teacher->name }}</td>
                            <td>{{ $teacher->position?->name ?? '-' }}</td>
                            <td>{{ $teacher->jenis_kelamin == 'L' ? 'Laki-laki' : ($teacher->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                            <td>{{ $teacher->createdBy?->name ?? '-' }}</td>
                            <td>{{ $teacher->is_active ? 'Aktif' : 'Tidak Aktif' }}</td>
                            <td>{{ $teacher->created_at?->format('d-m-Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data guru</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($teachers->count())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $teachers->firstItem() }} to {{ $teachers->lastItem() }} of {{ $teachers->total() }} entries
                </div>
                <div>
                    {{ $teachers->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif

    </div>
</div>

@endsection