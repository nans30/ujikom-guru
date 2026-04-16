@extends('layouts.admin', ['title' => 'Teacher Leaderboard'])

@section('content')

@include('admin.partials.page-title', ['subtitle'=>'Report','title'=>'Teacher Leaderboard'])

<div class="row mb-4">
    <div class="col-12">
        <div class="card overflow-hidden border-0 shadow-sm">
            <div class="card-body bg-primary-subtle p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-black text-primary mb-1 text-uppercase tracking-tighter">Ranking Guru</h4>
                        <p class="text-primary-emphasis opacity-75 mb-0 small">Berdasarkan total saldo poin yang dimiliki saat ini</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ti ti-download me-1"></i> Export Report
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.reports.points.export', ['scope' => 'leaderboard', 'type' => 'pdf']) }}">
                                        <i class="ti ti-file-type-pdf me-2 text-danger"></i> Export PDF
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.reports.points.export', ['scope' => 'leaderboard', 'type' => 'excel']) }}">
                                        <i class="ti ti-file-type-xls me-2 text-success"></i> Export Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.reports.points.export', ['scope' => 'leaderboard', 'type' => 'csv']) }}">
                                        <i class="ti ti-file-type-csv me-2 text-info"></i> Export CSV
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Top 3 Podium (Visual) --}}
@php
    $top3 = $rankings->take(3);
@endphp

