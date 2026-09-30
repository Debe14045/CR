Add-Type -AssemblyName System.Drawing

$bmp = [System.Drawing.Bitmap]::FromFile('C:\Users\tyoda\.gemini\antigravity-ide\brain\4bd142e5-f2e3-4a2d-9fe1-68185a58d645\.user_uploaded\media_1790588103342.png')

# Sidebar is roughly 0..152
# Let's find card bounds inside the main content (x > 155) at Y = 250
$cardLeft = 0
$cardRight = 0
for ($x = 155; $x -lt $bmp.Width; $x++) {
    $c = $bmp.GetPixel($x, 250)
    # Background outside card is white (255, 255, 255)
    # Card background is #F0F3F6 (R=240, G=243, B=246)
    $isCard = ($c.R -lt 250 -or $c.G -lt 250 -or $c.B -lt 250)
    if ($cardLeft -eq 0 -and $isCard) {
        $cardLeft = $x
    }
    if ($cardLeft -gt 0 -and $isCard) {
        $cardRight = $x
    }
}
$cardWidth = $cardRight - $cardLeft
Write-Host "Card bounds: left=$cardLeft, right=$cardRight, width=$cardWidth"

function Scan-Main-Row($y, $name) {
    Write-Host "`n$name (Y=$y):"
    $inBox = $false
    $start = 0
    for ($x = $cardLeft; $x -le $cardRight; $x++) {
        $c = $bmp.GetPixel($x, $y)
        # In Row 1 and 2, input interiors are either white (R>250,G>250,B>250) or grey (R~200..215)
        # Card background is R~240
        $isInput = ($c.R -gt 252 -and $c.G -gt 252 -and $c.B -gt 252) -or ($c.R -lt 220 -and $c.G -lt 220 -and $c.B -lt 220)
        if ($isInput -and -not $inBox) {
            $inBox = $true
            $start = $x
        } elseif (-not $isInput -and $inBox) {
            $inBox = $false
            $w = $x - $start
            if ($w -gt 5) {
                $pct = [Math]::Round(($w / $cardWidth) * 100, 1)
                $offset = [Math]::Round((($start - $cardLeft) / $cardWidth) * 100, 1)
                Write-Host "  Box: x=$start..$($x-1) (width=$w px, $pct% of card, offset=$offset%)"
            }
        }
    }
}

Scan-Main-Row 135 "Row 1 (Perusahaan, Inisial, PIC, CR Owner)"
Scan-Main-Row 190 "Row 2 (Project, Tanggal, Request Date)"
Scan-Main-Row 336 "Row 3 (Nama CR)"
Scan-Main-Row 435 "Row 5 (Dokumen & Prioritas)"
Scan-Main-Row 500 "Row 6 (CR Notes)"
Scan-Main-Row 545 "Row 7 (Buttons)"

$bmp.Dispose()
