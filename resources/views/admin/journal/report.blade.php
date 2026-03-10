@extends('layouts.admin', ['title' => 'Laporan Jurnal Mengajar'])

@section('content')

@include('admin.partials.page-title', ['subtitle'=>'Laporan','title'=>'Laporan Jurnal Mengajar'])

<div class="card">
    <div class="card-body">

        {{-- FILTER FORM --}}
        <form method="GET" class="row mb-4">

            <!-- Month -->
            <div class="col-md-2 mb-2 mb-md-0">
                <select name="month" class="form-control">
                    <option value="">-- Semua Bulan --</option>
                    @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" {{ ($month ?? '') == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                        @endfor
                </select>
            </div>

            <!-- Year -->
            <div class="col-md-2 mb-2 mb-md-0">
                <select name="year" class="form-control">
                    <option value="">-- Semua Tahun --</option>
                    @for($y=date('Y');$y>=2020;$y--)
                    <option value="{{ $y }}" {{ ($year ?? '') == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                    @endfor
                </select>
            </div>

            <!-- Search Teacher -->
            <div class="col-md-3 mb-2 mb-md-0">
                <input type="text"
                    name="search"
                    class="form-control"
                    placeholder="Cari Nama Guru..."
                    value="{{ $search ?? '' }}">
            </div>

            <!-- Buttons -->
            <div class="col-md-5 text-md-end">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.journal.report') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        {{-- EXPORT BUTTONS --}}
        <div class="mb-3">
            <a href="{{ route('admin.journal.report.export', ['type'=>'excel','month'=>$month,'year'=>$year,'search'=>$search]) }}"
                class="btn btn-success">Export Excel</a>

            <a href="{{ route('admin.journal.report.export', ['type'=>'csv','month'=>$month,'year'=>$year,'search'=>$search]) }}"
                class="btn btn-warning">Export CSV</a>

            <a href="{{ route('admin.journal.report.export', ['type'=>'pdf','month'=>$month,'year'=>$year,'search'=>$search]) }}"
                class="btn btn-danger">Export PDF</a>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th class="text-start">Nama Guru</th>
                        <th>Total Jurnal</th>
                        <th>Terbit (Published)</th>
                        <th>Draf (Draft)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($journals as $i => $row)
                    <tr>
                        <td>{{ ($journals->currentPage() - 1) * $journals->perPage() + $i + 1 }}</td>
                        <td class="text-start">{{ $row->teacher->name ?? '-' }}</td>
                        <td><span class="badge bg-primary">{{ $row->total_count }}</span></td>
                        <td><span class="badge bg-success">{{ $row->published_count }}</span></td>
                        <td><span class="badge bg-secondary">{{ $row->draft_count }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Data laporan jurnal tidak ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($journals->count())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Menampilkan {{ $journals->firstItem() }} sampai {{ $journals->lastItem() }} dari {{ $journals->total() }} entri
            </div>
            <div>
                {{ $journals->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif

    </div>
</div>

@endsection