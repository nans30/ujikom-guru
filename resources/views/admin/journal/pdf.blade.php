<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Jurnal Mengajar</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #eee;
        }

        .text-left {
            text-align: left;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        p {
            margin: 2px 0;
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>Laporan Jurnal Mengajar</h2>

    @if($month)
    <p>Bulan: {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}</p>
    @endif

    @if($year)
    <p>Tahun: {{ $year }}</p>
    @endif

    @if(isset($search) && $search != '')
    <p>Pencarian: {{ $search }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th width="40">No</th>
                <th class="text-left">Nama Guru</th>
                <th>Total Jurnal</th>
                <th>Terbit</th>
                <th>Draf</th>
            </tr>
        </thead>
        <tbody>
            @foreach($journals as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="text-left">{{ $item->teacher?->name ?? '-' }}</td>
                <td>{{ $item->total_count }}</td>
                <td>{{ $item->published_count }}</td>
                <td>{{ $item->draft_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>