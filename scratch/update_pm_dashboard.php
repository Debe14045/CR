<?php

// 1. Update resources/views/layouts/app.blade.php
$appFile = 'resources/views/layouts/app.blade.php';
$appContent = file_get_contents($appFile);

// Check sidebar replacement
$searchSidebar = <<< 'HTML'
            <div class="nav-group-body sidebar-sub open" id="crGroupBody" style="display: flex; flex-direction: column; gap: 0.35rem; margin-top: 0.4rem; padding-left: 0;">
                @php
                    $isCreateActive = request()->routeIs('change-requests.create') || request()->routeIs('change-requests.edit');
                    $isDraftActive = request('view') === 'draft';
                    $isShowActive = request()->routeIs('change-requests.show');
                    $showClientMenu = request()->routeIs('client.profile', 'change-requests.create', 'change-requests.edit', 'change-requests.show') || $isDraftActive;
                    $isSemuaActiveInClient = $isShowActive || (!$isCreateActive && !$isDraftActive && !request()->routeIs('client.profile'));
                @endphp
                @if ($showClientMenu)
                    {{-- Client Submenu (Exact match to Figma media_1791295113506.png, media_1791293254775.png, and media_1791291762057.png) --}}
                    <a href="{{ route('change-requests.index', ['view' => 'total']) }}"
                       class="sidebar-link"
                       style="background: transparent !important; padding: 0.48rem 1.15rem; font-size: 0.88rem; color: {{ $isSemuaActiveInClient ? '#FFFFFF' : 'rgba(255,255,255,0.85)' }}; display: flex; align-items: center; position: relative;">
                        @if ($isSemuaActiveInClient)
                            <span style="position: absolute; left: 16px; width: 7.5px; height: 7.5px; border-radius: 50%; background: #00B4FF; box-shadow: 0 0 8px 1px #00B4FF; display: inline-block;"></span>
                        @endif
                        <i class="bi bi-file-earmark-text nav-icon" style="font-size: 1.05rem; color: #FFFFFF; margin-left: 18px; margin-right: 0.7rem;"></i>
                        <span class="{{ $isSemuaActiveInClient ? 'fw-bold' : 'fw-normal' }}" data-i18n="nav_all_cr" style="color: #FFFFFF; letter-spacing: -0.01em;">Semua CR</span>
                    </a>
                    <a href="{{ route('change-requests.create') }}"
                       class="sidebar-link"
                       style="background: transparent !important; padding: 0.48rem 1.15rem; font-size: 0.88rem; color: {{ $isCreateActive ? '#FFFFFF' : 'rgba(255,255,255,0.85)' }}; display: flex; align-items: center; position: relative;">
                        @if ($isCreateActive)
                            <span style="position: absolute; left: 16px; width: 7.5px; height: 7.5px; border-radius: 50%; background: #00B4FF; box-shadow: 0 0 8px 1px #00B4FF; display: inline-block;"></span>
                        @endif
                        <i class="bi bi-file-earmark-diff nav-icon" style="font-size: 1rem; color: #FFFFFF; margin-left: 18px; margin-right: 0.7rem;"></i>
                        <span class="{{ $isCreateActive ? 'fw-bold' : 'fw-normal' }}" data-i18n="nav_submit_cr" style="color: #FFFFFF;">Ajukan CR</span>
                    </a>
                    <a href="{{ route('change-requests.index', ['view' => 'draft']) }}"
                       class="sidebar-link"
                       style="background: transparent !important; padding: 0.48rem 1.15rem; font-size: 0.88rem; color: {{ $isDraftActive ? '#FFFFFF' : 'rgba(255,255,255,0.85)' }}; display: flex; align-items: center; position: relative;">
                        @if ($isDraftActive)
                            <span style="position: absolute; left: 16px; width: 7.5px; height: 7.5px; border-radius: 50%; background: #00B4FF; box-shadow: 0 0 8px 1px #00B4FF; display: inline-block;"></span>
                        @endif
                        <i class="bi bi-file-earmark-bar-graph nav-icon" style="font-size: 1rem; color: #FFFFFF; margin-left: 18px; margin-right: 0.7rem;"></i>
                        <span class="{{ $isDraftActive ? 'fw-bold' : 'fw-normal' }}" data-i18n="nav_draft_cr" style="color: #FFFFFF;">Draft CR</span>
                    </a>
                @else
                    {{-- Monitoring Screen Sidebar (Exact match to Figma media_1791289882673.png) --}}
                    <a href="{{ route('change-requests.index', ['view' => 'total']) }}"
                       class="sidebar-link"
                       style="background: transparent !important; padding: 0.48rem 1.15rem; font-size: 0.88rem; color: {{ $isSemuaActive ? '#FFFFFF' : 'rgba(255,255,255,0.85)' }}; display: flex; align-items: center; position: relative;">
                        @if ($isSemuaActive)
                            <span style="position: absolute; left: 16px; width: 7.5px; height: 7.5px; border-radius: 50%; background: #00B4FF; box-shadow: 0 0 8px 1px #00B4FF; display: inline-block;"></span>
                        @endif
                        <i class="bi bi-file-earmark-text nav-icon" style="font-size: 1.05rem; color: #FFFFFF; margin-left: 18px; margin-right: 0.7rem;"></i>
                        <span class="{{ $isSemuaActive ? 'fw-bold' : 'fw-normal' }}" data-i18n="nav_all_cr" style="color: #FFFFFF; letter-spacing: -0.01em;">Semua CR</span>
                    </a>

                    <a href="{{ route('change-requests.index', ['view' => 'total', 'status' => 'diajukan']) }}"
                       class="sidebar-link"
                       style="background: transparent !important; padding: 0.48rem 1.15rem; font-size: 0.88rem; color: {{ $isNeedApprovalActive ? '#FFFFFF' : 'rgba(255,255,255,0.85)' }}; display: flex; align-items: center; position: relative;">
                        @if ($isNeedApprovalActive)
                            <span style="position: absolute; left: 16px; width: 7.5px; height: 7.5px; border-radius: 50%; background: #00B4FF; box-shadow: 0 0 8px 1px #00B4FF; display: inline-block;"></span>
                        @endif
                        <i class="bi bi-file-earmark-check nav-icon" style="font-size: 1rem; color: #FFFFFF; margin-left: 18px; margin-right: 0.7rem;"></i>
                        <span class="{{ $isNeedApprovalActive ? 'fw-bold' : 'fw-normal' }}" data-i18n="nav_need_approval" style="color: #FFFFFF;">Butuh Persetujuan</span>
                    </a>

                    <a href="{{ route('change-requests.index', ['view' => 'development']) }}"
                       class="sidebar-link"
                       style="background: transparent !important; padding: 0.48rem 1.15rem; font-size: 0.88rem; color: rgba(255,255,255,0.85); display: flex; align-items: center; position: relative;">
                        <i class="bi bi-file-earmark-bar-graph nav-icon" style="font-size: 1rem; color: #FFFFFF; margin-left: 18px; margin-right: 0.7rem;"></i>
                        <span class="fw-normal" data-i18n="nav_dev_cr" style="color: #FFFFFF;">Development CR</span>
                    </a>
                @endif
            </div>
