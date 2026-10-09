@extends('layouts.app')

@section('title', 'OUTSTANDING PAYMENT • Monitoring Change Request')

@section('content')
<style>
    .pm-table-container {
        border: 1px solid #D0DCEB !important;
        border-radius: 14px !important;
        background: #FFFFFF !important;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .pm-table thead th {
        background-color: #EBF2FA !important;
        color: #0F172A !important;
        font-weight: 700 !important;
        font-size: 0.72rem !important;
        letter-spacing: 0.04em !important;
        padding-top: 0.85rem !important;
        padding-bottom: 0.85rem !important;
        border-bottom: 1px solid #D0DCEB !important;
        border-top: none !important;
    }
    .pm-table tbody tr {
        border-bottom: 1px solid #E2E8F0 !important;
        border-top: none !important;
        transition: background 0.15s ease;
    }
    .pm-table tbody tr:hover {
        background: #F8FAFC !important;
    }
    .pm-table tbody tr:last-child {
        border-bottom: none !important;
    }
    .pm-table tbody td {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #E2E8F0 !important;
        border-top: none !important;
    }
    .pm-table tbody tr:last-child td {
        border-bottom: none !important;
    }
    .search-input-pm::placeholder {
        color: #94A3B8 !important;
        font-size: 0.86rem;
    }
    .pagination-arrow-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid #CBD5E1;
        border-radius: 8px;
        background: #FFFFFF;
        color: #334155;
        font-size: 0.76rem;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .pagination-arrow-btn:hover:not(.disabled) {
        background: #F1F5F9;
        color: #0F172A;
        border-color: #94A3B8;
    }
    .pagination-arrow-btn.disabled {
        opacity: 0.45;
        pointer-events: none;
        cursor: not-allowed;
    }
</style>

