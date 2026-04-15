<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Point Summary</title>
    <style>
        body { font-family: sans-serif; font-size:11px; color: #333; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f4f4f4; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        h2 { text-align: center; margin-bottom: 5px; text-transform: uppercase; }
        .info { text-align: center; margin-bottom: 20px; color: #666; }
        .text-end { text-align: right; }
        .text-success { color: #16a34a; }
        .text-primary { color: #2563eb; }
        .text-danger { color: #dc2626; }
        .fw-bold { font-weight: bold; }
        .bg-light { background-color: #f9fafb; }
    </style>
</head>
<body>

    <h2>Monthly Point Summary Report</h2>
    <div class="info">Periode: {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</div>

    <table>
        <thead>
            <tr>
                <th width="30" style="text-align: center">No</th>
                <th>Teacher</th>
                <th class="text-end text-success">Total Earned</th>
                <th class="text-end text-primary">Total Spent</th>
                <th class="text-end text-danger">Total Penalty</th>
                <th class="text-end bg-light">Net Change</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $row)
            @php
                $net = $row->total_earned - $row->total_spent - $row->total_penalty;
            @endphp
            <tr>
                <td style="text-align: center">{{ $i + 1 }}</td>
                <td class="fw-bold">{{ $row->teacher->name ?? '-' }}</td>
                <td class="text-end text-success">{{ number_format($row->total_earned, 0, ',', '.') }}</td>
                <td class="text-end text-primary">{{ number_format($row->total_spent, 0, ',', '.') }}</td>
                <td class="text-end text-danger">{{ number_format($row->total_penalty, 0, ',', '.') }}</td>
                <td class="text-end fw-bold bg-light {{ $net >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $net > 0 ? '+' : '' }}{{ number_format($net, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; font-style: italic; font-size: 9px; color: #999;">
        Generated on: {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>
