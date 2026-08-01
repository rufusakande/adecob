Add-Type -AssemblyName System.Drawing

function Create-SquareIcon {
    param ($sourcePath, $destPath, $size)
    
    $srcImg = [System.Drawing.Image]::FromFile($sourcePath)
    
    # Create new square bitmap
    $bmp = New-Object System.Drawing.Bitmap($size, $size)
    $g = [System.Drawing.Graphics]::FromImage($bmp)
    
    # Fill background with white
    $g.Clear([System.Drawing.Color]::White)
    
    # Calculate dimensions to maintain aspect ratio
    $ratio = $srcImg.Width / $srcImg.Height
    if ($ratio -gt 1) {
        # Wider than tall
        $newWidth = $size
        $newHeight = [int]($size / $ratio)
        $x = 0
        $y = [int](($size - $newHeight) / 2)
    } else {
        # Taller than wide
        $newHeight = $size
        $newWidth = [int]($size * $ratio)
        $y = 0
        $x = [int](($size - $newWidth) / 2)
    }
    
    # Draw image centered
    $g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $g.DrawImage($srcImg, $x, $y, $newWidth, $newHeight)
    
    # Save as PNG
    $bmp.Save($destPath, [System.Drawing.Imaging.ImageFormat]::Png)
    
    $g.Dispose()
    $bmp.Dispose()
    $srcImg.Dispose()
}

Create-SquareIcon -sourcePath "public/logo.jpg" -destPath "public/icon-192x192.png" -size 192
Create-SquareIcon -sourcePath "public/logo.jpg" -destPath "public/icon-512x512.png" -size 512

Write-Host "Icons generated."
