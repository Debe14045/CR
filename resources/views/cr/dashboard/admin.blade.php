@extends('cr.index')

@section('dashboard-intro')
    {{-- Role Header Banner --}}
    <div class="role-banner">
        <div class="role-banner__left">
            <div class="icon-wrap">
                <i class="bi bi-gear-wide-connected"></i>
            </div>
            <div>
                <div class="eyebrow">Administrator Control Panel</div>
                <h4 class="fw-bold mb-1">Pusat Kendali Sistem &amp; Master Data Client</h4>
                <p class="text-muted small mb-0">Kelola master data perusahaan client, pantau integritas workflow seluruh pengguna, dan akses rekapitulasi audit eksekutif lengkap.</p>
            </div>
        </div>

        <div class="d-none d-md-flex gap-2">
            <a href="{{ route('master.clients.index') }}" class="btn btn-outline-brand btn-sm">
                <i class="bi bi-buildings-fill"></i> Kelola Master Client
            </a>
            <a href="{{ route('change-requests.export.excel') }}" class="btn btn-outline-success btn-sm btn-export">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i> Export Rekap Excel
            </a>
            <a href="{{ route('change-requests.export.pdf') }}" class="btn btn-outline-danger btn-sm btn-export">
                <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF Laporan
            </a>
        </div>
    </div>

    {{-- Admin KPI Metric Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-6">
            <a href="{{ route('change-requests.index') }}" class="text-decoration-none">
                <div class="kpi-card card-lift">
                    <div>
                        <div class="kpi-icon" style="background: #EEF2FF; color: #4F46E5;">
                            <i class="bi bi-database"></i>
                        </div>
                        <div class="kpi-value">{{ $roleMetrics['total_crs'] ?? 0 }}</div>
                        <div class="kpi-label">Total Record CR</div>
                    </div>
                    <div class="kpi-footer">
                        <span>Semua status</span>
                        <i class="bi bi-hdd-stack"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-6">
            <a href="{{ route('master.clients.index') }}" class="text-decoration-none">
                <div class="kpi-card card-lift">
                    <div>
                        <div class="kpi-icon" style="background: #EFF6FF; color: #2563EB;">
                            <i class="bi bi-buildings"></i>
                        </div>
                        <div class="kpi-value">{{ $roleMetrics['total_clients'] ?? 0 }}</div>
                        <div class="kpi-label">Master Klien Terdaftar</div>
                    </div>
                    <div class="kpi-footer">
                        <span class="text-primary fw-semibold">
                            Lihat Data Klien &rarr;
                        </span>
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-6">
            <div class="kpi-card card-lift">
                <div>
                    <div class="kpi-icon" style="background: #ECFDF5; color: #059669;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="kpi-value">{{ $roleMetrics['total_users'] ?? 0 }}</div>
                    <div class="kpi-label">Total Akun Pengguna</div>
                </div>
                <div class="kpi-footer">
                    <span>5 Role terdaftar</span>
                    <i class="bi bi-person-check"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="kpi-card card-lift">
                <div>
                    <div class="kpi-icon" style="background: #FFFBEB; color: #D97706;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="kpi-value text-success" style="font-size: 1.45rem;">
                        Rp {{ number_format(($roleMetrics['total_revenue'] ?? 0) / 1000000, 1) }}M
                    </div>
                    <div class="kpi-label">Total Volume Nilai CR</div>
                </div>
                <div class="kpi-footer">
                    <span>Akumulasi nilai proyek</span>
                    <i class="bi bi-graph-up"></i>
                </div>
            </div>
        </div>
    </div>
@endsection
