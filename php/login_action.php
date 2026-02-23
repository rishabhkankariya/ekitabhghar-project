<?php
session_start();
require_once 'connection.php';
require_once '../config/send_mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_input = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $captcha_input = isset($_POST['captcha']) ? strtoupper(trim($_POST['captcha'])) : '';

    // 1. Validation
    if (empty($login_input) || empty($password)) {
        header("Location: ../student_login.html?error=Please fill in all fields.");
        exit;
    }

    if (!isset($_SESSION['captcha']) || $captcha_input !== $_SESSION['captcha']) {
        header("Location: ../student_login.html?error=Incorrect CAPTCHA. Please try again.");
        exit;
    }
    unset($_SESSION['captcha']); // Clear captcha after use

    // 2. Fetch User
    $stmt = $conn->prepare("SELECT id, roll_no, email, full_name, password_hash, is_temp_password, account_status FROM student_accounts WHERE email = ? OR roll_no = ?");
    $stmt->bind_param("ss", $login_input, $login_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Status Checks
        if ($row['account_status'] === 'blocked') {
            header("Location: ../student_login.html?error=Account is blocked. Contact Administrator.");
            exit;
        }

        // Password matching
        if (password_verify($password, $row['password_hash'])) {
            // Success Logic
            if ($row['is_temp_password']) {
                // Route to OTP (First Login / Temp Password)
                $_SESSION['temp_login_user_id'] = $row['id'];
                $_SESSION['user_id_for_otp'] = $row['id'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_name'] = $row['full_name'];
                $_SESSION['is_temp_password'] = 1;

                $otp = rand(100000, 999999);
                $_SESSION['login_otp'] = $otp;
                $_SESSION['login_otp_expiry'] = time() + 300;

                // [TESTING MODE] Skip email, show OTP on screen via URL param
                header("Location: ../verify_login_otp.html?debug_otp=" . $otp);
                exit;
            } else {
                // Permanent Login
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['roll_no'] = $row['roll_no'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_name'] = $row['full_name'];
                $_SESSION['is_logged_in'] = true;

                // Update Login Details
                $ip = $_SERVER['REMOTE_ADDR'];
                $conn->query("UPDATE student_accounts SET last_login_at = NOW(), last_login_ip = '$ip' WHERE id = " . $row['id']);

                header("Location: ../dashboard.php");
                exit;
            }
        } else {
            header("Location: ../student_login.html?error=Incorrect password. Please try again.");
            exit;
        }
    } else {
        header("Location: ../student_login.html?error=No student account found with this Email or Roll No.");
        exit;
    }
} else {
    header("Location: ../student_login.html");
    exit;
}
?>