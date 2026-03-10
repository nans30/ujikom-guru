<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Daily Attendance Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        p {
            text-align: center;
            margin: 2px 0;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>Daily Attendance Report</h2>
    <p>Range: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</p>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Tanggal</th>
                <th>Guru</th>
                <th>Status</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Telat</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $i => $item)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $item->date->format('d/m/Y') }}</td>
                <td>{{ $item->teacher->name ?? '-' }}</td>
                <td class="text-center">{{ ucfirst($item->status) }}</td>
                <td class="text-center">{{ $item->check_in ? $item->check_in->format('H:i') : '-' }}</td>
                <td class="text-center">{{ $item->check_out ? $item->check_out->format('H:i') : '-' }}</td>
                <td class="text-center">{{ $item->late_duration ?? '-' }}</td>
                <td>{{ $item->reason ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>