<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Guru</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h4>Laporan Guru</h4>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Jabatan / Posisi</th>
                <th>Jenis Kelamin</th>
                <th>Dibuat Oleh</th>
                <th>Status</th>
                <th>Dibuat Pada</th>
            </tr>
        </thead>
        <tbody>
            @foreach($teachers as $index => $teacher)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $teacher->nip }}</td>
                <td>{{ $teacher->name }}</td>
                <td>{{ $teacher->position?->name ?? '-' }}</td>
                <td>{{ $teacher->jenis_kelamin == 'L' ? 'Laki-laki' : ($teacher->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                <td>{{ $teacher->createdBy?->name ?? '-' }}</td>
                <td>{{ $teacher->is_active ? 'Aktif' : 'Tidak Aktif' }}</td>
                <td>{{ $teacher->created_at?->format('d-m-Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>