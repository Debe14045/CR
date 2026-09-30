<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$draftRequest = Illuminate\Http\Request::create('/change-requests', 'POST', [
    'action' => 'draft',
    'nama_perusahaan' => 'PT Maju Bersama',
    'inisial_klien' => 'MB',
    'nama_pic' => 'Totok Antok',
    'cr_owner' => 'Andik Vermansyah',
    'nama_project' => 'Eprocurement PT eagle Hight plantanions TBK',
    'date' => '04 Sep 2026',
    'request_date' => '04 Sep 2026',
    'cr_name' => '', // Empty to test draft flexibility
    'detail_cr' => 'Detail draft testing',
    'prioritas' => 'normal',
    'cr_notes' => 'Catatan draft testing',
]);
$app->instance('request', $draftRequest);

$user = App\Models\User::where('role', 'client')->first() ?? App\Models\User::first();
auth()->login($user);

$controller = app(App\Http\Controllers\ChangeRequestController::class);

// Test 1: Simpan Draft with minimal/empty fields
echo "Testing Draft Submission directly via controller...\n";
$response = $controller->store($draftRequest);
echo "Draft submission status: " . $response->getStatusCode() . " (Redirect: " . $response->headers->get('Location') . ")\n";

if ($response->getStatusCode() === 302) {
    echo " [PASS] Draft submission succeeded without validation errors!\n";
} else {
    echo " [FAIL] Draft submission failed!\n";
    exit(1);
}

// Test 2: Submit CR with standard fields
echo "\nTesting Standard CR Submission directly via controller...\n";
$submitRequest = Illuminate\Http\Request::create('/change-requests', 'POST', [
    'action' => 'submit',
    'nama_perusahaan' => 'PT Maju Bersama',
    'inisial_klien' => 'MB',
    'nama_pic' => 'Totok Antok',
    'cr_owner' => 'Andik Vermansyah',
    'nama_project' => 'Eprocurement PT eagle Hight plantanions TBK',
    'date' => '04 Sep 2026',
    'request_date' => '04 Sep 2026',
    'cr_name' => 'Integrasi API Payment Gateway Test',
    'detail_cr' => '<p>Penambahan integrasi payment gateway BCA, Mandiri, dan OVO.</p>',
    'prioritas' => 'normal',
    'cr_notes' => 'Harap diselesaikan sebelum akhir kuartal.',
]);
$app->instance('request', $submitRequest);

$response2 = $controller->store($submitRequest);
echo "Submit CR status: " . $response2->getStatusCode() . " (Redirect: " . $response2->headers->get('Location') . ")\n";

if ($response2->getStatusCode() === 302) {
    echo " [PASS] Standard CR submission succeeded!\n";
} else {
    echo " [FAIL] Standard CR submission failed!\n";
    exit(1);
}

echo "\n=== ALL BACKEND SUBMISSION TESTS PASSED! ===\n";
