<?php
session_start();
require_once __DIR__ . '/../php/connection.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("Unauthorized access!");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password using bcrypt
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // SQL query to insert the admin user with the hashed password
    $sql = "INSERT INTO admin (username, password, profile_pic) VALUES (?, ?, 'uploads/dummy.png')";

    $stmt = $pdo->prepare("INSERT INTO admin (username, password, profile_pic) VALUES (?, ?, 'uploads/dummy.png')");
    if ($stmt->execute([$username, $hashed_password])) {
        echo "Admin user added successfully!";
    } else {
        echo "Error inserting admin user.";
    }
}
?>
