<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'connection.php';
require_once '../config/send_mail.php';

try {
    if (!$pdo) {
        throw new PDOException("Database connection is not available.");
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Honepot field for basic spam protection
        if (!empty($_POST['website'])) {
            die("Spam detected.");
        }

        // Get and sanitize input
        $name = htmlspecialchars(trim($_POST['name']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $subject_field = htmlspecialchars(trim($_POST['subject']));
        $message = htmlspecialchars(trim($_POST['message']));

        // Basic Validation
        if (empty($name) || empty($email) || empty($message) || empty($subject_field)) {
            header("Location: ../index.php?toast=error&msg=All fields are required");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ../index.php?toast=error&msg=Invalid email format");
            exit;
        }

        // Store message in database (Prepend subject until DB schema is updated)
        $db_message = "Subject: $subject_field\n\n$message";
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $db_message]);

        // 1. Send "Thank You" email to the User
        $userSubject = "Thank you for contacting Kitabghar - " . $subject_field;
        $userBody = '
        <div style="max-width:600px;margin:auto;padding:20px;border:1px solid #eee;border-radius:10px;font-family:sans-serif;background:#fefefe;">
            <h2 style="color:#4A90E2;text-align:center;">📬 Hi ' . $name . '!</h2>
            <p style="font-size:16px;color:#333;">We have received your message regarding "<strong>' . $subject_field . '</strong>" and we will get back to you shortly.</p>
            <div style="margin:20px 0;padding:15px;background:#f9f9f9;border-left:4px solid #4A90E2;">
                <strong>Your Message:</strong><br>
                <p style="margin:5px 0;color:#555;">' . nl2br($message) . '</p>
            </div>
            <p style="font-size:14px;color:#777;">Regards,<br>Team Kitabghar</p>
            <hr style="margin:20px 0;">
            <p style="font-size:11px;text-align:center;color:#aaa;">&copy; ' . date("Y") . ' Kitabghar. All rights reserved.</p>
        </div>';

        // [TESTING MODE] Skip user thank-you email
        // sendEmail($email, $name, $userSubject, $userBody);

        // [TESTING MODE] Skip admin notification email
        // sendEmail($adminEmail, 'Admin', $adminSubject, $adminBody);

        // Success redirect
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Message sent successfully!'];
        header("Location: ../index.php?toast=success");
        exit;
    }

} catch (PDOException $e) {
    error_log("Contact form error: " . $e->getMessage());
    header("Location: ../index.php?toast=error&msg=System error. Please try again later.");
    exit;
}
