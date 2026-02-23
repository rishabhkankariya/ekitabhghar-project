<?php
// Start session to check for admin login (for security)
session_start();
header('Content-Type: application/json');

// 🔐 Verify admin session
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

require_once '../../config/send_mail.php';

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

// Helper to prepare HTML
function prepareHtml($bodyHtml)
{
    $allowedTags = '<p><br><b><strong><i><em><u><a><div><span>';
    $safeMessage = strip_tags($bodyHtml, $allowedTags);

    return "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto;'>
        <div style='background-color: #0d6efd; color: #fff; padding: 20px; border-radius: 10px 10px 0 0; text-align: center;'>
            <h2>📩 Message from Admin</h2>
        </div>
        <div style='background-color: #f9f9f9; padding: 20px; border-radius: 0 0 10px 10px;'>
            <p>$safeMessage</p>
            <p style='margin-top: 30px; font-size: 13px; color: #777; text-align: center;'>This is an automated message from E-Kitabghar. Please do not reply.</p>
        </div>
    </div>";
}

// [TESTING MODE] Skip email sending
// $htmlContent = prepareHtml($message);
// if (sendEmail($to_email, "Student", $subject, $htmlContent) === true) {
echo json_encode(['status' => 'success', 'message' => 'Action logged successfully! (Email disabled in test mode)']);
// }
?>