HTML;

$replaceSidebar = <<< 'HTML'
            <div class="nav-group-body sidebar-sub open" id="crGroupBody" style="display: flex; flex-direction: column; gap: 0.35rem; margin-top: 0.4rem; padding-left: 0;">
                @php
                    $isCreateActive = request()->routeIs('change-requests.create') || request()->routeIs('change-requests.edit');
                    $isDraftActive = request('view') === 'draft';
                    $isShowActive = request()->routeIs('change-requests.show');
                    // In Dashboard, no subitem has glowing dot (Dashboard itself is highlighted)
                    $isSemuaActiveSub = !$isDashboardActive && !$isCreateActive && !$isDraftActive && !request()->routeIs('client.profile');
                @endphp

                {{-- Submenu: Semua CR, Ajukan CR, Draft CR matching Figma across PM & Client --}}
                <a href="{{ route('change-requests.index', ['view' => 'total']) }}"
                   class="sidebar-link"
                   style="background: transparent !important; padding: 0.48rem 1.15rem; font-size: 0.88rem; color: {{ $isSemuaActiveSub ? '#FFFFFF' : 'rgba(255,255,255,0.85)' }}; display: flex; align-items: center; position: relative;">
                    @if ($isSemuaActiveSub)
                        <span style="position: absolute; left: 16px; width: 7.5px; height: 7.5px; border-radius: 50%; background: #00B4FF; box-shadow: 0 0 8px 1px #00B4FF; display: inline-block;"></span>
                    @endif
                    <i class="bi bi-file-earmark-text nav-icon" style="font-size: 1.05rem; color: #FFFFFF; margin-left: 18px; margin-right: 0.7rem;"></i>
                    <span class="{{ $isSemuaActiveSub ? 'fw-bold' : 'fw-normal' }}" data-i18n="nav_all_cr" style="color: #FFFFFF; letter-spacing: -0.01em;">Semua CR</span>
                </a>

                <a href="{{ route('change-requests.create') }}"
                   class="sidebar-link"
                   style="background: transparent !important; padding: 0.48rem 1.15rem; font-size: 0.88rem; color: {{ $isCreateActive ? '#FFFFFF' : 'rgba(255,255,255,0.85)' }}; display: flex; align-items: center; position: relative;">
                    @if ($isCreateActive)
                        <span style="position: absolute; left: 16px; width: 7.5px; height: 7.5px; border-radius: 50%; background: #00B4FF; box-shadow: 0 0 8px 1px #00B4FF; display: inline-block;"></span>
                    @endif
                    <i class="bi bi-file-earmark-diff nav-icon" style="font-size: 1rem; color: #FFFFFF; margin-left: 18px; margin-right: 0.7rem;"></i>
                    <span class="{{ $isCreateActive ? 'fw-bold' : 'fw-normal' }}" data-i18n="nav_submit_cr" style="color: #FFFFFF;">Ajukan CR</span>
                </a>

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
HTML;

