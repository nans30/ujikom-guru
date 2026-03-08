@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')

@include('admin.partials.page-title', ['title' => 'Dashboard'])

<style>

.dashboard-chart{
    height:350px;
}

.card-stat h3{
    font-weight:700;
    font-size:28px;
}

.card-stat p{
    font-size:13px;
}

.holiday-chart{
    height:250px;
}

.method-chart{
    height:250px;
}

</style>

<div class="content-page">
<div class="container-fluid">

<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1">

    <!-- TOTAL TEACHER -->
    <div class="col">
        <div class="card card-stat">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">Total Teacher</h5>
                <span class="badge badge-soft-success">System</span>
            </div>

            <div class="card-body text-center">

                <h3>{{ $totalTeachers }}</h3>

                <p class="text-muted">
                    Active: {{ $activeTeachers }}
                </p>

            </div>
        </div>
    </div>


    <!-- ATTENDANCE TODAY -->
    <div class="col">
        <div class="card card-stat">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">Attendance Today</h5>
                <span class="badge badge-soft-primary">Today</span>
            </div>

            <div class="card-body text-center">

                <h3>{{ $attendanceToday }}</h3>

                <p class="text-muted">
                    Present: {{ $presentToday }}
                </p>

            </div>
        </div>
    </div>


    <!-- LATE TODAY -->
    <div class="col">
        <div class="card card-stat">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">Late Today</h5>
                <span class="badge badge-soft-warning">Today</span>
            </div>

            <div class="card-body text-center">

                <h3>{{ $lateToday }}</h3>

                <p class="text-muted">
                    Alpha: {{ $alphaToday }}
                </p>

            </div>
        </div>
    </div>


    <!-- APPROVAL -->
    <div class="col">
        <div class="card card-stat">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">Approval Pending</h5>
                <span class="badge badge-soft-danger">Request</span>
            </div>

            <div class="card-body text-center">

                <h3>{{ $approvalPending }}</h3>

                <p class="text-muted">
                    Approved: {{ $approvalApproved }}
                </p>

            </div>
        </div>
    </div>

</div>



<!-- CHART ATTENDANCE -->
<div class="row mt-4">

<div class="col-lg-12">

<div class="card">

<div class="card-header">
<h5 class="card-title mb-0">Attendance Statistics (Last 7 Days)</h5>
</div>

<div class="card-body">

<div id="attendanceChart" class="dashboard-chart"></div>

</div>

</div>

</div>

</div>



<div class="row">

<!-- HOLIDAY -->
<div class="col-md-4">
<div class="card">
<div class="card-header">
<h5 class="card-title">Holiday Statistics</h5>
</div>

<div class="card-body">

<div id="holidayChart" class="holiday-chart"></div>

<div class="d-flex justify-content-between mt-3">
<span class="text-muted">Holiday This Year</span>
<strong>{{ $holidayThisYear }}</strong>
</div>

<div class="d-flex justify-content-between">
<span class="text-muted">Holiday This Month</span>
<strong>{{ $holidayThisMonth }}</strong>
</div>

</div>
</div>
</div>



<!-- RFID VS MANUAL -->
<div class="col-md-4">
<div class="card">
<div class="card-header">
<h5 class="card-title">Attendance Method</h5>
</div>

<div class="card-body">

<div id="methodChart" class="method-chart"></div>

<div class="d-flex justify-content-between mt-3">
<span class="text-muted">RFID</span>
<strong>{{ $rfidAttendance }}</strong>
</div>

<div class="d-flex justify-content-between">
<span class="text-muted">Manual</span>
<strong>{{ $manualAttendance }}</strong>
</div>

</div>
</div>
</div>



<!-- APPROVAL SUMMARY -->
<div class="col-md-4">
<div class="card">
<div class="card-header">
<h5 class="card-title">Approval Summary</h5>
</div>

<div class="card-body">

<div class="d-flex justify-content-between mb-2">
<span class="text-muted">Pending</span>
<strong>{{ $approvalPending }}</strong>
</div>

<div class="d-flex justify-content-between mb-2">
<span class="text-muted">Approved</span>
<strong>{{ $approvalApproved }}</strong>
</div>

<div class="d-flex justify-content-between">
<span class="text-muted">Rejected</span>
<strong>{{ $approvalRejected }}</strong>
</div>

</div>
</div>
</div>

</div>

</div>
</div>

@endsection



@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>

/* ATTENDANCE LINE CHART */

var options = {

series: [
{
name: 'Hadir',
data: @json($hadir)
},
{
name: 'Telat',
data: @json($telat)
},
{
name: 'Izin',
data: @json($izin)
},
{
name: 'Alpha',
data: @json($alpha)
}
],

chart: {
type: 'line',
height: 350,
toolbar: { show:false }
},

stroke:{
curve:'smooth',
width:3
},

markers:{ size:4 },

grid:{ borderColor:'#f1f1f1' },

xaxis:{
categories:@json($dates)
},

colors:[
'#22c55e',
'#f59e0b',
'#3b82f6',
'#ef4444'
],

legend:{ position:'top' }

};

var chart = new ApexCharts(document.querySelector("#attendanceChart"), options);
chart.render();



/* HOLIDAY DONUT */

var holidayOptions = {

series:[
{{ $holidayThisYear }},
{{ $holidayThisMonth }}
],

chart:{
type:'donut',
height:250
},

labels:[
'Holiday This Year',
'Holiday This Month'
],

colors:[
'#6366f1',
'#22c55e'
],

legend:{ position:'bottom' },

plotOptions:{
pie:{
donut:{ size:'65%' }
}
}

};

var holidayChart = new ApexCharts(
document.querySelector("#holidayChart"),
holidayOptions
);

holidayChart.render();



/* RFID VS MANUAL DONUT */

var methodOptions = {

series:[
{{ $rfidAttendance }},
{{ $manualAttendance }}
],

chart:{
type:'donut',
height:250
},

labels:[
'RFID',
'Manual'
],

colors:[
'#06b6d4',
'#f97316'
],

legend:{
position:'bottom'
},

plotOptions:{
pie:{
donut:{
size:'65%'
}
}
}

};

var methodChart = new ApexCharts(
document.querySelector("#methodChart"),
methodOptions
);

methodChart.render();

</script>

@endsection