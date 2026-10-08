@extends('layouts.app')

@section('title', 'OUTSTANDING PAYMENT • Monitoring Change Request')

@section('content')
<div class="page-shell pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.75rem; font-weight: 800; letter-spacing: -0.01em;">
                OUTSTANDING PAYMENT
            </h1>
            <p class="text-muted mb-0" style="font-size: 0.86rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda</p>
        </div>
        <div>
            <a href="{{ route('pmh.outstanding-payment.export') }}" class="btn text-white fw-semibold d-inline-flex align-items-center gap-2"
               style="background: #0063D7; border-radius: 8px; padding: 0.55rem 1.35rem; font-size: 0.85rem; border: none; box-shadow: 0 4px 12px rgba(0,99,215,0.25);">
                <i class="bi bi-download"></i>
                <span>Export Rekap (XLS/PDF)</span>
            </a>
        </div>
    </div>

    {{-- Search & Filter Toolbar --}}
    <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">
        <form method="GET" action="{{ route('pmh.outstanding-payment') }}" class="m-0" style="flex: 1; max-width: 440px;">
            <div style="position: relative; display: flex; align-items: center;">
                <i class="bi bi-search" style="position: absolute; left: 16px; color: #94A3B8; font-size: 0.9rem; pointer-events: none;"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search task or CR number..."
                       class="form-control"
                       style="background: #FFFFFF; border: 1.5px solid #CBD5E1; border-radius: 9999px; padding: 0.55rem 1rem 0.55rem 2.6rem; font-size: 0.88rem; color: #1E293B;">
            </div>
        </form>

        <div class="dropdown">
            <button class="btn btn-white bg-white dropdown-toggle px-3 py-2 d-flex align-items-center gap-2 shadow-sm"
                    type="button" id="statusFilterBtn" data-bs-toggle="dropdown" aria-expanded="false"
                    style="border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.86rem; color: #334155;">
                <span>{{ request('status') ? ucfirst(request('status')) : 'Semua Status' }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-1 mt-1" style="border-radius: 10px; min-width: 160px;" aria-labelledby="statusFilterBtn">
                <li><a class="dropdown-item py-1 px-3 small" href="{{ route('pmh.outstanding-payment') }}">Semua Status</a></li>
                <li><a class="dropdown-item py-1 px-3 small text-danger" href="{{ route('pmh.outstanding-payment', ['status' => 'belum bayar']) }}">Belum Bayar</a></li>
                <li><a class="dropdown-item py-1 px-3 small text-success" href="{{ route('pmh.outstanding-payment', ['status' => 'lunas']) }}">Lunas</a></li>
            </ul>
        </div>
    </div>

    {{-- 4 Big Metric Cards (Deep Royal Blue #134B8A) --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Tagihan Outstanding --}}
        <div class="col-xl-3 col-md-6 col-12">
            <div class="p-4 text-white h-100 shadow-sm"
                 style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-bold" style="font-size: 0.88rem; line-height: 1.35;">Total Tagihan<br>Outstanding</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                         style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.08);">
                        <i class="bi bi-wallet2" style="font-size: 0.88rem;"></i>
                    </div>
                </div>
                <div class="fw-bold my-2" style="font-size: 1.55rem; letter-spacing: -0.02em;">
                    {{ $metrics['total_tagihan'] }}
                </div>
                <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.74rem; font-weight: 500;">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Aktif Bulan Ini</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Jatuh Tempo Minggu ini --}}
        <div class="col-xl-3 col-md-6 col-12">
            <div class="p-4 text-white h-100 shadow-sm"
                 style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-bold" style="font-size: 0.88rem; line-height: 1.35;">Jatuh Tempo Minggu ini</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                         style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.08);">
                        <i class="bi bi-calendar-event" style="font-size: 0.88rem;"></i>
                    </div>
                </div>
                <div class="fw-bold my-2" style="font-size: 2.8rem; line-height: 1; letter-spacing: -0.02em;">
                    {{ $metrics['jatuh_tempo_minggu_ini'] }}
                </div>
                <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.74rem; font-weight: 500;">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Aktif Bulan Ini</span>
                </div>
            </div>
        </div>

        {{-- Card 3: Lewat Jatuh Tempo --}}
        <div class="col-xl-3 col-md-6 col-12">
            <div class="p-4 text-white h-100 shadow-sm"
                 style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-bold" style="font-size: 0.88rem; line-height: 1.35;">Lewat Jatuh Tempo</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                         style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.08);">
                        <i class="bi bi-exclamation-triangle" style="font-size: 0.88rem;"></i>
                    </div>
                </div>
                <div class="fw-bold my-2" style="font-size: 2.8rem; line-height: 1; letter-spacing: -0.02em;">
                    {{ $metrics['lewat_jatuh_tempo'] }}
                </div>
                <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.74rem; font-weight: 500;">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Aktif Bulan Ini</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Terbayar Bulan ini --}}
        <div class="col-xl-3 col-md-6 col-12">
            <div class="p-4 text-white h-100 shadow-sm"
                 style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-bold" style="font-size: 0.88rem; line-height: 1.35;">Terbayar Bulan ini</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                         style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.08);">
                        <i class="bi bi-check2-circle" style="font-size: 0.88rem;"></i>
                    </div>
                </div>
                <div class="fw-bold my-2" style="font-size: 2.8rem; line-height: 1; letter-spacing: -0.02em;">
                    {{ $metrics['terbayar_bulan_ini'] }}
                </div>
                <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.74rem; font-weight: 500;">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Aktif Bulan Ini</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Outstanding Payment Table --}}
    <div class="card overflow-hidden shadow-sm border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 20px; background: #FFFFFF;">
        <div class="table-responsive">
            <table class="table mb-0 align-middle" style="border-collapse: separate; border-spacing: 0;">
                <thead style="background: #F1F5F9; border-bottom: 1px solid #E2E8F0;">
                    <tr>
                        <th class="py-3 px-3 text-center text-uppercase fw-bold" style="font-size: 0.72rem; color: #475569; width: 45px;">NO</th>
                        <th class="py-3 px-3 text-uppercase fw-bold" style="font-size: 0.72rem; color: #475569; min-width: 170px;">PEMOHON</th>
                        <th class="py-3 px-3 text-uppercase fw-bold" style="font-size: 0.72rem; color: #475569; min-width: 220px;">JUDUL CR</th>
                        <th class="py-3 px-3 text-uppercase fw-bold" style="font-size: 0.72rem; color: #475569;">NILAI</th>
                        <th class="py-3 px-3 text-uppercase fw-bold text-center" style="font-size: 0.72rem; color: #475569;">TANGGAL GO-LIVE</th>
                        <th class="py-3 px-3 text-uppercase fw-bold text-center" style="font-size: 0.72rem; color: #475569;">TARGET INVOICE</th>
                        <th class="py-3 px-3 text-uppercase fw-bold text-center" style="font-size: 0.72rem; color: #475569;">TANGGAL INVOICE</th>
                        <th class="py-3 px-3 text-uppercase fw-bold text-center" style="font-size: 0.72rem; color: #475569;">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $p)
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                            <td class="py-3 px-3 text-center fw-semibold text-secondary" style="font-size: 0.85rem;">
                                {{ $p['no'] }}
                            </td>
                            <td class="py-3 px-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                         style="width: 30px; height: 30px; background: {{ $p['no'] % 2 == 0 ? '#EF4444' : ($p['no'] == 3 ? '#10B981' : ($p['no'] == 4 ? '#8B5CF6' : '#6366F1')) }}; font-size: 0.72rem; flex-shrink: 0;">
                                        {{ $p['avatar'] }}
                                    </div>
                                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">
                                        {{ $p['pemohon'] }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <div>
                                    <div class="fw-semibold text-dark" style="font-size: 0.86rem; line-height: 1.35;">{{ $p['judul_cr'] }}</div>
                                    <div class="text-muted" style="font-size: 0.74rem;">{{ $p['kategori'] }}</div>
                                </div>
                            </td>
                            <td class="py-3 px-3 fw-bold text-dark" style="font-size: 0.86rem;">
                                Rp. {{ number_format($p['nilai'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-center text-muted" style="font-size: 0.82rem;">
                                {{ $p['tgl_golive'] }}
                            </td>
                            <td class="py-3 px-3 text-center text-muted" style="font-size: 0.82rem;">
                                {{ $p['target_invoice'] }}
                            </td>
                            <td class="py-3 px-3 text-center text-muted" style="font-size: 0.82rem;">
                                {{ $p['tgl_invoice'] }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if ($p['status'] === 'BELUM BAYAR')
                                    <span class="badge fw-bold px-3 py-1"
                                          style="background: #FEE2E2; color: #DC2626; border-radius: 6px; font-size: 0.74rem; letter-spacing: 0.02em;">
                                        BELUM BAYAR
                                    </span>
                                @else
                                    <span class="badge fw-bold px-3 py-1"
                                          style="background: #DCFCE7; color: #16A34A; border-radius: 6px; font-size: 0.74rem; letter-spacing: 0.02em;">
                                        LUNAS
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                Tidak ada data tagihan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Figma Footer Pagination Bar (Sinkron Dinamis) --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-2 text-muted" style="font-size: 0.82rem; font-weight: 600;">
        <div>
            TOTAL DATA: <span class="text-dark">{{ $totalData }}</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span>ROWS PER PAGE</span>
                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.82rem; border-color: #CBD5E1 !important;">10</span>
            </div>
            <div>
                PAGE 1 OF 1
            </div>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary px-2 py-1 disabled"><i class="bi bi-chevron-double-left"></i></button>
                <button type="button" class="btn btn-outline-secondary px-2 py-1 disabled"><i class="bi bi-chevron-left"></i></button>
                <button type="button" class="btn btn-outline-secondary px-2 py-1 disabled"><i class="bi bi-chevron-right"></i></button>
                <button type="button" class="btn btn-outline-secondary px-2 py-1 disabled"><i class="bi bi-chevron-double-right"></i></button>
            </div>
        </div>
    </div>

</div>
@endsection
