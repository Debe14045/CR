<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PT. ITPI Technology</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-950">

    <!-- === LAPISAN 1 (PALING BELAKANG): HANYA FOTO BACKGROUND INI YANG BLUR === -->
    <div class="absolute inset-0 z-0">
        <!-- Ganti path dengan lokasi file foto lokal Anda, misal: asset('images/bg-login.jpg') -->
        <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1920&q=80" alt="Background Kota" class="w-full h-full object-cover blur-sm scale-105">
        
        <!-- Lapisan warna biru transparan penyejuk visual -->
        <div class="absolute inset-0 bg-sky-950/60"></div>
    </div>

    <!-- === LAPISAN 2 (DEPAN): LOGO, TEKS, DAN FORM LOGIN (TETAP TAJAM & BERSIH) === -->
    <div class="relative z-10 w-full max-w-md px-6 py-8 mx-auto text-center">
        
        <!-- Logo PT Langsung (Tanpa Lingkaran) -->
        <div class="mb-6 flex justify-center">
            <!-- Ganti path dengan lokasi file logo Anda, misal: asset('images/logo-itpi.png') -->
            <img src="{{ asset('images/itpi_logo_bersih.png') }}" alt="Logo PT. ITPI Technology" class="h-60 w-auto object-contain drop-shadow-md">
        </div>

        <!-- Teks Welcome Back -->
        <h2 class="text-2xl font-bold text-white tracking-wide mb-8 drop-shadow-md">
            Welcome Back!
        </h2>

        <!-- Tampilkan Pesan Error jika Login Gagal -->
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-500/80 backdrop-blur-md text-white text-xs rounded-xl shadow-lg">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Form Login -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Input Username / Email -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <i class="fa-solid fa-user text-sm"></i>
                </span>
                <input type="text" name="email" value="{{ old('email') }}" required autofocus placeholder="Username or Email" 
                    class="w-full pl-11 pr-4 py-3.5 bg-white text-slate-800 placeholder-slate-400 text-sm font-medium rounded-full shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
            </div>

            <!-- Input Password -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <i class="fa-solid fa-lock text-sm"></i>
                </span>
                <input type="password" name="password" required placeholder="Password" 
                    class="w-full pl-11 pr-4 py-3.5 bg-white text-slate-800 placeholder-slate-400 text-sm font-medium rounded-full shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
            </div>

            <!-- Tombol Login -->
            <button type="submit" class="w-full py-3.5 px-4 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm rounded-full shadow-xl transition duration-200 border border-slate-700">
                LOGIN
            </button>
        </form>

        <!-- Menu Lupa Password -->
        <div class="mt-6 space-y-2 text-sm">
            @if (Route::has('password.request'))
                <div>
                    <a href="{{ route('password.request') }}" class="text-white/90 hover:text-white font-medium text-xs tracking-wide drop-shadow transition">
                        Forgot Username or Password?
                    </a>
                </div>
            @endif
        </div>

    </div>

</body>
</html>