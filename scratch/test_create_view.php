<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/change-requests/create', 'GET');
$app->instance('request', $request);
$kernel->bootstrap();

$user = App\Models\User::where('role', 'client')->first() ?? App\Models\User::first();
if (!$user) {
    echo "No user found\n";
    exit(1);
}

auth()->login($user);

$response = $kernel->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
$content = $response->getContent();

$checks = [
    'figma-grid-3col' => 'CSS Grid 3-column unified layout',
    'companyDropdownBtn' => 'Nama Perusahaan Dropdown Button',
    'companyDropdownMenu' => 'Nama Perusahaan Dropdown Menu',
    'PT Maju Bersama' => 'Company options present',
    'inisial_klien' => 'Inisial Klien input',
    'nama_pic' => 'Nama PIC input',
    'cr_owner' => 'CR Owner input',
    'projectDropdownBtn' => 'Nama Project Dropdown Button',
    'projectDropdownMenu' => 'Nama Project Dropdown Menu',
    'date_input_1' => 'Tanggal picker input',
    'date_input_2' => 'Request Date picker input',
    'cr_name' => 'Nama CR input',
    'editorBody' => 'Rich text editor contenteditable',
    'deskripsi_cr_hidden' => 'Hidden textarea for sync',
    'menubar-btn' => 'TinyMCE menubar buttons',
    'paragraphDropdownBtn' => 'TinyMCE paragraph dropdown',
    'event.preventDefault()' => 'onmousedown preventDefault on toolbar buttons',
    'dropzoneBox' => 'Dokumen CR upload dropzone',
    'triggerFileInput' => 'Upload trigger click handler',
    'handleFileDrop' => 'Upload drag & drop handler',
    'priorityCardNormal' => 'Normal Priority Card',
    'priorityCardUrgent' => 'Urgent Priority Card',
    'cr_notes' => 'CR Notes textarea',
    'Kembali' => 'Kembali button',
    'Simpan Draft' => 'Simpan Draft button',
    'formnovalidate' => 'Simpan Draft formnovalidate attribute',
    'Submit CR' => 'Submit CR button',
    'customDatePickerModal' => 'Custom Date Picker Modal',
    'sourceCodeModal' => 'Source code modal',
    'previewModal' => 'Preview modal',
    'helpModal' => 'Help shortcut modal',
    'langDropdown' => 'Topbar Language dropdown',
    'inboxDropdown' => 'Topbar Inbox dropdown',
    'notifDropdown' => 'Topbar Notification dropdown',
    'userMenuDropdown' => 'Topbar User profile dropdown',
];

$allPassed = true;
foreach ($checks as $needle => $desc) {
    if (strpos($content, $needle) !== false) {
        echo " [PASS] $desc\n";
    } else {
        echo " [FAIL] $desc (needle '$needle' not found)\n";
        $allPassed = false;
    }
}

if ($allPassed) {
    echo "\n=== ALL 35 VERIFICATION CHECKS PASSED PERFECTLY! ===\n";
} else {
    echo "\n=== SOME CHECKS FAILED ===\n";
    exit(1);
}
