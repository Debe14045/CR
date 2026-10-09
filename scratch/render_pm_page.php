<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/change-requests', 'GET');
$app->instance('request', $request);

$user = App\Models\User::where('role', 'pm')->first();
auth()->setUser($user);
$request->setUserResolver(fn() => $user);

$response = $kernel->handle($request);
$content = $response->getContent();

// Base64 embed the ITPI logo so it renders crisply in headless browser
$logoPath = __DIR__ . '/../public/images/itpi_logo_white_tight.png';
if (file_exists($logoPath)) {
    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    $content = preg_replace('/src="[^"]*itpi_logo_white_tight\.png"/', 'src="' . $logoBase64 . '"', $content);
}

// Ensure other asset URLs point to dev server
$content = str_replace('href="/', 'href="http://127.0.0.1:8000/', $content);
$content = str_replace('src="/', 'src="http://127.0.0.1:8000/', $content);

file_put_contents(__DIR__ . '/pm_dashboard.html', $content);
echo "Rendered PM Dashboard to scratch/pm_dashboard.html (" . strlen($content) . " bytes)\n";
