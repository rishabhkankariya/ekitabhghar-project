<?php
session_start();
require_once 'connection.php'; // Database configuration

header('Content-Type: application/json');

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "⚠ Please fill in all fields."]);
        exit;
    }

    if (!isset($_POST["captcha"]) || !isset($_SESSION["captcha"]) || !isset($_SESSION["captcha_time"])) {
        echo json_encode([
            "success" => false,
            "message" => "⚠️ CAPTCHA validation failed. Session may have expired."
        ]);
        exit;
    }

    $captchaAge = time() - $_SESSION["captcha_time"]; // Calculate CAPTCHA age

    // Check if CAPTCHA is expired (4 minutes = 240 seconds)
    if ($captchaAge > 240) {
        unset($_SESSION["captcha"]);
        unset($_SESSION["captcha_time"]);
        echo json_encode([
            "success" => false,
            "message" => "⏳ CAPTCHA expired. Please try again."
        ]);
        exit;
    }

    // Validate CAPTCHA input
    if (strtoupper(trim($_POST["captcha"])) !== $_SESSION["captcha"]) {
        echo json_encode([
            "success" => false,
            "message" => "❌ Incorrect CAPTCHA. Please try again."
        ]);
        exit;
    }
    // One-time CAPTCHA use (only after validation passes)
    unset($_SESSION["captcha"]);
    unset($_SESSION["captcha_time"]);

    $stmt = $conn->prepare("SELECT id, full_name as username, email, password_hash as password, account_status FROM student_accounts WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['account_status'] === 'blocked') {
            echo json_encode(["success" => false, "message" => "❌ Account is blocked. Contact Administrator."]);
            exit;
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_logged_in'] = true;
            echo json_encode(["success" => true, "message" => "✅ Login successful! Redirecting..."]);
            exit;
        } else {
            echo json_encode(["success" => false, "message" => "❌ Invalid password."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "❌ No user found with this email."]);
    }

    $stmt->close();
    $conn->close();
}
?>
