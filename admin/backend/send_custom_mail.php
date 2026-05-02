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

echo json_encode(['status' => 'success', 'message' => 'Action logged successfully! (Email disabled in test mode)']);
?>