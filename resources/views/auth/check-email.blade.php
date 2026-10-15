<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Check your email — ITPI Enterprise Change Request Portal.">
    <title>Check your email — ITPI Enterprise CR Portal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg-canvas: #063970;
            --primary-blue: #38BDF8;
            --primary-hover: #0284C7;
            --text-navy: #1E2958;
            --text-muted: #64748B;
            --card-border: #BAE6FD;
            --link-purple: #6366F1;
            --font-main: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-canvas);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
            -webkit-font-smoothing: antialiased;
            color: #2D3748;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient White Halo Glow behind Card */
        .ambient-glow {
            position: fixed;
            width: 760px;
            height: 760px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.35) 0%, rgba(255, 255, 255, 0.12) 35%, rgba(255, 255, 255, 0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 0;
        }

        /* ── Main White Container Card ── */
        .auth-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 960px;
            min-height: 560px;
            background: #FFFFFF;
            border-radius: 36px;
            box-shadow: 0 0 50px 10px rgba(255, 255, 255, 0.45), 0 24px 60px rgba(0, 40, 90, 0.28);
            display: flex;
            align-items: stretch;
            overflow: hidden;
            padding: 22px;
            gap: 22px;
        }

        /* ── Left Hero Panel (Skyscrapers + Authentic ITPI Logo) ── */
        .hero-panel {
            flex: 1.05;
            border-radius: 26px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 2.6rem 2rem 2.6rem;
            background: #153E75 url('/images/skyscrapers.jpg') no-repeat center center;
            background-size: cover;
            min-height: 520px;
        }

        .hero-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(20, 50, 95, 0.25) 0%, rgba(10, 30, 70, 0.4) 45%, rgba(5, 20, 55, 0.76) 100%);
            pointer-events: none;
            z-index: 1;
        }

        .hero-logo-box {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding-top: 0.5rem;
        }

        .hero-logo-img {
            height: 68px;
            width: auto;
            max-width: 155px;
            display: block;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.15));
        }

        .hero-caption {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #FFFFFF;
            margin-bottom: 0.5rem;
        }

        .hero-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }

        .hero-subtitle {
            font-size: 0.92rem;
            color: #FFFFFF;
            line-height: 1.45;
            font-weight: 500;
            max-width: 270px;
            margin: 0 auto;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.4);
            opacity: 0.96;
        }

        /* ── Right Form Panel (Bordered Card) ── */
        .form-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 0.5rem;
        }

        .form-wrapper-box {
            width: 100%;
            max-width: 380px;
            border: 1.5px solid var(--card-border);
            border-radius: 22px;
            padding: 2.4rem 2rem;
            background: #FFFFFF;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            text-align: center;
        }

        .icon-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #F0F9FF;
            border: 2px solid #BAE6FD;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-blue);
            font-size: 2rem;
            margin-bottom: 1.25rem;
        }

        .auth-heading {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--text-navy);
            letter-spacing: -0.015em;
            margin-bottom: 0.75rem;
        }

        .auth-message {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 1.75rem;
        }

        .btn-sign-in {
            width: 100%;
            height: 42px;
            background: var(--primary-blue);
            border: none;
            border-radius: 8px;
            color: #FFFFFF;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.18s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(56, 189, 248, 0.35);
            margin-bottom: 1.25rem;
        }

        .btn-sign-in:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(56, 189, 248, 0.45);
        }

        .auth-footer {
            font-size: 0.78rem;
            color: #64748B;
        }

        .auth-footer a {
            color: var(--link-purple);
            font-weight: 700;
            text-decoration: none;
            margin-left: 3px;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 850px) {
            body {
                padding: 1.5rem 1rem;
            }
            .auth-container {
                flex-direction: column;
                max-width: 440px;
                padding: 14px;
                gap: 16px;
            }
            .hero-panel {
                min-height: 240px;
                padding: 1.75rem 1.5rem;
            }
            .hero-title {
                font-size: 1.75rem;
            }
            .hero-subtitle {
                font-size: 0.85rem;
            }
            .form-panel {
                padding: 0.5rem;
                width: 100%;
            }
            .form-wrapper-box {
                padding: 1.75rem 1.25rem;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="ambient-glow"></div>

    <div class="auth-container">
        <!-- SISI KIRI: Visual Skyscraper & Authentic ITPI Technology Logo -->
        <div class="hero-panel">
            <div class="hero-logo-box">
                <img src="/images/itpi_logo_tight.png" alt="ITPI TECHNOLOGY" class="hero-logo-img">
            </div>

            <div class="hero-caption">
                <h1 class="hero-title">Welcome back!</h1>
                <p class="hero-subtitle">Please enter your details to monitor your CR.</p>
            </div>
        </div>

        <!-- SISI KANAN: Check Email Message Box -->
        <div class="form-panel">
            <div class="form-wrapper-box">
                <div class="icon-circle">
                    <i class="bi bi-envelope-check"></i>
                </div>

                <h2 class="auth-heading">Check your email</h2>

                <p class="auth-message">
                    Kami telah mengirimkan instruksi dan tautan pemulihan kata sandi ke email Anda. Silakan periksa kotak masuk atau folder spam Anda.
                </p>

                <a href="{{ route('login') }}" class="btn-sign-in">
                    Kembali ke Sign In
                </a>

                <div class="auth-footer">
                    Tidak menerima email? <a href="{{ route('password.request') }}">Kirim Ulang</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
