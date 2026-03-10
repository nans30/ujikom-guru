@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
    {{-- Page Header --}}
    @include('admin.partials.page-title', ['subtitle' => 'Apps', 'title' => 'Dashboard'])
    @include('admin.partials.alerts')

    <style>
        /* Mengikuti gaya card pada halaman Journal */
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            /* margin-bottom dihapus karena sudah dihandle oleh class 'g-4' di row */
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
    </style>

    {{-- 
        Menambahkan class 'g-4' untuk memberi jarak (gap) antar kolom.
        Menambahkan 'mb-4' untuk memberi jarak ke section di bawahnya.
    --}}
    <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-4 mb-4">
        <div class="col">
            <div class="card card-stat h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Teacher</h5>
                    <span class="badge badge-soft-success">System</span>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h3>{{ $totalTeachers }}</h3>
                    <p class="text-muted mb-0">Active: {{ $activeTeachers }}</p>
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
                    <p class="text-muted mb-0">Present: {{ $presentToday }}</p>
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
                    <p class="text-muted mb-0">Alpha: {{ $alphaToday }}</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-stat h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Approval Pending</h5>
                    <span class="badge badge-soft-danger">Request</span>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h3>{{ $approvalPending }}</h3>
                    <p class="text-muted mb-0">Approved: {{ $approvalApproved }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ATTENDANCE CHART (Row 2) --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h5 class="card-title mb-0">Attendance Statistics (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    <div id="attendanceChart" style="height: 350px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- BOTTOM CHARTS (Row 3) --}}
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Holiday Statistics</h5>
                </div>
                <div class="card-body">
                    <div id="holidayChart" style="height: 250px; width: 100%;"></div>
                    <div class="d-flex justify-content-between mt-3 border-top pt-2">
                        <span class="text-muted small">Holiday This Year</span>
                        <span class="fw-bold">{{ $holidayThisYear }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Holiday This Month</span>
                        <span class="fw-bold">{{ $holidayThisMonth }}</span>
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
                    <div id="methodChart" style="height: 250px; width: 100%;"></div>
                    <div class="d-flex justify-content-between mt-3 border-top pt-2">
                        <span class="text-muted small text-info">RFID</span>
                        <span class="fw-bold">{{ $rfidAttendance }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small text-orange" style="color: #f97316;">Manual</span>
                        <span class="fw-bold">{{ $manualAttendance }}</span>
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
                        <span class="badge bg-warning rounded-pill">{{ $approvalPending }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">Approved</span>
                        <span class="badge bg-success rounded-pill">{{ $approvalApproved }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Rejected</span>
                        <span class="badge bg-danger rounded-pill">{{ $approvalRejected }}</span>
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
    var options = {
        series: [
            { name: 'Hadir', data: @json($hadir) },
            { name: 'Telat', data: @json($telat) },
            { name: 'Izin',  data: @json($izin) },
            { name: 'Alpha', data: @json($alpha) }
        ],
        chart: { type: 'line', height: 350, toolbar: { show: false } },
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 4 },
        grid: { borderColor: '#f1f1f1' },
        xaxis: { categories: @json($dates) },
        colors: ['#22c55e', '#f59e0b', '#3b82f6', '#ef4444'],
        legend: { position: 'top' }
    };
    new ApexCharts(document.querySelector("#attendanceChart"), options).render();

    /* 2. HOLIDAY DONUT */
    var holidayOptions = {
        series: [{{ $holidayThisYear }}, {{ $holidayThisMonth }}],
        chart: { type: 'donut', height: 250 },
        labels: ['Holiday This Year', 'Holiday This Month'],
        colors: ['#6366f1', '#22c55e'],
        legend: { position: 'bottom' },
        plotOptions: { pie: { donut: { size: '65%' } } }
    };
    new ApexCharts(document.querySelector("#holidayChart"), holidayOptions).render();

    /* 3. RFID VS MANUAL DONUT */
    var methodOptions = {
        series: [{{ $rfidAttendance }}, {{ $manualAttendance }}],
        chart: { type: 'donut', height: 250 },
        labels: ['RFID', 'Manual'],
        colors: ['#06b6d4', '#f97316'],
        legend: { position: 'bottom' },
        plotOptions: { pie: { donut: { size: '65%' } } }
    };
    new ApexCharts(document.querySelector("#methodChart"), methodOptions).render();
</script>
@endsection