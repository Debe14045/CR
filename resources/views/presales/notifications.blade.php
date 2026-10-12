@extends('layouts.app')

@section('title', 'Notifikasi • Presales Monitoring CR')

@section('content')
<style>
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
    .notif-row:hover {
        background: #F8FAFC;
    }
    .notif-row:last-child {
        border-bottom: none;
    }
    .notif-icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .notif-category-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.65rem;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 700;
    }
</style>

<div class="page-shell pb-5 notif-container">

    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
        <div>
            <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.85rem; font-weight: 800; letter-spacing: -0.01em;">
                Notifikasi
            </h1>
            <p class="text-muted mb-0" style="font-size: 0.88rem; color: #64748B;">
                Pemberitahuan persetujuan penting, update tahapan proyek, dan tindak lanjut CR
            </p>
        </div>
    </div>

    <div class="notif-divider"></div>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="notif-tab-btn active" onclick="filterNotif('all', this)">
                <span>Semua</span>
                <span class="notif-count-badge">{{ count($notifications) }}</span>
            </button>
            <button type="button" class="notif-tab-btn inactive" onclick="filterNotif('Persetujuan CR', this)">
                <span>Persetujuan</span>
            </button>
            <button type="button" class="notif-tab-btn inactive" onclick="filterNotif('Penolakan CR', this)">
                <span>Penolakan</span>
            </button>
            <button type="button" class="notif-tab-btn inactive" onclick="filterNotif('Revisi CR', this)">
                <span>Revisi</span>
            </button>
            <button type="button" class="notif-tab-btn inactive" onclick="filterNotif('Perubahan Status', this)">
                <span>Status</span>
            </button>
        </div>

        <div style="position: relative; width: 280px; max-width: 100%;">
            <i class="bi bi-search" style="position: absolute; left: 14px; top: 11px; color: #94A3B8; font-size: 0.85rem;"></i>
            <input type="text" id="searchNotifInput" class="form-control notif-search-input" placeholder="Cari notifikasi..." onkeyup="searchNotif(this.value)">
        </div>
    </div>

    <div class="notif-list-card shadow-sm">
        @forelse ($notifications as $n)
            <div class="notif-row notif-item" data-category="{{ $n['category_id'] }}">
                <div class="notif-icon-circle" style="background: {{ $n['icon_bg'] }}; color: {{ $n['icon_color'] }};">
                    <i class="bi {{ $n['icon'] }}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-1 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <span class="notif-category-pill" style="background: {{ $n['badge_bg'] }}; color: {{ $n['badge_color'] }}; border: 1px solid {{ $n['badge_border'] }};">
                                {{ $n['category_id'] }}
                            </span>
                            <span class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $n['title_id'] }}</span>
                        </div>
                        <span class="text-muted small" style="font-size: 0.76rem;">{{ $n['time_id'] }}</span>
                    </div>
                    <p class="text-secondary mb-2 small" style="font-size: 0.84rem; line-height: 1.5;">
                        {{ $n['desc_id'] }}
                    </p>
                    <a href="{{ $n['url'] }}" class="btn btn-sm btn-outline-primary fw-bold px-3 py-1" style="border-radius: 6px; font-size: 0.78rem;">
                        Tinjau CR &gt;
                    </a>
                </div>
            </div>
        @empty
            <div class="p-5 text-center text-muted">
                <i class="bi bi-bell-slash fs-2 mb-2 d-block"></i>
                <div class="fw-bold">Tidak ada notifikasi baru saat ini.</div>
            </div>
        @endforelse
    </div>

</div>

<script>
    function filterNotif(category, btn) {
        document.querySelectorAll('.notif-tab-btn').forEach(b => {
            b.classList.remove('active');
            b.classList.add('inactive');
        });
        btn.classList.add('active');
        btn.classList.remove('inactive');

        const rows = document.querySelectorAll('.notif-item');
        rows.forEach(r => {
            if (category === 'all' || r.getAttribute('data-category') === category) {
                r.style.display = 'flex';
            } else {
                r.style.display = 'none';
            }
        });
    }

    function searchNotif(query) {
        query = query.toLowerCase();
        const rows = document.querySelectorAll('.notif-item');
        rows.forEach(r => {
            const text = r.textContent.toLowerCase();
            if (text.includes(query)) {
                r.style.display = 'flex';
            } else {
                r.style.display = 'none';
            }
        });
    }
</script>
@endsection
