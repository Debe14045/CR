<?php
$imgPath = 'C:/Users/tyoda/.gemini/antigravity-ide/brain/a17c2af2-78db-4ea5-ac15-0134452976a0/.user_uploaded/media_1791361761241.png';
$file = fopen($imgPath, 'rb');
// Read PNG header
$header = fread($file, 24);
$width = unpack('N', substr($header, 16, 4))[1];
$height = unpack('N', substr($header, 20, 4))[1];
fclose($file);
echo "Image dimensions: {$width} x {$height}" . PHP_EOL;
