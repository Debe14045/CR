@extends('layouts.app')

@php
    $currentRoute = Route::currentRouteName() ?: 'presales.change-requests';
    $statusParam = strtolower(request('status', ''));

    // Dynamic title mapping based on selected status
    $statusTitleMap = [
        'diajukan'     => 'Diajukan',
        'awaiting_pm'  => 'Diajukan',
        'awaiting_pmh' => 'Diajukan',
        'persetujuan'  => 'Diajukan',
        'analisa'      => 'Analysis',
        'analysis'     => 'Analysis',
        'dianalisis'   => 'Analysis',
        'development'  => 'Develop',
        'develop'      => 'Develop',
        'dikerjakan'   => 'Develop',
        'golive'       => 'Go Live',
        'go-live'      => 'Go Live',
        'go_live'      => 'Go Live',
        'selesai'      => 'Go Live',
    ];

    if ($statusParam && isset($statusTitleMap[$statusParam])) {
        $computedTitle = $statusTitleMap[$statusParam];
        $computedSubtitle = 'Pantau seluruh permintaan perubahan berstatus ' . $computedTitle;
    } else {
        $computedTitle = $pageTitle ?? 'Change Request';
        $computedSubtitle = 'Pantau seluruh permintaan perubahan Anda';
    }

    $statusDisplay = match($statusParam) {
        'diajukan', 'awaiting_pm', 'awaiting_pmh', 'persetujuan' => 'Diajukan',
        'analisa', 'analysis', 'dianalisis' => 'Analysis',
        'development', 'develop', 'dikerjakan' => 'Develop',
        'golive', 'go-live', 'go_live', 'selesai' => 'Go Live',
        default => request('status') ? ucfirst(str_replace('_', ' ', request('status'))) : 'Semua Status'
    };
@endphp

