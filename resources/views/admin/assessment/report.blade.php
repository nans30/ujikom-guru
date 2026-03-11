@extends('layouts.admin', ['title' => 'Laporan Asesmen'])

@section('content')

@include('admin.partials.page-title', ['subtitle'=>'Report','title'=>'Laporan Asesmen'])

<div class="card">
    <div class="card-body">

        {{-- FILTER FORM --}}
        <form method="GET" class="row mb-4">
            
            <!-- Tahun Periode -->
            <div class="col-md-3">
                <select name="academic_year" class="form-control">
                    <option value="">-- Semua Tahun Periode --</option>
                    @foreach($academicYears as $year)
                        @if($year)
                            <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Status Penilaian -->
            <div class="col-md-3">
                <select name="status_dinilai" class="form-control">
                    <option value="">-- Semua Status --</option>
                    <option value="sudah" {{ request('status_dinilai') == 'sudah' ? 'selected' : '' }}>Sudah Dinilai</option>
                    <option value="belum" {{ request('status_dinilai') == 'belum' ? 'selected' : '' }}>Belum Dinilai</option>
                </select>
            </div>

            <!-- Nama Guru -->
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari Nama Guru..." value="{{ request('search') }}">
            </div>

            <!-- Buttons -->
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.assessment.report') }}" class="btn btn-secondary">Reset</a>
            </div>

        </form>

        {{-- EXPORT BUTTONS --}}
        <div class="mb-3">
            <a href="{{ route('admin.assessment.report.export', array_merge(request()->all(), ['type'=>'excel'])) }}" class="btn btn-success">Export Excel</a>
            <a href="{{ route('admin.assessment.report.export', array_merge(request()->all(), ['type'=>'csv'])) }}" class="btn btn-warning">Export CSV</a>
            <a href="{{ route('admin.assessment.report.export', array_merge(request()->all(), ['type'=>'pdf'])) }}" class="btn btn-danger">Export PDF</a>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Nama Guru</th>
                        <th>NIP</th>
                        <th>Jabatan</th>
                        <th>Status Penilaian</th>
                        <th>Evaluator</th>
                        <th>Periode</th>
                        <th>Tanggal Penilaian</th>
                        <th>Total Skor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paginatedTeachers as $i => $teacher)
                        @php $assmnt = $teacher->latest_assessment; @endphp
                        <tr>
                            <td>{{ $paginatedTeachers->firstItem() + $i }}</td>
                            <td>
                                <a href="{{ route('admin.assessment.report.show', ['id' => $teacher->id, 'academic_year' => $academicYear]) }}" class="text-primary fw-bold text-decoration-none">
                                    {{ $teacher->name }}
                                </a>
                            </td>
                            <td>{{ $teacher->nip ?? '-' }}</td>
                            <td>{{ $teacher->position?->name ?? '-' }}</td>
                            <td>
                                @if($teacher->status_penilaian == 'Sudah Dinilai')
                                    <span class="badge bg-success">Sudah</span>
                                @else
                                    <span class="badge bg-danger">Belum</span>
                                @endif
                            </td>
                            <td>{{ $assmnt?->evaluator?->name ?? '-' }}</td>
                            <td>{{ $assmnt?->period ?? '-' }}</td>
                            <td>{{ $assmnt?->assessment_date?->format('d-m-Y') ?? '-' }}</td>
                            <td>{{ $assmnt?->total_score ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data asessmen sesuai filter</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($paginatedTeachers->count())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $paginatedTeachers->firstItem() }} to {{ $paginatedTeachers->lastItem() }} of {{ $paginatedTeachers->total() }} entries
                </div>
                <div>
                    {{ $paginatedTeachers->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif

    </div>
</div>

@endsection
