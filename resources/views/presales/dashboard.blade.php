@extends('layouts.app')

@section('title', 'DASHBOARD • Presales Monitoring CR')

@section('content')
<div class="page-shell pb-5">

    {{-- Header: DASHBOARD --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.85rem; font-weight: 800; letter-spacing: -0.01em;">DASHBOARD</h1>
            <p class="text-muted mb-0" style="font-size: 0.88rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda</p>
        </div>
    </div>

    <div style="height: 3px; background: #E0F2FE; border-radius: 999px; margin-bottom: 1.5rem;"></div>

    {{-- 4 Kartu Metrik di Atas Sesuai Desain Figma / PM Head --}}
    <div class="row g-3 mb-4">
        {{-- 1. Total CR --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('presales.change-requests') }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #014DA1; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(1,77,161,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">Total CR</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $figmaTotalCr ?? 0 }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span>Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- 2. Butuh Persetujuan --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('presales.persetujuan') }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #144272; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(20,66,114,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">Butuh Persetujuan</span>
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
                        <span>Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- 3. Development --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('presales.development') }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #205295; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(32,82,149,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">Development</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $figmaDevCr ?? 0 }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span>Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- 4. GO LIVE --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('presales.golive') }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #2C74B3; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(44,116,179,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">GO LIVE</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $figmaGoLiveCr ?? 0 }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span>Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Bagian Bawah: GRAFIK REQUEST CR (Kiri) dan NOTIFIKASI (Kanan) --}}
    <div class="row g-4 mb-4">
        {{-- Card Left: Grafik Request CR --}}
        <div class="col-lg-7 col-12">
            <div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: #0F172A; font-size: 1rem; font-weight: 800;">Grafik Request CR</h5>
                        <p class="text-muted small mb-0" style="font-size: 0.78rem; color: #64748B;">Total permintaan CR: hari ini, kemarin, dan hari-hari sebelumnya</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge px-3 py-2 fw-semibold" style="background: #EFF6FF; color: #0063D7; border-radius: 999px; font-size: 0.76rem;">
                            <i class="bi bi-clock-history me-1"></i> 7 Hari Terakhir
                        </span>
                    </div>
                </div>

                {{-- Summary Badges: Hari Ini & Kemarin --}}
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 px-3 rounded-3" style="background: #F0F9FF; border: 1px solid #BAE6FD;">
                            <div class="text-muted small" style="font-size: 0.72rem; font-weight: 600;">REQUEST HARI INI</div>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="fw-bold text-primary" style="font-size: 1.4rem;">{{ $pmRequestsToday ?? 0 }}</span>
                                <span class="text-muted small" style="font-size: 0.72rem;">CR Baru</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 px-3 rounded-3" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                            <div class="text-muted small" style="font-size: 0.72rem; font-weight: 600;">REQUEST KEMARIN</div>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="fw-bold text-dark" style="font-size: 1.4rem;">{{ $pmRequestsYesterday ?? 0 }}</span>
                                <span class="text-muted small" style="font-size: 0.72rem;">CR Diproses</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SVG Spline Line & Area Chart of Requests --}}
                @php
                    $trendList = $dailyTrend ?? [
                        ['label' => '5 Hari Lalu', 'short_label' => '03/10', 'count' => 1],
                        ['label' => '4 Hari Lalu', 'short_label' => '04/10', 'count' => 3],
                        ['label' => '3 Hari Lalu', 'short_label' => '05/10', 'count' => 2],
                        ['label' => '2 Hari Lalu', 'short_label' => '06/10', 'count' => 5],
                        ['label' => 'Sebelumnya', 'short_label' => '07/10', 'count' => 3],
                        ['label' => 'Kemarin', 'short_label' => 'Kemarin', 'count' => 2],
                        ['label' => 'Hari Ini', 'short_label' => 'Hari Ini', 'count' => 4],
                    ];
                    $maxCount = max(array_merge([6], array_column($trendList, 'count')));
                    $points = [];
                    $svgWidth = 520;
                    $svgHeight = 160;
                    $startX = 50;
                    $endX = 490;
                    $stepX = ($endX - $startX) / (count($trendList) - 1 ?: 1);

                    foreach ($trendList as $idx => $t) {
                        $x = $startX + ($idx * $stepX);
                        $val = $t['count'];
                        $y = 135 - (($val / ($maxCount ?: 1)) * 105);
                        $points[] = ['x' => $x, 'y' => $y, 'count' => $val, 'label' => $t['short_label']];
                    }

                    $pathD = 'M ' . $points[0]['x'] . ' ' . $points[0]['y'];
                    for ($k = 1; $k < count($points); $k++) {
                        $prev = $points[$k - 1];
                        $curr = $points[$k];
                        $cx1 = $prev['x'] + ($stepX / 2);
                        $cy1 = $prev['y'];
                        $cx2 = $prev['x'] + ($stepX / 2);
                        $cy2 = $curr['y'];
                        $pathD .= " C $cx1 $cy1, $cx2 $cy2, {$curr['x']} {$curr['y']}";
                    }
                    $areaD = $pathD . " L {$points[count($points)-1]['x']} 140 L {$points[0]['x']} 140 Z";
                @endphp

                <div class="w-100 position-relative pt-1">
                    <svg viewBox="0 0 530 175" class="w-100" style="overflow: visible; font-family: 'Plus Jakarta Sans', sans-serif;">
                        <defs>
                            <linearGradient id="gradientPresalesRequest" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#0063D7" stop-opacity="0.25" />
                                <stop offset="100%" stop-color="#0063D7" stop-opacity="0.0" />
                            </linearGradient>
                        </defs>

                        {{-- Horizontal Grid Lines --}}
                        <line x1="45" y1="30" x2="500" y2="30" stroke="#F1F5F9" stroke-width="1" />
                        <line x1="45" y1="80" x2="500" y2="80" stroke="#F1F5F9" stroke-width="1" />
                        <line x1="45" y1="135" x2="500" y2="135" stroke="#E2E8F0" stroke-width="1.5" />

                        {{-- Area Fill --}}
                        <path d="{{ $areaD }}" fill="url(#gradientPresalesRequest)" />

                        {{-- Spline Line --}}
                        <path d="{{ $pathD }}" fill="none" stroke="#0063D7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                        {{-- Data Points & Labels --}}
                        @foreach ($points as $p)
                            <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="5" fill="#FFFFFF" stroke="#0063D7" stroke-width="3" />
                            <text x="{{ $p['x'] }}" y="{{ $p['y'] - 10 }}" text-anchor="middle" font-size="11" font-weight="700" fill="#0063D7">{{ $p['count'] }}</text>
                            <text x="{{ $p['x'] }}" y="156" text-anchor="middle" font-size="10.5" font-weight="600" fill="#64748B">{{ $p['label'] }}</text>
                        @endforeach
                    </svg>
                </div>
            </div>
        </div>

        {{-- Card Right: NOTIFIKASI --}}
        <div class="col-lg-5 col-12">
            <div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
                        <h5 class="fw-bold mb-0 text-uppercase" style="color: #0F172A; font-size: 0.95rem; font-weight: 800; letter-spacing: 0.02em;">NOTIFIKASI</h5>
                    </div>
                    <a href="{{ route('presales.notifications') }}" class="text-decoration-none fw-semibold" style="color: #0063D7; font-size: 0.8rem;">Lihat Semua &gt;</a>
                </div>
                <p class="text-muted small mb-0" style="font-size: 0.74rem; line-height: 1.45; color: #64748B;">
                    Pemberitahuan persetujuan penting, batas waktu tinjauan dokumen, dan tindak lanjut status tahapan proyek.
                </p>
                <div style="height: 1px; background: #E2E8F0; margin: 0.85rem 0 1.25rem;"></div>

                <div class="d-flex flex-column gap-3">
                    {{-- Alert 1: Red box (Penolakan / Menunggu Persetujuan > 14 hari) --}}
                    <a href="{{ route('presales.persetujuan') }}" class="text-decoration-none">
                        <div class="p-3" style="background: #FFF1F2; border: 1px solid #FECDD3; border-radius: 14px; transition: transform 0.15s ease;"
                             onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-x-circle-fill mt-1" style="color: #E11D48; font-size: 1.05rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.35;">
                                    <div class="fw-bold" style="color: #9F1239; font-size: 0.82rem;">
                                        Pengajuan Registrasi Akun ditolak oleh PM.
                                    </div>
                                    <div style="color: #E11D48; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                        Alasan: Arsitektur autentikasi belum menyertakan spesifikasi protokol OAuth 2.0.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    {{-- Alert 2: Yellow box (Revisi / Menunggu Estimasi) --}}
                    <a href="{{ route('presales.change-requests', ['status' => 'analisa']) }}" class="text-decoration-none">
                        <div class="p-3" style="background: #FEFCE8; border: 1px solid #FEF08A; border-radius: 14px; transition: transform 0.15s ease;"
                             onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-exclamation-triangle-fill mt-1" style="color: #D97706; font-size: 1.05rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.35;">
                                    <div class="fw-bold" style="color: #854D0E; font-size: 0.82rem;">
                                        Pengajuan CR Anda sedang direvisi oleh PM Head.
                                    </div>
                                    <div style="color: #B45309; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                        Periksa rincian man-hari dan rincian arsitektur sebelum penawaran diajukan.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    {{-- Alert 3: Blue box (Development & Pengerjaan) --}}
                    <a href="{{ route('presales.development') }}" class="text-decoration-none">
                        <div class="p-3" style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 14px; transition: transform 0.15s ease;"
                             onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-info-circle-fill mt-1" style="color: #2563EB; font-size: 1.05rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.35;">
                                    <div class="fw-bold" style="color: #1E40AF; font-size: 0.82rem;">
                                        Pengajuan CR dengan ID #CR-001 telah memasuki tahapan Development.
                                    </div>
                                    <div style="color: #2563EB; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                        Tahapan : 2. Pengembangan Fitur &amp; Integrasi
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>

</div>
@endsection
