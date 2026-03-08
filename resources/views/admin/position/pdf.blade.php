<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Position Report PDF</title>

<style>
body { font-family: DejaVu Sans, sans-serif; }

h2 { text-align: center; }

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

<h2>Position Report</h2>

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
<th>Position</th>
<th>Status</th>
<th>Total Teachers</th>
</tr>

</thead>

<tbody>

@foreach($positions as $i => $pos)

<tr>

<td>{{ $i + 1 }}</td>

<td>{{ $pos->name }}</td>

<td>
{{ $pos->status == 1 ? 'Active' : 'Inactive' }}
</td>

<td>
{{ $pos->teachers_count }}
</td>

</tr>

@endforeach

</tbody>

</table>

</body>
</html>