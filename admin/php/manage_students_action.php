<?php
require_once '../../config/send_mail.php';
session_start();
include '../../php/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    die("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // --- BULK UPLOAD ---
    if ($action === 'upload' && isset($_FILES['csv_file'])) {
        set_time_limit(600);
        ini_set('memory_limit', '256M');

        if ($_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            header("Location: ../manage_students.php?error=Upload failed (Code: " . $_FILES['csv_file']['error'] . ")");
            exit;
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");

        if ($handle) {
            // Skip header
            fgetcsv($handle);
            $successCount = 0;
            $failCount = 0;
            $emailCount = 0;
            $updatedCount = 0;

            // Prepare statement (Update all fields on duplicate Roll No)
            $stmt = $pdo->prepare("INSERT INTO student_accounts (roll_no, full_name, email, phone_number, course, admission_year, expected_passing_year, password_hash, is_temp_password) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1) 
                                    ON CONFLICT (roll_no) DO UPDATE SET 
                                        full_name=EXCLUDED.full_name, 
                                        email=EXCLUDED.email, 
                                        phone_number=EXCLUDED.phone_number, 
                                        course=EXCLUDED.course,
                                        admission_year=EXCLUDED.admission_year,
                                        expected_passing_year=EXCLUDED.expected_passing_year,
                                        password_hash=EXCLUDED.password_hash,
                                        is_temp_password=1");

            if (!$stmt) {
                die("Prepare failed");
            }

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) < 7) continue;

                $roll = trim($data[0]);
                $name = trim($data[1]);
                $email = trim($data[2]);
                $phone = trim($data[3]);
                $course = trim($data[4]);
                $admin_year = (int) trim($data[5]);
                $pass_year = (int) trim($data[6]);
                $dob_raw = isset($data[7]) ? trim($data[7]) : '';

                $tempPass = "";
                if (!empty($name) && !empty($dob_raw)) {
                    $cleanName = preg_replace('/[^a-zA-Z]/', '', $name);
                    $namePart = strtoupper(substr($cleanName, 0, 4));
                    $timestamp = strtotime(str_replace('/', '-', $dob_raw));
                    if ($timestamp) {
                        $day = date('d', $timestamp);
                        $year = date('Y', $timestamp);
                        $tempPass = $namePart . $day . $year;
                    }
                }

                if (empty($tempPass) || strlen($tempPass) < 8) {
                    $tempBytes = random_bytes(4);
                    $tempPass = bin2hex($tempBytes);
                }

                $hash = password_hash($tempPass, PASSWORD_BCRYPT);

                if ($stmt->execute([$roll, $name, $email, $phone, $course, $admin_year, $pass_year, $hash])) {
                    $successCount++;
                    $emailCount++;
                } else {
                    $failCount++;
                }
            }
            fclose($handle);



            $finalMsg = "Upload Results: $successCount New, $updatedCount Updated, $emailCount Emails Sent, $failCount Failed.";
            header("Location: ../manage_students.php?msg=" . urlencode($finalMsg));
            exit;
        } else {
            header("Location: ../manage_students.php?error=Could not open file");
            exit;
        }
    }

    // --- BULK ACTIONS ---
    if (in_array($action, ['mark_active', 'mark_completed', 'block', 'delete']) && isset($_POST['student_ids'])) {
        $ids = $_POST['student_ids'];
        $idList = implode(",", array_map('intval', $ids)); // Sanitize INTs

        if (empty($idList)) {
            header("Location: ../manage_students.php?error=No students selected");
            exit;
        }

        if ($action === 'delete') {
            $sql = "DELETE FROM student_accounts WHERE id IN ($idList)";
            $msg = "Deleted selected students.";
        } else {
            $statusMap = ['mark_active' => 'active', 'mark_completed' => 'completed', 'block' => 'blocked'];
            $newStatus = $statusMap[$action];
            $sql = "UPDATE student_accounts SET account_status = ? WHERE id IN ($idList)";
            $msg = "Updated status to '$newStatus'.";
        }

        $stmt = $pdo->prepare($sql);
        if ($action === 'delete') {
            $stmt->execute();
        } else {
            $stmt->execute([$newStatus]);
        }
        if ($stmt->rowCount() >= 0) {
            header("Location: ../manage_students.php?msg=" . urlencode($msg));
        } else {
            header("Location: ../manage_students.php?error=Database error");
        }
        exit;
    }
}

header("Location: ../manage_students.php");
?>