<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo $__env->yieldContent('title', 'CR Manager • Monitoring Change Request'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <?php
        $roleMeta = [
            'client'   => ['brand' => '#007DFF', 'brand_dark' => '#0062CC', 'soft' => '#EFF6FF', 'border' => '#BFDBFE', 'icon' => 'bi-person-workspace', 'label' => 'Client', 'desc' => 'Pengajuan & Tracking Status'],
            'pm'       => ['brand' => '#059669', 'brand_dark' => '#047857', 'soft' => '#ECFDF5', 'border' => '#A7F3D0', 'icon' => 'bi-kanban-fill', 'label' => 'Project Manager', 'desc' => 'Verifikasi, Solution Paper, Status & BA'],
            'pmh'      => ['brand' => '#E11D48', 'brand_dark' => '#BE123C', 'soft' => '#FFF1F2', 'border' => '#FECDD3', 'icon' => 'bi-shield-check', 'label' => 'PM Head', 'desc' => 'Validasi CR & Validasi Go-Live'],
            'finance'  => ['brand' => '#0D9488', 'brand_dark' => '#0F766E', 'soft' => '#F0FDFA', 'border' => '#99F6E4', 'icon' => 'bi-receipt-cutoff', 'label' => 'Marketing / Keuangan', 'desc' => 'Internal Quotation & Invoicing'],
            'presales' => ['brand' => '#0D9488', 'brand_dark' => '#0F766E', 'soft' => '#F0FDFA', 'border' => '#99F6E4', 'icon' => 'bi-receipt-cutoff', 'label' => 'Marketing / Keuangan', 'desc' => 'Internal Quotation & Invoicing'],
            'admin'    => ['brand' => '#4F46E5', 'brand_dark' => '#4338CA', 'soft' => '#EEF2FF', 'border' => '#C7D2FE', 'icon' => 'bi-gear-wide-connected', 'label' => 'Administrator', 'desc' => 'Master Data (Client, Pegawai, Status)'],
        ];
        $currentRole = auth()->user()?->role ?? 'guest';
        $rm = $roleMeta[$currentRole] ?? ['brand' => '#007DFF', 'brand_dark' => '#0062CC', 'soft' => '#EFF6FF', 'border' => '#BFDBFE', 'icon' => 'bi-person', 'label' => 'Tamu', 'desc' => 'Akses Publik'];
        $currentRouteName = Route::currentRouteName() ?? '';
    ?>

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
            --brand:        <?php echo e($rm['brand']); ?>;
            --brand-dark:   <?php echo e($rm['brand_dark']); ?>;
            --brand-soft:   <?php echo e($rm['soft']); ?>;
            --brand-border: <?php echo e($rm['border']); ?>;

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
        body.dark-theme .cr-table thead th {
            background-color: var(--panel-secondary) !important;
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
        }

        .app-sidebar .sidebar-link {
            color: rgba(255, 255, 255, 0.85) !important;
            padding: 0.65rem 1rem !important;
            border-radius: 9999px !important;
            font-size: 0.88rem !important;
            transition: all 0.15s ease !important;
        }
        .app-sidebar .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #FFFFFF !important;
        }
        .app-sidebar .sidebar-link.active {
            background: #0063D7 !important;
            color: #FFFFFF !important;
            box-shadow: none !important;
        }
        .app-sidebar .sidebar-link.active::before {
            display: none !important;
        }

        .topbar-search-input::placeholder {
            color: #94A3B8 !important;
            opacity: 1 !important;
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

        .cr-table thead th {
            background: #F8FAFC !important;
            padding: 1rem 1.25rem;
            color: #64748B;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 1px solid var(--line);
            white-space: nowrap;
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
            .main-wrapper { margin-left: 0 !important; }
            body { background: #FFFFFF !important; color: #000 !important; }
            .card { border: 1px solid #E2E8F0 !important; box-shadow: none !important; break-inside: avoid; }
            .main-content { padding: 0 !important; }
        }
    </style>
</head>
<body class="role-<?php echo e($currentRole); ?>">

<div class="app-shell">

    
    <?php if(auth()->guard()->check()): ?>
    <aside class="app-sidebar" id="appSidebar" role="navigation" aria-label="Navigasi Utama" style="background: #02376A; border-right: 1px solid rgba(255,255,255,0.06); width: 235px;">

        
        <div class="sidebar-header" style="padding: 1.35rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06);">
            <a class="sidebar-brand d-flex align-items-center gap-2 text-decoration-none" href="<?php echo e(route('change-requests.index')); ?>">
                <img src="<?php echo e(asset('images/itpi_logo_white_tight.png')); ?>" alt="ITPI" style="height: 34px; width: auto; object-fit: contain;">
                <div style="color: #FFFFFF; font-weight: 800; font-size: 0.95rem; letter-spacing: -0.01em; white-space: nowrap;">
                    PT. ITPI Technology
                </div>
            </a>
        </div>

        
        <nav class="sidebar-nav" style="padding: 1.1rem 0.85rem;">

            
            <span class="nav-section-label" data-i18n="menu_header" style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em; color: rgba(255,255,255,0.7); padding: 0.2rem 0.75rem 0.6rem; text-transform: uppercase; display: block;">
                MENU
            </span>

            
            <?php
                $isDashboardActive = ($currentRouteName === 'change-requests.index' && (!request()->filled('view') || request('view') === 'dashboard') && !request()->filled('search') && !request()->filled('status'));
                $isCrShow = request()->routeIs('change-requests.show');
                $isCrGroupActive = $isCrShow || (request()->routeIs('change-requests.*') && !$isDashboardActive);
                $isSemuaCrActive = $isCrShow || request('view') === 'total' || ($currentRouteName === 'change-requests.index' && !$isDashboardActive && !request()->filled('status') && $currentRouteName !== 'change-requests.create');
            ?>
            <a href="<?php echo e(route('change-requests.index')); ?>"
               class="sidebar-link <?php echo e($isDashboardActive ? 'active' : ''); ?>"
               style="position: relative; border-radius: 9999px; padding: 0.65rem 1rem; font-size: 0.88rem; <?php echo e($isDashboardActive ? 'background: #0063D7 !important; color: #FFFFFF !important;' : ''); ?>">
                <i class="bi bi-grid-fill nav-icon" style="font-size: 1.05rem;"></i>
                <span class="fw-semibold" data-i18n="nav_dashboard">Dashboard</span>
            </a>

            
            <div class="nav-group-toggle open"
                 id="crGroupToggle"
                 role="button"
                 onclick="this.classList.toggle('open'); document.getElementById('crGroupBody').classList.toggle('open');"
                 aria-expanded="true"
                 style="border-radius: 9999px; padding: 0.65rem 1.15rem; font-size: 0.92rem; display: flex; align-items: center; gap: 0.75rem; color: #FFFFFF; margin-top: 0.35rem; <?php echo e($isCrGroupActive ? 'background: #0063D7 !important;' : 'background: transparent;'); ?>">
                <i class="bi bi-file-earmark-text nav-icon" style="font-size: 1.05rem; opacity: 1;"></i>
                <span class="fw-bold" data-i18n="nav_cr" style="letter-spacing: -0.01em;">Change Request</span>
                <i class="bi bi-chevron-down toggle-chevron ms-auto" style="font-size: 0.8rem; font-weight: 700; opacity: 0.95;"></i>
            </div>

            <div class="nav-group-body sidebar-sub open" id="crGroupBody" style="padding-left: 0.45rem; display: flex; flex-direction: column; gap: 0.35rem; margin-top: 0.35rem;">
                
                <a href="<?php echo e(route('change-requests.index', ['view' => 'total'])); ?>"
                   class="sidebar-link <?php echo e($isSemuaCrActive ? 'active' : ''); ?>"
                   style="border-radius: 9999px; padding: 0.62rem 1.15rem; font-size: 0.9rem; color: #FFFFFF; display: flex; align-items: center; gap: 0.65rem; <?php echo e($isSemuaCrActive ? 'background: #0063D7 !important; color: #FFFFFF !important;' : 'background: transparent; color: rgba(255,255,255,0.85);'); ?>">
                    <?php if($isSemuaCrActive): ?>
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #00B4FF; display: inline-block; flex-shrink: 0; box-shadow: 0 0 10px #00B4FF;"></span>
                    <?php endif; ?>
                    <i class="bi bi-file-earmark-text nav-icon" style="font-size: 1.05rem; color: #FFFFFF; opacity: 1;"></i>
                    <span class="fw-bold" data-i18n="nav_all_cr" style="color: #FFFFFF; letter-spacing: -0.01em;">Semua CR</span>
                </a>

                
                <a href="<?php echo e(route('change-requests.create')); ?>"
                   class="sidebar-link <?php echo e($currentRouteName === 'change-requests.create' ? 'active' : ''); ?>"
                   style="border-radius: 9999px; padding: 0.55rem 1.15rem; font-size: 0.86rem; color: rgba(255,255,255,0.85); display: flex; align-items: center; gap: 0.65rem; <?php echo e($currentRouteName === 'change-requests.create' ? 'background: #0063D7 !important; color: #FFFFFF !important;' : 'background: transparent;'); ?>">
                    <?php if($currentRouteName === 'change-requests.create'): ?>
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #00B4FF; display: inline-block; flex-shrink: 0; box-shadow: 0 0 10px #00B4FF;"></span>
                    <?php endif; ?>
                    <i class="bi bi-pencil-square nav-icon" style="font-size: 0.95rem;"></i>
                    <span data-i18n="nav_submit_cr">Ajukan CR</span>
                </a>

                
                <a href="<?php echo e(route('change-requests.index', ['status' => 'diajukan'])); ?>"
                   class="sidebar-link <?php echo e(request('status') === 'diajukan' ? 'active' : ''); ?>"
                   style="border-radius: 9999px; padding: 0.55rem 1.15rem; font-size: 0.86rem; color: rgba(255,255,255,0.85); display: flex; align-items: center; gap: 0.65rem; <?php echo e(request('status') === 'diajukan' ? 'background: #0063D7 !important; color: #FFFFFF !important;' : 'background: transparent;'); ?>">
                    <?php if(request('status') === 'diajukan'): ?>
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #00B4FF; display: inline-block; flex-shrink: 0; box-shadow: 0 0 10px #00B4FF;"></span>
                    <?php endif; ?>
                    <i class="bi bi-bar-chart-line nav-icon" style="font-size: 0.95rem;"></i>
                    <span data-i18n="nav_draft_cr">Draft CR</span>
                </a>
            </div>

            
            <a href="javascript:void(0)"
               class="sidebar-link d-flex align-items-center justify-content-between"
               onclick="const toastEl = document.getElementById('notifToast'); if(toastEl){ new bootstrap.Toast(toastEl).show(); }"
               style="border-radius: 10px; padding: 0.65rem 1rem; font-size: 0.88rem; color: rgba(255,255,255,0.85); margin-top: 0.35rem;">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-bell nav-icon" style="font-size: 1.05rem;"></i>
                    <span class="fw-medium" data-i18n="nav_notifications">Notifikasi</span>
                </span>
            </a>

            
            <?php if($currentRole === 'admin'): ?>
                <div class="nav-group-toggle <?php echo e(str_starts_with($currentRouteName, 'master.') ? 'open' : ''); ?>"
                     id="settingsGroupToggle"
                     role="button"
                     onclick="this.classList.toggle('open'); document.getElementById('settingsGroupBody').classList.toggle('open');"
                     aria-expanded="<?php echo e(str_starts_with($currentRouteName, 'master.') ? 'true' : 'false'); ?>"
                     style="border-radius: 10px; padding: 0.65rem 1rem; font-size: 0.88rem; color: rgba(255,255,255,0.85); margin-top: 0.35rem;">
                    <i class="bi bi-gear nav-icon" style="font-size: 1.05rem;"></i>
                    <span class="fw-medium" data-i18n="nav_settings">Pengaturan</span>
                    <i class="bi bi-chevron-down toggle-chevron ms-auto" style="font-size: 0.75rem; opacity: 0.6;"></i>
                </div>

                <div class="nav-group-body sidebar-sub <?php echo e(str_starts_with($currentRouteName, 'master.') ? 'open' : ''); ?>" id="settingsGroupBody" style="padding-left: 1.2rem; display: flex; flex-direction: column; gap: 0.3rem;">
                    <a href="<?php echo e(route('master.clients.index')); ?>"
                       class="sidebar-link <?php echo e(str_starts_with($currentRouteName, 'master.clients') ? 'active' : ''); ?>"
                       style="border-radius: 8px; padding: 0.45rem 0.75rem; font-size: 0.84rem;">
                        <i class="bi bi-buildings nav-icon"></i>
                        <span data-i18n="master_clients">Master Client</span>
                    </a>
                    <a href="<?php echo e(route('master.pegawai.index')); ?>"
                       class="sidebar-link <?php echo e(str_starts_with($currentRouteName, 'master.pegawai') ? 'active' : ''); ?>"
                       style="border-radius: 8px; padding: 0.45rem 0.75rem; font-size: 0.84rem;">
                        <i class="bi bi-person-badge nav-icon"></i>
                        <span data-i18n="master_pegawai">Master Pegawai</span>
                    </a>
                    <a href="<?php echo e(route('master.status.index')); ?>"
                       class="sidebar-link <?php echo e(str_starts_with($currentRouteName, 'master.status') ? 'active' : ''); ?>"
                       style="border-radius: 8px; padding: 0.45rem 0.75rem; font-size: 0.84rem;">
                        <i class="bi bi-list-check nav-icon"></i>
                        <span data-i18n="master_status">Master Status</span>
                    </a>
                </div>
            <?php else: ?>
                <a href="<?php echo e($currentRole === 'client' ? route('client.profile') : 'javascript:void(0)'); ?>"
                   class="sidebar-link <?php echo e($currentRouteName === 'client.profile' ? 'active' : ''); ?>"
                   style="border-radius: 10px; padding: 0.65rem 1rem; font-size: 0.88rem; color: rgba(255,255,255,0.85); margin-top: 0.35rem;">
                    <i class="bi bi-gear nav-icon" style="font-size: 1.05rem;"></i>
                    <span class="fw-medium" data-i18n="nav_settings">Pengaturan</span>
                </a>
            <?php endif; ?>

        </nav>

    </aside>

    
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <?php endif; ?>

    
    <div class="main-wrapper" id="mainWrapper">

        
        <?php if(auth()->guard()->check()): ?>
        <header class="main-topbar" style="background: #F0F3F6; border-bottom: 1px solid #E2E8F0; height: 72px; padding: 0 2rem; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 1020;">
            
            <button class="topbar-hamburger me-2 d-lg-none" id="sidebarToggleBtn" type="button" aria-label="Buka/Tutup Sidebar">
                <i class="bi bi-list"></i>
            </button>

            <?php if(!request()->routeIs('change-requests.show')): ?>
            
            <form method="GET" action="<?php echo e(route('change-requests.index')); ?>" class="m-0" style="flex: 1; max-width: 360px;">
                <input type="hidden" name="view" value="total">
                <div style="position: relative; display: flex; align-items: center;">
                    <i class="bi bi-search" style="position: absolute; left: 16px; color: #94A3B8; font-size: 0.9rem; pointer-events: none;"></i>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                           placeholder="Search task or CR number..."
                           data-i18n-placeholder="search_placeholder"
                           class="topbar-search-input"
                           style="background: #FFFFFF; color: #1E293B; border: 1.5px solid #E2E8F0; border-radius: 999px; padding: 0.58rem 1.25rem 0.58rem 2.6rem; font-size: 0.84rem; width: 100%; outline: none; transition: all 0.2s ease;"
                           onfocus="this.style.borderColor='#0063D7'; this.style.boxShadow='0 0 0 3px rgba(0,99,215,0.12)';"
                           onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none';">
                </div>
            </form>
            <?php else: ?>
            <div style="flex: 1;"></div>
            <?php endif; ?>

            
            <div class="d-flex align-items-center gap-3">
                
                
                <div class="dropdown">
                    <button class="d-flex align-items-center gap-1 px-2 py-1 rounded-pill border-0 bg-white"
                            id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            style="border: 1.5px solid #E2E8F0 !important; font-size: 0.76rem; font-weight: 600; color: #64748B; cursor: pointer;">
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

                
                <div class="dropdown">
                    <button class="btn p-0 position-relative d-flex align-items-center justify-content-center border-0 bg-transparent"
                            type="button" id="inboxDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            style="width: 36px; height: 36px; color: #475569; font-size: 1.15rem;">
                        <i class="bi bi-inbox"></i>
                        <span style="position: absolute; top: 7px; right: 5px; width: 7px; height: 7px; border-radius: 50%; background: #EF4444; border: 1.5px solid #FFFFFF;"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-3 mt-2" style="width: 320px; border-radius: 14px;" aria-labelledby="inboxDropdown">
                        <li class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small text-dark" data-i18n="inbox_title">Pesan Masuk (Inbox)</span>
                            <span class="badge bg-primary-subtle text-primary rounded-pill" style="font-size: 0.68rem;" data-i18n="inbox_new_badge">2 Baru</span>
                        </li>
                        <li class="mb-2">
                            <div class="p-2 rounded-2" style="background: #F0F9FF; border-left: 3px solid #0284C7;">
                                <div class="fw-bold" style="font-size: 0.76rem; color: #0369A1;">PM Mochammad David</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Dokumen estimasi biaya CR-2026-004 telah diperbarui.</div>
                            </div>
                        </li>
                        <li>
                            <div class="p-2 rounded-2" style="background: #F8FAFC; border-left: 3px solid #94A3B8;">
                                <div class="fw-bold" style="font-size: 0.76rem; color: #334155;">System Support</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Pengajuan CR Anda sedang dalam proses verifikasi tim sales.</div>
                            </div>
                        </li>
                    </ul>
                </div>

                
                <div class="dropdown">
                    <button class="btn p-0 position-relative d-flex align-items-center justify-content-center border-0 bg-transparent" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            style="width: 36px; height: 36px; color: #475569; font-size: 1.15rem;">
                        <i class="bi bi-bell"></i>
                        <span style="position: absolute; top: 7px; right: 5px; width: 7px; height: 7px; border-radius: 50%; background: #EF4444; border: 1.5px solid #FFFFFF;"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-3 mt-2" style="width: 320px; border-radius: 14px;" aria-labelledby="notifDropdown">
                        <li class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small text-dark" data-i18n="notif_incoming">Notifikasi Masuk</span>
                            <span class="badge bg-danger-subtle text-danger rounded-pill" style="font-size: 0.68rem;" data-i18n="notif_badge">3 Baru</span>
                        </li>
                        <li class="mb-2">
                            <div class="p-2 rounded-2" style="background: #FEF2F2; border-left: 3px solid #EF4444;">
                                <div class="fw-bold" style="font-size: 0.76rem; color: #991B1B;">Batas Tenggat: Hari ini</div>
                                <div class="text-muted" style="font-size: 0.72rem;">CR Pengajuan Layanan Baru BAP/SPK</div>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="p-2 rounded-2" style="background: #FFFBEB; border-left: 3px solid #F59E0B;">
                                <div class="fw-bold" style="font-size: 0.76rem; color: #92400E;">Menunggu Konfirmasi</div>
                                <div class="text-muted" style="font-size: 0.72rem;">3 CR Baru Menunggu Konfirmasi</div>
                            </div>
                        </li>
                        <li>
                            <div class="p-2 rounded-2" style="background: #EFF6FF; border-left: 3px solid #3B82F6;">
                                <div class="fw-bold" style="font-size: 0.76rem; color: #1E40AF;">BAP Ditandatangani</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Dokumen BAP Anggota Selesai</div>
                            </div>
                        </li>
                    </ul>
                </div>

                
                <div class="dropdown">
                    <div class="d-flex align-items-center gap-2 ps-1 cursor-pointer" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm"
                             style="width: 40px; height: 40px; background: #D9D9D9; color: #334155; font-size: 0.85rem; flex-shrink: 0;">
                            TA
                        </div>
                        <div style="line-height: 1.25;" class="d-none d-sm-block text-start">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.86rem; font-weight: 700; letter-spacing: -0.01em; color: #0F172A;">
                                <?php echo e(auth()->check() ? auth()->user()->name : 'Totok Antok'); ?>

                            </div>
                            <div class="text-muted text-truncate" style="font-size: 0.72rem; color: #64748B;" data-i18n="user_role_label">
                                <?php echo e((auth()->check() && auth()->user()->isClient()) ? 'Klien' : (auth()->check() ? ucfirst(auth()->user()->role) : 'Klien')); ?>

                            </div>
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 p-2" style="border-radius: 12px; min-width: 180px;" aria-labelledby="userMenuDropdown">
                        <li>
                            <div class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold small"><?php echo e(auth()->user()->name); ?></div>
                                <div class="text-muted" style="font-size: 0.72rem;"><?php echo e(ucfirst(auth()->user()->role)); ?></div>
                            </div>
                        </li>
                        <?php if(auth()->user()->isClient()): ?>
                            <li><a class="dropdown-item py-1 px-3 small" href="<?php echo e(route('client.profile')); ?>"><i class="bi bi-person me-2"></i> Profil Klien</a></li>
                        <?php endif; ?>
                        <?php if(auth()->user()->isAdmin()): ?>
                            <li><a class="dropdown-item py-1 px-3 small" href="<?php echo e(route('master.clients.index')); ?>"><i class="bi bi-gear me-2"></i> Master Data</a></li>
                        <?php endif; ?>
                        <li>
                            <form method="POST" action="<?php echo e(route('logout')); ?>" class="m-0">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item py-1 px-3 small text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        <?php endif; ?>

        
        <?php if(session('success') || session('error')): ?>
            <div style="padding: 1rem 1.75rem 0;" class="d-print-none">
                <?php if(session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-0" role="alert">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <div><?php echo e(session('success')); ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-0" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <div><?php echo e(session('error')); ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        
        <main class="main-content" id="mainContent">
            <?php echo $__env->yieldContent('content'); ?>
        </main>

        
        <?php if(auth()->guard()->check()): ?>
        <?php if(!request()->routeIs('change-requests.show')): ?>
        <footer class="main-footer d-print-none">
            <div>
                &copy; <?php echo e(date('Y')); ?> <strong>PT ITPI Digital Solutions</strong> &middot; CR Manager v2
            </div>
            <div class="d-flex gap-3 text-muted align-items-center">
                <span><i class="bi bi-shield-check text-success me-1"></i>End-to-End Workflow Active</span>
            </div>
        </footer>
        <?php endif; ?>
        <?php endif; ?>
    </div>

</div>


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
            'badge_analisa': '• Analisa',
            'status_review': 'Menunggu Review',
            'priority_badge': 'Prioritas Normal',
            'label_submission_date_inline': 'Tanggal Pengajuan:',
            'val_header_date': '04 Sep 2026',
            'progress_work': 'PROGRESS PENGERJAAN',
            'progress_badge_pct': '17% — Analisa',
            'stage_1_analisa': 'Analisa',
            'stage_2_develop': 'Develop',
            'stage_3_sit': 'SIT',
            'stage_4_uat': 'UAT',
            'stage_5_training': 'Training',
            'stage_6_golive': 'Go Live',
            'section_info_cr': 'INFORMASI CR',
            'label_company': 'NAMA PERUSAHAAN',
            'val_company_name': 'PT Maju Bersama',
            'label_client_initial': 'INISIAL KLIEN',
            'val_client_initial': 'AF',
            'label_pic_name': 'NAMA PIC',
            'val_pic_name': 'Totok Antok',
            'label_project_name': 'NAMA PROJECT',
            'val_project_name': 'Sistem E-commerce',
            'label_cr_owner': 'CR OWNER',
            'val_cr_owner': 'Andik Virmansyah',
            'label_submission_date': 'TANGGAL PENGAJUAN',
            'val_submission_date': '15 Jan 2024',
            'label_request_golive': 'REQUEST GO-LIVE',
            'val_request_golive': '15 Jan 2024',
            'label_cr_priority': 'PRIORITAS CR',
            'val_priority_normal': 'Normal',
            'section_documents': 'DOKUMEN',
            'label_doc_cr': 'DOKUMEN CR',
            'label_solution_paper': 'SOLUTION PAPER',
            'section_detail_desc': 'Detail Deskripsi CR',
            'rendered_wysiwyg': 'Rendered WYSIWYG Content',
            'cr_description_text': 'Implementasi penambahan kanal pembayaran digital menggunakan e-wallet OVO (Push to Pay & QRIS) pada modul checkout Sistem E-commerce. Hal ini bertujuan untuk menaikkan rasio konversi checkout pelanggan serta mengurangi tingkat abandoned cart pada saat proses transaksi pembelian online.',
            'cr_scope_item_1': 'Penambahan opsi pembayaran OVO Wallet pada step 3 (Metode Pembayaran) di aplikasi Web dan Mobile.',
            'cr_scope_item_2': 'Integrasi Webhook Callback Service untuk konfirmasi status settlement secara real-time.',
            'cr_scope_item_3': 'Penyelarasan modul rekonsiliasi harian dan penyesuaian laporan keuangan di portal admin.',
            'cr_scope_item_4': 'Penambahan unit test coverage dan staging automated testing minimum 85%.',
            'section_mandays': 'ESTIMASI MANDAYS',
            'mandays_analisis': 'Analisis',
            'mandays_analisis_val': '0 hari',
            'mandays_development': 'Development',
            'mandays_dev_val': '0 hari',
            'mandays_testing': 'Testing',
            'mandays_test_val': '0 hari',
            'mandays_total': 'Total',
            'mandays_total_val': '0 hari kerja',
            'unit_hari': 'hari',
            'unit_hari_kerja': 'hari kerja',
            'section_cr_notes': 'CR Notes / Catatan',
            'cr_notes_quote': '“Mohon diprioritaskan untuk integrasi sandbox staging sebelum tanggal 15 September agar tim QA dapat melakukan testing payment gateway secara menyeluruh. Testing account sudah kami koordinasikan dengan pihak vendor OVO.”',
            'callout_dependency_title': 'Catatan Dependensi:',
            'callout_dependency_body': 'Membutuhkan integrasi API Gateway credentials (Client ID & Secret Key) production dari pihak Payment Aggregator sebelum tanggal 18 Sep 2026.',
            'h3_background_purpose': '1. LATAR BELAKANG & TUJUAN',
            'h3_scope_of_work': '2. RUANG LINGKUP PERUBAHAN (SCOPE OF WORK)',
            'h3_technical_impact': '3. DAMPAK TEKNIS & DEPENDENCIES',
            'user_role_label': 'Klien'
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
            'priority_badge': 'Normal Priority',
            'label_submission_date_inline': 'Submission Date:',
            'val_header_date': '04 Sep 2026',
            'progress_work': 'WORK PROGRESS',
            'progress_badge_pct': '17% — Analysis',
            'stage_1_analisa': 'Analysis',
            'stage_2_develop': 'Develop',
            'stage_3_sit': 'SIT',
            'stage_4_uat': 'UAT',
            'stage_5_training': 'Training',
            'stage_6_golive': 'Go Live',
            'section_info_cr': 'CR INFORMATION',
            'label_company': 'COMPANY NAME',
            'val_company_name': 'PT Maju Bersama',
            'label_client_initial': 'CLIENT INITIAL',
            'val_client_initial': 'AF',
            'label_pic_name': 'PIC NAME',
            'val_pic_name': 'Totok Antok',
            'label_project_name': 'PROJECT NAME',
            'val_project_name': 'E-commerce System',
            'label_cr_owner': 'CR OWNER',
            'val_cr_owner': 'Andik Virmansyah',
            'label_submission_date': 'SUBMISSION DATE',
            'val_submission_date': '15 Jan 2024',
            'label_request_golive': 'REQUESTED GO-LIVE',
            'val_request_golive': '15 Jan 2024',
            'label_cr_priority': 'CR PRIORITY',
            'val_priority_normal': 'Normal',
            'section_documents': 'DOCUMENTS',
            'label_doc_cr': 'CR DOCUMENT',
            'label_solution_paper': 'SOLUTION PAPER',
            'section_detail_desc': 'CR Detailed Description',
            'rendered_wysiwyg': 'Rendered WYSIWYG Content',
            'cr_description_text': 'Implementation of digital payment channel addition using OVO e-wallet (Push to Pay & QRIS) in the E-commerce System checkout module. This aims to increase customer checkout conversion rates and reduce abandoned cart rates during online purchasing transactions.',
            'cr_scope_item_1': 'Addition of OVO Wallet payment option at step 3 (Payment Method) in Web and Mobile applications.',
            'cr_scope_item_2': 'Integration of Webhook Callback Service for real-time settlement status confirmation.',
            'cr_scope_item_3': 'Alignment of daily reconciliation module and financial report adjustments in admin portal.',
            'cr_scope_item_4': 'Addition of unit test coverage and minimum 85% staging automated testing.',
            'section_mandays': 'MANDAYS ESTIMATE',
            'mandays_analisis': 'Analysis',
            'mandays_analisis_val': '0 days',
            'mandays_development': 'Development',
            'mandays_dev_val': '0 days',
            'mandays_testing': 'Testing',
            'mandays_test_val': '0 days',
            'mandays_total': 'Total',
            'mandays_total_val': '0 working days',
            'unit_hari': 'days',
            'unit_hari_kerja': 'working days',
            'section_cr_notes': 'CR Notes / Remarks',
            'cr_notes_quote': '“Please prioritize staging sandbox integration before September 15 so QA team can conduct thorough payment gateway testing. The testing account has been coordinated with the OVO vendor.”',
            'callout_dependency_title': 'Dependency Note:',
            'callout_dependency_body': 'Requires production API Gateway credentials (Client ID & Secret Key) integration from Payment Aggregator before 18 Sep 2026.',
            'h3_background_purpose': '1. BACKGROUND & PURPOSE',
            'h3_scope_of_work': '2. SCOPE OF CHANGE (SCOPE OF WORK)',
            'h3_technical_impact': '3. TECHNICAL IMPACT & DEPENDENCIES',
            'user_role_label': 'Client'
        }
    };

    const phraseReplacements = [
        ['Detail Change Request', 'Detail Change Request'],
        ['Melihat Data Lengkap CR', 'Viewing Complete CR Data'],
        ['Pantau seluruh permintaan perubahan Anda', 'Monitor all your change requests'],
        ['Ajukan CR Baru', 'Submit New CR'],
        ['Semua CR', 'All CR'],
        ['Aktif Bulan Ini', 'Active This Month'],
        ['Monitoring pergerakan CR melalui 6 tahapan siklus proyek TI', 'Monitoring CR progression through 6 IT project lifecycle stages'],
        ['Pemberitahuan persetujuan penting, batas waktu tinjauan dokumen, dan tindak lanjut status tahapan proyek.', 'Important approval notices, document review deadlines, and project stage follow-ups.'],
        ['3 Perubahan Ruang Lingkup CR menunggu persetujuan > 14 hari', '3 CR Scope Changes awaiting approval > 14 days'],
        ['Status : Menunggu Persetujuan PM', 'Status : Awaiting PM Approval'],
        ['8 CR Baru Menunggu Verifikasi Kebutuhan', '8 New CRs Awaiting Requirements Verification'],
        ['Tahapan : 1. Analisis Kebutuhan', 'Stage : 1. Requirements Analysis'],
        ['5 Masukan Pengguna UAT butuh tindak lanjut', '5 UAT User Feedbacks need follow-up'],
        ['Tahapan : 4. UAT & Pengujian', 'Stage : 4. UAT & Testing'],
        ['Lihat Semua', 'View All'],
        ['TGL PENGAJUAN', 'SUBMISSION DATE'],
        ['PEMOHON', 'REQUESTER'],
        ['JUDUL CR', 'CR TITLE'],
        ['PRIORITAS CR', 'CR PRIORITY'],
        ['PRIORITAS', 'PRIORITY'],
        ['AKSI', 'ACTION'],
        ['Dokumen BAP Anggota Selesai', 'Member BAP Document Completed'],
        ['BAP Ditandatangani', 'BAP Signed'],
        ['Menunggu Konfirmasi', 'Awaiting Confirmation'],
        ['3 CR Baru Menunggu Konfirmasi', '3 New CRs Awaiting Confirmation'],
        ['Batas Tenggat: Hari ini', 'Deadline: Today'],
        ['CR Pengajuan Layanan Baru BAP/SPK', 'New BAP/SPK Service Request CR'],
        ['Notifikasi Masuk', 'Incoming Notifications'],
        ['Pesan Masuk (Inbox)', 'Inbox Messages'],
        ['2 Baru', '2 New'],
        ['3 Baru', '3 New'],
        ['Pengaturan', 'Settings'],
        ['Notifikasi', 'Notifications'],
        ['Kembali ke Daftar CR', 'Back to CR List'],
        ['Akun Terverifikasi', 'Verified Account'],
        ['Status Kerjasama:', 'Partnership Status:'],
        ['Inisial / Kode:', 'Initials / Code:'],
        ['NAMA KLIEN / PIC UTAMA', 'CLIENT NAME / MAIN PIC'],
        ['EMAIL TERDAFTAR', 'REGISTERED EMAIL'],
        ['NOMOR TELEPON / WA', 'PHONE NUMBER / WA'],
        ['ALAMAT KANTOR OPERASIONAL', 'OPERATIONAL OFFICE ADDRESS'],
        ['Daftar 3 PIC Resmi Master Client', 'List of 3 Official Master Client PICs'],
        ['PIC Marketing', 'Marketing PIC'],
        ['PIC IT / Technical', 'IT / Technical PIC'],
        ['PIC Procurement', 'Procurement PIC'],
        ['Keluar', 'Logout'],
        ['Profil Saya', 'My Profile'],
        ['Pelatihan', 'Training'],
        ['Selesai', 'Done'],
        ['Menunggu Review', 'Awaiting Review'],
        ['Prioritas Normal', 'Normal Priority'],
        ['Tanggal Pengajuan:', 'Submission Date:'],
        ['Tanggal Pengajuan', 'Submission Date'],
        ['PROGRESS PENGERJAAN', 'WORK PROGRESS'],
        ['INFORMASI CR', 'CR INFORMATION'],
        ['NAMA PERUSAHAAN', 'COMPANY NAME'],
        ['INISIAL KLIEN', 'CLIENT INITIAL'],
        ['NAMA PIC', 'PIC NAME'],
        ['NAMA PROJECT', 'PROJECT NAME'],
        ['CR OWNER', 'CR OWNER'],
        ['REQUEST GO-LIVE', 'REQUESTED GO-LIVE'],
        ['DOKUMEN CR', 'CR DOCUMENT'],
        ['SOLUTION PAPER', 'SOLUTION PAPER'],
        ['DOKUMEN', 'DOCUMENTS'],
        ['Detail Deskripsi CR', 'Detailed Description of CR'],
        ['Rendered WYSIWYG Content', 'Rendered WYSIWYG Content'],
        ['1. LATAR BELAKANG & TUJUAN', '1. BACKGROUND & PURPOSE'],
        ['2. RUANG LINGKUP PERUBAHAN (SCOPE OF WORK)', '2. SCOPE OF CHANGE (SCOPE OF WORK)'],
        ['3. DAMPAK TEKNIS & DEPENDENCIES', '3. TECHNICAL IMPACT & DEPENDENCIES'],
        ['Catatan Dependensi:', 'Dependency Note:'],
        ['ESTIMASI MANDAYS', 'MANDAYS ESTIMATE'],
        ['Analisis', 'Analysis'],
        ['hari kerja', 'working days'],
        ['hari', 'days'],
        ['CR Notes / Catatan', 'CR Notes / Remarks'],
        ['Klien', 'Client'],
        ['Penambahan opsi pembayaran OVO Wallet pada step 3 (Metode Pembayaran) di aplikasi Web dan Mobile.', 'Addition of OVO Wallet payment option at step 3 (Payment Method) in Web and Mobile apps.'],
        ['Integrasi Webhook Callback Service untuk konfirmasi status settlement secara real-time.', 'Integration of Webhook Callback Service for real-time settlement status confirmation.'],
        ['Penyelarasan modul rekonsiliasi harian dan penyesuaian laporan keuangan di portal admin.', 'Alignment of daily reconciliation module and financial report adjustments in admin portal.'],
        ['Penambahan unit test coverage dan staging automated testing minimum 85%.', 'Addition of unit test coverage and minimum 85% staging automated testing.'],
        ['Mohon diprioritaskan untuk integrasi sandbox staging sebelum tanggal 15 September agar tim QA dapat melakukan testing payment gateway secara menyeluruh. Testing account sudah kami koordinasikan dengan pihak vendor OVO.', 'Please prioritize staging sandbox integration before September 15 so QA team can perform thorough payment gateway testing. Testing account has been coordinated with OVO vendor.']
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
        function walkTextNodes(node) {
            if (node.nodeType === Node.TEXT_NODE) {
                let text = node.nodeValue;
                if (!text || !text.trim()) return;

                phraseReplacements.forEach(([idPhrase, enPhrase]) => {
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
                // If element has placeholder and no data-i18n-placeholder
                if (node.hasAttribute('placeholder')) {
                    let ph = node.getAttribute('placeholder');
                    phraseReplacements.forEach(([idPhrase, enPhrase]) => {
                        if (lang === 'en' && ph.includes(idPhrase)) {
                            ph = ph.split(idPhrase).join(enPhrase);
                        } else if (lang === 'id' && ph.includes(enPhrase)) {
                            ph = ph.split(enPhrase).join(idPhrase);
                        }
                    });
                    node.setAttribute('placeholder', ph);
                }
                node.childNodes.forEach(walkTextNodes);
            }
        }

        walkTextNodes(document.body);
    };

    const savedLang = localStorage.getItem('itpi_lang') || 'id';
    if (savedLang) {
        setAppLanguage(savedLang);
    }
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\tyoda\Downloads\cr-monitoring-updated\resources\views/layouts/app.blade.php ENDPATH**/ ?>