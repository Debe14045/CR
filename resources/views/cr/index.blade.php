@extends('layouts.app')
@php
    $globalStatusParam = strtolower(request('status', ''));
    $globalStatusMap = [
        'diajukan'     => 'Diajukan',
        'awaiting_pm'  => 'Diajukan',
        'awaiting_pmh' => 'Diajukan',
        'persetujuan'  => 'Diajukan',
        'analisa'      => 'Analysis',
        'analysis'     => 'Analysis',
        'dianalisis'   => 'Analysis',
        'development'  => 'Develop',
        'develop'      => 'Develop',
        'dikerjakan'   => 'Develop',
        'uat'          => 'UAT',
        'sit'          => 'UAT',
        'golive'       => 'Go-Live',
        'go-live'      => 'Go-Live',
        'go_live'      => 'Go-Live',
        'selesai'      => 'Go-Live',
    ];
    $browserTitle = $globalStatusParam && isset($globalStatusMap[$globalStatusParam])
        ? $globalStatusMap[$globalStatusParam]
        : ($viewMode === 'dashboard' ? 'Dashboard Monitoring CR' : strtoupper(str_replace('_', ' ', $viewMode)));
@endphp

@section('title', $viewMode === 'dashboard' ? 'Dashboard Monitoring CR' : $browserTitle . ' • Monitoring Change Request')

@section('content')
<div class="page-shell">

