<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_id'])) { echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']); exit(); }
$message_id = isset($_POST['id']) ? $_POST['id'] : null;
if (!$message_id || !is_numeric($message_id)) { echo json_encode(['status' => 'error', 'message' => 'Invalid message ID']); exit(); }
require_once '../../php/connection.php';
$stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
if ($stmt->execute([$message_id])) {
    echo json_encode(['status' => 'success', 'message' => 'Message deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete message']);
}
?>