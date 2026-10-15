@extends('layouts.app')

@php
    $currentRoute = Route::currentRouteName() ?: 'pmh.change-requests';
    $curTab = $activeTab ?? request('tab', 'semua');
    $statusParam = strtolower(request('status', ''));

    // Dynamic title mapping based on clicked status or tab
    $statusTitleMap = [
        'diajukan' => 'Diajukan',
        'awaiting_pm' => 'Diajukan',
        'awaiting_pmh' => 'Diajukan',
        'persetujuan' => 'Diajukan',
        'analisa' => 'Analysis',
        'analysis' => 'Analysis',
        'dianalisis' => 'Analysis',
        'development' => 'Develop',
        'develop' => 'Develop',
        'dikerjakan' => 'Develop',
        'golive' => 'Go Live',
        'go-live' => 'Go Live',
        'selesai' => 'Go Live',
    ];

    if ($statusParam && isset($statusTitleMap[$statusParam])) {
        $computedTitle = $statusTitleMap[$statusParam];
        $computedSubtitle = 'Pantau seluruh permintaan perubahan berstatus ' . $computedTitle;
    } elseif ($curTab === 'aktif') {
        $computedTitle = 'CR Aktif';
        $computedSubtitle = 'Pantau seluruh permintaan perubahan yang sedang aktif berjalan';
    } elseif ($curTab === 'selesai') {
        $computedTitle = 'CR Selesai';
        $computedSubtitle = 'Daftar riwayat seluruh permintaan perubahan yang telah selesai';
    } else {
        $computedTitle = $pageTitle ?? 'Change Request';
        $computedSubtitle = 'Pantau seluruh permintaan perubahan Anda';
    }

    $statusDisplay = match($statusParam) {
        'diajukan', 'awaiting_pm', 'persetujuan' => 'Diajukan',
        'analisa', 'analysis' => 'Analysis',
        'development', 'develop' => 'Develop',
        'golive', 'go-live' => 'Go Live',
        default => request('status') ? ucfirst(str_replace('_', ' ', request('status'))) : 'Semua Status'
    };
@endphp

@section('title', $computedTitle . ' • PM Head Monitoring CR')