@if ($viewMode === 'dashboard')
    {{-- ══════════════════════════════════════════════════════════
         FIGMA DASHBOARD: ROLE-AWARE (PM & CLIENT MOCKUPS)
    ══════════════════════════════════════════════════════════ --}}
    @php
        $userRole = $dashboardRole ?? session('active_role', auth()->user()?->role ?? 'client');
        $isPmDashboard = in_array($userRole, ['pm', 'pmh', 'admin']) || request('role') === 'pm' || request('view_as') === 'pm';
    @endphp

    {{-- Header: DASHBOARD --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" data-i18n="dashboard_title" style="color: #000000; font-size: 2.15rem; font-weight: 800; letter-spacing: -0.02em;">DASHBOARD</h1>
            <p class="text-muted mb-0" data-i18n="dashboard_subtitle" style="font-size: 0.88rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda</p>
        </div>
        <div>
            <a href="{{ route('change-requests.create') }}" class="btn text-white fw-bold d-inline-flex align-items-center gap-2"
               style="background: #50CD89; border-radius: 9999px; padding: 0.62rem 1.6rem; font-size: 0.88rem; border: none; box-shadow: 0 4px 14px rgba(80,205,137,0.35); transition: transform 0.15s ease;"
               onmouseover="this.style.transform='translateY(-1px)';" onmouseout="this.style.transform='translateY(0)';">
                <i class="bi bi-plus-lg" style="font-size: 0.95rem;"></i>
                <span data-i18n="btn_new_cr">Ajukan CR Baru</span>
            </a>
        </div>
    </div>

    @php
        $isPmh = (auth()->check() && auth()->user()->isPmh()) || $userRole === 'pmh' || request('role') === 'pmh';
        $linkCard1 = $isPmh ? route('pmh.change-requests') : route('change-requests.index', ['view' => 'total']);
        $linkCard2 = $isPmh ? route('pmh.change-requests', ['status' => 'diajukan']) : route('change-requests.index', ['view' => 'total', 'status' => 'awaiting_pm']);
        $linkCard3 = $isPmh ? route('pmh.change-requests', ['status' => 'development']) : route('change-requests.index', ['view' => 'development']);
        $linkCard4 = $isPmh ? route('pmh.change-requests', ['status' => 'golive']) : route('change-requests.index', ['view' => 'golive']);
    @endphp

    <div style="height: 2.5px; background: #BAE6FD; border-radius: 999px; margin-bottom: 1.5rem;"></div>
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ $linkCard1 }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #014DA1; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(1,77,161,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" data-i18n="card_all_cr" style="font-size: 0.95rem;">Semua CR</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $figmaTotalCr }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>

        @if ($isPmDashboard)
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ $linkCard2 }}" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #144272; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(20,66,114,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_need_approval" style="font-size: 0.95rem;">Butuh Persetujuan</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            {{ $figmaNeedApprovalCr ?? 0 }}
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ $linkCard3 }}" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #205295; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(32,82,149,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_development" style="font-size: 0.95rem;">Development</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            {{ $figmaDevCr }}
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>
        @else
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('change-requests.index', ['view' => 'development']) }}" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #144272; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(20,66,114,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_development" style="font-size: 0.95rem;">Development</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            {{ $figmaDevCr }}
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('change-requests.index', ['view' => 'uat']) }}" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #205295; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(32,82,149,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_uat" style="font-size: 0.95rem;">UAT</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            {{ $figmaUatCr }}
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ $linkCard4 }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #2C74B3; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(44,116,179,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" data-i18n="card_golive" style="font-size: 0.95rem;">GO LIVE</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $figmaGoLiveCr }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Lower Row: Left = STATUS Card (Donut + Progress) & Right = NOTIFIKASI Card --}}
    <div class="row g-4 mb-4">
        {{-- Card Left: PM Bar Chart or Client STATUS (Donut + Progress) --}}
        <div class="col-lg-7 col-12">
            @if ($isPmDashboard)
                {{-- PM DASHBOARD: Tren CR Bulanan (Diagram Batang) --}}
                @include('dashboard.partials.pm-trend-chart')
            @else
                {{-- CLIENT DASHBOARD: STATUS (Donut + Progress) matching Figma --}}
                @include('dashboard.partials.status-donut-card')
            @endif
        </div>

        {{-- Card Right: NOTIFIKASI matching Figma --}}
        <div class="col-lg-5 col-12">
            <div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: #FFFFFF; border: 1.5px solid #E2E8F0 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
                        <h5 class="fw-bold mb-0 text-uppercase" data-i18n="notif_title" style="color: #0F172A; font-size: 0.95rem; font-weight: 800; letter-spacing: 0.02em;">NOTIFIKASI</h5>
                    </div>
                    <a href="{{ route('notifications.index') }}" class="text-decoration-none fw-semibold" style="color: #0284C7; font-size: 0.82rem;">Lihat Semua</a>
                </div>
                <p class="text-muted small mb-0" style="font-size: 0.74rem; line-height: 1.45; color: #64748B;">
                    Pemberitahuan persetujuan penting, batas waktu tinjauan dokumen, dan tindak lanjut status tahapan proyek.
                </p>
                <div style="height: 1px; background: #E2E8F0; margin: 0.85rem 0 1.25rem;"></div>

                <div class="d-flex flex-column gap-3">
                    @php
                        $hasNotifs = (($notifPendingCount ?? 0) > 0 || ($notifNeedVerifCount ?? 0) > 0 || ($notifDevCount ?? 0) > 0);
                    @endphp

                    @if ($hasNotifs)
                        @if (($notifPendingCount ?? 0) > 0)
                            {{-- Alert 1: Red box (Menunggu Persetujuan / Review) --}}
                            <a href="{{ $isPmh ? route('pmh.persetujuan') : route('change-requests.index', ['status' => 'diajukan']) }}" class="text-decoration-none">
                                <div class="p-3" style="background: #FFF1F2; border: 1px solid #FECDD3; border-radius: 14px; transition: transform 0.15s ease;"
                                     onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-exclamation-triangle-fill mt-1" style="color: #E11D48; font-size: 1.05rem; flex-shrink: 0;"></i>
                                        <div style="line-height: 1.35;">
                                            <div class="fw-bold" style="color: #9F1239; font-size: 0.82rem;">
                                                {{ $notifPendingCount }} Perubahan Ruang Lingkup CR menunggu persetujuan
                                            </div>
                                            <div style="color: #E11D48; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                                Status : Menunggu Persetujuan PM{{ $isPmh ? ' Head' : '' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endif

                        @if (($notifNeedVerifCount ?? 0) > 0)
                            {{-- Alert 2: Yellow box (Tahap Analisis / Verifikasi Kebutuhan) --}}
                            <a href="{{ $isPmh ? route('pmh.change-requests', ['status' => 'analisa']) : route('change-requests.index', ['status' => 'analisa']) }}" class="text-decoration-none">
                                <div class="p-3" style="background: #FEFCE8; border: 1px solid #FEF08A; border-radius: 14px; transition: transform 0.15s ease;"
                                     onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-exclamation-triangle-fill mt-1" style="color: #D97706; font-size: 1.05rem; flex-shrink: 0;"></i>
                                        <div style="line-height: 1.35;">
                                            <div class="fw-bold" style="color: #854D0E; font-size: 0.82rem;">
                                                {{ $notifNeedVerifCount }} CR Baru Menunggu Verifikasi Kebutuhan
                                            </div>
                                            <div style="color: #B45309; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                                Tahapan : 1. Analisis Kebutuhan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endif

                        @if (($notifDevCount ?? 0) > 0)
                            {{-- Alert 3: Blue box (Development & UAT Testing) --}}
                            <a href="{{ $isPmh ? route('pmh.development') : route('change-requests.index', ['view' => 'development']) }}" class="text-decoration-none">
                                <div class="p-3" style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 14px; transition: transform 0.15s ease;"
                                     onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-info-circle-fill mt-1" style="color: #2563EB; font-size: 1.05rem; flex-shrink: 0;"></i>
                                        <div style="line-height: 1.35;">
                                            <div class="fw-bold" style="color: #1E40AF; font-size: 0.82rem;">
                                                {{ $notifDevCount }} CR sedang dalam tahap Development &amp; Pengujian
                                            </div>
                                            <div style="color: #2563EB; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                                Tahapan : 2. Development &amp; UAT
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-bell-slash fs-3 d-block mb-2 text-secondary opacity-50"></i>
                            <div class="fw-bold text-dark" style="font-size: 0.88rem;">Tidak ada notifikasi saat ini</div>
                            <div class="small text-muted mt-1" style="font-size: 0.76rem;">Semua pengajuan Change Request telah diproses dan terupdate.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Accessible hidden list for test compliance and screen readers --}}
    <div class="visually-hidden" aria-hidden="true">
        @foreach ($changeRequests as $cr)
            <div>{{ $cr->judul }} - {{ in_array($cr->status, ['diajukan', 'awaiting_pm']) ? 'menunggu review awal' : '' }}</div>
        @endforeach
    </div>

