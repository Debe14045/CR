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
            --bg-outer: #02376A;
            --title-navy: #1D2E7C;
            --text-muted: #64748B;
            --card-border: #BAE6FD;
            --link-purple: #8B5CF6;
            --font-main: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-outer);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            -webkit-font-smoothing: antialiased;
            color: #2D3748;
        }

        /* ── Main Outer White Card ── */
        .auth-card-container {
            width: 100%;
            max-width: 900px;
            min-height: 520px;
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 25px 60px -10px rgba(0, 15, 45, 0.4);
            display: flex;
            padding: 20px;
            gap: 20px;
            position: relative;
        }

        /* ── Left Hero Panel (Skyscrapers) ── */
        .hero-panel {
            flex: 1.05;
            border-radius: 16px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 2.2rem 1.8rem;
            background: #183358 url('/images/skyscrapers.jpg') no-repeat center center;
            background-size: cover;
        }

        .hero-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(20, 50, 95, 0.35) 0%, rgba(10, 30, 70, 0.45) 45%, rgba(5, 20, 55, 0.8) 100%);
            pointer-events: none;
            z-index: 1;
        }

        .hero-top-logo {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .hero-logo-img {
            height: 48px;
            width: auto;
            display: block;
        }

        .hero-bottom-text {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #FFFFFF;
        }

        .hero-headline {
            font-size: 1.85rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .hero-subtext {
            font-size: 0.92rem;
            color: rgba(255, 255, 255, 0.92);
            line-height: 1.5;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        }

        /* ── Right Content Panel (Bordered Info Card) ── */
        .form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-inner-box {
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 2.8rem 2rem;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: #FFFFFF;
        }

        .email-icon-box {
            margin-bottom: 1.4rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .email-icon-img {
            width: 120px;
            height: auto;
            display: block;
        }

        .page-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--title-navy);
            margin-bottom: 0.8rem;
            letter-spacing: -0.01em;
        }

        .desc-text {
            font-size: 0.86rem;
            color: var(--text-muted);
            line-height: 1.6;
            max-width: 320px;
            margin-bottom: 2rem;
        }

        .desc-text strong {
            color: #1E293B;
            font-weight: 600;
        }

        .bottom-links {
            text-align: center;
            font-size: 0.84rem;
            color: #64748B;
        }

        .link-signup {
            color: var(--link-purple);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .link-signup:hover {
            color: #6D28D9;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .auth-card-container {
                flex-direction: column;
                max-width: 440px;
                padding: 16px;
            }

            .hero-panel {
                min-height: 200px;
                padding: 1.5rem;
            }

            .hero-headline {
                font-size: 1.5rem;
            }

            .form-inner-box {
                padding: 2.2rem 1.5rem;
            }
        }
    </style>
</head>
<body>

    <div class="auth-card-container">
        
        <!-- Left: Skyscraper Hero Panel -->
        <div class="hero-panel">
            <div class="hero-top-logo">
                <img src="/images/itpi_logo_tight.png" alt="ITPI TECHNOLOGY" class="hero-logo-img">
            </div>

            <div class="hero-bottom-text">
                <h1 class="hero-headline">Welcome back!</h1>
                <p class="hero-subtext">Please enter your details to monitor your CR.</p>
            </div>
        </div>

        <!-- Right: Check Your Email in Bordered Card -->
        <div class="form-panel">
            <div class="form-inner-box">
                
                <div class="email-icon-box">
                    <img src="/images/icon_check_email.svg" alt="Check Email" class="email-icon-img">
                </div>

                <h2 class="page-title">Check your email</h2>

                <p class="desc-text">
                    We've sent password recovery instructions to <strong><?php echo e($email ?? session('reset_email', 'user@example.com')); ?></strong>. Please check your inbox and spam folder.
                </p>

                <div class="bottom-links">
                    Remember your password? <a href="<?php echo e(route('login')); ?>" class="link-signup">Sign Up</a>
                </div>

            </div>
        </div>

    </div>

</body>
</html>
<?php /**PATH C:\CR\resources\views/auth/check-email.blade.php ENDPATH**/ ?>