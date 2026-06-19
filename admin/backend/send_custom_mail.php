<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_id'])) { echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); exit(); }
require_once '../../config/send_mail.php';
require_once '../../php/connection.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? '';
$subject = $data['subject'] ?? '';
$message = $data['message'] ?? '';

if (!$id || !$subject || !$message) { echo json_encode(['status' => 'error', 'message' => 'Missing data']); exit(); }

$stmt = $pdo->prepare("SELECT student_name, email_id FROM students WHERE id = ?");
$stmt->execute([$id]);
if ($stmt->rowCount() === 0) { echo json_encode(['status' => 'error', 'message' => 'Student not found']); exit(); }
$student = $stmt->fetch(PDO::FETCH_ASSOC);
$email = $student['email_id'];
$name = $student['student_name'];

$htmlBody = "
<div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee; border-radius: 12px; overflow: hidden;'>
    <div style='background: #0d6efd; color: white; padding: 25px; text-align: center;'>
        <h2 style='margin:0; font-size: 22px;'>Message from Support</h2>
    </div>
    <div style='padding: 30px; line-height: 1.6; color: #333; background: #fff;'>
        <p style='font-size: 15px;'>Dear $name,</p>
        <p style='font-size: 15px;'>" . nl2br(htmlspecialchars($message)) . "</p>
        <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #888; text-align: center;'>
            This is an official communication from Kitabghar. Please do not reply directly to this email.
        </div>
    </div>
</div>";

$res = sendEmail($email, $name, $subject, $htmlBody);
if ($res === true) {
    echo json_encode(['status' => 'success', 'message' => 'Email sent successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send email: ' . $res]);
}
?>