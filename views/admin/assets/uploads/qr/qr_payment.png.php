<?php
// Simple QR code placeholder generator
$text = "DR.SHOEZCLEAN - PAYMENT";
$size = 80;
$qr = imagecreatetruecolor($size, $size);
$white = imagecolorallocate($qr, 255, 255, 255);
$black = imagecolorallocate($qr, 0, 0, 0);
imagefill($qr, 0, 0, $white);

// Simple pattern to simulate QR code
for ($i = 0; $i < $size; $i += 8) {
    for ($j = 0; $j < $size; $j += 8) {
        if (rand(0, 1)) {
            imagefilledrectangle($qr, $i, $j, $i + 6, $j + 6, $black);
        }
    }
}

header('Content-Type: image/png');
imagepng($qr);
imagedestroy($qr);
?>