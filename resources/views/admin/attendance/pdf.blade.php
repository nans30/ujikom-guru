<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report PDF</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Attendance Report</h2>

    @if($month) <p>Month: {{ $month }}</p> @endif
    @if($year) <p>Year: {{ $year }}</p> @endif

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Teacher</th>
                <th>Date</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Method</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item->teacher->name ?? '-' }}</td>
                <td>{{ $item->date }}</td>
                <td>{{ $item->check_in ?? '-' }}</td>
                <td>{{ $item->check_out ?? '-' }}</td>
                <td>{{ strtoupper($item->method_in ?? '-') }} / {{ strtoupper($item->method_out ?? '-') }}</td>
                <td>{{ ucfirst($item->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>