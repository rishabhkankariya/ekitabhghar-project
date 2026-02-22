<?php
// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';
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

            // Initialize PHPMailer once outside the loop
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'ekitabghar@gmail.com';
                $mail->Password = 'pdfxjcyzffgskypq';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
                $mail->SMTPKeepAlive = true; // KEEP CONNECTION ALIVE

                $mail->clearAddresses(); // Added for safety
                $mail->setFrom('ekitabghar@gmail.com', 'Kitabghar Admin');
                $mail->isHTML(true);
                $mail->Subject = 'Student Account Credentials';
                $mail->Timeout = 15; // SMTP timeout
            } catch (Exception $e) {
                error_log("PHPMailer Setup Error: " . $mail->ErrorInfo);
                // We'll continue even if SMTP setup fails, but emails won't send
            }

            // Prepare statement (Update all fields on duplicate Roll No)
            $stmt = $conn->prepare("INSERT INTO student_accounts (roll_no, full_name, email, phone_number, course, admission_year, expected_passing_year, password_hash, is_temp_password) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1) 
                                    ON DUPLICATE KEY UPDATE 
                                        full_name=VALUES(full_name), 
                                        email=VALUES(email), 
                                        phone_number=VALUES(phone_number), 
                                        course=VALUES(course),
                                        admission_year=VALUES(admission_year),
                                        expected_passing_year=VALUES(expected_passing_year),
                                        password_hash=VALUES(password_hash),
                                        is_temp_password=1");

            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) < 7)
                    continue;

                $roll = trim($data[0]);
                $name = trim($data[1]);
                $email = trim($data[2]);
                $phone = trim($data[3]);
                $course = trim($data[4]);
                $admin_year = (int) trim($data[5]);
                $pass_year = (int) trim($data[6]);
                $dob_raw = isset($data[7]) ? trim($data[7]) : '';

                // Generate Custom Temp Password
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
                $stmt->bind_param("sssssiis", $roll, $name, $email, $phone, $course, $admin_year, $pass_year, $hash);

                if ($stmt->execute()) {
                    if ($stmt->affected_rows >= 1) {
                        if ($stmt->affected_rows === 1) {
                            $successCount++;
                        } else {
                            $updatedCount++;
                        }
                        try {
                            $mail->clearAddresses();
                            $mail->addAddress($email, $name);

                            // Dynamically Calculate Login URL
                            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                            $host = $_SERVER['HTTP_HOST'];
                            $path = dirname(dirname(dirname($_SERVER['SCRIPT_NAME'])));
                            $path = rtrim(str_replace('\\', '/', $path), '/');
                            $loginLink = $protocol . $host . $path . '/student_login.html';

                            $mail->Body = "
                            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
                                <h2 style='color: #2563EB; border-bottom: 2px solid #eee; padding-bottom: 10px;'>Welcome to Kitabghar</h2>
                                <p>Dear <strong>$name</strong>,</p>
                                <p>Your student account has been created by the administration. Please find your login credentials below:</p>
                                
                                <div style='background-color: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; margin: 15px 0;'>
                                    <p style='margin: 5px 0;'><strong>Roll No:</strong> $roll</p>
                                    <p style='margin: 5px 0;'><strong>Email:</strong> $email</p>
                                    <div style='background-color: #fff; border-left: 4px solid #f59e0b; padding: 10px; margin-top: 5px;'>
                                        <p style='margin: 0; color: #856404; font-size: 0.95em;'><strong>Temporary Password Format:</strong></p>
                                        <p style='margin: 5px 0 0 0; font-size: 0.9em;'>
                                            First 4 letters of your Name + Day (DD) + Year (YYYY).<br>
                                            <span style='color: #666; font-style: italic;'>Example: 'Pankaj Sharma' (born 15/12/2004) &rarr; <strong>PANK152004</strong></span>
                                        </p>
                                    </div>
                                </div>

                                <h3 style='font-size: 16px; margin-top: 20px;'>Steps to Login:</h3>
                                <ol style='line-height: 1.6;'>
                                    <li>Go to the <a href='$loginLink' style='color: #2563EB; font-weight: bold;'>Student Login Page</a>.</li>
                                    <li>Enter your Email and the Temporary Password provided above.</li>
                                    <li>You will be required to set a new, secure password upon your first login.</li>
                                </ol>

                                <div style='margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;'>
                                    <p style='margin: 0;'>Best Regards,</p>
                                    <p style='margin: 5px 0; font-weight: bold;'>Kitabghar Administration</p>
                                </div>
                            </div>";

                            $mail->send();
                            $emailCount++;
                        } catch (Exception $e) {
                            error_log("Failed to send email to $email: " . $mail->ErrorInfo);
                        }
                    } else {
                        // affected_rows is 0 (matched exactly, no change)
                        $updatedCount++;
                    }
                } else {
                    $failCount++;
                }
            }
            fclose($handle);

            // Safer close of SMTP
            try {
                if ($mail->getSMTPInstance()) {
                    $mail->smtpClose();
                }
            } catch (Exception $e) { /* ignore cleanup errors */
            }

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
            $statusMap = [
                'mark_active' => 'active',
                'mark_completed' => 'completed',
                'block' => 'blocked'
            ];
            $newStatus = $statusMap[$action];
            $sql = "UPDATE student_accounts SET account_status = '$newStatus' WHERE id IN ($idList)";
            $msg = "Updated status to '$newStatus'.";
        }

        if ($conn->query($sql)) {
            header("Location: ../manage_students.php?msg=" . urlencode($msg));
        } else {
            header("Location: ../manage_students.php?error=" . urlencode("Database error: " . $conn->error));
        }
        exit;
    }
}

header("Location: ../manage_students.php");
?>

