<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/send_mail.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

include '../../php/connection.php';

$roll_no = $_POST['roll_no'] ?? '';
$full_name = $_POST['full_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$course = $_POST['course'] ?? 'Diploma';
$admission_year = $_POST['admission_year'] ?? date('Y');
$passing_year = $_POST['passing_year'] ?? (date('Y') + 3);
$dob = $_POST['dob'] ?? '';

if (empty($roll_no) || empty($full_name) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit();
}

// Check if student already exists
$check = $conn->prepare("SELECT id FROM student_accounts WHERE roll_no = ? OR email = ?");
$check->bind_param("ss", $roll_no, $email);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Roll number or Email already exists']);
    exit();
}

// Generate Custom Temp Password (Parity with Excel Upload)
$tempPass = "";
if (!empty($full_name) && !empty($dob)) {
    $cleanName = preg_replace('/[^a-zA-Z]/', '', $full_name);
    $namePart = strtoupper(substr($cleanName, 0, 4));
    // Input is YYYY-MM-DD from HTML5 date picker usually, let's handle both
    $timestamp = strtotime(str_replace('/', '-', $dob));
    if ($timestamp) {
        $day = date('d', $timestamp);
        $year = date('Y', $timestamp);
        $tempPass = $namePart . $day . $year;
    }
}

if (empty($tempPass) || strlen($tempPass) < 8) {
    // Fallback if DOB logic fails
    $tempPass = $roll_no . "@123";
}

$passwordHash = password_hash($tempPass, PASSWORD_BCRYPT);

$sql = "INSERT INTO student_accounts (roll_no, full_name, email, phone_number, course, admission_year, expected_passing_year, password_hash, is_temp_password, account_status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, 'active')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssiis", $roll_no, $full_name, $email, $phone, $course, $admission_year, $passing_year, $passwordHash);

if ($stmt->execute()) {
    // Calculate Login URL
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname(dirname(dirname($_SERVER['SCRIPT_NAME'])));
    $path = rtrim(str_replace('\\', '/', $path), '/');
    $loginLink = $protocol . $host . $path . '/student_login.html';

    $subject = 'Student Account Credentials';
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
        <h2 style='color: #2563EB; border-bottom: 2px solid #eee; padding-bottom: 10px;'>Welcome to Kitabghar</h2>
        <p>Dear <strong>$full_name</strong>,</p>
        <p>Your student account has been created by the administration. Please find your login credentials below:</p>
        
        <div style='background-color: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; margin: 15px 0;'>
            <p style='margin: 5px 0;'><strong>Roll No:</strong> $roll_no</p>
            <p style='margin: 5px 0;'><strong>Email:</strong> $email</p>
            <div style='background-color: #fff; border-left: 4px solid #f59e0b; padding: 10px; margin-top: 5px;'>
                <p style='margin: 0; color: #856404; font-size: 0.95em;'><strong>Temporary Password Format:</strong></p>
                <p style='margin: 5px 0 0 0; font-size: 0.9em;'>
                    First 4 letters of your Name + Day (DD) + Year (YYYY).<br>
                    <span style='color: #666; font-style: italic;'>Example: 'Pankaj Sharma' (born 15/12/2004) -> <strong>PANK152004</strong></span>
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

    $res = sendEmail($email, $full_name, $subject, $body);
    if ($res === true) {
        echo json_encode(['status' => 'success', 'message' => 'Student account created and credentials emailed!']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Account created, but email failed: ' . $res]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
}
?>