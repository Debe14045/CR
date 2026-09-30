<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Verifikasi Kode OTP — ITPI Enterprise CR Portal">
    <title>Verifikasi Kode — ITPI Enterprise CR Portal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue: #2563EB;
            --blue-dark: #1D4ED8;
            --blue-light: #EFF6FF;
            --green: #10B981;
            --slate-900: #0F172A;
            --slate-800: #1E293B;
            --slate-700: #334155;
            --slate-500: #64748B;
            --slate-300: #CBD5E1;
            --slate-100: #F1F5F9;
            --white: #FFFFFF;
            --font: 'Plus Jakarta Sans', 'Inter', sans-serif;
            --font-mono: 'Space Grotesk', monospace;
        }

        html, body { height: 100%; }

        body {
            min-height: 100vh;
            font-family: var(--font);
            background: #06091A;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
            -webkit-font-smoothing: antialiased;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Glow */
        body::before {
            content: '';
            position: absolute;
            top: -20%;
            left: 20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.22) 0%, transparent 70%);
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            bottom: -20%;
            right: 20%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .otp-card {
            width: 100%;
            max-width: 460px;
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            padding: 3rem 2.5rem;
            position: relative;
            z-index: 10;
            text-align: center;
        }

        .logo-icon-wrap {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, #2563EB, #4F46E5);
            color: #FFFFFF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.35);
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: -0.025em;
            margin-bottom: 0.5rem;
        }

        .card-subtitle {
            font-size: 0.9rem;
            color: var(--slate-500);
            line-height: 1.5;
            margin-bottom: 1.75rem;
        }

        .email-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #F1F5F9;
            border: 1px solid #CBD5E1;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--slate-800);
            margin-bottom: 1.5rem;
        }

        /* 6 Digit Inputs */
        .otp-inputs-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 0.5rem;
            margin-bottom: 1.75rem;
        }

        .otp-box {
            width: 100%;
            height: 60px;
            text-align: center;
            font-family: var(--font-mono);
            font-size: 1.6rem;
            font-weight: 700;
            color: #0F172A;
            background: #F8FAFC;
            border: 2px solid #E2E8F0;
            border-radius: 12px;
            outline: none;
            transition: all 0.15s ease;
        }

        .otp-box:focus {
            border-color: var(--blue);
            background: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
            transform: translateY(-2px);
        }

        .btn-verify {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: var(--font);
            cursor: pointer;
            transition: all 0.18s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.35);
            margin-bottom: 1.25rem;
        }

        .btn-verify:hover {
            background: linear-gradient(135deg, #1D4ED8, #1E40AF);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
        }

        /* Resend Section */
        .resend-box {
            font-size: 0.86rem;
            color: var(--slate-500);
            margin-top: 1rem;
            padding-top: 1.25rem;
            border-top: 1px solid #F1F5F9;
        }

        .btn-resend {
            background: none;
            border: none;
            color: var(--blue);
            font-weight: 700;
            font-size: 0.86rem;
            cursor: pointer;
            text-decoration: none;
            padding: 0;
            font-family: var(--font);
        }

        .btn-resend:hover:not(:disabled) {
            text-decoration: underline;
        }

        .btn-resend:disabled {
            color: #94A3B8;
            cursor: not-allowed;
            text-decoration: none;
        }

        /* Alerts */
        .alert-box {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.84rem;
            text-align: left;
            margin-bottom: 1.25rem;
        }

        .alert-danger-custom {
            background: #FFF1F2;
            border: 1px solid #FECDD3;
            color: #BE123C;
        }

        .alert-success-custom {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            color: #15803D;
        }

        .alert-dev-badge {
            background: #EFF6FF;
            border: 1px dashed #3B82F6;
            color: #1D4ED8;
            padding: 0.6rem 0.9rem;
            border-radius: 10px;
            font-size: 0.8rem;
            margin-bottom: 1.25rem;
            text-align: left;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.84rem;
            color: var(--slate-500);
            text-decoration: none;
            margin-top: 1.25rem;
            transition: color 0.15s ease;
        }

        .back-link:hover {
            color: var(--blue);
        }
    </style>
</head>
<body>

    <div class="otp-card">
        <div class="logo-icon-wrap">
            <i class="bi bi-shield-lock-fill"></i>
        </div>

        <h1 class="card-title">Verifikasi Kode Masuk</h1>
        <p class="card-subtitle">
            Masukkan 6-digit kode verifikasi yang telah kami kirimkan ke email Anda:
        </p>

        <div class="email-pill">
            <i class="bi bi-envelope-check-fill" style="color: var(--blue);"></i>
            <span>{{ $email }}</span>
        </div>

        @if ($errors->any())
            <div class="alert-box alert-danger-custom">
                <i class="bi bi-exclamation-circle-fill" style="font-size: 1.1rem; flex-shrink: 0;"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        @if (session('success'))
            <div class="alert-box alert-success-custom">
                <i class="bi bi-check-circle-fill" style="font-size: 1.1rem; flex-shrink: 0;"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if (app()->environment('local') && session('local_otp_code'))
            <div class="alert-dev-badge">
                <strong><i class="bi bi-code-slash"></i> Kode Dev Lokal:</strong>
                <span style="font-family: var(--font-mono); font-size: 1rem; font-weight: 800; letter-spacing: 2px; margin-left: 4px;">{{ session('local_otp_code') }}</span>
                <span style="display: block; font-size: 0.72rem; color: #64748B; margin-top: 2px;">(Otomatis tampil di mode local untuk kemudahan pengujian)</span>
            </div>
        @endif

        <form id="otpVerifyForm" method="POST" action="{{ route('auth.otp.verify') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" id="fullCodeInput" name="code" value="">

            <div class="otp-inputs-grid">
                <input type="text" class="otp-box" maxlength="1" pattern="[0-9]*" inputmode="numeric" autofocus autocomplete="one-time-code">
                <input type="text" class="otp-box" maxlength="1" pattern="[0-9]*" inputmode="numeric">
                <input type="text" class="otp-box" maxlength="1" pattern="[0-9]*" inputmode="numeric">
                <input type="text" class="otp-box" maxlength="1" pattern="[0-9]*" inputmode="numeric">
                <input type="text" class="otp-box" maxlength="1" pattern="[0-9]*" inputmode="numeric">
                <input type="text" class="otp-box" maxlength="1" pattern="[0-9]*" inputmode="numeric">
            </div>

            <button type="submit" class="btn-verify" id="btnSubmitOtp">
                <i class="bi bi-check2-circle"></i>
                Verifikasi &amp; Masuk ke Dashboard
            </button>
        </form>

        <div class="resend-box">
            <span>Belum menerima kode? </span>
            <form method="POST" action="{{ route('auth.otp.resend') }}" style="display: inline;" id="resendForm">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit" class="btn-resend" id="btnResend" @if($remainingCooldown > 0) disabled @endif>
                    Kirim Ulang Kode @if($remainingCooldown > 0)<span id="timerSpan">({{ $remainingCooldown }}s)</span>@endif
                </button>
            </form>
        </div>

        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Ganti Email / Masuk dengan Password
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const boxes = Array.from(document.querySelectorAll('.otp-box'));
            const fullCodeInput = document.getElementById('fullCodeInput');
            const form = document.getElementById('otpVerifyForm');
            const btnResend = document.getElementById('btnResend');
            const timerSpan = document.getElementById('timerSpan');

            let cooldown = {{ $remainingCooldown ?? 0 }};

            // Cooldown Countdown Timer
            if (cooldown > 0) {
                const interval = setInterval(function () {
                    cooldown--;
                    if (cooldown <= 0) {
                        clearInterval(interval);
                        btnResend.removeAttribute('disabled');
                        if (timerSpan) timerSpan.innerText = '';
                    } else {
                        if (timerSpan) timerSpan.innerText = `(${cooldown}s)`;
                    }
                }, 1000);
            }

            // Sync hidden full code
            function updateFullCode() {
                const code = boxes.map(b => b.value).join('');
                fullCodeInput.value = code;
                return code;
            }

            // Box Input Handling
            boxes.forEach((box, index) => {
                box.addEventListener('input', function (e) {
                    const val = this.value.replace(/[^0-9]/g, '');
                    this.value = val ? val[0] : '';

                    if (this.value && index < boxes.length - 1) {
                        boxes[index + 1].focus();
                    }

                    const fullCode = updateFullCode();
                    if (fullCode.length === 6) {
                        form.submit();
                    }
                });

                box.addEventListener('keydown', function (e) {
                    if (e.key === 'Backspace' && !this.value && index > 0) {
                        boxes[index - 1].focus();
                    }
                });

                box.addEventListener('paste', function (e) {
                    e.preventDefault();
                    const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
                    const digits = pasteData.replace(/[^0-9]/g, '').slice(0, 6);

                    if (digits.length > 0) {
                        digits.split('').forEach((d, idx) => {
                            if (boxes[idx]) boxes[idx].value = d;
                        });
                        const nextIdx = Math.min(digits.length, boxes.length - 1);
                        boxes[nextIdx].focus();
                        
                        const fullCode = updateFullCode();
                        if (fullCode.length === 6) {
                            form.submit();
                        }
                    }
                });
            });

            // Form Submit validation
            form.addEventListener('submit', function (e) {
                const code = updateFullCode();
                if (code.length !== 6) {
                    e.preventDefault();
                    alert('Silakan masukkan 6 digit kode verifikasi secara lengkap.');
                    boxes[0].focus();
                }
            });

            // If local OTP exists, auto-fill for frictionless development test
            @if(app()->environment('local') && session('local_otp_code'))
                const devCode = "{{ session('local_otp_code') }}";
                if (devCode.length === 6 && boxes[0].value === '') {
                    // Optional: prefill boxes
                    devCode.split('').forEach((d, i) => { if(boxes[i]) boxes[i].value = d; });
                    updateFullCode();
                }
            @endif
        });
    </script>
</body>
</html>
