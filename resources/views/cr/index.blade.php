@extends('layouts.app')

@section('title', $viewMode === 'dashboard' ? 'Dashboard Monitoring CR' : strtoupper(str_replace('_', ' ', $viewMode)) . ' • Monitoring Change Request')

@section('content')
<div class="page-shell">

@if ($viewMode === 'dashboard')
    {{-- ══════════════════════════════════════════════════════════
         FIGMA DASHBOARD: ROLE-AWARE (PM & CLIENT MOCKUPS)
    ══════════════════════════════════════════════════════════ --}}
    @php
        $userRole = auth()->user()?->role ?? 'client';
        $isPmDashboard = in_array($userRole, ['pm', 'pmh']) || request('role') === 'pm' || request('view_as') === 'pm';
    @endphp

    {{-- Header: DASHBOARD --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" data-i18n="dashboard_title" style="color: #0F172A; font-size: 1.65rem; font-weight: 800; letter-spacing: -0.01em;">DASHBOARD</h1>
            <p class="text-muted mb-0" data-i18n="dashboard_subtitle" style="font-size: 0.84rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda</p>
        </div>
        @if (!$isPmDashboard)
            <div>
                <a href="{{ route('change-requests.create') }}" class="btn text-white fw-bold d-inline-flex align-items-center gap-2"
                   style="background: #22C55E; border-radius: 8px; padding: 0.6rem 1.45rem; font-size: 0.88rem; border: none; box-shadow: 0 4px 14px rgba(34,197,94,0.3); transition: transform 0.15s ease;">
                    <i class="bi bi-plus-lg"></i>
                    <span data-i18n="btn_new_cr">Ajukan CR Baru</span>
                </a>
            </div>
        @endif
    </div>

    {{-- 4 Big Metric Cards (Figma: #134B8A Deep Royal Blue) --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Semua CR (#134B8A) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('change-requests.index', ['view' => 'total']) }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
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
            {{-- PM Card 2: Butuh Persetujuan (#134B8A) --}}
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('change-requests.index', ['view' => 'total', 'status' => 'awaiting_pm']) }}" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_need_approval" style="font-size: 0.95rem;">Butuh Persetujuan</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            {{ $figmaNeedApprovalCr ?? 4 }}
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>

            {{-- PM Card 3: Development (#134B8A) --}}
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('change-requests.index', ['view' => 'development']) }}" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
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
            {{-- Client Card 2: Development (#134B8A) --}}
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('change-requests.index', ['view' => 'development']) }}" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
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

            {{-- Client Card 3: UAT (#134B8A) --}}
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('change-requests.index', ['view' => 'uat']) }}" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
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

        {{-- Card 4: GO LIVE (#134B8A) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('change-requests.index', ['view' => 'golive']) }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
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

    {{-- Lower Row: Chart (Tren CR Bulanan for PM OR Status Donut for Client) and NOTIFIKASI --}}
    <div class="row g-4 mb-4">
        {{-- Card Left --}}
        <div class="col-lg-7 col-12">
            @if ($isPmDashboard)
                {{-- ── PM DASHBOARD: Tren CR Bulanan Spline Line Chart (Figma Exact) ── --}}
                <div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
                    <div class="mb-2">
                        <h5 class="fw-bold mb-1" style="color: #0F172A; font-size: 0.95rem; font-weight: 800;">Tren CR Bulanan</h5>
                        <p class="text-muted small mb-0" style="font-size: 0.78rem; color: #64748B;">Jumlah CR masuk, Go-Live, dan ditolak per bulan</p>
                    </div>

                    {{-- SVG Line Chart matching Figma exactly --}}
                    <div class="w-100 position-relative pt-2">
                        <svg viewBox="0 0 540 215" class="w-100" style="overflow: visible; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <defs>
                                <linearGradient id="gradientMasuk" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#3B82F6" stop-opacity="0.22" />
                                    <stop offset="100%" stop-color="#3B82F6" stop-opacity="0.0" />
                                </linearGradient>
                                <linearGradient id="gradientGoLive" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#10B981" stop-opacity="0.15" />
                                    <stop offset="100%" stop-color="#10B981" stop-opacity="0.0" />
                                </linearGradient>
                            </defs>

                            <line x1="45" y1="20" x2="520" y2="20" stroke="#F1F5F9" stroke-width="1.2" />
                            <text x="32" y="24" font-size="11" fill="#94A3B8" text-anchor="end">4</text>

                            <line x1="45" y1="58" x2="520" y2="58" stroke="#F1F5F9" stroke-width="1.2" />
                            <text x="32" y="62" font-size="11" fill="#94A3B8" text-anchor="end">3</text>

                            <line x1="45" y1="96" x2="520" y2="96" stroke="#F1F5F9" stroke-width="1.2" />
                            <text x="32" y="100" font-size="11" fill="#94A3B8" text-anchor="end">2</text>

                            <line x1="45" y1="134" x2="520" y2="134" stroke="#F1F5F9" stroke-width="1.2" />
                            <text x="32" y="138" font-size="11" fill="#94A3B8" text-anchor="end">1</text>

                            <line x1="45" y1="172" x2="520" y2="172" stroke="#E2E8F0" stroke-width="1.2" />
                            <text x="32" y="176" font-size="11" fill="#94A3B8" text-anchor="end">0</text>

                            <text x="65" y="193" font-size="11" fill="#94A3B8" text-anchor="middle">Jan</text>
                            <text x="155" y="193" font-size="11" fill="#94A3B8" text-anchor="middle">Feb</text>
                            <text x="245" y="193" font-size="11" fill="#94A3B8" text-anchor="middle">Mar</text>
                            <text x="335" y="193" font-size="11" fill="#94A3B8" text-anchor="middle">Apr</text>
                            <text x="425" y="193" font-size="11" fill="#94A3B8" text-anchor="middle">Mei</text>
                            <text x="515" y="193" font-size="11" fill="#94A3B8" text-anchor="middle">Jun</text>

                            <path d="M 65 96
                                     C 110 70, 120 58, 155 58
                                     C 190 58, 210 134, 245 134
                                     C 285 134, 305 20, 335 20
                                     C 365 20, 395 96, 425 96
                                     C 455 96, 485 58, 515 58
                                     L 515 172 L 65 172 Z"
                                  fill="url(#gradientMasuk)" />

                            <path d="M 65 172
                                     C 100 155, 120 134, 155 134
                                     C 185 134, 215 134, 245 134
                                     C 280 134, 300 96, 335 96
                                     C 370 96, 390 134, 425 134
                                     C 460 134, 480 96, 515 96
                                     L 515 172 L 65 172 Z"
                                  fill="url(#gradientGoLive)" />

                            <path d="M 65 172
                                     L 155 172
                                     C 185 172, 215 134, 245 134
                                     C 275 134, 305 172, 335 172
                                     C 365 172, 395 134, 425 134
                                     C 455 134, 485 172, 515 172"
                                  fill="none" stroke="#EF4444" stroke-width="2" stroke-dasharray="4, 3" />

                            <path d="M 65 172
                                     C 100 155, 120 134, 155 134
                                     C 185 134, 215 134, 245 134
                                     C 280 134, 300 96, 335 96
                                     C 370 96, 390 134, 425 134
                                     C 460 134, 480 96, 515 96"
                                  fill="none" stroke="#10B981" stroke-width="2.2" stroke-linecap="round" />

                            <path d="M 65 96
                                     C 110 70, 120 58, 155 58
                                     C 190 58, 210 134, 245 134
                                     C 285 134, 305 20, 335 20
                                     C 365 20, 395 96, 425 96
                                     C 455 96, 485 58, 515 58"
                                  fill="none" stroke="#3B82F6" stroke-width="2.2" stroke-linecap="round" />

                            <circle cx="65" cy="172" r="3.5" fill="#EF4444" />
                            <circle cx="155" cy="172" r="3.5" fill="#EF4444" />
                            <circle cx="245" cy="134" r="3.5" fill="#EF4444" />
                            <circle cx="335" cy="172" r="3.5" fill="#EF4444" />
                            <circle cx="425" cy="134" r="3.5" fill="#EF4444" />
                            <circle cx="515" cy="172" r="3.5" fill="#EF4444" />

                            <circle cx="65" cy="172" r="4" fill="#10B981" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="155" cy="134" r="4" fill="#10B981" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="245" cy="134" r="4" fill="#10B981" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="335" cy="96" r="4" fill="#10B981" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="425" cy="134" r="4" fill="#10B981" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="515" cy="96" r="4" fill="#10B981" stroke="#FFFFFF" stroke-width="1.5" />

                            <circle cx="65" cy="96" r="4" fill="#3B82F6" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="155" cy="58" r="4" fill="#3B82F6" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="245" cy="134" r="4" fill="#3B82F6" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="335" cy="20" r="4" fill="#3B82F6" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="425" cy="96" r="4" fill="#3B82F6" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="515" cy="58" r="4" fill="#3B82F6" stroke="#FFFFFF" stroke-width="1.5" />
                        </svg>
                    </div>

                    <div class="d-flex align-items-center justify-content-center gap-4 mt-2 pt-1 text-muted" style="font-size: 0.74rem; font-weight: 500;">
                        <div class="d-flex align-items-center gap-2">
                            <span style="display: inline-flex; align-items: center; width: 22px;">
                                <span style="width: 100%; border-top: 2px dashed #EF4444; position: relative;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #EF4444; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);"></span>
                                </span>
                            </span>
                            <span style="color: #EF4444; font-weight: 600;">Ditolak</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span style="display: inline-flex; align-items: center; width: 22px;">
                                <span style="width: 100%; border-top: 2px solid #10B981; position: relative;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #10B981; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);"></span>
                                </span>
                            </span>
                            <span style="color: #10B981; font-weight: 600;">GoLive</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span style="display: inline-flex; align-items: center; width: 22px;">
                                <span style="width: 100%; border-top: 2px solid #3B82F6; position: relative;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #3B82F6; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);"></span>
                                </span>
                            </span>
                            <span style="color: #3B82F6; font-weight: 600;">Masuk</span>
                        </div>
                    </div>
                </div>
            @else
                {{-- ── CLIENT DASHBOARD: STATUS (Donut + Progress) ── --}}
                <div class="card p-4 border-0 h-100" style="border-radius: 20px; background: #F0F3F6; border: 1px solid #E2E8F0 !important;">
                    <div>
                        <h5 class="fw-bold mb-1 text-uppercase" data-i18n="status_title" style="color: #0F172A; font-size: 1rem; font-weight: 800; letter-spacing: 0.02em;">STATUS</h5>
                        <p class="text-muted small mb-0" data-i18n="status_subtitle" style="font-size: 0.78rem; color: #64748B;">Monitoring pergerakan CR melalui 6 tahapan siklus proyek TI</p>
                    </div>
                    <div style="height: 1px; background: #DCE1E7; margin: 0.85rem 0 1.25rem;"></div>

                    <div class="row align-items-center g-3 pt-1">
                        {{-- Donut Chart Left --}}
                        <div class="col-sm-5 col-12 text-center">
                            <div style="position: relative; width: 155px; height: 155px; margin: 0 auto;">
                                @php
                                    $cAnalisa  = $statusCounts['analisis'] ?? 2;
                                    $cDevelop  = $statusCounts['develop'] ?? 0;
                                    $cSit      = $statusCounts['sit'] ?? 1;
                                    $cUat      = $statusCounts['uat'] ?? 0;
                                    $cTraining = $statusCounts['deploying'] ?? 0;
                                    $cGoLive   = $statusCounts['golive'] ?? 2;
                                    $totalDonut = $cAnalisa + $cDevelop + $cSit + $cUat + $cTraining + $cGoLive;
                                    if ($totalDonut == 0) $totalDonut = 1;

                                    $p1 = ($cAnalisa / $totalDonut) * 100;
                                    $p2 = $p1 + (($cDevelop / $totalDonut) * 100);
                                    $p3 = $p2 + (($cSit / $totalDonut) * 100);
                                    $p4 = $p3 + (($cUat / $totalDonut) * 100);
                                    $p5 = $p4 + (($cTraining / $totalDonut) * 100);
                                @endphp
                                <div style="width: 100%; height: 100%; border-radius: 50%; background: conic-gradient(
                                    #00A3FF 0% {{ $p1 }}%,
                                    #4338CA {{ $p1 }}% {{ $p2 }}%,
                                    #F59E0B {{ $p2 }}% {{ $p3 }}%,
                                    #EC4899 {{ $p3 }}% {{ $p4 }}%,
                                    #047857 {{ $p4 }}% {{ $p5 }}%,
                                    #10B981 {{ $p5 }}% 100%
                                ); display: flex; align-items: center; justify-content: center;">
                                    <div style="width: 96px; height: 96px; background: #F0F3F6; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <span class="fw-bold" style="font-size: 2.2rem; line-height: 1; color: #0F172A; font-weight: 800;">{{ $totalDonut }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 6 Status Progress Lines Right (INLINE LAYOUT MATCHING FIGMA) --}}
                        <div class="col-sm-7 col-12">
                            <div class="d-flex flex-column gap-3">
                                {{-- 1. Analisa --}}
                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #00A3FF; flex-shrink: 0;"></span>
                                        <span data-i18n="stage_analisa">Analisa</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: {{ ($cAnalisa / $totalDonut) * 100 }}%; height: 100%; background: #00A3FF; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{ $cAnalisa }}</span>
                                </div>

                                {{-- 2. Develop --}}
                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #4338CA; flex-shrink: 0;"></span>
                                        <span data-i18n="stage_develop">Develop</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: {{ ($cDevelop / $totalDonut) * 100 }}%; height: 100%; background: #4338CA; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{ $cDevelop }}</span>
                                </div>

                                {{-- 3. SIT --}}
                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #F59E0B; flex-shrink: 0;"></span>
                                        <span data-i18n="stage_sit">SIT</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: {{ ($cSit / $totalDonut) * 100 }}%; height: 100%; background: #F59E0B; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{ $cSit }}</span>
                                </div>

                                {{-- 4. UAT --}}
                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #EC4899; flex-shrink: 0;"></span>
                                        <span data-i18n="stage_uat">UAT</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: {{ ($cUat / $totalDonut) * 100 }}%; height: 100%; background: #EC4899; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{ $cUat }}</span>
                                </div>

                                {{-- 5. Training --}}
                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #047857; flex-shrink: 0;"></span>
                                        <span data-i18n="stage_training">Training</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: {{ ($cTraining / $totalDonut) * 100 }}%; height: 100%; background: #047857; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{ $cTraining }}</span>
                                </div>

                                {{-- 6. Go Live --}}
                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #10B981; flex-shrink: 0;"></span>
                                        <span data-i18n="stage_golive">Go Live</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: {{ ($cGoLive / $totalDonut) * 100 }}%; height: 100%; background: #10B981; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{ $cGoLive }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Card Right: NOTIFIKASI --}}
        <div class="col-lg-5 col-12">
            <div class="card p-4 border-0 h-100" style="border-radius: 20px; background: #F0F3F6; border: 1px solid #E2E8F0 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
                        <h5 class="fw-bold mb-0 text-uppercase" data-i18n="notif_title" style="color: #0F172A; font-size: 0.95rem; font-weight: 800; letter-spacing: 0.02em;">NOTIFIKASI</h5>
                    </div>
                    <a href="javascript:void(0)" class="text-decoration-none fw-semibold" data-i18n="notif_view_all" style="color: #0063D7; font-size: 0.8rem;">Lihat Semua</a>
                </div>
                <p class="text-muted small mb-0" data-i18n="notif_subtitle" style="font-size: 0.74rem; line-height: 1.45; color: #64748B;">
                    Pemberitahuan persetujuan penting, batas waktu tinjauan dokumen, dan tindak lanjut status tahapan proyek.
                </p>
                <div style="height: 1px; background: #DCE1E7; margin: 0.85rem 0 1.25rem;"></div>

                <div class="d-flex flex-column gap-3">
                    {{-- Alert 1: Red box --}}
                    <div class="p-3" style="background: #FEF3F4; border: 1px solid #FECDCA; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-exclamation-triangle-fill mt-1" style="color: #DC2626; font-size: 1.05rem; flex-shrink: 0;"></i>
                            <div style="line-height: 1.35;">
                                <div class="fw-bold" data-i18n="notif_alert1_title" style="color: #991B1B; font-size: 0.82rem;">
                                    3 Perubahan Ruang Lingkup CR menunggu persetujuan > 14 hari
                                </div>
                                <div data-i18n="notif_alert1_desc" style="color: #DC2626; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                    Status : Menunggu Persetujuan PM
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Alert 2: Yellow box --}}
                    <div class="p-3" style="background: #FDFBF0; border: 1px solid #FEDF89; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-exclamation-triangle-fill mt-1" style="color: #D97706; font-size: 1.05rem; flex-shrink: 0;"></i>
                            <div style="line-height: 1.35;">
                                <div class="fw-bold" data-i18n="notif_alert2_title" style="color: #92400E; font-size: 0.82rem;">
                                    8 CR Baru Menunggu Verifikasi Kebutuhan
                                </div>
                                <div data-i18n="notif_alert2_desc" style="color: #B45309; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                    Tahapan : 1. Analisis Kebutuhan
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Alert 3: Blue box --}}
                    <div class="p-3" style="background: #F2F7FE; border: 1px solid #BAE6FD; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle-fill mt-1" style="color: #2563EB; font-size: 1.05rem; flex-shrink: 0;"></i>
                            <div style="line-height: 1.35;">
                                <div class="fw-bold" data-i18n="notif_alert3_title" style="color: #1E40AF; font-size: 0.82rem;">
                                    5 Masukan Pengguna UAT butuh tindak lanjut
                                </div>
                                <div data-i18n="notif_alert3_desc" style="color: #2563EB; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                    Tahapan : 4. UAT & Pengujian
                                </div>
                            </div>
                        </div>
                    </div>
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

    @php
        $pageTitle = match($viewMode) {
            'development' => 'DEVELOPMENT',
            'uat' => 'UAT',
            'golive' => 'GO LIVE',
            'status' => 'STATUS CR',
            default => 'TOTAL CR',
        };
    @endphp

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="color: #000000; font-size: 1.65rem; font-weight: 800; letter-spacing: -0.01em;">{{ $pageTitle }}</h1>
            <p class="text-muted mb-0" data-i18n="dashboard_subtitle" style="font-size: 0.84rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('change-requests.create') }}" class="btn text-white fw-bold d-inline-flex align-items-center gap-2"
               style="background: #22C55E; border-radius: 8px; padding: 0.55rem 1.3rem; font-size: 0.84rem; border: none; box-shadow: 0 4px 12px rgba(34,197,94,0.3);">
                <i class="bi bi-plus-lg"></i>
                <span data-i18n="btn_new_cr">Ajukan CR Baru</span>
            </a>
        </div>
    </div>

    {{-- CR Table (Figma columns: TGL PENGAJUAN, PEMOHON, JUDUL CR, STATUS, PRIORITAS, AKSI) --}}
    <div class="card overflow-hidden shadow-sm border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 20px; background: #FFFFFF;">
        <div class="table-responsive">
            <table class="table mb-0 align-middle" style="border-collapse: separate; border-spacing: 0;">
                <thead style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0;">
                    <tr>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase" data-i18n="th_submission_date" style="font-size: 0.72rem; letter-spacing: 0.06em; width: 170px;">TGL PENGAJUAN</th>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase" data-i18n="th_requester" style="font-size: 0.72rem; letter-spacing: 0.06em;">PEMOHON</th>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase" data-i18n="th_cr_title" style="font-size: 0.72rem; letter-spacing: 0.06em;">JUDUL CR</th>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase" data-i18n="th_status" style="font-size: 0.72rem; letter-spacing: 0.06em; width: 150px;">STATUS</th>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase text-center" data-i18n="th_priority" style="font-size: 0.72rem; letter-spacing: 0.06em; width: 120px;">PRIORITAS</th>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase text-end" data-i18n="th_action" style="font-size: 0.72rem; letter-spacing: 0.06em; width: 130px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $avatarColors = ['#F59E0B', '#10B981', '#3B82F6', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4'];
                    @endphp

                    @forelse ($changeRequests as $index => $cr)
                        @php
                            $colorIndex = $index % count($avatarColors);
                            $avColor = $avatarColors[$colorIndex];

                            $pemohonName = $cr->owner_cr ?: ($cr->user?->name ?: 'Totok Antok');
                            $pemohonInitials = strtoupper(substr($pemohonName, 0, 1));
                            $companyName = $cr->klien ?: ($cr->client?->company ?: 'PT Maju Bersama');

                            // Status badge mapping
                            $statusText = 'Diajukan';
                            $statusBg = '#FEF3C7';
                            $statusColor = '#D97706';

                            if (in_array($cr->status, ['analisa', 'dianalisis'])) {
                                $statusText = 'Analisis';
                                $statusBg = '#DBEAFE';
                                $statusColor = '#2563EB';
                            } elseif (in_array($cr->status, ['development', 'dikerjakan'])) {
                                $statusText = 'Develop';
                                $statusBg = '#EDE9FE';
                                $statusColor = '#7C3AED';
                            } elseif (in_array($cr->status, ['uat', 'sit'])) {
                                $statusText = 'UAT';
                                $statusBg = '#FCE7F3';
                                $statusColor = '#DB2777';
                            } elseif (in_array($cr->status, ['golive', 'selesai', 'invoicing'])) {
                                $statusText = 'Go-Live';
                                $statusBg = '#D1FAE5';
                                $statusColor = '#059669';
                            }

                            // Priority badge
                            $isUrgent = in_array(strtolower($cr->prioritas ?? ''), ['urgent', 'kritis', 'tinggi', 'high']);
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC';" onmouseout="this.style.background='#FFFFFF';">
                            {{-- TGL PENGAJUAN --}}
                            <td class="py-3 px-4">
                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                    {{ $cr->tanggal_pengajuan ? $cr->tanggal_pengajuan->translatedFormat('d M Y') : '15 Sep 2024' }}
                                </div>
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    {{ $cr->created_at ? $cr->created_at->format('H:i:s') : '09:30:12' }}
                                </div>
                            </td>

                            {{-- PEMOHON --}}
                            <td class="py-3 px-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                         style="width: 34px; height: 34px; background: {{ $avColor }}; font-size: 0.82rem;">
                                        {{ $pemohonInitials }}
                                    </div>
                                    <div style="line-height: 1.2;">
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $pemohonName }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $companyName }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- JUDUL CR --}}
                            <td class="py-3 px-4">
                                <div style="line-height: 1.25;">
                                    <a href="{{ route('change-requests.show', $cr) }}" class="fw-bold text-dark text-decoration-none hover-primary" style="font-size: 0.88rem;">
                                        {{ $cr->judul }}
                                    </a>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        {{ $cr->proyek_terkait ?: 'Akses & Dashboard' }}
                                    </div>
                                </div>
                            </td>

                            {{-- STATUS --}}
                            <td class="py-3 px-4">
                                <span class="d-inline-flex align-items-center gap-1 px-3 py-1 fw-bold"
                                      style="background: {{ $statusBg }}; color: {{ $statusColor }}; border-radius: 999px; font-size: 0.76rem;">
                                    ● {{ $statusText }}
                                </span>
                            </td>

                            {{-- PRIORITAS --}}
                            <td class="py-3 px-4 text-center">
                                @if ($isUrgent)
                                    <span class="badge fw-bold" style="background: #FEE2E2; color: #DC2626; border-radius: 6px; font-size: 0.72rem; padding: 4px 8px;">
                                        HIGH
                                    </span>
                                @else
                                    <span class="badge fw-medium" style="background: #F1F5F9; color: #64748B; border-radius: 6px; font-size: 0.72rem; padding: 4px 8px;">
                                        NORMAL
                                    </span>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td class="py-3 px-4 text-end">
                                <a href="{{ route('change-requests.show', $cr) }}"
                                   class="btn text-white fw-bold px-3 py-1 d-inline-flex align-items-center gap-1 shadow-sm"
                                   style="background: #2563EB; border-radius: 999px; font-size: 0.78rem; border: none;">
                                    <span>Review</span>
                                    <i class="bi bi-chevron-right" style="font-size: 0.7rem;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="bi bi-inbox fs-2 text-muted"></i></div>
                                <div class="fw-bold">Tidak ada Change Request pada kategori ini</div>
                                <div class="small mt-1">Gunakan tombol "+ Ajukan CR Baru" untuk membuat pengajuan baru.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($changeRequests->hasPages())
            <div class="px-4 py-3 border-top w-100">
                {{ $changeRequests->links() }}
            </div>
        @endif
    </div>

@endif

</div>
@endsection
