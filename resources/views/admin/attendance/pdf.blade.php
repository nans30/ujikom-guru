<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Attendance Report</title>
<style>
body { font-family: sans-serif; font-size:12px; }
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #000; padding: 6px; }
th { background: #eee; }
</style>
</head>
<body>

<h2>Attendance Report</h2>

@if($month) <p>Month: {{ \Carbon\Carbon::create()->month($month)->format('F') }}</p> @endif
@if($year)  <p>Year: {{ $year }}</p> @endif
@if($status) <p>Status: {{ ucfirst($status) }}</p> @endif

<table>
<thead>
<tr>
<th>No</th>
<th>Teacher</th>
<th>Date</th>
<th>Status</th>
<th>Check In</th>
<th>Check Out</th>
<th>Method</th>
</tr>
</thead>
<tbody>
@foreach($attendances as $i => $item)
<tr>
<td>{{ $i + 1 }}</td>
<td>{{ $item->teacher?->name ?? '-' }}</td>
<td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
<td>{{ ucfirst($item->status) }}</td>
<td>{{ $item->check_in ?? '-' }}</td>
<td>{{ $item->check_out ?? '-' }}</td>
<td>{{ strtoupper((string)($item->method_in ?? '-')) }} / {{ strtoupper((string)($item->method_out ?? '-')) }}</td>
</tr>
@endforeach
</tbody>
</table>

</body>
</html>