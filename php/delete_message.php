<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Unauthorized access!'); window.location.href='../admin/admin_login.php';</script>";
    exit();
}

require_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message_id'])) {
    $message_id = (int)$_POST['message_id'];

    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    if ($stmt->execute([$message_id])) {
        echo "<script>alert('Message deleted successfully!'); window.location.href='../admin/admin_message.php';</script>";
    } else {
        echo "Error deleting message.";
    }
}
?>
