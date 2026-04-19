@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
    {{-- Page Header --}}
    @include('admin.partials.page-title', ['subtitle' => 'Apps', 'title' => 'Dashboard'])
    @include('admin.partials.alerts')

    <style>
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.2s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,.05);
            padding: 1rem 1.25rem;
        }

        .card-stat h3 {
            font-weight: 700;
            font-size: 28px;
            color: #333;
        }

        /* Utility badge soft */
        .badge-soft-success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .badge-soft-primary { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .badge-soft-warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .badge-soft-danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .badge-soft-info { background: rgba(6, 182, 212, 0.1); color: #06b6d4; }
    </style>

    {{-- Row 1: 4 Kolom Statistik Utama --}}
    <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-4 mb-4">
        <div class="col">
            <div class="card card-stat h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Teacher</h5>
                    <span class="badge badge-soft-success">System</span>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h3>{{ $totalTeachers }}</h3>
                    <p class="text-muted mb-0 small">Active: <span class="fw-bold">{{ $activeTeachers }}</span></p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-stat h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Attendance Today</h5>
                    <span class="badge badge-soft-primary">Today</span>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h3>{{ $attendanceToday }}</h3>
                    <p class="text-muted mb-0 small">Present: <span class="text-success fw-bold">{{ $presentToday }}</span></p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-stat h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Late Today</h5>
                    <span class="badge badge-soft-warning">Today</span>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h3>{{ $lateToday }}</h3>
                    <p class="text-muted mb-0 small">Alpha: <span class="text-danger fw-bold">{{ $alphaToday }}</span></p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-stat h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Assessment Progress</h5>
                    <span class="badge badge-soft-info">Semester</span>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h3>{{ $assessmentProgress }}%</h3>
                    <div class="progress mt-1" style="height: 6px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $assessmentProgress }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 2: Charts Penilaian (Radar & Bar) --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Teacher Competency Analysis</h5>
                </div>
                <div class="card-body">
                    <div id="radarAssessmentChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top 10 Teacher Performance</h5>
                </div>
                <div class="card-body">
                    <div id="barAssessmentChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3: Trend Statistics (Attendance & Assessment Trend) --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Attendance Trend (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    <div id="attendanceChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Average Score Trend (Last 6 Months)</h5>
                </div>
                <div class="card-body">
                    <div id="assessmentTrendChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 4: Bottom Donuts & Summary --}}
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Holiday Statistics</h5>
                </div>
                <div class="card-body">
                    <div id="holidayChart" style="height: 250px;"></div>
                    <div class="d-flex justify-content-between mt-3 border-top pt-2">
                        <span class="text-muted small">Total This Year</span>
                        <span class="fw-bold">{{ $holidayThisYear }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Attendance Method</h5>
                </div>
                <div class="card-body">
                    <div id="methodChart" style="height: 250px;"></div>
                    <div class="d-flex justify-content-between mt-3 border-top pt-2">
                        <span class="text-muted small text-primary">Face ID Check-ins</span>
                        <span class="fw-bold">{{ $faceIdAttendance }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Approval Summary</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">Pending</span>
                        <span class="badge bg-warning rounded-pill px-3">{{ $approvalPending }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">Approved</span>
                        <span class="badge bg-success rounded-pill px-3">{{ $approvalApproved }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Rejected</span>
                        <span class="badge bg-danger rounded-pill px-3">{{ $approvalRejected }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    /* 1. ATTENDANCE LINE CHART */
    new ApexCharts(document.querySelector("#attendanceChart"), {
        series: [
            { name: 'Hadir', data: @json($hadir ?? []) },
            { name: 'Telat', data: @json($telat ?? []) },
            { name: 'Izin',  data: @json($izin ?? []) },
            { name: 'Alpha', data: @json($alpha ?? []) }
        ],
        chart: { type: 'line', height: 350, toolbar: { show: false } },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: { categories: @json($dates ?? []) },
        colors: ['#22c55e', '#f59e0b', '#3b82f6', '#ef4444'],
        legend: { position: 'top' },
        markers: { size: 4 }
    }).render();

    /* 2. RADAR ASSESSMENT CHART */
    new ApexCharts(document.querySelector("#radarAssessmentChart"), {
        series: [{ name: 'Avg Score', data: @json($radarData ?? []) }],
        chart: { height: 350, type: 'radar', toolbar: { show: false } },
        xaxis: { categories: @json($radarLabels ?? []) },
        colors: ['#6366f1'],
        fill: { opacity: 0.4 },
        yaxis: { show: false, min: 0 },
        stroke: { width: 2 }
    }).render();

    /* 3. BAR PERFORMANCE CHART */
    new ApexCharts(document.querySelector("#barAssessmentChart"), {
        series: [{ name: 'Total Score', data: @json($barTotalScores ?? []) }],
        chart: { type: 'bar', height: 350, toolbar: { show: false } },
        plotOptions: { 
            bar: { borderRadius: 5, horizontal: true, distributed: true, barHeight: '70%' } 
        },
        xaxis: { categories: @json($barTeacherNames ?? []) },
        dataLabels: { enabled: true, textAnchor: 'start', style: { colors: ['#fff'] }, offsetX: 0 },
        colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
        legend: { show: false }
    }).render();

    /* 4. ASSESSMENT TREND AREA CHART (NEW) */
    new ApexCharts(document.querySelector("#assessmentTrendChart"), {
        series: [{ name: 'Avg Score', data: @json($assessmentTrendData ?? []) }],
        chart: { type: 'area', height: 350, toolbar: { show: false } },
        colors: ['#6366f1'],
        fill: { type: 'gradient', gradient: { opacityFrom: 0.6, opacityTo: 0.1 } },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: { categories: @json($assessmentTrendLabels ?? []) },
        yaxis: { min: 0, max: 100 },
        tooltip: { y: { formatter: function (val) { return val + " pts" } } }
    }).render();

    /* 5. HOLIDAY DONUT */
    new ApexCharts(document.querySelector("#holidayChart"), {
        series: [{{ $holidayThisYear ?? 0 }}, {{ $holidayThisMonth ?? 0 }}],
        chart: { type: 'donut', height: 250 },
        labels: ['Yearly', 'Monthly'],
        colors: ['#6366f1', '#22c55e'],
        legend: { position: 'bottom' },
        plotOptions: { pie: { donut: { size: '65%' } } }
    }).render();

    /* 6. METHOD DONUT */
    new ApexCharts(document.querySelector("#methodChart"), {
        series: [{{ $rfidAttendance ?? 0 }}, {{ $manualAttendance ?? 0 }}, {{ $faceIdAttendance ?? 0 }}],
        chart: { type: 'donut', height: 250 },
        labels: ['RFID', 'Manual', 'Face ID'],
        colors: ['#06b6d4', '#f97316', '#6366f1'],
        legend: { position: 'bottom' },
        plotOptions: { pie: { donut: { size: '65%' } } }
    }).render();
</script>
@endsection