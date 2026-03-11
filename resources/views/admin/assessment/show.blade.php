@extends('layouts.admin', ['title' => 'Detail Assessment'])

@section('content')
@include('admin.partials.page-title', ['subtitle' => 'Assessment', 'title' => 'Detail'])
@include('admin.partials.alerts')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-light d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Informasi Penilaian</h5>
                <a href="{{ route('admin.assessment.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless sm">
                            <tr>
                                <th style="width: 150px;">Evaluator</th>
                                <td>: {{ $assessment->evaluator->name }}</td>
                            </tr>
                            <tr>
                                <th>Guru (Evaluatee)</th>
                                <td>: {{ $assessment->evaluatee->name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless sm">
                            <tr>
                                <th style="width: 150px;">Periode</th>
                                <td>: {{ $assessment->period }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>: {{ $assessment->assessment_date->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>:
                                    <span class="badge {{ $assessment->status == 2 ? 'bg-success' : 'bg-warning' }}">
                                        {{ $assessment->status == 2 ? 'Final' : 'Draft' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <h5 class="mb-3 border-bottom pb-2 text-primary">Indikator Penilaian</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Kategori / Indikator</th>
                                <th style="width: 100px;" class="text-center">Skor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assessment->details as $index => $detail)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $detail->category->name }}</strong>
                                    <p class="text-muted small mb-0">{{ $detail->category->description }}</p>
                                </td>
                                <td class="text-center fw-bold text-primary">{{ $detail->score }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="2" class="text-end">Rata-Rata Nilai</td>
                                <td class="text-center">{{ number_format($assessment->details->avg('score'), 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-4">
                    <h5 class="text-primary border-bottom pb-2">Catatan Umum</h5>
                    <div class="p-3 bg-light rounded italic">
                        {!! nl2br(e($assessment->general_notes ?? 'Tidak ada catatan.')) !!}
                    </div>
                </div>
            </div>
            <div class="card-footer text-end border-light">
                <button onclick="window.print()" class="btn btn-outline-primary">
                    <i class="fa fa-print"></i> Print Halaman
                </button>
            </div>
        </div>
    </div>
</div>
@endsection