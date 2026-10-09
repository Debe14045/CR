@extends('layouts.app')

@section('title', 'Notifikasi - PT ITPI Technology')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3" style="max-width: 1050px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb small mb-0" style="font-size: 0.8rem;">
            <li class="breadcrumb-item">
                <a href="{{ route('change-requests.index') }}" class="text-decoration-none text-muted">
                    <i class="bi bi-house-door me-1"></i>Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">
                Notifikasi
            </li>
        </ol>
    </nav>

    {{-- Simple Page Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3 pb-3 border-bottom" style="border-color: #E2E8F0 !important;">
        <div>
            <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.02em; font-size: 1.35rem;">
                Notifikasi
            </h4>
            <p class="text-muted small mb-0 mt-0.5" style="font-size: 0.82rem;">
                Pemberitahuan pembaruan status, persetujuan, dan aktivitas Change Request Anda.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if($unreadCount > 0)
                <form action="{{ route('notifications.markAllRead') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1.5 px-3 py-1.5"
                            style="border-radius: 8px; font-weight: 600; font-size: 0.8rem;">
                        <i class="bi bi-check2-all"></i>
                        <span>Tandai Semua Dibaca</span>
                    </button>
                </form>
            @endif

            <form action="{{ route('notifications.clearRead') }}" method="POST" class="m-0"
                  onsubmit="return confirm('Bersihkan riwayat notifikasi yang telah dibaca?');">
                @csrf
                <button type="submit" class="btn btn-sm btn-light text-muted border d-flex align-items-center gap-1.5 px-2.5 py-1.5"
                        style="border-radius: 8px; font-size: 0.8rem; background: #FFFFFF;" title="Bersihkan riwayat notifikasi yang sudah dibaca">
                    <i class="bi bi-trash3"></i>
                    <span class="d-none d-sm-inline">Bersihkan</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 py-2 px-3 small d-flex align-items-center gap-2 mb-3"
             style="border-radius: 10px; background: #ECFDF5; color: #065F46;" role="alert">
            <i class="bi bi-check-circle-fill text-success"></i>
            <div class="small fw-semibold">{{ session('success') }}</div>
            <button type="button" class="btn-close btn-close-sm ms-auto" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.75rem;"></button>
        </div>
    @endif

    {{-- Filter Bar & Search --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
        {{-- Tabs: Hanya Semua dan Belum Dibaca --}}
        <div class="d-flex align-items-center gap-1.5 flex-wrap">
            <a href="{{ route('notifications.index', ['tab' => 'all', 'search' => request('search')]) }}"
               class="btn btn-sm px-3 py-1.5 text-decoration-none fw-semibold"
               style="border-radius: 20px; font-size: 0.82rem; {{ $tab !== 'unread' ? 'background: #0063D7; color: #FFFFFF;' : 'background: #FFFFFF; color: #64748B; border: 1px solid #E2E8F0;' }}">
                Semua
                <span class="badge {{ $tab !== 'unread' ? 'bg-white text-primary' : 'bg-light text-secondary border' }} rounded-pill ms-1" style="font-size: 0.68rem;">
                    {{ $totalCount }}
                </span>
            </a>

            <a href="{{ route('notifications.index', ['tab' => 'unread', 'search' => request('search')]) }}"
               class="btn btn-sm px-3 py-1.5 text-decoration-none fw-semibold"
               style="border-radius: 20px; font-size: 0.82rem; {{ $tab === 'unread' ? 'background: #0063D7; color: #FFFFFF;' : 'background: #FFFFFF; color: #64748B; border: 1px solid #E2E8F0;' }}">
                Belum Dibaca
                @if($unreadCount > 0)
                    <span class="badge bg-danger text-white rounded-pill ms-1" style="font-size: 0.68rem;">
                        {{ $unreadCount }}
                    </span>
                @endif
            </a>
        </div>

        {{-- Search Input --}}
        <form action="{{ route('notifications.index') }}" method="GET" class="m-0" style="min-width: 230px;">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted ps-2.5" style="border-radius: 8px 0 0 8px; border-color: #CBD5E1;">
                    <i class="bi bi-search" style="font-size: 0.75rem;"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control bg-white border-start-0 ps-1"
                       placeholder="Cari notifikasi / kode CR..."
                       style="border-radius: 0 8px 8px 0; border-color: #CBD5E1; font-size: 0.8rem;">
                @if(request('search'))
                    <a href="{{ route('notifications.index', ['tab' => $tab]) }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Clean Notifications Feed List --}}
    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
        @if($notifications->isEmpty())
            {{-- Empty State --}}
            <div class="text-center py-5 px-3">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                     style="width: 56px; height: 56px; background: #F8FAFC; color: #94A3B8; font-size: 1.6rem;">
                    <i class="bi bi-bell-slash"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Tidak Ada Notifikasi</h6>
                <p class="text-muted small mb-3 mx-auto" style="max-width: 380px; font-size: 0.8rem; line-height: 1.45;">
                    @if(request('search'))
                        Tidak ditemukan notifikasi untuk pencarian "<strong>{{ request('search') }}</strong>".
                    @elseif($tab === 'unread')
                        Semua notifikasi telah dibaca. Tidak ada notifikasi baru saat ini.
                    @else
                        Belum ada pemberitahuan pada kategori ini.
                    @endif
                </p>
                @if(request('search') || $tab !== 'all')
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary btn-sm px-3 py-1.5" style="border-radius: 8px; font-weight: 600; font-size: 0.8rem;">
                        Tampilkan Semua
                    </a>
                @endif
            </div>
        @else
            {{-- Notification Rows --}}
            <div class="list-group list-group-flush">
                @foreach($notifications as $notif)
                    @php
                        $theme = $notif->getColorTheme();
                        $targetUrl = $notif->action_url ?: ($notif->change_request_id ? route('change-requests.show', $notif->change_request_id) : null);
                    @endphp
                    <div class="list-group-item p-3 notif-row {{ !$notif->is_read ? 'unread-bg' : '' }}"
                         style="border-color: #F1F5F9; background: {{ !$notif->is_read ? '#F8FAFC' : '#FFFFFF' }}; border-left: 4px solid {{ !$notif->is_read ? $theme['icon_color'] : 'transparent' }} !important; transition: background 0.15s ease;">
                        <div class="d-flex align-items-start gap-3">

                            {{-- Clean Icon Circle --}}
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5"
                                 style="width: 36px; height: 36px; background: {{ $theme['icon_bg'] }}; color: {{ $theme['icon_color'] }}; font-size: 1.05rem;">
                                <i class="bi {{ $notif->icon }}"></i>
                            </div>

                            {{-- Content Body --}}
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-1">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        {{-- Title --}}
                                        <span class="fw-bold text-dark" style="font-size: 0.88rem; letter-spacing: -0.01em;">
                                            {{ $notif->title }}
                                        </span>

                                        {{-- Category Badge --}}
                                        <span class="badge {{ $theme['badge'] }} rounded-pill px-2 py-0.5" style="font-size: 0.65rem; font-weight: 600;">
                                            {{ $notif->category_label }}
                                        </span>

                                        {{-- Linked CR Code Badge --}}
                                        @if($notif->changeRequest)
                                            <a href="{{ route('notifications.go', $notif->id) }}"
                                               class="badge bg-light text-primary border text-decoration-none rounded-pill px-2 py-0.5"
                                               style="font-size: 0.68rem; font-weight: 600;"
                                               title="Buka CR {{ $notif->changeRequest->kode_cr }}">
                                                {{ $notif->changeRequest->kode_cr }}
                                            </a>
                                        @endif

                                        {{-- Unread Dot Marker --}}
                                        @if(!$notif->is_read)
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #EF4444; display: inline-block;" title="Belum dibaca"></span>
                                        @endif
                                    </div>

                                    {{-- Timestamp --}}
                                    <span class="text-muted small" style="font-size: 0.73rem; white-space: nowrap;">
                                        <i class="bi bi-clock me-1"></i>{{ $notif->formatted_time }}
                                    </span>
                                </div>

                                {{-- Message Text --}}
                                <p class="mb-2 text-secondary" style="font-size: 0.82rem; line-height: 1.45;">
                                    {{ $notif->message }}
                                </p>

                                {{-- Clean Actions Row --}}
                                <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($targetUrl)
                                            <a href="{{ route('notifications.go', $notif->id) }}"
                                               class="btn btn-sm btn-light text-primary d-inline-flex align-items-center gap-1 px-2.5 py-1 border"
                                               style="border-radius: 6px; font-weight: 600; font-size: 0.75rem; background: #F0F9FF; border-color: #BAE6FD !important;">
                                                <span>Buka Detail CR</span>
                                                <i class="bi bi-arrow-right" style="font-size: 0.72rem;"></i>
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

                                    {{-- Delete Icon --}}
                                    <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST" class="m-0"
                                          onsubmit="return confirm('Hapus notifikasi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm p-0 text-muted border-0 bg-transparent opacity-50 hover-opacity-100"
                                                style="font-size: 0.78rem;" title="Hapus">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Compact Pagination Footer --}}
            <div class="px-3 py-2.5 d-flex justify-content-between align-items-center flex-wrap gap-2 border-top bg-light-subtle" style="border-color: #F1F5F9 !important;">
                <div class="text-muted small" style="font-size: 0.76rem;">
                    Menampilkan <strong>{{ $notifications->firstItem() ?? 0 }}</strong> - <strong>{{ $notifications->lastItem() ?? 0 }}</strong> dari <strong>{{ $notifications->total() }}</strong> notifikasi
                </div>
                <div>
                    {{ $notifications->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

<style>
.notif-row:hover {
    background-color: #F8FAFC !important;
}
.hover-opacity-100:hover {
    opacity: 1 !important;
    color: #DC2626 !important;
}
</style>
@endsection
