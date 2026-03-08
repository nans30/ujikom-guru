<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Holiday Report</title>

<style>

body {
font-family: DejaVu Sans, sans-serif;
}

h2 {
text-align: center;
}

table {
border-collapse: collapse;
width: 100%;
margin-top: 20px;
}

th, td {
border: 1px solid #000;
padding: 6px;
text-align: left;
}

th {
background: #eee;
}

</style>

</head>

<body>

<h2>Holiday Report</h2>

@if($search)
<p><strong>Search:</strong> {{ $search }}</p>
@endif

@if($statusKey)
<p><strong>Status:</strong> {{ ucfirst($statusKey) }}</p>
@endif

<table>

<thead>
<tr>
<th>No</th>
<th>Holiday</th>
<th>Date</th>
<th>Status</th>
</tr>
</thead>

<tbody>

@foreach($holidays as $i => $holiday)

<tr>

<td>{{ $i + 1 }}</td>

<td>{{ $holiday->name }}</td>

<td>{{ \Carbon\Carbon::parse($holiday->date)->format('d M Y') }}</td>

<td>
{{ $holiday->status == 1 ? 'Active' : 'Inactive' }}
</td>

</tr>

@endforeach

</tbody>

</table>

</body>
</html>