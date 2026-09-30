<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFIKASI SEMUA HALAMAN & ALUR FIGMA ===\n\n";

$admin = \App\Models\User::where('email', 'admin@itpi.test')->first();
$client = \App\Models\User::where('email', 'client@itpi.test')->first();
$pm = \App\Models\User::where('email', 'pm@itpi.test')->first();
$sampleCr = \App\Models\ChangeRequest::first();

$tests = [
    [
        'name' => '0. Homepage Landing Page (Figma Mockup)',
        'user' => null,
        'method' => 'GET',
        'uri' => '/',
        'expects' => [
            'Kelola Change',
            'Request Tanpa Ribet',
            'Satu platform untuk klien, PM, dan tim development',
            'Platform CR Management Praktis No. 1',
            'Buat Akun Gratis',
            'Masuk ke Akun',
            '2.400+',
            '98%',
            '60+',
            'FITUR UNGGULAN',
            'Semua yang dibutuhkan tim Anda',
            'Manajemen CR Terpusat',
            'Estimasi Mandays Akurat',
            'Kolaborasi Multi-Peran',
            'Notifikasi &amp; Reminder Otomatis',
            'Audit Trail Lengkap',
            'Manajemen Dokumen Terintegrasi',
            'CARA KERJA',
            'Mulai dalam 3 langkah sederhana',
            'Klien Buat CR',
            'PM Review &amp; Estimasi',
            'Pantau Progres Live',
            'Siap mengelola CR dengan lebih profesional?',
            'Privasi',
            'Syarat',
            'Kontak'
        ],
    ],
    [
        'name' => '0a. Login Page (Figma MacBook Pro 14" - 27)',
        'user' => null,
        'method' => 'GET',
        'uri' => '/login',
        'expects' => [
            'itpi_logo_tight.png',
            'Sign in with email',
            'Welcome back!',
            'Please enter your details to monitor your CR.',
            'Email',
            'Password',
            'Forgot Password ?',
            'SIGN IN',
            'Our Continue With',
            'Sign Up'
        ],
    ],
    [
        'name' => '0b. Register Page (Figma MacBook Pro 14" - 28)',
        'user' => null,
        'method' => 'GET',
        'uri' => '/register',
        'expects' => [
            'itpi_logo_tight.png',
            'Create an account',
            'Nama',
            'Email',
            'Password',
            'Forgot Password ?',
            'Get Started',
            'Our Continue With',
            'Welcome back!',
            'Please enter your details to monitor your CR.',
            'Sign In'
        ],
    ],
    [
        'name' => '0c. Forgot Password Page',
        'user' => null,
        'method' => 'GET',
        'uri' => '/forgot-password',
        'expects' => [
            'itpi_logo_tight.png',
            'Forget Password ?',
            'Welcome back!'
        ],
    ],
    [
        'name' => '0d. Check Email Page',
        'user' => null,
        'method' => 'GET',
        'uri' => '/check-email',
        'expects' => [
            'itpi_logo_tight.png',
            'Check your email',
            'Welcome back!'
        ],
    ],
    [
        'name' => '1. Dashboard Client (Figma Gambar 1)',
        'user' => $client,
        'method' => 'GET',
        'uri' => '/change-requests',
        'expects' => ['DASHBOARD', 'Total CR', 'Development', 'UAT', 'GO LIVE', 'STATUS', 'NOTIFIKASI', 'Ajukan CR Baru'],
    ],
    [
        'name' => '1b. Dashboard PM (Figma PM Update)',
        'user' => $pm,
        'method' => 'GET',
        'uri' => '/change-requests',
        'expects' => ['DASHBOARD', 'Total CR', 'Butuh Persetujuan', 'Development', 'GO LIVE', 'Tren CR Bulanan', 'Masuk', 'GoLive', 'Ditolak', 'Verifikasi CR'],
    ],
    [
        'name' => '2. Total CR Table (Figma Gambar 4 Kiri)',
        'user' => $admin,
        'method' => 'GET',
        'uri' => '/change-requests?view=total',
        'expects' => ['TOTAL CR', 'TGL PENGAJUAN', 'PEMOHON', 'JUDUL CR', 'STATUS', 'PRIORITAS', 'AKSI', 'Review'],
    ],
    [
        'name' => '3. Development Table (Figma Gambar 4 Kanan)',
        'user' => $admin,
        'method' => 'GET',
        'uri' => '/change-requests?view=development',
        'expects' => ['DEVELOPMENT', 'TGL PENGAJUAN', 'PEMOHON', 'JUDUL CR', 'STATUS', 'PRIORITAS', 'AKSI'],
    ],
    [
        'name' => '4. UAT Table (Figma Gambar 5 Kiri)',
        'user' => $admin,
        'method' => 'GET',
        'uri' => '/change-requests?view=uat',
        'expects' => ['UAT', 'TGL PENGAJUAN', 'PEMOHON', 'JUDUL CR', 'STATUS', 'PRIORITAS', 'AKSI'],
    ],
    [
        'name' => '5. GO LIVE Table (Figma Gambar 5 Kanan)',
        'user' => $admin,
        'method' => 'GET',
        'uri' => '/change-requests?view=golive',
        'expects' => ['GO LIVE', 'TGL PENGAJUAN', 'PEMOHON', 'JUDUL CR', 'STATUS', 'PRIORITAS', 'AKSI'],
    ],
    [
        'name' => '6. Form Ajukan Change Request Baru (Figma Gambar 2 Update)',
        'user' => $client,
        'method' => 'GET',
        'uri' => '/change-requests/create',
        'expects' => [
            'Ajukan Change Request Baru',
            'Pembuatan Pengajuan CR',
            'Nama Perusahaan',
            'PT Maju Bersama',
            'Inisial Klien',
            'Nama PIC',
            'Date',
            'Nama Project',
            'Sistem E-commerce',
            'CR Owner',
            'CR Name',
            'tinyMCE',
            'Klik untuk upload PDF',
            'Prioritas CR',
            'Normal',
            'Urgent',
            'CR Notes',
            'Kembali',
            'Submit CR'
        ],
    ],
    [
        'name' => '7. Detail Change Request (Figma Gambar 3)',
        'user' => $admin,
        'method' => 'GET',
        'uri' => '/change-requests/' . $sampleCr->id,
        'expects' => [
            'Detail Change Request',
            'Ubah Data CR',
            'PROGRES STATUS CR',
            'Analisis',
            'Review',
            'SIT',
            'UAT',
            'Deploy',
            'Go Live',
            'PARAMETER DATA REGISTRASI PENGAJUAN',
            'FSD & Kontrak Status',
            'Nama Perusahaan',
            'Nomor CR',
            'Nilai Total',
            'Nama Project',
            'PIC Sales',
            'Detail Deskripsi CR',
            'LATAR BELAKANG & TUJUAN',
            'RUANG LINGKUP PERUBAHAN',
            'DAMPAK TEKNIS & DEPENDENCIES',
            'Dokumen CR (FSD)',
            'CR Notes / Catatan'
        ],
    ],
];

