<?php
$imgPath = 'C:/Users/tyoda/.gemini/antigravity-ide/brain/4bd142e5-f2e3-4a2d-9fe1-68185a58d645/.user_uploaded/media_1790588103342.png';
$im = imagecreatefrompng($imgPath);
$w = imagesx($im);
$h = imagesy($im);

// Let's find card bounds by scanning horizontally at Y = 250
// Background outside card is white (255,255,255)
$cardLeft = 0;
$cardRight = 0;
for ($x = 0; $x < $w; $x++) {
    $rgb = imagecolorat($im, $x, 250);
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
    if ($cardLeft == 0 && ($r < 250 || $g < 250 || $b < 250)) {
        $cardLeft = $x;
    }
    if ($cardLeft > 0 && ($r < 250 || $g < 250 || $b < 250)) {
        $cardRight = $x;
    }
}

echo "Card left: $cardLeft, right: $cardRight, width: " . ($cardRight - $cardLeft) . "\n";

// Let's scan Row 1 inputs (Y = 135)
// Check where white inputs are located
$inputs = [];
$inInput = false;
$inputStart = 0;
for ($x = $cardLeft; $x <= $cardRight; $x++) {
    $rgb = imagecolorat($im, $x, 135);
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
    // Input backgrounds: either white (255,255,255) or grey (Inisial & PIC)
    // Card background is ~240-246
    $isInput = ($r > 250 && $g > 250 && $b > 250) || ($r < 220 && $g < 220 && $b < 220);
    if ($isInput && !$inInput) {
        $inInput = true;
        $inputStart = $x;
    } else if (!$isInput && $inInput) {
        $inInput = false;
        $inputs[] = [$inputStart, $x - 1, ($x - $inputStart)];
    }
}

echo "Row 1 inputs:\n";
foreach ($inputs as $idx => $inp) {
    echo "  Input $idx: left {$inp[0]}, right {$inp[1]}, width {$inp[2]}\n";
}

// Row 2 inputs (Y = 190)
$inputs2 = [];
$inInput = false;
$inputStart = 0;
for ($x = $cardLeft; $x <= $cardRight; $x++) {
    $rgb = imagecolorat($im, $x, 190);
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
    $isInput = ($r > 250 && $g > 250 && $b > 250);
    if ($isInput && !$inInput) {
        $inInput = true;
        $inputStart = $x;
    } else if (!$isInput && $inInput) {
        $inInput = false;
        $inputs2[] = [$inputStart, $x - 1, ($x - $inputStart)];
    }
}

echo "Row 2 inputs:\n";
foreach ($inputs2 as $idx => $inp) {
    echo "  Input $idx: left {$inp[0]}, right {$inp[1]}, width {$inp[2]}\n";
}

// Check Row 5 (Dokumen & Prioritas) at Y = 430
$inputs5 = [];
$inInput = false;
$inputStart = 0;
for ($x = $cardLeft; $x <= $cardRight; $x++) {
    $rgb = imagecolorat($im, $x, 430);
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
    $isInput = ($r > 250 && $g > 250 && $b > 250);
    if ($isInput && !$inInput) {
        $inInput = true;
        $inputStart = $x;
    } else if (!$isInput && $inInput) {
        $inInput = false;
        $inputs5[] = [$inputStart, $x - 1, ($x - $inputStart)];
    }
}
echo "Row 5 inputs:\n";
foreach ($inputs5 as $idx => $inp) {
    echo "  Input $idx: left {$inp[0]}, right {$inp[1]}, width {$inp[2]}\n";
}

