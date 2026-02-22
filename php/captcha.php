<?php
session_start();

// Generate a random string for CAPTCHA
function generateCaptchaText($length = 6) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
    return substr(str_shuffle($chars), 0, $length);
}

$captchaText = generateCaptchaText();
$_SESSION['captcha'] = $captchaText;
$_SESSION['captcha_time'] = time();

// Create an image
$width = 180;
$height = 60;
$image = imagecreatetruecolor($width, $height);

// Set colors
$background = imagecolorallocate($image, 240, 240, 240);
$lineColor = imagecolorallocate($image, 100, 100, 100);
$textColor = imagecolorallocate($image, 0, 0, 0);

// Fill background
imagefilledrectangle($image, 0, 0, $width, $height, $background);

// Add noise lines
for ($i = 0; $i < 8; $i++) {
    imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
}

// Load font (ensure you have this font in the `fonts` directory)
$fontPath = __DIR__ . "/fonts/arial.ttf"; 
if (!file_exists($fontPath)) {
    die("Font file missing!");
}

// Add CAPTCHA text with distortion
$fontSize = 25;
$x = 20;
$y = 45;
for ($i = 0; $i < strlen($captchaText); $i++) {
    $angle = rand(-20, 20);
    imagettftext($image, $fontSize, $angle, $x, $y, $textColor, $fontPath, $captchaText[$i]);
    $x += 25;
}

// Send image headers
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
?>