@section('title', $computedTitle . ' • Presales Monitoring CR')

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

    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="color: #000000; font-size: 2.15rem; font-weight: 800; letter-spacing: -0.025em; line-height: 1.2;">
            {{ $computedTitle }}
        </h1>
        <p class="mb-0" style="font-size: 0.88rem; color: #64748B; font-weight: 500;">{{ $computedSubtitle }}</p>
    </div>

    <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">
        <form method="GET" action="{{ route($currentRoute) }}" class="m-0" style="flex: 1; max-width: 440px;">
            <div style="position: relative; display: flex; align-items: center;">
                <i class="bi bi-search" style="position: absolute; left: 18px; color: #94A3B8; font-size: 0.95rem; pointer-events: none;"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search task or CR number..."
                       class="form-control search-input-pm"
                       style="background: #FFFFFF; border: 1.5px solid #CBD5E1; border-radius: 9999px; padding: 0.55rem 1.25rem 0.55rem 2.85rem; font-size: 0.88rem; color: #1E293B; box-shadow: none;">
            </div>
            @if(request()->filled('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
        </form>

        <div class="dropdown">
            <button class="btn btn-white bg-white dropdown-toggle px-3 py-2 d-flex align-items-center justify-content-between shadow-none"
                    type="button" id="statusFilterBtn" data-bs-toggle="dropdown" aria-expanded="false"
                    style="border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.88rem; font-weight: 600; color: #475569; min-width: 160px; height: 42px; gap: 0.85rem;">
                <span>{{ $statusDisplay }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border p-1 mt-1" style="border-radius: 10px; min-width: 170px; border-color: #E2E8F0;" aria-labelledby="statusFilterBtn">
                <li><a class="dropdown-item py-1.5 px-3 small {{ empty($statusParam) ? 'active fw-bold' : '' }}" href="{{ route($currentRoute, array_merge(request()->except('page', 'status'), [])) }}">Semua Status</a></li>
                <li><a class="dropdown-item py-1.5 px-3 small {{ in_array($statusParam, ['diajukan', 'awaiting_pm', 'awaiting_pmh', 'persetujuan']) ? 'active fw-bold' : '' }}" href="{{ route($currentRoute, array_merge(request()->except('page'), ['status' => 'diajukan'])) }}">Diajukan</a></li>
                <li><a class="dropdown-item py-1.5 px-3 small {{ in_array($statusParam, ['analisa', 'analysis', 'dianalisis']) ? 'active fw-bold' : '' }}" href="{{ route($currentRoute, array_merge(request()->except('page'), ['status' => 'analisa'])) }}">Analysis</a></li>
                <li><a class="dropdown-item py-1.5 px-3 small {{ in_array($statusParam, ['development', 'develop', 'dikerjakan']) ? 'active fw-bold' : '' }}" href="{{ route($currentRoute, array_merge(request()->except('page'), ['status' => 'development'])) }}">Develop</a></li>
                <li><a class="dropdown-item py-1.5 px-3 small {{ in_array($statusParam, ['golive', 'go-live', 'go_live', 'selesai']) ? 'active fw-bold' : '' }}" href="{{ route($currentRoute, array_merge(request()->except('page'), ['status' => 'golive'])) }}">Go Live</a></li>
            </ul>
        </div>
    </div>

    <div style="height: 2.5px; background: #BAE6FD; border-radius: 999px; margin-bottom: 1.5rem;"></div>

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
                    @if (!empty($isEmptyState))
                        {{-- Baris kosong untuk halaman placeholder Butuh Persetujuan & Development CR --}}
                        @for ($i = 1; $i <= 5; $i++)
                            <tr style="height: 52px; background: {{ $i % 2 === 0 ? '#F8FAFC' : '#FFFFFF' }};">
                                <td class="px-3 text-center text-muted" style="font-size: 0.85rem;">{{ $i }}</td>
                                <td class="px-3 text-muted" style="font-size: 0.85rem;">-</td>
                                <td class="px-3 text-muted" style="font-size: 0.85rem;">-</td>
                                <td class="px-3 text-muted" style="font-size: 0.85rem;">-</td>
                                <td class="px-3 text-center text-muted" style="font-size: 0.85rem;">-</td>
                                <td class="px-3 text-center text-muted" style="font-size: 0.85rem;">-</td>
                                <td class="px-3 text-center text-muted" style="font-size: 0.85rem;">-</td>
                                <td class="px-3 text-center text-muted" style="font-size: 0.85rem;">-</td>
                            </tr>
                        @endfor
                        <tr>
                            <td colspan="8" class="text-center py-4" style="border: none !important; background: transparent;">
                                <div class="text-muted small py-2">
                                    <i class="bi bi-info-circle me-1 text-primary"></i>
                                    {{ $emptyMessage ?? 'Baris kosong. Halaman ini disiapkan untuk tahapan alur kerja berikutnya. Silakan gunakan filter di halaman Semua CR.' }}
                                </div>
                            </td>
                        </tr>
                    @else
                        @forelse ($changeRequests as $index => $cr)
                            @php
                                $statusKey = strtolower($cr->status);
                                $isUrgent = in_array(strtolower($cr->prioritas ?? ''), ['kritis', 'tinggi', 'urgent']);
                                $clientCompany = $cr->client?->company ?? ($cr->klien ?: 'PT Maju Bersama');
                                $companyClean = trim(preg_replace('/^(PT|CV|PT\.)\s+/i', '', $clientCompany));
                                $words = preg_split('/\s+/', $companyClean);
                                if (count($words) >= 2) {
                                    $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                } else {
                                    $initials = strtoupper(substr($companyClean, 0, 2));
                                }
                                $colorSeed = abs(crc32($clientCompany)) % 4;
                                $avatarBg = match($colorSeed) {
                                    0 => '#8B5CF6',
                                    1 => '#EF4444',
                                    2 => '#10B981',
                                    default => '#6366F1',
                                };
                            @endphp
                            <tr>
                            <td class="px-3 text-center" style="font-size: 0.88rem; font-weight: 600; color: #1E293B;">
                                {{ $changeRequests->firstItem() ? ($changeRequests->firstItem() + $index) : ($index + 1) }}
                            </td>
                            <td class="px-3 fw-bold text-dark" style="font-size: 0.88rem; font-weight: 700;">
                                {{ $cr->nama_pm ?: ($cr->user?->name ?: 'Danendra dada') }}
                            </td>
                            <td class="px-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
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
                                    <div class="text-dark" style="font-size: 0.88rem; line-height: 1.35; font-weight: 500;">{{ $cr->judul }}</div>
                                    <div class="text-muted" style="font-size: 0.74rem; font-weight: 400; margin-top: 2px;">{{ $cr->proyek_terkait ?: ($cr->kode_cr ?: 'System Enhancement') }}</div>
                                </div>
                            </td>
                            <td class="px-3 text-center text-secondary" style="font-size: 0.84rem; font-weight: 500; color: #475569;">
                                {{ $cr->tanggal_pengajuan ? $cr->tanggal_pengajuan->format('d M Y') : $cr->created_at->format('d M Y') }}
                            </td>
                            <td class="px-3 text-center">
                                @if (in_array($statusKey, ['diajukan', 'awaiting_pm', 'awaiting_pmh', 'persetujuan']))
                                    <span class="badge px-3 py-1 fw-bold d-inline-flex align-items-center gap-1.5"
                                          style="background: #FFF7ED; color: #D97706; border-radius: 999px; font-size: 0.74rem;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #D97706;"></span>
                                        Diajukan
                                    </span>
                                @elseif (in_array($statusKey, ['analisa', 'analysis', 'dianalisis']))
                                    <span class="badge px-3 py-1 fw-bold d-inline-flex align-items-center gap-1.5"
                                          style="background: #EFF6FF; color: #0284C7; border-radius: 999px; font-size: 0.74rem;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #0284C7;"></span>
                                        Analysis
                                    </span>
                                @elseif (in_array($statusKey, ['development', 'develop', 'dikerjakan']))
                                    <span class="badge px-3 py-1 fw-bold d-inline-flex align-items-center gap-1.5"
                                          style="background: #EDE9FE; color: #7C3AED; border-radius: 999px; font-size: 0.74rem;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #7C3AED;"></span>
                                        Develop
                                    </span>
                                @elseif (in_array($statusKey, ['golive', 'selesai']))
                                    <span class="badge px-3 py-1 fw-bold d-inline-flex align-items-center gap-1.5"
                                          style="background: #DCFCE7; color: #16A34A; border-radius: 999px; font-size: 0.74rem;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #16A34A;"></span>
                                        Go Live
                                    </span>
                                @else
                                    <span class="badge px-3 py-1 fw-bold d-inline-flex align-items-center gap-1.5"
                                          style="background: #EFF6FF; color: #0284C7; border-radius: 999px; font-size: 0.74rem;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #0284C7;"></span>
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
                                <a href="{{ route('presales.review', $cr) }}" class="btn text-white fw-bold d-inline-flex align-items-center justify-content-center gap-1 shadow-none"
                                   style="background: #0063D7; border-radius: 6px; font-size: 0.8rem; padding: 0.36rem 1.15rem; border: none; text-decoration: none; transition: background 0.15s ease;"
                                   onmouseover="this.style.background='#0052B3'"
                                   onmouseout="this.style.background='#0063D7'">
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
                    @endif
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