@section('content')
<style>
    .pm-table-container {
        border: 1px solid #D0DCEB !important;
        border-radius: 14px !important;
        background: #FFFFFF !important;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .pm-table thead th {
        background-color: #EBF2FA !important;
        color: #0F172A !important;
        font-weight: 700 !important;
        font-size: 0.72rem !important;
        letter-spacing: 0.05em !important;
        padding-top: 0.9rem !important;
        padding-bottom: 0.9rem !important;
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
        border-radius: 6px;
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

    /* Segmented Tab Bar - Full Bleed Block */
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
        background: #0063D7 !important;
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
        <h1 class="fw-bold mb-1" style="color: #000000; font-size: 2.15rem; font-weight: 800; letter-spacing: -0.025em; line-height: 1.2;">
            {{ $computedTitle }}
        </h1>
        <p class="mb-0" style="font-size: 0.88rem; color: #64748B; font-weight: 500;">{{ $computedSubtitle }}</p>
    </div>

    <div class="pm-filter-container">
        <a href="{{ route($currentRoute, array_merge(request()->except('page', 'tab'), ['tab' => 'semua'])) }}"
           class="pm-filter-tab {{ $curTab === 'semua' ? 'active' : '' }}"
           style="{{ $curTab === 'semua' ? 'border-top-left-radius: 11px; border-bottom-left-radius: 11px;' : '' }}">
            Semua CR
        </a>
        <div class="pm-filter-divider" style="{{ ($curTab === 'semua' || $curTab === 'aktif') ? 'visibility: hidden;' : '' }}"></div>
        <a href="{{ route($currentRoute, array_merge(request()->except('page', 'tab'), ['tab' => 'aktif'])) }}"
           class="pm-filter-tab {{ $curTab === 'aktif' ? 'active' : '' }}">
            CR Aktif
        </a>
        <div class="pm-filter-divider" style="{{ ($curTab === 'aktif' || $curTab === 'selesai') ? 'visibility: hidden;' : '' }}"></div>
        <a href="{{ route($currentRoute, array_merge(request()->except('page', 'tab'), ['tab' => 'selesai'])) }}"
           class="pm-filter-tab {{ $curTab === 'selesai' ? 'active' : '' }}"
           style="{{ $curTab === 'selesai' ? 'border-top-right-radius: 11px; border-bottom-right-radius: 11px;' : '' }}">
            CR Selesai
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">
        <form method="GET" action="{{ route($currentRoute) }}" class="m-0" style="flex: 1; max-width: 380px;">
            <input type="hidden" name="tab" value="{{ $curTab }}">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div style="position: relative; display: flex; align-items: center;">
                <i class="bi bi-search" style="position: absolute; left: 16px; color: #64748B; font-size: 0.92rem; pointer-events: none;"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search task or CR number..."
                       class="form-control search-input-pm"
                       style="background: #FFFFFF; border: 1.5px solid #CBD5E1; border-radius: 9999px; padding: 0.52rem 1rem 0.52rem 2.65rem; font-size: 0.88rem; color: #1E293B; box-shadow: none;">
            </div>
        </form>

        <div class="dropdown">
            <button class="btn btn-white bg-white dropdown-toggle px-3 py-2 d-flex align-items-center justify-content-between shadow-none"
                    type="button" id="statusFilterBtn" data-bs-toggle="dropdown" aria-expanded="false"
                    style="border: 1.5px solid #D6D3CD; border-radius: 10px; font-size: 0.86rem; font-weight: 600; color: #475569; min-width: 145px; gap: 0.85rem;">
                <span>{{ $statusDisplay }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border p-1 mt-1" style="border-radius: 10px; min-width: 160px; border-color: #E2E8F0;" aria-labelledby="statusFilterBtn">
                <li><a class="dropdown-item py-1.5 px-3 small fw-semibold" href="{{ route($currentRoute, array_merge(request()->except('page', 'status'), [])) }}">Semua Status</a></li>
                <li><a class="dropdown-item py-1.5 px-3 small fw-semibold" href="{{ route($currentRoute, array_merge(request()->except('page'), ['status' => 'diajukan'])) }}">Diajukan</a></li>
                <li><a class="dropdown-item py-1.5 px-3 small fw-semibold" href="{{ route($currentRoute, array_merge(request()->except('page'), ['status' => 'analisa'])) }}">Analysis</a></li>
                <li><a class="dropdown-item py-1.5 px-3 small fw-semibold" href="{{ route($currentRoute, array_merge(request()->except('page'), ['status' => 'development'])) }}">Develop</a></li>
                <li><a class="dropdown-item py-1.5 px-3 small fw-semibold" href="{{ route($currentRoute, array_merge(request()->except('page'), ['status' => 'golive'])) }}">Go Live</a></li>
            </ul>
        </div>
    </div>

    {{-- Cyan accent line matching Figma exact --}}
    <div style="height: 3px; background: #BAE6FD; border-radius: 999px; margin-bottom: 1.25rem;"></div>

    <div class="card pm-table-container mb-4">
        <div class="table-responsive m-0">
            <table class="table pm-table mb-0 align-middle" style="border-collapse: collapse; border: none !important;">
                <thead>
                    <tr>
                        <th class="py-3 px-3 text-center" style="width: 55px;">NO</th>
                        <th class="py-3 px-3">NAMA PM</th>
                        <th class="py-3 px-3">PEMOHON</th>
                        <th class="py-3 px-3">JUDUL CR</th>
                        <th class="py-3 px-3" style="width: 130px;">PENGAJUAN</th>
                        <th class="py-3 px-3" style="width: 140px;">STATUS</th>
                        <th class="py-3 px-3 text-center" style="width: 110px;">PRIORITAS</th>
                        <th class="py-3 px-4 text-center" style="width: 130px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $avatarColors = ['#8B5CF6', '#EF4444', '#10B981', '#8B5CF6', '#6366F1', '#EF4444', '#3B82F6', '#EC4899'];
                    @endphp

                    @forelse ($changeRequests as $index => $cr)
                        @php
                            $colorIndex = $index % count($avatarColors);
                            $avColor = $avatarColors[$colorIndex];

                            $companyName = $cr->klien ?: ($cr->client?->company ?: '-');

                            // Extract initials
                            $cleanCompany = trim(preg_replace('/^(pt\.?|cv\.?)\s+/i', '', $companyName));
                            $words = preg_split('/\s+/', $cleanCompany);
                            $initials = '';
                            if (!empty($words[0])) $initials .= strtoupper(substr($words[0], 0, 1));
                            if (!empty($words[1])) $initials .= strtoupper(substr($words[1], 0, 1));
                            if (!$initials) $initials = $cleanCompany ? strtoupper(substr($cleanCompany, 0, 2)) : 'CR';

                            // Status badge mapping matching Figma exactly
                            $st = strtolower($cr->status ?? '');
                            if (in_array($st, ['diajukan', 'awaiting_pm', 'awaiting_pmh', 'pending'])) {
                                $statusText = 'Diajukan';
                                $statusBg = '#FEF9C3';
                                $statusColor = '#D97706';
                                $dotColor = '#D97706';
                            } elseif (in_array($st, ['analisa', 'dianalisis', 'analysis'])) {
                                $statusText = 'Analysis';
                                $statusBg = '#E0F2FE';
                                $statusColor = '#0284C7';
                                $dotColor = '#0284C7';
                            } elseif (in_array($st, ['development', 'dikerjakan', 'develop'])) {
                                $statusText = 'Develop';
                                $statusBg = '#EDE9FE';
                                $statusColor = '#7C3AED';
                                $dotColor = '#7C3AED';
                            } elseif (in_array($st, ['golive', 'selesai', 'go-live', 'go_live', 'validated'])) {
                                $statusText = 'Go-Live';
                                $statusBg = '#DCFCE7';
                                $statusColor = '#16A34A';
                                $dotColor = '#16A34A';
                            } else {
                                $statusText = ucfirst(str_replace('_', ' ', $cr->status));
                                $statusBg = '#E0F2FE';
                                $statusColor = '#0284C7';
                                $dotColor = '#0284C7';
                            }

                            // Priority matching Figma: URGENT is soft red, NORMAL is soft green
                            $isUrgent = in_array(strtolower($cr->prioritas ?? ''), ['urgent', 'kritis', 'tinggi', 'high']);

                            $rowNo = method_exists($changeRequests, 'firstItem') ? ($changeRequests->firstItem() + $index) : ($index + 1);
                        @endphp
                        <tr>
                            {{-- NO --}}
                            <td class="py-3 px-3 text-center fw-bold text-dark" style="font-size: 0.88rem;">
                                {{ $rowNo }}
                            </td>

                            {{-- NAMA PM --}}
                            <td class="py-3 px-3 fw-bold text-dark" style="font-size: 0.88rem; font-weight: 700;">
                                {{ $cr->nama_pm ?: ($cr->user?->name ?: '-') }}
                            </td>

                            {{-- PEMOHON --}}
                            <td class="py-3 px-3">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                         style="width: 32px; height: 32px; background: {{ $avColor }}; font-size: 0.76rem; letter-spacing: -0.02em;">
                                        {{ $initials }}
                                    </div>
                                    <span class="fw-bold text-dark" style="font-size: 0.88rem;">
                                        {{ $companyName }}
                                    </span>
                                </div>
                            </td>

                            {{-- JUDUL CR --}}
                            <td class="py-3 px-3">
                                <div style="line-height: 1.3;">
                                    <div class="fw-semibold text-dark" style="font-size: 0.88rem;">
                                        {{ $cr->judul }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.74rem; margin-top: 2px;">
                                        {{ $cr->proyek_terkait ?: ($cr->main_desk ?: ($cr->kode_cr ?: 'System Enhancement')) }}
                                    </div>
                                </div>
                            </td>

                            {{-- PENGAJUAN --}}
                            <td class="py-3 px-3" style="font-size: 0.85rem; color: #334155; font-weight: 500;">
                                {{ $cr->tanggal_pengajuan ? $cr->tanggal_pengajuan->format('d M Y') : $cr->created_at->format('d M Y') }}
                            </td>

                            {{-- STATUS --}}
                            <td class="py-3 px-3">
                                <span class="d-inline-flex align-items-center gap-1.5 px-3 py-1 fw-bold"
                                      style="background: {{ $statusBg }}; color: {{ $statusColor }}; border-radius: 999px; font-size: 0.76rem;">
                                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: {{ $dotColor }};"></span>
                                    {{ $statusText }}
                                </span>
                            </td>

                            {{-- PRIORITAS --}}
                            <td class="py-3 px-3 text-center">
                                @if ($isUrgent)
                                    <span class="badge fw-bold" style="background: #FEE2E2; color: #DC2626; border-radius: 4px; font-size: 0.72rem; padding: 4px 8px; letter-spacing: 0.02em;">
                                        URGENT
                                    </span>
                                @else
                                    <span class="badge fw-bold" style="background: #DCFCE7; color: #16A34A; border-radius: 4px; font-size: 0.72rem; padding: 4px 8px; letter-spacing: 0.02em;">
                                        NORMAL
                                    </span>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('pmh.review', $cr) }}"
                                   class="btn text-white fw-bold d-inline-flex align-items-center justify-content-center gap-1 shadow-none"
                                   style="background: #0063D7; border-radius: 9999px; font-size: 0.78rem; border: none; padding: 0.38rem 1.15rem;">
                                    <span>Review</span>
                                    <i class="bi bi-chevron-right" style="font-size: 0.68rem;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="bi bi-inbox fs-2 text-muted"></i></div>
                                <div class="fw-bold">Tidak ada Change Request pada kategori ini</div>
                                <div class="small mt-1">Ubah filter status atau kata kunci pencarian Anda.</div>
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
                    <button class="btn btn-white bg-white dropdown-toggle px-2.5 py-1 d-flex align-items-center gap-2 shadow-none"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false"
                            style="border: 1.5px solid #CBD5E1; border-radius: 6px; font-size: 0.82rem; font-weight: 700; color: #0F172A; line-height: 1.2;">
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
            <div class="d-flex align-items-center gap-1.5">
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
