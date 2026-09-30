<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><title>Invoice {{ $changeRequest->kode_cr }}</title><style>body{font-family:Arial,sans-serif;color:#1f2937}h1{color:#111827}.meta{margin:24px 0}table{width:100%;border-collapse:collapse}td,th{padding:10px;border:1px solid #d1d5db;text-align:left}.total{font-size:18px;font-weight:bold;text-align:right}</style></head>
<body>
    <h1>INVOICE</h1>
    <div class="meta"><strong>Kode CR:</strong> {{ $changeRequest->kode_cr }}<br><strong>Client:</strong> {{ $changeRequest->klien }}<br><strong>Proyek:</strong> {{ $changeRequest->proyek_terkait }}<br><strong>Tanggal:</strong> {{ now()->format('d-m-Y') }}</div>
    <table><tr><th>Deskripsi</th><th>Nilai</th></tr><tr><td>{{ $changeRequest->judul }}</td><td>Rp {{ number_format($changeRequest->harga_penawaran ?? 0, 0, ',', '.') }}</td></tr></table>
    <p class="total">Total: Rp {{ number_format($changeRequest->harga_penawaran ?? 0, 0, ',', '.') }}</p>
</body>
</html>