@if($rankings->currentPage() == 1 && $top3->count() > 0)
<div class="row g-3 mb-4">
    {{-- Rank 2 --}}
    <div class="col-md-4 order-md-1 order-2">
        @if($top3->count() >= 2)
        <div class="card border-0 shadow-sm h-100 bg-white">
            <div class="card-body text-center p-4">
                <div class="position-relative d-inline-block mb-3">
                    <div class="avatar-xl rounded-circle border border-4 border-slate-200 overflow-hidden shadow-sm">
                        <img src="{{ $top3[1]->photo ?? 'https://ui-avatars.com/api/?name='.urlencode($top3[1]->name).'&background=94a3b8&color=fff' }}" class="w-100 h-100 object-cover">
                    </div>
                    <span class="position-absolute bottom-0 start-50 translate-middle-x badge rounded-pill bg-secondary shadow-sm" style="margin-bottom: -10px;">#2</span>
                </div>
                <h5 class="fw-black mb-1 text-truncate text-uppercase">{{ $top3[1]->name }}</h5>
                <p class="text-muted small mb-3 uppercase tracking-widest font-bold">{{ $top3[1]->position->name ?? 'Guru' }}</p>
                <div class="bg-light rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                    <span class="fw-black text-dark">{{ number_format($top3[1]->point_balance, 0, ',', '.') }}</span>
                    <span class="text-muted small text-uppercase font-black text-[9px]">Points</span>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Rank 1 --}}
    <div class="col-md-4 order-md-2 order-1">
        @if($top3->count() >= 1)
        <div class="card border-primary border-2 shadow h-100 bg-white" style="transform: scale(1.05); z-index: 10;">
            <div class="card-body text-center p-4 position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 p-3 opacity-10">
                    <i class="ti ti-crown fs-1 text-warning"></i>
                </div>
                <div class="position-relative d-inline-block mb-3">
                    <div class="avatar-xxl rounded-circle border border-4 border-warning overflow-hidden shadow">
                        <img src="{{ $top3[0]->photo ?? 'https://ui-avatars.com/api/?name='.urlencode($top3[0]->name).'&background=fbbf24&color=fff' }}" class="w-100 h-100 object-cover">
                    </div>
                    <span class="position-absolute top-0 start-100 translate-middle text-warning bg-white rounded-circle shadow-sm" style="padding: 5px; margin-left: -15px; margin-top: 15px;">
                        <i class="ti ti-crown fs-4"></i>
                    </span>
                    <span class="position-absolute bottom-0 start-50 translate-middle-x badge rounded-pill bg-warning shadow-sm" style="margin-bottom: -10px;">#1</span>
                </div>
                <h4 class="fw-black mb-1 text-truncate text-uppercase text-warning">{{ $top3[0]->name }}</h4>
                <p class="text-muted small mb-3 uppercase tracking-widest font-bold">{{ $top3[0]->position->name ?? 'Guru' }}</p>
                <div class="bg-warning-subtle rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 border border-warning-subtle">
                    <span class="fw-black text-warning-emphasis fs-5">{{ number_format($top3[0]->point_balance, 0, ',', '.') }}</span>
                    <span class="text-warning-emphasis small text-uppercase font-black text-[10px]">Points</span>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Rank 3 --}}
    <div class="col-md-4 order-md-3 order-3">
        @if($top3->count() >= 3)
        <div class="card border-0 shadow-sm h-100 bg-white">
            <div class="card-body text-center p-4">
                <div class="position-relative d-inline-block mb-3">
                    <div class="avatar-xl rounded-circle border border-4 border-amber-600 overflow-hidden shadow-sm">
                        <img src="{{ $top3[2]->photo ?? 'https://ui-avatars.com/api/?name='.urlencode($top3[2]->name).'&background=92400e&color=fff' }}" class="w-100 h-100 object-cover">
                    </div>
                    <span class="position-absolute bottom-0 start-50 translate-middle-x badge rounded-pill bg-amber-700 shadow-sm" style="margin-bottom: -10px;">#3</span>
                </div>
                <h5 class="fw-black mb-1 text-truncate text-uppercase">{{ $top3[2]->name }}</h5>
                <p class="text-muted small mb-3 uppercase tracking-widest font-bold">{{ $top3[2]->position->name ?? 'Guru' }}</p>
                <div class="bg-light rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                    <span class="fw-black text-dark">{{ number_format($top3[2]->point_balance, 0, ',', '.') }}</span>
                    <span class="text-muted small text-uppercase font-black text-[9px]">Points</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="bg-light">
                        <th width="80">Peringkat</th>
                        <th>Guru</th>
                        <th>Jabatan</th>
                        <th class="text-end">Total Poin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rankings as $index => $row)
                        @php
                            $rank = ($rankings->currentPage() - 1) * $rankings->perPage() + $index + 1;
                        @endphp
                        <tr class="{{ $rank <= 3 ? 'bg-light-subtle fw-medium' : '' }}">
                            <td>
                                @if($rank == 1)
                                    <span class="badge bg-warning text-dark px-3 py-2 fs-6">#1</span>
                                @elseif($rank == 2)
                                    <span class="badge bg-secondary px-3 py-2 fs-6">#2</span>
                                @elseif($rank == 3)
                                    <span class="badge bg-amber-700 text-white px-3 py-2 fs-6" style="background-color: #92400e !important;">#3</span>
                                @else
                                    <span class="ps-3 text-muted fw-bold">#{{ $rank }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-sm rounded-circle overflow-hidden border border-light">
                                        <img src="{{ $row->photo ?? 'https://ui-avatars.com/api/?name='.urlencode($row->name).'&background=random' }}" class="w-100 h-100 object-cover" width="40">
                                    </div>
                                    <div>
                                        <div class="fw-black text-uppercase tracking-tighter">{{ $row->name }}</div>
                                        <div class="text-muted small">NIP: {{ $row->nip ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-outline-dark border border-dark-subtle text-dark px-2 py-1 uppercase font-black text-[9px]">
                                    {{ $row->position->name ?? '-' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="fs-5 fw-black text-primary">{{ number_format($row->point_balance, 0, ',', '.') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Data tidak ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rankings->hasPages())
            <div class="mt-4">
                {{ $rankings->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
    .avatar-xl { height: 80px; width: 80px; }
    .avatar-xxl { height: 100px; width: 100px; }
    .avatar-sm { height: 40px; width: 40px; }
    .fw-black { font-weight: 900; }
    .tracking-tighter { letter-spacing: -0.05em; }
    .font-bold { font-weight: 700; }
</style>
@endpush
