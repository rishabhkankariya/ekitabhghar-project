<?php
session_start();
header('Content-Type: application/json');

// Check for admin session
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once '../../php/connection.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $student_id = $data['id'] ?? null;
    $action = $data['action'] ?? null; // 'enable' or 'disable'

    if (!$student_id || !$action) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        exit;
    }

    // Fetch student email and name before updating
    $stmt_fetch = $conn->prepare("SELECT student_name, email_id FROM students WHERE id = ?");
    $stmt_fetch->bind_param("i", $student_id);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();

    if ($result_fetch->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit;
    }

    $student = $result_fetch->fetch_assoc();
    $student_name = $student['student_name'];
    $student_email = $student['email_id'];
    $stmt_fetch->close();


    $can_edit = ($action === 'enable') ? 1 : 0;

    // Use prepared statement to update
    if (isset($conn)) {
        $stmt = $conn->prepare("UPDATE students SET can_edit = ? WHERE id = ?");
        $stmt->bind_param("ii", $can_edit, $student_id);

        if ($stmt->execute()) {
            $response_message = ($action === 'enable') ? 'Access enabled' : 'Access disabled';

            // Send Email if Enabled
            if ($action === 'enable') {
                $email_sent = sendAccessEmail($student_email, $student_name);
                if ($email_sent) {
                    $response_message .= ' and email notification sent.';
                } else {
                    $response_message .= ', but email failed to send.';
                }
            }

            echo json_encode(['success' => true, 'message' => $response_message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed: ' . $conn->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

// Function to send email
function sendAccessEmail($to, $name)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ekitabghar@gmail.com';
        $mail->Password = 'pdfxjcyzffgskypq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // SSL verification bypass (if needed for local dev)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('ekitabghar@gmail.com', 'Kitabghar Admin');
        $mail->addAddress($to, $name);
        $mail->addReplyTo('no-reply@ekitabghar.in', 'No Reply');

        $mail->isHTML(true);
        $mail->Subject = 'Exam Form Edit Access Granted';

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto;'>
            <div style='background-color: #4f46e5; color: #fff; padding: 20px; border-radius: 10px 10px 0 0; text-align: center;'>
                <h2>🔓 Action Required: Edit Access Granted</h2>
            </div>
            <div style='background-color: #f9fafb; padding: 20px; border-radius: 0 0 10px 10px; border: 1px solid #e5e7eb;'>
                <p>Dear <strong>$name</strong>,</p>
                <p>The administrator has granted you permission to <strong>edit your exam form</strong>.</p>
                <p>You can now log in to the student portal and make the necessary changes to your application. Please ensure all details are correct before re-submitting.</p>
                
                 <div style='text-align: center; margin: 30px 0;'>
                    <a href='http://localhost/website/student_login.html' style='background-color: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Login to Edit</a>
                </div>

                <p style='margin-top: 30px; font-size: 13px; color: #6b7280; text-align: center;'>This is an automated message. Please do not reply directly to this email.</p>
            </div>
        </div>";

        $mail->AltBody = "Dear $name,\n\nThe administrator has granted you permission to edit your exam form.\n\nPlease log in to the student portal to update your details.\n\nThank you.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>
