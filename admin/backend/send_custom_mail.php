<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

require_once '../../config/send_mail.php';
require_once '../../php/connection.php';

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

// Helper to prepare HTML message
function prepareHtmlMessage($name, $msg, $title)
{
    $allowedTags = '<p><br><b><strong><i><em><u><a><div><span>';
    $cleanMessage = strip_tags($msg, $allowedTags);

    return "
    <div style='max-width: 600px; margin: auto; font-family: Arial, sans-serif;'>
        <div style='background: #0d6efd; padding: 20px; color: white; text-align: center; font-size: 24px; border-radius: 10px 10px 0 0;'>
            📩 $title
        </div>
        <div style='padding: 20px; background: #f9f9f9; border-radius: 0 0 10px 10px;'>
            <p style='font-size: 18px;'>Dear <strong>$name</strong>,</p>
            <p style='font-size: 16px;'>$cleanMessage</p>
            <p style='font-size: 14px; color: #777; margin-top: 20px; text-align: center;'>This is an automated email. Please do not reply.</p>
        </div>
    </div>";
}

// [TESTING MODE] Skip sending email
// $htmlContent = prepareHtmlMessage($name, $message, "Notification From ADMIN");
// $sent = sendEmail($email, $name, $subject, $htmlContent);

echo json_encode([
    'status' => 'success',
    'message' => 'Action logged successfully! (Email disabled in test mode)'
]);
?>