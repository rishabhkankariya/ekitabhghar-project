<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if vendor/autoload.php exists in root
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    // Try one level up if called from elsewhere
    $autoloadPath = __DIR__ . '/../../vendor/autoload.php';
}

if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
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
