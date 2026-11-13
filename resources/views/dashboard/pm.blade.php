@extends('layouts.app')

@section('title', 'Dashboard PM • PT. ITPI Technology')

@section('content')
<div class="page-shell" style="width: 100%; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;">

    {{-- ── 1. Page Header (DASHBOARD & Ajukan CR Baru) ── --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="color: #000000; font-size: 2.15rem; font-weight: 800; letter-spacing: -0.02em;">DASHBOARD</h1>
            <p class="text-muted mb-0" style="font-size: 0.88rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda</p>
        </div>
        <div>
            <a href="{{ route('change-requests.create') }}" class="btn text-white fw-bold d-inline-flex align-items-center gap-2"
               style="background: #50CD89; border-radius: 9999px; padding: 0.62rem 1.6rem; font-size: 0.88rem; border: none; box-shadow: 0 4px 14px rgba(80,205,137,0.35); transition: transform 0.15s ease;"
               onmouseover="this.style.transform='translateY(-1px)';" onmouseout="this.style.transform='translateY(0)';">
                <i class="bi bi-plus-lg" style="font-size: 0.95rem;"></i>
                <span>Ajukan CR Baru</span>
            </a>
        </div>
    </div>

    {{-- Horizontal Light Blue Line matching Figma --}}
    <div style="height: 2px; background: #BAE6FD; border-radius: 999px; margin-bottom: 1.5rem;"></div>

    {{-- ── 2. 4 Kartu Metrik Utama PM (Semua CR, Butuh Persetujuan, Development, GO LIVE) ── --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Semua CR (#014DA1) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('change-requests.index', ['view' => 'total']) }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #014DA1; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(1,77,161,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">Semua CR</span>
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

        {{-- Card 2: Butuh Persetujuan (#144272) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('change-requests.index', ['view' => 'total', 'status' => 'awaiting_pm']) }}" class="text-decoration-none d-block h-100">
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

        {{-- Card 3: Development (#205295) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('change-requests.index', ['view' => 'development']) }}" class="text-decoration-none d-block h-100">
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

        {{-- Card 4: GO LIVE (#2C74B3) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('change-requests.index', ['view' => 'golive']) }}" class="text-decoration-none d-block h-100">
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

    {{-- ── 3. Lower Row: Tren CR Bulanan (Kiri) & NOTIFIKASI (Kanan) ── --}}
    <div class="row g-4 mb-4">
        
        {{-- Kolom Kiri: Tren CR Bulanan (Diagram Batang) --}}
        <div class="col-lg-7 col-12">
            @include('dashboard.partials.pm-trend-chart')
        </div>

        {{-- Kolom Kanan: NOTIFIKASI --}}
        <div class="col-lg-5 col-12">
            @include('dashboard.partials.role-notifications-card')
        </div>

    </div>

</div>
@endsection