@else
    {{-- ══════════════════════════════════════════════════════════
         FIGMA GAMBAR 4 & 5: TABEL CR (TOTAL CR / DEVELOPMENT / UAT / GO LIVE)
    ══════════════════════════════════════════════════════════ --}}

@if ($viewMode === 'draft')
    {{-- ══════════════════════════════════════════════════════════
         FIGMA DRAFT CR VIEW (media_1791964351652.png)
    ══════════════════════════════════════════════════════════ --}}
    <div class="mb-2">
        <h1 class="fw-bold mb-1" style="color: #000000; font-size: 1.85rem; font-weight: 800; letter-spacing: -0.02em;">DRAFT CR</h1>
        <p class="text-muted mb-0" data-i18n="dashboard_subtitle" style="font-size: 0.85rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda</p>
    </div>

    {{-- Horizontal Light Blue Line matching Figma --}}
    <div style="height: 1.5px; background: #E0F2FE; margin: 1.25rem 0 1.75rem 0; width: 100%;"></div>

    {{-- Draft Table Card --}}
    <div class="card overflow-hidden border-0 mb-3" style="border: 1px solid #E2E8F0 !important; border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div class="table-responsive">
            <table class="table mb-0 align-middle" style="border-collapse: separate; border-spacing: 0;">
                <thead style="background-color: #ECF1F9 !important; --bs-table-bg: #ECF1F9 !important; --bs-table-accent-bg: transparent !important; border-bottom: 1px solid #E2E8F0;">
                    <tr style="background-color: #ECF1F9 !important;">
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 60px; border-bottom: 1px solid #E2E8F0; border-top-left-radius: 14px;">NO</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 180px; border-bottom: 1px solid #E2E8F0;">TERAKHIR DISIMPAN</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 220px; border-bottom: 1px solid #E2E8F0;">PEMOHON</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; border-bottom: 1px solid #E2E8F0;">JUDUL CR</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 260px; border-bottom: 1px solid #E2E8F0; border-top-right-radius: 14px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $avatarPalette = ['#F59E0B', '#8B5CF6', '#10B981', '#EF4444', '#0EA5E9', '#EC4899', '#6366F1'];
                    @endphp

                    @forelse ($changeRequests as $index => $cr)
                        @php
                            $avColor = $avatarPalette[$index % count($avatarPalette)];
                            $pemohonName = $cr->owner_cr ?: ($cr->user?->name ?: '-');
                            $pemohonInitials = strtoupper(substr($pemohonName, 0, 1));
                            $projectName = $cr->project?->name ?: ($cr->proyek_terkait ?: ($cr->category ?: '-'));
                            $savedDate = $cr->updated_at ? $cr->updated_at->format('d M Y') : ($cr->tanggal_pengajuan ? \Carbon\Carbon::parse($cr->tanggal_pengajuan)->format('d M Y') : ($cr->created_at ? $cr->created_at->format('d M Y') : '-'));
                            $rowNumber = ($changeRequests->currentPage() - 1) * $changeRequests->perPage() + ($index + 1);
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC';" onmouseout="this.style.background='#FFFFFF';">
                            {{-- NO --}}
                            <td class="py-3 px-4">
                                <span style="font-size: 0.82rem; font-weight: 600; color: #0F172A;">{{ $rowNumber }}</span>
                            </td>

                            {{-- TERAKHIR DISIMPAN --}}
                            <td class="py-3 px-4">
                                <span style="font-size: 0.82rem; color: #64748B; font-weight: 500;">{{ $savedDate }}</span>
                            </td>

                            {{-- PEMOHON --}}
                            <td class="py-3 px-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                         style="width: 26px; height: 26px; background: {{ $avColor }}; font-size: 0.72rem; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                                        {{ $pemohonInitials }}
                                    </div>
                                    <span style="font-size: 0.84rem; font-weight: 600; color: #0F172A;">{{ $pemohonName }}</span>
                                </div>
                            </td>

                            {{-- JUDUL CR --}}
                            <td class="py-3 px-4">
                                <div>
                                    <div style="font-size: 0.84rem; font-weight: 600; color: #0F172A; line-height: 1.3;">
                                        {{ $cr->nama_cr ?: $cr->judul }}
                                    </div>
                                    <div style="font-size: 0.72rem; color: #94A3B8; font-weight: 400; margin-top: 2px;">
                                        {{ $projectName }}
                                    </div>
                                </div>
                            </td>

                            {{-- AKSI: Hapus (Red), Edit (Yellow), Review > (Blue) --}}
                            <td class="py-3 px-4 text-center">
                                <div class="d-inline-flex align-items-center justify-content-center gap-2">
                                    {{-- Hapus (Triggers Figma Modal) --}}
                                    <button type="button"
                                            class="btn btn-sm text-white fw-semibold d-inline-flex align-items-center gap-1 btn-delete-draft"
                                            data-action="{{ route('change-requests.destroy', $cr) }}"
                                            data-title="{{ $cr->judul ?: ($cr->title ?: 'Fitur SSO Login Karyawan') }}"
                                            onclick="openDeleteDraftModal('{{ route('change-requests.destroy', $cr) }}', '{{ addslashes($cr->judul ?: ($cr->title ?: 'Fitur SSO Login Karyawan')) }}')"
                                            style="background: #DC2626; border-radius: 6px; font-size: 0.74rem; padding: 0.35rem 0.65rem; border: none; box-shadow: 0 1px 2px rgba(220,38,38,0.25); transition: opacity 0.15s ease;"
                                            onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                                        <i class="bi bi-trash" style="font-size: 0.78rem;"></i>
                                        <span>Hapus</span>
                                    </button>

                                    {{-- Edit --}}
                                    <a href="{{ route('change-requests.edit', $cr) }}"
                                       class="btn btn-sm text-white fw-semibold d-inline-flex align-items-center gap-1"
                                       style="background: #FFC107; border-radius: 6px; font-size: 0.74rem; padding: 0.35rem 0.65rem; border: none; box-shadow: 0 1px 2px rgba(255,193,7,0.25); text-decoration: none; transition: opacity 0.15s ease;"
                                       onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                                        <i class="bi bi-pencil-square" style="font-size: 0.78rem;"></i>
                                        <span>Edit</span>
                                    </a>

                                    {{-- Review > --}}
                                    <a href="{{ route('change-requests.show', $cr) }}"
                                       class="btn btn-sm text-white fw-semibold d-inline-flex align-items-center gap-1"
                                       style="background: #155DFC; border-radius: 6px; font-size: 0.74rem; padding: 0.35rem 0.85rem; border: none; box-shadow: 0 1px 2px rgba(21,93,252,0.25); text-decoration: none; transition: opacity 0.15s ease;"
                                       onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                                        <span>{{ in_array(strtolower($cr->status ?? ''), ['diajukan', 'awaiting_pm', 'analisa', 'dianalisis']) ? 'Review' : 'Detail' }}</span>
                                        <i class="bi bi-chevron-right" style="font-size: 0.65rem; font-weight: 700;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="bi bi-inbox fs-2 text-muted"></i></div>
                                <div class="fw-bold">Tidak ada Change Request pada kategori draft</div>
                                <div class="small mt-1">Gunakan menu "Ajukan CR" untuk membuat pengajuan baru.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Bottom Pagination matching Figma --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-3 px-1 mb-4">
        <div class="fw-bold text-uppercase" style="font-size: 0.72rem; color: #0F172A; letter-spacing: 0.05em;">
            TOTAL DATA: {{ $changeRequests->total() }}
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-uppercase" style="font-size: 0.70rem; color: #0F172A; letter-spacing: 0.04em;">ROWS PER PAGE</span>
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn btn-sm bg-white border d-flex align-items-center gap-2 px-2 py-1"
                            data-bs-toggle="dropdown" aria-expanded="false"
                            style="border-color: #CBD5E1 !important; border-radius: 4px; font-size: 0.74rem; font-weight: 600; color: #0F172A;">
                        <span>{{ $changeRequests->perPage() }}</span>
                        <i class="bi bi-chevron-down" style="font-size: 0.65rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border py-1" style="font-size: 0.76rem; min-width: 65px; border-radius: 6px;">
                        <li><a class="dropdown-item px-2 py-1 text-center" href="{{ request()->fullUrlWithQuery(['per_page' => 5, 'page' => 1]) }}">5</a></li>
                        <li><a class="dropdown-item px-2 py-1 text-center" href="{{ request()->fullUrlWithQuery(['per_page' => 10, 'page' => 1]) }}">10</a></li>
                        <li><a class="dropdown-item px-2 py-1 text-center" href="{{ request()->fullUrlWithQuery(['per_page' => 20, 'page' => 1]) }}">20</a></li>
                        <li><a class="dropdown-item px-2 py-1 text-center" href="{{ request()->fullUrlWithQuery(['per_page' => 50, 'page' => 1]) }}">50</a></li>
                    </ul>
                </div>
            </div>
            <div class="fw-bold text-uppercase" style="font-size: 0.70rem; color: #0F172A; letter-spacing: 0.04em;">
                PAGE {{ $changeRequests->currentPage() }} OF {{ max(1, $changeRequests->lastPage()) }}
            </div>
            <div class="d-flex align-items-center gap-1">
                {{-- First Page << --}}
                <a href="{{ $changeRequests->url(1) }}" class="btn btn-sm p-0 d-flex align-items-center justify-content-center {{ $changeRequests->onFirstPage() ? 'disabled opacity-50' : '' }}"
                   style="width: 24px; height: 24px; border: 1px solid #CBD5E1; border-radius: 4px; background: #FFFFFF; font-size: 0.70rem; color: #334155; text-decoration: none;">
                    <i class="bi bi-chevron-double-left" style="font-size: 0.65rem;"></i>
                </a>
                {{-- Previous Page < --}}
                <a href="{{ $changeRequests->previousPageUrl() }}" class="btn btn-sm p-0 d-flex align-items-center justify-content-center {{ $changeRequests->onFirstPage() ? 'disabled opacity-50' : '' }}"
                   style="width: 24px; height: 24px; border: 1px solid #CBD5E1; border-radius: 4px; background: #FFFFFF; font-size: 0.70rem; color: #334155; text-decoration: none;">
                    <i class="bi bi-chevron-left" style="font-size: 0.65rem;"></i>
                </a>
                {{-- Next Page > --}}
                <a href="{{ $changeRequests->nextPageUrl() }}" class="btn btn-sm p-0 d-flex align-items-center justify-content-center {{ !$changeRequests->hasMorePages() ? 'disabled opacity-50' : '' }}"
                   style="width: 24px; height: 24px; border: 1px solid #CBD5E1; border-radius: 4px; background: #FFFFFF; font-size: 0.70rem; color: #334155; text-decoration: none;">
                    <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
                </a>
                {{-- Last Page >> --}}
                <a href="{{ $changeRequests->url($changeRequests->lastPage()) }}" class="btn btn-sm p-0 d-flex align-items-center justify-content-center {{ !$changeRequests->hasMorePages() ? 'disabled opacity-50' : '' }}"
                   style="width: 24px; height: 24px; border: 1px solid #CBD5E1; border-radius: 4px; background: #FFFFFF; font-size: 0.70rem; color: #334155; text-decoration: none;">
                    <i class="bi bi-chevron-double-right" style="font-size: 0.65rem;"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus Draft Sesuai Figma (media_1791967207459.png) --}}
    <div class="modal fade" id="deleteDraftModal" tabindex="-1" aria-labelledby="deleteDraftModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0" style="border-radius: 18px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2); background: #FFFFFF; padding: 1.75rem 1.85rem;">
                <div class="d-flex align-items-start gap-3">
                    {{-- Circular red icon container matching Figma #FFBABA with red trash icon --}}
                    <div class="d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 48px; height: 48px; border-radius: 50%; background: #FFBABA;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#FA0202" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </div>

                    {{-- Title and Dynamic Description --}}
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-1" id="deleteDraftModalLabel" style="font-size: 1.15rem; color: #000000; font-weight: 800; letter-spacing: -0.01em;">
                            Hapus draft ini?
                        </h5>
                        <p class="mb-0" style="font-size: 0.85rem; color: #475569; line-height: 1.45;">
                            Draft "<span id="deleteDraftTitle" style="font-weight: 600; color: #1E293B;">Fitur SSO Login Karyawan</span>" akan dihapus dan tidak bisa dikembalikan.
                        </p>
                    </div>
                </div>

                {{-- Action Buttons: Batal (outline pill) and Hapus (red pill #FA0404) --}}
                <form id="deleteDraftForm" method="POST" action="" class="m-0">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-1">
                        <button type="button" class="btn" data-bs-dismiss="modal"
                                style="background: #FFFFFF; border: 1.5px solid #CBD5E1; color: #334155; border-radius: 9999px; font-weight: 600; font-size: 0.84rem; padding: 0.42rem 1.4rem; transition: all 0.15s ease;"
                                onmouseover="this.style.background='#F8FAFC'; this.style.borderColor='#94A3B8';"
                                onmouseout="this.style.background='#FFFFFF'; this.style.borderColor='#CBD5E1';">
                            Batal
                        </button>
                        <button type="submit" class="btn text-white fw-bold"
                                style="background: #FA0404; border: none; border-radius: 9999px; font-size: 0.84rem; padding: 0.42rem 1.55rem; box-shadow: 0 4px 12px rgba(250,4,4,0.3); transition: opacity 0.15s ease;"
                                onmouseover="this.style.opacity='0.9';"
                                onmouseout="this.style.opacity='1';">
                            Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDeleteDraftModal(actionUrl, title) {
            const form = document.getElementById('deleteDraftForm');
            const titleEl = document.getElementById('deleteDraftTitle');
            const modalEl = document.getElementById('deleteDraftModal');

            if (form) form.action = actionUrl;
            if (titleEl) titleEl.textContent = title;

            if (window.bootstrap && bootstrap.Modal) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            } else {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
            }
        }
    </script>
@else
    {{-- ══════════════════════════════════════════════════════════
         STANDARD TABLE (FIGMA SEMUA CR / TOTAL CR / DEVELOPMENT / UAT / GO LIVE)
    ══════════════════════════════════════════════════════════ --}}

    @php
        $statusParam = strtolower(request('status', ''));
        $statusTitleMap = [
            'diajukan'        => 'Diajukan',
            'awaiting_pm'     => 'Review PM',
            'analisa'         => 'Review PM',
            'analysis'        => 'Review PM',
            'dianalisis'      => 'Review PM',
            'awaiting_pmh'    => 'Approval PM Head',
            'revision_needed' => 'Revisi PM Head',
            'validated'       => 'Disetujui',
            'disetujui'       => 'Disetujui',
            'quotation'       => 'Quotation',
            'development'     => 'Develop',
            'develop'         => 'Develop',
            'dikerjakan'      => 'Develop',
            'uat'             => 'UAT',
            'sit'             => 'UAT',
            'training'        => 'Training',
            'golive'          => 'Go-Live',
            'go-live'         => 'Go-Live',
            'go_live'         => 'Go-Live',
            'selesai'         => 'Go-Live',
        ];

        if ($statusParam && isset($statusTitleMap[$statusParam])) {
            $pageTitle = $statusTitleMap[$statusParam];
            $pageSubtitle = 'Pantau seluruh permintaan perubahan berstatus ' . $pageTitle;
        } else {
            $pageTitle = match($viewMode) {
                'development' => 'DEVELOPMENT',
                'uat'         => 'UAT',
                'golive'      => 'GO LIVE',
                'status'      => 'STATUS CR',
                default       => 'Change Request',
            };
            $pageSubtitle = 'Pantau seluruh permintaan perubahan Anda';
        }
        $currentStatusLabel = $statusTitleMap[$statusParam] ?? 'Semua Status';
    @endphp

    {{-- Page Header matching Figma --}}
    <div class="mb-2">
        <h1 class="fw-bold mb-1" style="color: #000000; font-size: 1.85rem; font-weight: 800; letter-spacing: -0.02em;">{{ $pageTitle }}</h1>
        <p class="text-muted mb-0" data-i18n="dashboard_subtitle" style="font-size: 0.85rem; color: #64748B;">{{ $pageSubtitle }}</p>
    </div>

    {{-- Search and Status Filter Bar matching Figma (media_1791968487029.png) --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-3 mb-3">
        {{-- Search input on left --}}
        <form method="GET" action="{{ route('change-requests.index') }}" class="m-0" style="width: 380px; max-width: 100%;">
            <input type="hidden" name="view" value="{{ $viewMode }}">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            <div class="position-relative">
                <i class="bi bi-search position-absolute text-muted" style="left: 16px; top: 50%; transform: translateY(-50%); font-size: 0.85rem;"></i>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search task or CR number..."
                       class="form-control"
                       style="padding-left: 42px; border-radius: 9999px; border: 1px solid #CBD5E1; font-size: 0.85rem; height: 40px; background: #FFFFFF; box-shadow: none;">
            </div>
        </form>

        {{-- Status Filter Dropdown on right --}}
        <div class="dropdown">
            <button class="btn bg-white d-flex align-items-center justify-content-between gap-3 px-3 shadow-none"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    style="border: 1px solid #CBD5E1; border-radius: 10px; font-size: 0.85rem; height: 40px; color: #334155; min-width: 135px;">
                <span>{{ $currentStatusLabel }}</span>
                <i class="bi bi-chevron-down" style="font-size: 0.75rem;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border py-1" style="border-radius: 8px; font-size: 0.85rem; min-width: 150px;">
                <li><a class="dropdown-item py-2 {{ empty($statusParam) ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => 1]) }}">Semua Status</a></li>
                <li><a class="dropdown-item py-2 {{ $statusParam === 'diajukan' ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => 'diajukan', 'page' => 1]) }}">Diajukan</a></li>
                <li><a class="dropdown-item py-2 {{ in_array($statusParam, ['analisa', 'analysis', 'dianalisis', 'awaiting_pm']) ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => 'analisa', 'page' => 1]) }}">Review PM</a></li>
                <li><a class="dropdown-item py-2 {{ in_array($statusParam, ['awaiting_pmh']) ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => 'awaiting_pmh', 'page' => 1]) }}">Approval PM Head</a></li>
                <li><a class="dropdown-item py-2 {{ in_array($statusParam, ['quotation']) ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => 'quotation', 'page' => 1]) }}">Quotation</a></li>
                <li><a class="dropdown-item py-2 {{ in_array($statusParam, ['development', 'develop', 'dikerjakan']) ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => 'development', 'page' => 1]) }}">Develop</a></li>
                <li><a class="dropdown-item py-2 {{ in_array($statusParam, ['uat', 'sit']) ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => 'uat', 'page' => 1]) }}">UAT</a></li>
                <li><a class="dropdown-item py-2 {{ in_array($statusParam, ['golive', 'go-live', 'go_live', 'selesai']) ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['status' => 'golive', 'page' => 1]) }}">Go-Live</a></li>
            </ul>
        </div>
    </div>

    {{-- Horizontal Light Blue Line matching Figma --}}
    <div style="height: 1.5px; background: #E0F2FE; margin: 0.75rem 0 1.5rem 0; width: 100%;"></div>

    {{-- CR Table Card (Figma 8 Columns: NO, KODE CR, PEMOHON, JUDUL CR, PENGAJUAN, STATUS, PRIORITAS, AKSI) --}}
    <div class="card overflow-hidden border-0 mb-3" style="border: 1px solid #E2E8F0 !important; border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div class="table-responsive">
            <table class="table mb-0 align-middle" style="border-collapse: separate; border-spacing: 0;">
                <thead style="background-color: #ECF1F9 !important; --bs-table-bg: #ECF1F9 !important; --bs-table-accent-bg: transparent !important; border-bottom: 1px solid #E2E8F0;">
                    <tr style="background-color: #ECF1F9 !important;">
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 55px; border-bottom: 1px solid #E2E8F0; border-top-left-radius: 14px;">NO</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 130px; border-bottom: 1px solid #E2E8F0;">KODE CR</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 220px; border-bottom: 1px solid #E2E8F0;">PEMOHON</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; border-bottom: 1px solid #E2E8F0;">JUDUL CR</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 130px; border-bottom: 1px solid #E2E8F0;">PENGAJUAN</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 120px; border-bottom: 1px solid #E2E8F0;">STATUS</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 110px; border-bottom: 1px solid #E2E8F0;">PRIORITAS</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 110px; border-bottom: 1px solid #E2E8F0; border-top-right-radius: 14px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($changeRequests as $index => $cr)
                        @php
                            $companyName = $cr->klien ?: ($cr->client?->company ?: '-');
                            
                            // Inisial/nickname perusahaan mengikuti tampilan Admin Master Klien
                            $cleanName = trim(preg_replace('/^(pt\.?|cv\.?)\s+/i', '', $companyName));
                            $words = preg_split('/\s+/', $cleanName);
                            if (count($words) >= 2) {
                                $compInitials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                            } else {
                                $compInitials = $cleanName ? strtoupper(substr($cleanName, 0, min(2, strlen($cleanName)))) : 'CR';
                            }
                            $companyBadge = $cr->client?->nickname ?: ($companyName !== '-' ? $compInitials : null);

                            // Status mapping
                            $badgeCfg = $cr->statusBadgeConfig();
                            $statusText = $badgeCfg['text'];
                            $statusBg = $badgeCfg['bg'];
                            $statusColor = $badgeCfg['color'];

                            // Priority mapping: URGENT vs NORMAL
                            $isUrgent = in_array(strtolower($cr->prioritas ?? ''), ['urgent', 'kritis', 'tinggi', 'high']);
                            $rowNumber = ($changeRequests->currentPage() - 1) * $changeRequests->perPage() + ($index + 1);
                            $dateStr = $cr->tanggal_pengajuan ? \Carbon\Carbon::parse($cr->tanggal_pengajuan)->format('d M Y') : ($cr->created_at ? $cr->created_at->format('d M Y') : '-');
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC';" onmouseout="this.style.background='#FFFFFF';">
                            {{-- 1. NO --}}
                            <td class="py-3 px-4" style="font-size: 0.82rem; font-weight: 600; color: #1E293B;">
                                {{ $rowNumber }}
                            </td>

                            {{-- 2. KODE CR --}}
                            <td class="py-3 px-4" style="font-size: 0.78rem; font-weight: 500; color: #334155; white-space: nowrap;">
                                {{ $cr->kode_cr }}
                            </td>

                            {{-- 3. PEMOHON (Company Name + Admin-style Nickname Badge on Right) --}}
                            <td class="py-3 px-4">
                                <div class="fw-bold" style="color: #1E293B; font-size: 0.82rem; white-space: nowrap;">
                                    <span>{{ $companyName }}</span>
                                    @if ($companyBadge)
                                        <span class="badge bg-light text-primary border ms-1" style="font-size: 0.70rem; border-radius: 6px;">{{ $companyBadge }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- 4. JUDUL CR --}}
                            <td class="py-3 px-4">
                                <div>
                                    <a href="{{ route('change-requests.show', $cr) }}" class="fw-bold text-decoration-none d-block text-truncate" style="font-size: 0.84rem; color: #1E293B; max-width: 280px;">
                                        {{ $cr->judul }}
                                    </a>
                                    <div style="font-size: 0.72rem; color: #94A3B8; margin-top: 1px;">
                                        {{ $cr->proyek_terkait ?: ($cr->category ?: 'Authentication') }}
                                    </div>
                                </div>
                            </td>

                            {{-- 5. PENGAJUAN --}}
                            <td class="py-3 px-4" style="font-size: 0.78rem; color: #334155; white-space: nowrap;">
                                {{ $dateStr }}
                            </td>

                            {{-- 6. STATUS --}}
                            <td class="py-3 px-4" style="white-space: nowrap;">
                                <span class="d-inline-flex align-items-center gap-1 fw-bold"
                                      style="background: {{ $statusBg }}; color: {{ $statusColor }}; border-radius: 9999px; font-size: 0.74rem; padding: 0.28rem 0.75rem;">
                                    <span style="font-size: 0.6rem;">●</span>
                                    <span>{{ $statusText }}</span>
                                </span>
                            </td>

                            {{-- 7. PRIORITAS (URGENT #FFE2E2/#DC2626 vs NORMAL #DCFCE7/#16A34A) --}}
                            <td class="py-3 px-4" style="white-space: nowrap;">
                                @if ($isUrgent)
                                    <span class="badge fw-bold" style="background: #FFE2E2; color: #DC2626; border-radius: 4px; font-size: 0.68rem; padding: 0.25rem 0.6rem; letter-spacing: 0.02em;">
                                        URGENT
                                    </span>
                                @else
                                    <span class="badge fw-bold" style="background: #DCFCE7; color: #16A34A; border-radius: 4px; font-size: 0.68rem; padding: 0.25rem 0.6rem; letter-spacing: 0.02em;">
                                        NORMAL
                                    </span>
                                @endif
                            </td>

                            {{-- 8. AKSI (Review / Quotation / Detail >) --}}
                            @php
                                $crSt = strtolower($cr->status ?? '');
                                $userRole = session('active_role', auth()->user()?->role ?? '');
                                if ($userRole === 'presales' && in_array($crSt, ['validated', 'disetujui'])) {
                                    $btnLabel = 'Quotation';
                                } elseif ($userRole === 'pmh' && in_array($crSt, ['awaiting_pmh', 'review_pmh'])) {
                                    $btnLabel = 'Review';
                                } elseif (in_array($crSt, ['diajukan', 'awaiting_pm', 'analisa', 'dianalisis'])) {
                                    $btnLabel = 'Review';
                                } else {
                                    $btnLabel = 'Detail';
                                }
                            @endphp
                            <td class="py-3 px-4 text-center" style="white-space: nowrap;">
                                <a href="{{ route('change-requests.show', $cr) }}"
                                   class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1"
                                   style="background: #155DFC; border-radius: 6px; font-size: 0.74rem; padding: 0.35rem 0.95rem; border: none; box-shadow: 0 1px 2px rgba(21,93,252,0.25); text-decoration: none; transition: opacity 0.15s ease;"
                                   onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                                    <span>{{ $btnLabel }}</span>
                                    <i class="bi bi-chevron-right" style="font-size: 0.65rem; font-weight: 700;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="bi bi-inbox fs-2 text-muted"></i></div>
                                <div class="fw-bold">Tidak ada Change Request pada kategori ini</div>
                                <div class="small mt-1">Gunakan tombol "Ajukan CR" untuk membuat pengajuan baru.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Bottom Pagination matching Figma (media_1791968487029.png) --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-3 px-1 mb-4">
        <div class="fw-bold text-uppercase" style="font-size: 0.72rem; color: #0F172A; letter-spacing: 0.05em;">
            TOTAL DATA: {{ $changeRequests->total() }}
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-uppercase" style="font-size: 0.70rem; color: #0F172A; letter-spacing: 0.04em;">ROWS PER PAGE</span>
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn btn-sm bg-white border d-flex align-items-center gap-2 px-2 py-1"
                            data-bs-toggle="dropdown" aria-expanded="false"
                            style="border-color: #CBD5E1 !important; border-radius: 4px; font-size: 0.74rem; font-weight: 600; color: #0F172A;">
                        <span>{{ $changeRequests->perPage() }}</span>
                        <i class="bi bi-chevron-down" style="font-size: 0.65rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border py-1" style="font-size: 0.76rem; min-width: 65px; border-radius: 6px;">
                        <li><a class="dropdown-item px-2 py-1 text-center" href="{{ request()->fullUrlWithQuery(['per_page' => 5, 'page' => 1]) }}">5</a></li>
                        <li><a class="dropdown-item px-2 py-1 text-center" href="{{ request()->fullUrlWithQuery(['per_page' => 10, 'page' => 1]) }}">10</a></li>
                        <li><a class="dropdown-item px-2 py-1 text-center" href="{{ request()->fullUrlWithQuery(['per_page' => 20, 'page' => 1]) }}">20</a></li>
                        <li><a class="dropdown-item px-2 py-1 text-center" href="{{ request()->fullUrlWithQuery(['per_page' => 50, 'page' => 1]) }}">50</a></li>
                    </ul>
                </div>
            </div>
            <div class="fw-bold text-uppercase" style="font-size: 0.70rem; color: #0F172A; letter-spacing: 0.04em;">
                PAGE {{ $changeRequests->currentPage() }} OF {{ max(1, $changeRequests->lastPage()) }}
            </div>
            <div class="d-flex align-items-center gap-1">
                {{-- First Page << --}}
                <a href="{{ $changeRequests->url(1) }}" class="btn btn-sm p-0 d-flex align-items-center justify-content-center {{ $changeRequests->onFirstPage() ? 'disabled opacity-50' : '' }}"
                   style="width: 24px; height: 24px; border: 1px solid #CBD5E1; border-radius: 4px; background: #FFFFFF; font-size: 0.70rem; color: #334155; text-decoration: none;">
                    <i class="bi bi-chevron-double-left" style="font-size: 0.65rem;"></i>
                </a>
                {{-- Previous Page < --}}
                <a href="{{ $changeRequests->previousPageUrl() }}" class="btn btn-sm p-0 d-flex align-items-center justify-content-center {{ $changeRequests->onFirstPage() ? 'disabled opacity-50' : '' }}"
                   style="width: 24px; height: 24px; border: 1px solid #CBD5E1; border-radius: 4px; background: #FFFFFF; font-size: 0.70rem; color: #334155; text-decoration: none;">
                    <i class="bi bi-chevron-left" style="font-size: 0.65rem;"></i>
                </a>
                {{-- Next Page > --}}
                <a href="{{ $changeRequests->nextPageUrl() }}" class="btn btn-sm p-0 d-flex align-items-center justify-content-center {{ !$changeRequests->hasMorePages() ? 'disabled opacity-50' : '' }}"
                   style="width: 24px; height: 24px; border: 1px solid #CBD5E1; border-radius: 4px; background: #FFFFFF; font-size: 0.70rem; color: #334155; text-decoration: none;">
                    <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
                </a>
                {{-- Last Page >> --}}
                <a href="{{ $changeRequests->url($changeRequests->lastPage()) }}" class="btn btn-sm p-0 d-flex align-items-center justify-content-center {{ !$changeRequests->hasMorePages() ? 'disabled opacity-50' : '' }}"
                   style="width: 24px; height: 24px; border: 1px solid #CBD5E1; border-radius: 4px; background: #FFFFFF; font-size: 0.70rem; color: #334155; text-decoration: none;">
                    <i class="bi bi-chevron-double-right" style="font-size: 0.65rem;"></i>
                </a>
            </div>
        </div>
    </div>
@endif

@endif

</div>
@endsection
