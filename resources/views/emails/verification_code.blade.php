<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi ITPI Enterprise</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #0B1120;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #0B1120;
            padding: 40px 15px;
        }
        .container {
            max-width: 540px;
            margin: 0 auto;
            background-color: #FFFFFF;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        }
        .header {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            padding: 32px 30px;
            text-align: center;
            border-bottom: 3px solid #2563EB;
        }
        .logo-text {
            color: #FFFFFF;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin: 0;
        }
        .logo-text span {
            color: #60A5FA;
            font-weight: 400;
        }
        .badge {
            display: inline-block;
            background: rgba(37, 99, 235, 0.2);
            color: #93C5FD;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 4px 10px;
            border-radius: 20px;
            margin-top: 8px;
            border: 1px solid rgba(59, 130, 246, 0.4);
        }
        .body {
            padding: 36px 32px;
            text-align: center;
        }
        h1 {
            color: #0F172A;
            font-size: 22px;
            font-weight: 800;
            margin: 0 0 12px;
            letter-spacing: -0.02em;
        }
        p {
            color: #475569;
            font-size: 14px;
            line-height: 1.6;
            margin: 0 0 24px;
        }
        .code-container {
            background: #F8FAFC;
            border: 2px dashed #CBD5E1;
            border-radius: 14px;
            padding: 22px 15px;
            margin: 24px 0;
        }
        .otp-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 38px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #2563EB;
            margin: 0;
            user-select: all;
        }
        .expiry-text {
            font-size: 12px;
            color: #64748B;
            margin-top: 8px;
            margin-bottom: 0;
        }
        .info-box {
            background-color: #EFF6FF;
            border-left: 4px solid #3B82F6;
            border-radius: 6px;
            padding: 12px 16px;
            text-align: left;
            margin-bottom: 24px;
        }
        .info-box p {
            color: #1E40AF;
            font-size: 13px;
            margin: 0;
            line-height: 1.5;
        }
        .footer {
            background-color: #F8FAFC;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #E2E8F0;
        }
        .footer p {
            color: #94A3B8;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
        }
        .social-link {
            color: #2563EB;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <div class="logo-text">ITPI <span>Enterprise</span></div>
                <div class="badge">Keamanan Akun &bull; Portal Change Request</div>
            </div>
            
            <div class="body">
                <h1>Kode Verifikasi Anda</h1>
                <p>
                    Gunakan kode verifikasi berikut untuk melanjutkan ke portal <strong>ITPI Enterprise CR Monitoring</strong> menggunakan email <strong>{{ $email }}</strong>.
                </p>

                <div class="code-container">
                    <div class="otp-code">{{ $code }}</div>
                    <p class="expiry-text">&bull; Berlaku selama {{ $expiryMinutes }} menit &bull;</p>
                </div>

                <div class="info-box">
                    <p>
                        <strong>Tips Keamanan:</strong> Jangan berikan kode 6-digit ini kepada siapa pun, termasuk staf PT ITPI Digital Solutions.
                    </p>
                </div>

                <p style="font-size: 13px; color: #64748B; margin-bottom: 0;">
                    Jika Anda tidak meminta kode ini, silakan abaikan email ini. Akun Anda tetap aman.
                </p>
            </div>

            <div class="footer">
                <p>
                    &copy; {{ date('Y') }} <strong>PT ITPI Digital Solutions</strong>. Seluruh hak cipta dilindungi.<br>
                    Sistem Pemantauan dan Tata Kelola Change Request Berstandar Enterprise.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
