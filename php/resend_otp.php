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

    $name = $_SESSION['temp_user']['username'] ?? 'Student';
    $subject = "Your Verification OTP - E-Kitabghar";
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee; border-radius: 12px; padding: 20px;'>
        <h2 style='color: #4A90E2;'>Verification</h2>
        <p>Dear $name,</p>
        <p>Your new one-time passcode (OTP) for verification is:</p>
        <div style='font-size: 24px; font-weight: bold; color: #4A90E2; padding: 10px; background: #f0f4f8; text-align: center; border-radius: 8px; margin: 20px 0;'>
            $otp
        </div>
        <p>This OTP is valid for 5 minutes.</p>
    </div>";

    $res = sendEmail($email, $name, $subject, $body);
    if ($res === true) {
        echo json_encode(["success" => true, "message" => "OTP has been sent to your registered email address."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to send email: " . $res]);
    }
    exit;
}
?>