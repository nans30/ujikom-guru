@extends('layouts.admin', ['title' => 'Detail Rapor Kinerja'])

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1 fw-bold">Rapor Kinerja & Sikap</h3>
        <p class="text-muted mb-0">Analisis kompetensi pendidik berbasis data evaluasi.</p>
    </div>
    <div>
        <label class="form-label text-muted small text-uppercase mb-1">Staf Pendidik</label>
        <div class="dropdown">
            <button class="btn btn-light border dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="min-width: 250px;">
                <span class="fw-semibold">{{ $teacher->name }}</span>
            </button>
            <ul class="dropdown-menu w-100 shadow-sm" style="max-height: 300px; overflow-y: auto;">
                {{-- To keep it simple, we just show the current one and a button to go back. If we wanted all teachers, we'd pass them from controller. --}}
                <li><h6 class="dropdown-header">Aksi</h6></li>
                <li><a class="dropdown-item text-primary" href="{{ route('admin.assessment.report') }}"><i class="ti ti-arrow-left me-2"></i> Kembali ke Daftar</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Peta Kekuatan Kompetensi (Radar Chart) -->
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                <h5 class="card-title fw-bold mb-0 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-3">
                        <i class="ti ti-chart-radar fs-4"></i>
                    </div>
                    Peta Kekuatan Kompetensi
                </h5>
            </div>
            <div class="card-body">
                @if(count($radarData) > 0)
                    <div id="radarCompetencyChart" style="min-height: 350px;"></div>
                    <div class="d-flex justify-content-center mt-3 gap-4">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle p-1 me-2" style="width: 12px; height: 12px;"></span>
                            <span class="small text-muted">Skor Aktual</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-secondary rounded-circle p-1 me-2" style="width: 12px; height: 12px;"></span>
                            <span class="small text-muted">Target (5.0)</span>
                        </div>
                    </div>
                @else
                    <div class="d-flex flex-column justify-content-center align-items-center h-100 text-muted">
                        <i class="ti ti-box-off fs-1 mb-2"></i>
                        <p>Belum ada data evaluasi di periode ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Riwayat Feedback -->
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                <h5 class="card-title fw-bold mb-0 d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 text-info rounded p-2 me-3">
                        <i class="ti ti-message-2 fs-4"></i>
                    </div>
                    Riwayat Feedback
                </h5>
            </div>
            <div class="card-body px-4">
                @if($feedbacks->count() > 0)
                    <div class="timeline position-relative mt-3" style="border-left: 2px solid #e9ecef; margin-left: 10px; padding-left: 20px;">
                        @foreach($feedbacks as $feedback)
                            <div class="timeline-item mb-4 position-relative">
                                <span class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: -27px; top: 5px; border: 2px solid white;"></span>
                                
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-bold">{{ $feedback->period }}</h6>
                                    <span class="badge bg-light text-dark border">{{ $feedback->assessment_date->format('Y-m-d') }}</span>
                                </div>
                                
                                <div class="card bg-light border-0">
                                    <div class="card-body p-3">
                                        <p class="fst-italic text-dark mb-3">"{{ $feedback->general_notes }}"</p>
                                        <hr class="my-2" style="border-color: rgba(0,0,0,0.1);">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-2" style="width: 24px; height: 24px; font-size: 10px;">
                                                {{ substr($feedback->evaluator->name ?? 'A', 0, 1) }}
                                            </div>
                                            <span class="small text-muted">Oleh: {{ $feedback->evaluator->name ?? 'Admin Sekolah' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="d-flex flex-column justify-content-center align-items-center h-100 text-muted">
                        <i class="ti ti-messages-off fs-1 mb-2"></i>
                        <p>Belum ada riwayat feedback.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@if(count($radarData) > 0)
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var options = {
        series: [{
            name: 'Skor Aktual',
            data: @json($radarData)
        }],
        chart: {
            height: 380,
            type: 'radar',
            toolbar: { show: false }
        },
        xaxis: {
            categories: @json($radarLabels),
            labels: {
                style: {
                    colors: ['#6c757d', '#6c757d', '#6c757d', '#6c757d', '#6c757d', '#6c757d'],
                    fontSize: '12px',
                    fontFamily: 'inherit'
                }
            }
        },
        yaxis: {
            show: true,
            min: 0,
            max: 5,
            tickAmount: 5,
            labels: {
                formatter: function (val) {
                    return Math.floor(val);
                },
                style: {
                    colors: ['#6c757d'],
                    fontSize: '10px',
                }
            }
        },
        colors: ['#0d6efd'],
        markers: {
            size: 0,
        },
        fill: {
            opacity: 0.1,
            colors: ['#0d6efd']
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['#0d6efd'],
            dashArray: 0
        },
        plotOptions: {
            radar: {
                size: 130,
                polygons: {
                    strokeColors: '#e9ecef',
                    connectorColors: '#e9ecef',
                    fill: {
                        colors: ['transparent', 'transparent']
                    }
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#radarCompetencyChart"), options);
    chart.render();
</script>
@endif
@endsection
