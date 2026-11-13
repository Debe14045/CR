@php
    $notifPending = (int) ($notifPendingCount ?? 0);
    $notifNeedVerif = (int) ($notifNeedVerifCount ?? 0);
    $notifDev = (int) ($notifDevCount ?? 0);
    $hasNotifs = ($notifPending > 0 || $notifNeedVerif > 0 || $notifDev > 0);
@endphp

<div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: #FFFFFF; border: 1.5px solid #E2E8F0 !important;">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <div class="d-flex align-items-center gap-2">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
            <h5 class="fw-bold mb-0 text-uppercase" data-i18n="notif_title" style="color: #0F172A; font-size: 0.95rem; font-weight: 800; letter-spacing: 0.02em;">NOTIFIKASI</h5>
        </div>
        <a href="{{ route('notifications.index') }}" class="text-decoration-none fw-semibold" style="color: #0066cc; font-size: 0.82rem;">Lihat Semua</a>
    </div>
    <p class="text-muted small mb-0" style="font-size: 0.74rem; line-height: 1.45; color: #64748B;">
        Pemberitahuan persetujuan penting, batas waktu tinjauan dokumen, dan tindak lanjut status tahapan proyek.
    </p>
    <div style="height: 1px; background: #E2E8F0; margin: 0.85rem 0 1.25rem;"></div>

    <div class="d-flex flex-column gap-3">
        @if ($hasNotifs)
            @if ($notifPending > 0)
                <a href="{{ route('change-requests.index', ['view' => 'total', 'status' => 'diajukan']) }}" class="text-decoration-none">
                    <div class="p-3" style="background: #FFF1F2; border: 1px solid #FECDD3; border-radius: 14px; transition: transform 0.15s ease;"
                         onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-exclamation-triangle-fill mt-1" style="color: #E11D48; font-size: 1.05rem; flex-shrink: 0;"></i>
                            <div style="line-height: 1.35;">
                                <div class="fw-bold" style="color: #9F1239; font-size: 0.82rem;">
                                    {{ $notifPending }} Perubahan Ruang Lingkup CR menunggu persetujuan
                                </div>
                                <div style="color: #E11D48; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                    Status : Menunggu Persetujuan PM
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endif

            @if ($notifNeedVerif > 0)
                <a href="{{ route('change-requests.index', ['status' => 'analisa']) }}" class="text-decoration-none">
                    <div class="p-3" style="background: #FEFCE8; border: 1px solid #FEF08A; border-radius: 14px; transition: transform 0.15s ease;"
                         onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-exclamation-triangle-fill mt-1" style="color: #D97706; font-size: 1.05rem; flex-shrink: 0;"></i>
                            <div style="line-height: 1.35;">
                                <div class="fw-bold" style="color: #854D0E; font-size: 0.82rem;">
                                    {{ $notifNeedVerif }} CR Baru Menunggu Verifikasi Kebutuhan
                                </div>
                                <div style="color: #B45309; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                    Tahapan : 1. Analisis Kebutuhan
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endif

            @if ($notifDev > 0)
                <a href="{{ route('change-requests.index', ['view' => 'development']) }}" class="text-decoration-none">
                    <div class="p-3" style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 14px; transition: transform 0.15s ease;"
                         onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle-fill mt-1" style="color: #2563EB; font-size: 1.05rem; flex-shrink: 0;"></i>
                            <div style="line-height: 1.35;">
                                <div class="fw-bold" style="color: #1E40AF; font-size: 0.82rem;">
                                    {{ $notifDev }} CR sedang dalam tahap Development &amp; Pengujian
                                </div>
                                <div style="color: #2563EB; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                    Tahapan : 2. Development, SIT &amp; UAT
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

