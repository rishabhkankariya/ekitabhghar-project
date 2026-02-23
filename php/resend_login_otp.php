<?php
session_start();
require_once 'connection.php';
require_once '../config/send_mail.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_email']) || !isset($_SESSION['temp_login_user_id'])) {
        echo json_encode(["success" => false, "message" => "Session expired. Please login again."]);
        exit;
    }

    // Generate new OTP
    $otp = rand(100000, 999999);
    $_SESSION['login_otp'] = $otp;
    $_SESSION['login_otp_expiry'] = time() + 300;

    $email = $_SESSION['user_email'];
    $name = $_SESSION['user_name'] ?? 'Student';

    // [TESTING MODE] Skip email, return OTP directly
    echo json_encode(["success" => true, "message" => "🔑 [TEST MODE] Your OTP is: $otp"]);
    exit;
}
?>