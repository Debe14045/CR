<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Reset Password — ITPI Enterprise Change Request Portal.">
    <title>Reset Password — ITPI Enterprise CR Portal</title>

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
            --btn-blue: #38BDF8;
            --btn-blue-hover: #0284C7;
            --title-navy: #1D2E7C;
            --label-color: #475569;
            --input-bg: #F0F9FF;
            --input-border: #BAE6FD;
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

        /* ── Right Content Panel (Bordered Form Card) ── */
        .form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-inner-box {
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 2.2rem 2.2rem;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #FFFFFF;
        }

        .padlock-icon-box {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .padlock-icon-img {
            width: 90px;
            height: auto;
            display: block;
        }

        .form-wrapper {
            width: 100%;
            max-width: 320px;
        }

        .form-group {
            margin-bottom: 1rem;
            text-align: left;
        }

        .field-label {
            display: block;
            font-size: 0.84rem;
            font-weight: 500;
            color: var(--label-color);
            margin-bottom: 5px;
        }

        .field-input {
            width: 100%;
            height: 42px;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 8px;
            padding: 0 14px;
            font-size: 0.9rem;
            color: #1E293B;
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
        }

        .field-input:focus {
            border-color: #38BDF8;
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
        }

        .field-input.is-invalid {
            border-color: #EF4444;
            background: #FEF2F2;
        }

        .btn-submit {
            width: 100%;
            height: 42px;
            background: var(--btn-blue);
            color: #FFFFFF;
            border: none;
            border-radius: 9999px;
            font-size: 0.92rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(56, 189, 248, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 1.2rem;
            margin-bottom: 1.4rem;
            text-decoration: none;
        }

        .btn-submit:hover {
            background: var(--btn-blue-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(56, 189, 248, 0.45);
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

        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
            font-size: 0.82rem;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 0.8rem;
            text-align: left;
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
                padding: 2rem 1.5rem;
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

        <!-- Right: Reset Password Form in Bordered Card -->
        <div class="form-panel">
            <div class="form-inner-box">
                
                <div class="padlock-icon-box">
                    <img src="/images/icon_padlock_reset.svg" alt="Reset Password" class="padlock-icon-img">
                </div>

                <div class="form-wrapper">
                    
                    @if ($errors->any())
                        <div class="alert-error">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ old('email', $email) }}">

                        <div class="form-group">
                            <label for="password" class="field-label">New Password</label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="field-input @error('password') is-invalid @enderror" 
                                required 
                                autofocus
                            >
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="field-label">Confirm New Password</label>
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                class="field-input @error('password_confirmation') is-invalid @enderror" 
                                required
                            >
                        </div>

                        <button type="submit" class="btn-submit">
                            Send Reset Link
                        </button>
                    </form>

                    <div class="bottom-links">
                        Remember your password? <a href="{{ route('login') }}" class="link-signup">Sign Up</a>
                    </div>

                </div>

            </div>
        </div>

    </div>

</body>
</html>
