<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\ChangeRequest;

class AuthController extends Controller
{
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();
            if ($user->pegawai && !$user->pegawai->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun pegawai Anda sedang dinonaktifkan.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            // Arahkan ke dashboard sesuai role
            return redirect()->intended(route('dashboard'))
                             ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Proses switch role khusus PMH (Project Manager Head) menjadi PM
    public function switchRole(Request $request)
    {
        $user = auth()->user();

        if ($user->canSwitchToPm()) {
            $currentActiveRole = session('active_role', 'pmh');
            
            // Toggle active role antara 'pmh' dan 'pm'
            $newRole = $currentActiveRole === 'pmh' ? 'pm' : 'pmh';
            session(['active_role' => $newRole]);

            return redirect()->back()->with('success', 'Berhasil beralih mode ke: ' . strtoupper($newRole));
        }

        return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk beralih role.');
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function profile()
    {
        $user = auth()->user();
        $clientRecord = optional($user->clientPic)->client;

        if (!$clientRecord && $user->isClient()) {
            $clientRecord = Client::first();
        }
        
        $recentCRs = [];
        if ($user->isClient() && $clientRecord) {
            $recentCRs = ChangeRequest::where('client_id', $clientRecord->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } elseif ($user->isPm()) {
            $recentCRs = ChangeRequest::where('pic_sales', $user->id)
                ->orWhere('owner_cr', $user->name)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } else {
            $recentCRs = ChangeRequest::orderBy('created_at', 'desc')->take(5)->get();
        }

        return view('client.profile', compact('user', 'clientRecord', 'recentCRs'));
    }
}