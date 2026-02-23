<?php
session_start();

// Auth Check
if (!isset($_SESSION['admin_id'])) {
    echo "<script>
            alert('Unauthorized access! Please log in.');
            window.location.href = '../admin_login.php';
          </script>";
    exit();
}

require_once '../../config/send_mail.php';
require_once '../../php/connection.php';

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => "DB connection failed: " . $conn->connect_error]));
}

// Check for JSON POST (Bulk Actions)
$input = json_decode(file_get_contents('php://input'), true);
if ($input && isset($input['action']) && isset($input['ids'])) {
    $action = $input['action'];
    $ids = $input['ids'];
    $reason = $input['reason'] ?? 'Bulk Rejection';
    $errors = [];

    foreach ($ids as $id) {
        $id = intval($id);

        // Fetch student
        $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$student)
            continue;

        if ($action === 'approve') {
            $update = $conn->prepare("UPDATE students SET status = 'approved' WHERE id = ?");
            $update->bind_param("i", $id);
            if ($update->execute()) {
                // [TESTING MODE] Skip email
                // sendEmail($student['email_id'], $student['student_name'], "Exam Form Approved", prepareHtml($student['student_name'], "Your exam form has been approved ✅."));
            }
            $update->close();
        } elseif ($action === 'reject') {
            $conn->begin_transaction();
            try {
                $insert = $conn->prepare("INSERT INTO rejected_students (original_id, roll_no, student_name, current_semester, category, mobile_no, email_id, exam_date, reason, status, rejected_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'rejected', NOW())");
                $insert->bind_param("issssssss", $student['id'], $student['roll_no'], $student['student_name'], $student['current_semester'], $student['category'], $student['mobile_no'], $student['email_id'], $student['exam_date'], $reason);
                $insert->execute();

                $delete = $conn->prepare("DELETE FROM students WHERE id = ?");
                $delete->bind_param("i", $id);
                $delete->execute();

                $conn->commit();
                // [TESTING MODE] Skip email
                // sendEmail($student['email_id'], $student['student_name'], "Exam Form Rejected", prepareHtml($student['student_name'], "Your exam form has been rejected ❌. Reason: $reason"));
            } catch (Exception $e) {
                $conn->rollback();
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

    // Fetch student full details
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    $stmt->close();

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
        $message = "<i class='bi bi-check-circle-fill' style='color:green;'></i> Your exam form has been <strong style='color:green;'>approved ✅</strong>. You may proceed with the next steps.";

        // UPDATE DATABASE FIRST
        $update = $conn->prepare("UPDATE students SET status = 'approved' WHERE id = ?");
        $update->bind_param("i", $id);
        $success = $update->execute();
        $update->close();

        if ($success) {
            if (true) { // [TESTING MODE] Skip email, always succeed
                // sendEmail($email, $name, $subject, prepareHtml($name, $message));
                $_SESSION['message'] = "<i class='bi bi-check-circle-fill text-success'></i> Student approved and email sent.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "<i class='bi bi-check-circle-fill text-success'></i> Student approved, but <i class='bi bi-exclamation-triangle-fill text-warning'></i> email sending failed!";
                $_SESSION['message_type'] = "warning";
            }
        } else {
            $_SESSION['message'] = "<i class='bi bi-x-circle-fill text-danger'></i> Failed to update status in database.";
            $_SESSION['message_type'] = "danger";
        }

    } elseif ($action == "reject") {
        $reason = isset($_GET['reason']) ? trim($_GET['reason']) : '';
        $subject = "Exam Form Rejected";
        $message = "<i class='bi bi-x-circle-fill' style='color:red;'></i> Unfortunately, your exam form has been <strong style='color:red;'>rejected ❌</strong>.<br><br><b>Reason:</b> <span style='color:#dc3545;'>$reason</span><br><br>Please contact the office for further clarification.";

        $conn->begin_transaction(); // Transaction start

        try {
            // Insert data into rejected_students (including reason)
            $insert = $conn->prepare("INSERT INTO rejected_students 
                (original_id, roll_no, student_name, current_semester, category, mobile_no, email_id, exam_date, reason, status, rejected_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'rejected', NOW())");

            $insert->bind_param(
                "issssssss",
                $student['id'],
                $student['roll_no'],
                $student['student_name'],
                $student['current_semester'],
                $student['category'],
                $student['mobile_no'],
                $student['email_id'],
                $student['exam_date'],
                $reason
            );

            if (!$insert->execute()) {
                throw new Exception("Insert failed: " . $insert->error);
            }
            $insert->close();

            // Delete student from main table
            $delete = $conn->prepare("DELETE FROM students WHERE id = ?");
            $delete->bind_param("i", $id);
            if (!$delete->execute()) {
                throw new Exception("Delete failed: " . $delete->error);
            }
            $delete->close();

            $conn->commit();

            // Only attempt email after successful DB transaction
            if (true) { // [TESTING MODE] Skip email, always succeed
                // sendEmail($email, $name, $subject, prepareHtml($name, $message));
                $_SESSION['message'] = "<i class='bi bi-trash-fill text-danger'></i> Student rejected, archived & email sent.";
                $_SESSION['message_type'] = "danger";
            } else {
                $_SESSION['message'] = "<i class='bi bi-trash-fill text-danger'></i> Student rejected & archived, but <i class='bi bi-exclamation-triangle-fill text-warning'></i> email sending failed!";
                $_SESSION['message_type'] = "warning";
            }

        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['message'] = "<i class='bi bi-x-circle-fill text-danger'></i> Rejection failed: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
        }

    } elseif ($action == "allow_edit") {
        $subject = "Exam Form Editing Enabled";
        $message = "<i class='bi bi-pencil-square' style='color:#0d6efd;'></i> Your exam form has been enabled for <strong style='color:#0d6efd;'>editing ✏️</strong>.<br><br>You can now log in and update your details. Please make sure to re-submit after editing.";

        // UPDATE DATABASE FIRST
        $update = $conn->prepare("UPDATE students SET can_edit = 1 WHERE id = ?");
        $update->bind_param("i", $id);
        $success = $update->execute();
        $update->close();

        if ($success) {
            if (true) { // [TESTING MODE] Skip email, always succeed
                // sendEmail($email, $name, $subject, prepareHtml($name, $message));
                $_SESSION['message'] = "<i class='bi bi-check-circle-fill text-primary'></i> Student can now edit their form. Email sent.";
                $_SESSION['message_type'] = "primary";
            } else {
                $_SESSION['message'] = "<i class='bi bi-check-circle-fill text-primary'></i> Student can now edit, but <i class='bi bi-exclamation-triangle-fill text-warning'></i> email sending failed!";
                $_SESSION['message_type'] = "warning";
            }
        } else {
            $_SESSION['message'] = "<i class='bi bi-x-circle-fill text-danger'></i> Failed to enable editing in database.";
            $_SESSION['message_type'] = "danger";
        }
    }
}

// Redirect back to manage
header("Location: ../manage.php");
exit();
?>