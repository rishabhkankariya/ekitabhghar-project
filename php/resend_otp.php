<?php
session_start();
require_once 'connection.php';
require_once '../config/send_mail.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['email']) || !isset($_SESSION['temp_user'])) {
        echo json_encode(["success" => false, "message" => "⚠ Session expired. Please sign up again."]);
        exit;
    }

    // Generate new OTP
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 300;
    $email = $_SESSION['email'];

    // [TESTING MODE] Skip email, return OTP directly
    echo json_encode(["success" => true, "message" => "🔑 [TEST MODE] Your OTP is: $otp"]);
    exit;
}
?>