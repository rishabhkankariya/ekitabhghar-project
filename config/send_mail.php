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
    // Define stubs so callers don't crash
    if (!function_exists('sendEmail')) {
        function sendEmail($toEmail, $toName, $subject, $htmlBody, $altBody = '', $bcc = [], $attachments = []) {
            error_log("sendEmail() called but PHPMailer vendor/autoload.php was not found.");
            return "PHPMailer not found. Run: composer install in /var/www/html";
        }
    }
    if (!function_exists('sendEmailReal')) {
        function sendEmailReal($toEmail, $toName, $subject, $htmlBody, $altBody = '', $bcc = [], $attachments = []) {
            error_log("sendEmailReal() called but PHPMailer vendor/autoload.php was not found.");
            return "PHPMailer not found. Run: composer install in /var/www/html";
        }
    }
    return; // stop loading rest of file
}

require_once __DIR__ . '/mail_config.php';

/**
 * Real synchronous SMTP email sending function using PHPMailer and Brevo SMTP.
 */
function sendEmailReal($toEmail, $toName, $subject, $htmlBody, $altBody = '', $bcc = [], $attachments = [])
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

/**
 * Asynchronous email sending wrapper that queues email in database.
 */
function sendEmail($toEmail, $toName, $subject, $htmlBody, $altBody = '', $bcc = [], $attachments = [])
{
    global $pdo;

    // Fallback if PDO is not initialized
    if (!isset($pdo)) {
        try {
            require_once dirname(__DIR__) . '/php/connection.php';
        } catch (Exception $e) {
            error_log("Failed to include connection.php in sendEmail: " . $e->getMessage());
        }
    }

    // If we still don't have $pdo, fallback to synchronous SMTP sending directly
    if (!isset($pdo)) {
        error_log("Database connection not available. Falling back to synchronous SMTP sending.");
        return sendEmailReal($toEmail, $toName, $subject, $htmlBody, $altBody, $bcc, $attachments);
    }

    try {
        // Persist attachments if any to a dedicated persistent folder
        $persistedAttachments = [];
        if (!empty($attachments)) {
            $destDir = dirname(__DIR__) . '/php/uploads/email_attachments/';
            if (!is_dir($destDir)) {
                @mkdir($destDir, 0777, true);
            }

            if (is_array($attachments)) {
                foreach ($attachments as $file) {
                    if (is_array($file) && isset($file['path'])) {
                        $origPath = $file['path'];
                        $origName = $file['name'] ?? basename($origPath);
                    } else {
                        $origPath = $file;
                        $origName = basename($origPath);
                    }

                    if (file_exists($origPath)) {
                        $ext = pathinfo($origName, PATHINFO_EXTENSION);
                        $uniqName = uniqid('att_', true) . ($ext ? '.' . $ext : '');
                        $newPath = $destDir . $uniqName;
                        if (@copy($origPath, $newPath)) {
                            $persistedAttachments[] = [
                                'path' => $newPath,
                                'name' => $origName
                            ];
                        } else {
                            $persistedAttachments[] = [
                                'path' => $origPath,
                                'name' => $origName
                            ];
                        }
                    }
                }
            } else {
                if (file_exists($attachments)) {
                    $origName = basename($attachments);
                    $ext = pathinfo($origName, PATHINFO_EXTENSION);
                    $uniqName = uniqid('att_', true) . ($ext ? '.' . $ext : '');
                    $newPath = $destDir . $uniqName;
                    if (@copy($attachments, $newPath)) {
                        $persistedAttachments[] = [
                            'path' => $newPath,
                            'name' => $origName
                        ];
                    } else {
                        $persistedAttachments[] = [
                            'path' => $attachments,
                            'name' => $origName
                        ];
                    }
                }
            }
        }

        $bccJson = json_encode(is_array($bcc) ? $bcc : ($bcc ? [$bcc] : []));
        $attachmentsJson = json_encode($persistedAttachments);

        // Insert into database queue
        $stmt = $pdo->prepare("INSERT INTO email_queue (to_email, to_name, subject, body, alt_body, bcc, attachments) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$toEmail, $toName, $subject, $htmlBody, $altBody, $bccJson, $attachmentsJson]);

        // Spawn cron_email_sender.php in background asynchronously
        $senderScript = __DIR__ . '/cron_email_sender.php';
        $cmd = 'php ' . escapeshellarg($senderScript);
        if (substr(php_uname(), 0, 7) == "Windows") {
            pclose(popen("start /B " . $cmd, "r"));
        } else {
            pclose(popen($cmd . " > /dev/null 2>&1 &", "r"));
        }

        return true;
    } catch (Exception $dbEx) {
        error_log("Failed to queue email to database: " . $dbEx->getMessage() . ". Falling back to synchronous SMTP sending.");
        return sendEmailReal($toEmail, $toName, $subject, $htmlBody, $altBody, $bcc, $attachments);
    }
}
?>
