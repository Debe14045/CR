<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kelola Change Request Tanpa Ribet — Satu platform untuk klien, PM, dan tim development. Lacak estimasi mandays, progress pengerjaan, dan dokumen — semua tersinkron secara real-time.">
    <title>ITPI · Kelola Change Request Tanpa Ribet</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #0284C7;
            --primary-blue-hover: #0369A1;
            --royal-navy: #001F68;
            --royal-navy-dark: #001548;
            --title-blue: #002376;
            --footer-dark: #0E172A;
            --text-dark: #0F172A;
            --text-muted: #64748B;
            --border-color: #E2E8F0;
            --bg-ice-hero: linear-gradient(180deg, #EDF4FA 0%, #F3F7FC 100%);
            --bg-cara-kerja: #EEF4FB;
            --bg-cta: linear-gradient(180deg, #01206E 0%, #001548 100%);
            --font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-sans);
            color: var(--text-dark);
            background-color: #FFFFFF;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           GLOBAL WIDE CONTAINER (Full-Screen Spacing)
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .content-container {
            width: 92%;
            max-width: 1380px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           NAVBAR (Clean & Full Width)
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .site-navbar {
            background: #FFFFFF;
            height: 72px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #EDF2F7;
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .brand-logo-img {
            height: 40px;
            width: auto;
            display: block;
        }

        .nav-center-menu {
            display: flex;
            align-items: center;
            gap: 2.4rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-link-item {
            color: #334155;
            font-size: 0.94rem;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .nav-link-item:hover {
            color: var(--primary-blue);
        }

        .nav-auth-actions {
            display: flex;
            align-items: center;
            gap: 1.6rem;
        }

        .btn-nav-login {
            color: #0284C7;
            font-size: 0.94rem;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s;
        }

        .btn-nav-login:hover {
            color: #0369A1;
        }

        .btn-nav-register {
            background: var(--primary-blue);
            color: #FFFFFF;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 9999px;
            padding: 0.58rem 1.45rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            transition: all 0.18s ease;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.22);
        }

        .btn-nav-register:hover {
            background: var(--primary-blue-hover);
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(2, 132, 199, 0.32);
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           HERO SECTION ("PAS SATU LAYAR" / 100VH ON DESKTOP)
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .hero-section {
            background: var(--bg-ice-hero);
            position: relative;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        @media (min-width: 992px) {
            .hero-section {
                min-height: calc(100vh - 72px);
                padding-top: 1.8rem;
                padding-bottom: 2rem;
            }
        }

        @media (max-width: 991px) {
            .hero-section {
                padding-top: 2.5rem;
                padding-bottom: 2.5rem;
            }
        }

        .hero-top-area {
            flex: 1;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .hero-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: #F0F9FF;
            border: 1px solid #BAE6FD;
            border-radius: 9999px;
            padding: 0.35rem 0.95rem;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: #0284C7;
            margin-bottom: 1.2rem;
        }

        .hero-badge-icon {
            font-size: 0.65rem;
            color: #0284C7;
        }

        .hero-title {
            font-size: clamp(2.4rem, 3.4vw, 3.5rem);
            font-weight: 800;
            line-height: 1.12;
            letter-spacing: -0.03em;
            margin-bottom: 1.1rem;
            color: var(--text-dark);
        }

        .hero-title .text-title-royal {
            color: var(--title-blue);
            display: block;
        }

        .hero-desc {
            font-size: clamp(0.92rem, 1.05vw, 1.02rem);
            line-height: 1.6;
            color: var(--text-muted);
            max-width: 520px;
            margin-bottom: 1.6rem;
            font-weight: 400;
        }

        .hero-btn-group {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.6rem;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            background: #0284C7;
            color: #FFFFFF;
            font-weight: 700;
            font-size: 0.94rem;
            border-radius: 9999px;
            padding: 0.75rem 1.75rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.18s ease;
            box-shadow: 0 6px 18px rgba(2, 132, 199, 0.28);
        }

        .btn-hero-primary:hover {
            background: #0369A1;
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(2, 132, 199, 0.38);
        }

        .btn-hero-secondary {
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            color: #1E293B;
            font-weight: 600;
            font-size: 0.94rem;
            border-radius: 9999px;
            padding: 0.75rem 1.75rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.18s ease;
        }

        .btn-hero-secondary:hover {
            background: #F8FAFC;
            color: #0F172A;
            border-color: #94A3B8;
        }

        .hero-checklist-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.65rem 1.8rem;
            max-width: 540px;
        }

        .hero-check-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.84rem;
            color: #475569;
            font-weight: 500;
        }

        .hero-check-icon {
            font-size: 0.95rem;
            color: #10B981;
            font-weight: 800;
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           HERO RIGHT: DASHBOARD BROWSER MOCKUP
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .dashboard-browser-card {
            background: #FFFFFF;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 45px -8px rgba(15, 23, 42, 0.1), 0 4px 12px rgba(0, 0, 0, 0.04);
            border: 1px solid #E2E8F0;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            width: 100%;
        }

        .dashboard-browser-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 55px -10px rgba(15, 23, 42, 0.12), 0 6px 16px rgba(0, 0, 0, 0.05);
        }

        .browser-topbar {
            background: #FFFFFF;
            border-bottom: 1px solid #F1F5F9;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .browser-dots {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .browser-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
        }

        .browser-search-bar {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 3px 14px;
            font-size: 0.72rem;
            color: #94A3B8;
            display: flex;
            align-items: center;
            gap: 6px;
            width: 230px;
            justify-content: center;
        }

        .browser-body {
            padding: 16px 20px;
            background: #FAFAFC;
        }

        .mockup-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .mockup-breadcrumb {
            font-size: 0.76rem;
            color: #64748B;
        }

        .mockup-breadcrumb strong {
            color: #0F172A;
            font-weight: 600;
        }

        .mockup-status-badge {
            background: #E0F2FE;
            color: #0284C7;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 11px;
            border-radius: 9999px;
            letter-spacing: 0.02em;
        }

        .mockup-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 14px;
        }

        .mockup-kpi-box {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 9px 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .mockup-kpi-lbl {
            font-size: 0.68rem;
            color: #64748B;
            margin-bottom: 2px;
        }

        .mockup-kpi-val {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0F172A;
            line-height: 1.15;
        }

        .mockup-content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .mockup-subcard {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 13px 15px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .mockup-skeleton-line {
            height: 7px;
            background: #E2E8F0;
            border-radius: 4px;
            margin-bottom: 8px;
        }

        .mockup-avatar-cluster {
            display: flex;
            align-items: center;
        }

        .mockup-avatar {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #FFFFFF;
            margin-right: -6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.55rem;
            color: #FFFFFF;
            font-weight: 700;
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           STATS BANNER (Fits at the Bottom of First Screen)
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .stats-banner-wrap {
            width: 100%;
            margin-top: 1.8rem;
        }

        .stats-banner {
            background: var(--royal-navy);
            border-radius: 18px;
            padding: 1.6rem 1.5rem;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            box-shadow: 0 16px 36px -6px rgba(0, 31, 104, 0.35);
        }

        .stats-col {
            text-align: center;
            position: relative;
            padding: 0 1rem;
        }

        .stats-col:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 15%;
            height: 70%;
            width: 1px;
            background: rgba(255, 255, 255, 0.15);
        }

        .stats-val {
            font-size: clamp(1.8rem, 2.3vw, 2.35rem);
            font-weight: 800;
            color: #FFFFFF;
            line-height: 1.1;
            margin-bottom: 0.25rem;
            letter-spacing: -0.02em;
        }

        .stats-lbl {
            font-size: 0.84rem;
            color: #CBD5E1;
            font-weight: 500;
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           SECTION: FITUR UNGGULAN (Pure White Background)
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .features-section {
            padding: 6.5rem 0 5.5rem 0;
            background: #FFFFFF;
            width: 100%;
        }

        .section-tag-blue {
            color: #0284C7;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 0.7rem;
            display: block;
        }

        .section-heading-dark {
            color: #0F172A;
            font-size: clamp(2rem, 2.6vw, 2.5rem);
            font-weight: 800;
            letter-spacing: -0.025em;
            margin-bottom: 0.8rem;
        }

        .section-sub-muted {
            color: #64748B;
            font-size: 1.02rem;
            line-height: 1.6;
            max-width: 660px;
            margin: 0 auto;
        }

        .feature-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 2.4rem 2rem;
            height: 100%;
            transition: all 0.22s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        .feature-card:hover {
            transform: translateY(-4px);
            border-color: #CBD5E1;
            box-shadow: 0 14px 28px -4px rgba(15, 23, 42, 0.06);
        }

        .feature-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.4rem;
        }

        .feature-icon-blue {
            background: #F0F9FF;
            color: #0284C7;
        }

        .feature-icon-amber {
            background: #FFFBEB;
            color: #D97706;
        }

        .feature-icon-green {
            background: #ECFDF5;
            color: #059669;
        }

        .feature-card-title {
            font-size: 1.12rem;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 0.7rem;
            letter-spacing: -0.01em;
        }

        .feature-card-desc {
            font-size: 0.9rem;
            color: #64748B;
            line-height: 1.62;
            margin: 0;
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           SECTION: CARA KERJA (Soft Ice-Blue Background)
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .how-section {
            padding: 6rem 0 6.5rem 0;
            background: var(--bg-cara-kerja);
            width: 100%;
        }

        .step-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 2.8rem 2rem;
            text-align: center;
            height: 100%;
            transition: all 0.22s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .step-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -4px rgba(15, 23, 42, 0.06);
            border-color: #CBD5E1;
        }

        .step-circle {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: #0284C7;
            color: #FFFFFF;
            font-size: 1.18rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.6rem auto;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.28);
        }

        .step-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 0.7rem;
            letter-spacing: -0.01em;
        }

        .step-desc {
            font-size: 0.9rem;
            color: #64748B;
            line-height: 1.62;
            max-width: 330px;
            margin: 0 auto;
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           SECTION: CTA (Deep Royal Sapphire Blue)
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .cta-section {
            background: var(--bg-cta);
            padding: 6rem 0 5.2rem 0;
            color: #FFFFFF;
            text-align: center;
            width: 100%;
        }

        .cta-heading {
            font-size: clamp(2.2rem, 3vw, 2.7rem);
            font-weight: 800;
            letter-spacing: -0.025em;
            margin-bottom: 1.1rem;
            color: #FFFFFF;
            line-height: 1.22;
        }

        .cta-subtitle {
            font-size: 1.05rem;
            color: #CBD5E1;
            max-width: 640px;
            margin: 0 auto 2.4rem auto;
            line-height: 1.6;
        }

        .cta-btn-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.2rem;
            margin-bottom: 1.8rem;
            flex-wrap: wrap;
        }

        .btn-cta-white {
            background: #FFFFFF;
            color: #001F68;
            font-weight: 700;
            font-size: 0.95rem;
            border-radius: 9999px;
            padding: 0.8rem 1.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.18s ease;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
        }

        .btn-cta-white:hover {
            background: #F8FAFC;
            color: #0284C7;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.28);
        }

        .btn-cta-navy {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #FFFFFF;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 9999px;
            padding: 0.8rem 1.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.18s ease;
            backdrop-filter: blur(4px);
        }

        .btn-cta-navy:hover {
            background: rgba(255, 255, 255, 0.16);
            color: #FFFFFF;
            border-color: rgba(255, 255, 255, 0.4);
        }

        .cta-subtext {
            font-size: 0.84rem;
            color: #94A3B8;
            margin: 0;
        }

        /* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
           FOOTER (Deep Dark Navy/Slate)
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
        .site-footer {
            background: var(--footer-dark);
            padding: 3rem 0 2.2rem 0;
            color: #FFFFFF;
            width: 100%;
        }

        .footer-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.5rem;
            padding-bottom: 2.2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .footer-links {
            display: flex;
            align-items: center;
            gap: 2.4rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .footer-link {
            color: #94A3B8;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .footer-link:hover {
            color: #FFFFFF;
        }

        .footer-bottom-row {
            padding-top: 1.8rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.84rem;
            color: #64748B;
        }

        @media (max-width: 991px) {
            .stats-banner {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem 0;
            }
            .stats-col:nth-child(2)::after {
                display: none;
            }
            .nav-center-menu {
                display: none;
            }
            .dashboard-browser-card {
                margin-top: 2rem;
            }
        }

        @media (max-width: 575px) {
            .content-container {
                width: 100%;
                padding: 0 1.2rem;
            }
            .stats-banner {
                grid-template-columns: 1fr;
                gap: 1.5rem 0;
            }
            .stats-col::after {
                display: none !important;
            }
            .hero-checklist-grid {
                grid-template-columns: 1fr;
            }
            .footer-top-row, .footer-bottom-row {
                flex-direction: column;
                align-items: flex-start;
            }
            .footer-links {
                gap: 1.2rem;
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>

    <!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         NAVBAR
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <nav class="site-navbar">
        <div class="content-container">
            <div class="nav-inner">
                <!-- Left: ITPI Technology Logo -->
                <a href="<?php echo e(url('/')); ?>" class="d-flex align-items-center text-decoration-none">
                    <img src="/images/itpi_logo_tight.png" alt="ITPI TECHNOLOGY" class="brand-logo-img">
                </a>

                <!-- Center Menu Links -->
                <ul class="nav-center-menu">
                    <li><a href="#fitur" class="nav-link-item">Fitur</a></li>
                    <li><a href="#cara-kerja" class="nav-link-item">Cara Kerja</a></li>
                    <li><a href="#tentang-kami" class="nav-link-item">Tentang Kami</a></li>
                    <li><a href="#faq" class="nav-link-item">FAQ</a></li>
                </ul>

                <!-- Right Actions: Masuk & Buat Akun -->
                <div class="nav-auth-actions">
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('login')); ?>" class="btn-nav-login">Masuk</a>
                        <a href="<?php echo e(route('change-requests.index')); ?>" class="btn-nav-register">
                            Dashboard <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="<?php echo e(route('logout')); ?>" class="btn-nav-login" style="color: #EF4444;" title="Keluar">Keluar</a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="btn-nav-login">Masuk</a>
                        <a href="<?php echo e(route('register')); ?>" class="btn-nav-register">
                            Buat Akun <i class="bi bi-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         HERO SECTION ("PAS SATU LAYAR" - Viewport Fit)
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <section class="hero-section">
        <div class="content-container d-flex flex-column justify-content-between flex-grow-1">
            
            <!-- Hero Upper Fold: Content & Mockup -->
            <div class="hero-top-area">
                <div class="row align-items-center g-4 w-100 m-0">
                    
                    <!-- Left Column: Copy & CTAs -->
                    <div class="col-lg-6 col-12 ps-0 pe-lg-4">
                        
                        <!-- Top Badge Pill -->
                        <div class="hero-badge-pill">
                            <i class="bi bi-diamond-fill hero-badge-icon"></i>
                            <span>Platform CR Management Praktis No. 1</span>
                        </div>

                        <!-- Main H1 Headline -->
                        <h1 class="hero-title">
                            Kelola Change<br>
                            Request<br>
                            <span class="text-title-royal">Tanpa Ribet</span>
                        </h1>

                        <!-- Paragraph description -->
                        <p class="hero-desc">
                            Satu platform untuk klien, PM, dan tim development. Lacak estimasi mandays, progress pengerjaan, dan dokumen — semua tersinkron secara real-time.
                        </p>

                        <!-- CTA Buttons -->
                        <div class="hero-btn-group">
                            <a href="<?php echo e(route('register')); ?>" class="btn-hero-primary">
                                Buat Akun Gratis <i class="bi bi-arrow-right"></i>
                            </a>
                            <a href="<?php echo e(route('login')); ?>" class="btn-hero-secondary">
                                Masuk ke Akun
                            </a>
                        </div>

                        <!-- Checklist (2x2 Grid) -->
                        <div class="hero-checklist-grid">
                            <div class="hero-check-item">
                                <i class="bi bi-check-lg hero-check-icon"></i>
                                <span>Gratis 14 hari tanpa kartu kredit</span>
                            </div>
                            <div class="hero-check-item">
                                <i class="bi bi-check-lg hero-check-icon"></i>
                                <span>Setup dalam 5 menit</span>
                            </div>
                            <div class="hero-check-item">
                                <i class="bi bi-check-lg hero-check-icon"></i>
                                <span>Support via WhatsApp &amp; email</span>
                            </div>
                            <div class="hero-check-item">
                                <i class="bi bi-check-lg hero-check-icon"></i>
                                <span>Data tersimpan aman di server Indonesia</span>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Interactive Browser Dashboard Mockup -->
                    <div class="col-lg-6 col-12 pe-0 ps-lg-4">
                        <div class="dashboard-browser-card">
                            
                            <!-- Browser Top Bar -->
                            <div class="browser-topbar">
                                <div class="browser-dots">
                                    <span class="browser-dot" style="background: #EF4444;"></span>
                                    <span class="browser-dot" style="background: #F59E0B;"></span>
                                    <span class="browser-dot" style="background: #10B981;"></span>
                                </div>
                                <div class="browser-search-bar">
                                    <i class="bi bi-search" style="font-size: 0.65rem;"></i>
                                    <span>CR Manager / Dashboard</span>
                                </div>
                                <div style="width: 28px;"></div>
                            </div>

                            <!-- Browser Inner View -->
                            <div class="browser-body">
                                
                                <!-- Header Row: Breadcrumb & Status Badge -->
                                <div class="mockup-header-row">
                                    <div class="mockup-breadcrumb">
                                        Beranda &gt; CR Saya &gt; <strong>Detail CR #104</strong>
                                    </div>
                                    <div class="mockup-status-badge">
                                        In Progress
                                    </div>
                                </div>

                                <!-- 4 KPI Boxes -->
                                <div class="mockup-kpi-grid">
                                    <div class="mockup-kpi-box">
                                        <div class="mockup-kpi-lbl">Est. Klien</div>
                                        <div class="mockup-kpi-val">15 hari</div>
                                    </div>
                                    <div class="mockup-kpi-box">
                                        <div class="mockup-kpi-lbl">Est. PM</div>
                                        <div class="mockup-kpi-val" style="color: #0284C7;">18 hari</div>
                                    </div>
                                    <div class="mockup-kpi-box">
                                        <div class="mockup-kpi-lbl">Deadline</div>
                                        <div class="mockup-kpi-val">28 Feb</div>
                                    </div>
                                    <div class="mockup-kpi-box">
                                        <div class="mockup-kpi-lbl">Progress</div>
                                        <div class="mockup-kpi-val" style="color: #10B981;">17%</div>
                                    </div>
                                </div>

                                <!-- 2 Inner Panels -->
                                <div class="mockup-content-grid">
                                    <!-- Left Panel -->
                                    <div class="mockup-subcard">
                                        <div class="mockup-skeleton-line" style="width: 85%;"></div>
                                        <div class="mockup-skeleton-line" style="width: 65%;"></div>
                                        <div class="mockup-skeleton-line" style="width: 45%; margin-bottom: 20px;"></div>
                                        <div class="d-flex align-items-center gap-2" style="font-size: 0.72rem; color: #0284C7; font-weight: 600;">
                                            <i class="bi bi-file-earmark-code"></i>
                                            <div style="height: 6px; width: 60px; background: #CBD5E1; border-radius: 3px;"></div>
                                        </div>
                                    </div>

                                    <!-- Right Panel -->
                                    <div class="mockup-subcard">
                                        <div class="mockup-skeleton-line" style="width: 75%;"></div>
                                        <div class="mockup-skeleton-line" style="width: 50%; margin-bottom: 12px;"></div>
                                        <div style="height: 5px; background: #F1F5F9; border-radius: 3px; overflow: hidden; margin-bottom: 16px;">
                                            <div style="width: 60%; height: 100%; background: #10B981; border-radius: 3px;"></div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="mockup-avatar-cluster">
                                                <div class="mockup-avatar" style="background: #0284C7;">JD</div>
                                                <div class="mockup-avatar" style="background: #64748B;">PM</div>
                                            </div>
                                            <span style="font-size: 0.68rem; color: #64748B; font-weight: 500;">3 tugas aktif</span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <!-- Stats Banner (Directly at the base of the First Screen) -->
            <div class="stats-banner-wrap">
                <div class="stats-banner">
                    <div class="stats-col">
                        <div class="stats-val">2.400+</div>
                        <div class="stats-lbl">Change Request dikelola</div>
                    </div>
                    <div class="stats-col">
                        <div class="stats-val">98%</div>
                        <div class="stats-lbl">Tingkat kepuasan klien</div>
                    </div>
                    <div class="stats-col">
                        <div class="stats-val">60+</div>
                        <div class="stats-lbl">Perusahaan aktif</div>
                    </div>
                    <div class="stats-col">
                        <div class="stats-val">3&times;</div>
                        <div class="stats-lbl">Lebih cepat dari email</div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         SECTION: FITUR UNGGULAN (Pure White Background)
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <section id="fitur" class="features-section">
        <div class="content-container">
            
            <!-- Section Header -->
            <div class="text-center mb-5">
                <span class="section-tag-blue">FITUR UNGGULAN</span>
                <h2 class="section-heading-dark">Semua yang dibutuhkan tim Anda</h2>
                <p class="section-sub-muted">
                    Dari permintaan pertama klien hingga go-live — CR Manager menemani setiap langkahnya.
                </p>
            </div>

            <!-- 6 Feature Cards Grid -->
            <div class="row g-4">
                
                <!-- Card 1: Manajemen CR Terpusat -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="feature-card">
                        <div class="feature-icon-box feature-icon-blue">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="6" height="6" rx="1.5"></rect>
                                <rect x="15" y="3" width="6" height="6" rx="1.5"></rect>
                                <rect x="15" y="15" width="6" height="6" rx="1.5"></rect>
                                <rect x="3" y="15" width="6" height="6" rx="1.5"></rect>
                                <path d="M9 6h6"></path>
                                <path d="M18 9v6"></path>
                                <path d="M15 18H9"></path>
                                <path d="M6 15V9"></path>
                            </svg>
                        </div>
                        <h3 class="feature-card-title">Manajemen CR Terpusat</h3>
                        <p class="feature-card-desc">
                            Kelola semua change request dari satu platform. Lacak status, dokumen, dan approval dalam satu tampilan yang terorganisir.
                        </p>
                    </div>
                </div>

                <!-- Card 2: Estimasi Mandays Akurat -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="feature-card">
                        <div class="feature-icon-box feature-icon-blue">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                                <polyline points="16 7 22 7 22 13"></polyline>
                            </svg>
                        </div>
                        <h3 class="feature-card-title">Estimasi Mandays Akurat</h3>
                        <p class="feature-card-desc">
                            Bandingkan estimasi klien dengan estimasi PM secara real-time. Breakdown per fase: Analisa, Development, hingga Testing.
                        </p>
                    </div>
                </div>

                <!-- Card 3: Kolaborasi Multi-Peran -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="feature-card">
                        <div class="feature-icon-box feature-icon-blue">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <h3 class="feature-card-title">Kolaborasi Multi-Peran</h3>
                        <p class="feature-card-desc">
                            Klien, PM, dan tim development bekerja dalam satu sistem dengan hak akses yang tepat untuk masing-masing peran.
                        </p>
                    </div>
                </div>

                <!-- Card 4: Notifikasi & Reminder Otomatis -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="feature-card">
                        <div class="feature-icon-box feature-icon-amber">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                        </div>
                        <h3 class="feature-card-title">Notifikasi &amp; Reminder Otomatis</h3>
                        <p class="feature-card-desc">
                            Dapatkan notifikasi real-time saat status CR berubah, dokumen di-upload, atau target date mulai mendekat.
                        </p>
                    </div>
                </div>

                <!-- Card 5: Audit Trail Lengkap -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="feature-card">
                        <div class="feature-icon-box feature-icon-green">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <polyline points="9 12 11 14 15 10"></polyline>
                            </svg>
                        </div>
                        <h3 class="feature-card-title">Audit Trail Lengkap</h3>
                        <p class="feature-card-desc">
                            Setiap perubahan tercatat rapi beserta waktu dan pelakunya. Transparansi penuh untuk semua pemangku kepentingan.
                        </p>
                    </div>
                </div>

                <!-- Card 6: Manajemen Dokumen Terintegrasi -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="feature-card">
                        <div class="feature-icon-box feature-icon-blue">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                        </div>
                        <h3 class="feature-card-title">Manajemen Dokumen Terintegrasi</h3>
                        <p class="feature-card-desc">
                            Lampirkan solution paper, dokumen CR, dan hasil QA langsung di platform. Tak perlu berpindah-pindah aplikasi.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         SECTION: CARA KERJA (Soft Ice-Blue Background)
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <section id="cara-kerja" class="how-section">
        <div class="content-container">
            
            <!-- Section Header -->
            <div class="text-center mb-5">
                <span class="section-tag-blue">CARA KERJA</span>
                <h2 class="section-heading-dark">Mulai dalam 3 langkah sederhana</h2>
            </div>

            <!-- 3 Step Cards Grid (White Cards on Ice-Blue) -->
            <div class="row g-4 justify-content-center">
                
                <!-- Step 1 -->
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="step-card">
                        <div class="step-circle">01</div>
                        <h3 class="step-title">Klien Buat CR</h3>
                        <p class="step-desc">
                            Klien mengisi form change request dengan detail kebutuhan, dokumen pendukung, dan estimasi mandays.
                        </p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="step-card">
                        <div class="step-circle">02</div>
                        <h3 class="step-title">PM Review &amp; Estimasi</h3>
                        <p class="step-desc">
                            PM menganalisa, menambahkan estimasi breakdown per fase: Analisa, Dev, Testing &amp; lampirkan solution paper.
                        </p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="step-card">
                        <div class="step-circle">03</div>
                        <h3 class="step-title">Pantau Progres Live</h3>
                        <p class="step-desc">
                            Semua pihak dapat memantau progress pengerjaan secara real-time dari analisa hingga go-live.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         SECTION: CALL TO ACTION (Deep Royal Sapphire Blue)
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <section id="tentang-kami" class="cta-section">
        <div class="content-container" style="max-width: 960px;">
            
            <h2 class="cta-heading">
                Siap mengelola CR dengan lebih profesional?
            </h2>
            
            <p class="cta-subtitle">
                Bergabung bersama 60+ perusahaan yang sudah mempercayai CR Manager untuk kelancaran proyek IT mereka.
            </p>

            <div class="cta-btn-group">
                <a href="<?php echo e(route('register')); ?>" class="btn-cta-white">
                    Buat Akun Gratis <i class="bi bi-arrow-right"></i>
                </a>
                <a href="<?php echo e(route('login')); ?>" class="btn-cta-navy">
                    Masuk ke Akun
                </a>
            </div>

            <p class="cta-subtext">
                Tidak perlu kartu kredit &middot; Gratis 14 hari &middot; Batalkan kapan saja
            </p>

        </div>
    </section>

    <!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         FOOTER (Deep Dark Navy/Slate)
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
    <footer class="site-footer">
        <div class="content-container">
            
            <!-- Top Footer Row: Logo & Navigation Links -->
            <div class="footer-top-row">
                <!-- Left: White ITPI Logo -->
                <div class="d-flex align-items-center">
                    <img src="/images/itpi_logo_white_tight.png" alt="ITPI TECHNOLOGY" style="height: 42px; width: auto;">
                </div>

                <!-- Right: Navigation Links -->
                <ul class="footer-links">
                    <li><a href="#" class="footer-link">Privasi</a></li>
                    <li><a href="#" class="footer-link">Syarat</a></li>
                    <li><a href="#" class="footer-link">Kontak</a></li>
                    <li><a href="#" class="footer-link">Dokumentasi</a></li>
                </ul>
            </div>

            <!-- Bottom Footer Row: Copyright & Slogan -->
            <div class="footer-bottom-row">
                <div>&copy; 2024 ITPI Technology. All rights reserved.</div>
                <div>Solusi Change Request Management modern untuk tim profesional</div>
            </div>

        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\Users\tyoda\Downloads\cr-monitoring-updated\resources\views/welcome.blade.php ENDPATH**/ ?>