<?php
session_start();
require_once 'connection.php';

// Load centralized mail helper
require_once '../config/send_mail.php';
header('Content-Type: application/json');

// Check if the user is coming from the registration process
if (!isset($_SESSION['temp_user'])) {
    echo json_encode(["success" => false, "message" => "⚠ Unauthorized access. Redirecting...", "redirect" => "../student_sign.html"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if OTP is set and not expired
    if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_expiry']) || time() > $_SESSION['otp_expiry']) {
        echo json_encode(["success" => false, "message" => "⏳ OTP expired. Please sign up again.", "redirect" => "../student_sign.html"]);
        exit;
    }

    $userOtp = trim($_POST['otp']);

    // Verify OTP
    if ($userOtp != $_SESSION['otp']) {
        echo json_encode(["success" => false, "message" => "❌ Invalid OTP."]);
        exit;
    }

    // Insert user data into database after OTP verification
    if (isset($_SESSION['temp_user'])) {
        $username = $_SESSION['temp_user']['username'];
        $email = $_SESSION['temp_user']['email'];
        $hashedPassword = $_SESSION['temp_user']['password'];

        $stmt = $conn->prepare("INSERT INTO users (username, email, password, email_verified) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);
        if ($stmt->execute()) {
            unset($_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['temp_user']);
            // [TESTING MODE] Skip verification success email
            // sendEmail($email, $username, $subject, $body);
            echo json_encode(["success" => true, "message" => "Email verified & account created!", "redirect" => "student_login.html"]);
            exit;
        } else {
            echo json_encode(["success" => false, "message" => "⚠ Database error."]);
            exit;
        }
    } else {
        echo json_encode(["success" => false, "message" => "⚠ Session expired. Please register again.", "redirect" => "student_sign.html"]);
        exit;
    }
}
?>