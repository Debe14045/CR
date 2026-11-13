<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetOtpMail;
use Carbon\Carbon;

class PasswordController extends Controller
{
    // Tampilkan form permintaan ganti password / input OTP
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    // Tahap 1: Kirim Kode OTP ke Gmail
    public function sendOtp(Request $request)
    {
        $user = auth()->user();

        // Generate kode OTP 6 digit acak
        $otp = rand(100000, 999999);

        $user->password_otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10); // Berlaku 10 menit
        $user->save();

        // Kirim email OTP menggunakan Laravel Mail
        Mail::to($user->email)->send(new PasswordResetOtpMail($otp));

        return response()->json([
            'status' => 'success',
            'message' => 'Kode verifikasi OTP telah dikirim ke email Gmail Anda.'
        ]);
    }

    // Tahap 2: Verifikasi OTP & Simpan Password Baru
    public function verifyAndStore(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if ($user->password_otp !== $request->otp_code) {
            return back()->withErrors(['otp_code' => 'Kode verifikasi OTP salah.']);
        }

        if (Carbon::now()->gt($user->otp_expires_at)) {
            return back()->withErrors(['otp_code' => 'Kode verifikasi sudah kadaluarsa. Silakan minta ulang.']);
        }

        // Update password baru & bersihkan kolom OTP
        $user->password = Hash::make($request->new_password);
        $user->password_otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Password berhasil diubah!');
    }
}