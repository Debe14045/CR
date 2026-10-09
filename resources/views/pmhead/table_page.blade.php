@extends('layouts.app')

@section('title', $pageTitle . ' • PM Head Monitoring CR')

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

    /* Segmented Tab Bar - Full Bleed Block (No Outer Inset Padding) */
    .pm-filter-container {
        display: flex;
        align-items: stretch;
        width: 100%;
        background: #EAF2FB;
        border: 1px solid #CBDCEE;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        padding: 0 !important;
    }
    .pm-filter-tab {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1rem;
        font-size: 0.92rem;
        font-weight: 700;
        text-decoration: none;
        text-align: center;
        transition: all 0.15s ease;
        position: relative;
    }
    .pm-filter-tab.active {
        background: #0B6EBD !important;
        color: #FFFFFF !important;
        box-shadow: none !important;
    }
    .pm-filter-tab:not(.active) {
        background: transparent !important;
        color: #1E3A8A !important;
    }
    .pm-filter-divider {
        width: 1px;
        background: #CBDCEE;
        flex-shrink: 0;
    }
</style>

<div class="page-shell pb-5">

    <div class="mb-3">
        <h1 class="fw-bold mb-1" style="color: #000000; font-size: 2.1rem; font-weight: 800; letter-spacing: -0.025em; line-height: 1.2;">
            Change Request
        </h1>
        <p class="mb-0" style="font-size: 0.88rem; color: #64748B; font-weight: 500;">Pantau seluruh permintaan perubahan Anda</p>
    </div>

    @php
        $currentRoute = Route::currentRouteName();
        $curTab = $activeTab ?? 'semua';
    @endphp
    <div class="pm-filter-container">
        <a href="{{ route($currentRoute, array_merge(request()->query(), ['tab' => 'semua'])) }}"
           class="pm-filter-tab {{ $curTab === 'semua' ? 'active' : '' }}"
           style="{{ $curTab === 'semua' ? 'border-top-left-radius: 11px; border-bottom-left-radius: 11px;' : '' }}">
            Semua CR
        </a>
        <div class="pm-filter-divider" style="{{ ($curTab === 'semua' || $curTab === 'aktif') ? 'visibility: hidden;' : '' }}"></div>
        <a href="{{ route($currentRoute, array_merge(request()->query(), ['tab' => 'aktif'])) }}"
           class="pm-filter-tab {{ $curTab === 'aktif' ? 'active' : '' }}">
            CR Aktif
        </a>
        <div class="pm-filter-divider" style="{{ ($curTab === 'aktif' || $curTab === 'selesai') ? 'visibility: hidden;' : '' }}"></div>
        <a href="{{ route($currentRoute, array_merge(request()->query(), ['tab' => 'selesai'])) }}"
           class="pm-filter-tab {{ $curTab === 'selesai' ? 'active' : '' }}"
           style="{{ $curTab === 'selesai' ? 'border-top-right-radius: 11px; border-bottom-right-radius: 11px;' : '' }}">
            CR Selesai
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">
        <form method="GET" action="{{ route($currentRoute) }}" class="m-0" style="flex: 1; max-width: 380px;">
            <input type="hidden" name="tab" value="{{ $curTab }}">
            <div style="position: relative; display: flex; align-items: center;">
                <i class="bi bi-search" style="position: absolute; left: 16px; color: #64748B; font-size: 0.92rem; pointer-events: none;"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search task or CR number..."
                       class="form-control search-input-pm"
                       style="background: #FFFFFF; border: 1.5px solid #CBD5E1; border-radius: 9999px; padding: 0.52rem 1rem 0.52rem 2.65rem; font-size: 0.88rem; color: #1E293B; box-shadow: none;">
            </div>
        </form>

        <div class="dropdown">
            @php
                $statusDisplay = match(strtolower(request('status', ''))) {
                    'diajukan', 'awaiting_pm', 'persetujuan' => 'Diajukan',
                    'analisa', 'analysis' => 'Analysis',
                    'development', 'develop' => 'Develop',
                    'golive', 'go-live' => 'Go Live',
                    default => request('status') ? ucfirst(str_replace('_', ' ', request('status'))) : 'Semua Status'
                };
            @endphp
            <button class="btn btn-white bg-white dropdown-toggle px-3 py-2 d-flex align-items-center justify-content-between shadow-none"
                    type="button" id="statusFilterBtn" data-bs-toggle="dropdown" aria-expanded="false"
                    style="border: 1.5px solid #D6D3CD; border-radius: 12px; font-size: 0.86rem; font-weight: 600; color: #475569; min-width: 145px; gap: 0.85rem;">
                <span>{{ $statusDisplay }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border p-1 mt-1" style="border-radius: 10px; min-width: 160px; border-color: #E2E8F0;" aria-labelledby="statusFilterBtn">
                <li><a class="dropdown-item py-1 px-3 small" href="{{ route($currentRoute, ['tab' => $curTab, 'search' => request('search')]) }}">Semua Status</a></li>
                <li><a class="dropdown-item py-1 px-3 small" href="{{ route($currentRoute, ['tab' => $curTab, 'status' => 'diajukan', 'search' => request('search')]) }}">Diajukan</a></li>
                <li><a class="dropdown-item py-1 px-3 small" href="{{ route($currentRoute, ['tab' => $curTab, 'status' => 'analisa', 'search' => request('search')]) }}">Analysis</a></li>
                <li><a class="dropdown-item py-1 px-3 small" href="{{ route($currentRoute, ['tab' => $curTab, 'status' => 'development', 'search' => request('search')]) }}">Develop</a></li>
                <li><a class="dropdown-item py-1 px-3 small" href="{{ route($currentRoute, ['tab' => $curTab, 'status' => 'golive', 'search' => request('search')]) }}">Go Live</a></li>
            </ul>
        </div>
    </div>

    <div style="height: 3px; background: #E0F2FE; border-radius: 999px; margin-bottom: 1.5rem;"></div>

    <div class="pm-table-container mb-4">
        <div class="table-responsive m-0">
            <table class="table pm-table mb-0 align-middle" style="border-collapse: collapse; border: none !important;">
                <thead>
                    <tr>
                        <th class="px-3 text-center text-uppercase" style="width: 50px;">NO</th>
                        <th class="px-3 text-uppercase" style="min-width: 140px;">NAMA PM</th>
                        <th class="px-3 text-uppercase" style="min-width: 180px;">PEMOHON</th>
                        <th class="px-3 text-uppercase" style="min-width: 250px;">JUDUL CR</th>
                        <th class="px-3 text-center text-uppercase" style="min-width: 110px;">PENGAJUAN</th>
                        <th class="px-3 text-center text-uppercase" style="min-width: 105px;">STATUS</th>
                        <th class="px-3 text-center text-uppercase" style="min-width: 100px;">PRIORITAS</th>
                        <th class="px-3 text-center text-uppercase" style="width: 110px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($changeRequests as $index => $cr)
                        @php
                            $statusKey = strtolower($cr->status);
                            $isUrgent = in_array(strtolower($cr->prioritas ?? ''), ['kritis', 'tinggi', 'urgent']);
                            $clientCompany = $cr->client?->company ?? ($cr->klien ?: 'PT Maju Bersama');
                            $initials = strtoupper(substr($cr->client?->nickname ?? $clientCompany, 0, 2));
                            $colorSeed = abs(crc32($clientCompany)) % 4;
                            $avatarBg = match($colorSeed) {
                                0 => '#EF4444',
                                1 => '#10B981',
                                2 => '#8B5CF6',
                                default => '#6366F1',
                            };
                        @endphp
                        <tr>
                            <td class="px-3 text-center text-muted" style="font-size: 0.88rem; font-weight: 500;">
                                {{ $changeRequests->firstItem() ? ($changeRequests->firstItem() + $index) : ($index + 1) }}
                            </td>
                            <td class="px-3 fw-bold text-dark" style="font-size: 0.88rem; font-weight: 700;">
                                {{ $cr->nama_pm ?: ($cr->user?->name ?: 'Danendra dada') }}
                            </td>
                            <td class="px-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                         style="width: 28px; height: 28px; background: {{ $avatarBg }}; font-size: 0.68rem; flex-shrink: 0;">
                                        {{ $initials }}
                                    </div>
                                    <span class="fw-bold text-dark" style="font-size: 0.88rem; font-weight: 700;">
                                        {{ $clientCompany }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-3">
                                <div>
                                    <div class="text-dark" style="font-size: 0.88rem; line-height: 1.35; font-weight: 400;">{{ $cr->judul }}</div>
                                    <div class="text-muted" style="font-size: 0.74rem; font-weight: 400;">{{ $cr->proyek_terkait ?: ($cr->kode_cr ?: 'System Enhancement') }}</div>
                                </div>
                            </td>
                            <td class="px-3 text-center text-secondary" style="font-size: 0.84rem; font-weight: 400;">
                                {{ $cr->tanggal_pengajuan ? $cr->tanggal_pengajuan->format('d M Y') : $cr->created_at->format('d M Y') }}
                            </td>
                            <td class="px-3 text-center">
                                @if (in_array($statusKey, ['diajukan', 'awaiting_pm', 'awaiting_pmh']))
                                    <span class="badge px-3 py-1 fw-bold d-inline-flex align-items-center gap-1"
                                          style="background: #FEF3C7; color: #D97706; border-radius: 999px; font-size: 0.74rem;">
                                        <span style="width: 5px; height: 5px; border-radius: 50%; background: #D97706;"></span>
                                        Diajukan
                                    </span>
                                @elseif (in_array($statusKey, ['analisa', 'dianalisis']))
                                    <span class="badge px-3 py-1 fw-bold d-inline-flex align-items-center gap-1"
                                          style="background: #E0F2FE; color: #0284C7; border-radius: 999px; font-size: 0.74rem;">
                                        <span style="width: 5px; height: 5px; border-radius: 50%; background: #0284C7;"></span>
                                        Analysis
                                    </span>
                                @elseif (in_array($statusKey, ['development', 'dikerjakan']))
                                    <span class="badge px-3 py-1 fw-bold d-inline-flex align-items-center gap-1"
                                          style="background: #EDE9FE; color: #7C3AED; border-radius: 999px; font-size: 0.74rem;">
                                        <span style="width: 5px; height: 5px; border-radius: 50%; background: #7C3AED;"></span>
                                        Develop
                                    </span>
                                @elseif (in_array($statusKey, ['golive', 'selesai']))
                                    <span class="badge px-3 py-1 fw-bold d-inline-flex align-items-center gap-1"
                                          style="background: #DCFCE7; color: #16A34A; border-radius: 999px; font-size: 0.74rem;">
                                        <span style="width: 5px; height: 5px; border-radius: 50%; background: #16A34A;"></span>
                                        Go Live
                                    </span>
                                @else
                                    <span class="badge px-3 py-1 fw-bold d-inline-flex align-items-center gap-1"
                                          style="background: #E0F2FE; color: #0284C7; border-radius: 999px; font-size: 0.74rem;">
                                        <span style="width: 5px; height: 5px; border-radius: 50%; background: #0284C7;"></span>
                                        {{ ucfirst(str_replace('_', ' ', $cr->status)) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 text-center">
                                @if ($isUrgent)
                                    <span class="badge fw-bold px-2 py-1"
                                          style="background: #FEE2E2; color: #DC2626; border-radius: 6px; font-size: 0.68rem; letter-spacing: 0.04em;">
                                        URGENT
                                    </span>
                                @else
                                    <span class="badge fw-bold px-2 py-1"
                                          style="background: #DCFCE7; color: #16A34A; border-radius: 6px; font-size: 0.68rem; letter-spacing: 0.04em;">
                                        NORMAL
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 text-center">
                                <a href="{{ route('pmh.review', $cr) }}" class="btn text-white fw-bold d-inline-flex align-items-center justify-content-center gap-1"
                                   style="background: #0063D7; border-radius: 8px; font-size: 0.8rem; padding: 0.38rem 1.15rem; border: none;">
                                    Review <i class="bi bi-chevron-right" style="font-size: 0.68rem;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted" style="border: none !important;">
                                <i class="bi bi-inbox text-secondary fs-2 d-block mb-2"></i>
                                <div class="fw-bold text-dark">Tidak ada data Change Request yang ditemukan.</div>
                                <div class="small">Silakan ubah filter status atau kata kunci pencarian Anda.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @php
        $currentPage = $changeRequests->currentPage();
        $lastPage = max(1, $changeRequests->lastPage());
        $perPage = $changeRequests->perPage();
    @endphp
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
                        <span>{{ $perPage }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border p-1" style="min-width: 80px; border-radius: 8px; border-color: #E2E8F0;">
                        <li><a class="dropdown-item py-1 px-2 small fw-bold" href="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}">10</a></li>
                        <li><a class="dropdown-item py-1 px-2 small fw-bold" href="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}">25</a></li>
                        <li><a class="dropdown-item py-1 px-2 small fw-bold" href="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}">50</a></li>
                    </ul>
                </div>
            </div>
            <div style="color: #0F172A; letter-spacing: 0.02em;">
                PAGE {{ $currentPage }} OF {{ $lastPage }}
            </div>
            <div class="d-flex align-items-center gap-1">
                <a href="{{ $changeRequests->url(1) }}" class="pagination-arrow-btn {{ $currentPage <= 1 ? 'disabled' : '' }}" title="First Page">
                    <i class="bi bi-chevron-double-left"></i>
                </a>
                <a href="{{ $changeRequests->previousPageUrl() ?: 'javascript:void(0)' }}" class="pagination-arrow-btn {{ !$changeRequests->previousPageUrl() ? 'disabled' : '' }}" title="Previous Page">
                    <i class="bi bi-chevron-left"></i>
                </a>
                <a href="{{ $changeRequests->nextPageUrl() ?: 'javascript:void(0)' }}" class="pagination-arrow-btn {{ !$changeRequests->nextPageUrl() ? 'disabled' : '' }}" title="Next Page">
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="{{ $changeRequests->url($lastPage) }}" class="pagination-arrow-btn {{ $currentPage >= $lastPage ? 'disabled' : '' }}" title="Last Page">
                    <i class="bi bi-chevron-double-right"></i>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
