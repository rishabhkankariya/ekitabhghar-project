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

    $subject = 'Your New OTP';
    $body = "<div style='max-width: 600px; margin: 20px auto; padding: 20px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); text-align: center;'>
          <h2 style='font-size: 24px; color: #4CAF50; margin-bottom: 20px;'>NEW OTP</h2>
          
          <div style='background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);'>
            <p style='font-size: 16px; line-height: 1.6; margin: 10px 0;'>Your OTP is:</p>
            <h3 style='font-size: 30px; font-weight: bold; color: #2196F3; margin: 10px 0;'> <strong>$otp</strong></h3>
        <p style='font-size: 14px; color: #555;'>It expires in 5 minutes.</p>
      </div>
    </div>";

    $res = sendEmail($email, "Student", $subject, $body);
    if ($res === true) {
        echo json_encode(["success" => true, "message" => "✅ New OTP sent. [Didn't receive your OTP? Please check your spam or junk folder.]"]);
    } else {
        echo json_encode(["success" => false, "message" => "⚠ Error sending OTP."]);
    }
    exit;
}
?>