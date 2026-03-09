<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Attendance Report</title>
<style>
body { font-family: sans-serif; font-size:12px; }
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #000; padding: 6px; text-align: center; }
th { background: #eee; }
h2 { text-align: center; }
p { margin: 2px 0; text-align: center; }
</style>
</head>
<body>

<h2>Attendance Report</h2>

@if($month)
    <p>Month: {{ \Carbon\Carbon::create()->month($month)->format('F') }}</p>
@endif

@if($year)
    <p>Year: {{ $year }}</p>
@endif

@if(isset($status) && $status != '')
    <p>Status: {{ ucfirst($status) }}</p>
@endif

<table>
<thead>
<tr>
<th>No</th>
<th>Teacher</th>
<th>Hadir</th>
<th>Telat</th>
<th>Izin</th>
<th>Sakit</th>
<th>Cuti</th>
<th>Alpha</th>
</tr>
</thead>
<tbody>
@foreach($attendances as $i => $item)
<tr>
<td>{{ $i + 1 }}</td>
<td>{{ $item->teacher?->name ?? '-' }}</td>
<td>{{ $item->hadir_count }}</td>
<td>{{ $item->telat_count }}</td>
<td>{{ $item->izin_count }}</td>
<td>{{ $item->sakit_count }}</td>
<td>{{ $item->cuti_count }}</td>
<td>{{ $item->alpha_count }}</td>
</tr>
@endforeach
</tbody>
</table>

</body>
</html>