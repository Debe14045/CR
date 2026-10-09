@extends('layouts.app')

@section('title', 'Notifikasi • PM Head Monitoring CR')

@section('content')
<div class="page-shell pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.85rem; font-weight: 800; letter-spacing: -0.02em;">
                Notifikasi
            </h1>
            <p class="text-muted mb-0" style="font-size: 0.86rem; color: #64748B;">Pemberitahuan persetujuan penting dan status tahapan Change Request Anda.</p>
        </div>
    </div>

    <div class="card p-4 p-md-5 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 20px; background: #FFFFFF;">

        <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom" style="border-color: #F1F5F9 !important;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-bell-fill text-primary" style="font-size: 1.25rem;"></i>
                <h5 class="fw-bold text-dark mb-0" style="font-size: 1.1rem;">Semua Notifikasi</h5>
            </div>
            <span class="badge px-3 py-2 text-primary bg-primary-subtle rounded-pill fw-semibold" style="font-size: 0.76rem;">
                {{ $totalNew ?? 0 }} Baru
            </span>
        </div>

        <div class="d-flex flex-column gap-3">
            @forelse ($notifications as $n)
                <div class="p-3 p-md-4 rounded-3 border transition-all" style="background: {{ $n['card_bg'] }}; border-color: {{ $n['border_color'] }} !important; border-radius: 14px;">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center {{ $n['icon_color'] }} bg-white shadow-sm flex-shrink-0"
                             style="width: 40px; height: 40px;">
                            <i class="bi {{ $n['icon'] }} fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                <span class="fw-bold {{ $n['icon_color'] }}" style="font-size: 0.95rem;">
                                    {{ $n['title'] }}
                                </span>
                                <span class="text-muted small" style="font-size: 0.74rem;">{{ $n['time'] }}</span>
                            </div>
                            <p class="text-secondary small mb-2" style="font-size: 0.82rem;">
                                {{ $n['desc'] }}
                            </p>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge px-2 py-1 {{ $n['badge_class'] }} rounded-pill fw-medium" style="font-size: 0.72rem;">
                                    {{ $n['badge'] }}
                                </span>
                                <a href="{{ $n['url'] }}" class="btn btn-sm {{ $n['btn_class'] }} text-white fw-semibold px-3 py-1" style="border-radius: 6px; font-size: 0.76rem;">
                                    {{ $n['btn_text'] }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-bell-slash fs-2 d-block mb-2 text-secondary"></i>
                    <div class="fw-bold text-dark">Tidak ada notifikasi baru saat ini.</div>
                    <div class="small">Seluruh aktivitas supervisi Change Request telah terpantau dengan baik.</div>
                </div>
            @endforelse
        </div>

        </div>

    </div>

</div>
@endsection
