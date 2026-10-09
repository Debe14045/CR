<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$req = Illuminate\Http\Request::create('/change-requests/32?tab=status', 'GET');
$app->instance('request', $req);
$kernel->bootstrap();

$u = App\Models\User::where('role', 'client')->first();
auth()->setUser($u);

$res = $kernel->handle($req);
$html = $res->getContent();

echo 'Contains PROGRESS PENGERJAAN: ' . (strpos($html, 'PROGRESS PENGERJAAN') !== false ? 'YES' : 'NO') . "\n";
echo 'Contains STATUS ALUR CR: ' . (strpos($html, 'STATUS ALUR CR') !== false ? 'YES' : 'NO') . "\n";
echo 'tabPanel-detail has d-none: ' . (strpos($html, 'id="tabPanel-detail" class="d-none"') !== false ? 'YES' : 'NO') . "\n";
echo 'tabPanel-verifikasi exists: ' . (strpos($html, 'tabPanel-verifikasi') !== false ? 'YES' : 'NO') . "\n";
echo 'tabPanel-status exists: ' . (strpos($html, 'tabPanel-status') !== false ? 'YES' : 'NO') . "\n";
