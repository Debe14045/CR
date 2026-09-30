<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Change Requests Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
        th { background: #eff6ff; }
        h2 { margin-bottom: 0; }
    </style>
</head>
<body>
    <h2>Laporan Change Request</h2>
    <table>
        <thead>
            <tr>
                <th>Kode CR</th>
                <th>Judul</th>
                <th>Klien</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Estimasi Waktu</th>
                <th>Biaya</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($changeRequests as $item)
                <tr>
                    <td>{{ $item->kode_cr }}</td>
                    <td>{{ $item->judul }}</td>
                    <td>{{ $item->klien }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->tanggal_pengajuan?->format('d M Y') }}</td>
                    <td>{{ $item->estimasi_waktu ?: '-' }}</td>
                    <td>{{ $item->biaya_pengerjaan !== null ? 'Rp ' . number_format($item->biaya_pengerjaan, 0, ',', '.') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
