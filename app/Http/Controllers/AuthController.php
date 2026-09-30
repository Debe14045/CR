<?php

namespace App\Http\Controllers;

use App\Mail\VerificationCodeMail;
use App\Models\Client;
use App\Models\EmailVerification;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required_without:name', 'nullable', 'string'],
            'password' => ['required'],
            'name'     => ['nullable', 'string'],
        ]);

        $email = $request->input('email');
        if (! $email && $request->filled('name')) {
            $userByName = User::where('name', $request->input('name'))->orWhere('email', $request->input('name'))->first();
            if ($userByName) {
                $email = $userByName->email;
            }
        }

        if ($email && Auth::attempt(['email' => $email, 'password' => $request->input('password')], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('change-requests.index'));
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak valid. Silakan periksa kembali.',
        ])->onlyInput('email', 'name');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'company'  => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'      => 'Nama lengkap / PIC wajib diisi.',
            'company.required'   => 'Nama perusahaan wajib diisi.',
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email ini sudah terdaftar. Silakan masuk atau gunakan email lain.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => strtolower(trim($validated['email'])),
            'password'          => Hash::make($validated['password']),
            'role'              => User::ROLE_CLIENT,
            'email_verified_at' => Carbon::now(),
        ]);

        Client::updateOrCreate(
            ['email' => $user->email],
            [
                'name'    => $user->name,
                'company' => $validated['company'],
                'active'  => true,
            ]
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('change-requests.index')
            ->with('success', 'Selamat datang, ' . $user->name . '! Akun Client Anda berhasil dibuat.');
    }

    // ─── Real Verification Code (OTP) via Email ─────────────────────────────

    /**
     * Tampilkan halaman input kode verifikasi OTP.
     */
    public function showOtpForm(Request $request)
    {
        $email = $request->query('email', session('otp_email'));

        if (! $email) {
            return redirect()->route('login')->withErrors(['email' => 'Silakan masukkan email terlebih dahulu.']);
        }

        $remainingCooldown = EmailVerification::getRemainingCooldown($email, 'login');

        return view('auth.verify-otp', [
            'email'             => $email,
            'remainingCooldown' => $remainingCooldown,
        ]);
    }

    /**
     * Kirim kode verifikasi OTP ke email (Gmail / custom email).
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email'   => ['required', 'string', 'email', 'max:255'],
            'purpose' => ['nullable', 'string', 'in:login,register,link'],
        ], [
            'email.required' => 'Silakan masukkan alamat email / Gmail Anda.',
            'email.email'    => 'Format alamat email tidak valid.',
        ]);

        $email = strtolower(trim($request->input('email')));
        $purpose = $request->input('purpose', 'login');

        // Check rate limiting / cooldown
        if (EmailVerification::hasActiveCooldown($email, $purpose)) {
            $cooldown = EmailVerification::getRemainingCooldown($email, $purpose);
            return redirect()->route('auth.otp', ['email' => $email])
                ->with('warning', "Harap tunggu {$cooldown} detik sebelum meminta kode baru.");
        }

        // Generate OTP
        $otp = EmailVerification::createOtp($email, $purpose);
        $mailSent = false;
        $mailError = null;

        try {
            Mail::to($email)->send(new VerificationCodeMail(
                code: $otp->code,
                email: $email,
                purpose: $purpose,
                expiryMinutes: EmailVerification::CODE_LIFETIME_MINUTES
            ));
            $mailSent = true;
        } catch (Exception $e) {
            Log::warning("Gagal mengirim email verifikasi ke {$email}: " . $e->getMessage());
            $mailError = $e->getMessage();
        }

        session([
            'otp_email'   => $email,
            'otp_purpose' => $purpose,
        ]);

        $successMsg = "Kode verifikasi 6-digit telah dikirim ke {$email}. Silakan periksa kotak masuk atau folder spam Anda.";

        // In local environment, if mail driver is log or if SMTP fails, provide helper info
        if (app()->environment('local')) {
            if (! $mailSent) {
                session()->flash('local_otp_code', $otp->code);
                $successMsg = "Kode verifikasi berhasil dibuat! (Mode Dev Lokal: {$otp->code})";
            } else {
                session()->flash('local_otp_code', $otp->code);
            }
        }

        return redirect()->route('auth.otp', ['email' => $email])->with('success', $successMsg);
    }

    /**
     * Verifikasi kode OTP dan langsung login/sambungkan akun.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'code'  => ['required', 'string', 'size:6'],
        ], [
            'email.required' => 'Email tidak ditemukan.',
            'code.required'  => 'Masukkan 6-digit kode verifikasi.',
            'code.size'      => 'Kode verifikasi harus tepat 6 digit angka.',
        ]);

        $email = strtolower(trim($request->input('email')));
        $code = trim($request->input('code'));
        $purpose = session('otp_purpose', 'login');

        $verification = EmailVerification::where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $verification) {
            return back()->withErrors([
                'code' => 'Tidak ada permintaan kode aktif untuk email ini. Silakan kirim ulang kode.',
            ])->withInput();
        }

        if ($verification->isExpired()) {
            return back()->withErrors([
                'code' => 'Kode verifikasi sudah kadaluarsa (lebih dari 10 menit). Silakan minta kode baru.',
            ])->withInput();
        }

        if ($verification->attempts >= EmailVerification::MAX_ATTEMPTS) {
            return back()->withErrors([
                'code' => 'Batas percobaan verifikasi telah habis. Silakan kirim ulang kode baru demi keamanan.',
            ])->withInput();
        }

        if (! $verification->verify($code)) {
            $remainingAttempts = max(0, EmailVerification::MAX_ATTEMPTS - $verification->attempts);
            return back()->withErrors([
                'code' => "Kode verifikasi salah. Sisa kesempatan: {$remainingAttempts} kali.",
            ])->withInput();
        }

        // Cari atau buat User berdasarkan email
        $user = User::where('email', $email)->first();

        if (! $user) {
            // Pengguna baru -> daftarkan otomatis sebagai Client
            $name = ucwords(explode('@', $email)[0]);

            $user = User::create([
                'name'              => $name,
                'email'             => $email,
                'password'          => Hash::make(uniqid('otp_user_', true)),
                'role'              => User::ROLE_CLIENT,
                'email_verified_at' => Carbon::now(),
            ]);

            Client::updateOrCreate(
                ['email' => $user->email],
                [
                    'name'    => $user->name,
                    'company' => 'Perusahaan (' . $name . ')',
                    'active'  => true,
                ]
            );

            $message = "Selamat datang! Akun Client untuk {$email} berhasil dibuat dan diverifikasi.";
        } else {
            // Pengguna lama -> update status verifikasi
            if (! $user->email_verified_at) {
                $user->update(['email_verified_at' => Carbon::now()]);
            }

            $roleLabel = User::roleOptions()[$user->role] ?? $user->role;
            $message = "Selamat datang kembali, {$user->name}! Anda berhasil masuk sebagai {$roleLabel}.";
        }

        // Hapus session OTP
        $request->session()->forget(['otp_email', 'otp_purpose']);

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('change-requests.index'))->with('success', $message);
    }

    /**
     * Resend OTP code endpoint (AJAX or form).
     */
    public function resendOtp(Request $request)
    {
        return $this->sendOtp($request);
    }

    // ─── Google OAuth via Laravel Socialite ────────────────────────────────

    public function redirectToGoogle(Request $request)
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        // Jika GOOGLE_CLIENT_ID belum diisi di .env
        if (! $clientId || ! $clientSecret || $clientId === 'your-google-client-id.apps.googleusercontent.com') {
            return redirect()->route('login')
                ->withErrors(['email' => 'Kredensial Google OAuth belum dimasukkan ke file .env. Harap isi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET dari Google Cloud Console.']);
        }

        try {
            return Socialite::driver('google')
                ->scopes(['openid', 'profile', 'email'])
                ->redirect();
        } catch (Exception $e) {
            Log::error('Google OAuth Redirect Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['email' => 'Gagal mengarahkan ke Google: ' . $e->getMessage()]);
        }
    }

    public function handleGoogleCallback(Request $request)
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (! $clientId || ! $clientSecret || $clientId === 'your-google-client-id.apps.googleusercontent.com') {
            return redirect()->route('login')
                ->withErrors(['email' => 'Google Sign-In OAuth belum dikonfigurasi di file .env.']);
        }

        try {
            // Coba ambil data user secara standard
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            try {
                // Fallback stateless untuk menghindari InvalidStateException pada environment lokal
                $googleUser = Socialite::driver('google')->stateless()->user();
            } catch (Exception $e2) {
                Log::error('Google OAuth Callback Error: ' . $e2->getMessage());
                return redirect()->route('login')
                    ->withErrors(['email' => 'Autentikasi Google gagal (' . $e2->getMessage() . '). Silakan coba lagi.']);
            }
        }

        $email = strtolower(trim($googleUser->getEmail()));
        $googleId = (string) $googleUser->getId();

        // Cari user berdasarkan google_id atau email
        $user = User::where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        if (! $user) {
            // Buat akun baru sebagai Client
            $user = User::create([
                'name'              => $googleUser->getName() ?: ucwords(explode('@', $email)[0]),
                'email'             => $email,
                'google_id'         => $googleId,
                'avatar'            => $googleUser->getAvatar(),
                'password'          => Hash::make(uniqid('google_', true)),
                'role'              => User::ROLE_CLIENT,
                'email_verified_at' => Carbon::now(),
            ]);

            // Sinkronisasi ke master client
            Client::updateOrCreate(
                ['email' => $user->email],
                [
                    'name'    => $user->name,
                    'company' => $googleUser->user['hd'] ?? ($googleUser->getNickname() ?: 'Google Account (' . $user->name . ')'),
                    'active'  => true,
                ]
            );

            $message = 'Selamat datang! Akun Client Anda berhasil dibuat menggunakan Google (' . $user->email . ').';
        } else {
            // Sambungkan Google ID, avatar, dan verifikasi email jika belum tersimpan
            $user->update([
                'google_id'         => $user->google_id ?: $googleId,
                'avatar'            => $googleUser->getAvatar() ?: $user->avatar,
                'email_verified_at' => $user->email_verified_at ?: Carbon::now(),
            ]);

            $roleLabel = User::roleOptions()[$user->role] ?? $user->role;
            $message = 'Selamat datang kembali, ' . $user->name . '! Anda berhasil masuk sebagai ' . $roleLabel . ' dengan akun Google.';
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('change-requests.index'))->with('success', $message);
    }

    // ─── Figma Password Reset Flow ─────────────────────────────────────────

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ], [
            'email.required' => 'Silakan masukkan alamat email Anda.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        $email = strtolower(trim($request->input('email')));
        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Alamat email tidak terdaftar dalam sistem.'])->withInput();
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token'      => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);

        try {
            Mail::send([], [], function ($message) use ($email, $resetUrl) {
                $message->to($email)
                    ->subject('Instruksi Reset Password - ITPI Enterprise CR Portal')
                    ->html("
                        <div style='font-family: Arial, sans-serif; max-width: 540px; margin: auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px;'>
                            <h2 style='color: #1a365d;'>Reset Password Akun Anda</h2>
                            <p>Halo,</p>
                            <p>Kami menerima permintaan untuk mengatur ulang kata sandi akun ITPI Change Request Portal Anda.</p>
                            <p style='margin: 25px 0;'>
                                <a href='{$resetUrl}' style='background: #0284C7; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;'>Atur Ulang Password &rarr;</a>
                            </p>
                            <p style='color: #64748b; font-size: 13px;'>Tautan ini hanya berlaku selama 60 menit. Jika Anda tidak meminta reset password, abaikan email ini dengan aman.</p>
                        </div>
                    ");
            });
        } catch (Exception $e) {
            Log::warning("Gagal mengirim email reset password ke {$email}: " . $e->getMessage());
        }

        session([
            'reset_email' => $email,
            'reset_url'   => $resetUrl,
        ]);

        return redirect()->route('password.check-email', ['email' => $email])
            ->with('success', "Instruksi reset kata sandi telah dikirim ke {$email}.");
    }

    public function showCheckEmail(Request $request)
    {
        $email = $request->query('email', session('reset_email', 'user@example.com'));
        return view('auth.check-email', ['email' => $email]);
    }

    public function showResetPasswordForm(Request $request, $token)
    {
        $email = $request->query('email', session('reset_email', ''));
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => ['required'],
            'email'                 => ['required', 'string', 'email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        $email = strtolower(trim($request->input('email')));
        $tokenRecord = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (! $tokenRecord) {
            return back()->withErrors(['email' => 'Permintaan reset password tidak valid atau sudah kadaluarsa.'])->withInput();
        }

        if (Carbon::parse($tokenRecord->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->withErrors(['email' => 'Tautan reset password sudah kadaluarsa (melebihi 60 menit). Silakan minta tautan baru.'])->withInput();
        }

        if (! Hash::check($request->token, $tokenRecord->token) && $request->token !== $tokenRecord->token) {
            return back()->withErrors(['token' => 'Token reset password tidak valid.'])->withInput();
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'Pengguna dengan email ini tidak ditemukan.'])->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        session()->forget(['reset_email', 'reset_url']);

        return redirect()->route('login')->with('success', 'Password Anda berhasil diperbarui! Silakan masuk menggunakan password baru.');
    }

    // ───────────────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
