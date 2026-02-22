<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../vendor/autoload.php';

$conn = new mysqli("localhost", "root", "", "ekitabhghar");
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? '';
$subject = $data['subject'] ?? '';
$message = $data['message'] ?? '';

if (!$id || !$subject || !$message) {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
    exit();
}

// Get student info
$stmt = $conn->prepare("SELECT student_name, email_id FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Student not found']);
    exit();
}

$row = $res->fetch_assoc();
$name = $row['student_name'];
$email = $row['email_id'];

// ✅ Now define the function (after everything's ready)
function sendEmail($to, $name, $subject, $msg)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ekitabghar@gmail.com';
        $mail->Password = 'pdfxjcyzffgskypq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));

        // SSL verification bypass
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('ekitabghar@gmail.com', 'Kitabghar');
        $mail->addAddress($to, $name);
        $mail->addReplyTo('no-reply@ekitabghar.in', 'No Reply');
        $mail->isHTML(true);
        $mail->Subject = $subject;

        $allowedTags = '<p><br><b><strong><i><em><u><a><div><span>';
        $cleanMessage = strip_tags($msg, $allowedTags);

        $mail->Body = "
        <div style='max-width: 600px; margin: auto; font-family: Arial, sans-serif;'>
            <div style='background: #0d6efd; padding: 20px; color: white; text-align: center; font-size: 24px; border-radius: 10px 10px 0 0;'>
                📩 Notification From ADMIN
            </div>
            <div style='padding: 20px; background: #f9f9f9; border-radius: 0 0 10px 10px;'>
                <p style='font-size: 18px;'>Dear <strong>$name</strong>,</p>
                <p style='font-size: 16px;'>$cleanMessage</p>
                <p style='font-size: 14px; color: #777; margin-top: 20px; text-align: center;'>This is an automated email. Please do not reply.</p>
            </div>
        </div>";

        $mail->AltBody = "Dear $name,\n\n$msg\n\n(This is an automated message.)";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email failed: " . $mail->ErrorInfo);
        return false;
    }
}

// ✅ Finally send the email
$sent = sendEmail($email, $name, $subject, $message);

echo json_encode([
    'status' => $sent ? 'success' : 'error',
    'message' => $sent ? 'Email sent!' : 'Email failed to send.'
]);
?>
