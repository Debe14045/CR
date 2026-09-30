Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile('C:\Users\tyoda\.gemini\antigravity-ide\brain\4bd142e5-f2e3-4a2d-9fe1-68185a58d645\.user_uploaded\media_1790588103342.png')
$inBox = $false; $start = 0
for ($x = 155; $x -le 623; $x++) {
    $c = $bmp.GetPixel($x, 195)
    $isInput = ($c.R -gt 250 -and $c.G -gt 250 -and $c.B -gt 250)
    if ($isInput -and -not $inBox) { $inBox = $true; $start = $x }
    elseif (-not $isInput -and $inBox) {
        $inBox = $false
        $w = $x - $start
        if ($w -gt 5) {
            $pct = [Math]::Round(($w / 468) * 100, 1)
            Write-Host "Row 2 input: x=$start..$($x-1) (width=$w px, $pct%)"
        }
    }
}
$bmp.Dispose()
