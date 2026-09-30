@extends('layouts.app')

@section('title', 'CR Saya • CR Manager')

@section('content')

@php
    $statusColors = [
        'diajukan'                   => ['bg' => '#FEF3C7', 'color' => '#D97706', 'label' => 'Diajukan'],
        'awaiting_pm'                => ['bg' => '#FEF3C7', 'color' => '#D97706', 'label' => 'Menunggu PM'],
        'awaiting_pmh'               => ['bg' => '#EDE9FE', 'color' => '#7C3AED', 'label' => 'Menunggu PMH'],
        'dianalisis'                 => ['bg' => '#EDE9FE', 'color' => '#7C3AED', 'label' => 'Dianalisis'],
        'revision_needed'            => ['bg' => '#FEE2E2', 'color' => '#DC2626', 'label' => 'Perlu Revisi'],
        'validated'                  => ['bg' => '#DBEAFE', 'color' => '#2563EB', 'label' => 'Divalidasi'],
        'disetujui'                  => ['bg' => '#DBEAFE', 'color' => '#2563EB', 'label' => 'Disetujui'],
        'analisa'                    => ['bg' => '#DBEAFE', 'color' => '#2563EB', 'label' => 'Analisa'],
        'development'                => ['bg' => '#DBEAFE', 'color' => '#2563EB', 'label' => 'Development'],
        'dikerjakan'                 => ['bg' => '#DBEAFE', 'color' => '#2563EB', 'label' => 'Dikerjakan'],
        'sit'                        => ['bg' => '#FEF3C7', 'color' => '#D97706', 'label' => 'SIT'],
        'uat'                        => ['bg' => '#FEF3C7', 'color' => '#D97706', 'label' => 'UAT'],
        'training'                   => ['bg' => '#D1FAE5', 'color' => '#059669', 'label' => 'Training'],
        'awaiting_golive_validation' => ['bg' => '#D1FAE5', 'color' => '#059669', 'label' => 'Go-Live Val.'],
        'golive'                     => ['bg' => '#D1FAE5', 'color' => '#059669', 'label' => 'Go-Live'],
        'selesai'                    => ['bg' => '#D1FAE5', 'color' => '#059669', 'label' => 'Selesai'],
        'invoicing'                  => ['bg' => '#D1FAE5', 'color' => '#059669', 'label' => 'Invoicing'],
        'ditolak'                    => ['bg' => '#FEE2E2', 'color' => '#DC2626', 'label' => 'Ditolak'],
    ];

    $statusProgress = [
        'diajukan'                   => 5,
        'awaiting_pm'                => 10,
        'awaiting_pmh'               => 20,
        'dianalisis'                 => 20,
        'revision_needed'            => 15,
        'validated'                  => 30,
        'disetujui'                  => 30,
        'analisa'                    => 40,
        'development'                => 55,
        'dikerjakan'                 => 55,
        'sit'                        => 65,
        'uat'                        => 75,
        'training'                   => 85,
        'awaiting_golive_validation' => 90,
        'golive'                     => 100,
        'selesai'                    => 100,
        'invoicing'                  => 100,
        'ditolak'                    => 0,
    ];

    $progressBarColors = [
        'diajukan'                   => '#F59E0B',
        'awaiting_pm'                => '#F59E0B',
        'awaiting_pmh'               => '#A78BFA',
        'dianalisis'                 => '#A78BFA',
        'revision_needed'            => '#EF4444',
        'validated'                  => '#3B82F6',
        'disetujui'                  => '#3B82F6',
        'analisa'                    => '#3B82F6',
        'development'                => '#3B82F6',
        'dikerjakan'                 => '#3B82F6',
        'sit'                        => '#F97316',
        'uat'                        => '#F97316',
        'training'                   => '#10B981',
        'awaiting_golive_validation' => '#10B981',
        'golive'                     => '#10B981',
        'selesai'                    => '#10B981',
        'invoicing'                  => '#10B981',
        'ditolak'                    => '#EF4444',
    ];

    $avatarColors = ['#3B82F6','#F97316','#8B5CF6','#10B981','#EF4444','#06B6D4','#D97706','#EC4899'];
@endphp

