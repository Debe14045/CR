@props([
    'currentRole' => auth()->user()?->role ?? 'client',
])

@php
    $isDashboardActive = (request()->routeIs('change-requests.index') && (!request()->has('view') || request('view') === 'dashboard')) || request()->is('dashboard');
@endphp

{{-- ══════════════════════════════════════════════════════════════
     TopNavbar Component (Pure Native HTML/CSS)
     Background: #FFFFFF, Height: 58px, Border: 1px solid #E2E8F0
     Actions: Language Pill, Inbox + Red Dot, Bell + Red Dot, Divider, User TA
══════════════════════════════════════════════════════════════ --}}
<header class="main-topbar" style="background: {{ request()->routeIs('change-requests.show') ? '#F0F3F6' : (request('view') === 'draft' ? '#F4F6F8' : (request()->routeIs('change-requests.create', 'change-requests.edit') || $isDashboardActive ? '#FFFFFF' : '#F0F3F6')) }}; border-bottom: {{ request('view') === 'draft' ? 'none' : '1px solid #E2E8F0' }}; height: 58px; padding: 0 2rem; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 1020; box-shadow: {{ request('view') === 'draft' ? 'none' : '0 1px 4px rgba(15,23,42,0.04)' }}">
    {{-- Mobile hamburger --}}
    <button class="topbar-hamburger me-2 d-lg-none" id="sidebarToggleBtn" type="button" aria-label="Buka/Tutup Sidebar">
        <i class="bi bi-list"></i>
    </button>

    <div style="flex: 1;"></div>

    {{-- Right Actions: Language + Inbox + Notification Bell + Divider + User Profile --}}
    <div class="d-flex align-items-center gap-3">
        
        {{-- Language Selector Pill (ID with Globe) --}}
        <div class="dropdown">
            <button class="d-flex align-items-center gap-1 px-2 py-1 rounded-pill border-0"
                    id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                    style="border: 1.5px solid #E2E8F0 !important; font-size: 0.76rem; font-weight: 600; color: #64748B; cursor: pointer; background: transparent !important;">
                <i class="bi bi-globe2 me-1" style="font-size: 0.78rem;"></i>
                <span id="currentLangLabel">ID</span>
                <i class="bi bi-chevron-down ms-1" style="font-size: 0.65rem;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-1 mt-1" style="border-radius: 10px; min-width: 155px;" aria-labelledby="langDropdown">
                <li>
                    <a class="dropdown-item py-1 px-3 small d-flex align-items-center justify-content-between active" id="langOptId" href="javascript:void(0)" onclick="window.setAppLanguage('id');">
                        <span data-i18n="lang_id_label">🇮🇩 ID (Indonesia)</span>
                        <i class="bi bi-check text-primary" id="checkLangId"></i>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-1 px-3 small d-flex align-items-center justify-content-between" id="langOptEn" href="javascript:void(0)" onclick="window.setAppLanguage('en');">
                        <span data-i18n="lang_en_label">🇬🇧 EN (English)</span>
                        <i class="bi bi-check text-primary d-none" id="checkLangEn"></i>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Inbox Button with Dynamic Notification Dot & Content --}}
        <div class="dropdown">
            <button class="btn p-0 position-relative d-flex align-items-center justify-content-center border-0 bg-transparent"
                    type="button" id="inboxDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                    style="width: 36px; height: 36px; color: #475569; font-size: 1.15rem;">
                <i class="bi bi-inbox"></i>
                @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                    <span style="position: absolute; top: 7px; right: 5px; width: 7px; height: 7px; border-radius: 50%; background: #EF4444; border: 1.5px solid #FFFFFF;"></span>
                @endif
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-3 mt-2" style="width: 320px; border-radius: 14px; border: 1px solid #E2E8F0 !important;" aria-labelledby="inboxDropdown">
                <li class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <span class="fw-bold small text-dark" data-i18n="inbox_title">Pesan Masuk (Inbox)</span>
                    @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                        <span class="badge bg-primary-subtle text-primary rounded-pill" style="font-size: 0.68rem;">{{ $unreadNotificationsCount }} Baru</span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.68rem;">Kosong</span>
                    @endif
                </li>
                @if(isset($recentNotifications) && $recentNotifications->isNotEmpty())
                    @foreach($recentNotifications as $notif)
                        <li class="mb-2">
                            <a href="{{ route('notifications.go', $notif->id) }}" class="text-decoration-none d-block">
                                <div class="p-2 rounded-2" style="background: #F8FAFC; border-left: 3px solid #0063D7;">
                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.76rem;">{{ $notif->title }}</div>
                                    <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ $notif->message }}</div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                @else
                    <li class="text-center py-3 text-muted small" style="font-size: 0.78rem;">
                        <i class="bi bi-inbox d-block fs-4 mb-1 text-secondary opacity-50"></i>
                        Tidak ada pesan masuk saat ini
                    </li>
                @endif
            </ul>
        </div>

        {{-- Notification Bell with Dynamic Notification Dot & Dropdown --}}
        <div class="dropdown">
            <button class="btn p-0 position-relative d-flex align-items-center justify-content-center border-0 bg-transparent" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                    style="width: 36px; height: 36px; color: #475569; font-size: 1.15rem;">
                <i class="bi bi-bell"></i>
                @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                    <span style="position: absolute; top: 7px; right: 5px; width: 7px; height: 7px; border-radius: 50%; background: #EF4444; border: 1.5px solid #FFFFFF;"></span>
                @endif
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-3 mt-2" style="width: 340px; border-radius: 16px; border: 1px solid #E2E8F0 !important;" aria-labelledby="notifDropdown">
                <li class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <span class="fw-bold small text-dark" data-i18n="notif_incoming">Notifikasi Masuk</span>
                    @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                        <span class="badge bg-danger-subtle text-danger rounded-pill" style="font-size: 0.68rem;">{{ $unreadNotificationsCount }} Baru</span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.68rem;">Semua Terbaca</span>
                    @endif
                </li>

                @if(isset($recentNotifications) && $recentNotifications->isNotEmpty())
                    @foreach($recentNotifications as $notif)
                        @php
                            $theme = $notif->getColorTheme();
                            $targetUrl = route('notifications.go', $notif->id);
                        @endphp
                        <li class="mb-2">
                            <a href="{{ $targetUrl }}" class="text-decoration-none d-block">
                                <div class="p-2 rounded-3" style="background: {{ !$notif->is_read ? $theme['bg'] : '#F8FAFC' }}; border-left: 3px solid {{ $theme['icon_color'] }}; transition: background 0.15s ease;">
                                    <div class="d-flex justify-content-between align-items-start gap-1">
                                        <div class="fw-bold text-truncate" style="font-size: 0.76rem; color: {{ !$notif->is_read ? $theme['text'] : '#1E293B' }}; max-width: 210px;">
                                            {{ $notif->title }}
                                        </div>
                                        <span class="text-muted" style="font-size: 0.65rem; white-space: nowrap;">{{ $notif->formatted_time }}</span>
                                    </div>
                                    <div class="text-muted text-truncate" style="font-size: 0.72rem; max-width: 280px;">
                                        {{ $notif->message }}
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                @else
                    <li class="text-center py-3 text-muted small" style="font-size: 0.78rem;">
                        <i class="bi bi-bell-slash d-block fs-4 mb-1 text-secondary opacity-50"></i>
                        Tidak ada notifikasi baru
                    </li>
                @endif

                <li class="pt-2 border-top mt-2">
                    <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-light w-100 fw-semibold text-primary py-1.5 d-flex align-items-center justify-content-center gap-1"
                       style="font-size: 0.78rem; border-radius: 8px; background: #EFF6FF; border: 1px solid #BFDBFE;">
                        <span>Lihat Semua Notifikasi</span>
                        <i class="bi bi-arrow-right" style="font-size: 0.75rem;"></i>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Vertical Divider as in Reference --}}
        <div style="height: 22px; width: 1px; background: #CBD5E1; margin: 0 4px;"></div>

        {{-- User Profile --}}
        @php
            $userNameNav = auth()->check() ? auth()->user()->name : 'User';
            $wordsNav = preg_split('/\s+/', trim($userNameNav));
            $initialsNav = count($wordsNav) >= 2
                ? strtoupper(substr($wordsNav[0], 0, 1) . substr($wordsNav[1], 0, 1))
                : strtoupper(substr($userNameNav, 0, 2));
            $userRoleLabel = auth()->check() ? (auth()->user()->isPresales() ? 'PRESALES' : (auth()->user()->isPmh() ? 'PM HEAD' : (auth()->user()->isPm() ? 'PM' : (auth()->user()->isClient() ? 'KLIEN' : strtoupper(auth()->user()->role))))) : 'USER';
        @endphp
        <div class="dropdown">
            <div class="d-flex align-items-center gap-2 ps-1 cursor-pointer" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm"
                     style="width: 40px; height: 40px; background: #D5CCBE; color: #292524; font-size: 0.85rem; flex-shrink: 0;">
                    {{ $initialsNav }}
                </div>
                <div style="line-height: 1.25;" class="d-none d-sm-block text-start">
                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.86rem; font-weight: 700; letter-spacing: -0.01em; color: #0F172A;">
                        {{ $userNameNav }}
                    </div>
                    <div class="text-muted text-truncate" style="font-size: 0.72rem; color: #64748B; text-transform: uppercase;">
                        {{ $userRoleLabel }}
                    </div>
                </div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 p-2" style="border-radius: 12px; min-width: 180px;" aria-labelledby="userMenuDropdown">
                <li>
                    <div class="px-3 py-2 border-bottom mb-1">
                        <div class="fw-bold small">{{ $userNameNav }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">{{ ucfirst(auth()->user()?->role ?? 'user') }}</div>
                    </div>
                </li>
                <li><a class="dropdown-item py-1 px-3 small" href="{{ route('profile.index') }}"><i class="bi bi-person me-2"></i> Profil {{ auth()->check() && auth()->user()->isClient() ? 'Klien' : 'Pengguna' }}</a></li>
                @if(auth()->check() && auth()->user()->isAdmin())
                    <li><a class="dropdown-item py-1 px-3 small" href="{{ route('master.clients.index') }}"><i class="bi bi-gear me-2"></i> Master Data</a></li>
                @endif
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item py-1 px-3 small text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
