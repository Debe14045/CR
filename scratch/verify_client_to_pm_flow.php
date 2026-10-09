<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$kernel->bootstrap();

echo "========================================================\n";
echo "    VERIFIKASI ALUR LENGKAP: CLIENT KE PM TANPA ERROR   \n";
echo "========================================================\n\n";

view()->share('errors', new \Illuminate\Support\ViewErrorBag());

$errors = [];

$clientUser = App\Models\User::where('role', 'client')->first();
$pmUser = App\Models\User::where('role', 'pm')->first();
$controller = app(App\Http\Controllers\ChangeRequestController::class);

// -------------------------------------------------------------
// STEP 1: Client loads /change-requests/create
// -------------------------------------------------------------
$req1 = Illuminate\Http\Request::create('/change-requests/create', 'GET');
$app->instance('request', $req1);
\Illuminate\Support\Facades\Auth::login($clientUser);

$view1 = $controller->create();
$html1 = $view1->render();

if ($html1) {
    echo " [OK] 1. Client berhasil buka form Ajukan CR (Render Sukses)\n";
    
    // Check dropdowns in HTML
    if (strpos($html1, 'companyDropdownWrapper') !== false && strpos($html1, 'companyDropdownMenu') !== false) {
        echo "      -> Dropdown Nama Perusahaan terkonfirmasi ada di DOM\n";
    } else {
        $errors[] = "Dropdown Nama Perusahaan tidak ditemukan di halaman create!";
    }
    
    if (strpos($html1, 'projectDropdownWrapper') !== false && strpos($html1, 'projectDropdownMenu') !== false) {
        echo "      -> Dropdown Nama Project terkonfirmasi ada di DOM\n";
    } else {
        $errors[] = "Dropdown Nama Project tidak ditemukan di halaman create!";
    }
    
    // Check z-index 9999 and overflow
    if (strpos($html1, 'z-index: 9999') !== false) {
        echo "      -> Dropdown Menu z-index tinggi (9999) terkonfirmasi aman melayang\n";
    } else {
        $errors[] = "Dropdown Menu z-index 9999 tidak ditemukan!";
    }
} else {
    $errors[] = "Gagal memuat form pengajuan CR!";
}

// -------------------------------------------------------------
// STEP 2: Client submits new Change Request
// -------------------------------------------------------------
$crData = [
    'cr_name' => 'Integrasi Payment QRIS Dinamis ' . uniqid(),
    'nama_project' => 'Sistem E-commerce',
    'nama_perusahaan' => 'PT Maju Bersama',
    'inisial_klien' => 'MB',
    'nama_pic' => 'Totok Antok',
    'cr_owner' => 'Andik Vermansyah',
    'date' => '04 Sep 2026',
    'request_date' => '15 Sep 2026',
    'detail_cr' => 'Implementasi modul QRIS dinamis untuk checkout payment gateway.',
    'prioritas' => 'urgent',
    'cr_notes' => 'Harap ditinjau oleh PM secepatnya untuk persiapan staging sandbox.',
    'action' => 'submit',
];

$req2 = Illuminate\Http\Request::create('/change-requests', 'POST', $crData);
$app->instance('request', $req2);
\Illuminate\Support\Facades\Auth::login($clientUser);

$res2 = $controller->store($req2);

$createdCr = App\Models\ChangeRequest::where('judul', $crData['cr_name'])->first();

if ($createdCr) {
    echo " [OK] 2. Client berhasil submit CR baru (Kode: {$createdCr->kode_cr})\n";
    echo "      -> Status awal tersimpan: '{$createdCr->status}'\n";
    echo "      -> Klien: '{$createdCr->klien}', Proyek: '{$createdCr->proyek_terkait}'\n";
    echo "      -> Prioritas: '{$createdCr->prioritas}'\n";
} else {
    $errors[] = "Gagal menyimpan CR baru dari Client!";
}

// -------------------------------------------------------------
// STEP 3: PM sees the new CR in PM dashboard & detail
// -------------------------------------------------------------
$req3 = Illuminate\Http\Request::create('/change-requests', 'GET');
$app->instance('request', $req3);
\Illuminate\Support\Facades\Auth::login($pmUser);

$view3 = $controller->index($req3);
if ($view3) {
    echo " [OK] 3. PM berhasil mengakses daftar Semua CR\n";
} else {
    $errors[] = "PM gagal mengakses daftar CR!";
}

