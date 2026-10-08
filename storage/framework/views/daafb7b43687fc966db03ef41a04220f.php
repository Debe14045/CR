<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sign In with email — ITPI Enterprise Change Request Portal. Masuk untuk mengelola siklus Change Request Anda.">
    <title>Sign in with email — ITPI Enterprise CR Portal</title>

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
            --primary-blue: #00A3FF;
            --primary-hover: #008FE0;
            --text-navy: #1E2958;
            --text-muted: #64748B;
            --input-bg: #F0F7FD;
            --input-border: #D5E7F7;
            --card-border: #BAE6FD;
            --link-purple: #6366F1;
            --font-main: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
        .auth-container {
            width: 100%;
            max-width: 980px;
            min-height: 560px;
            background: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            gap: 46px;
        }

        /* ── Left Hero Panel (Skyscrapers + Authentic ITPI Logo) ── */
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

        .hero-logo-box {
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

        .hero-caption {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #FFFFFF;
        }

        .hero-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.02em;
            margin-bottom: 0.6rem;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }

        .hero-subtitle {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.5;
            font-weight: 500;
            max-width: 290px;
            margin: 0 auto;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.4);
        }

        /* ── Right Form Panel (Bordered Card in Figma) ── */
        .form-panel {
            flex: 1;
            width: 100%;
            max-width: 416px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-wrapper-box {
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

        .auth-heading {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-navy);
            text-align: center;
            letter-spacing: -0.015em;
            margin-bottom: 1.6rem;
        }

        /* ── Form Inputs ── */
        .input-group-custom {
            margin-bottom: 1.15rem;
            text-align: left;
        }

        .custom-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
        }

        .custom-input {
            width: 100%;
            height: 44px;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 8px;
            padding: 0 1rem;
            font-size: 0.9rem;
            color: #1A202C;
            font-family: var(--font-main);
            outline: none;
            transition: all 0.2s ease;
        }

        .custom-input:focus {
            background: #FFFFFF;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(0, 163, 255, 0.18);
        }

        .custom-input.is-invalid {
            border-color: #EF4444;
            background: #FEF2F2;
        }

        /* Forgot password link */
        .forgot-link-wrap {
            display: flex;
            justify-content: flex-end;
            margin-top: 0.35rem;
            margin-bottom: 1.35rem;
        }

        .forgot-link {
            font-size: 0.78rem;
            color: #64748B;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.15s ease;
        }

        .forgot-link:hover {
            color: var(--primary-blue);
            text-decoration: underline;
        }

        /* ── Submit Button (SIGN IN) ── */
        .btn-sign-in {
            width: 100%;
            height: 44px;
            background: var(--primary-blue);
            border: none;
            border-radius: 10px;
            color: #FFFFFF;
            font-size: 0.92rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.18s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(0, 163, 255, 0.35);
        }

        .btn-sign-in:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(0, 163, 255, 0.45);
        }

        /* ── Divider: Our Continue With ── */
        .divider-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.85rem;
            margin: 1.35rem 0 1.2rem 0;
        }

        .divider-line {
            width: 48px;
            height: 1.5px;
            background: #CBD5E1;
        }

        .divider-text {
            font-size: 0.76rem;
            font-weight: 600;
            color: #64748B;
            white-space: nowrap;
        }

        /* ── Social Icons (Google, Apple) ── */
        .social-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1.25rem;
            margin-bottom: 1.4rem;
        }

        .btn-social {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #FFFFFF;
            border: 1.5px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            text-decoration: none;
        }

        .btn-social:hover {
            transform: translateY(-2px);
            border-color: #CBD5E1;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .btn-social svg {
            width: 19px;
            height: 19px;
        }

        /* ── Footer Link ── */
        .auth-footer {
            text-align: center;
            font-size: 0.82rem;
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

        .alert-box {
            padding: 0.65rem 0.9rem;
            border-radius: 8px;
            font-size: 0.8rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #B91C1C;
        }

        .alert-success {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            color: #15803D;
        }

        @media (max-width: 900px) {
            body {
                padding: 1.5rem 1rem;
            }
            .auth-container {
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
            .hero-title {
                font-size: 1.75rem;
            }
            .hero-subtitle {
                font-size: 0.88rem;
            }
            .form-panel {
                max-width: 100%;
            }
            .form-wrapper-box {
                padding: 2rem 1.5rem;
                border-radius: 20px;
            }
        }
    </style>
</head>
<body>

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

        <!-- SISI KANAN: Form Box Sign in with email -->
        <div class="form-panel">
            <div class="form-wrapper-box">
                <h2 class="auth-heading">Sign in with email</h2>

                
                <?php if($errors->any()): ?>
                    <div class="alert-box alert-error">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <div><?php echo e($errors->first()); ?></div>
                    </div>
                <?php endif; ?>

                <?php if(session('success')): ?>
                    <div class="alert-box alert-success">
                        <i class="bi bi-check-circle-fill"></i>
                        <div><?php echo e(session('success')); ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('login')); ?>">
                    <?php echo csrf_field(); ?>

                    <!-- Email Input -->
                    <div class="input-group-custom">
                        <label class="custom-label" for="email">Email</label>
                        <input type="email" id="email" name="email"
                               class="custom-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('email')); ?>"
                               placeholder="client@itpi.test"
                               required autofocus autocomplete="email">
                    </div>

                    <!-- Password Input -->
                    <div class="input-group-custom" style="margin-bottom: 0.25rem;">
                        <label class="custom-label" for="password">Password</label>
                        <input type="password" id="password" name="password"
                               class="custom-input <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               required autocomplete="current-password"
                               placeholder="••••••••">
                    </div>

                    <div class="forgot-link-wrap">
                        <a href="<?php echo e(route('password.request')); ?>" class="forgot-link">Forgot Password ?</a>
                    </div>

                    <!-- Submit Button SIGN IN -->
                    <button type="submit" class="btn-sign-in">
                        SIGN IN
                    </button>
                </form>

                <!-- Divider: Our Continue With -->
                <div class="divider-container">
                    <span class="divider-line"></span>
                    <span class="divider-text">Our Continue With</span>
                    <span class="divider-line"></span>
                </div>

                <!-- Social Login: Google & Apple -->
                <div class="social-buttons">
                    <!-- Google Button -->
                    <a href="<?php echo e(route('auth.google')); ?>" class="btn-social" title="Masuk dengan Akun Google">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                    </a>

                    <!-- Apple Button -->
                    <a href="javascript:void(0)" onclick="alert('Apple Sign In akan segera aktif pada pembaruan mendatang. Silakan gunakan Akun Google atau Email.')" class="btn-social" title="Masuk dengan Apple ID">
                        <svg viewBox="0 0 24 24" fill="#000000" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.38c.62-.75 1.04-1.8 0.93-2.85-.9.04-1.99.6-2.63 1.35-.57.65-1.06 1.71-.93 2.73 1 .08 2.01-.48 2.63-1.23z"/>
                        </svg>
                    </a>
                </div>

                <!-- Footer: Register Switch -->
                <div class="auth-footer">
                    Don't have an account? <a href="<?php echo e(route('register')); ?>">Sign Up</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
<?php /**PATH C:\Magang_ITPI\CR\resources\views/auth/login.blade.php ENDPATH**/ ?>