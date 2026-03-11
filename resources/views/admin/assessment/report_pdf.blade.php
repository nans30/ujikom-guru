<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Asesmen</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 20px;">
        <h3>LAPORAN ASESMEN GURU</h3>
        @if($academicYear)
            <p>Tahun Periode: {{ $academicYear }}</p>
        @else
            <p>Semua Tahun Periode</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="30" class="text-center">No</th>
                <th>Nama Guru</th>
                <th>NIP</th>
                <th>Jabatan</th>
                <th>Status Penilaian</th>
                <th>Evaluator</th>
                <th>Periode</th>
                <th>Tanggal Penilaian</th>
                <th>Total Skor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($teachers as $index => $teacher)
                @php $assmnt = $teacher->latest_assessment; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $teacher->name }}</td>
                    <td>{{ $teacher->nip ?? '-' }}</td>
                    <td>{{ $teacher->position?->name ?? '-' }}</td>
                    <td>{{ $teacher->status_penilaian }}</td>
                    <td>{{ $assmnt?->evaluator?->name ?? '-' }}</td>
                    <td>{{ $assmnt?->period ?? '-' }}</td>
                    <td>{{ $assmnt?->assessment_date?->format('d-m-Y') ?? '-' }}</td>
                    <td class="text-center">{{ $assmnt?->total_score ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data asessmen sesuai filter</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
