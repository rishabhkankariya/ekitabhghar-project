<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../student_login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        header("Location: ../change_password.php?error=Passwords do not match");
        exit;
    }

    if (strlen($new_pass) < 8) {
        header("Location: ../change_password.php?error=Password must be at least 8 characters");
        exit;
    }

    // Hash and Update
    $hash = password_hash($new_pass, PASSWORD_BCRYPT);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE student_accounts SET password_hash = ?, is_temp_password = 0, account_status = 'active', updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $hash, $user_id);

    if ($stmt->execute()) {
        unset($_SESSION['force_password_change']);
        $_SESSION['is_logged_in'] = true;

        // Redirect to Dashboard
        header("Location: ../dashboard.php?msg=Password Updated Successfully");
        exit;
    } else {
        header("Location: ../change_password.php?error=Database error");
        exit;
    }
}
?>
