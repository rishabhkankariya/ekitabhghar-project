<?php
// debug_mail.php — Run this on the server to diagnose PHPMailer path issues
// DELETE THIS FILE after debugging!

echo "<pre>\n";
echo "=== PHP Mail Debug ===\n\n";

// 1. Show key paths
echo "1. __DIR__ (this file's dir): " . __DIR__ . "\n";
echo "   DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A (CLI)') . "\n\n";

// 2. Check vendor paths
$paths = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    '/var/www/html/vendor/autoload.php',
];

echo "2. Checking vendor/autoload.php paths:\n";
foreach ($paths as $p) {
    $realpath = realpath(dirname($p));
    echo "   " . $p . " => " . (file_exists($p) ? "✅ EXISTS" : "❌ NOT FOUND") . "\n";
}

echo "\n3. Directory listing of /var/www/html:\n";
foreach (scandir('/var/www/html') as $f) {
    if ($f !== '.' && $f !== '..') {
        echo "   $f\n";
    }
}

echo "\n4. Trying to load PHPMailer...\n";
$autoload = '/var/www/html/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "   ✅ PHPMailer class loaded successfully!\n";
    } else {
        echo "   ❌ autoload.php loaded but PHPMailer class NOT found\n";
    }
} else {
    echo "   ❌ vendor/autoload.php not found at $autoload\n";
    echo "   Run: cd /var/www/html && sudo composer install\n";
}

echo "\n5. Checking .env file:\n";
$envPath = '/var/www/html/.env';
if (file_exists($envPath)) {
    echo "   ✅ .env exists\n";
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, 'SMTP') !== false) {
            // Mask password
            if (strpos($line, 'SMTP_PASS') !== false) {
                echo "   SMTP_PASS=***HIDDEN***\n";
            } else {
                echo "   $line\n";
            }
        }
    }
} else {
    echo "   ❌ .env NOT found at $envPath\n";
}

echo "\n6. Environment variables (getenv):\n";
$envVars = ['SMTP_HOST', 'SMTP_PORT', 'SMTP_USER', 'SMTP_FROM_EMAIL', 'SMTP_FROM_NAME'];
foreach ($envVars as $v) {
    $val = getenv($v);
    echo "   $v = " . ($val !== false ? $val : "(not set)") . "\n";
}
$pass = getenv('SMTP_PASS');
echo "   SMTP_PASS = " . ($pass !== false ? "***SET*** (length: " . strlen($pass) . ")" : "(not set)") . "\n";

echo "\n=== End Debug ===\n";
echo "</pre>\n";
