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
                    style="border: 1.5px solid #CBD5E1; border-radius: 12px; font-size: 0.88rem; font-weight: 500; color: #334155; min-width: 145px; gap: 1rem;">
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

    <div class="card overflow-hidden shadow-sm border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 20px; background: #FFFFFF;">
        <div class="table-responsive">
            <table class="table mb-0 align-middle" style="border-collapse: separate; border-spacing: 0;">
                <thead style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0;">
                    <tr>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase" data-i18n="th_no" style="font-size: 0.72rem; letter-spacing: 0.06em; width: 60px;">NO</th>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase" data-i18n="th_kode_cr" style="font-size: 0.72rem; letter-spacing: 0.06em;">KODE CR</th>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase" data-i18n="th_requester" style="font-size: 0.72rem; letter-spacing: 0.06em;">PEMOHON</th>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase" data-i18n="th_cr_title" style="font-size: 0.72rem; letter-spacing: 0.06em;">JUDUL CR</th>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase" data-i18n="th_submission_date" style="font-size: 0.72rem; letter-spacing: 0.06em; width: 140px;">PENGAJUAN</th>
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

                            if (in_array(strtolower($cr->status), ['analisa', 'dianalisis'])) {
                                $statusText = 'Analisis';
                                $statusBg = '#DBEAFE';
                                $statusColor = '#2563EB';
                            } elseif (in_array(strtolower($cr->status), ['development', 'dikerjakan'])) {
                                $statusText = 'Develop';
                                $statusBg = '#EDE9FE';
                                $statusColor = '#7C3AED';
                            } elseif (in_array(strtolower($cr->status), ['uat', 'sit'])) {
                                $statusText = 'UAT';
                                $statusBg = '#FCE7F3';
                                $statusColor = '#DB2777';
                            } elseif (in_array(strtolower($cr->status), ['golive', 'selesai', 'invoicing'])) {
                                $statusText = 'Go-Live';
                                $statusBg = '#D1FAE5';
                                $statusColor = '#059669';
                            }

                            // Priority badge
                            $isUrgent = in_array(strtolower($cr->prioritas ?? ''), ['urgent', 'kritis', 'tinggi', 'high']);
                            
                            $rowNo = method_exists($changeRequests, 'firstItem') ? ($changeRequests->firstItem() + $index) : ($index + 1);
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC';" onmouseout="this.style.background='#FFFFFF';">
                            {{-- NO --}}
                            <td class="py-3 px-4 text-muted fw-medium" style="font-size: 0.85rem;">
                                {{ str_pad($rowNo, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            
                            {{-- KODE CR --}}
                            <td class="py-3 px-4">
                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                    {{ $cr->kode_cr ?? 'CR-'.str_pad($cr->id, 4, '0', STR_PAD_LEFT) }}
                                </div>
                            </td>

                            {{-- PEMOHON --}}
                            <td class="py-3 px-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                         style="width: 34px; height: 34px; background: {{ $avColor }}; font-size: 0.82rem;">
                                        {{ strtoupper(substr($companyName, 0, 2)) }}
                                    </div>
                                    <div style="line-height: 1.2;">
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $companyName }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $pemohonName }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- JUDUL CR --}}
                            <td class="py-3 px-4">
                                <div style="line-height: 1.25;">
                                    <a href="{{ route('pmh.review', $cr) }}" class="fw-bold text-dark text-decoration-none hover-primary" style="font-size: 0.88rem;">
                                        {{ $cr->judul }}
                                    </a>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        {{ $cr->proyek_terkait ?: 'Akses & Dashboard' }}
                                    </div>
                                </div>
                            </td>

                            {{-- PENGAJUAN --}}
                            <td class="py-3 px-4">
                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                    {{ $cr->tanggal_pengajuan ? $cr->tanggal_pengajuan->translatedFormat('d M Y') : '15 Sep 2024' }}
                                </div>
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    {{ $cr->created_at ? $cr->created_at->format('H:i:s') : '09:30:12' }}
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
                                <a href="{{ route('pmh.review', $cr) }}"
                                   class="btn text-white fw-bold px-3 py-1 d-inline-flex align-items-center gap-1 shadow-sm"
                                   style="background: #2563EB; border-radius: 999px; font-size: 0.78rem; border: none;">
                                    <span>Review</span>
                                    <i class="bi bi-chevron-right" style="font-size: 0.7rem;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="bi bi-inbox fs-2 text-muted"></i></div>
                                <div class="fw-bold">Tidak ada Change Request pada kategori ini</div>
                                <div class="small mt-1">Gunakan tombol "+ Ajukan CR Baru" untuk membuat pengajuan baru.</div>
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
