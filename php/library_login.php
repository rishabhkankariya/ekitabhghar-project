<?php
session_start();
include "connection.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CAPTCHA Validation
    if (!isset($_POST["captcha"]) || !isset($_SESSION["captcha"])) {
        $_SESSION['library_login_toast'] = [
            'type' => 'error',
            'message' => 'CAPTCHA validation failed. Session may have expired.'
        ];
        header("Location: ../library_login.html");
        exit();
    }

    $entered_captcha = strtoupper(trim($_POST["captcha"]));
    $actual_captcha = $_SESSION["captcha"];
    unset($_SESSION["captcha"]);

    if ($entered_captcha !== $actual_captcha) {
        $_SESSION['library_login_toast'] = [
            'type' => 'error',
            'message' => 'Incorrect CAPTCHA. Please try again.'
        ];
        header("Location: ../library_login.html");
        exit();
    }

    if (isset($_SESSION["captcha_time"]) && (time() - $_SESSION["captcha_time"]) > 120) {
        $_SESSION['library_login_toast'] = [
            'type' => 'error',
            'message' => 'CAPTCHA expired. Try again.'
        ];
        header("Location: ../library_login.html");
        exit();
    }

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $sql = "SELECT id, username, password FROM library_admin WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();

      if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["username"] = $admin["username"];

            $_SESSION['library_login_toast'] = [
                'type' => 'success',
                'message' => 'Login successful! Redirecting...'
            ];
            // Redirect to intermediate page for spinner + delay
            header("Location: ../library_dashboard.php");
            exit();
        }else {
            $_SESSION['library_login_toast'] = [
                'type' => 'error',
                'message' => 'Invalid username or password!'
            ];
            header("Location: ../library_login.html");
            exit();
        }
    } else {
        $_SESSION['library_login_toast'] = [
            'type' => 'error',
            'message' => 'Database error: ' . $conn->error
        ];
        header("Location: ../library_login.html");
        exit();
    }
    $conn->close();
}
?>
