@extends('layouts.app')

@section('title', 'Dashboard • PM Head Executive Board')

@section('content')
<div class="page-shell pb-5">

    @php
        $roleMetrics = $roleMetrics ?? [
            'total' => 0,
            'pending_pmh' => 0,
            'pending_golive' => 0,
            'approved' => 0,
            'rejected' => 0,
            'in_dev' => 0,
        ];
        $latestReviews = $latestReviews ?? collect();
        $cGoLive = $statusCounts['golive'] ?? 0;
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
                    <a href="{{ route('notifications.index') }}" class="text-decoration-none fw-semibold" style="color: #0063D7; font-size: 0.8rem;">Lihat Semua</a>
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

        {{-- Card Left: Chart / Grafik --}}
        <div class="col-lg-7 col-12 order-lg-1">
            @include('dashboard.partials.status-donut-card', [
                'cardBg' => '#FFFFFF',
                'cardBorder' => '1px solid #E2E8F0 !important',
                'dividerColor' => '#DCE1E7',
                'trainingLabel' => 'Pelatihan',
            ])
        </div>
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
                                <div class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $cr->judul ?? $cr->title ?? 'Change Request' }}</div>
                                <div class="text-muted small font-monospace">{{ $cr->kode_cr ?? $cr->cr_number ?? ('#'.$cr->id) }}</div>
                            </td>
                            <td class="py-3 px-3 fw-semibold text-dark small">
                                {{ $cr->client?->company ?? ($cr->klien ?: '-') }}
                            </td>
                            <td class="py-3 px-3 small text-secondary">
                                {{ $cr->nama_pm ?? $cr->user?->name ?? 'PM Internal' }}
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
