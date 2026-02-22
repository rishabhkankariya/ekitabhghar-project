<?php
// Start session to check for admin login (for security)
session_start();
header('Content-Type: application/json');

// 🔐 Verify admin session
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// 📥 Retrieve message ID from request (POST)
$message_id = isset($_POST['id']) ? $_POST['id'] : null;

// 🚫 Validate message ID
if (!$message_id || !is_numeric($message_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid message ID']);
    exit();
}

// 📝 Database connection using mysqli
$conn = new mysqli("localhost", "cse", "cse", "ekitabhghar");

// 🚨 Check for connection error
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}

// 📝 Database deletion query using mysqli
$sql = "DELETE FROM contact_messages WHERE id = ?";

// Prepare the statement
$stmt = $conn->prepare($sql);

// Bind the message_id to the SQL query
$stmt->bind_param('i', $message_id);  // 'i' for integer

// Execute the query and check if the message was deleted successfully
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Message deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete message']);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
