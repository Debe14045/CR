<?php

$appFile = 'resources/views/layouts/app.blade.php';
$content = file_get_contents($appFile);
$contentNorm = str_replace("\r\n", "\n", $content);

// 1. Target Sidebar block
$startAside = '<aside class="app-sidebar"';
$endAside = '</aside>';
$posStartAside = strpos($contentNorm, $startAside);
$posEndAside = strpos($contentNorm, $endAside, $posStartAside);

if ($posStartAside !== false && $posEndAside !== false) {
    $fullAside = substr($contentNorm, $posStartAside, ($posEndAside + strlen($endAside)) - $posStartAside);
    $contentNorm = str_replace($fullAside, '<x-sidebar :currentRole="$currentRole" />', $contentNorm);
    echo "Replaced sidebar with <x-sidebar />\n";
} else {
    echo "Warning: sidebar block not located\n";
}

// 2. Target Topbar block
$startHeader = '<header class="main-topbar"';
$endHeader = '</header>';
$posStartHeader = strpos($contentNorm, $startHeader);
$posEndHeader = strpos($contentNorm, $endHeader, $posStartHeader);

if ($posStartHeader !== false && $posEndHeader !== false) {
    $fullHeader = substr($contentNorm, $posStartHeader, ($posEndHeader + strlen($endHeader)) - $posStartHeader);
    $contentNorm = str_replace($fullHeader, '<x-top-navbar :currentRole="$currentRole" />', $contentNorm);
    echo "Replaced topbar with <x-top-navbar />\n";
} else {
    echo "Warning: topbar block not located\n";
}

file_put_contents($appFile, $contentNorm);
echo "Successfully refactored app.blade.php layout!\n";
