@props([
    'currentRole' => auth()->user()?->role ?? 'client',
])

@php
    $currentRouteName = Route::currentRouteName() ?? '';
    $isNotificationsActive = request()->routeIs('notifications.*');
    $isDashboardActive = !$isNotificationsActive && ((request()->routeIs('change-requests.index') && (!request()->has('view') || request('view') === 'dashboard')) || request()->is('dashboard'));
    $isDevelopmentActive = request('view') === 'development' || request('status') === 'development';
    $isNeedApprovalActive = request('status') === 'diajukan' || request('status') === 'awaiting_pm';
    $isSemuaActive = !$isDashboardActive && !$isNotificationsActive && !$isNeedApprovalActive && (request('view') === 'total' || request()->routeIs('change-requests.show') || (!request()->has('view') && !request()->has('status')) || $isDevelopmentActive);
    $isCrActive = request()->routeIs('change-requests.*') && !$isDashboardActive;
@endphp

{{-- ══════════════════════════════════════════════════════════════
     Sidebar Component (Pure Native HTML/CSS)
     Background: #02376A Dark Royal Navy
     Logo: PT. ITPI Technology
     Menu: Dashboard, Change Request (Semua, Ajukan, Draft), Notifikasi, Pengaturan
══════════════════════════════════════════════════════════════ --}}
<aside class="app-sidebar" id="appSidebar" role="navigation" aria-label="Navigasi Utama" style="background: #02376A; border-right: 1px solid rgba(255,255,255,0.06); width: 235px;">

    {{-- Brand / Logo (PT. ITPI Technology) --}}
    <div class="sidebar-header" style="padding: 1.35rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06);">
        <a class="sidebar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ route('change-requests.index') }}">
            <img src="{{ asset('images/itpi_logo_white_tight.png') }}" alt="ITPI" style="height: 34px; width: auto; object-fit: contain;">
            <div style="color: #FFFFFF; font-weight: 800; font-size: 0.95rem; letter-spacing: -0.01em; white-space: nowrap;">
                PT. ITPI Technology
            </div>
        </a>
    </div>

    {{-- Navigation List --}}
    <nav class="sidebar-nav" style="padding: 1.1rem 0.85rem;">

        {{-- Section: MENU --}}
        <span class="nav-section-label" data-i18n="menu_header" style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em; color: rgba(255,255,255,0.7); padding: 0.2rem 0.75rem 0.6rem; text-transform: uppercase; display: block;">
            MENU
        </span>

        {{-- 1. Dashboard (Active Blue Pill when active) --}}
        <a href="{{ route('change-requests.index') }}"
           class="sidebar-link"
           style="background: {{ $isDashboardActive ? '#0063D7 !important' : 'transparent !important' }}; border-radius: 9999px; padding: 0.62rem 1.15rem; font-size: 0.9rem; color: #FFFFFF; display: flex; align-items: center; gap: 0.75rem;">
            <i class="bi bi-grid-fill nav-icon" style="font-size: 1.05rem; color: #FFFFFF;"></i>
            <span class="{{ $isDashboardActive ? 'fw-bold' : 'fw-semibold' }}" data-i18n="nav_dashboard" style="color: #FFFFFF;">Dashboard</span>
        </a>

        {{-- 2. Change Request group with chevron up --}}
        <div class="nav-group-toggle open"
             id="crGroupToggle"
             role="button"
             onclick="this.classList.toggle('open'); document.getElementById('crGroupBody').classList.toggle('open'); const c = this.querySelector('.toggle-chevron'); if(c){ c.classList.toggle('bi-chevron-up'); c.classList.toggle('bi-chevron-down'); }"
             aria-expanded="true"
             style="border-radius: 9999px; padding: 0.62rem 1.15rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.75rem; color: #FFFFFF; background: {{ $isCrActive ? '#0063D7 !important' : 'transparent !important' }}; margin-top: 0.35rem;">
            <i class="bi bi-file-earmark-text nav-icon" style="font-size: 1.05rem; opacity: 1;"></i>
            <span class="fw-bold" data-i18n="nav_cr" style="letter-spacing: -0.01em;">Change Request</span>
            <i class="bi bi-chevron-up toggle-chevron ms-auto" style="font-size: 0.8rem; font-weight: 700; opacity: 0.95; transform: none !important;"></i>
        </div>

        {{-- Submenu: Role-Aware (PM: Semua CR, Butuh Persetujuan, Development CR | Client: Semua CR, Ajukan CR, Draft CR) --}}
        <div class="nav-group-body sidebar-sub open" id="crGroupBody" style="display: flex; flex-direction: column; gap: 0.35rem; margin-top: 0.4rem; padding-left: 0;">
            @php
                $isPmRole = in_array($currentRole, ['pm', 'pmh']) || request('role') === 'pm' || (auth()->check() && auth()->user()->isPm());
                $isCreateActive = request()->routeIs('change-requests.create') || request()->routeIs('change-requests.edit');
                $isDraftActive = request('view') === 'draft';
                $isNeedApprovalActive = request('status') === 'diajukan' || request('status') === 'awaiting_pm';
                $isDevActive = request('view') === 'development' || request('status') === 'development';
                $isSemuaActiveSub = !$isDashboardActive && !$isCreateActive && !$isDraftActive && !$isNeedApprovalActive && !$isDevActive && !request()->routeIs('profile.index');
            @endphp

            {{-- Submenu matching Figma & screenshot: Semua CR, Ajukan CR, Draft CR --}}
            {{-- 1. Semua CR --}}
            <a href="{{ route('change-requests.index', ['view' => 'total']) }}"
               class="sidebar-link"
               style="background: transparent !important; padding: 0.48rem 1.15rem; font-size: 0.88rem; color: {{ $isSemuaActiveSub ? '#FFFFFF' : 'rgba(255,255,255,0.85)' }}; display: flex; align-items: center; position: relative;">
                @if ($isSemuaActiveSub)
                    <span style="position: absolute; left: 16px; width: 7.5px; height: 7.5px; border-radius: 50%; background: #00B4FF; box-shadow: 0 0 8px 1px #00B4FF; display: inline-block;"></span>
                @endif
                <i class="bi bi-file-earmark-text nav-icon" style="font-size: 1.05rem; color: #FFFFFF; margin-left: 18px; margin-right: 0.7rem;"></i>
                <span class="{{ $isSemuaActiveSub ? 'fw-bold' : 'fw-normal' }}" data-i18n="nav_all_cr" style="color: #FFFFFF; letter-spacing: -0.01em;">Semua CR</span>
            </a>

            {{-- 2. Ajukan CR --}}
            <a href="{{ route('change-requests.create') }}"
               class="sidebar-link"
               style="background: transparent !important; padding: 0.48rem 1.15rem; font-size: 0.88rem; color: {{ $isCreateActive ? '#FFFFFF' : 'rgba(255,255,255,0.85)' }}; display: flex; align-items: center; position: relative;">
                @if ($isCreateActive)
                    <span style="position: absolute; left: 16px; width: 7.5px; height: 7.5px; border-radius: 50%; background: #00B4FF; box-shadow: 0 0 8px 1px #00B4FF; display: inline-block;"></span>
                @endif
                <i class="bi bi-file-earmark-diff nav-icon" style="font-size: 1rem; color: #FFFFFF; margin-left: 18px; margin-right: 0.7rem;"></i>
                <span class="{{ $isCreateActive ? 'fw-bold' : 'fw-normal' }}" data-i18n="nav_submit_cr" style="color: #FFFFFF;">Ajukan CR</span>
            </a>

            {{-- 3. Draft CR --}}
            <a href="{{ route('change-requests.index', ['view' => 'draft']) }}"
               class="sidebar-link"
               style="background: transparent !important; padding: 0.48rem 1.15rem; font-size: 0.88rem; color: {{ $isDraftActive ? '#FFFFFF' : 'rgba(255,255,255,0.85)' }}; display: flex; align-items: center; position: relative;">
                @if ($isDraftActive)
                    <span style="position: absolute; left: 16px; width: 7.5px; height: 7.5px; border-radius: 50%; background: #00B4FF; box-shadow: 0 0 8px 1px #00B4FF; display: inline-block;"></span>
                @endif
                <i class="bi bi-file-earmark-bar-graph nav-icon" style="font-size: 1rem; color: #FFFFFF; margin-left: 18px; margin-right: 0.7rem;"></i>
                <span class="{{ $isDraftActive ? 'fw-bold' : 'fw-normal' }}" data-i18n="nav_draft_cr" style="color: #FFFFFF;">Draft CR</span>
            </a>
        </div>

        {{-- 3. Notifikasi --}}
        <a href="{{ route('notifications.index') }}"
           class="sidebar-link d-flex align-items-center"
           style="background: {{ $isNotificationsActive ? '#0063D7 !important' : 'transparent !important' }}; border-radius: 9999px; padding: 0.62rem 1.15rem; font-size: 0.9rem; color: #FFFFFF; margin-top: 0.35rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="bi bi-bell{{ $isNotificationsActive ? '-fill' : '' }} nav-icon" style="font-size: 1.05rem; color: #FFFFFF;"></i>
            <span class="{{ $isNotificationsActive ? 'fw-bold' : 'fw-semibold' }}" data-i18n="nav_notifications" style="color: #FFFFFF;">Notifikasi</span>
            @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                <span class="badge bg-danger rounded-pill ms-auto px-2 py-0.5" style="font-size: 0.68rem; font-weight: 700;">
                    {{ $unreadNotificationsCount }}
                </span>
            @endif
        </a>

        {{-- 4. Pengaturan --}}
        @if ($currentRole === 'admin')
            <div class="nav-group-toggle {{ str_starts_with($currentRouteName, 'master.') ? 'open' : '' }}"
                 id="settingsGroupToggle"
                 role="button"
                 onclick="this.classList.toggle('open'); document.getElementById('settingsGroupBody').classList.toggle('open');"
                 aria-expanded="{{ str_starts_with($currentRouteName, 'master.') ? 'true' : 'false' }}"
                 style="border-radius: 9999px; padding: 0.62rem 1.15rem; font-size: 0.9rem; color: #FFFFFF; margin-top: 0.35rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="bi bi-gear nav-icon" style="font-size: 1.05rem; color: #FFFFFF;"></i>
                <span class="fw-semibold" data-i18n="nav_settings" style="color: #FFFFFF;">Pengaturan</span>
                <i class="bi bi-chevron-down toggle-chevron ms-auto" style="font-size: 0.75rem; opacity: 0.6;"></i>
            </div>

            <div class="nav-group-body sidebar-sub {{ str_starts_with($currentRouteName, 'master.') ? 'open' : '' }}" id="settingsGroupBody" style="padding-left: 1.2rem; display: flex; flex-direction: column; gap: 0.3rem;">
                <a href="{{ route('master.clients.index') }}"
                   class="sidebar-link {{ str_starts_with($currentRouteName, 'master.clients') ? 'active' : '' }}"
                   style="border-radius: 8px; padding: 0.45rem 0.75rem; font-size: 0.84rem;">
                    <i class="bi bi-buildings nav-icon"></i>
                    <span data-i18n="master_clients">Master Client</span>
                </a>
                <a href="{{ route('master.pegawai.index') }}"
                   class="sidebar-link {{ str_starts_with($currentRouteName, 'master.pegawai') ? 'active' : '' }}"
                   style="border-radius: 8px; padding: 0.45rem 0.75rem; font-size: 0.84rem;">
                    <i class="bi bi-person-badge nav-icon"></i>
                    <span data-i18n="master_pegawai">Master Pegawai</span>
                </a>
                <a href="{{ route('master.status.index') }}"
                   class="sidebar-link {{ str_starts_with($currentRouteName, 'master.status') ? 'active' : '' }}"
                   style="border-radius: 8px; padding: 0.45rem 0.75rem; font-size: 0.84rem;">
                    <i class="bi bi-list-check nav-icon"></i>
                    <span data-i18n="master_status">Master Status</span>
                </a>
            </div>
        @else
            <a href="{{ route('profile.index') }}"
               class="sidebar-link d-flex align-items-center {{ request()->routeIs('profile.index') ? 'active' : '' }}"
               style="background: {{ request()->routeIs('profile.index') ? '#0063D7 !important' : 'transparent !important' }}; border-radius: 9999px; padding: 0.62rem 1.15rem; font-size: 0.9rem; color: #FFFFFF; margin-top: 0.35rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="bi bi-gear nav-icon" style="font-size: 1.05rem; color: #FFFFFF;"></i>
                <span class="fw-semibold" data-i18n="nav_settings" style="color: #FFFFFF;">Pengaturan</span>
            </a>
        @endif

    </nav>
</aside>
