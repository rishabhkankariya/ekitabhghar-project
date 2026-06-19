<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Unauthorized access! Please log in.'); window.location.href = '../admin_login.php';</script>";
    exit();
}
require_once '../../config/send_mail.php';
require_once '../../php/connection.php';

$input = json_decode(file_get_contents('php://input'), true);
if ($input && isset($input['action']) && isset($input['ids'])) {
    $action = $input['action'];
    $ids = $input['ids'];
    $reason = $input['reason'] ?? 'Bulk Rejection';

    foreach ($ids as $id) {
        $id = intval($id);
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$student) continue;

        if ($action === 'approve') {
            if ($pdo->prepare("UPDATE students SET status = 'approved' WHERE id = ?")->execute([$id])) {
                $subject = "Exam Form Approved";
                $message = "Your exam form has been approved.";
                $htmlBody = prepareHtml($student['student_name'], $message);
                sendEmail($student['email_id'], $student['student_name'], $subject, $htmlBody);
            }
        } elseif ($action === 'reject') {
            try {
                $pdo->beginTransaction();
                $pdo->prepare("INSERT INTO rejected_students (original_id, roll_no, student_name, current_semester, category, mobile_no, email_id, exam_date, reason, status, rejected_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'rejected', NOW())")
                    ->execute([$student['id'], $student['roll_no'], $student['student_name'], $student['current_semester'], $student['category'], $student['mobile_no'], $student['email_id'], $student['exam_date'], $reason]);
                $pdo->prepare("DELETE FROM students WHERE id = ?")->execute([$id]);
                $pdo->commit();
                
                $subject = "Exam Form Rejected";
                $message = "Your exam form has been rejected. Reason: " . $reason;
                $htmlBody = prepareHtml($student['student_name'], $message);
                sendEmail($student['email_id'], $student['student_name'], $subject, $htmlBody);
            } catch (Exception $e) {
                $pdo->rollBack();
            }
        }
    }
    echo json_encode(['status' => 'success']);
    exit();
}

// Helper to prepare HTML
function prepareHtml($name, $message)
{
    return "
    <div style='max-width: 600px; margin: auto; font-family: Arial, sans-serif;'>
        <div style='background: #0d6efd; padding: 20px; color: white; text-align: center; font-size: 24px; border-radius: 10px 10px 0 0;'>
            Exam Form Notification
        </div>
        <div style='padding: 20px; background: #f9f9f9; border-radius: 0 0 10px 10px;'>
            <p style='font-size: 18px;'>Dear <strong>$name</strong>,</p>
            <p style='font-size: 16px;'>$message</p>
            <p style='font-size: 14px; color: #777; margin-top: 20px; text-align: center;'>This is an automated email. Please do not reply.</p>
        </div>
    </div>";
}

// Handle Approve/Reject
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $_SESSION['message'] = "⚠️ Student not found!";
        $_SESSION['message_type'] = "danger";
        header("Location: ../manage.php");
        exit();
    }

    $email = $student['email_id'];
    $name = $student['student_name'];

    if ($action == "approve") {
        $subject = "Exam Form Approved";
        $message = "Your exam form has been approved.";
        $update = $pdo->prepare("UPDATE students SET status = 'approved' WHERE id = ?");
        $success = $update->execute([$id]);
        if ($success) {
            $htmlBody = prepareHtml($name, $message);
            sendEmail($email, $name, $subject, $htmlBody);
            $_SESSION['message'] = "Student approved and email sent.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to update status in database.";
            $_SESSION['message_type'] = "danger";
        }
    } elseif ($action == "reject") {
        $reason = isset($_GET['reason']) ? trim($_GET['reason']) : '';
        try {
            $pdo->beginTransaction();
            $pdo->prepare("INSERT INTO rejected_students (original_id, roll_no, student_name, current_semester, category, mobile_no, email_id, exam_date, reason, status, rejected_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'rejected', NOW())")
                ->execute([$student['id'], $student['roll_no'], $student['student_name'], $student['current_semester'], $student['category'], $student['mobile_no'], $student['email_id'], $student['exam_date'], $reason]);
            $pdo->prepare("DELETE FROM students WHERE id = ?")->execute([$id]);
            $pdo->commit();
            
            $subject = "Exam Form Rejected";
            $message = "Your exam form has been rejected. Reason: " . ($reason ? $reason : 'N/A');
            $htmlBody = prepareHtml($name, $message);
            sendEmail($email, $name, $subject, $htmlBody);
            
            $_SESSION['message'] = "Student rejected, archived & email sent.";
            $_SESSION['message_type'] = "danger";
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['message'] = "Rejection failed: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
        }
    } elseif ($action == "allow_edit") {
        $update = $pdo->prepare("UPDATE students SET can_edit = 1 WHERE id = ?");
        $success = $update->execute([$id]);
        if ($success) {
            $subject = "Exam Form Edit Access Granted";
            $message = "The administrator has granted you permission to edit your exam form. You can now log in to the student portal and make the necessary changes to your application.";
            $htmlBody = prepareHtml($name, $message);
            sendEmail($email, $name, $subject, $htmlBody);
            
            $_SESSION['message'] = "Student can now edit their form. Email sent.";
            $_SESSION['message_type'] = "primary";
        } else {
            $_SESSION['message'] = "Failed to enable editing in database.";
            $_SESSION['message_type'] = "danger";
        }
    }
}

// Redirect back to manage
header("Location: ../manage.php");
exit();
?>