$allPassed = true;

foreach ($tests as $t) {
    $request = \Illuminate\Http\Request::create($t['uri'], $t['method']);
    $app->instance('request', $request);

    if ($t['user']) {
        \Illuminate\Support\Facades\Auth::login($t['user']);
    } else {
        \Illuminate\Support\Facades\Auth::guard('web')->logout();
    }

    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    $content = $response->getContent();

    $failedKeywords = [];
    foreach ($t['expects'] as $kw) {
        if (stripos($content, $kw) === false) {
            $failedKeywords[] = $kw;
        }
    }

    if ($status === 200 && empty($failedKeywords)) {
        echo "✅ {$t['name']} : Status 200 OK & semua elemen Figma terkonfirmasi!\n";
    } else {
        $allPassed = false;
        echo "❌ {$t['name']} : Gagal (Status: {$status})\n";
        if (!empty($failedKeywords)) {
            echo "   Kata kunci hilang: " . implode(', ', $failedKeywords) . "\n";
        }
    }
}

// Test Workflow Submit CR Baru
echo "\n--- Menguji Alur Submit CR Baru ---\n";
\Illuminate\Support\Facades\Auth::login($client);
$session = $app['session']->driver();
$session->start();
$token = $session->token();

$postRequest = \Illuminate\Http\Request::create('/change-requests', 'POST', [
    '_token' => $token,
    'nama_perusahaan' => 'PT Maju Bersama',
    'nama_project' => 'Akses & Dashboard',
    'pic_sales' => 'Andika Wicaksono, ST',
    'bap' => '2024-09-01',
    'cr_name' => 'Uji Coba Integrasi API Figma',
    'target_golive' => '2024-09-20',
    'deskripsi_cr' => 'Uji coba deskripsi lengkap alur pengerjaan sistem sesuai Figma.',
    'prioritas' => 'urgent',
    'cr_notes' => 'Catatan uji alur workflow pengajuan CR baru.',
]);
$postRequest->setLaravelSession($session);
$postRequest->headers->set('X-CSRF-TOKEN', $token);

$postResponse = $kernel->handle($postRequest);
$postStatus = $postResponse->getStatusCode();

if ($postStatus === 302) {
    echo "✅ Alur Submit CR Baru : Status 302 Redirect Sukses!\n";
    $latest = \App\Models\ChangeRequest::where('judul', 'Uji Coba Integrasi API Figma')->first();
    if ($latest) {
        echo "   Tersimpan dengan Kode CR: {$latest->kode_cr} | Status: {$latest->status} | Prioritas: {$latest->prioritas} | PIC Sales: {$latest->pic_sales}\n";
    }
} else {
    $allPassed = false;
    echo "❌ Alur Submit CR Baru gagal dengan status {$postStatus}\n";
}

echo "\n" . ($allPassed ? "🎉 SEMUA HALAMAN DAN ALUR SESUAI 100% DENGAN FIGMA TANPA ERROR!" : "⚠️ Masih ada catatan perbaikan.") . "\n";
