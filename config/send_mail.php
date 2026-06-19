<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Resolve vendor autoload — always use the document root as the anchor
// __DIR__ here is /var/www/html/config (on server) or similar
// We walk up one level to reach the project root where vendor/ lives
$possiblePaths = [
    __DIR__ . '/../vendor/autoload.php',           // config/../vendor (standard)
    dirname(__DIR__) . '/vendor/autoload.php',      // explicit one level up
    $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php', // webroot/vendor
];

$autoloadLoaded = false;
foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $autoloadLoaded = true;
        break;
    }
}

if (!$autoloadLoaded) {
    $triedPaths = implode(', ', $possiblePaths);
    error_log("PHPMailer autoload FAILED. Tried: $triedPaths");
    // Do NOT silently continue — log and define a stub so callers get a clear error
    if (!function_exists('sendEmail')) {
        function sendEmail($toEmail, $toName, $subject, $htmlBody, $altBody = '', $bcc = [], $attachments = []) {
            error_log("sendEmail() called but PHPMailer vendor/autoload.php was not found.");
            return "PHPMailer not found. Run: composer install in /var/www/html";
        }
    }
    return; // stop loading rest of file
}

require_once __DIR__ . '/mail_config.php';

/**
 * Robust email sending function using PHPMailer and Brevo SMTP.
 * 
 * @param string $toEmail   Recipient email
 * @param string $toName    Recipient name
 * @param string $subject   Email subject
 * @param string $htmlBody  HTML content of the email
 * @param string $altBody   (Optional) Plain text version
 * @param array  $bcc       (Optional) BCC recipients
 * @param array  $attachments (Optional) File attachments
 * @return bool|string      Returns true on success, or error message on failure
 */
function sendEmail($toEmail, $toName, $subject, $htmlBody, $altBody = '', $bcc = [], $attachments = [])
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        
        // Gmail SSL Options to bypass verification issues
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        if ($toEmail) {
            $mail->addAddress($toEmail, $toName);
        }

        // Add BCCs
        if (!empty($bcc)) {
            if (is_array($bcc)) {
                foreach ($bcc as $bcc_email) {
                    $mail->addBCC($bcc_email);
                }
            } else {
                $mail->addBCC($bcc);
            }
        }

        // Add Attachments
        if (!empty($attachments)) {
            if (is_array($attachments)) {
                foreach ($attachments as $file) {
                    if (is_array($file) && isset($file['path'])) {
                        $mail->addAttachment($file['path'], $file['name'] ?? '');
                    } else {
                        $mail->addAttachment($file);
                    }
                }
            } else {
                $mail->addAttachment($attachments);
            }
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;

        if (!empty($altBody)) {
            $mail->AltBody = $altBody;
        } else {
            $mail->AltBody = strip_tags($htmlBody);
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed. Error: {$mail->ErrorInfo}");
        return $mail->ErrorInfo;
    }
}
