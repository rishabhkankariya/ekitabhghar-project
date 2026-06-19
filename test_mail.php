<?php
require_once 'config/send_mail.php';

$to = 'rishabhkankariya02@gmail.com';
$subject = 'Test Email from Kitabghar';
$body = 'This is a test email to verify SMTP configuration.';

echo "Sending email to $to...\n";
$res = sendEmail($to, 'Rishabh', $subject, $body);

if ($res === true) {
    echo "SUCCESS: Email sent successfully!\n";
} else {
    echo "ERROR: Email failed to send. Error message: " . $res . "\n";
}
