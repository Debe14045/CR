<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'CR Manager • Monitoring Change Request')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @php
        $roleMeta = [
            'client'   => ['brand' => '#007DFF', 'brand_dark' => '#0062CC', 'soft' => '#EFF6FF', 'border' => '#BFDBFE', 'icon' => 'bi-person-workspace', 'label' => 'Client', 'desc' => 'Pengajuan & Tracking Status'],
            'pm'       => ['brand' => '#059669', 'brand_dark' => '#047857', 'soft' => '#ECFDF5', 'border' => '#A7F3D0', 'icon' => 'bi-kanban-fill', 'label' => 'Project Manager', 'desc' => 'Verifikasi, Solution Paper, Status & BA'],
            'pmh'      => ['brand' => '#E11D48', 'brand_dark' => '#BE123C', 'soft' => '#FFF1F2', 'border' => '#FECDD3', 'icon' => 'bi-shield-check', 'label' => 'PM Head', 'desc' => 'Validasi CR & Validasi Go-Live'],
            'presales' => ['brand' => '#0063D7', 'brand_dark' => '#02376A', 'soft' => '#EFF6FF', 'border' => '#BFDBFE', 'icon' => 'bi-briefcase-fill', 'label' => 'Presales', 'desc' => 'Presales Dashboard & Pipeline CR'],
            'admin'    => ['brand' => '#4F46E5', 'brand_dark' => '#4338CA', 'soft' => '#EEF2FF', 'border' => '#C7D2FE', 'icon' => 'bi-gear-wide-connected', 'label' => 'Administrator', 'desc' => 'Master Data (Client, Pegawai, Status)'],
        ];
        $currentRole = auth()->user()?->role ?? 'guest';
        $rm = $roleMeta[$currentRole] ?? ['brand' => '#007DFF', 'brand_dark' => '#0062CC', 'soft' => '#EFF6FF', 'border' => '#BFDBFE', 'icon' => 'bi-person', 'label' => 'Tamu', 'desc' => 'Akses Publik'];
        $currentRouteName = Route::currentRouteName() ?? '';
    @endphp

    <style>
        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           DESIGN TOKENS
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        :root {
            /* Sidebar */
            --sidebar-bg:         #02376A;
            --sidebar-dark:       #01244A;
            --sidebar-accent:     #00B4D8;
            --sidebar-text:       rgba(255,255,255,0.88);
            --sidebar-text-muted: rgba(255,255,255,0.65);
            --sidebar-active-bg:  #0063D7;
            --sidebar-hover-bg:   rgba(255,255,255,0.08);
            --sidebar-border:     rgba(255,255,255,0.08);
            --sidebar-width:      235px;
            --sidebar-collapsed:  68px;

            /* Topbar */
            --topbar-bg:    #F0F3F6;
            --topbar-h:     72px;
            --topbar-line:  #E2E8F0;

            /* Canvas & Panels */
            --bg-canvas:        #FFFFFF;
            --panel:            #FFFFFF;
            --panel-secondary:  #F1F5F9;
            --line:             #E2E8F0;
            --line-soft:        #F1F5F9;

            /* Role accent */
            --brand:        {{ $rm['brand'] }};
            --brand-dark:   {{ $rm['brand_dark'] }};
            --brand-soft:   {{ $rm['soft'] }};
            --brand-border: {{ $rm['border'] }};

            /* Typography */
            --muted:   #64748B;
            --text:    #334155;
            --heading: #0F172A;
            --font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-mono: 'Space Grotesk', ui-monospace, monospace;

            /* Shadows */
            --shadow-xs: 0 1px 2px 0 rgba(0,0,0,0.05);
            --shadow-sm: 0 1px 3px 0 rgba(0,0,0,0.08), 0 1px 2px -1px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 12px -2px rgba(15,23,42,0.08), 0 2px 4px -2px rgba(15,23,42,0.04);
            --shadow-lg: 0 10px 30px -4px rgba(15,23,42,0.10), 0 4px 8px -4px rgba(15,23,42,0.04);
            --shadow-sidebar: 4px 0 24px rgba(15,23,42,0.15);

            /* Radius */
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           DARK THEME
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        body.dark-theme {
            --bg-canvas:       #090E17;
            --panel:           #111827;
            --panel-secondary: #1E293B;
            --line:            #1F2937;
            --line-soft:       #1E293B;
            --muted:           #94A3B8;
            --text:            #CBD5E1;
            --heading:         #F8FAFC;
            --brand-soft:      rgba(0, 125, 255, 0.12);
            --brand-border:    rgba(0, 125, 255, 0.30);
            --topbar-bg:       #111827;
            --topbar-line:     #1F2937;
        }

        body.dark-theme .main-topbar,
        body.dark-theme .card,
        body.dark-theme .modal-content {
            background-color: var(--panel) !important;
            border-color: var(--line) !important;
            color: var(--text) !important;
        }

        body.dark-theme .bg-light,
        body.dark-theme .table-light,
        body.dark-theme .cr-table thead th,
        body.dark-theme .table thead th,
        body.dark-theme .cr-table thead tr,
        body.dark-theme .table thead tr,
        body.dark-theme .table thead {
            background-color: var(--panel-secondary) !important;
            --bs-table-bg: var(--panel-secondary) !important;
            color: var(--muted) !important;
            border-color: var(--line) !important;
        }

        body.dark-theme .form-control,
        body.dark-theme .form-select,
        body.dark-theme .input-group-text {
            background-color: var(--panel-secondary) !important;
            border-color: var(--line) !important;
            color: var(--heading) !important;
        }

        body.dark-theme .cr-code-badge {
            background-color: var(--panel-secondary);
            border-color: var(--line);
            color: #60A5FA;
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           RESET & BASE
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; height: 100%; }

        body {
            background: var(--bg-canvas);
            color: var(--text);
            font-family: var(--font-sans);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            transition: background-color 0.2s ease, color 0.2s ease;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6, .fw-title {
            font-family: var(--font-sans);
            color: var(--heading);
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        .mono, .cr-code, .metric-number, .money {
            font-family: var(--font-mono);
            letter-spacing: -0.01em;
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           APP SHELL (Sidebar + Content)
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        .app-sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: #02376A !important;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12) !important;
            transition: transform 0.3s cubic-bezier(0.16,1,0.3,1), width 0.3s cubic-bezier(0.16,1,0.3,1);
            overflow: hidden;
            border-right: 2px solid #0080FF !important;
        }

        .app-sidebar .sidebar-link {
            color: #FFFFFF !important;
            padding: 0.65rem 1rem !important;
            border-radius: 9999px !important;
            font-size: 0.88rem !important;
            font-weight: 500 !important;
            transition: all 0.15s ease !important;
        }
        .app-sidebar .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.12) !important;
            color: #FFFFFF !important;
        }
        .app-sidebar .sidebar-link.active {
            background: #0063D7 !important;
            color: #FFFFFF !important;
            font-weight: 600 !important;
            box-shadow: none !important;
        }
        .app-sidebar .sidebar-link.active::before {
            display: none !important;
        }

        .topbar-search-input::placeholder {
            color: #94A3B8 !important;
            opacity: 1 !important;
        }

        /* ── Modern Dropdown Styling & Reset Bootstrap Blue Active ── */
        .dropdown-menu {
            border: 1px solid #E2E8F0 !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04) !important;
        }
        .dropdown-item {
            border-radius: 8px !important;
            transition: background-color 0.15s ease, color 0.15s ease !important;
            color: #1E293B !important;
        }
        .dropdown-item:hover,
        .dropdown-item:focus {
            background-color: #F1F5F9 !important;
            color: #0F172A !important;
        }
        .dropdown-item:active {
            background-color: #E2E8F0 !important;
            color: #0F172A !important;
        }
        .dropdown-item.text-danger {
            color: #DC2626 !important;
        }
        .dropdown-item.text-danger:hover,
        .dropdown-item.text-danger:focus {
            background-color: #FEE2E2 !important;
            color: #B91C1C !important;
        }
        .dropdown-item.text-danger:active {
            background-color: #FECACA !important;
            color: #991B1B !important;
        }

        /* ── Sidebar Header (Logo) ── */
        .sidebar-header {
            padding: 1.4rem 1.4rem 1rem;
            border-bottom: 1px solid var(--sidebar-border);
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .sidebar-brand .brand-icon {
            width: 38px;
            height: 38px;
            min-width: 38px;
            border-radius: 11px;
            background: linear-gradient(135deg, #007DFF 0%, #0062CC 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.15rem;
            box-shadow: 0 4px 16px rgba(0,125,255,0.45);
            transition: transform 0.2s ease;
        }

        .sidebar-brand:hover .brand-icon {
            transform: scale(1.05);
        }

        .sidebar-brand .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            overflow: hidden;
        }

        .sidebar-brand .brand-name {
            font-size: 0.95rem;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.02em;
            white-space: nowrap;
        }

        .sidebar-brand .brand-sub {
            font-size: 0.68rem;
            font-weight: 500;
            color: var(--sidebar-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.07em;
            white-space: nowrap;
        }

        /* ── Sidebar Navigation ── */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.75rem 0.75rem 0;
            position: relative;
            z-index: 1;
        }

        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: var(--sidebar-border); border-radius: 4px; }

        .nav-section-label {
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--sidebar-text-muted);
            padding: 0.9rem 0.6rem 0.4rem;
            display: block;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.62rem 0.75rem;
            border-radius: var(--radius-sm);
            text-decoration: none;
            color: var(--sidebar-text);
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.18s ease;
            position: relative;
            white-space: nowrap;
            margin-bottom: 2px;
        }

        .sidebar-link:hover {
            background: var(--sidebar-hover-bg);
            color: rgba(255,255,255,0.92);
        }

        .sidebar-link.active {
            background: var(--sidebar-active-bg);
            color: #FFFFFF;
            font-weight: 600;
            border-radius: 9999px;
        }

        .sidebar-link.active::before {
            display: none;
        }

        .sidebar-link .nav-icon {
            font-size: 1rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
            opacity: 0.85;
            transition: opacity 0.15s ease;
        }

        .sidebar-link.active .nav-icon,
        .sidebar-link:hover .nav-icon {
            opacity: 1;
        }

        .sidebar-link .nav-badge {
            margin-left: auto;
            background: #EF4444;
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.15rem 0.45rem;
            border-radius: 999px;
            min-width: 18px;
            text-align: center;
            line-height: 1.4;
        }

        .sidebar-link .nav-badge.badge-yellow {
            background: #F59E0B;
        }

        /* Sub-nav (for expandable groups) */
        .sidebar-sub {
            padding-left: 0.5rem;
            margin-bottom: 2px;
        }

        .sidebar-sub .sidebar-link {
            font-size: 0.835rem;
            padding: 0.52rem 0.75rem 0.52rem 0.65rem;
            color: var(--sidebar-text-muted);
        }

        .sidebar-sub .sidebar-link:hover {
            color: rgba(255,255,255,0.85);
        }

        .sidebar-sub .sidebar-link.active {
            color: #FFFFFF;
            background: rgba(0,125,255,0.2);
        }

        .nav-group-toggle {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.62rem 0.75rem;
            border-radius: var(--radius-sm);
            cursor: pointer;
            color: var(--sidebar-text);
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.18s ease;
            white-space: nowrap;
            margin-bottom: 2px;
            user-select: none;
        }

        .nav-group-toggle:hover {
            background: var(--sidebar-hover-bg);
            color: rgba(255,255,255,0.92);
        }

        .nav-group-toggle .toggle-chevron {
            margin-left: auto;
            font-size: 0.75rem;
            transition: transform 0.2s ease;
            opacity: 0.6;
        }

        .nav-group-toggle.open .toggle-chevron {
            transform: rotate(180deg);
        }

        .nav-group-body {
            overflow: hidden;
            transition: max-height 0.25s ease, opacity 0.2s ease;
            max-height: 0;
            opacity: 0;
        }

        .nav-group-body.open {
            max-height: 300px;
            opacity: 1;
        }

        .sidebar-divider {
            height: 1px;
            background: var(--sidebar-border);
            margin: 0.6rem 0.25rem;
        }

        /* ── Sidebar Footer (User Profile) ── */
        .sidebar-footer {
            border-top: 1px solid var(--sidebar-border);
            padding: 0.9rem 1rem;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }

        .sidebar-avatar {
            width: 36px;
            height: 36px;
            min-width: 36px;
            border-radius: 10px;
            background: var(--sidebar-active-bg);
            border: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.8);
            font-size: 0.95rem;
            overflow: hidden;
        }

        .sidebar-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sidebar-user-info {
            flex: 1;
            overflow: hidden;
        }

        .sidebar-user-name {
            font-size: 0.83rem;
            font-weight: 700;
            color: #FFFFFF;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-role {
            font-size: 0.68rem;
            font-weight: 500;
            color: var(--sidebar-text-muted);
            white-space: nowrap;
        }

        .sidebar-footer-actions {
            display: flex;
            gap: 0.35rem;
            margin-top: 0.6rem;
        }

        .sidebar-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.42rem 0.6rem;
            border-radius: 7px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid var(--sidebar-border);
            background: var(--sidebar-hover-bg);
            color: var(--sidebar-text);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .sidebar-btn:hover {
            background: var(--sidebar-active-bg);
            color: rgba(255,255,255,0.92);
            border-color: rgba(255,255,255,0.2);
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           MAIN CONTENT AREA
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
            transition: margin-left 0.3s cubic-bezier(0.16,1,0.3,1);
        }

        /* ── Topbar ── */
        .main-topbar {
            height: var(--topbar-h);
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--topbar-line);
            display: flex;
            align-items: center;
            padding: 0 1.75rem;
            position: sticky;
            top: 0;
            z-index: 1030;
            gap: 1rem;
            box-shadow: 0 1px 4px rgba(15,23,42,0.04);
        }

        /* Hamburger (mobile only) */
        .topbar-hamburger {
            display: none;
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--line);
            background: var(--panel-secondary);
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--muted);
            font-size: 1.1rem;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }

        .topbar-hamburger:hover {
            background: var(--line);
            color: var(--heading);
        }

        .topbar-breadcrumb {
            flex: 1;
        }

        .topbar-page-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--heading);
            margin: 0;
            letter-spacing: -0.02em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-page-sub {
            font-size: 0.72rem;
            color: var(--muted);
            font-weight: 500;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-shrink: 0;
        }

        .topbar-icon-btn {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--line);
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .topbar-icon-btn:hover {
            background: var(--panel-secondary);
            border-color: var(--brand-border);
            color: var(--brand);
        }

        .topbar-divider {
            width: 1px;
            height: 24px;
            background: var(--line);
        }

        .topbar-user-pill {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.35rem 0.75rem 0.35rem 0.4rem;
            border-radius: 999px;
            background: var(--brand-soft);
            border: 1px solid var(--brand-border);
            text-decoration: none;
        }

        .topbar-user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--brand);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            overflow: hidden;
            flex-shrink: 0;
        }

        .topbar-user-avatar img {
            width: 100%; height: 100%; object-fit: cover;
        }

        .topbar-user-name {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--heading);
        }

        .topbar-user-role {
            font-size: 0.7rem;
            font-weight: 500;
            color: var(--muted);
        }

        /* ── Page Content ── */
        .main-content {
            flex: 1;
            padding: 1.75rem 1.75rem 3rem;
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           PAGE HEADER / SHELL COMPATIBILITY
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        /* Keep .page-shell divs in views as transparent wrappers */
        .page-shell { /* intentionally empty — layout provides spacing */ }

        .page-header {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 1.5rem 1.75rem;
            margin-bottom: 1.75rem;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--brand), color-mix(in srgb, var(--brand) 40%, transparent));
        }

        .page-header .eyebrow {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            color: var(--brand);
            margin-bottom: 0.35rem;
        }

        /* Role Banner */
        .role-banner {
            background: linear-gradient(135deg, var(--brand-soft) 0%, var(--panel) 100%);
            border: 1px solid var(--brand-border);
            border-radius: var(--radius-lg);
            padding: 1.4rem 1.6rem;
            margin-bottom: 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
            box-shadow: var(--shadow-sm);
        }

        .role-banner__left {
            display: flex;
            align-items: center;
            gap: 1.15rem;
        }

        .role-banner .icon-wrap {
            width: 52px;
            height: 52px;
            min-width: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            box-shadow: 0 6px 16px color-mix(in srgb, var(--brand) 30%, transparent);
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           CARDS
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: color-mix(in srgb, var(--brand) 40%, var(--line));
        }

        /* KPI Cards */
        .kpi-card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 1.35rem 1.5rem;
            height: 100%;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .kpi-card .kpi-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 0.9rem;
        }

        .kpi-card .kpi-value {
            font-family: var(--font-mono);
            font-size: 2rem;
            font-weight: 700;
            color: var(--heading);
            line-height: 1.1;
            margin-bottom: 0.25rem;
        }

        .kpi-card .kpi-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .kpi-card .kpi-footer {
            margin-top: 0.85rem;
            padding-top: 0.75rem;
            border-top: 1px solid var(--line-soft);
            font-size: 0.78rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           STATUS PILLS & BADGES
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.35rem 0.8rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: capitalize;
        }

        .status-pill .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-pill--diajukan,
        .status-pill--awaiting_pm    { background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; }
        .status-pill--diajukan .dot,
        .status-pill--awaiting_pm .dot { background: #D97706; }

        .status-pill--dianalisis,
        .status-pill--awaiting_pmh   { background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; }
        .status-pill--dianalisis .dot,
        .status-pill--awaiting_pmh .dot { background: #2563EB; }

        .status-pill--disetujui,
        .status-pill--validated      { background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; }
        .status-pill--disetujui .dot,
        .status-pill--validated .dot { background: #059669; }

        .status-pill--ditolak,
        .status-pill--revision_needed { background: #FFF1F2; color: #BE123C; border: 1px solid #FECDD3; }
        .status-pill--ditolak .dot,
        .status-pill--revision_needed .dot { background: #E11D48; }

        .status-pill--dikerjakan,
        .status-pill--development    { background: #F0FDF4; color: #15803D; border: 1px solid #BBF7D0; }
        .status-pill--dikerjakan .dot,
        .status-pill--development .dot { background: #16A34A; }

        .status-pill--analisa        { background: #F0F9FF; color: #0369A1; border: 1px solid #BAE6FD; }
        .status-pill--analisa .dot   { background: #0284C7; }

        .status-pill--sit,
        .status-pill--uat            { background: #FEF3C7; color: #B45309; border: 1px solid #FCD34D; }
        .status-pill--sit .dot,
        .status-pill--uat .dot       { background: #F59E0B; }

        .status-pill--training       { background: #F5F3FF; color: #6D28D9; border: 1px solid #DDD6FE; }
        .status-pill--training .dot  { background: #7C3AED; }

        .status-pill--awaiting_golive_validation { background: #F3E8FF; color: #7E22CE; border: 1px solid #E9D5FF; }
        .status-pill--awaiting_golive_validation .dot { background: #9333EA; }

        .status-pill--selesai,
        .status-pill--golive         { background: #ECFDF5; color: #065F46; border: 1px solid #6EE7B7; }
        .status-pill--selesai .dot,
        .status-pill--golive .dot    { background: #10B981; }

        .status-pill--invoicing      { background: #F0FDFA; color: #0F766E; border: 1px solid #99F6E4; }
        .status-pill--invoicing .dot { background: #0D9488; }

        /* Overdue */
        .tr-overdue {
            background-color: rgba(254,226,226,0.35) !important;
            border-left: 4px solid #EF4444 !important;
        }

        .badge-overdue {
            background-color: #FEF2F2 !important;
            border: 1px solid #F87171 !important;
            color: #B91C1C !important;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 0.25rem 0.55rem;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            animation: pulseOverdue 2s infinite ease-in-out;
        }

        @keyframes pulseOverdue {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.3); }
            50%       { box-shadow: 0 0 0 5px rgba(239,68,68,0); }
        }

        /* Priority Badges */
        .priority-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .priority-badge--kritis { background: #FFE4E6; color: #BE123C; border: 1px solid #FECDD3; }
        .priority-badge--tinggi { background: #FEF3C7; color: #B45309; border: 1px solid #FDE68A; }
        .priority-badge--normal { background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0; }
        .priority-badge--rendah { background: #F8FAFC; color: #64748B; border: 1px solid #E2E8F0; }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           TABLE
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .cr-table {
            width: 100%;
            margin-bottom: 0;
            vertical-align: middle;
        }

        .cr-table thead th,
        .table thead th {
            background-color: #ECF1F9 !important;
            --bs-table-bg: #ECF1F9 !important;
            --bs-table-accent-bg: transparent !important;
            padding: 1rem 1.25rem;
            color: #1E293B;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 1px solid var(--line);
            white-space: nowrap;
        }

        .cr-table thead,
        .table thead,
        .cr-table thead tr,
        .table thead tr {
            background-color: #ECF1F9 !important;
            --bs-table-bg: #ECF1F9 !important;
            --bs-table-accent-bg: transparent !important;
        }

        .cr-table tbody td {
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid var(--line-soft);
            color: var(--text);
            font-size: 0.88rem;
        }

        .cr-table tbody tr {
            transition: background 0.15s ease;
        }

        .cr-table tbody tr:hover {
            background: rgba(0,125,255,0.025);
        }

        .cr-code-badge {
            font-family: var(--font-mono);
            font-weight: 700;
            font-size: 0.82rem;
            color: var(--heading);
            background: #F1F5F9;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            border: 1px solid #E2E8F0;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .copy-btn {
            border: none;
            background: transparent;
            color: var(--muted);
            padding: 0 0.15rem;
            font-size: 0.78rem;
            cursor: pointer;
            transition: color 0.15s ease;
        }

        .copy-btn:hover { color: var(--brand); }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           BUTTONS
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .btn {
            font-family: var(--font-sans);
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: var(--radius-sm);
            padding: 0.55rem 1.15rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.18s ease;
        }

        .btn-primary {
            background: var(--brand);
            border-color: var(--brand);
            color: #FFFFFF;
            box-shadow: 0 2px 6px color-mix(in srgb, var(--brand) 30%, transparent);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: var(--brand-dark);
            border-color: var(--brand-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px color-mix(in srgb, var(--brand) 35%, transparent);
            color: #FFFFFF;
        }

        .btn-outline-brand {
            background: #FFFFFF;
            border: 1px solid var(--brand-border);
            color: var(--brand);
        }

        .btn-outline-brand:hover {
            background: var(--brand-soft);
            border-color: var(--brand);
            color: var(--brand-dark);
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           FORMS
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .form-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--heading);
            margin-bottom: 0.45rem;
        }

        .form-control, .form-select {
            font-family: var(--font-sans);
            font-size: 0.88rem;
            border-radius: var(--radius-sm);
            border: 1px solid #CBD5E1;
            padding: 0.6rem 0.85rem;
            color: var(--heading);
            background-color: #FFFFFF;
            transition: all 0.15s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--brand) 18%, transparent);
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           STEPPER
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .stepper-track {
            display: flex;
            align-items: flex-start;
            position: relative;
            padding: 1.5rem 0.5rem 0.5rem;
            overflow-x: auto;
        }

        .stepper-node {
            flex: 1;
            text-align: center;
            position: relative;
            min-width: 95px;
        }

        .stepper-node:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 17px;
            left: 50%;
            width: 100%;
            height: 3px;
            background: #E2E8F0;
            z-index: 1;
        }

        .stepper-node.is-done:not(:last-child)::after   { background: #10B981; }
        .stepper-node.is-active:not(:last-child)::after { background: #E2E8F0; }

        .stepper-dot {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #FFFFFF;
            border: 2px solid #CBD5E1;
            color: #64748B;
            font-weight: 700;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.6rem;
            position: relative;
            z-index: 2;
            transition: all 0.2s ease;
        }

        .stepper-node.is-done .stepper-dot     { background: #10B981; border-color: #10B981; color: #FFFFFF; }
        .stepper-node.is-active .stepper-dot   {
            background: var(--brand);
            border-color: var(--brand);
            color: #FFFFFF;
            box-shadow: 0 0 0 5px color-mix(in srgb, var(--brand) 20%, transparent);
        }
        .stepper-node.is-rejected .stepper-dot { background: #E11D48; border-color: #E11D48; color: #FFFFFF; }

        .stepper-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--muted);
            display: block;
        }

        .stepper-node.is-active .stepper-label,
        .stepper-node.is-done .stepper-label { color: var(--heading); font-weight: 700; }

        /* Status Mini-Progress Bar */
        .status-minibar       { display: flex; gap: 3px; margin-top: 4px; width: 90px; }
        .status-minibar-seg   { height: 4px; flex: 1; background: #E2E8F0; border-radius: 2px; }
        .status-minibar-seg.active   { background: var(--brand); }
        .status-minibar-seg.done     { background: #10B981; }
        .status-minibar-seg.rejected { background: #E11D48; }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           MICRO ANIMATIONS
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        @keyframes pulseGlow {
            0%   { transform: scale(0.98); opacity: 0.85; }
            50%  { transform: scale(1.02); opacity: 1; }
            100% { transform: scale(0.98); opacity: 0.85; }
        }

        .pulse-active { animation: pulseGlow 2.5s infinite ease-in-out; }

        .card-lift { transition: all 0.25s cubic-bezier(0.16,1,0.3,1); }
        .card-lift:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }

        /* Toast */
        .toast-container-fixed {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1090;
        }

        /* Sidebar Overlay (mobile) */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(9,14,23,0.55);
            z-index: 1039;
            backdrop-filter: blur(2px);
        }

        .sidebar-overlay.visible { display: block; }

        /* Footer */
        .main-footer {
            background: var(--panel);
            border-top: 1px solid var(--line);
            padding: 1rem 1.75rem;
            font-size: 0.8rem;
            color: var(--muted);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           RESPONSIVE
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        @media (max-width: 991.98px) {
            .app-sidebar {
                transform: translateX(-100%);
            }

            .app-sidebar.sidebar-open {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100vw !important;
            }

            .topbar-hamburger {
                display: flex;
            }

            .main-content {
                padding: 1.25rem 1rem 3rem;
            }

            .main-topbar {
                padding: 0 1rem;
            }
        }

        @media (max-width: 575.98px) {
            .topbar-user-role,
            .topbar-page-sub { display: none; }
            .main-content { padding: 1rem 0.75rem 3rem; }
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           MISC UTILITIES
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .required-mark { color: #E11D48; font-weight: 700; }
        .eyebrow {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            color: var(--brand);
            margin-bottom: 0.35rem;
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           PAGINATION (Bootstrap 5 & Figma Modern)
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .pagination {
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
            margin-bottom: 0 !important;
            padding-left: 0 !important;
            list-style: none !important;
        }
        .pagination .page-item {
            margin: 0 !important;
        }
        .pagination .page-item .page-link {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 36px !important;
            height: 36px !important;
            padding: 0 0.65rem !important;
            font-size: 0.84rem !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            border: 1px solid #E2E8F0 !important;
            color: #334155 !important;
            background: #FFFFFF !important;
            text-decoration: none !important;
            transition: all 0.15s ease !important;
        }
        .pagination .page-item.active .page-link {
            background: #3454E2 !important;
            border-color: #3454E2 !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 10px rgba(52, 84, 226, 0.3) !important;
        }
        .pagination .page-item:not(.active):not(.disabled) .page-link:hover {
            background: #F1F5F9 !important;
            border-color: #CBD5E1 !important;
            color: #0F172A !important;
            transform: translateY(-1px);
        }
        .pagination .page-item.disabled .page-link {
            background: #F8FAFC !important;
            border-color: #E2E8F0 !important;
            color: #94A3B8 !important;
            cursor: not-allowed !important;
            opacity: 0.65 !important;
        }

        /* Guard against unstyled SVG icons anywhere in pagination */
        nav[role="navigation"] svg,
        .pagination svg,
        .page-link svg {
            width: 14px !important;
            height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           PRINT
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        @media print {
            .app-sidebar, .main-topbar, .main-footer,
            .btn, .toast-container-fixed, .d-print-none { display: none !important; }
            .main-wrapper { 
                margin-left: 0 !important; 
                width: 100% !important;
                max-width: none !important;
            }
            body { background: #FFFFFF !important; color: #000 !important; }
            .card { border: 1px solid #E2E8F0 !important; box-shadow: none !important; break-inside: avoid; }
            .main-content { padding: 0 !important; }
        }
    </style>
</head>
<body class="role-{{ $currentRole }}">

<div class="app-shell">

    {{-- ══════════════════════════════════════
         SIDEBAR (Figma #02376A Dark Royal Navy)
    ══════════════════════════════════════ --}}
    @auth
    <aside class="app-sidebar" id="appSidebar" role="navigation" aria-label="Navigasi Utama" style="background: #02376A; border-right: 1px solid rgba(255,255,255,0.06); width: 235px;">

        {{-- Brand / Logo (Figma: ITPI PT. ITPI Technology) --}}
        @php
            $brandHomeUrl = route('change-requests.index');
            if (auth()->check()) {
                if (auth()->user()->isPresales()) {
                    $brandHomeUrl = route('presales.dashboard');
                } elseif (auth()->user()->isPmh()) {
                    $brandHomeUrl = route('pmh.dashboard');
                }
            }
        @endphp
        <div class="sidebar-header" style="padding: 1.35rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06);">
            <a class="sidebar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ $brandHomeUrl }}">
                <img src="{{ asset('images/itpi_logo_white_tight.png') }}" alt="ITPI" style="height: 34px; width: auto; object-fit: contain;">
                <div style="color: #FFFFFF; font-weight: 800; font-size: 0.95rem; letter-spacing: -0.01em; white-space: nowrap;">
                    PT. ITPI Technology
                </div>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav" style="padding: 1.1rem 0.85rem;">

            {{-- MENU Header --}}
            <span class="nav-section-label" data-i18n="menu_header" style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em; color: rgba(255,255,255,0.7); padding: 0.2rem 0.75rem 0.6rem; text-transform: uppercase; display: block;">
                MENU
            </span>

            @if ($currentRole === 'presales')
                {{-- ══ PRESALES DEDICATED SIDEBAR ══ --}}
                @php
                    $isPresalesDashboardActive = request()->routeIs('presales.dashboard') || ($currentRouteName === 'change-requests.index' && (!request()->filled('view') || request('view') === 'dashboard') && !request()->filled('search') && !request()->filled('status'));
                    $isPresalesReview = request()->routeIs('presales.review');
                    $isPresalesCrGroupActive = request()->routeIs('presales.change-requests') || request()->routeIs('presales.persetujuan') || request()->routeIs('presales.development') || $isPresalesReview;
                @endphp

                {{-- 1. Dashboard --}}
                <a href="{{ route('presales.dashboard') }}"
                   class="sidebar-link {{ $isPresalesDashboardActive ? 'active' : '' }}"
                   style="position: relative; border-radius: 9999px; padding: 0.65rem 1rem; font-size: 0.88rem; {{ $isPresalesDashboardActive ? 'background: #0063D7 !important; color: #FFFFFF !important;' : '' }}">
                    <i class="bi bi-grid-fill nav-icon" style="font-size: 1.05rem;"></i>
                    <span class="fw-semibold">Dashboard</span>
                </a>

                {{-- 2. Change Request Accordion Group (Semua CR, Butuh Persetujuan, Development CR) --}}
                <div class="nav-group-toggle open"
                     id="presalesCrGroupToggle"
                     role="button"
                     onclick="this.classList.toggle('open'); document.getElementById('presalesCrGroupBody').classList.toggle('open');"
                     aria-expanded="true"
                     style="border-radius: 9999px; padding: 0.65rem 1.15rem; font-size: 0.92rem; display: flex; align-items: center; gap: 0.75rem; color: #FFFFFF; margin-top: 0.35rem; {{ $isPresalesCrGroupActive ? 'background: #0063D7 !important;' : 'background: transparent;' }}">
                    <i class="bi bi-file-earmark-text nav-icon" style="font-size: 1.05rem; opacity: 1;"></i>
                    <span class="fw-bold" style="letter-spacing: -0.01em;">Change Request</span>
                    <i class="bi bi-chevron-up toggle-chevron ms-auto" style="font-size: 0.8rem; font-weight: 700; opacity: 0.95;"></i>
                </div>

                <div class="nav-group-body sidebar-sub open" id="presalesCrGroupBody" style="padding-left: 0.45rem; display: flex; flex-direction: column; gap: 0.35rem; margin-top: 0.35rem;">
                    {{-- 2a. Semua CR (Active with blue dot on left, transparent background matching Figma) --}}
                    @php $isPresalesSemuaCr = request()->routeIs('presales.change-requests') || ($isPresalesReview && !request()->routeIs('presales.persetujuan') && !request()->routeIs('presales.development')); @endphp
                    <a href="{{ route('presales.change-requests') }}"
                       class="sidebar-link {{ $isPresalesSemuaCr ? 'active' : '' }}"
                       style="border-radius: 9999px; padding: 0.55rem 1.15rem; font-size: 0.88rem; color: #FFFFFF; display: flex; align-items: center; gap: 0.65rem; background: transparent !important;">
                        @if($isPresalesSemuaCr)
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #0084FF; display: inline-block; flex-shrink: 0; box-shadow: 0 0 8px #0084FF;"></span>
                        @endif
                        <i class="bi bi-file-earmark-text nav-icon" style="font-size: 1.05rem; color: #FFFFFF; opacity: 1;"></i>
                        <span class="fw-bold" style="color: #FFFFFF; letter-spacing: -0.01em;">Semua CR</span>
                    </a>

                    {{-- 2b. Butuh Persetujuan --}}
                    @php $isPresalesPersetujuan = request()->routeIs('presales.persetujuan'); @endphp
                    <a href="{{ route('presales.persetujuan') }}"
                       class="sidebar-link {{ $isPresalesPersetujuan ? 'active' : '' }}"
                       style="border-radius: 9999px; padding: 0.55rem 1.15rem; font-size: 0.86rem; color: rgba(255,255,255,0.85); display: flex; align-items: center; gap: 0.65rem; background: transparent !important;">
                        @if($isPresalesPersetujuan)
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #0084FF; display: inline-block; flex-shrink: 0; box-shadow: 0 0 8px #0084FF;"></span>
                        @endif
                        <i class="bi bi-file-earmark-check nav-icon" style="font-size: 0.95rem;"></i>
                        <span>Butuh Persetujuan</span>
                    </a>

                    {{-- 2c. Development CR --}}
                    @php $isPresalesDev = request()->routeIs('presales.development'); @endphp
                    <a href="{{ route('presales.development') }}"
                       class="sidebar-link {{ $isPresalesDev ? 'active' : '' }}"
                       style="border-radius: 9999px; padding: 0.55rem 1.15rem; font-size: 0.86rem; color: rgba(255,255,255,0.85); display: flex; align-items: center; gap: 0.65rem; background: transparent !important;">
                        @if($isPresalesDev)
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #0084FF; display: inline-block; flex-shrink: 0; box-shadow: 0 0 8px #0084FF;"></span>
                        @endif
                        <i class="bi bi-bar-chart-steps nav-icon" style="font-size: 0.95rem;"></i>
                        <span>Development CR</span>
                    </a>
                </div>

                {{-- 3. Notifikasi --}}
                @php
                    $isNotifActive = request()->routeIs('presales.notifications');
                    $presalesNotifCount = auth()->check() ? \App\Models\Notification::forUser(auth()->user())->unread()->count() : 0;
                @endphp
                <a href="{{ route('presales.notifications') }}"
                   class="sidebar-link d-flex align-items-center justify-content-between {{ $isNotifActive ? 'active' : '' }}"
                   style="border-radius: 9999px; padding: 0.65rem 1rem; font-size: 0.88rem; color: #FFFFFF; margin-top: 0.35rem; {{ $isNotifActive ? 'background: #0063D7 !important;' : '' }}">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-bell nav-icon" style="font-size: 1.05rem;"></i>
                        <span class="fw-medium">Notifikasi</span>
                    </span>
                    @if ($presalesNotifCount > 0)
                        <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">{{ $presalesNotifCount }}</span>
                    @endif
                </a>

                {{-- 4. Pengaturan --}}
                <a href="{{ route('profile') }}"
                   class="sidebar-link"
                   style="border-radius: 10px; padding: 0.65rem 1rem; font-size: 0.88rem; color: rgba(255,255,255,0.85); margin-top: 0.35rem;">
                    <i class="bi bi-gear nav-icon" style="font-size: 1.05rem;"></i>
                    <span class="fw-medium">Pengaturan</span>
                </a>

            @elseif ($currentRole === 'pmh')
                {{-- ══ PM HEAD DEDICATED SIDEBAR (SESUAI GAMBAR FIGMA) ══ --}}
                @php
                    $isPmhDashboardActive = request()->routeIs('pmh.dashboard') || ($currentRouteName === 'change-requests.index' && (!request()->filled('view') || request('view') === 'dashboard') && !request()->filled('search') && !request()->filled('status'));
                    $isPmhCrActive = request()->routeIs('pmh.change-requests') || request()->routeIs('pmh.persetujuan') || request()->routeIs('pmh.development') || request()->routeIs('pmh.golive') || request()->routeIs('pmh.review');
                    $isPmhSemuaCr = request()->routeIs('pmh.change-requests') || (request()->routeIs('pmh.review') && !request()->routeIs('pmh.persetujuan') && !request()->routeIs('pmh.development'));
                    $isPmhPersetujuan = request()->routeIs('pmh.persetujuan');
                    $isPmhDev = request()->routeIs('pmh.development') || request()->routeIs('pmh.golive');
                    $isPmhPayment = request()->routeIs('pmh.outstanding-payment*');
                    $isNotifActive = request()->routeIs('pmh.notifications');
                    $isProfileActive = request()->routeIs('profile');
                    $sidebarNotifCount = auth()->check() ? \App\Models\Notification::forUser(auth()->user())->unread()->count() : 0;
                @endphp

                {{-- 1. Dashboard (Active Blue Capsule) --}}
                <a href="{{ route('pmh.dashboard') }}"
                   class="sidebar-link {{ $isPmhDashboardActive ? 'active' : '' }}"
                   style="position: relative; border-radius: 9999px; padding: 0.65rem 1.15rem; font-size: 0.92rem; display: flex; align-items: center; gap: 0.75rem; color: #FFFFFF; {{ $isPmhDashboardActive ? 'background: #0063D7 !important; color: #FFFFFF !important;' : '' }}">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                        <rect width="7" height="7" x="3" y="3" rx="1" />
                        <rect width="7" height="7" x="14" y="3" rx="1" />
                        <rect width="7" height="7" x="14" y="14" rx="1" />
                        <rect width="7" height="7" x="3" y="14" rx="1" />
                    </svg>
                    <span class="{{ $isPmhDashboardActive ? 'fw-bold' : 'fw-semibold' }}" style="font-size: 0.95rem;">Dashboard</span>
                </a>

                {{-- 2. Change Request (Accordion Group with Chevron Up) --}}
                <div class="nav-group-toggle open"
                     id="pmhCrGroupToggle"
                     role="button"
                     onclick="this.classList.toggle('open'); document.getElementById('pmhCrGroupBody').classList.toggle('open'); const c = this.querySelector('.toggle-chevron'); if(c){ c.classList.toggle('bi-chevron-up'); c.classList.toggle('bi-chevron-down'); }"
                     aria-expanded="true"
                     style="border-radius: 9999px; padding: 0.65rem 1.15rem; font-size: 0.92rem; display: flex; align-items: center; gap: 0.75rem; color: #FFFFFF; margin-top: 0.35rem; cursor: pointer; {{ $isPmhCrActive && !$isPmhDashboardActive ? 'background: #0063D7 !important;' : 'background: transparent;' }}">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                        <path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z" />
                        <path d="M14 2v5a1 1 0 0 0 1 1h5" />
                    </svg>
                    <span class="fw-bold" style="font-size: 0.95rem; letter-spacing: -0.01em;">Change Request</span>
                    <i class="bi bi-chevron-up toggle-chevron ms-auto" style="font-size: 0.8rem; font-weight: 700; opacity: 0.95;"></i>
                </div>

                <div class="nav-group-body sidebar-sub open" id="pmhCrGroupBody" style="padding-left: 0.75rem; display: flex; flex-direction: column; gap: 0.4rem; margin-top: 0.35rem;">
                    {{-- 2a. Semua CR --}}
                    <a href="{{ route('pmh.change-requests') }}"
                       class="sidebar-link {{ $isPmhSemuaCr ? 'active' : '' }}"
                       style="border-radius: 9999px; padding: 0.55rem 1.15rem; font-size: 0.92rem; color: #FFFFFF; display: flex; align-items: center; gap: 0.75rem; {{ $isPmhSemuaCr ? 'background: #0063D7 !important; color: #FFFFFF !important;' : 'background: transparent !important; color: rgba(255,255,255,0.9);' }}">
                        <svg class="nav-icon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                            <path d="M15 2h-4a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V8" />
                            <path d="M16.706 2.706A2.4 2.4 0 0 0 15 2v5a1 1 0 0 0 1 1h5a2.4 2.4 0 0 0-.706-1.706z" />
                            <path d="M5 7a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h8a2 2 0 0 0 1.732-1" />
                        </svg>
                        <span class="{{ $isPmhSemuaCr ? 'fw-bold' : 'fw-semibold' }}" style="letter-spacing: -0.01em;">Semua CR</span>
                    </a>

                    {{-- 2b. Butuh Persetujuan --}}
                    <a href="{{ route('pmh.persetujuan') }}"
                       class="sidebar-link {{ $isPmhPersetujuan ? 'active' : '' }}"
                       style="border-radius: 9999px; padding: 0.55rem 1.15rem; font-size: 0.92rem; color: #FFFFFF; display: flex; align-items: center; gap: 0.75rem; {{ $isPmhPersetujuan ? 'background: #0063D7 !important; color: #FFFFFF !important;' : 'background: transparent !important; color: rgba(255,255,255,0.9);' }}">
                        <svg class="nav-icon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                            <path d="M14.364 13.634a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506l4.013-4.009a1 1 0 0 0-3.004-3.004z" />
                            <path d="M14.487 7.858A1 1 0 0 1 14 7V2" />
                            <path d="M20 19.645V20a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l2.516 2.516" />
                            <path d="M8 18h1" />
                        </svg>
                        <span class="{{ $isPmhPersetujuan ? 'fw-bold' : 'fw-semibold' }}" style="letter-spacing: -0.01em; line-height: 1.2;">Butuh Persetujuan</span>
                    </a>

                    {{-- 2c. Development --}}
                    <a href="{{ route('pmh.development') }}"
                       class="sidebar-link {{ $isPmhDev ? 'active' : '' }}"
                       style="border-radius: 9999px; padding: 0.55rem 1.15rem; font-size: 0.92rem; color: #FFFFFF; display: flex; align-items: center; gap: 0.75rem; {{ $isPmhDev ? 'background: #0063D7 !important; color: #FFFFFF !important;' : 'background: transparent !important; color: rgba(255,255,255,0.9);' }}">
                        <svg class="nav-icon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                            <path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z" />
                            <path d="M14 2v5a1 1 0 0 0 1 1h5" />
                            <path d="M8 18v-1" />
                            <path d="M12 18v-6" />
                            <path d="M16 18v-3" />
                        </svg>
                        <span class="{{ $isPmhDev ? 'fw-bold' : 'fw-semibold' }}" style="letter-spacing: -0.01em;">Development</span>
                    </a>
                </div>

                {{-- 3. Outstanding Payment --}}
                <a href="{{ route('pmh.outstanding-payment') }}"
                   class="sidebar-link {{ $isPmhPayment ? 'active' : '' }}"
                   style="border-radius: 9999px; padding: 0.65rem 1.15rem; font-size: 0.92rem; color: #FFFFFF; display: flex; align-items: center; gap: 0.75rem; margin-top: 0.35rem; {{ $isPmhPayment ? 'background: #0063D7 !important; color: #FFFFFF !important;' : '' }}">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                        <path d="M15 21v-5a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v5" />
                        <path d="M17.774 10.31a1.12 1.12 0 0 0-1.549 0 2.5 2.5 0 0 1-3.451 0 1.12 1.12 0 0 0-1.548 0 2.5 2.5 0 0 1-3.452 0 1.12 1.12 0 0 0-1.549 0 2.5 2.5 0 0 1-3.77-3.248l2.889-4.184A2 2 0 0 1 7 2h10a2 2 0 0 1 1.653.873l2.895 4.192a2.5 2.5 0 0 1-3.774 3.244" />
                        <path d="M4 10.95V19a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8.05" />
                    </svg>
                    <span class="{{ $isPmhPayment ? 'fw-bold' : 'fw-semibold' }}" style="font-size: 0.95rem;">Outstanding Payment</span>
                </a>

                {{-- 4. Notifikasi --}}
                <a href="{{ route('pmh.notifications') }}"
                   class="sidebar-link d-flex align-items-center justify-content-between {{ $isNotifActive ? 'active' : '' }}"
                   style="border-radius: 9999px; padding: 0.65rem 1.15rem; font-size: 0.92rem; color: #FFFFFF; margin-top: 0.35rem; {{ $isNotifActive ? 'background: #0063D7 !important;' : '' }}">
                    <span class="d-flex align-items-center gap-3">
                        <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                            <path d="M10.268 21a2 2 0 0 0 3.464 0" />
                            <path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326" />
                        </svg>
                        <span class="{{ $isNotifActive ? 'fw-bold' : 'fw-semibold' }}" style="font-size: 0.95rem;">Notifikasi</span>
                    </span>
                    @if ($sidebarNotifCount > 0)
                        <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">{{ $sidebarNotifCount }}</span>
                    @endif
                </a>

                {{-- 5. Pengaturan --}}
                <a href="{{ route('profile') }}"
                   class="sidebar-link d-flex align-items-center gap-3 {{ $isProfileActive ? 'active' : '' }}"
                   style="border-radius: 9999px; padding: 0.65rem 1.15rem; font-size: 0.92rem; color: #FFFFFF; margin-top: 0.35rem; {{ $isProfileActive ? 'background: #0063D7 !important;' : '' }}">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                        <path d="M9.671 4.136a2.34 2.34 0 0 1 4.659 0 2.34 2.34 0 0 0 3.319 1.915 2.34 2.34 0 0 1 2.33 4.033 2.34 2.34 0 0 0 0 3.831 2.34 2.34 0 0 1-2.33 4.033 2.34 2.34 0 0 0-3.319 1.915 2.34 2.34 0 0 1-4.659 0 2.34 2.34 0 0 0-3.32-1.915 2.34 2.34 0 0 1-2.33-4.033 2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    <span class="{{ $isProfileActive ? 'fw-bold' : 'fw-semibold' }}" style="font-size: 0.95rem;">Pengaturan</span>
                </a>

            @else
                {{-- ══ STANDARD ROLE SIDEBAR (CLIENT, PM, ADMIN) ══ --}}
                {{-- 1. Dashboard --}}
                @php
                    $isDashboardActive = ($currentRouteName === 'change-requests.index' && (!request()->filled('view') || request('view') === 'dashboard') && !request()->filled('search') && !request()->filled('status'));
                    $isCrShow = request()->routeIs('change-requests.show');
                    $isDraftActive = request('view') === 'draft';
                    $isCreateActive = $currentRouteName === 'change-requests.create';
                    $isTotalCrActive = request('view') === 'total';
                    $isSemuaCrActive = !$isDraftActive && !$isCreateActive && ($isCrShow || $isTotalCrActive || ($currentRouteName === 'change-requests.index' && !$isDashboardActive && !request()->filled('status')));
                    $isCrGroupActive = $isCrShow || (request()->routeIs('change-requests.*') && !$isDashboardActive);
                    $isUserPmh = (auth()->check() && auth()->user()->isPmh()) || $currentRole === 'pmh' || request('role') === 'pmh';
                    $isUserPresales = (auth()->check() && auth()->user()->isPresales()) || $currentRole === 'presales' || request('role') === 'presales';
                    $canSubmitCr = !$isUserPmh && !$isUserPresales;
                @endphp
                <a href="{{ route('change-requests.index') }}"
                   class="sidebar-link {{ $isDashboardActive ? 'active' : '' }}"
                   style="position: relative; border-radius: 9999px; padding: 0.65rem 1rem; font-size: 0.88rem; {{ $isDashboardActive ? 'background: #0063D7 !important; color: #FFFFFF !important;' : '' }}">
                    <i class="bi bi-grid-fill nav-icon" style="font-size: 1.05rem;"></i>
                    <span class="fw-semibold" data-i18n="nav_dashboard">Dashboard</span>
                </a>

                {{-- 2. Change Request (Accordion Group) --}}
                <div class="nav-group-toggle open"
                     id="crGroupToggle"
                     role="button"
                     onclick="this.classList.toggle('open'); document.getElementById('crGroupBody').classList.toggle('open'); const c = this.querySelector('.toggle-chevron'); if(c){ c.classList.toggle('bi-chevron-up'); c.classList.toggle('bi-chevron-down'); }"
                     aria-expanded="true"
                     style="border-radius: 9999px; padding: 0.65rem 1.15rem; font-size: 0.92rem; display: flex; align-items: center; gap: 0.75rem; color: #FFFFFF; margin-top: 0.35rem; {{ $isCrGroupActive ? 'background: #0063D7 !important;' : 'background: transparent;' }}">
                    <i class="bi bi-file-earmark nav-icon" style="font-size: 1.05rem; opacity: 1;"></i>
                    <span class="fw-bold" data-i18n="nav_cr" style="letter-spacing: -0.01em;">Change Request</span>
                    <i class="bi bi-chevron-up toggle-chevron ms-auto" style="font-size: 0.8rem; font-weight: 700; opacity: 0.95; transform: none !important;"></i>
                </div>

                <div class="nav-group-body sidebar-sub open" id="crGroupBody" style="padding-left: 0.45rem; display: flex; flex-direction: column; gap: 0.35rem; margin-top: 0.35rem;">
                    {{-- Semua CR --}}
                    <a href="{{ route('change-requests.index', ['view' => 'total']) }}"
                       class="sidebar-link {{ $isSemuaCrActive ? 'active' : '' }}"
                       style="border-radius: 9999px; padding: 0.55rem 1rem; font-size: 0.88rem; color: #FFFFFF; display: flex; align-items: center; gap: 0.65rem; background: transparent !important;">
                        @if($isSemuaCrActive)
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #00B4FF; display: inline-block; flex-shrink: 0; box-shadow: 0 0 10px #00B4FF;"></span>
                        @endif
                        <i class="bi bi-files nav-icon" style="font-size: 0.95rem; color: #FFFFFF; opacity: 1;"></i>
                        <span data-i18n="nav_all_cr" style="color: #FFFFFF; font-weight: {{ $isSemuaCrActive ? '700' : '500' }}; letter-spacing: -0.01em;">Semua CR</span>
                    </a>

                    {{-- Ajukan CR --}}
                    @if($canSubmitCr)
                    <a href="{{ route('change-requests.create') }}"
                       class="sidebar-link {{ $isCreateActive ? 'active' : '' }}"
                       style="border-radius: 9999px; padding: 0.55rem 1rem; font-size: 0.88rem; color: #FFFFFF; display: flex; align-items: center; gap: 0.65rem; background: transparent !important;">
                        @if($isCreateActive)
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #00B4FF; display: inline-block; flex-shrink: 0; box-shadow: 0 0 10px #00B4FF;"></span>
                        @endif
                        <i class="bi bi-file-earmark-diff nav-icon" style="font-size: 0.95rem; color: #FFFFFF; opacity: 1;"></i>
                        <span data-i18n="nav_submit_cr" style="color: #FFFFFF; font-weight: {{ $isCreateActive ? '700' : '500' }};">Ajukan CR</span>
                    </a>
                    @endif

                    {{-- Draft CR --}}
                    <a href="{{ route('change-requests.index', ['view' => 'draft']) }}"
                       class="sidebar-link {{ $isDraftActive ? 'active' : '' }}"
                       style="border-radius: 9999px; padding: 0.55rem 1rem; font-size: 0.88rem; color: #FFFFFF; display: flex; align-items: center; gap: 0.65rem; background: transparent !important;">
                        @if($isDraftActive)
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #00B4FF; display: inline-block; flex-shrink: 0; box-shadow: 0 0 10px #00B4FF;"></span>
                        @endif
                        <i class="bi bi-file-earmark-bar-graph nav-icon" style="font-size: 0.95rem; color: #FFFFFF; opacity: 1;"></i>
                        <span data-i18n="nav_draft_cr" style="color: #FFFFFF; font-weight: {{ $isDraftActive ? '700' : '500' }};">Draft CR</span>
                    </a>

                    @if(auth()->check() && (auth()->user()->isPm() || auth()->user()->isAdmin()))
                    {{-- Dev Board PM --}}
                    @php $isDevboardActive = request()->routeIs('change-requests.devboard'); @endphp
                    <a href="{{ route('change-requests.devboard') }}"
                       class="sidebar-link {{ $isDevboardActive ? 'active' : '' }}"
                       style="border-radius: 9999px; padding: 0.55rem 1rem; font-size: 0.88rem; color: #FFFFFF; display: flex; align-items: center; gap: 0.65rem; background: transparent !important;">
                        @if($isDevboardActive)
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #00B4FF; display: inline-block; flex-shrink: 0; box-shadow: 0 0 10px #00B4FF;"></span>
                        @endif
                        <i class="bi bi-kanban nav-icon" style="font-size: 0.95rem; color: #FFFFFF; opacity: 1;"></i>
                        <span style="color: #FFFFFF; font-weight: {{ $isDevboardActive ? '700' : '500' }};">Dev Board</span>
                    </a>
                    @endif
                </div>

                {{-- 3. Notifikasi --}}
                @php
                    $stdNotifCount = auth()->check() ? \App\Models\Notification::forUser(auth()->user())->unread()->count() : 0;
                @endphp
                <a href="{{ route('notifications.index') }}"
                   class="sidebar-link d-flex align-items-center justify-content-between"
                   style="border-radius: 9999px; padding: 0.65rem 1rem; font-size: 0.88rem; color: rgba(255,255,255,0.85); margin-top: 0.35rem;">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-bell nav-icon" style="font-size: 1.05rem;"></i>
                        <span class="fw-medium" data-i18n="nav_notifications">Notifikasi</span>
                    </span>
                    @if ($stdNotifCount > 0)
                        <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">{{ $stdNotifCount }}</span>
                    @endif
                </a>

                {{-- 4. Pengaturan --}}
                @if ($currentRole === 'admin')
                    <div class="nav-group-toggle {{ str_starts_with($currentRouteName, 'master.') ? 'open' : '' }}"
                         id="settingsGroupToggle"
                         role="button"
                         onclick="this.classList.toggle('open'); document.getElementById('settingsGroupBody').classList.toggle('open');"
                         aria-expanded="{{ str_starts_with($currentRouteName, 'master.') ? 'true' : 'false' }}"
                         style="border-radius: 9999px; padding: 0.65rem 1rem; font-size: 0.88rem; color: rgba(255,255,255,0.85); margin-top: 0.35rem;">
                        <i class="bi bi-gear nav-icon" style="font-size: 1.05rem;"></i>
                        <span class="fw-medium" data-i18n="nav_settings">Pengaturan</span>
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
                        <a href="{{ route('profile') }}"
                           class="sidebar-link {{ request()->routeIs('profile') ? 'active' : '' }}"
                           style="border-radius: 8px; padding: 0.45rem 0.75rem; font-size: 0.84rem;">
                            <i class="bi bi-person-gear nav-icon"></i>
                            <span>Profil &amp; Akun</span>
                        </a>
                    </div>
                @else
                    <a href="{{ route('profile') }}"
                       class="sidebar-link"
                       style="border-radius: 9999px; padding: 0.65rem 1rem; font-size: 0.88rem; color: rgba(255,255,255,0.85); margin-top: 0.35rem;">
                        <i class="bi bi-gear nav-icon" style="font-size: 1.05rem;"></i>
                        <span class="fw-medium" data-i18n="nav_settings">Pengaturan</span>
                    </a>
                @endif
            @endif

        </nav>

    </aside>

    {{-- Sidebar overlay (mobile) --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    @endauth

    {{-- ══════════════════════════════════════
         MAIN WRAPPER
    ══════════════════════════════════════ --}}
    <div class="main-wrapper" id="mainWrapper">

        {{-- ── Topbar (Figma Exact Header) ── --}}
        @auth
        <header class="main-topbar" style="background: #F0F3F6; border-bottom: 1px solid #E2E8F0; height: 72px; padding: 0 2rem; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 1020;">
            {{-- Mobile hamburger --}}
            <button class="topbar-hamburger me-2 d-lg-none" id="sidebarToggleBtn" type="button" aria-label="Buka/Tutup Sidebar">
                <i class="bi bi-list"></i>
            </button>

            <div style="flex: 1;"></div>

            {{-- Right Actions: Language + Inbox + Notification Bell + Divider + User Profile --}}
            <div class="d-flex align-items-center gap-3">
                
                {{-- Language Selector Pill (ID with Globe) --}}
                <div class="dropdown">
                    <button class="d-flex align-items-center gap-2 px-3 py-1 rounded-pill border-0 bg-transparent"
                            id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            style="border: 1.5px solid #E2E8F0 !important; font-size: 0.8rem; font-weight: 700; color: #475569; cursor: pointer; height: 34px;">
                        <i class="bi bi-globe2" style="font-size: 0.95rem; color: #64748B;"></i>
                        <span id="currentLangLabel">ID</span>
                        <i class="bi bi-chevron-down ms-0.5" style="font-size: 0.72rem; color: #64748B;"></i>
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

                @php
                    $unreadNotifs = auth()->check() ? \App\Models\Notification::forUser(auth()->user())->latest()->take(4)->get() : collect();
                    $unreadNotifsCount = auth()->check() ? \App\Models\Notification::forUser(auth()->user())->where('is_read', false)->count() : 0;
                @endphp

                {{-- Inbox Button with Dynamic Notification Dot --}}
                <div class="dropdown">
                    <button class="btn p-0 position-relative d-flex align-items-center justify-content-center border-0 bg-transparent shadow-none"
                            type="button" id="inboxDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            style="width: 36px; height: 36px; color: #1E293B; font-size: 1.25rem;">
                        <i class="bi bi-inbox"></i>
                        @if($unreadNotifsCount > 0)
                            <span style="position: absolute; top: 5px; right: 4px; width: 8px; height: 8px; border-radius: 50%; background: #EF4444; border: 1.5px solid #FFFFFF;"></span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-3 mt-2" style="width: 320px; border-radius: 14px;" aria-labelledby="inboxDropdown">
                        <li class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="fw-bold small text-dark" data-i18n="inbox_title">Pesan Masuk (Inbox)</span>
                            @if($unreadNotifsCount > 0)
                                <span class="badge bg-primary-subtle text-primary rounded-pill" style="font-size: 0.68rem;">{{ $unreadNotifsCount }} Baru</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.68rem;">Kosong</span>
                            @endif
                        </li>
                        @forelse($unreadNotifs as $notif)
                            <li class="mb-2">
                                <a href="{{ $notif->action_url ?: route('notifications.go', $notif->id) }}" class="text-decoration-none">
                                    <div class="p-2 rounded-2" style="background: #F8FAFC; border-left: 3px solid #0063D7;">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.76rem;">{{ $notif->title }}</div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ $notif->message }}</div>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="text-center py-3 text-muted small" style="font-size: 0.78rem;">
                                <i class="bi bi-inbox d-block fs-4 mb-1 text-secondary opacity-50"></i>
                                Tidak ada pesan masuk saat ini
                            </li>
                        @endforelse
                    </ul>
                </div>
                {{-- Notification Bell with Red Notification Dot --}}
                <div class="dropdown">
                    <button class="btn p-0 position-relative d-flex align-items-center justify-content-center border-0 bg-transparent shadow-none" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            style="width: 36px; height: 36px; color: #1E293B; font-size: 1.25rem;">
                        <i class="bi bi-bell"></i>
                        @if($unreadNotifsCount > 0)
                        <span style="position: absolute; top: 5px; right: 4px; width: 8px; height: 8px; border-radius: 50%; background: #EF4444; border: 1.5px solid #FFFFFF;"></span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-3 mt-2" style="width: 320px; border-radius: 14px;" aria-labelledby="notifDropdown">
                        <li class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small text-dark" data-i18n="notif_incoming">Notifikasi Masuk</span>
                            <span class="badge bg-danger-subtle text-danger rounded-pill" style="font-size: 0.68rem;">{{ $unreadNotifsCount }} Baru</span>
                        </li>
                        @forelse($unreadNotifs as $notif)
                        <li class="mb-2">
                            <a href="{{ $notif->action_url ?: route('notifications.go', $notif->id) }}" class="text-decoration-none">
                                <div class="p-2 rounded-2" style="background: {{ $notif->type === 'danger' ? '#FEF2F2' : ($notif->type === 'warning' ? '#FFFBEB' : '#EFF6FF') }}; border-left: 3px solid {{ $notif->type === 'danger' ? '#EF4444' : ($notif->type === 'warning' ? '#F59E0B' : '#3B82F6') }};">
                                    <div class="fw-bold" style="font-size: 0.76rem; color: #0F172A;">{{ $notif->title }}</div>
                                    <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ $notif->message }}</div>
                                </div>
                            </a>
                        </li>
                        @empty
                        <li class="text-center py-2 text-muted small">Tidak ada notifikasi baru</li>
                        @endforelse
                        <li class="border-top pt-2 mt-2 text-center">
                            <a href="{{ route('notifications.index') }}" class="small text-decoration-none fw-bold text-primary">Lihat Semua Notifikasi &rarr;</a>
                        </li>
                    </ul>
                </div>

                {{-- Vertical Divider between icons and profile --}}
                <div style="width: 1px; height: 26px; background-color: #CBD5E1; margin: 0 4px;"></div>

                {{-- User Profile (Totok Antok with warm beige TA Avatar & Dropdown matching Figma Image 2) --}}
                <div class="dropdown">
                    <div class="d-flex align-items-center gap-3 ps-1 cursor-pointer" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                        @php
                            $currentUserName = auth()->check() ? auth()->user()->name : '-';
                            $curWords = preg_split('/\s+/', trim($currentUserName));
                            $curInitials = count($curWords) >= 2
                                ? strtoupper(substr($curWords[0], 0, 1) . substr($curWords[1], 0, 1))
                                : strtoupper(substr($currentUserName, 0, 2));
                        @endphp
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm"
                             style="width: 42px; height: 42px; background: #D5CCBE; color: #292524; font-size: 0.95rem; font-weight: 700; flex-shrink: 0;">
                            {{ $curInitials ?: 'US' }}
                        </div>
                        <div style="line-height: 1.25;" class="d-none d-sm-block text-start">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.92rem; font-weight: 700; letter-spacing: -0.01em; color: #0F172A;">
                                {{ $currentUserName }}
                            </div>
                            <div class="text-muted text-truncate" style="font-size: 0.72rem; color: #64748B; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;">
                                {{ auth()->check() ? (auth()->user()->isPresales() ? 'PRESALES' : (auth()->user()->isPmh() ? 'PM HEAD' : (auth()->user()->isPm() ? 'PM' : (auth()->user()->isClient() ? 'KLIEN' : strtoupper(auth()->user()->role))))) : '-' }}
                            </div>
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 p-2" style="border-radius: 12px; min-width: 200px;" aria-labelledby="userMenuDropdown">
                        <li class="px-3 py-2 border-bottom mb-1">
                            <div class="fw-bold small text-dark">{{ auth()->check() ? auth()->user()->name : '-' }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">{{ auth()->check() ? (auth()->user()->email ?? '-') : '-' }}</div>
                        </li>
                        <li>
                            <a href="{{ route('profile') }}" class="dropdown-item py-2 px-3 small d-flex align-items-center gap-2 fw-medium text-dark">
                                <i class="bi bi-person text-secondary"></i> Profil Saya
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 px-3 small text-danger d-flex align-items-center gap-2 fw-semibold">
                                    <i class="bi bi-box-arrow-right fs-6 text-danger"></i> Logout
                                </button>
                            </form>
                        </li>
                        <li>
                            <a href="{{ route('login') }}" class="dropdown-item py-2 px-3 small d-flex align-items-center gap-2 fw-medium text-secondary">
                                <i class="bi bi-person-badge text-secondary"></i> Ganti Role
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        @endauth

        {{-- ── Flash Messages (Animated Premium Notification Banner) ── --}}
        @php
            $flashType = null;
            $flashMsg = null;
            $flashTitle = null;

            if (session('success')) {
                $flashType = 'success';
                $flashMsg = session('success');
                $flashTitle = (session('cr_action_type') === 'approve' || str_contains(strtolower($flashMsg), 'disetujui') || str_contains(strtolower($flashMsg), 'divalidasi'))
                    ? 'Persetujuan Berhasil Divalidasi!'
                    : 'Aksi Berhasil Disimpan!';
            } elseif (session('warning')) {
                $flashType = 'warning';
                $flashMsg = session('warning');
                $flashTitle = (session('cr_action_type') === 'reject' || str_contains(strtolower($flashMsg), 'ditolak') || str_contains(strtolower($flashMsg), 'revisi'))
                    ? 'Change Request Dikembalikan untuk Revisi'
                    : 'Perhatian!';
            } elseif (session('error')) {
                $flashType = 'danger';
                $flashMsg = session('error');
                $flashTitle = 'Terjadi Kesalahan!';
            } elseif (session('info') || session('status')) {
                $flashType = 'info';
                $flashMsg = session('info') ?: session('status');
                $flashTitle = 'Informasi Sistem';
            }
        @endphp

        @if ($flashType && $flashMsg)
            <div id="appFlashNotification" class="px-3 px-md-4 pt-3 d-print-none" style="animation: slideDownAlert 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;">
                <div class="position-relative overflow-hidden p-3 p-md-3.5 rounded-4 shadow-sm border d-flex align-items-center justify-content-between gap-3"
                     style="
                        @if($flashType === 'success')
                            background: linear-gradient(135deg, #ECFDF5 0%, #F0FDF4 100%);
                            border-color: #86EFAC !important;
                            color: #065F46;
                        @elseif($flashType === 'warning')
                            background: linear-gradient(135deg, #FFFBEB 0%, #FEF2F2 100%);
                            border-color: #FCD34D !important;
                            color: #92400E;
                        @elseif($flashType === 'danger')
                            background: linear-gradient(135deg, #FEF2F2 0%, #FFF1F2 100%);
                            border-color: #FCA5A5 !important;
                            color: #991B1B;
                        @else
                            background: linear-gradient(135deg, #EFF6FF 0%, #F0F9FF 100%);
                            border-color: #93C5FD !important;
                            color: #1E40AF;
                        @endif
                     ">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0"
                             style="width: 44px; height: 44px;
                                @if($flashType === 'success') background: #10B981; color: white;
                                @elseif($flashType === 'warning') background: #F59E0B; color: white;
                                @elseif($flashType === 'danger') background: #EF4444; color: white;
                                @else background: #3B82F6; color: white;
                                @endif
                             ">
                            @if($flashType === 'success')
                                <i class="bi bi-check2-circle fs-4"></i>
                            @elseif($flashType === 'warning')
                                <i class="bi bi-arrow-counterclockwise fs-4"></i>
                            @elseif($flashType === 'danger')
                                <i class="bi bi-exclamation-octagon-fill fs-4"></i>
                            @else
                                <i class="bi bi-info-circle-fill fs-4"></i>
                            @endif
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h6 class="fw-bold mb-0" style="font-size: 0.92rem; font-weight: 800; letter-spacing: -0.01em;">
                                    {{ $flashTitle }}
                                </h6>
                                @if(session('cr_action_type') === 'approve' || str_contains(strtolower($flashMsg), 'disetujui'))
                                    <span class="badge bg-success text-white px-2 py-0.5" style="font-size: 0.70rem; font-weight: 700;">
                                        <i class="bi bi-shield-check me-0.5"></i> Validasi Disetujui
                                    </span>
                                @elseif(session('cr_action_type') === 'reject' || str_contains(strtolower($flashMsg), 'ditolak'))
                                    <span class="badge bg-danger text-white px-2 py-0.5" style="font-size: 0.70rem; font-weight: 700;">
                                        <i class="bi bi-arrow-return-left me-0.5"></i> Revision Needed
                                    </span>
                                @endif
                            </div>
                            <p class="mb-0 small" style="font-size: 0.83rem; opacity: 0.95; line-height: 1.45;">
                                {{ $flashMsg }}
                            </p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <button type="button" class="btn btn-sm btn-link text-muted p-1" onclick="dismissFlashNotification()" title="Tutup Notifikasi" style="font-size: 1.1rem; text-decoration: none;">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    {{-- Countdown Timer Line --}}
                    <div id="flashProgressBar" class="position-absolute bottom-0 start-0"
                         style="height: 3px !important; width: 100%; top: auto;
                            @if($flashType === 'success') background: #10B981;
                            @elseif($flashType === 'warning') background: #F59E0B;
                            @elseif($flashType === 'danger') background: #EF4444;
                            @else background: #3B82F6;
                            @endif
                            transition: width 6s linear;
                         "></div>
                </div>
            </div>
        @endif

        {{-- ── Main Content ── --}}
        <main class="main-content" id="mainContent">
            @yield('content')
        </main>

    </div>{{-- /.main-wrapper --}}

</div>{{-- /.app-shell --}}

{{-- Global Toast --}}
<div class="toast-container-fixed">
    <div id="appToast" class="toast align-items-center text-bg-dark border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill text-success" id="toastIcon"></i>
                <span id="toastMessage">Tersalin ke clipboard!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    /* ── Sidebar Toggle (Mobile) ── */
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const appSidebar       = document.getElementById('appSidebar');
    const sidebarOverlay   = document.getElementById('sidebarOverlay');

    function openSidebar() {
        appSidebar?.classList.add('sidebar-open');
        sidebarOverlay?.classList.add('visible');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        appSidebar?.classList.remove('sidebar-open');
        sidebarOverlay?.classList.remove('visible');
        document.body.style.overflow = '';
    }

    sidebarToggleBtn?.addEventListener('click', () => {
        appSidebar?.classList.contains('sidebar-open') ? closeSidebar() : openSidebar();
    });

    sidebarOverlay?.addEventListener('click', closeSidebar);

    /* ── Master Data Collapsible ── */
    const masterToggle = document.getElementById('masterDataToggle');
    const masterBody   = document.getElementById('masterDataBody');

    masterToggle?.addEventListener('click', () => {
        const isOpen = masterBody.classList.toggle('open');
        masterToggle.classList.toggle('open', isOpen);
        masterToggle.setAttribute('aria-expanded', String(isOpen));
    });

    /* ── Dark Mode ── */
    const themeToggleBtn   = document.getElementById('themeToggleBtn');
    const themeToggleIcon  = document.getElementById('themeToggleIcon');
    const themeToggleLabel = document.getElementById('themeToggleLabel');

    function applyTheme(isDark) {
        if (isDark) {
            document.body.classList.add('dark-theme');
            if (themeToggleIcon)  themeToggleIcon.className  = 'bi bi-sun-fill text-warning';
            if (themeToggleLabel) themeToggleLabel.textContent = 'Terang';
        } else {
            document.body.classList.remove('dark-theme');
            if (themeToggleIcon)  themeToggleIcon.className  = 'bi bi-moon-stars';
            if (themeToggleLabel) themeToggleLabel.textContent = 'Gelap';
        }
    }

    const savedTheme = localStorage.getItem('itpi_theme');
    if (savedTheme === 'dark') applyTheme(true);

    themeToggleBtn?.addEventListener('click', () => {
        const isDark = document.body.classList.toggle('dark-theme');
        localStorage.setItem('itpi_theme', isDark ? 'dark' : 'light');
        applyTheme(isDark);
    });

    /* ── Global Toast Helper ── */
    window.showAppToast = function (message, isSuccess = true) {
        const toastEl = document.getElementById('appToast');
        const msgEl   = document.getElementById('toastMessage');
        const iconEl  = document.getElementById('toastIcon');

        if (toastEl && msgEl && iconEl) {
            msgEl.innerText = message;
            iconEl.className = isSuccess
                ? 'bi bi-check-circle-fill text-success'
                : 'bi bi-info-circle-fill text-info';
            new bootstrap.Toast(toastEl, { delay: 2800 }).show();
        }
    };

    /* ── Copy CR Code ── */
    window.copyCrCode = function (text, event) {
        if (event) event.stopPropagation();
        navigator.clipboard.writeText(text).then(() => {
            window.showAppToast('Kode ' + text + ' tersalin ke clipboard!');
        }).catch(() => {
            window.showAppToast('Gagal menyalin kode.', false);
        });
    };

    /* ── Export Download Toast ── */
    document.addEventListener('click', function (e) {
        if (e.target.closest('a[href*="export"]')) {
            window.showAppToast('Menyiapkan & mengunduh berkas laporan...', true);
        }
    });

    /* ── Multi-Language Translation Engine (ID / EN) ── */
    const appTranslations = {
        id: {
            // Sidebar & Navigation
            'menu_header': 'MENU',
            'nav_dashboard': 'Dashboard',
            'nav_cr': 'Change Request',
            'nav_all_cr': 'Semua CR',
            'nav_submit_cr': 'Ajukan CR',
            'nav_draft_cr': 'Draft CR',
            'nav_notifications': 'Notifikasi',
            'nav_settings': 'Pengaturan',
            'master_clients': 'Master Client',
            'master_pegawai': 'Master Pegawai',
            'master_status': 'Master Status',
            'search_placeholder': 'Search task or CR number...',
            'lang_id_label': '🇮🇩 ID (Indonesia)',
            'lang_en_label': '🇬🇧 EN (English)',
            'inbox_title': 'Pesan Masuk (Inbox)',
            'inbox_new_badge': '2 Baru',
            'notif_badge': '3 Baru',
            'notif_incoming': 'Notifikasi Masuk',

            // Dashboard Header & Cards
            'dashboard_title': 'DASHBOARD',
            'dashboard_subtitle': 'Pantau seluruh permintaan perubahan Anda',
            'btn_new_cr': 'Ajukan CR Baru',
            'card_all_cr': 'Semua CR',
            'card_development': 'Development',
            'card_uat': 'UAT',
            'card_golive': 'GO LIVE',
            'card_need_approval': 'Butuh Persetujuan',
            'card_active_month': 'Aktif Bulan Ini',

            // Status Card & Pipeline
            'status_title': 'STATUS',
            'status_subtitle': 'Monitoring pergerakan CR melalui 6 tahapan siklus proyek TI',
            'stage_analisa': 'Analisa',
            'stage_develop': 'Develop',
            'stage_sit': 'SIT',
            'stage_uat': 'UAT',
            'stage_training': 'Training',
            'stage_golive': 'Go Live',

            // Notification Card
            'notif_title': 'NOTIFIKASI',
            'notif_view_all': 'Lihat Semua',
            'notif_subtitle': 'Pemberitahuan persetujuan penting, batas waktu tinjauan dokumen, dan tindak lanjut status tahapan proyek.',
            'notif_alert1_title': '3 Perubahan Ruang Lingkup CR menunggu persetujuan > 14 hari',
            'notif_alert1_desc': 'Status : Menunggu Persetujuan PM',
            'notif_alert2_title': '8 CR Baru Menunggu Verifikasi Kebutuhan',
            'notif_alert2_desc': 'Tahapan : 1. Analisis Kebutuhan',
            'notif_alert3_title': '5 Masukan Pengguna UAT butuh tindak lanjut',
            'notif_alert3_desc': 'Tahapan : 4. UAT & Pengujian',

            // Subpages & Tables
            'th_submission_date': 'TGL PENGAJUAN',
            'th_requester': 'PEMOHON',
            'th_cr_title': 'JUDUL CR',
            'th_status': 'STATUS',
            'th_priority': 'PRIORITAS',
            'th_action': 'AKSI',
            'action_detail': 'Detail',
            'btn_download_doc': 'Unduh Dokumen',
            'priority_urgent': 'Urgent',
            'priority_normal': 'Normal',
            'priority_low': 'Rendah',
            'priority_high': 'Tinggi',
            'status_submitted': 'Diajukan',
            'status_analysis': 'Analisis',
            'status_develop': 'Develop',
            'status_uat': 'UAT',
            'status_golive': 'Go-Live',
            'empty_cr_title': 'Belum Ada Change Request di Kategori Ini',
            'empty_cr_desc': 'Tidak ada data Change Request yang sesuai dengan kategori yang dipilih.',
            'back_to_cr': 'Kembali ke Daftar CR',

            // Client Profile
            'client_profile_badge': 'Profil Instansi Klien',
            'client_profile_title': 'Data Profil & Master Klien',
            'client_profile_subtitle': 'Informasi identitas instansi rekanan serta data 3 Person in Charge (PIC) penanggung jawab.',
            'initial_label': 'Inisial / Kode:',
            'status_partner': 'Status Kerjasama:',
            'status_active': 'Aktif',
            'verified_account': 'Akun Terverifikasi',
            'pic_main_name': 'NAMA KLIEN / PIC UTAMA',
            'email_contact': 'EMAIL TERDAFTAR',
            'phone_contact': 'NOMOR TELEPON / WA',
            'office_address': 'ALAMAT KANTOR OPERASIONAL',
            'three_pic_title': 'Daftar 3 PIC Resmi Master Client',
            'three_pic_desc': 'PIC penanggung jawab komunikasi lintas divisi untuk administrasi dan eskalasi pengerjaan Change Request.',
            'pic_marketing_title': 'PIC Marketing',
            'pic_it_title': 'PIC IT / Technical',
            'pic_proc_title': 'PIC Procurement',

            // Detail CR Page
            'detail_cr_title': 'Detail Change Request',
            'detail_cr_subtitle': 'Melihat Data Lengkap CR',
            'progress_work': 'PROGRESS PENGERJAAN',
            'stage_1_analisa': 'Analisa',
            'stage_2_develop': 'Develop',
            'stage_3_sit': 'SIT',
            'stage_4_uat': 'UAT',
            'stage_5_training': 'Training',
            'stage_6_golive': 'Go Live',
            'section_info_cr': 'INFORMASI CR',
            'label_company': 'NAMA PERUSAHAAN',
            'label_client_initial': 'INISIAL KLIEN',
            'label_pic_name': 'NAMA PIC',
            'label_project_name': 'NAMA PROJECT',
            'label_cr_owner': 'CR OWNER',
            'label_submission_date': 'TANGGAL PENGAJUAN',
            'label_request_golive': 'REQUEST GO-LIVE',
            'label_cr_priority': 'PRIORITAS CR',
            'section_documents': 'DOKUMEN',
            'label_doc_cr': 'DOKUMEN CR',
            'label_solution_paper': 'SOLUTION PAPER',
            'section_detail_desc': 'Detail Deskripsi CR',
            'section_mandays': 'ESTIMASI MANDAYS',
            'mandays_analisis': 'Analisis',
            'mandays_development': 'Development',
            'mandays_testing': 'Testing',
            'mandays_total': 'Total',
            'unit_hari': 'hari',
            'unit_hari_kerja': 'hari kerja',
            'section_cr_notes': 'CR Notes / Catatan',
            'callout_dependency_title': 'Catatan Dependensi:',
            'callout_dependency_body': 'Membutuhkan integrasi API Gateway credentials (Client ID & Secret Key) production dari pihak Payment Aggregator sebelum tanggal 18 Sep 2026.',
            'h3_background_purpose': '1. LATAR BELAKANG & TUJUAN',
            'h3_scope_of_work': '2. RUANG LINGKUP PERUBAHAN (SCOPE OF WORK)',
            'h3_technical_impact': '3. DAMPAK TEKNIS & DEPENDENCIES',
            'user_role_label': 'Klien',

            // Notifikasi Page
            'notif_page_title': 'Notifikasi',
            'notif_page_subtitle': 'Pemberitahuan pembaruan status, persetujuan, dan aktivitas Change Request Anda.',
            'btn_bersihkan': 'Bersihkan',
            'tab_semua': 'Semua',
            'tab_belum_dibaca': 'Belum Dibaca',
            'tab_all_cr': 'Semua CR',
            'tab_active_cr': 'CR Aktif',
            'tab_completed_cr': 'CR Selesai',
            'all_status': 'Semua Status',
            'placeholder_search_notif': 'Cari notifikasi / kode CR...',
            'breadcrumb_dashboard': 'Dashboard',
            'breadcrumb_notifikasi': 'Notifikasi'
        },
        en: {
            // Sidebar & Navigation
            'menu_header': 'MENU',
            'nav_dashboard': 'Dashboard',
            'nav_cr': 'Change Request',
            'nav_all_cr': 'All CR',
            'nav_submit_cr': 'Submit CR',
            'nav_draft_cr': 'Draft CR',
            'nav_notifications': 'Notifications',
            'nav_settings': 'Settings',
            'master_clients': 'Master Client',
            'master_pegawai': 'Master Staff',
            'master_status': 'Master Status',
            'search_placeholder': 'Search task or CR number...',
            'lang_id_label': '🇮🇩 ID (Indonesian)',
            'lang_en_label': '🇬🇧 EN (English)',
            'inbox_title': 'Inbox Messages',
            'inbox_new_badge': '2 New',
            'notif_badge': '3 New',
            'notif_incoming': 'Incoming Notifications',

            // Notifikasi Page
            'notif_page_title': 'Notifications',
            'notif_page_subtitle': 'Notifications of status updates, approvals, and your Change Request activities.',
            'btn_bersihkan': 'Clear All',
            'tab_semua': 'All',
            'tab_belum_dibaca': 'Unread',
            'tab_all_cr': 'All CR',
            'tab_active_cr': 'Active CR',
            'tab_completed_cr': 'Completed CR',
            'all_status': 'All Status',
            'placeholder_search_notif': 'Search notification / CR code...',
            'breadcrumb_dashboard': 'Dashboard',
            'breadcrumb_notifikasi': 'Notifications',

            // Dashboard Header & Cards
            'dashboard_title': 'DASHBOARD',
            'dashboard_subtitle': 'Monitor all your change requests',
            'btn_new_cr': 'Submit New CR',
            'card_all_cr': 'All CR',
            'card_development': 'Development',
            'card_uat': 'UAT',
            'card_golive': 'GO LIVE',
            'card_need_approval': 'Needs Approval',
            'card_active_month': 'Active This Month',

            // Status Card & Pipeline
            'status_title': 'STATUS',
            'status_subtitle': 'Monitoring CR progression through 6 IT project lifecycle stages',
            'stage_analisa': 'Analysis',
            'stage_develop': 'Develop',
            'stage_sit': 'SIT',
            'stage_uat': 'UAT',
            'stage_training': 'Training',
            'stage_golive': 'Go Live',

            // Notification Card
            'notif_title': 'NOTIFICATIONS',
            'notif_view_all': 'View All',
            'notif_subtitle': 'Important approval notices, document review deadlines, and project stage follow-ups.',
            'notif_alert1_title': '3 CR Scope Changes awaiting approval > 14 days',
            'notif_alert1_desc': 'Status : Awaiting PM Approval',
            'notif_alert2_title': '8 New CRs Awaiting Requirements Verification',
            'notif_alert2_desc': 'Stage : 1. Requirements Analysis',
            'notif_alert3_title': '5 UAT User Feedbacks need follow-up',
            'notif_alert3_desc': 'Stage : 4. UAT & Testing',

            // Subpages & Tables
            'th_submission_date': 'SUBMISSION DATE',
            'th_requester': 'REQUESTER',
            'th_cr_title': 'CR TITLE',
            'th_status': 'STATUS',
            'th_priority': 'PRIORITY',
            'th_action': 'ACTION',
            'action_detail': 'Details',
            'btn_download_doc': 'Download Document',
            'priority_urgent': 'Urgent',
            'priority_normal': 'Normal',
            'priority_low': 'Low',
            'priority_high': 'High',
            'status_submitted': 'Submitted',
            'status_analysis': 'Analysis',
            'status_develop': 'Develop',
            'status_uat': 'UAT',
            'status_golive': 'Go-Live',
            'empty_cr_title': 'No Change Requests in This Category',
            'empty_cr_desc': 'There are no Change Requests matching the selected category.',
            'back_to_cr': 'Back to CR List',

            // Client Profile
            'client_profile_badge': 'Client Organization Profile',
            'client_profile_title': 'Client Profile & Master Data',
            'client_profile_subtitle': 'Information regarding partner agency identity and 3 official Persons in Charge (PICs).',
            'initial_label': 'Initials / Code:',
            'status_partner': 'Partnership Status:',
            'status_active': 'Active',
            'verified_account': 'Verified Account',
            'pic_main_name': 'CLIENT NAME / MAIN PIC',
            'email_contact': 'REGISTERED EMAIL',
            'phone_contact': 'PHONE NUMBER / WA',
            'office_address': 'OPERATIONAL OFFICE ADDRESS',
            'three_pic_title': 'List of 3 Official Master Client PICs',
            'three_pic_desc': 'PICs responsible for cross-division communication for administration and Change Request escalation.',
            'pic_marketing_title': 'Marketing PIC',
            'pic_it_title': 'IT / Technical PIC',
            'pic_proc_title': 'Procurement PIC',

            // Detail CR Page
            'detail_cr_title': 'Detail Change Request',
            'detail_cr_subtitle': 'Viewing Complete CR Data',
            'badge_analisa': '• Analysis',
            'status_review': 'Awaiting Review',
            'progress_work': 'WORK PROGRESS',
            'stage_1_analisa': 'Analysis',
            'stage_2_develop': 'Develop',
            'stage_3_sit': 'SIT',
            'stage_4_uat': 'UAT',
            'stage_5_training': 'Training',
            'stage_6_golive': 'Go Live',
            'section_info_cr': 'CR INFORMATION',
            'label_company': 'COMPANY NAME',
            'label_client_initial': 'CLIENT INITIAL',
            'label_pic_name': 'PIC NAME',
            'label_project_name': 'PROJECT NAME',
            'label_cr_owner': 'CR OWNER',
            'label_submission_date': 'SUBMISSION DATE',
            'label_request_golive': 'REQUESTED GO-LIVE',
            'label_cr_priority': 'CR PRIORITY',
            'section_documents': 'DOCUMENTS',
            'label_doc_cr': 'CR DOCUMENT',
            'label_solution_paper': 'SOLUTION PAPER',
            'section_detail_desc': 'CR Detailed Description',
            'section_mandays': 'MANDAYS ESTIMATE',
            'mandays_analisis': 'Analysis',
            'mandays_development': 'Development',
            'mandays_testing': 'Testing',
            'mandays_total': 'Total',
            'unit_hari': 'days',
            'unit_hari_kerja': 'working days',
            'section_cr_notes': 'CR Notes / Remarks',
            'callout_dependency_title': 'Dependency Note:',
            'callout_dependency_body': 'Requires production API Gateway credentials (Client ID & Secret Key) integration from Payment Aggregator before 18 Sep 2026.',
            'h3_background_purpose': '1. BACKGROUND & PURPOSE',
            'h3_scope_of_work': '2. SCOPE OF CHANGE (SCOPE OF WORK)',
            'h3_technical_impact': '3. TECHNICAL IMPACT & DEPENDENCIES',
            'user_role_label': 'Client'
        }
    };

    const phraseReplacements = [
        // Long sentences, notices & descriptions
        ['Monitoring pergerakan CR melalui 6 tahapan siklus proyek TI', 'Monitoring CR progression through 6 IT project lifecycle stages'],
        ['Pemberitahuan persetujuan penting, batas waktu tinjauan dokumen, dan tindak lanjut status tahapan proyek.', 'Important approval notices, document review deadlines, and project stage follow-ups.'],
        ['3 Perubahan Ruang Lingkup CR menunggu persetujuan > 14 hari', '3 CR Scope Changes awaiting approval > 14 days'],
        ['8 CR Baru Menunggu Verifikasi Kebutuhan', '8 New CRs Awaiting Requirements Verification'],
        ['5 Masukan Pengguna UAT butuh tindak lanjut', '5 UAT User Feedbacks need follow-up'],
        ['Pantau seluruh permintaan perubahan Anda', 'Monitor all your change requests'],
        ['Total permintaan CR dari PM: hari ini, kemarin, dan hari-hari sebelumnya', 'Total CR requests from PM: today, yesterday, and previous days'],
        ['Lengkapi detail informasi perubahan yang akan diajukan', 'Complete the details of the proposed change'],
        ['Data identitas pemohon dan perusahaan terkait pengajuan CR', 'Identity data of requester and company regarding CR submission'],
        ['Belum ada catatan pengajuan dari pemohon CR.', 'No submission notes from CR requester.'],
        ['Belum ada detail deskripsi yang diisi untuk CR ini.', 'No detailed description provided for this CR.'],
        ['Belum ada detail deskripsi yang diisi.', 'No detailed description provided.'],
        ['Belum ada solution paper yang diunggah.', 'No solution paper uploaded yet.'],
        ['Belum ada dokumen solusi yang diunggah.', 'No solution paper uploaded yet.'],
        ['Belum ada dokumen yang diunggah.', 'No document uploaded yet.'],
        ['Belum ada dokumen CR terlampir.', 'No CR document attached yet.'],
        ['Belum ada Solution Paper terlampir.', 'No Solution Paper attached yet.'],
        ['Tidak ada data Change Request yang sesuai dengan kategori yang dipilih.', 'There are no Change Requests matching the selected category.'],
        ['Informasi identitas instansi rekanan serta data 3 Person in Charge (PIC) penanggung jawab.', 'Information regarding partner agency identity and 3 official Persons in Charge (PICs).'],
        ['Daftar 3 PIC Resmi Master Client', 'List of 3 Official Master Client PICs'],
        ['PIC penanggung jawab komunikasi lintas divisi untuk administrasi dan eskalasi pengerjaan Change Request.', 'PICs responsible for cross-division communication for administration and Change Request escalation.'],
        ['Pemberitahuan pembaruan status, persetujuan, dan aktivitas Change Request Anda.', 'Notifications of status updates, approvals, and your Change Request activities.'],
        ['Bagian Man-Days terkunci otomatis untuk Presales karena telah ditetapkan dan dihitung oleh Project Manager saat verifikasi teknis.', 'Man-Days section is automatically locked for Presales as it was determined and calculated by the Project Manager during technical verification.'],
        ['Estimasi Man-Days ditentukan langsung oleh Project Manager', 'Man-Days estimate is directly determined by the Project Manager'],
        ['Belum ada Termin Pembayaran (TOP). Klik tombol + Tambah Termin di atas untuk menginput termin penawaran.', 'No Terms of Payment (TOP) yet. Click the + Add Term button above to input quotation terms.'],
        ['Penyelesaian seluruh pengerjaan teknis CR hingga Go-Live', 'Completion of all technical CR work through Go-Live'],
        ['Disertai Berita Acara & Kelengkapan Dokumen', 'Accompanied by Official Report & Complete Documents'],
        ['Quotation berhasil disimpan & disubmit!', 'Quotation successfully saved & submitted!'],
        ['Quotation berhasil disimpan & diajukan!', 'Quotation successfully saved & submitted!'],
        ['Pencatatan invoice baru dapat dilakukan setelah proyek', 'New invoice recording can only be performed after project'],
        ['Data di bawah ini bersifat baca-dan-pantau untuk Presales.', 'Data below is read-and-monitor for Presales.'],

        // Stepper & Workflow Section (CR Show & Presales Review)
        ['PROGRESS PENGERJAAN TEKNIS (STATUS PENGEMBANGAN)', 'TECHNICAL WORK PROGRESS (DEVELOPMENT STATUS)'],
        ['PROGRESS PENGERJAAN TEKNIS (STATUS DEVELOPMENT)', 'TECHNICAL WORK PROGRESS (DEVELOPMENT STATUS)'],
        ['PROGRESS PENGERJAAN TEKNIS (PM)', 'TECHNICAL WORK PROGRESS (PM)'],
        ['Progress Pengerjaan Teknis (PM)', 'Technical Work Progress (PM)'],
        ['PROGRESS PENGERJAAN TEKNIS', 'TECHNICAL WORK PROGRESS'],
        ['Progress Pengerjaan Teknis', 'Technical Work Progress'],
        ['Status Eksekusi Saat Ini:', 'Current Execution Status:'],
        ['Status Eksekusi Saat Ini', 'Current Execution Status'],
        ['STATUS ALUR CR', 'CR WORKFLOW STATUS'],
        ['Status Alur CR', 'CR Workflow Status'],
        ['Total Progres Alur:', 'Total Workflow Progress:'],
        ['Total Progres Alur', 'Total Workflow Progress'],
        ['Catatan Pengerjaan PM:', 'PM Work Notes:'],
        ['Catatan Pengerjaan PM', 'PM Work Notes'],
        ['Status Saat Ini:', 'Current Status:'],
        ['Status Saat Ini', 'Current Status'],

        // Workflow 6 stages
        ['1. Diajukan', '1. Submitted'],
        ['Tinjauan PM', 'PM Review'],
        ['Review PM', 'PM Review'],
        ['Persetujuan PM Head', 'PM Head Approval'],
        ['Approval PM Head', 'PM Head Approval'],
        ['Penawaran', 'Quotation'],
        ['Pengembangan', 'Development'],
        ['Faktur', 'Invoice'],
        ['Sedang Berjalan', 'In Progress'],
        ['Menunggu', 'Pending'],
        ['Selesai', 'Completed'],

        // Execution status labels
        ['Menunggu Tinjauan PM', 'Awaiting PM Review'],
        ['Menunggu Review PM', 'Awaiting PM Review'],
        ['Sedang Dianalisa', 'Being Analyzed'],
        ['Menunggu Persetujuan PM Head', 'Awaiting PM Head Approval'],
        ['Menunggu Approval PM Head', 'Awaiting PM Head Approval'],
        ['Perlu Revisi', 'Revision Needed'],
        ['Revision Needed', 'Revision Needed'],
        ['Disetujui PM Head', 'Approved by PM Head'],
        ['Sedang Dikerjakan', 'In Development'],
        ['Menunggu Validasi Go-Live', 'Awaiting Go-Live Validation'],
        ['Selesai / Penagihan', 'Completed / Invoicing'],
        ['Selesai / Invoicing', 'Completed / Invoicing'],
        ['Awaiting PM Verification', 'Awaiting PM Verification'],
        ['Awaiting PMH Validation', 'Awaiting PMH Validation'],
        ['Tervalidasi', 'Validated'],
        ['Validated', 'Validated'],
        ['Disetujui', 'Approved'],
        ['Approved', 'Approved'],
        ['Ditolak', 'Rejected'],
        ['Rejected', 'Rejected'],
        ['Belum Bayar', 'Unpaid'],
        ['Lunas', 'Paid'],
        ['Mendesak', 'Urgent'],
        ['MENDESAK', 'URGENT'],
        ['Normal', 'Normal'],
        ['NORMAL', 'NORMAL'],
        ['Tinggi', 'High'],
        ['Rendah', 'Low'],
        ['Kritis', 'Critical'],

        // Technical progress 6 steps
        ['Analisis', 'Analysis'],
        ['ANALISIS', 'ANALYSIS'],
        ['Analisa', 'Analysis'],
        ['Pengembangan', 'Development'],
        ['PENGEMBANGAN', 'DEVELOPMENT'],
        ['Develop', 'Development'],
        ['Pengujian', 'Testing'],
        ['PENGUJIAN', 'TESTING'],
        ['Testing', 'Testing'],
        ['Pelatihan', 'Training'],
        ['PELATIHAN', 'TRAINING'],
        ['Training', 'Training'],
        ['Go Live', 'Go-Live'],
        ['Go-Live', 'Go-Live'],

        // Presales Review Sections & Quotation
        ['INFORMASI PENAWARAN (QUOTATION)', 'QUOTATION INFORMATION'],
        ['INFORMASI PENAWARAN', 'QUOTATION INFORMATION'],
        ['INFORMASI QUOTATION', 'QUOTATION INFORMATION'],
        ['Informasi Penawaran', 'Quotation Information'],
        ['Informasi Quotation', 'Quotation Information'],
        ['Nomor Penawaran', 'Quotation Number'],
        ['Nomor Quotation', 'Quotation Number'],
        ['Tanggal Dibuat', 'Created Date'],
        ['Berlaku Sampai (30 hari)', 'Valid Until (30 days)'],
        ['INFORMASI PENGIRIMAN PENAWARAN', 'QUOTATION DELIVERY INFORMATION'],
        ['INFORMASI PENGIRIMAN QUOTATION', 'QUOTATION DELIVERY INFORMATION'],
        ['Informasi Pengiriman Penawaran', 'Quotation Delivery Information'],
        ['Informasi Pengiriman Quotation', 'Quotation Delivery Information'],
        ['Metode Pengiriman', 'Delivery Method'],
        ['Alamat Email Tujuan', 'Destination Email Address'],
        ['Diverifikasi oleh PM (Terkunci)', 'Verified by PM (Locked)'],
        ['Analisis (hari)', 'Analysis (days)'],
        ['Pengembangan (hari)', 'Development (days)'],
        ['Pengujian (hari)', 'Testing (days)'],
        ['Total Mandays', 'Total Mandays'],
        ['HARI KERJA', 'MANDAYS'],
        ['Hari Kerja', 'Mandays'],
        ['TERMIN PEMBAYARAN (SKEMA TAHAPAN PEMBAYARAN)', 'TERM OF PAYMENT (PAYMENT STAGES SCHEME)'],
        ['TERMIN OF PAYMENT (SKEMA TAHAPAN PEMBAYARAN)', 'TERM OF PAYMENT (PAYMENT STAGES SCHEME)'],
        ['Termin Pembayaran (Skema Tahapan Pembayaran)', 'Term of Payment (Payment Stages Scheme)'],
        ['Termin of Payment (Skema Tahapan Pembayaran)', 'Term of Payment (Payment Stages Scheme)'],
        ['TERMIN PEMBAYARAN', 'TERM OF PAYMENT'],
        ['TERMIN OF PAYMENT', 'TERM OF PAYMENT'],
        ['Termin Pembayaran', 'Term of Payment'],
        ['Termin of Payment', 'Term of Payment'],
        ['+ Isi Termin 100%', '+ Fill 100% Term'],
        ['Tambah Termin', 'Add Term'],
        ['Simpan Termin', 'Save Term'],
        ['Ubah Termin', 'Edit Term'],
        ['KETERANGAN / TAHAPAN DELIVERABLE', 'DESCRIPTION / MILESTONE DELIVERABLE'],
        ['KETERANGAN / MILESTONE DELIVERABLE', 'DESCRIPTION / MILESTONE DELIVERABLE'],
        ['Keterangan / Milestone Deliverable', 'Description / Milestone Deliverable'],
        ['Tahapan Deliverable', 'Milestone Deliverable'],
        ['Milestone Deliverable', 'Milestone Deliverable'],
        ['Hasil Pengerjaan', 'Deliverable'],
        ['Tahapan', 'Milestone'],
        ['Subtotal Termin', 'Term Subtotal'],
        ['Dokumen Penawaran / Quotation', 'Quotation Document'],
        ['Dokumen Penawaran', 'Quotation Document'],
        ['Pilih Berkas', 'Choose File'],
        ['Pilih File', 'Choose File'],
        ['Tidak ada berkas yang dipilih', 'No file chosen'],
        ['Tidak ada file yang dipilih', 'No file chosen'],
        ['Total Nilai Layanan', 'Total Service Value'],
        ['Nilai Penawaran', 'Quotation Value'],
        ['Total Penawaran Akhir', 'Final Quotation Total'],
        ['Total Penawaran Final', 'Final Quotation Total'],
        ['Net Termasuk Pajak', 'Net Including Tax'],
        ['Nett Termasuk Pajak', 'Net Including Tax'],
        ['Simpan & Ajukan', 'Save & Submit'],
        ['Simpan & Submit', 'Save & Submit'],
        ['Termin Ke-', 'Term No. '],
        ['Termin Tunggal / Pelunasan', 'Single Term / Full Settlement'],

        // Payment Tab & Invoicing
        ['Detail Pembayaran & QR', 'Payment & QR Details'],
        ['Unduh Faktur PDF', 'Download Invoice PDF'],
        ['Unduh Invoice PDF', 'Download Invoice PDF'],
        ['Catat Faktur', 'Record Invoice'],
        ['Catat Invoice', 'Record Invoice'],
        ['TOTAL DITAGIHKAN', 'TOTAL INVOICED'],
        ['TOTAL INVOICED', 'TOTAL INVOICED'],
        ['Total Ditagihkan', 'Total Invoiced'],
        ['Total Invoiced', 'Total Invoiced'],
        ['LUNAS', 'PAID'],
        ['MENUNGGU PEMBAYARAN', 'PENDING PAYMENT'],
        ['DAFTAR FAKTUR & TERMIN', 'INVOICE & TERM LIST'],
        ['DAFTAR INVOICE & TERMIN', 'INVOICE & TERM LIST'],
        ['Tgl Faktur:', 'Invoice Date:'],
        ['Tgl Invoice:', 'Invoice Date:'],
        ['Tgl Faktur', 'Invoice Date'],
        ['Tgl Invoice', 'Invoice Date'],
        ['Rencana Bayar:', 'Payment Plan:'],
        ['Plan Bayar:', 'Payment Plan:'],
        ['Rencana Bayar', 'Payment Plan'],
        ['Plan Bayar', 'Payment Plan'],
        ['Aktual:', 'Actual:'],
        ['Total Tagihan Outstanding', 'Total Outstanding Invoices'],
        ['Total Tagihan', 'Total Invoices'],
        ['Outstanding', 'Outstanding'],
        ['Sudah Lunas', 'Already Paid'],
        ['Jatuh Tempo', 'Overdue'],

        // Cards & Sections (CR Detail)
        ['Detail Permintaan Perubahan', 'Change Request Detail'],
        ['Detail Change Request', 'Change Request Detail'],
        ['Melihat Data Lengkap CR', 'Viewing Complete CR Data'],
        ['INFORMASI CR', 'CR INFORMATION'],
        ['Informasi CR', 'CR Information'],
        ['NAMA PERUSAHAAN', 'COMPANY NAME'],
        ['Nama Perusahaan', 'Company Name'],
        ['INISIAL KLIEN', 'CLIENT INITIALS'],
        ['Inisial Klien', 'Client Initials'],
        ['Inisial / Kode:', 'Initials / Code:'],
        ['NAMA PIC', 'PIC NAME'],
        ['Nama PIC', 'PIC Name'],
        ['NAMA PROYEK', 'PROJECT NAME'],
        ['NAMA PROJECT', 'PROJECT NAME'],
        ['Nama Proyek', 'Project Name'],
        ['Nama Project', 'Project Name'],
        ['PEMILIK CR', 'CR OWNER'],
        ['CR OWNER', 'CR OWNER'],
        ['Pemilik CR', 'CR Owner'],
        ['CR Owner', 'CR Owner'],
        ['TANGGAL PENGAJUAN', 'SUBMISSION DATE'],
        ['Tanggal Pengajuan:', 'Submission Date:'],
        ['Tanggal Pengajuan', 'Submission Date'],
        ['Tanggal Permintaan', 'Request Date'],
        ['PERMINTAAN GO-LIVE', 'REQUESTED GO-LIVE'],
        ['REQUEST GO-LIVE', 'REQUESTED GO-LIVE'],
        ['Permintaan Go-Live', 'Requested Go-Live'],
        ['Request Go-Live', 'Requested Go-Live'],
        ['Tanggal Go Live', 'Go-Live Date'],
        ['Target Go Live', 'Target Go-Live'],
        ['PRIORITAS CR', 'CR PRIORITY'],
        ['Prioritas CR', 'CR Priority'],
        ['Prioritas Normal', 'Normal Priority'],
        ['PRIORITAS', 'PRIORITY'],
        ['Prioritas', 'Priority'],

        // Documents
        ['DOKUMEN CR', 'CR DOCUMENT'],
        ['Dokumen CR', 'CR Document'],
        ['DOKUMEN SOLUSI', 'SOLUTION PAPER'],
        ['SOLUTION PAPER', 'SOLUTION PAPER'],
        ['Dokumen Solusi', 'Solution Paper'],
        ['Solution Paper', 'Solution Paper'],
        ['DOKUMEN', 'DOCUMENTS'],
        ['Dokumen', 'Documents'],
        ['Lihat Dokumen', 'View Document'],
        ['Dokumen Dan Lainnya', 'Documents and Others'],
        ['(Hanya format .pdf)', '(PDF format only)'],
        ['Klik untuk unggah PDF', 'Click to upload PDF'],
        ['Klik untuk upload PDF', 'Click to upload PDF'],
        ['Format: .pdf (Maks. 20MB)', 'Format: .pdf (Max. 20MB)'],

        // Description & Mandays
        ['Detail Deskripsi CR', 'CR Detailed Description'],
        ['Data Aktual CR', 'Actual CR Data'],
        ['Data aktual CR', 'Actual CR data'],
        ['ESTIMASI MANDAYS', 'MANDAYS ESTIMATE'],
        ['Estimasi Mandays', 'Mandays Estimate'],
        ['hari kerja', 'working days'],
        [' hari ', ' days '],
        ['Catatan CR', 'CR Notes / Remarks'],
        ['CR Notes / Catatan', 'CR Notes / Remarks'],
        ['Keterangan & Catatan Tambahan', 'Additional Remarks & Notes'],
        ['Catatan Pengajuan', 'Submission Notes'],
        ['Catatan Analisis', 'Analysis Notes'],

        // Tabs
        ['Verifikasi CR', 'CR Verification'],
        ['Status CR', 'CR Status'],
        ['(Terkunci)', '(Locked)'],
        ['Terkunci', 'Locked'],

        // Buttons & Modals
        ['Kembali ke Daftar CR', 'Back to CR List'],
        ['Kembali', 'Back'],
        ['Batal', 'Cancel'],
        ['Hapus', 'Delete'],
        ['Simpan Draft', 'Save Draft'],
        ['Simpan sebagai Draft', 'Save as Draft'],
        ['Simpan sebagai Draf', 'Save as Draft'],
        ['Ajukan CR', 'Submit CR'],
        ['Simpan & Ajukan CR', 'Save & Submit CR'],
        ['Kirim ke PM Head', 'Submit to PM Head'],
        ['Konfirmasi Tolak CR', 'Confirm Reject CR'],
        ['Alasan Penolakan', 'Rejection Reason'],
        ['Status CR Belum Bisa Diisi', 'CR Status Not Fillable Yet'],
        ['Pilih atau Masukkan Project', 'Select or Enter Project'],
        ['Nama CR', 'CR Name'],
        ['Detail CR', 'CR Detail'],
        ['Ajukan CR Baru', 'Submit New CR'],

        // Navigation & Dashboard
        ['Semua CR', 'All CR'],
        ['CR Aktif', 'Active CR'],
        ['CR Selesai', 'Completed CR'],
        ['Semua Status', 'All Statuses'],
        ['Semua Status', 'All Status'],
        ['Draf CR', 'Draft CR'],
        ['Draft CR', 'Draft CR'],
        ['Aktif Bulan Ini', 'Active This Month'],
        ['Lihat Semua', 'View All'],
        ['TGL PENGAJUAN', 'SUBMISSION DATE'],
        ['KODE CR', 'CR CODE'],
        ['Kode CR', 'CR Code'],
        ['PEMOHON', 'REQUESTER'],
        ['Pemohon', 'Requester'],
        ['JUDUL CR', 'CR TITLE'],
        ['Judul CR', 'CR Title'],
        ['PENGAJUAN', 'SUBMISSION'],
        ['Pengajuan', 'Submission'],
        ['AKSI', 'ACTION'],
        ['Aksi', 'Action'],
        ['Tinjau >', 'Review >'],
        ['Tinjau', 'Review'],
        ['TINJAU', 'REVIEW'],
        ['Review', 'Review'],
        ['BARIS PER HALAMAN', 'ROWS PER PAGE'],
        ['Baris per halaman', 'Rows per page'],
        ['ROWS PER PAGE', 'ROWS PER PAGE'],
        ['TOTAL DATA:', 'TOTAL DATA:'],
        ['TOTAL DATA', 'TOTAL DATA'],
        ['HALAMAN', 'PAGE'],
        ['Halaman', 'Page'],
        ['PAGE', 'PAGE'],
        ['DARI', 'OF'],
        ['Dari', 'of'],
        ['OF', 'OF'],
        ['Notifikasi Masuk', 'Incoming Notifications'],
        ['Pesan Masuk (Inbox)', 'Inbox Messages'],
        ['2 Baru', '2 New'],
        ['3 Baru', '3 New'],
        ['Pengaturan', 'Settings'],
        ['Notifikasi', 'Notifications'],
        ['Akun Terverifikasi', 'Verified Account'],
        ['Status Kerjasama:', 'Partnership Status:'],
        ['NAMA KLIEN / PIC UTAMA', 'CLIENT NAME / MAIN PIC'],
        ['EMAIL TERDAFTAR', 'REGISTERED EMAIL'],
        ['NOMOR TELEPON / WA', 'PHONE NUMBER / WA'],
        ['ALAMAT KANTOR OPERASIONAL', 'OPERATIONAL OFFICE ADDRESS'],
        ['PIC Marketing', 'Marketing PIC'],
        ['PIC IT / Technical', 'IT / Technical PIC'],
        ['PIC Procurement', 'Procurement PIC'],
        ['Keluar', 'Logout'],
        ['Profil Saya', 'My Profile'],
        ['Profil Klien', 'Client Profile'],
        ['Klien', 'Client'],
        ['Menunggu Review', 'Awaiting Review'],
        ['Hari Ini', 'Today'],
        ['Kemarin', 'Yesterday'],
        ['Sebelumnya', 'Previous'],
        ['Hari Lalu', 'Days Ago'],
        ['NAMA PM', 'PM NAME'],
        ['Nama PM', 'PM Name'],
        ['Review Awal PMH', 'PMH Initial Review'],
        ['Export Rekap (XLS/PDF)', 'Export Summary (XLS/PDF)'],

        // Select options
        ['Keseluruhan', 'Overall'],
        ['Tahap 1', 'Stage 1'],
        ['Tahap 2', 'Stage 2'],
        ['WhatsApp Bisnis', 'WhatsApp Business'],
        ['Portal Klien', 'Client Portal'],
        ['Kurir / Cetak Fisik', 'Courier / Hardcopy'],

        // Single generic words
        ['Penawaran', 'Quotation'],
        ['PENAWARAN', 'QUOTATION'],
        ['Faktur', 'Invoice'],
        ['FAKTUR', 'INVOICE'],
        ['faktur', 'invoice'],
        ['Pengembangan', 'Development'],
        ['PENGEMBANGAN', 'DEVELOPMENT'],
        ['Draf', 'Draft'],
        ['DRAF', 'DRAFT']
    ];

    window.setAppLanguage = function (lang) {
        lang = (lang || 'id').toLowerCase();
        localStorage.setItem('itpi_lang', lang);

        const label = document.getElementById('currentLangLabel');
        const checkId = document.getElementById('checkLangId');
        const checkEn = document.getElementById('checkLangEn');
        const optId = document.getElementById('langOptId');
        const optEn = document.getElementById('langOptEn');

        if (label) label.textContent = lang.toUpperCase();
        if (checkId && checkEn) {
            if (lang === 'id') {
                checkId.classList.remove('d-none');
                checkEn.classList.add('d-none');
                if (optId) optId.classList.add('active');
                if (optEn) optEn.classList.remove('active');
            } else {
                checkId.classList.add('d-none');
                checkEn.classList.remove('d-none');
                if (optId) optId.classList.remove('active');
                if (optEn) optEn.classList.add('active');
            }
        }

        const dict = appTranslations[lang] || appTranslations.id;

        // 1. Attribute translation: data-i18n
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            if (dict[key] !== undefined) {
                el.textContent = dict[key];
            }
        });

        // 2. Placeholder translation: data-i18n-placeholder
        document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
            const key = el.getAttribute('data-i18n-placeholder');
            if (dict[key] !== undefined) {
                el.setAttribute('placeholder', dict[key]);
            }
        });

        // 3. Bidirectional phrase replacement for text nodes across the UI
        const sortedPhrases = [...phraseReplacements].sort((a, b) => {
            return Math.max(b[0].length, b[1].length) - Math.max(a[0].length, a[1].length);
        });

        function walkTextNodes(node) {
            if (node.nodeType === Node.ELEMENT_NODE && (['INPUT', 'TEXTAREA'].includes(node.tagName) || node.classList?.contains('no-i18n'))) {
                return;
            }

            // Translate SELECT options text
            if (node.nodeType === Node.ELEMENT_NODE && node.tagName === 'SELECT') {
                Array.from(node.options).forEach(opt => {
                    if (opt._origText === undefined) opt._origText = opt.text;
                    let text = opt._origText;
                    if (!text || !text.trim()) return;
                    sortedPhrases.forEach(([idPhrase, enPhrase]) => {
                        if (idPhrase === enPhrase) return;
                        if (lang === 'en' && text.includes(idPhrase)) {
                            text = text.split(idPhrase).join(enPhrase);
                        } else if (lang === 'id' && text.includes(enPhrase)) {
                            text = text.split(enPhrase).join(idPhrase);
                        }
                    });
                    opt.text = text;
                });
                return;
            }

            if (node.nodeType === Node.TEXT_NODE) {
                if (node._origText === undefined) {
                    node._origText = node.nodeValue;
                }
                let text = node._origText;
                if (!text || !text.trim()) return;

                sortedPhrases.forEach(([idPhrase, enPhrase]) => {
                    if (idPhrase === enPhrase) {
                        if (lang === 'id') {
                            if (enPhrase === 'Revision Needed' && text.includes('Revision Needed')) {
                                text = text.split('Revision Needed').join('Perlu Revisi');
                            } else if (enPhrase === 'Awaiting PM Verification' && text.includes('Awaiting PM Verification')) {
                                text = text.split('Awaiting PM Verification').join('Menunggu Tinjauan PM');
                            } else if (enPhrase === 'Awaiting PMH Validation' && text.includes('Awaiting PMH Validation')) {
                                text = text.split('Awaiting PMH Validation').join('Menunggu Persetujuan PM Head');
                            }
                        }
                        return;
                    }

                    if (lang === 'en') {
                        if (text.includes(idPhrase)) {
                            text = text.split(idPhrase).join(enPhrase);
                        }
                    } else {
                        if (text.includes(enPhrase)) {
                            text = text.split(enPhrase).join(idPhrase);
                        }
                    }
                });
                node.nodeValue = text;
            } else if (node.nodeType === Node.ELEMENT_NODE && !['SCRIPT', 'STYLE'].includes(node.tagName)) {
                if (node.hasAttribute('placeholder') && !node.classList.contains('no-i18n')) {
                    if (node._origPlaceholder === undefined) {
                        node._origPlaceholder = node.getAttribute('placeholder');
                    }
                    let ph = node._origPlaceholder;
                    sortedPhrases.forEach(([idPhrase, enPhrase]) => {
                        if (idPhrase === enPhrase) return;
                        if (lang === 'en' && ph.includes(idPhrase)) {
                            ph = ph.split(idPhrase).join(enPhrase);
                        } else if (lang === 'id' && ph.includes(enPhrase)) {
                            ph = ph.split(enPhrase).join(idPhrase);
                        }
                    });
                    node.setAttribute('placeholder', ph);
                }
                if (node.hasAttribute('title') && !node.classList.contains('no-i18n')) {
                    let tit = node.getAttribute('title');
                    sortedPhrases.forEach(([idPhrase, enPhrase]) => {
                        if (idPhrase === enPhrase) return;
                        if (lang === 'en' && tit.includes(idPhrase)) {
                            tit = tit.split(idPhrase).join(enPhrase);
                        } else if (lang === 'id' && tit.includes(enPhrase)) {
                            tit = tit.split(enPhrase).join(idPhrase);
                        }
                    });
                    node.setAttribute('title', tit);
                }
                node.childNodes.forEach(walkTextNodes);
            }
        }

        walkTextNodes(document.body);

        // Dispatch language change event for dynamic components
        window.dispatchEvent(new CustomEvent('appLanguageChanged', { detail: { lang } }));
    };

    const savedLang = localStorage.getItem('itpi_lang') || 'id';
    if (savedLang) {
        setAppLanguage(savedLang);
    }

    /* ── Flash Notification Dismiss & Auto-timer ── */
    function dismissFlashNotification() {
        const notif = document.getElementById('appFlashNotification');
        if (notif) {
            notif.style.transition = 'all 0.35s cubic-bezier(0.16, 1, 0.3, 1)';
            notif.style.opacity = '0';
            notif.style.transform = 'translateY(-20px) scale(0.97)';
            setTimeout(() => notif.remove(), 350);
        }
    }

    const flashProgress = document.getElementById('flashProgressBar');
    if (flashProgress) {
        setTimeout(() => {
            flashProgress.style.width = '0%';
        }, 50);

        setTimeout(() => {
            dismissFlashNotification();
        }, 6050);
    }

    /* ── Confetti Celebration Effect for PMH Approval ── */
    @if(session('cr_action_type') === 'approve' || (session('success') && (str_contains(strtolower(session('success')), 'disetujui') || str_contains(strtolower(session('success')), 'divalidasi'))))
    (function runCelebrationConfetti() {
        const canvas = document.createElement('canvas');
        canvas.id = 'celebrationConfettiCanvas';
        canvas.style.position = 'fixed';
        canvas.style.top = '0';
        canvas.style.left = '0';
        canvas.style.width = '100vw';
        canvas.style.height = '100vh';
        canvas.style.pointerEvents = 'none';
        canvas.style.zIndex = '999999';
        document.body.appendChild(canvas);

        const ctx = canvas.getContext('2d');
        let width = canvas.width = window.innerWidth;
        let height = canvas.height = window.innerHeight;

        window.addEventListener('resize', () => {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        });

        const colors = ['#10B981', '#059669', '#34D399', '#0098EA', '#F59E0B', '#38BDF8', '#6EE7B7'];
        const particles = [];
        const particleCount = 65;

        for (let i = 0; i < particleCount; i++) {
            particles.push({
                x: width * (0.2 + Math.random() * 0.6),
                y: height * 0.15 + (Math.random() * 50),
                r: 4 + Math.random() * 6,
                d: Math.random() * particleCount,
                color: colors[Math.floor(Math.random() * colors.length)],
                tilt: Math.floor(Math.random() * 10) - 10,
                tiltAngleIncremental: (Math.random() * 0.07) + 0.05,
                tiltAngle: 0,
                vx: (Math.random() * 6) - 3,
                vy: (Math.random() * -3) - 2.5,
                opacity: 1
            });
        }

        let startTime = Date.now();
        const duration = 2800;

        function render() {
            const elapsed = Date.now() - startTime;
            if (elapsed > duration) {
                canvas.style.transition = 'opacity 0.4s ease';
                canvas.style.opacity = '0';
                setTimeout(() => canvas.remove(), 400);
                return;
            }

            ctx.clearRect(0, 0, width, height);

            for (let i = 0; i < particleCount; i++) {
                const p = particles[i];
                p.tiltAngle += p.tiltAngleIncremental;
                p.y += (Math.cos(p.d) + 3 + p.r / 2) / 1.5;
                p.x += Math.sin(p.d);
                p.tilt = Math.sin(p.tiltAngle - (i / 3)) * 15;

                ctx.beginPath();
                ctx.lineWidth = p.r;
                ctx.strokeStyle = p.color;
                ctx.moveTo(p.x + p.tilt + (p.r / 2), p.y);
                ctx.lineTo(p.x + p.tilt, p.y + p.tilt + (p.r / 2));
                ctx.stroke();
            }

            requestAnimationFrame(render);
        }

        requestAnimationFrame(render);
    })();
    @endif
</script>

@stack('scripts')
</body>
</html>
