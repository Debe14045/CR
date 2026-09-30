Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile('C:\Users\tyoda\.gemini\antigravity-ide\brain\4bd142e5-f2e3-4a2d-9fe1-68185a58d645\.user_uploaded\media_1790588103342.png')

for ($y = 150; $y -le 240; $y += 5) {
    # check x=200
    $c = $bmp.GetPixel(200, $y)
    if ($c.R -gt 250 -and $c.G -gt 250 -and $c.B -gt 250) {
        Write-Host "Row 2 input white at Y=$y"
    }
}
$bmp.Dispose()
