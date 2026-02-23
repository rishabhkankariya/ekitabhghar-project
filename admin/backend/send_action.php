<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// 🔐 Security: Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access. Please login.']);
    exit();
}

require_once '../../config/send_mail.php';
require_once '../../php/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : null;
    $message = isset($_POST['message']) ? trim($_POST['message']) : null;
    $action = isset($_POST['action']) ? $_POST['action'] : null;

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Valid email is required.']);
        exit;
    }

    if (!$message) {
        echo json_encode(['status' => 'error', 'message' => 'Message content is empty.']);
        exit;
    }

    // Check if action is send message or send warning
    if ($action === 'message' || $action === 'warning') {
        $subject = ($action === 'message') ? 'Message from Kitabghar Support' : '⚠️ Account Warning from Kitabghar';

        $htmlBody = '
        <div style="font-family: sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <div style="background: ' . ($action === 'message' ? '#4A90E2' : '#FF5722') . '; color: white; padding: 25px; text-align: center;">
                <h2 style="margin:0; font-size: 22px;">' . ($action === 'message' ? 'Message from Support' : 'Administrative Warning') . '</h2>
            </div>
            <div style="padding: 30px; line-height: 1.6; color: #333; background: #fff;">
                <p style="font-size: 15px;">' . nl2br(htmlspecialchars($message)) . '</p>
                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #888; text-align: center;">
                    This is an official communication from Kitabghar. Please do not reply directly to this email.
                </div>
            </div>
        </div>';

        // [TESTING MODE] Skip sending email
        // $res = sendEmail($email, "User", $subject, $htmlBody);
        echo json_encode(['status' => 'success', 'message' => ucfirst($action) . ' logged successfully! (Email disabled in test mode)']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action type specified.']);
    }
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>