@extends('layouts.app')

@section('title', 'Dashboard • PM Head Executive Board')

@section('content')
<div class="page-shell pb-5">

    @php
        $cAnalisa = \App\Models\ChangeRequest::whereIn('status', ['analisa', 'dianalisis', 'awaiting_pm', 'diajukan', 'awaiting_pmh'])->count();
        $cDev = \App\Models\ChangeRequest::whereIn('status', ['development', 'dikerjakan'])->count();
        $cSit = \App\Models\ChangeRequest::whereIn('status', ['sit'])->count();
        $cUat = \App\Models\ChangeRequest::whereIn('status', ['uat'])->count();
        $cTrain = \App\Models\ChangeRequest::whereIn('status', ['training'])->count();
        $cGoLive = \App\Models\ChangeRequest::whereIn('status', ['golive', 'selesai', 'invoicing'])->count();
        $totalDonut = $cAnalisa + $cDev + $cSit + $cUat + $cTrain + $cGoLive;
        $totalDonutCalc = $totalDonut == 0 ? 1 : $totalDonut;

        $p1 = ($cAnalisa / $totalDonutCalc) * 100;
        $p2 = $p1 + (($cDev / $totalDonutCalc) * 100);
        $p3 = $p2 + (($cSit / $totalDonutCalc) * 100);
        $p4 = $p3 + (($cUat / $totalDonutCalc) * 100);
        $p5 = $p4 + (($cTrain / $totalDonutCalc) * 100);
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.65rem; font-weight: 800; letter-spacing: -0.01em;">DASHBOARD</h1>
            <p class="text-muted mb-0" style="font-size: 0.84rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda (PM Head)</p>
        </div>
    </div>

    <div style="height: 3px; background: #E0F2FE; border-radius: 999px; margin-bottom: 1.5rem;"></div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('pmh.change-requests') }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">Semua CR</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $roleMetrics['total'] ?? 0 }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span>Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('pmh.development') }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">Development</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $roleMetrics['in_dev'] ?? 0 }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span>Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('pmh.golive') }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">UAT</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $roleMetrics['pending_golive'] ?? 0 }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span>Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('pmh.golive') }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">GO LIVE</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $cGoLive }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span>Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Card Right: NOTIFIKASI --}}
        <div class="col-lg-5 col-12 order-lg-2">
            <div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
                        <h5 class="fw-bold mb-0 text-uppercase" style="color: #0F172A; font-size: 0.95rem; font-weight: 800; letter-spacing: 0.02em;">NOTIFIKASI</h5>
                    </div>
                    <a href="{{ route('pmh.notifications') }}" class="text-decoration-none fw-semibold" style="color: #0063D7; font-size: 0.8rem;">Lihat Semua</a>
                </div>
                <p class="text-muted small mb-0" style="font-size: 0.74rem; line-height: 1.45; color: #64748B;">
                    Pemberitahuan persetujuan penting, batas waktu tinjauan dokumen, dan tindak lanjut status tahapan proyek.
                </p>
                <div style="height: 1px; background: #E2E8F0; margin: 0.85rem 0 1.25rem;"></div>

                <div class="d-flex flex-column gap-3">
                    @php
                        $notifPmhCount = $roleMetrics['pending_pmh'] ?? 0;
                        $notifGoliveCount = $roleMetrics['pending_golive'] ?? 0;
                        $hasPmhNotifs = ($notifPmhCount > 0 || $notifGoliveCount > 0);
                    @endphp

                    @if ($hasPmhNotifs)
                        @if ($notifPmhCount > 0)
                            <a href="{{ route('pmh.persetujuan') }}" class="text-decoration-none">
                                <div class="p-3" style="background: #FFF1F2; border: 1px solid #FECDD3; border-radius: 14px; transition: transform 0.15s ease;"
                                     onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-exclamation-triangle-fill mt-1" style="color: #E11D48; font-size: 1.05rem; flex-shrink: 0;"></i>
                                        <div style="line-height: 1.35;">
                                            <div class="fw-bold" style="color: #9F1239; font-size: 0.82rem;">
                                                {{ $notifPmhCount }} Perubahan Ruang Lingkup CR menunggu persetujuan
                                            </div>
                                            <div style="color: #E11D48; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                                Status : Menunggu Persetujuan PM Head
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('pmh.change-requests', ['status' => 'analisa']) }}" class="text-decoration-none">
                                <div class="p-3" style="background: #FEFCE8; border: 1px solid #FEF08A; border-radius: 14px; transition: transform 0.15s ease;"
                                     onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-exclamation-triangle-fill mt-1" style="color: #D97706; font-size: 1.05rem; flex-shrink: 0;"></i>
                                        <div style="line-height: 1.35;">
                                            <div class="fw-bold" style="color: #854D0E; font-size: 0.82rem;">
                                                {{ $notifPmhCount }} CR Baru Menunggu Verifikasi Kebutuhan
                                            </div>
                                            <div style="color: #B45309; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                                Tahapan : 1. Analisis Kebutuhan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endif

                        @if ($notifGoliveCount > 0)
                            <a href="{{ route('pmh.development') }}" class="text-decoration-none">
                                <div class="p-3" style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 14px; transition: transform 0.15s ease;"
                                     onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-info-circle-fill mt-1" style="color: #2563EB; font-size: 1.05rem; flex-shrink: 0;"></i>
                                        <div style="line-height: 1.35;">
                                            <div class="fw-bold" style="color: #1E40AF; font-size: 0.82rem;">
                                                {{ $notifGoliveCount }} Masukan Pengguna UAT butuh tindak lanjut
                                            </div>
                                            <div style="color: #2563EB; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                                Tahapan : 4. UAT &amp; Pengujian
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
                            <div class="small text-muted mt-1" style="font-size: 0.76rem;">Semua antrean persetujuan dan aktivitas telah diproses.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card Left: Tren CR Bulanan (Diagram Batang) & Status Siklus --}}
        <div class="col-lg-7 col-12 order-lg-1">
            <div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: #EEF1F6; border: 1px solid #E2E8F0 !important;">
                {{-- Header with Tab switcher --}}
                <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <h5 class="fw-bold mb-0" id="pmhChartTitle" style="color: #0F172A; font-size: 0.98rem; font-weight: 800;">Tren CR Bulanan (Diagram Batang)</h5>
                            <span class="badge text-white d-inline-flex align-items-center gap-1.5" style="background: #0063D7; font-size: 0.72rem; padding: 4px 10px; border-radius: 9999px; font-weight: 700; box-shadow: 0 2px 6px rgba(0,99,215,0.25);">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #4ADE80; display: inline-block;"></span>
                                <span>Aktif: {{ $currentMonthLabel ?? 'Bulan Ini' }}</span>
                            </span>
                        </div>
                        <p class="text-muted small mb-0" id="pmhChartSubtitle" style="font-size: 0.78rem; color: #64748B;">
                            Jumlah CR Masuk, Keluar, dan Dibatalkan ({{ $trendPeriodLabel ?? '6 Bulan Terakhir' }})
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2 ms-auto">
                        <ul class="nav nav-pills" id="pmhChartTabs" style="background: #E2E8F0; padding: 3px; border-radius: 999px;">
                            <li class="nav-item">
                                <button type="button" class="nav-link active py-1 px-3 fw-bold" id="tabPmhTrendBtn" onclick="switchPmhChart('trend')" style="font-size: 0.74rem; border-radius: 999px;">
                                    Tren Bulanan
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link py-1 px-3 fw-semibold text-secondary" id="tabPmhStatusBtn" onclick="switchPmhChart('status')" style="font-size: 0.74rem; border-radius: 999px;">
                                    Status Siklus
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- VIEW 1: Tren Bulanan (Diagram Batang) --}}
                <div id="pmhTrendView">
                    <div class="d-flex align-items-center justify-content-end gap-3 mt-1 mb-2" style="font-size: 0.75rem; font-weight: 600;">
                        <div class="d-flex align-items-center gap-1.5">
                            <span style="width: 11px; height: 11px; background: #3B7CE7; border-radius: 2px; display: inline-block;"></span>
                            <span style="color: #334155;">CR Masuk</span>
                        </div>
                        <div class="d-flex align-items-center gap-1.5">
                            <span style="width: 11px; height: 11px; background: #31A773; border-radius: 2px; display: inline-block;"></span>
                            <span style="color: #334155;">Keluar</span>
                        </div>
                        <div class="d-flex align-items-center gap-1.5">
                            <span style="width: 11px; height: 11px; background: #EF4444; border-radius: 2px; display: inline-block;"></span>
                            <span style="color: #334155;">Dibatalkan</span>
                        </div>
                    </div>

                    @php
                        $trendData = $pmMonthlyTrend ?? [];
                        $allVals = [5];
                        foreach ($trendData as $td) {
                            $allVals[] = (int)($td['masuk'] ?? 0);
                            $allVals[] = (int)($td['keluar'] ?? 0);
                            $allVals[] = (int)($td['batal'] ?? 0);
                        }
                        $rawMax = max($allVals);
                        $maxVal = max(5, (int)(ceil($rawMax / 5) * 5));
                        $xCenters = [96, 181, 266, 351, 436, 511];

                        $gridSteps = [
                            ['val' => $maxVal, 'y' => 35],
                            ['val' => (int)round($maxVal * 0.75), 'y' => 70],
                            ['val' => (int)round($maxVal * 0.5), 'y' => 105],
                            ['val' => (int)round($maxVal * 0.25), 'y' => 140],
                            ['val' => 0, 'y' => 175],
                        ];
                    @endphp

                    <div class="w-100 position-relative pt-2">
                        <svg viewBox="0 0 540 230" class="w-100" style="overflow: visible; font-family: 'Plus Jakarta Sans', sans-serif;">
                            @foreach($gridSteps as $gs)
                                @if($gs['val'] == 0)
                                    <line x1="45" y1="{{ $gs['y'] }}" x2="525" y2="{{ $gs['y'] }}" stroke="#CBD5E1" stroke-width="1.2" />
                                @else
                                    <line x1="45" y1="{{ $gs['y'] }}" x2="525" y2="{{ $gs['y'] }}" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                                @endif
                                <text x="34" y="{{ $gs['y'] + 4 }}" font-size="11" fill="#94A3B8" font-weight="600" text-anchor="end">{{ $gs['val'] }}</text>
                            @endforeach

                            @foreach($trendData as $idx => $m)
                                @php
                                    $cx = $xCenters[$idx] ?? (96 + $idx * 85);
                                    $vMasuk = (int)($m['masuk'] ?? 0);
                                    $vKeluar = (int)($m['keluar'] ?? 0);
                                    $vBatal = (int)($m['batal'] ?? 0);
                                    $isCurrent = !empty($m['is_current']);

                                    $hMasuk = $vMasuk > 0 ? max(4, min(140, ($vMasuk / $maxVal) * 140)) : 2;
                                    $yMasuk = $vMasuk > 0 ? (175 - $hMasuk) : 173;
                                    $tYMasuk = $vMasuk > 0 ? ($yMasuk - 6) : 168;

                                    $hKeluar = $vKeluar > 0 ? max(4, min(140, ($vKeluar / $maxVal) * 140)) : 2;
                                    $yKeluar = $vKeluar > 0 ? (175 - $hKeluar) : 173;
                                    $tYKeluar = $vKeluar > 0 ? ($yKeluar - 6) : 168;

                                    $hBatal = $vBatal > 0 ? max(4, min(140, ($vBatal / $maxVal) * 140)) : 2;
                                    $yBatal = $vBatal > 0 ? (175 - $hBatal) : 173;
                                    $tYBatal = $vBatal > 0 ? ($yBatal - 6) : 168;
                                @endphp

                                @if ($isCurrent)
                                    {{-- Highlight Column for Current Active Month --}}
                                    <rect x="{{ $cx - 30 }}" y="22" width="60" height="190" rx="8" fill="#0063D7" fill-opacity="0.08" stroke="#0063D7" stroke-width="1.2" stroke-dasharray="3, 2" />
                                    <rect x="{{ $cx - 18 }}" y="7" width="36" height="15" rx="7.5" fill="#0063D7" />
                                    <text x="{{ $cx }}" y="17.5" font-size="8" font-weight="700" fill="#FFFFFF" text-anchor="middle">Aktif</text>
                                @endif

                                <g class="chart-month-group" style="cursor: pointer;">
                                    <text x="{{ $cx - 16 }}" y="{{ $tYMasuk }}" font-size="10.5" font-weight="700" fill="#3B7CE7" text-anchor="middle">{{ $vMasuk }}</text>
                                    <rect x="{{ $cx - 23 }}" y="{{ $yMasuk }}" width="14" height="{{ $hMasuk }}" rx="2" fill="#3B7CE7" style="transition: opacity 0.15s ease;" onmouseover="this.style.opacity='0.85';" onmouseout="this.style.opacity='1';">
                                        <title>{{ $m['month'] }}: {{ $vMasuk }} CR Masuk</title>
                                    </rect>

                                    <text x="{{ $cx }}" y="{{ $tYKeluar }}" font-size="{{ $vKeluar > 0 ? 10 : 9 }}" font-weight="600" fill="#31A773" text-anchor="middle">{{ $vKeluar }}</text>
                                    <rect x="{{ $cx - 7 }}" y="{{ $yKeluar }}" width="14" height="{{ $hKeluar }}" rx="{{ $vKeluar > 0 ? 2 : 1 }}" fill="#31A773" style="transition: opacity 0.15s ease;" onmouseover="this.style.opacity='0.85';" onmouseout="this.style.opacity='1';">
                                        <title>{{ $m['month'] }}: {{ $vKeluar }} CR Keluar / Selesai</title>
                                    </rect>

                                    <text x="{{ $cx + 16 }}" y="{{ $tYBatal }}" font-size="{{ $vBatal > 0 ? 10 : 9 }}" font-weight="600" fill="#EF4444" text-anchor="middle">{{ $vBatal }}</text>
                                    <rect x="{{ $cx + 9 }}" y="{{ $yBatal }}" width="14" height="{{ $hBatal }}" rx="{{ $vBatal > 0 ? 2 : 1 }}" fill="#EF4444" style="transition: opacity 0.15s ease;" onmouseover="this.style.opacity='0.85';" onmouseout="this.style.opacity='1';">
                                        <title>{{ $m['month'] }}: {{ $vBatal }} CR Dibatalkan</title>
                                    </rect>

                                    @if ($isCurrent)
                                        <text x="{{ $cx }}" y="196" font-size="12" font-weight="800" fill="#0063D7" text-anchor="middle">{{ $m['month'] }}</text>
                                        <circle cx="{{ $cx }}" cy="204" r="2.5" fill="#0063D7" />
                                    @else
                                        <text x="{{ $cx }}" y="196" font-size="11.5" font-weight="600" fill="#64748B" text-anchor="middle">{{ $m['month'] }}</text>
                                    @endif
                                </g>
                            @endforeach
                        </svg>
                    </div>

                    <div class="mt-2 py-1.5 px-3 rounded-2 text-end" style="background: #E3E6ED;">
                        <span style="font-size: 0.72rem; color: #64748B; font-weight: 500;">Sumber Data: Laporan Internal CR</span>
                    </div>
                </div>

                {{-- VIEW 2: Status Siklus (Donut + 6 Progress Lines) --}}
                <div id="pmhStatusView" style="display: none;">
                    <div style="height: 1px; background: #DCE1E7; margin: 0.85rem 0 1.25rem;"></div>

                    <div class="row align-items-center g-3 pt-1">
                        {{-- Donut Chart Left --}}
                        <div class="col-sm-5 col-12 text-center">
                            <div style="position: relative; width: 155px; height: 155px; margin: 0 auto;">
                                <div style="width: 100%; height: 100%; border-radius: 50%; background: {{ $totalDonut == 0 ? '#E2E8F0' : "conic-gradient(
                                    #00A3FF 0% {$p1}%,
                                    #4338CA {$p1}% {$p2}%,
                                    #F59E0B {$p2}% {$p3}%,
                                    #EC4899 {$p3}% {$p4}%,
                                    #047857 {$p4}% {$p5}%,
                                    #10B981 {$p5}% 100%
                                )" }}; display: flex; align-items: center; justify-content: center;">
                                    <div style="width: 96px; height: 96px; background: #FFFFFF; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <span class="fw-bold" style="font-size: 2.2rem; line-height: 1; color: #0F172A; font-weight: 800;">{{ $totalDonut }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 6 Status Progress Lines Right --}}
                        <div class="col-sm-7 col-12">
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #00A3FF; flex-shrink: 0;"></span>
                                        <span>Analisa</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: {{ ($cAnalisa / $totalDonutCalc) * 100 }}%; height: 100%; background: #00A3FF; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{ $cAnalisa }}</span>
                                </div>

                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #4338CA; flex-shrink: 0;"></span>
                                        <span>Develop</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: {{ ($cDev / $totalDonutCalc) * 100 }}%; height: 100%; background: #4338CA; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{ $cDev }}</span>
                                </div>

                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #F59E0B; flex-shrink: 0;"></span>
                                        <span>SIT</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: {{ ($cSit / $totalDonutCalc) * 100 }}%; height: 100%; background: #F59E0B; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{ $cSit }}</span>
                                </div>

                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #EC4899; flex-shrink: 0;"></span>
                                        <span>UAT</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: {{ ($cUat / $totalDonutCalc) * 100 }}%; height: 100%; background: #EC4899; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{ $cUat }}</span>
                                </div>

                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #047857; flex-shrink: 0;"></span>
                                        <span>Pelatihan</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: {{ ($cTrain / $totalDonutCalc) * 100 }}%; height: 100%; background: #047857; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{ $cTrain }}</span>
                                </div>

                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #10B981; flex-shrink: 0;"></span>
                                        <span>Go Live</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: {{ ($cGoLive / $totalDonutCalc) * 100 }}%; height: 100%; background: #10B981; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{ $cGoLive }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function switchPmhChart(mode) {
                const trendView = document.getElementById('pmhTrendView');
                const statusView = document.getElementById('pmhStatusView');
                const trendBtn = document.getElementById('tabPmhTrendBtn');
                const statusBtn = document.getElementById('tabPmhStatusBtn');
                const title = document.getElementById('pmhChartTitle');
                const sub = document.getElementById('pmhChartSubtitle');

                if (mode === 'trend') {
                    if (trendView) trendView.style.display = 'block';
                    if (statusView) statusView.style.display = 'none';
                    if (trendBtn) { trendBtn.classList.add('active', 'fw-bold'); trendBtn.classList.remove('text-secondary'); }
                    if (statusBtn) { statusBtn.classList.remove('active', 'fw-bold'); statusBtn.classList.add('text-secondary'); }
                    if (title) title.innerText = 'Tren CR Bulanan (Diagram Batang)';
                    if (sub) sub.innerText = 'Jumlah CR Masuk, Keluar, dan Dibatalkan ({{ $trendPeriodLabel ?? "6 Bulan Terakhir" }})';
                } else {
                    if (trendView) trendView.style.display = 'none';
                    if (statusView) statusView.style.display = 'block';
                    if (statusBtn) { statusBtn.classList.add('active', 'fw-bold'); statusBtn.classList.remove('text-secondary'); }
                    if (trendBtn) { trendBtn.classList.remove('active', 'fw-bold'); trendBtn.classList.add('text-secondary'); }
                    if (title) title.innerText = 'STATUS SIKLUS PROYEK';
                    if (sub) sub.innerText = 'Monitoring pergerakan CR melalui 6 tahapan siklus proyek TI';
                }
            }
        </script>
    </div>

    <div class="card p-4 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 18px; background: #FFFFFF;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Antrean CR Perlu Keputusan PM Head</h5>
                <p class="text-muted small mb-0" style="font-size: 0.8rem;">Daftar CR yang telah diverifikasi PM dan menunggu validasi kelayakan atau go-live.</p>
            </div>
            <a href="{{ route('pmh.persetujuan') }}" class="btn btn-sm btn-outline-primary fw-semibold" style="border-radius: 8px;">
                Lihat Semua Antrean <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead style="background: #F8FAFC;">
                    <tr>
                        <th class="py-2 px-3 small text-muted">ID &amp; JUDUL CR</th>
                        <th class="py-2 px-3 small text-muted">PEMOHON</th>
                        <th class="py-2 px-3 small text-muted">NAMA PM</th>
                        <th class="py-2 px-3 small text-muted text-center">STATUS</th>
                        <th class="py-2 px-3 small text-muted text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestReviews as $cr)
                        <tr>
                            <td class="py-3 px-3">
                                <div class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $cr->judul }}</div>
                                <div class="text-muted small font-monospace">{{ $cr->kode_cr }}</div>
                            </td>
                            <td class="py-3 px-3 fw-semibold text-dark small">
                                {{ $cr->client?->company ?? ($cr->klien ?: '-') }}
                            </td>
                            <td class="py-3 px-3 small text-secondary">
                                {{ $cr->nama_pm }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="badge bg-warning-subtle text-warning fw-semibold px-2 py-1" style="border-radius: 999px;">
                                    {{ ucfirst(str_replace('_', ' ', $cr->status)) }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <a href="{{ route('pmh.review', $cr) }}" class="btn btn-primary btn-sm px-3 fw-bold" style="border-radius: 6px; font-size: 0.78rem;">
                                    Review &gt;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-muted small">
                                Tidak ada antrean persetujuan yang menunggu saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