<style>
    /* ── Client Dashboard (Figma MacBook Pro 14" – 1) ── */
    .client-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .client-page-title {
        font-size: 1.45rem;
        font-weight: 800;
        color: #0F172A;
        letter-spacing: -0.03em;
        margin: 0;
        line-height: 1.2;
    }
    .client-page-subtitle {
        font-size: 0.82rem;
        color: #64748B;
        margin: 0.25rem 0 0;
    }
    .btn-ajukan-cr {
        background: #10B981;
        color: #fff !important;
        border: none;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 0.55rem 1.25rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        box-shadow: 0 2px 8px rgba(16,185,129,0.3);
        transition: all 0.18s ease;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .btn-ajukan-cr:hover {
        background: #059669;
        color: #fff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(16,185,129,0.35);
    }

    /* ── UX PROGRESS SUMMARY SECTION AT THE TOP (Grafik Progress Paling Atas) ── */
    .top-progress-card {
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
        color: #FFFFFF;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.12);
        border: 1px solid rgba(255,255,255,0.08);
    }
    .top-progress-headline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    /* ── Stat Pills ── */
    .client-stat-pills {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }
    .stat-pill {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 0.7rem 1.1rem;
        transition: box-shadow 0.18s, transform 0.18s;
        min-width: 105px;
        flex: 1;
    }
    .stat-pill:hover {
        box-shadow: 0 4px 14px rgba(15,23,42,0.08);
        transform: translateY(-1px);
    }
    .stat-pill-number {
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1;
        color: #0284C7;
        font-family: 'Space Grotesk', monospace;
        letter-spacing: -0.03em;
    }
    .stat-pill-label {
        font-size: 0.76rem;
        color: #64748B;
        font-weight: 500;
        line-height: 1.2;
    }

    /* ── 4 Category Filter Tabs ── */
    .client-nav-tabs {
        display: flex;
        gap: 0.5rem;
        border-bottom: 1px solid #E2E8F0;
        padding-bottom: 0.5rem;
        margin-bottom: 1.25rem;
        overflow-x: auto;
    }
    .client-nav-tab {
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748B;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .client-nav-tab:hover {
        color: #0284C7;
        background: #F0F9FF;
        border-color: #BAE6FD;
    }
    .client-nav-tab.active {
        color: #FFFFFF;
        background: #0284C7;
        border-color: #0284C7;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.28);
    }
    .tab-badge {
        font-size: 0.7rem;
        padding: 0.15rem 0.45rem;
        border-radius: 999px;
        background: rgba(0,0,0,0.08);
        color: inherit;
    }
    .client-nav-tab.active .tab-badge {
        background: rgba(255,255,255,0.25);
        color: #FFFFFF;
    }

    /* ── CR Cards Grid (Mencegah Empty Space > 1:3 dengan 2-Kolom) ── */
    .cr-client-card {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        padding: 1rem 1.15rem;
        transition: box-shadow 0.18s, transform 0.18s;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .cr-client-card:hover {
        box-shadow: 0 6px 20px rgba(15,23,42,0.09);
        transform: translateY(-1px);
        border-color: #BFDBFE;
    }
    .cr-client-card-inner {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
    }
    .cr-avatar-circle {
        width: 38px;
        height: 38px;
        min-width: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .cr-card-body { flex: 1; min-width: 0; }
    .cr-card-title {
        font-size: 0.92rem;
        font-weight: 700;
        color: #0F172A;
        margin: 0 0 0.2rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        letter-spacing: -0.01em;
    }
    .cr-card-meta {
        font-size: 0.76rem;
        color: #94A3B8;
        margin-bottom: 0.45rem;
        line-height: 1.3;
    }
    .cr-progress-label {
        font-size: 0.7rem;
        color: #94A3B8;
        font-weight: 500;
        margin-bottom: 0.2rem;
    }
    .cr-progress-bar-track {
        height: 5px;
        background: #F1F5F9;
        border-radius: 999px;
        overflow: hidden;
        flex: 1;
    }
    .cr-progress-bar-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.4s ease;
    }
    .cr-progress-pct {
        font-size: 0.72rem;
        color: #64748B;
        font-weight: 600;
        font-family: 'Space Grotesk', monospace;
        min-width: 28px;
        text-align: right;
    }
    .cr-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 0.55rem;
        flex-wrap: wrap;
        gap: 0.4rem;
        padding-top: 0.4rem;
        border-top: 1px dashed #F1F5F9;
    }
    .cr-status-pill-client {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
    }
    .cr-detail-link {
        font-size: 0.76rem;
        font-weight: 600;
        color: #0284C7;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
    }
    .cr-detail-link:hover { color: #0369A1; text-decoration: underline; }
    .client-empty-state {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 3rem 2rem;
        text-align: center;
        width: 100%;
    }
</style>

{{-- ── Page Header ── --}}
<div class="client-page-header">
    <div>
        <h1 class="client-page-title" data-i18n="client_dashboard">Dashboard Monitoring CR</h1>
        <p class="client-page-subtitle" data-i18n="client_subtitle">Rekapitulasi status dan monitoring Change Request Anda</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('change-requests.create') }}" class="btn-ajukan-cr" data-i18n="new_cr">
            <i class="bi bi-plus-lg"></i> Ajukan CR Baru
        </a>
    </div>
</div>

{{-- ── 1. GRAFIK PROGRESS DI BAGIAN PALING ATAS (UX Priority) ── --}}
@php
    $totalCR = $roleMetrics['total'] ?? 0;
    $doneCR  = $roleMetrics['completed'] ?? 0;
    $overallPct = $totalCR > 0 ? round(($doneCR / $totalCR) * 100) : 0;
@endphp
<div class="top-progress-card">
    <div class="top-progress-headline">
        <div class="d-flex align-items-center gap-2">
            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(56, 189, 248, 0.2); display: flex; align-items: center; justify-content: center; color: #38BDF8;">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div>
                <div class="fw-bold" style="font-size: 0.95rem; letter-spacing: -0.01em;">Overview Progress Penyelesaian CR</div>
                <div class="small" style="color: #94A3B8; font-size: 0.76rem;">Rangkuman kemajuan implementasi CR yang telah tuntas</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #34D399; font-size: 0.75rem; font-weight: 600; padding: 0.35rem 0.75rem; border-radius: 999px;">
                {{ $doneCR }} / {{ $totalCR }} CR Selesai
            </span>
            <span style="font-family: 'Space Grotesk', monospace; font-size: 1.25rem; font-weight: 800; color: #38BDF8;">
                {{ $overallPct }}%
            </span>
        </div>
    </div>
    <div class="progress" style="height: 8px; background-color: rgba(255,255,255,0.12); border-radius: 999px;">
        <div class="progress-bar" role="progressbar"
             style="width: {{ $overallPct }}%; background: linear-gradient(90deg, #0284C7, #10B981); border-radius: 999px; transition: width 0.6s ease;"
             aria-valuenow="{{ $overallPct }}" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</div>

{{-- ── 2. STAT PILLS (Metrik Singkat) ── --}}
<div class="client-stat-pills">
    <a href="{{ route('change-requests.index') }}" class="stat-pill text-decoration-none">
        <div class="stat-pill-number">{{ $roleMetrics['total'] ?? 0 }}</div>
        <div class="stat-pill-label">Total CR</div>
    </a>
    <a href="{{ route('change-requests.index', ['tab' => 'pending']) }}" class="stat-pill text-decoration-none">
        <div class="stat-pill-number" style="color:#D97706;">{{ $roleMetrics['pending_tab_count'] ?? 0 }}</div>
        <div class="stat-pill-label">Pending Approval</div>
    </a>
    <a href="{{ route('change-requests.index', ['tab' => 'progress']) }}" class="stat-pill text-decoration-none">
        <div class="stat-pill-number" style="color:#2563EB;">{{ $roleMetrics['progress_tab_count'] ?? 0 }}</div>
        <div class="stat-pill-label">In Progress</div>
    </a>
    <a href="{{ route('change-requests.index', ['tab' => 'payment']) }}" class="stat-pill text-decoration-none">
        <div class="stat-pill-number" style="color:#0284C7;">{{ $roleMetrics['payment_tab_count'] ?? 0 }}</div>
        <div class="stat-pill-label">Outstanding Payment</div>
    </a>
    <a href="{{ route('change-requests.index', ['tab' => 'history']) }}" class="stat-pill text-decoration-none">
        <div class="stat-pill-number" style="color:#10B981;">{{ $roleMetrics['history_tab_count'] ?? 0 }}</div>
        <div class="stat-pill-label">History / Selesai</div>
    </a>
</div>

{{-- ── 3. PEMISAHAN KATEGORI MENU DATA CR (4 Kategori / Tabs) ── --}}
@php
    $currentTab = request('tab', 'all');
@endphp
<div class="client-nav-tabs">
    <a href="{{ route('change-requests.index') }}" class="client-nav-tab {{ $currentTab === 'all' && !request('status') ? 'active' : '' }}">
        <span>Semua Data</span>
        <span class="tab-badge">{{ $roleMetrics['total'] ?? 0 }}</span>
    </a>
    <a href="{{ route('change-requests.index', ['tab' => 'pending']) }}" class="client-nav-tab {{ $currentTab === 'pending' ? 'active' : '' }}">
        <i class="bi bi-clock-history"></i>
        <span>Pending Approval</span>
        <span class="tab-badge">{{ $roleMetrics['pending_tab_count'] ?? 0 }}</span>
    </a>
    <a href="{{ route('change-requests.index', ['tab' => 'progress']) }}" class="client-nav-tab {{ $currentTab === 'progress' ? 'active' : '' }}">
        <i class="bi bi-arrow-repeat"></i>
        <span>In Progress / Outstanding</span>
        <span class="tab-badge">{{ $roleMetrics['progress_tab_count'] ?? 0 }}</span>
    </a>
    <a href="{{ route('change-requests.index', ['tab' => 'payment']) }}" class="client-nav-tab {{ $currentTab === 'payment' ? 'active' : '' }}">
        <i class="bi bi-credit-card"></i>
        <span>Outstanding Payment</span>
        <span class="tab-badge">{{ $roleMetrics['payment_tab_count'] ?? 0 }}</span>
    </a>
    <a href="{{ route('change-requests.index', ['tab' => 'history']) }}" class="client-nav-tab {{ $currentTab === 'history' ? 'active' : '' }}">
        <i class="bi bi-check2-circle"></i>
        <span>History Selesai</span>
        <span class="tab-badge">{{ $roleMetrics['history_tab_count'] ?? 0 }}</span>
    </a>
</div>

{{-- ── 4. RESPONSIVE 2-COLUMN GRID (Mencegah Empty Space Melebihi Rasio 1:3) ── --}}
<div class="row row-cols-1 row-cols-lg-2 g-3 mb-4">
@forelse ($changeRequests as $index => $cr)
    @php
        $sts      = $cr->status;
        $pill     = $statusColors[$sts] ?? ['bg' => '#F1F5F9', 'color' => '#475569', 'label' => ucfirst($sts)];
        $pct      = $statusProgress[$sts] ?? 0;
        $barColor = $progressBarColors[$sts] ?? '#3B82F6';
        $avatarBg = $avatarColors[$index % count($avatarColors)];
        $initial  = strtoupper(substr($cr->judul ?: 'C', 0, 1));
        $pm       = $cr->owner_cr ?? null;
    @endphp

    <div class="col">
        <div class="cr-client-card">
            <div>
                <div class="cr-client-card-inner">
                    {{-- Avatar --}}
                    <div class="cr-avatar-circle" style="background:{{ $avatarBg }};">{{ $initial }}</div>

                    {{-- Body --}}
                    <div class="cr-card-body">
                        <div class="cr-card-title" title="{{ $cr->judul }}">{{ $cr->judul }}</div>
                        <div class="cr-card-meta">
                            <span class="fw-semibold text-dark">{{ $cr->kode_cr }}</span> &bull; {{ $cr->tanggal_pengajuan?->format('d M Y') ?? '-' }}
                            @if($cr->proyek_terkait) &bull; <span class="text-primary">{{ $cr->proyek_terkait }}</span>@endif
                            @if($pm) &bull; PM: {{ $pm }}@endif
                        </div>

                        {{-- Progress Bar --}}
                        @if($sts !== 'ditolak')
                            <div class="cr-progress-label">Progress Pengerjaan</div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <div class="cr-progress-bar-track">
                                    <div class="cr-progress-bar-fill" style="width:{{ $pct }}%;background:{{ $barColor }};"></div>
                                </div>
                                <div class="cr-progress-pct">{{ $pct }}%</div>
                            </div>
                        @endif

                        {{-- Quotation Notice --}}
                        @if($cr->harga_penawaran !== null && !$cr->quotation_approved_client_at)
                            <div class="mt-2 p-2 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2"
                                 style="background:#FFFBEB;border:1px solid #FDE68A;font-size:0.75rem;">
                                <span style="color:#92400E;">
                                    <i class="bi bi-tag-fill me-1" style="color:#D97706;"></i>
                                    <strong>Quotation:</strong> Rp {{ number_format($cr->harga_penawaran, 0, ',', '.') }}
                                </span>
                                <form method="POST" action="{{ route('change-requests.quotation.client', $cr) }}" class="d-inline m-0">
                                    @csrf
                                    <button class="btn btn-sm fw-semibold px-2 py-1"
                                            style="background:#10B981;color:#fff;border:none;border-radius:999px;font-size:0.72rem;">
                                        <i class="bi bi-check-lg"></i> Setujui
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Card Footer --}}
            <div class="cr-card-footer">
                <span class="cr-status-pill-client" style="background:{{ $pill['bg'] }};color:{{ $pill['color'] }};">
                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $pill['color'] }};display:inline-block;flex-shrink:0;"></span>
                    {{ $pill['label'] }}
                </span>
                <a href="{{ route('change-requests.show', $cr) }}" class="cr-detail-link">
                    Lihat Detail <i class="bi bi-arrow-right" style="font-size:0.7rem;"></i>
                </a>
            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <div class="client-empty-state">
            <i class="bi bi-folder2-open fs-1 mb-3 d-block" style="color:#CBD5E1;"></i>
            <h6 class="fw-bold text-dark mb-1">Belum ada data pada kategori ini</h6>
            <p class="text-muted small mb-3">Tidak ditemukan Change Request untuk filter kategori yang sedang dipilih.</p>
            <a href="{{ route('change-requests.create') }}" class="btn-ajukan-cr">
                <i class="bi bi-plus-lg"></i> Ajukan CR Baru
            </a>
        </div>
    </div>
@endforelse
</div>

{{-- Pagination --}}
@if($changeRequests->hasPages())
    <div class="d-flex justify-content-center mt-3 mb-4">
        {{ $changeRequests->links() }}
    </div>
@endif

@endsection