// Normalize line endings for replacement
$searchSidebarNorm = str_replace("\r\n", "\n", $searchSidebar);
$appContentNorm = str_replace("\r\n", "\n", $appContent);

if (strpos($appContentNorm, $searchSidebarNorm) !== false) {
    $appContentNorm = str_replace($searchSidebarNorm, str_replace("\r\n", "\n", $replaceSidebar), $appContentNorm);
    file_put_contents($appFile, $appContentNorm);
    echo "Updated app.blade.php sidebar successfully!\n";
} else {
    echo "Warning: sidebar block not found in app.blade.php\n";
}

// 2. Now let's update resources/views/cr/index.blade.php
$crFile = 'resources/views/cr/index.blade.php';
$crContent = file_get_contents($crFile);
$crContentNorm = str_replace("\r\n", "\n", $crContent);

// Target Dashboard Header + 4 Cards
$searchCards = <<< 'HTML'
    {{-- Header: DASHBOARD --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" data-i18n="dashboard_title" style="color: #0F172A; font-size: 1.65rem; font-weight: 800; letter-spacing: -0.01em;">DASHBOARD</h1>
            <p class="text-muted mb-0" data-i18n="dashboard_subtitle" style="font-size: 0.84rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda</p>
        </div>
        @if (!$isPmDashboard)
            <div>
                <a href="{{ route('change-requests.create') }}" class="btn text-white fw-bold d-inline-flex align-items-center gap-2"
                   style="background: #22C55E; border-radius: 8px; padding: 0.6rem 1.45rem; font-size: 0.88rem; border: none; box-shadow: 0 4px 14px rgba(34,197,94,0.3); transition: transform 0.15s ease;">
                    <i class="bi bi-plus-lg"></i>
                    <span data-i18n="btn_new_cr">Ajukan CR Baru</span>
                </a>
            </div>
        @endif
    </div>

    {{-- 4 Big Metric Cards (Figma: #134B8A Deep Royal Blue) --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Semua CR (#134B8A) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('change-requests.index', ['view' => 'total']) }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" data-i18n="card_all_cr" style="font-size: 0.95rem;">Semua CR</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $figmaTotalCr }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>

        @if ($isPmDashboard)
            {{-- PM Card 2: Butuh Persetujuan (#134B8A) --}}
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('change-requests.index', ['view' => 'total', 'status' => 'awaiting_pm']) }}" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_need_approval" style="font-size: 0.95rem;">Butuh Persetujuan</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            {{ $figmaNeedApprovalCr ?? 4 }}
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>

            {{-- PM Card 3: Development (#134B8A) --}}
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('change-requests.index', ['view' => 'development']) }}" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_development" style="font-size: 0.95rem;">Development</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            {{ $figmaDevCr }}
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>
        @else
            {{-- Client Card 2: Development (#134B8A) --}}
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('change-requests.index', ['view' => 'development']) }}" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_development" style="font-size: 0.95rem;">Development</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            {{ $figmaDevCr }}
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Client Card 3: UAT (#134B8A) --}}
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('change-requests.index', ['view' => 'uat']) }}" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_uat" style="font-size: 0.95rem;">UAT</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            {{ $figmaUatCr }}
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        {{-- Card 4: GO LIVE (#134B8A) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('change-requests.index', ['view' => 'golive']) }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" data-i18n="card_golive" style="font-size: 0.95rem;">GO LIVE</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $figmaGoLiveCr }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
HTML;

$replaceCards = <<< 'HTML'
    {{-- Header: DASHBOARD --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" data-i18n="dashboard_title" style="color: #0F172A; font-size: 1.65rem; font-weight: 800; letter-spacing: -0.01em;">DASHBOARD</h1>
            <p class="text-muted mb-0" data-i18n="dashboard_subtitle" style="font-size: 0.84rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda</p>
        </div>
        <div>
            <a href="{{ route('change-requests.create') }}" class="btn text-white fw-bold d-inline-flex align-items-center gap-2"
               style="background: #50CD89; border-radius: 9999px; padding: 0.58rem 1.45rem; font-size: 0.88rem; border: none; box-shadow: 0 4px 14px rgba(80,205,137,0.3); transition: transform 0.15s ease;">
                <i class="bi bi-plus-lg"></i>
                <span data-i18n="btn_new_cr">Ajukan CR Baru</span>
            </a>
        </div>
    </div>

    {{-- 4 Big Metric Cards (Figma: #014DA1, #144272, #205295, #2C74B3) --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Semua CR (#014DA1) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('change-requests.index', ['view' => 'total']) }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #014DA1; border-radius: 16px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(1,77,161,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" data-i18n="card_all_cr" style="font-size: 0.95rem;">Semua CR</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $figmaTotalCr }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>

        @if ($isPmDashboard)
            {{-- PM Card 2: Butuh Persetujuan (#144272) --}}
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('change-requests.index', ['view' => 'total', 'status' => 'awaiting_pm']) }}" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #144272; border-radius: 16px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(20,66,114,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_need_approval" style="font-size: 0.95rem;">Butuh Persetujuan</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            {{ $figmaNeedApprovalCr ?? 4 }}
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>

            {{-- PM Card 3: Development (#205295) --}}
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('change-requests.index', ['view' => 'development']) }}" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #205295; border-radius: 16px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(32,82,149,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_development" style="font-size: 0.95rem;">Development</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            {{ $figmaDevCr }}
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>
        @else
            {{-- Client Card 2: Development (#144272) --}}
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('change-requests.index', ['view' => 'development']) }}" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #144272; border-radius: 16px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(20,66,114,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_development" style="font-size: 0.95rem;">Development</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            {{ $figmaDevCr }}
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Client Card 3: UAT (#205295) --}}
            <div class="col-xl-3 col-md-6 col-12">
                <a href="{{ route('change-requests.index', ['view' => 'uat']) }}" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #205295; border-radius: 16px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(32,82,149,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_uat" style="font-size: 0.95rem;">UAT</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            {{ $figmaUatCr }}
                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        {{-- Card 4: GO LIVE (#2C74B3) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('change-requests.index', ['view' => 'golive']) }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #2C74B3; border-radius: 16px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(44,116,179,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" data-i18n="card_golive" style="font-size: 0.95rem;">GO LIVE</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $figmaGoLiveCr }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
HTML;

$searchCardsNorm = str_replace("\r\n", "\n", $searchCards);
if (strpos($crContentNorm, $searchCardsNorm) !== false) {
    $crContentNorm = str_replace($searchCardsNorm, str_replace("\r\n", "\n", $replaceCards), $crContentNorm);
    echo "Updated cr/index.blade.php Header and 4 Cards successfully!\n";
} else {
    echo "Warning: searchCards not found in cr/index.blade.php\n";
}

// 3. Update Chart Card (PM Spline Line Chart -> Bar Chart)
$searchPmChart = <<< 'HTML'
            @if ($isPmDashboard)
                {{-- ── PM DASHBOARD: Tren CR Bulanan Spline Line Chart (Figma Exact) ── --}}
                <div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
                    <div class="mb-2">
                        <h5 class="fw-bold mb-1" style="color: #0F172A; font-size: 0.95rem; font-weight: 800;">Tren CR Bulanan</h5>
                        <p class="text-muted small mb-0" style="font-size: 0.78rem; color: #64748B;">Jumlah CR masuk, Go-Live, dan ditolak per bulan</p>
                    </div>

                    {{-- SVG Line Chart matching Figma exactly --}}
                    <div class="w-100 position-relative pt-2">
                        <svg viewBox="0 0 540 215" class="w-100" style="overflow: visible; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <defs>
                                <linearGradient id="gradientMasuk" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#3B82F6" stop-opacity="0.22" />
                                    <stop offset="100%" stop-color="#3B82F6" stop-opacity="0.0" />
                                </linearGradient>
                                <linearGradient id="gradientGoLive" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#10B981" stop-opacity="0.15" />
                                    <stop offset="100%" stop-color="#10B981" stop-opacity="0.0" />
                                </linearGradient>
                            </defs>

                            <line x1="45" y1="20" x2="520" y2="20" stroke="#F1F5F9" stroke-width="1.2" />
                            <text x="32" y="24" font-size="11" fill="#94A3B8" text-anchor="end">4</text>

                            <line x1="45" y1="58" x2="520" y2="58" stroke="#F1F5F9" stroke-width="1.2" />
                            <text x="32" y="62" font-size="11" fill="#94A3B8" text-anchor="end">3</text>

                            <line x1="45" y1="96" x2="520" y2="96" stroke="#F1F5F9" stroke-width="1.2" />
                            <text x="32" y="100" font-size="11" fill="#94A3B8" text-anchor="end">2</text>

                            <line x1="45" y1="134" x2="520" y2="134" stroke="#F1F5F9" stroke-width="1.2" />
                            <text x="32" y="138" font-size="11" fill="#94A3B8" text-anchor="end">1</text>

                            <line x1="45" y1="172" x2="520" y2="172" stroke="#E2E8F0" stroke-width="1.2" />
                            <text x="32" y="176" font-size="11" fill="#94A3B8" text-anchor="end">0</text>

                            <text x="65" y="193" font-size="11" fill="#94A3B8" text-anchor="middle">Jan</text>
                            <text x="155" y="193" font-size="11" fill="#94A3B8" text-anchor="middle">Feb</text>
                            <text x="245" y="193" font-size="11" fill="#94A3B8" text-anchor="middle">Mar</text>
                            <text x="335" y="193" font-size="11" fill="#94A3B8" text-anchor="middle">Apr</text>
                            <text x="425" y="193" font-size="11" fill="#94A3B8" text-anchor="middle">Mei</text>
                            <text x="515" y="193" font-size="11" fill="#94A3B8" text-anchor="middle">Jun</text>

                            <path d="M 65 96
                                     C 110 70, 120 58, 155 58
                                     C 190 58, 210 134, 245 134
                                     C 285 134, 305 20, 335 20
                                     C 365 20, 395 96, 425 96
                                     C 455 96, 485 58, 515 58
                                     L 515 172 L 65 172 Z"
                                  fill="url(#gradientMasuk)" />

                            <path d="M 65 172
                                     C 100 155, 120 134, 155 134
                                     C 185 134, 215 134, 245 134
                                     C 280 134, 300 96, 335 96
                                     C 370 96, 390 134, 425 134
                                     C 460 134, 480 96, 515 96
                                     L 515 172 L 65 172 Z"
                                  fill="url(#gradientGoLive)" />

                            <path d="M 65 172
                                     L 155 172
                                     C 185 172, 215 134, 245 134
                                     C 275 134, 305 172, 335 172
                                     C 365 172, 395 134, 425 134
                                     C 455 134, 485 172, 515 172"
                                  fill="none" stroke="#EF4444" stroke-width="2" stroke-dasharray="4, 3" />

                            <path d="M 65 172
                                     C 100 155, 120 134, 155 134
                                     C 185 134, 215 134, 245 134
                                     C 280 134, 300 96, 335 96
                                     C 370 96, 390 134, 425 134
                                     C 460 134, 480 96, 515 96"
                                  fill="none" stroke="#10B981" stroke-width="2.2" stroke-linecap="round" />

                            <path d="M 65 96
                                     C 110 70, 120 58, 155 58
                                     C 190 58, 210 134, 245 134
                                     C 285 134, 305 20, 335 20
                                     C 365 20, 395 96, 425 96
                                     C 455 96, 485 58, 515 58"
                                  fill="none" stroke="#3B82F6" stroke-width="2.2" stroke-linecap="round" />

                            <circle cx="65" cy="172" r="3.5" fill="#EF4444" />
                            <circle cx="155" cy="172" r="3.5" fill="#EF4444" />
                            <circle cx="245" cy="134" r="3.5" fill="#EF4444" />
                            <circle cx="335" cy="172" r="3.5" fill="#EF4444" />
                            <circle cx="425" cy="134" r="3.5" fill="#EF4444" />
                            <circle cx="515" cy="172" r="3.5" fill="#EF4444" />

                            <circle cx="65" cy="172" r="4" fill="#10B981" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="155" cy="134" r="4" fill="#10B981" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="245" cy="134" r="4" fill="#10B981" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="335" cy="96" r="4" fill="#10B981" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="425" cy="134" r="4" fill="#10B981" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="515" cy="96" r="4" fill="#10B981" stroke="#FFFFFF" stroke-width="1.5" />

                            <circle cx="65" cy="96" r="4" fill="#3B82F6" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="155" cy="58" r="4" fill="#3B82F6" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="245" cy="134" r="4" fill="#3B82F6" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="335" cy="20" r="4" fill="#3B82F6" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="425" cy="96" r="4" fill="#3B82F6" stroke="#FFFFFF" stroke-width="1.5" />
                            <circle cx="515" cy="58" r="4" fill="#3B82F6" stroke="#FFFFFF" stroke-width="1.5" />
                        </svg>
                    </div>

                    <div class="d-flex align-items-center justify-content-center gap-4 mt-2 pt-1 text-muted" style="font-size: 0.74rem; font-weight: 500;">
                        <div class="d-flex align-items-center gap-2">
                            <span style="display: inline-flex; align-items: center; width: 22px;">
                                <span style="width: 100%; border-top: 2px dashed #EF4444; position: relative;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #EF4444; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);"></span>
                                </span>
                            </span>
                            <span style="color: #EF4444; font-weight: 600;">Ditolak</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span style="display: inline-flex; align-items: center; width: 22px;">
                                <span style="width: 100%; border-top: 2px solid #10B981; position: relative;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #10B981; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);"></span>
                                </span>
                            </span>
                            <span style="color: #10B981; font-weight: 600;">GoLive</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span style="display: inline-flex; align-items: center; width: 22px;">
                                <span style="width: 100%; border-top: 2px solid #3B82F6; position: relative;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #3B82F6; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);"></span>
                                </span>
                            </span>
                            <span style="color: #3B82F6; font-weight: 600;">Masuk</span>
                        </div>
                    </div>
                </div>
HTML;

$replacePmChart = <<< 'HTML'
            @if ($isPmDashboard)
                {{-- ── PM DASHBOARD: Tren CR Bulanan (Diagram Batang) Figma Exact ── --}}
                <div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: #EEF1F6; border: 1px solid #E2E8F0 !important;">
                    <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-1" style="color: #0F172A; font-size: 0.98rem; font-weight: 800;">Tren CR Bulanan (Diagram Batang)</h5>
                            <p class="text-muted small mb-0" style="font-size: 0.78rem; color: #64748B;">Jumlah CR Masuk, Keluar, dan Dibatalkan (Januari - Juni)</p>
                        </div>
                        {{-- Legend matching Figma --}}
                        <div class="d-flex align-items-center gap-3 ms-auto" style="font-size: 0.75rem; font-weight: 600;">
                            <div class="d-flex align-items-center gap-1.5">
                                <span style="width: 11px; height: 11px; background: #3B7CE7; border-radius: 2px; display: inline-block;"></span>
                                <span style="color: #334155;">CR Masuk</span>
                            </div>
                            <div class="d-flex align-items-center gap-1.5">
                                <span style="width: 11px; height: 11px; background: #31A773; border-radius: 2px; display: inline-block;"></span>
                                <span style="color: #334155;">Keluar</span>
                            </div>
                            <div class="d-flex align-items-center gap-1.5">
                                <span style="width: 11px; height: 11px; background: #EF4444; border-radius: 2px; display: inline-block;"></span>
                                <span style="color: #334155;">Dibatalkan</span>
                            </div>
                        </div>
                    </div>

                    {{-- SVG Bar Chart matching Figma exactly --}}
                    <div class="w-100 position-relative pt-2">
                        <svg viewBox="0 0 540 230" class="w-100" style="overflow: visible; font-family: 'Plus Jakarta Sans', sans-serif;">
                            {{-- Horizontal Guide Lines & Y-Axis Labels matching Figma --}}
                            <line x1="45" y1="35" x2="525" y2="35" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                            <text x="34" y="39" font-size="10.5" fill="#94A3B8" font-weight="600" text-anchor="end">5</text>

                            <line x1="45" y1="65" x2="525" y2="65" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                            <text x="34" y="69" font-size="10.5" fill="#94A3B8" font-weight="600" text-anchor="end">4</text>

                            <line x1="45" y1="95" x2="525" y2="95" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                            <text x="34" y="99" font-size="10.5" fill="#94A3B8" font-weight="600" text-anchor="end">3</text>

                            <line x1="45" y1="135" x2="525" y2="135" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                            <text x="34" y="139" font-size="10.5" fill="#94A3B8" font-weight="600" text-anchor="end">1</text>

                            <line x1="45" y1="175" x2="525" y2="175" stroke="#CBD5E1" stroke-width="1.2" />
                            <text x="34" y="179" font-size="10.5" fill="#94A3B8" font-weight="600" text-anchor="end">0</text>

                            {{-- Month 1: Jan (Masuk: 3, Keluar: 0, Batal: 0) --}}
                            <text x="78" y="90" font-size="10" font-weight="700" fill="#3B7CE7" text-anchor="middle">3</text>
                            <rect x="73" y="95" width="11" height="80" rx="2" fill="#3B7CE7" />
                            <text x="91" y="170" font-size="9" font-weight="600" fill="#31A773" text-anchor="middle">0</text>
                            <rect x="86" y="173" width="11" height="2" rx="1" fill="#31A773" />
                            <text x="104" y="170" font-size="9" font-weight="600" fill="#EF4444" text-anchor="middle">0</text>
                            <rect x="99" y="173" width="11" height="2" rx="1" fill="#EF4444" />
                            <text x="91" y="195" font-size="11" font-weight="600" fill="#64748B" text-anchor="middle">Jan</text>

                            {{-- Month 2: Feb (Masuk: 4, Keluar: 1, Batal: 0) --}}
                            <text x="163" y="60" font-size="10" font-weight="700" fill="#3B7CE7" text-anchor="middle">4</text>
                            <rect x="158" y="65" width="11" height="110" rx="2" fill="#3B7CE7" />
                            <text x="176" y="130" font-size="9.5" font-weight="600" fill="#31A773" text-anchor="middle">1</text>
                            <rect x="171" y="135" width="11" height="40" rx="2" fill="#31A773" />
                            <text x="189" y="170" font-size="9" font-weight="600" fill="#EF4444" text-anchor="middle">0</text>
                            <rect x="184" y="173" width="11" height="2" rx="1" fill="#EF4444" />
                            <text x="176" y="195" font-size="11" font-weight="600" fill="#64748B" text-anchor="middle">Feb</text>

                            {{-- Month 3: Mar (Masuk: 1, Keluar: 1, Batal: 1) --}}
                            <text x="248" y="130" font-size="9.5" font-weight="600" fill="#3B7CE7" text-anchor="middle">1</text>
                            <rect x="243" y="135" width="11" height="40" rx="2" fill="#3B7CE7" />
                            <text x="261" y="130" font-size="9.5" font-weight="600" fill="#31A773" text-anchor="middle">1</text>
                            <rect x="256" y="135" width="11" height="40" rx="2" fill="#31A773" />
                            <text x="274" y="130" font-size="9.5" font-weight="600" fill="#EF4444" text-anchor="middle">1</text>
                            <rect x="269" y="135" width="11" height="40" rx="2" fill="#EF4444" />
                            <text x="261" y="195" font-size="11" font-weight="600" fill="#64748B" text-anchor="middle">Mar</text>

                            {{-- Month 4: Apr (Masuk: 5, Keluar: 2, Batal: 0) --}}
                            <text x="333" y="30" font-size="10" font-weight="700" fill="#3B7CE7" text-anchor="middle">5</text>
                            <rect x="328" y="35" width="11" height="140" rx="2" fill="#3B7CE7" />
                            <text x="346" y="105" font-size="9.5" font-weight="600" fill="#31A773" text-anchor="middle">2</text>
                            <rect x="341" y="110" width="11" height="65" rx="2" fill="#31A773" />
                            <text x="359" y="170" font-size="9" font-weight="600" fill="#EF4444" text-anchor="middle">0</text>
                            <rect x="354" y="173" width="11" height="2" rx="1" fill="#EF4444" />
                            <text x="346" y="195" font-size="11" font-weight="600" fill="#64748B" text-anchor="middle">Apr</text>

                            {{-- Month 5: Mei (Masuk: 3, Keluar: 1, Batal: 1) --}}
                            <text x="418" y="90" font-size="10" font-weight="700" fill="#3B7CE7" text-anchor="middle">3</text>
                            <rect x="413" y="95" width="11" height="80" rx="2" fill="#3B7CE7" />
                            <text x="431" y="130" font-size="9.5" font-weight="600" fill="#31A773" text-anchor="middle">1</text>
                            <rect x="426" y="135" width="11" height="40" rx="2" fill="#31A773" />
                            <text x="444" y="130" font-size="9.5" font-weight="600" fill="#EF4444" text-anchor="middle">1</text>
                            <rect x="439" y="135" width="11" height="40" rx="2" fill="#EF4444" />
                            <text x="431" y="195" font-size="11" font-weight="600" fill="#64748B" text-anchor="middle">Mei</text>

                            {{-- Month 6: Jun (Masuk: 4, Keluar: 2, Batal: 0) --}}
                            <text x="493" y="60" font-size="10" font-weight="700" fill="#3B7CE7" text-anchor="middle">4</text>
                            <rect x="488" y="65" width="11" height="110" rx="2" fill="#3B7CE7" />
                            <text x="506" y="105" font-size="9.5" font-weight="600" fill="#31A773" text-anchor="middle">2</text>
                            <rect x="501" y="110" width="11" height="65" rx="2" fill="#31A773" />
                            <text x="519" y="170" font-size="9" font-weight="600" fill="#EF4444" text-anchor="middle">0</text>
                            <rect x="514" y="173" width="11" height="2" rx="1" fill="#EF4444" />
                            <text x="506" y="195" font-size="11" font-weight="600" fill="#64748B" text-anchor="middle">Jun</text>
                        </svg>
                    </div>

                    {{-- Footer Strip matching Figma --}}
                    <div class="mt-2 py-1.5 px-3 rounded-2 text-end" style="background: #E3E6ED;">
                        <span style="font-size: 0.72rem; color: #64748B; font-weight: 500;">Sumber Data: Laporan Internal CR</span>
                    </div>
                </div>
HTML;

$searchPmChartNorm = str_replace("\r\n", "\n", $searchPmChart);
if (strpos($crContentNorm, $searchPmChartNorm) !== false) {
    $crContentNorm = str_replace($searchPmChartNorm, str_replace("\r\n", "\n", $replacePmChart), $crContentNorm);
    echo "Updated cr/index.blade.php PM Bar Chart successfully!\n";
} else {
    echo "Warning: searchPmChart not found in cr/index.blade.php\n";
}

// 4. Update Right Card (NOTIFIKASI)
$searchNotif = <<< 'HTML'
        {{-- Card Right: NOTIFIKASI --}}
        <div class="col-lg-5 col-12">
            <div class="card p-4 border-0 h-100" style="border-radius: 20px; background: #F0F3F6; border: 1px solid #E2E8F0 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
                        <h5 class="fw-bold mb-0 text-uppercase" data-i18n="notif_title" style="color: #0F172A; font-size: 0.95rem; font-weight: 800; letter-spacing: 0.02em;">NOTIFIKASI</h5>
                    </div>
                    <a href="javascript:void(0)" class="text-decoration-none fw-semibold" data-i18n="notif_view_all" style="color: #0063D7; font-size: 0.8rem;">Lihat Semua</a>
                </div>
                <p class="text-muted small mb-0" data-i18n="notif_subtitle" style="font-size: 0.74rem; line-height: 1.45; color: #64748B;">
                    Pemberitahuan persetujuan penting, batas waktu tinjauan dokumen, dan tindak lanjut status tahapan proyek.
                </p>
                <div style="height: 1px; background: #DCE1E7; margin: 0.85rem 0 1.25rem;"></div>

                <div class="d-flex flex-column gap-3">
                    {{-- Alert 1: Red box --}}
                    <div class="p-3" style="background: #FEF3F4; border: 1px solid #FECDCA; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-exclamation-triangle-fill mt-1" style="color: #DC2626; font-size: 1.05rem; flex-shrink: 0;"></i>
                            <div style="line-height: 1.35;">
                                <div class="fw-bold" data-i18n="notif_alert1_title" style="color: #991B1B; font-size: 0.82rem;">
                                    3 Perubahan Ruang Lingkup CR menunggu persetujuan > 14 hari
                                </div>
                                <div data-i18n="notif_alert1_desc" style="color: #DC2626; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                    Status : Menunggu Persetujuan PM
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Alert 2: Yellow box --}}
                    <div class="p-3" style="background: #FDFBF0; border: 1px solid #FEDF89; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-exclamation-triangle-fill mt-1" style="color: #D97706; font-size: 1.05rem; flex-shrink: 0;"></i>
                            <div style="line-height: 1.35;">
                                <div class="fw-bold" data-i18n="notif_alert2_title" style="color: #92400E; font-size: 0.82rem;">
                                    8 CR Baru Menunggu Verifikasi Kebutuhan
                                </div>
                                <div data-i18n="notif_alert2_desc" style="color: #B45309; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                    Tahapan : 1. Analisis Kebutuhan
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Alert 3: Blue box --}}
                    <div class="p-3" style="background: #F2F7FE; border: 1px solid #BAE6FD; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle-fill mt-1" style="color: #2563EB; font-size: 1.05rem; flex-shrink: 0;"></i>
                            <div style="line-height: 1.35;">
                                <div class="fw-bold" data-i18n="notif_alert3_title" style="color: #1E40AF; font-size: 0.82rem;">
                                    5 Masukan Pengguna UAT butuh tindak lanjut
                                </div>
                                <div data-i18n="notif_alert3_desc" style="color: #2563EB; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                    Tahapan : 4. UAT & Pengujian
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
HTML;

$replaceNotif = <<< 'HTML'
        {{-- Card Right: NOTIFIKASI --}}
        <div class="col-lg-5 col-12">
            <div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 7.5px; height: 7.5px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
                        <h5 class="fw-bold mb-0 text-uppercase" data-i18n="notif_title" style="color: #0F172A; font-size: 0.95rem; font-weight: 800; letter-spacing: 0.02em;">NOTIFIKASI</h5>
                    </div>
                    <a href="javascript:void(0)" class="text-decoration-none fw-semibold" data-i18n="notif_view_all" style="color: #009EF7; font-size: 0.82rem;">Lihat Semua</a>
                </div>
                <p class="text-muted small mb-0 mt-1" data-i18n="notif_subtitle" style="font-size: 0.74rem; line-height: 1.45; color: #64748B;">
                    Pemberitahuan persetujuan penting, batas waktu tinjauan dokumen, dan tindak lanjut status tahapan proyek.
                </p>
                <div style="height: 1px; background: #E2E8F0; margin: 0.85rem 0 1.15rem;"></div>

                <div class="d-flex flex-column gap-3">
                    {{-- Alert 1: Red box --}}
                    <a href="{{ route('change-requests.index', ['view' => 'total', 'status' => 'awaiting_pm']) }}" class="text-decoration-none d-block">
                        <div class="p-3" style="background: #FDEAED; border: 1px solid #FECDCA; border-radius: 14px; transition: transform 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-start gap-2.5">
                                <i class="bi bi-exclamation-triangle-fill mt-0.5" style="color: #DC2626; font-size: 1.1rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.35;">
                                    <div class="fw-bold" data-i18n="notif_alert1_title" style="color: #991B1B; font-size: 0.83rem;">
                                        3 Perubahan Ruang Lingkup CR menunggu persetujuan &gt; 14 hari
                                    </div>
                                    <div data-i18n="notif_alert1_desc" style="color: #DC2626; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                        Status : Menunggu Persetujuan PM
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    {{-- Alert 2: Yellow box --}}
                    <a href="{{ route('change-requests.index', ['view' => 'total', 'tab' => 'pending']) }}" class="text-decoration-none d-block">
                        <div class="p-3" style="background: #FEFCF0; border: 1px solid #FEDF89; border-radius: 14px; transition: transform 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-start gap-2.5">
                                <i class="bi bi-exclamation-triangle-fill mt-0.5" style="color: #D97706; font-size: 1.1rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.35;">
                                    <div class="fw-bold" data-i18n="notif_alert2_title" style="color: #92400E; font-size: 0.83rem;">
                                        8 CR Baru Menunggu Verifikasi Kebutuhan
                                    </div>
                                    <div data-i18n="notif_alert2_desc" style="color: #B45309; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                        Tahapan : 1. Analisis Kebutuhan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    {{-- Alert 3: Blue box --}}
                    <a href="{{ route('change-requests.index', ['view' => 'uat']) }}" class="text-decoration-none d-block">
                        <div class="p-3" style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 14px; transition: transform 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-start gap-2.5">
                                <i class="bi bi-info-circle-fill mt-0.5" style="color: #2563EB; font-size: 1.1rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.35;">
                                    <div class="fw-bold" data-i18n="notif_alert3_title" style="color: #1E40AF; font-size: 0.83rem;">
                                        5 Masukan Pengguna UAT butuh tindak lanjut
                                    </div>
                                    <div data-i18n="notif_alert3_desc" style="color: #2563EB; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                        Tahapan : 4. UAT & Pengujian
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
HTML;

$searchNotifNorm = str_replace("\r\n", "\n", $searchNotif);
if (strpos($crContentNorm, $searchNotifNorm) !== false) {
    $crContentNorm = str_replace($searchNotifNorm, str_replace("\r\n", "\n", $replaceNotif), $crContentNorm);
    echo "Updated cr/index.blade.php NOTIFIKASI card successfully!\n";
} else {
    echo "Warning: searchNotif not found in cr/index.blade.php\n";
}

file_put_contents($crFile, $crContentNorm);
echo "cr/index.blade.php written successfully!\n";