// PM opens Detail CR
if ($createdCr) {
    $req4 = Illuminate\Http\Request::create('/change-requests/' . $createdCr->id, 'GET');
    $app->instance('request', $req4);
    \Illuminate\Support\Facades\Auth::login($pmUser);
    
    $view4 = $controller->show($createdCr);
    $html4 = $view4->render();

    if ($html4) {
        echo " [OK] 4. PM berhasil membuka Detail CR #{$createdCr->id}\n";
        
        // Check 3 tabs for PM
        $hasTabDetail = strpos($html4, 'tabBtn-detail') !== false;
        $hasTabVerif  = strpos($html4, 'tabBtn-verifikasi') !== false;
        $hasTabStatus = strpos($html4, 'tabBtn-status') !== false;
        
        if ($hasTabDetail && $hasTabVerif && $hasTabStatus) {
            echo "      -> 3 Tab PM (Detail, Verifikasi, Status) terkonfirmasi lengkap!\n";
        } else {
            $errors[] = "Tab PM tidak lengkap di halaman detail!";
        }
    } else {
        $errors[] = "PM gagal membuka detail CR #{$createdCr->id}";
    }

    // -------------------------------------------------------------
    // STEP 4: PM verifies the CR and submits to PM Head
    // -------------------------------------------------------------
    $verifyData = [
        'mindesk_analisis' => 3,
        'mindesk_development' => 7,
        'mindesk_testing' => 2,
        'solution_paper_required' => 0,
        'catatan_analisis' => 'CR telah dianalisis teknis, estimasi 12 mandays siap diteruskan ke PM Head.',
        'action' => 'approve',
        'submit_to_pmh' => 1,
    ];

    $req5 = Illuminate\Http\Request::create('/change-requests/' . $createdCr->id, 'PUT', $verifyData);
    $app->instance('request', $req5);
    \Illuminate\Support\Facades\Auth::login($pmUser);
    
    $res5 = $controller->update($req5, $createdCr);

    $createdCr->refresh();
    if ($createdCr->status === 'awaiting_pmh') {
        echo " [OK] 5. PM berhasil verifikasi CR -> Status maju ke: '{$createdCr->status}'\n";
        echo "      -> Mandays tersimpan: Analisis={$createdCr->mindesk_analisis}, Dev={$createdCr->mindesk_development}, Test={$createdCr->mindesk_testing}\n";
    } else {
        $errors[] = "Gagal memajukan status ke awaiting_pmh, status sekarang: '{$createdCr->status}'";
    }
}

// -------------------------------------------------------------
// STEP 5: Test PM rejection flow with reason
// -------------------------------------------------------------
$crData2 = [
    'cr_name' => 'CR Penolakan Test ' . uniqid(),
    'nama_project' => 'Sistem E-commerce',
    'nama_perusahaan' => 'PT Maju Bersama',
    'inisial_klien' => 'MB',
    'nama_pic' => 'Totok Antok',
    'cr_owner' => 'Andik Vermansyah',
    'detail_cr' => 'Test penolakan oleh PM.',
    'prioritas' => 'normal',
    'action' => 'submit',
];

$req6 = Illuminate\Http\Request::create('/change-requests', 'POST', $crData2);
$app->instance('request', $req6);
\Illuminate\Support\Facades\Auth::login($clientUser);

$res6 = $controller->store($req6);
$createdCr2 = App\Models\ChangeRequest::where('judul', $crData2['cr_name'])->first();

if ($createdCr2) {
    // PM rejects CR 2
    $rejectData = [
        'action' => 'reject',
        'reject_reason' => 'Perlu revisi pada dokumen referensi karena scope belum lengkap.',
    ];
    $req7 = Illuminate\Http\Request::create('/change-requests/' . $createdCr2->id, 'PUT', $rejectData);
    $app->instance('request', $req7);
    \Illuminate\Support\Facades\Auth::login($pmUser);
    
    $res7 = $controller->update($req7, $createdCr2);

    $createdCr2->refresh();
    if ($createdCr2->status === 'revision_needed' && $createdCr2->reject_reason === $rejectData['reject_reason']) {
        echo " [OK] 6. Alur Penolakan PM berhasil -> Status: '{$createdCr2->status}'\n";
        echo "      -> Alasan penolakan tersimpan: '{$createdCr2->reject_reason}'\n";
    } else {
        $errors[] = "Gagal menjalankan alur penolakan PM! Status: {$createdCr2->status}";
    }
}

// -------------------------------------------------------------
// STEP 6: Clean up test change requests
// -------------------------------------------------------------
if ($createdCr) $createdCr->delete();
if ($createdCr2) $createdCr2->delete();
echo " [OK] 7. Data dummy pengujian dibersihkan dari database.\n\n";

if (empty($errors)) {
    echo "🎉 SEMUA ALUR DARI CLIENT KE PM BERJALAN 100% SUKSES TANPA ERROR!\n";
} else {
    echo "❌ DITEMUKAN ERROR:\n";
    foreach ($errors as $err) {
        echo "   - $err\n";
    }
}
