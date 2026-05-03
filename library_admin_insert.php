<?php
session_start();
include "php/connection.php";

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("Unauthorized access!");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    $stmt = $pdo->prepare("INSERT INTO library_admin (username, password) VALUES (?, ?)");
    if ($stmt->execute([$username, $password])) {
        echo "Admin user inserted successfully!";
    } else {
        echo "Error inserting library admin.";
    }
}
?>
