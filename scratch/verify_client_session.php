<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$req = Illuminate\Http\Request::create('/change-requests/32?tab=status', 'GET');
$app->instance('request', $req);
$kernel->bootstrap();

$user = App\Models\User::where('role', 'client')->first();
auth()->setUser($user);

$res = $kernel->handle($req);
$content = $res->getContent();

echo "Status Code: " . $res->getStatusCode() . "\n";
echo "Has 'tabPanel-detail': " . (strpos($content, 'id="tabPanel-detail"') !== false ? 'YES' : 'NO') . "\n";
echo "tabPanel-detail has 'd-none': " . (strpos($content, 'id="tabPanel-detail" class="d-none"') !== false ? 'YES' : 'NO') . "\n";
echo "Has 'Fitur SSO Login Karyawan': " . (strpos($content, 'Fitur SSO Login Karyawan') !== false ? 'YES' : 'NO') . "\n";
echo "Has 'PT Delta Solusi': " . (strpos($content, 'PT Delta Solusi') !== false ? 'YES' : 'NO') . "\n";
echo "Has 'STATUS ALUR CR': " . (strpos($content, 'STATUS ALUR CR') !== false ? 'YES' : 'NO') . "\n";
echo "Has 'INFORMASI CR': " . (strpos($content, 'INFORMASI CR') !== false ? 'YES' : 'NO') . "\n";
echo "Has 'DOKUMEN': " . (strpos($content, 'DOKUMEN') !== false ? 'YES' : 'NO') . "\n";
echo "Has 'Detail Deskripsi CR': " . (strpos($content, 'Detail Deskripsi CR') !== false ? 'YES' : 'NO') . "\n";
echo "Has 'ESTIMASI MANDAYS': " . (strpos($content, 'ESTIMASI MANDAYS') !== false ? 'YES' : 'NO') . "\n";
echo "Has 'CR Notes / Catatan': " . (strpos($content, 'CR Notes / Catatan') !== false ? 'YES' : 'NO') . "\n";
echo "Has tab buttons (tabBtn-verifikasi): " . (strpos($content, 'tabBtn-verifikasi') !== false ? 'YES' : 'NO') . "\n";
