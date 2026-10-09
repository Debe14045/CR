<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$clients = App\Models\User::where('role', 'client')->get();
echo "Client count: " . $clients->count() . "\n";
foreach ($clients as $c) {
    $crCount = App\Models\ChangeRequest::where('user_id', $c->id)->count();
    echo "- ID: {$c->id}, Name: {$c->name}, Email: {$c->email}, CRs: {$crCount}\n";
}
$nullCrs = App\Models\ChangeRequest::whereNull('user_id')->count();
echo "CRs with null user_id: {$nullCrs}\n";
echo "Total CRs: " . App\Models\ChangeRequest::count() . "\n";
