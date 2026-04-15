<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Point Report</title>
    <style>
        body { font-family: sans-serif; font-size:11px; color: #333; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f4f4f4; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        h2 { text-align: center; margin-bottom: 5px; text-transform: uppercase; }
        .info { text-align: center; margin-bottom: 20px; color: #666; }
        .text-end { text-align: right; }
        .text-success { color: #16a34a; }
        .text-danger { color: #dc2626; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>

    <h2>Daily Point Transaction Report</h2>
    <div class="info">Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="60">Time</th>
                <th>Teacher</th>
                <th width="60">Type</th>
                <th class="text-end" width="70">Amount</th>
                <th class="text-end" width="80">Balance</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->created_at->format('H:i:s') }}</td>
                <td>{{ $row->teacher->name ?? '-' }}</td>
                <td>{{ $row->transaction_type }}</td>
                <td class="text-end fw-bold {{ $row->amount > 0 ? 'text-success' : 'text-danger' }}">
                    {{ $row->amount > 0 ? '+' : '' }}{{ number_format($row->amount, 0, ',', '.') }}
                </td>
                <td class="text-end fw-bold">
                    {{ number_format($row->current_balance, 0, ',', '.') }}
                </td>
                <td>{{ $row->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; font-style: italic; font-size: 9px; color: #999;">
        Generated on: {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>
