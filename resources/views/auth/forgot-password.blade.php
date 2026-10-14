<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Forget Password — ITPI Enterprise Change Request Portal.">
    <title>Forget Password ? — ITPI Enterprise CR Portal</title>

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
            --bg-outer: #FFFFFF;
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
            background-color: #FFFFFF;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            -webkit-font-smoothing: antialiased;
            color: #2D3748;
        }

        /* ── Main Full-Screen Center Container (No Outer Canvas Background) ── */
        .auth-card-container {
            width: 100%;
            max-width: 980px;
            min-height: 560px;
            background: #FFFFFF;
            border-radius: 0;
            box-shadow: none;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            gap: 46px;
            position: relative;
        }

        /* ── Left Hero Panel (Skyscrapers) ── */
        .hero-panel {
            flex: 1.05;
            width: 100%;
            max-width: 508px;
            height: 562px;
            border-radius: 28px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 2.8rem 2.2rem 2.8rem;
            background: #153E75 url('/images/skyscrapers.jpg') no-repeat center center;
            background-size: cover;
        }

        .hero-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(20, 50, 95, 0.3) 0%, rgba(10, 30, 70, 0.45) 45%, rgba(5, 20, 55, 0.78) 100%);
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
            height: 52px;
            width: auto;
            display: block;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.15));
        }

        .hero-bottom-text {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #FFFFFF;
        }

        .hero-headline {
            font-size: 2.2rem;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.02em;
            margin-bottom: 0.6rem;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }

        .hero-subtext {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.5;
            font-weight: 500;
            max-width: 290px;
            margin: 0 auto;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.4);
        }

        /* ── Right Content Panel (Bordered Form Card) ── */
        .form-panel {
            flex: 1;
            width: 100%;
            max-width: 416px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-inner-box {
            width: 100%;
            border: 1.5px solid var(--card-border);
            border-radius: 24px;
            padding: 2.6rem 2.3rem;
            background: #FFFFFF;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .page-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--title-navy);
            margin-bottom: 1.5rem;
            text-align: center;
            letter-spacing: -0.01em;
        }

        .form-wrapper {
            width: 100%;
            max-width: 320px;
        }

        .form-group {
            margin-bottom: 1.2rem;
            text-align: left;
        }

        .field-label {
            display: block;
            font-size: 0.84rem;
            font-weight: 500;
            color: var(--label-color);
            margin-bottom: 6px;
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

        .invalid-msg {
            color: #EF4444;
            font-size: 0.78rem;
            margin-top: 5px;
            display: block;
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
            margin-top: 1.4rem;
            margin-bottom: 1.6rem;
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
            margin-bottom: 1rem;
            text-align: left;
        }

        @media (max-width: 900px) {
            body {
                padding: 1.5rem 1rem;
            }
            .auth-card-container {
                flex-direction: column;
                max-width: 440px;
                padding: 0;
                gap: 20px;
            }

            .hero-panel {
                max-width: 100%;
                height: auto;
                min-height: 250px;
                padding: 2rem 1.5rem;
                border-radius: 20px;
            }

            .hero-headline {
                font-size: 1.75rem;
            }

            .hero-subtext {
                font-size: 0.88rem;
            }

            .form-panel {
                max-width: 100%;
            }

            .form-inner-box {
                padding: 2rem 1.5rem;
                border-radius: 20px;
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

        <!-- Right: Forget Password Form in Bordered Card -->
        <div class="form-panel">
            <div class="form-inner-box">
                
                <h2 class="page-title">Forget Password ?</h2>

                <div class="form-wrapper">
                    
                    @if (session('status'))
                        <div style="background: #F0FDF4; border: 1px solid #BBF7D0; color: #16A34A; font-size: 0.82rem; border-radius: 8px; padding: 8px 12px; margin-bottom: 1rem; text-align: center;">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert-error">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('password.email') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="email" class="field-label">Email</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                class="field-input @error('email') is-invalid @enderror" 
                                required 
                                autofocus
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
