@extends('layouts.app')

@section('title', 'Notifikasi • Monitoring CR')

@section('content')
<style>
    /* Styling Notifikasi Figma Standard */
    .notif-container {
        padding: 0.5rem 0.75rem 2rem 0.75rem;
    }

    .notif-divider {
        height: 1px;
        background: #E2E8F0;
        margin-top: 1.25rem;
        margin-bottom: 1.25rem;
        border: none;
    }

    .notif-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 1.15rem;
        border-radius: 9999px;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .notif-tab-btn.active {
        background: #0063D7 !important;
        color: #FFFFFF !important;
        box-shadow: 0 2px 6px rgba(0, 99, 215, 0.28);
        border-color: #0063D7;
    }

    .notif-tab-btn.inactive {
        background: #FFFFFF;
        color: #475569;
        border-color: #E2E8F0;
    }

    .notif-tab-btn.inactive:hover {
        background: #F8FAFC;
        color: #0F172A;
    }

    .notif-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 9999px;
        background: #FFFFFF;
        color: #0063D7;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .notif-search-input {
        height: 38px;
        border-radius: 9999px;
        border: 1px solid #CBD5E1;
        background: #FFFFFF;
        padding-left: 2.25rem;
        padding-right: 1rem;
        font-size: 0.82rem;
        color: #334155;
        transition: border-color 0.2s;
    }

    .notif-search-input:focus {
        border-color: #0063D7;
        box-shadow: 0 0 0 3px rgba(0, 99, 215, 0.12);
        outline: none;
    }

    .notif-list-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        overflow: hidden;
    }

    .notif-row {
        padding: 1.15rem 1.5rem;
        border-bottom: 1px solid #F1F5F9;
        transition: background 0.15s ease;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .notif-row:last-child {
        border-bottom: none;
    }

    .notif-row:hover {
        background: #FAFCFF;
    }

    .notif-avatar-icon {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1rem;
    }

    .notif-category-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.68rem;
        font-weight: 600;
        line-height: 1.2;
    }

    .notif-code-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.68rem;
        font-weight: 600;
        background: #F0F9FF;
        color: #0284C7;
        border: 1px solid #BAE6FD;
        line-height: 1.2;
        text-decoration: none;
    }

    .notif-code-pill:hover {
        background: #E0F2FE;
        color: #0369A1;
    }

    .btn-buka-detail {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.32rem 0.85rem;
        border-radius: 8px;
        background: #F0F9FF;
        border: 1px solid #BAE6FD;
        color: #0284C7;
        font-size: 0.74rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-buka-detail:hover {
        background: #0063D7;
        color: #FFFFFF;
        border-color: #0063D7;
    }

    .btn-action-trash {
        background: transparent;
        border: none;
        color: #94A3B8;
        padding: 0.25rem;
        border-radius: 6px;
        font-size: 0.95rem;
        cursor: pointer;
        transition: color 0.2s;
    }

    .btn-action-trash:hover {
        color: #EF4444;
    }

    .btn-bersihkan {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 1rem;
        border-radius: 10px;
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        color: #475569;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-bersihkan:hover {
        background: #F8FAFC;
        color: #0F172A;
        border-color: #CBD5E1;
    }

    .btn-mark-all {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 1rem;
        border-radius: 10px;
        background: #F0F9FF;
        border: 1px solid #BAE6FD;
        color: #0063D7;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-mark-all:hover {
        background: #0063D7;
        color: #FFFFFF;
    }
</style>

<div class="notif-container">

    {{-- Breadcrumb: Dashboard / Notifikasi --}}
    <nav class="d-flex align-items-center gap-1.5 mb-2 text-muted" style="font-size: 0.78rem;">
        <a href="{{ route('change-requests.index') }}" class="text-decoration-none d-flex align-items-center gap-1" style="color: #64748B;">
            <i class="bi bi-house-door" style="font-size: 0.85rem;"></i>
            <span>Dashboard</span>
        </a>
        <span style="color: #94A3B8;">/</span>
        <span style="color: #0063D7; font-weight: 600;">Notifikasi</span>
    </nav>

    {{-- Header Title + Button Actions --}}
    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
        <div>
            <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.65rem; font-weight: 800; letter-spacing: -0.02em;">
                Notifikasi
            </h1>
            <p class="mb-0" style="font-size: 0.85rem; color: #64748B;">
                Pemberitahuan pembaruan status, persetujuan, dan aktivitas Change Request Anda.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if($unreadCount > 0)
                <form action="{{ route('notifications.markAllRead') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn-mark-all">
                        <i class="bi bi-check2-all"></i>
                        <span>Tandai Semua Dibaca</span>
                    </button>
                </form>
            @endif

            <form action="{{ route('notifications.clearRead') }}" method="POST" class="m-0"
                  onsubmit="return confirm('Bersihkan semua riwayat notifikasi yang telah dibaca?');">
                @csrf
                <button type="submit" class="btn-bersihkan">
                    <i class="bi bi-trash3 text-muted"></i>
                    <span>Bersihkan</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 py-2.5 px-3.5 small d-flex align-items-center gap-2 mt-3 mb-0"
             style="border-radius: 12px; background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0 !important;" role="alert">
            <i class="bi bi-check-circle-fill text-success"></i>
            <div class="small fw-semibold">{{ session('success') }}</div>
            <button type="button" class="btn-close btn-close-sm ms-auto" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.75rem;"></button>
        </div>
    @endif

    {{-- Divider garis abu-abu tipis di atas filter dan search bar --}}
    <div class="notif-divider"></div>

    {{-- Filter Toolbar: Tabs ("Semua (count)", "Belum Dibaca") & Search Input --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('notifications.index', ['tab' => 'all', 'search' => request('search')]) }}"
               class="notif-tab-btn {{ $tab !== 'unread' ? 'active' : 'inactive' }}">
                <span>Semua</span>
                <span class="notif-count-badge">{{ $totalCount }}</span>
            </a>

            <a href="{{ route('notifications.index', ['tab' => 'unread', 'search' => request('search')]) }}"
               class="notif-tab-btn {{ $tab === 'unread' ? 'active' : 'inactive' }}">
                <span>Belum Dibaca</span>
                @if($unreadCount > 0)
                    <span class="notif-count-badge" style="background: #EF4444; color: #FFFFFF;">{{ $unreadCount }}</span>
                @endif
            </a>
        </div>

        <form action="{{ route('notifications.index') }}" method="GET" class="position-relative" style="min-width: 260px; max-width: 320px; width: 100%; margin: 0;">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <i class="bi bi-search position-absolute text-muted" style="left: 0.9rem; top: 50%; transform: translateY(-50%); font-size: 0.85rem;"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control notif-search-input"
                   placeholder="Cari notifikasi / kode CR..."
                   onkeydown="if(event.key === 'Enter') this.form.submit();">
            @if(request('search'))
                <a href="{{ route('notifications.index', ['tab' => $tab]) }}" class="position-absolute text-muted"
                   style="right: 0.9rem; top: 50%; transform: translateY(-50%); text-decoration: none;" title="Hapus Pencarian">
                    <i class="bi bi-x-circle-fill"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- Notification List Card --}}
    <div class="notif-list-card shadow-sm mb-3">
        @forelse ($notifications as $notif)
            @php
                $theme = $notif->getColorTheme();
                $targetUrl = $notif->action_url ?: ($notif->change_request_id ? route('change-requests.show', $notif->change_request_id) : null);
            @endphp
            <div class="notif-row {{ !$notif->is_read ? 'unread-bg' : '' }}"
                 style="background: {{ !$notif->is_read ? '#F8FAFC' : '#FFFFFF' }}; border-left: 4px solid {{ !$notif->is_read ? $theme['icon_color'] : 'transparent' }} !important;">
                
                {{-- Leading Circular Icon --}}
                <div class="notif-avatar-icon" style="background: {{ $theme['icon_bg'] }}; color: {{ $theme['icon_color'] }};">
                    <i class="bi {{ $notif->icon }}"></i>
                </div>

                {{-- Content Body --}}
                <div class="flex-grow-1 min-w-0">
                    {{-- Row 1: Title, Category Badge, Code Badge & Relative Time on far right --}}
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-1 flex-wrap">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="fw-bold" style="font-size: 0.92rem; color: #0F172A; font-weight: 800;">
                                {{ $notif->title }}
                            </span>

                            <span class="notif-category-pill"
                                  style="background: {{ $theme['icon_bg'] }}; color: {{ $theme['icon_color'] }}; border: 1px solid {{ $theme['border'] }};">
                                {{ $notif->category_label }}
                            </span>

                            @if($notif->changeRequest)
                                <a href="{{ route('notifications.go', $notif->id) }}" class="notif-code-pill" title="Lihat CR {{ $notif->changeRequest->kode_cr }}">
                                    {{ $notif->changeRequest->kode_cr }}
                                </a>
                            @endif

                            @if(!$notif->is_read)
                                <span style="width: 7px; height: 7px; border-radius: 50%; background: #EF4444; display: inline-block;" title="Belum dibaca"></span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.74rem; color: #64748B;">
                            <i class="bi bi-clock"></i>
                            <span>{{ $notif->formatted_time }}</span>
                        </div>
                    </div>

                    {{-- Row 2: Description --}}
                    <p class="mb-2" style="font-size: 0.81rem; line-height: 1.55; color: #64748B;">
                        {{ $notif->message }}
                    </p>

                    {{-- Row 3: Action Buttons & Delete Trash Icon on bottom right --}}
                    <div class="d-flex justify-content-between align-items-center pt-1">
                        <div class="d-flex align-items-center gap-2">
                            @if($targetUrl)
                                <a href="{{ route('notifications.go', $notif->id) }}" class="btn-buka-detail">
                                    <span>Buka Detail CR</span>
                                    <i class="bi bi-arrow-right" style="font-size: 0.76rem;"></i>
                                </a>
                            @endif

                            @if(!$notif->is_read)
                                <form action="{{ route('notifications.markAsRead', $notif->id) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-link text-muted p-0 text-decoration-none d-inline-flex align-items-center gap-1"
                                            style="font-size: 0.74rem;">
                                        <i class="bi bi-check2"></i>
                                        <span>Tandai Dibaca</span>
                                    </button>
                                </form>
                            @endif
                        </div>

                        <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST" class="m-0"
                              onsubmit="return confirm('Hapus notifikasi ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action-trash" title="Hapus Notifikasi">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bell-slash fs-2 d-block mb-2 text-secondary"></i>
                <div class="fw-bold text-dark" style="font-size: 0.95rem;">Tidak ada notifikasi saat ini.</div>
                <div class="small text-muted mt-1">
                    @if(request('search'))
                        Tidak ditemukan notifikasi untuk pencarian "<strong>{{ request('search') }}</strong>".
                    @elseif($tab === 'unread')
                        Semua notifikasi telah dibaca. Tidak ada notifikasi baru.
                    @else
                        Semua aktivitas Change Request Anda telah terupdate.
                    @endif
                </div>
                @if(request('search') || $tab !== 'all')
                    <div class="mt-3">
                        <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-primary px-3 py-1.5" style="border-radius: 8px; font-weight: 600; font-size: 0.8rem;">
                            Tampilkan Semua
                        </a>
                    </div>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Footer Count & Pagination --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-muted" style="font-size: 0.75rem; color: #64748B;">
            @if ($notifications->total() > 0)
                Menampilkan {{ $notifications->firstItem() ?? 1 }}-{{ $notifications->lastItem() ?? $notifications->total() }} dari {{ $notifications->total() }} notifikasi
            @else
                Menampilkan 0 dari 0 notifikasi
            @endif
        </div>
        <div>
            {{ $notifications->links() }}
        </div>
    </div>

</div>
@endsection