<div class="page-shell pb-5">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="color: #000000; font-size: 2.1rem; font-weight: 800; letter-spacing: -0.025em; line-height: 1.2;">
                OUTSTANDING PAYMENT
            </h1>
            <p class="text-muted mb-0" style="font-size: 0.88rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda</p>
        </div>
        <div>
            <a href="{{ route('pmh.outstanding-payment.export') }}" class="btn text-white fw-bold d-inline-flex align-items-center gap-2"
               style="background: #0063D7; border-radius: 8px; padding: 0.6rem 1.4rem; font-size: 0.85rem; border: none; box-shadow: 0 4px 12px rgba(0,99,215,0.25);">
                <i class="bi bi-download"></i>
                <span>Export Rekap (XLS/PDF)</span>
            </a>
        </div>
    </div>

    <div style="height: 3px; background: #E0F2FE; border-radius: 999px; margin-bottom: 1.5rem;"></div>

    <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">
        <form method="GET" action="{{ route('pmh.outstanding-payment') }}" class="m-0" style="flex: 1; max-width: 380px;">
            <div style="position: relative; display: flex; align-items: center;">
                <i class="bi bi-search" style="position: absolute; left: 16px; color: #94A3B8; font-size: 0.9rem; pointer-events: none;"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search task or CR number..."
                       class="form-control search-input-pm"
                       style="background: #FFFFFF; border: 1.5px solid #CBD5E1; border-radius: 9999px; padding: 0.55rem 1rem 0.55rem 2.6rem; font-size: 0.88rem; color: #1E293B;">
            </div>
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
        </form>

        <div class="dropdown">
            <button class="btn btn-white bg-white dropdown-toggle px-3 py-2 d-flex align-items-center gap-2 shadow-none"
                    type="button" id="statusFilterBtn" data-bs-toggle="dropdown" aria-expanded="false"
                    style="border: 1.5px solid #CBD5E1; border-radius: 12px; font-size: 0.86rem; font-weight: 600; color: #334155;">
                <span>{{ request('status') ? (strtoupper(request('status')) === 'BELUM BAYAR' ? 'Belum Bayar' : (strtoupper(request('status')) === 'LUNAS' ? 'Lunas' : ucfirst(request('status')))) : 'Semua Status' }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border p-1 mt-1" style="border-radius: 10px; min-width: 160px; border-color: #E2E8F0;" aria-labelledby="statusFilterBtn">
                <li><a class="dropdown-item py-1 px-3 small fw-semibold" href="{{ route('pmh.outstanding-payment', array_merge(request()->except('status'))) }}">Semua Status</a></li>
                <li><a class="dropdown-item py-1 px-3 small fw-semibold text-danger" href="{{ route('pmh.outstanding-payment', array_merge(request()->all(), ['status' => 'belum bayar'])) }}">Belum Bayar</a></li>
                <li><a class="dropdown-item py-1 px-3 small fw-semibold text-success" href="{{ route('pmh.outstanding-payment', array_merge(request()->all(), ['status' => 'lunas'])) }}">Lunas</a></li>
            </ul>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="p-4 text-white h-100 shadow-sm"
                 style="background: #014DA1; border-radius: 18px; transition: transform 0.2s ease;">
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

        <div class="col-xl-3 col-md-6 col-12">
            <div class="p-4 text-white h-100 shadow-sm"
                 style="background: #144272; border-radius: 18px; transition: transform 0.2s ease;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-bold" style="font-size: 0.88rem; line-height: 1.35;">Jatuh Tempo Minggu ini</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                         style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.08);">
                        <i class="bi bi-calendar-event" style="font-size: 0.88rem;"></i>
                    </div>
                </div>
                <div class="fw-bold my-2" style="font-size: 3.2rem; line-height: 1; letter-spacing: -0.02em;">
                    {{ $metrics['jatuh_tempo_minggu_ini'] }}
                </div>
                <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.74rem; font-weight: 500;">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Aktif Bulan Ini</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="p-4 text-white h-100 shadow-sm"
                 style="background: #205295; border-radius: 18px; transition: transform 0.2s ease;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-bold" style="font-size: 0.88rem; line-height: 1.35;">Lewat Jatuh Tempo</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                         style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.08);">
                        <i class="bi bi-exclamation-triangle" style="font-size: 0.88rem;"></i>
                    </div>
                </div>
                <div class="fw-bold my-2" style="font-size: 3.2rem; line-height: 1; letter-spacing: -0.02em;">
                    {{ $metrics['lewat_jatuh_tempo'] }}
                </div>
                <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.74rem; font-weight: 500;">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Aktif Bulan Ini</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="p-4 text-white h-100 shadow-sm"
                 style="background: #2C74B3; border-radius: 18px; transition: transform 0.2s ease;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-bold" style="font-size: 0.88rem; line-height: 1.35;">Terbayar Bulan ini</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                         style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.08);">
                        <i class="bi bi-check2-circle" style="font-size: 0.88rem;"></i>
                    </div>
                </div>
                <div class="fw-bold my-2" style="font-size: 3.2rem; line-height: 1; letter-spacing: -0.02em;">
                    {{ $metrics['terbayar_bulan_ini'] }}
                </div>
                <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.74rem; font-weight: 500;">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Aktif Bulan Ini</span>
                </div>
            </div>
        </div>
    </div>

    <div class="pm-table-container mb-4">
        <div class="table-responsive">
            <table class="table pm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">NO</th>
                        <th style="min-width: 220px; padding-left: 1.25rem;">PEMOHON</th>
                        <th style="min-width: 240px;">JUDUL CR</th>
                        <th style="min-width: 140px;">NILAI</th>
                        <th class="text-center" style="min-width: 130px;">TANGGAL GO-LIVE</th>
                        <th class="text-center" style="min-width: 130px;">TARGET INVOICE</th>
                        <th class="text-center" style="min-width: 130px;">TANGGAL INVOICE</th>
                        <th class="text-center" style="min-width: 130px;">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $avatarColors = ['#818CF8', '#F87171', '#10B981', '#A855F7', '#6366F1', '#EC4899', '#3B82F6', '#14B8A6'];
                    @endphp
                    @forelse ($payments as $p)
                        @php
                            $colorIndex = ($p['no'] - 1) % count($avatarColors);
                            $badgeColor = $avatarColors[$colorIndex];
                        @endphp
                        <tr>
                            <td class="text-center fw-bold" style="font-size: 0.85rem; color: #0F172A;">
                                {{ $p['no'] }}
                            </td>
                            <td style="padding-left: 1.25rem;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                         style="width: 28px; height: 28px; background: {{ $badgeColor }}; font-size: 0.72rem; flex-shrink: 0;">
                                        {{ $p['avatar'] }}
                                    </div>
                                    <span class="fw-bold" style="font-size: 0.85rem; color: #0F172A;">
                                        {{ $p['pemohon'] }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-semibold" style="font-size: 0.86rem; color: #0F172A; line-height: 1.35;">{{ $p['judul_cr'] }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem; color: #64748B !important;">{{ $p['kategori'] }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold" style="font-size: 0.86rem; color: #0F172A;">
                                    Rp. {{ number_format($p['nilai'], 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-center" style="font-size: 0.82rem; color: #334155;">
                                {{ $p['tgl_golive'] }}
                            </td>
                            <td class="text-center" style="font-size: 0.82rem; color: #334155;">
                                {{ $p['target_invoice'] }}
                            </td>
                            <td class="text-center" style="font-size: 0.82rem; color: #334155;">
                                {{ $p['tgl_invoice'] }}
                            </td>
                            <td class="text-center">
                                @if (strtoupper($p['status']) === 'BELUM BAYAR')
                                    <span class="d-inline-block fw-bold text-center"
                                          style="background: #FEE2E2; color: #EF4444; border-radius: 6px; padding: 4px 10px; font-size: 0.72rem; letter-spacing: 0.02em;">
                                        BELUM BAYAR
                                    </span>
                                @else
                                    <span class="d-inline-block fw-bold text-center"
                                          style="background: #DCFCE7; color: #22C55E; border-radius: 6px; padding: 4px 14px; font-size: 0.72rem; letter-spacing: 0.02em;">
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

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-2 text-dark" style="font-size: 0.82rem; font-weight: 700;">
        <div style="color: #0F172A; letter-spacing: 0.02em;">
            TOTAL DATA: <span style="font-weight: 800;">{{ $totalData }}</span>
        </div>
        <div class="d-flex align-items-center gap-4">
            <div class="d-flex align-items-center gap-2" style="color: #0F172A; letter-spacing: 0.02em;">
                <span>ROWS PER PAGE</span>
                <div class="dropdown">
                    <button class="btn btn-white bg-white dropdown-toggle px-2 py-1 d-flex align-items-center gap-2 shadow-none"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false"
                            style="border: 1.5px solid #CBD5E1; border-radius: 8px; font-size: 0.82rem; font-weight: 700; color: #0F172A; line-height: 1.2;">
                        <span>10</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border p-1" style="min-width: 80px; border-radius: 8px; border-color: #E2E8F0;">
                        <li><a class="dropdown-item py-1 px-2 small fw-bold" href="#">10</a></li>
                        <li><a class="dropdown-item py-1 px-2 small fw-bold" href="#">25</a></li>
                        <li><a class="dropdown-item py-1 px-2 small fw-bold" href="#">50</a></li>
                    </ul>
                </div>
            </div>
            <div style="color: #0F172A; letter-spacing: 0.02em;">
                PAGE 1 OF {{ max(1, ceil($totalData / 10)) }}
            </div>
            <div class="d-flex align-items-center gap-1">
                <a href="javascript:void(0)" class="pagination-arrow-btn disabled" title="First Page">
                    <i class="bi bi-chevron-double-left"></i>
                </a>
                <a href="javascript:void(0)" class="pagination-arrow-btn disabled" title="Previous Page">
                    <i class="bi bi-chevron-left"></i>
                </a>
                <a href="javascript:void(0)" class="pagination-arrow-btn disabled" title="Next Page">
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="javascript:void(0)" class="pagination-arrow-btn disabled" title="Last Page">
                    <i class="bi bi-chevron-double-right"></i>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
