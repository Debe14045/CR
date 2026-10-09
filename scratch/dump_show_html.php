<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/change-requests/32?tab=status', 'GET');
$app->instance('request', $request);
$kernel->bootstrap();

$user = App\Models\User::where('role', 'client')->first();
auth()->setUser($user);

$response = $kernel->handle($request);
$html = $response->getContent();

// Embed logo as base64
$logoPath = __DIR__ . '/../public/images/itpi_logo_white_tight.png';
if (file_exists($logoPath)) {
    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    $html = preg_replace('/src="[^"]*itpi_logo_white_tight\.png"/', 'src="' . $logoBase64 . '"', $html);
}

// Fix asset paths to absolute http://127.0.0.1:8000
$html = str_replace('href="/', 'href="http://127.0.0.1:8000/', $html);
$html = str_replace('src="/', 'src="http://127.0.0.1:8000/', $html);

file_put_contents(__DIR__ . '/rendered_show_client_32.html', $html);
echo "rendered_show_client_32.html saved (" . strlen($html) . " bytes)\n";
