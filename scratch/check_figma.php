<?php
$imgPath = 'C:/Users/tyoda/.gemini/antigravity-ide/brain/4bd142e5-f2e3-4a2d-9fe1-68185a58d645/.user_uploaded/media_1790588103342.png';
$size = getimagesize($imgPath);
echo "Image Dimensions: " . $size[0] . "x" . $size[1] . "\n";
