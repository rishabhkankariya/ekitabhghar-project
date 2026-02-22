<?php
// Start session to check for admin login (for security)
session_start();
header('Content-Type: application/json');

// 🔐 Verify admin session
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../vendor/autoload.php';

// 📥 Grab POST data (JSON)
$data = json_decode(file_get_contents("php://input"), true);
$to_email = $data['to_email'] ?? null;
$subject = trim($data['subject'] ?? '');
$message = trim($data['message'] ?? '');

// 🚫 Validate inputs
if (!$to_email || !$subject || !$message) {
    echo json_encode(['status' => 'error', 'message' => 'Both subject and message are required']);
    exit();
}

// 📧 Email sending logic
function sendEmail($to, $subject, $bodyHtml)
{
    $mail = new PHPMailer(true);
    try {
        // Setup SMTP and Authentication
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ekitabghar@gmail.com';
        $mail->Password = 'pdfxjcyzffgskypq';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));

        // SSL verification bypass for local environments
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('ekitabghar@gmail.com', 'Kitabghar');
        $mail->addAddress($to);
        $mail->addReplyTo('no-reply@ekitabghar.in', 'No Reply');

        $mail->isHTML(true);
        $mail->Subject = $subject;

        // Allow safe HTML tags
        $allowedTags = '<p><br><b><strong><i><em><u><a><div><span>';
        $safeMessage = strip_tags($bodyHtml, $allowedTags);

        // HTML Body
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto;'>
            <div style='background-color: #0d6efd; color: #fff; padding: 20px; border-radius: 10px 10px 0 0; text-align: center;'>
                <h2>📩 Message from Admin</h2>
            </div>
            <div style='background-color: #f9f9f9; padding: 20px; border-radius: 0 0 10px 10px;'>
                <p>$safeMessage</p>
                <p style='margin-top: 30px; font-size: 13px; color: #777; text-align: center;'>This is an automated message from E-Kitabghar. Please do not reply.</p>
            </div>
        </div>";

        $mail->AltBody = strip_tags($bodyHtml); // Fallback in case the recipient can't view HTML emails

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log any errors and return false
        error_log("Mail error: " . $mail->ErrorInfo);
        return false;
    }
}

// ✉️ Send the email
if (sendEmail($to_email, $subject, $message)) {
    echo json_encode(['status' => 'success', 'message' => 'Email sent successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send email. Please try again']);
}
?>
