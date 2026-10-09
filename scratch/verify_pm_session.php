<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$req = Illuminate\Http\Request::create('/change-requests/32?tab=status', 'GET');
$app->instance('request', $req);
$kernel->bootstrap();

$user = App\Models\User::where('role', 'pm')->first();
auth()->setUser($user);

$res = $kernel->handle($req);
$content = $res->getContent();

echo "=== PM VERIFICATION ===\n";
echo "Status Code: " . $res->getStatusCode() . "\n";
echo "Has 'tabBtn-verifikasi': " . (strpos($content, 'id="tabBtn-verifikasi"') !== false ? 'YES' : 'NO') . "\n";
echo "Has 'tabBtn-status': " . (strpos($content, 'id="tabBtn-status"') !== false ? 'YES' : 'NO') . "\n";
echo "Has 'tabPanel-status': " . (strpos($content, 'id="tabPanel-status"') !== false ? 'YES' : 'NO') . "\n";
echo "Has 'modalRejectCr': " . (strpos($content, 'id="modalRejectCr"') !== false ? 'YES' : 'NO') . "\n";
