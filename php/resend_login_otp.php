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

    $subject = 'Resent Login Verification Code';
    $body = "
            <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 10px; background-color: #ffffff;'>
                <div style='text-align: center; border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; margin-bottom: 20px;'>
                     <h2 style='color: #4F46E5; margin: 0;'>Login Verification</h2>
                     <p style='color: #666; font-size: 14px; margin-top: 5px;'>Resend Request</p>
                </div>
                <div style='text-align: center;'>
                    <p style='font-size: 16px; color: #333;'>Hello <strong>$name</strong>,</p>
                    <p style='color: #555; line-height: 1.5;'>Here is your new verification code:</p>
                    <div style='background-color: #f3f4f6; color: #1F2937; letter-spacing: 5px; font-weight: bold; font-size: 24px; padding: 15px; margin: 20px 0; border-radius: 8px; display: inline-block;'>
                        $otp
                    </div>
                    <p style='font-size: 12px; color: #999;'>This code is valid for 5 minutes.</p>
                </div>
            </div>";

    $res = sendEmail($email, $name, $subject, $body);
    if ($res === true) {
        echo json_encode(["success" => true, "message" => "New code sent to your email."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error sending email."]);
    }
    exit;
}
?>