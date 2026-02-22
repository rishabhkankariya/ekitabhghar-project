<?php
session_start();
include "../../php/connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["captcha"]) || !isset($_SESSION["captcha"])) {
        $_SESSION['admin_login_toast'] = [
            'type' => 'error',
            'message' => 'CAPTCHA validation failed. Session may have expired.'
        ];
        header("Location: ../admin_login.php");
        exit();
    }

    $entered_captcha = strtoupper(trim($_POST["captcha"]));
    $actual_captcha = $_SESSION["captcha"];
    unset($_SESSION["captcha"]);

    if ($entered_captcha !== $actual_captcha) {
        $_SESSION['admin_login_toast'] = [
            'type' => 'error',
            'message' => 'Incorrect CAPTCHA. Please try again.'
        ];
        header("Location: ../admin_login.php");
        exit();
    }

    if (isset($_SESSION["captcha_time"]) && (time() - $_SESSION["captcha_time"]) > 120) {
        $_SESSION['admin_login_toast'] = [
            'type' => 'error',
            'message' => 'CAPTCHA expired. Try again.'
        ];
        header("Location: ../admin_login.php");
        exit();
    }

    $username = trim($_POST["admin_username"]);
    $password = trim($_POST["admin_password"]);

    $sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_id"] = $admin['admin_id'];
        $_SESSION["admin_username"] = $admin['username'];

        header("Location: /admin/adminpanel.php");
        exit();
    } else {
        $_SESSION['admin_login_toast'] = [
            'type' => 'error',
            'message' => 'Invalid username or password!'
        ];
        header("Location: ../admin_login.php");
        exit();
    }


}
?>
