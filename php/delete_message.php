<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Unauthorized access!'); window.location.href='../admin/admin_login.php';</script>";
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "ekitabhghar";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message_id'])) {
    $message_id = $_POST['message_id'];

    $sql = "DELETE FROM messages WHERE id = '$message_id'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Message deleted successfully!'); window.location.href='../admin/admin_message.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
