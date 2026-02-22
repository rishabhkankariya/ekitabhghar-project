<?php
session_start();
require_once 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Session & Input Check
    if (!isset($_SESSION['login_otp']) || !isset($_SESSION['temp_login_user_id'])) {
        echo json_encode(["success" => false, "message" => "Session expired. Please login again.", "redirect" => "student_login.html"]);
        exit;
    }

    $user_otp = trim($_POST['otp']);

    // 2. Validate format
    if (empty($user_otp)) {
        echo json_encode(["success" => false, "message" => "Please enter the OTP."]);
        exit;
    }

    // 3. Verify OTP
    // (Check expiry if you want strict security, added in generated time + 300s)
    if (time() > $_SESSION['login_otp_expiry']) {
        echo json_encode(["success" => false, "message" => "OTP has expired."]);
        exit;
    }

    if ($user_otp == $_SESSION['login_otp']) {
        // --- SUCCESS ---

        // Finalize Login Session
        $_SESSION['user_id'] = $_SESSION['temp_login_user_id'];
        // 'user_email', 'user_name', 'roll_no' were already set in login_action.php

        // Cleanup Temp OTP vars
        unset($_SESSION['login_otp']);
        unset($_SESSION['login_otp_expiry']);
        unset($_SESSION['temp_login_user_id']);

        // Check if Force Password Change is needed
        if ($_SESSION['is_temp_password']) {
            $_SESSION['force_password_change'] = true;
            unset($_SESSION['is_temp_password']);
            echo json_encode(["success" => true, "redirect" => "change_password.php"]);
        } else {
            $_SESSION['is_logged_in'] = true;
            unset($_SESSION['is_temp_password']);

            // Log access time (already done in login_action, but maybe update again? Not critical)
            $update = $conn->prepare("UPDATE student_accounts SET last_login_at = NOW() WHERE id = ?");
            $update->bind_param("i", $_SESSION['user_id']);
            $update->execute();

            echo json_encode(["success" => true, "redirect" => "dashboard.php"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid Verification Code."]);
    }
}
?>
