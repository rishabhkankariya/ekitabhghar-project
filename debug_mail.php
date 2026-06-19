<?php
// debug_mail.php — Full stack mail debug. DELETE after use!

echo "<pre>\n";
echo "=== PHP Mail Full-Stack Debug ===\n\n";

// 1. Load env_loader (simulating what mail_config.php does)
$envLoaderPath = __DIR__ . '/php/env_loader.php';
if (file_exists($envLoaderPath)) {
    require_once $envLoaderPath;
    echo "1. env_loader.php: ✅ loaded\n";
} else {
    echo "1. env_loader.php: ❌ NOT FOUND at $envLoaderPath\n";
}

// 2. Check env vars AFTER loading
echo "\n2. SMTP env vars after env_loader:\n";
$vars = ['SMTP_HOST', 'SMTP_PORT', 'SMTP_USER', 'SMTP_FROM_EMAIL', 'SMTP_FROM_NAME'];
foreach ($vars as $v) {
    $val = getenv($v);
    echo "   $v = " . ($val !== false && $val !== '' ? $val : "❌ EMPTY/NOT SET") . "\n";
}
$pass = getenv('SMTP_PASS');
echo "   SMTP_PASS = " . ($pass !== false && $pass !== '' ? "✅ SET (length: " . strlen($pass) . ")" : "❌ EMPTY/NOT SET") . "\n";

// 3. Load mail config and send test email
echo "\n3. Loading send_mail.php and sending test email...\n";
require_once __DIR__ . '/config/send_mail.php';

$to    = 'rishabhkankariya02@gmail.com';
$name  = 'Rishabh Test';
$subj  = 'Kitabghar SMTP Debug Test - ' . date('H:i:s');
$body  = '<h2>✅ SMTP is working!</h2><p>This test was sent at ' . date('Y-m-d H:i:s') . '</p>';

$result = sendEmail($to, $name, $subj, $body);

if ($result === true) {
    echo "   ✅ Email sent successfully to $to\n";
    echo "   Check your inbox (and spam folder)\n";
} else {
    echo "   ❌ Email FAILED. Error: $result\n";
}

echo "\n=== End Debug ===\n";
echo "</pre>\n";
