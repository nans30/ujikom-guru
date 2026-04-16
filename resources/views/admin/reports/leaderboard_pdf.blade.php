<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Teacher Point Leaderboard</title>
    <style>
        body { font-family: sans-serif; font-size:11px; color: #333; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #f4f4f4; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        h2 { text-align: center; margin-bottom: 5px; text-transform: uppercase; }
        .info { text-align: center; margin-bottom: 20px; color: #666; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .rank-badge { 
            display: inline-block; 
            padding: 2px 8px; 
            border-radius: 10px; 
            background: #eee; 
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h2>Teacher Point Leaderboard</h2>
    <div class="info">Generated on: {{ now()->format('d F Y H:i') }}</div>

    <table>
        <thead>
            <tr>
                <th width="50">Rank</th>
                <th width="100">NIP</th>
                <th>Teacher Name</th>
                <th>Position</th>
                <th class="text-end" width="100">Point Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $row)
            <tr>
                <td>
                    <span class="rank-badge">#{{ $i + 1 }}</span>
                </td>
                <td>{{ $row->nip ?? '-' }}</td>
                <td class="fw-bold">{{ $row->name }}</td>
                <td>{{ $row->position->name ?? '-' }}</td>
                <td class="text-end fw-bold" style="color: #2a8cf2;">
                    {{ number_format($row->point_balance, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; font-style: italic; font-size: 9px; color: #999;">
        Document generated automatically by system.
    </div>

</body>
</html>
