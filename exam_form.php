<?php
session_start();
require_once 'php/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
  header("Location: student_login.html");
  exit;
}

$user_email = $_SESSION['user_email']; // Logged-in student email

// Single Session Enforcement
$check_sess = $conn->prepare("SELECT session_token FROM student_accounts WHERE email = ?");
$check_sess->bind_param("s", $user_email);
$check_sess->execute();
$sess_res = $check_sess->get_result();
if ($sess_row = $sess_res->fetch_assoc()) {
  if (!empty($sess_row['session_token']) && $sess_row['session_token'] !== session_id()) {
    session_unset();
    session_destroy();
    header("Location: student_login.html?error=You have been logged out because your account was accessed from another device.");
    exit;
  }
}

// --- RESTRICT ACCESS IF PROFILE IMAGE IS MISSING ---
$check_img = $conn->prepare("SELECT profile_image FROM student_accounts WHERE email = ?");
$check_img->bind_param("s", $user_email);
$check_img->execute();
$img_res = $check_img->get_result()->fetch_assoc();

if (!$img_res || empty($img_res['profile_image']) || $img_res['profile_image'] === 'users.png') {
  header("Location: profile_update.php?error=Access Denied! You must upload a profile image first.");
  exit;
}
$check_img->close();
// ----------------------------------------------------

// 1. Fetch Master Data from student_accounts
$master_query = $conn->prepare("SELECT roll_no, full_name, phone_number, course FROM student_accounts WHERE email = ?");
$master_query->bind_param("s", $user_email);
$master_query->execute();
$master_data = $master_query->get_result()->fetch_assoc();
$master_query->close();

$master_roll = $master_data['roll_no'] ?? "";
$master_name = $master_data['full_name'] ?? "";
$master_phone = $master_data['phone_number'] ?? "";
$master_course = $master_data['course'] ?? "";

// 2. Check Submission Status from students table
$student_query = $conn->prepare("SELECT * FROM students WHERE email_id = ?");
$student_query->bind_param("s", $user_email);
$student_query->execute();
$student_result = $student_query->get_result();
$student_data = $student_result->fetch_assoc();
$student_query->close();

$already_submitted = ($student_data !== null);
$can_edit = $student_data['can_edit'] ?? 0;

$roll_no = $student_data['roll_no'] ?? $master_roll;
$student_name = $student_data['student_name'] ?? $master_name;
$mobile_no = $student_data['mobile_no'] ?? $master_phone;

// Get exam start and end dates from the database
$exam_query = $conn->query("SELECT start_date, end_date FROM exam_settings LIMIT 1");
$exam_settings = $exam_query->fetch_assoc();

if (!$exam_settings) {
  die("Exam settings not found.");
}

// Convert MySQL DATETIME to timestamps
$start_date = strtotime($exam_settings['start_date']);
$end_date = strtotime($exam_settings['end_date']);
$current_time = time();

$status_message = "";
$status_class = ""; // CSS class for styling

if ($already_submitted && !$can_edit) {
  $status = htmlspecialchars($student_data["status"]);

  // Assign color based on status
  if ($status == "approved") {
    $status_class = "approved";
  } elseif ($status == "pending") {
    $status_class = "pending";
  } elseif ($status == "rejected") {
    $status_class = "rejected";
  }

  $status_message = "
   <div style='font-size: 16px; font-weight: bold; color: #333; padding: 20px; background: #fff; border-radius: 12px; display: inline-block; text-align: center; width: 100%; max-width: 600px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);'>
       <p style='margin-bottom: 10px; font-weight: 700; font-size: 20px; color: #111;'>🎉 Your exam form has been successfully submitted.</p>
    <p style='margin-bottom: 15px; color: #6b7280; font-size: 15px;'>
        <span>Status: </span>
        <span class='$status_class' style='font-weight: 600; text-transform: uppercase;'>$status</span>
    </p>
    <p style='margin-bottom: 0;'>
        <a href='dashboard.php' style='color: #4f46e5; text-decoration: none; font-weight: 600; padding: 10px 20px; border: 1px solid #4f46e5; border-radius: 8px; display: inline-block; transition: all 0.3s;'>Back to Dashboard</a>
    </p>
      <div style='margin-top: 20px; display: flex; justify-content: center; align-items: center; width: 100%;'>
    <dotlottie-player 
        loading='lazy'
        src='https://lottie.host/15078e87-394d-4d63-a49e-77e4df2b244a/7KzXhhk4tp.lottie' 
        background='transparent' 
        speed='1'
        style='max-width: 100%; width: 200px; height: 200px;'
        loop 
        autoplay>
    </dotlottie-player>
   </div>
   </div>";
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KITABGHAR | Exam Registration</title>

  <!-- Fonts -->
  <link
    href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Dancing+Script:wght@700&display=swap"
    rel="stylesheet">

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Lottie Player -->
  <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>

  <style>
    :root {
      --primary: #4F46E5;
      --primary-dark: #4338ca;
      --secondary: #ec4899;
      /* Pinkish accent */
      --bg-color: #f3f4f6;
      --card-bg: #ffffff;
      --text-main: #1f2937;
      --text-light: #6b7280;
      --border-color: #e5e7eb;
      --error: #ef4444;
      --success: #10b981;
      --warning: #f59e0b;
    }

    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Outfit', sans-serif;
      background-color: var(--bg-color);
      margin: 0;
      padding: 20px;
      color: var(--text-main);
      line-height: 1.5;
    }

    .approved {
      color: var(--success);
    }

    .pending {
      color: var(--warning);
    }

    .rejected {
      color: var(--error);
    }

    /* Layout Container */
    .main-container {
      max-width: 1000px;
      margin: 0 auto;
      background: var(--card-bg);
      border-radius: 16px;
      box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      position: relative;
    }

    /* Header Section */
    .header-section {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      padding: 40px 20px;
      text-align: center;
      position: relative;
    }

    .logo-container img {
      max-height: 100px;
      margin-bottom: 15px;
      filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
    }

    .header-title {
      font-size: 24px;
      font-weight: 700;
      margin: 0;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .header-subtitle {
      font-size: 16px;
      opacity: 0.9;
      margin-top: 5px;
      font-weight: 300;
    }

    /* Photo Badge (Floating) */
    .photo-badge {
      position: absolute;
      top: 20px;
      right: 20px;
      background: rgba(255, 255, 255, 0.95);
      padding: 10px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      width: 130px;
      backdrop-filter: blur(5px);
      z-index: 10;
      text-align: center;
    }

    .photo-view-box {
      width: 100px;
      height: 130px;
      background: #f0f0f0;
      border: 2px dashed #cbd5e1;
      border-radius: 8px;
      margin: 0 auto 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      position: relative;
      transition: all 0.3s ease;
    }

    .photo-view-box.drag-over {
      border-color: var(--primary);
      background: #e0e7ff;
    }

    .photo-view-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .action-btn-sm {
      width: 100%;
      padding: 6px;
      font-size: 11px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-bottom: 5px;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      transition: transform 0.2s;
    }

    .btn-primary-sm {
      background: var(--primary);
      color: white;
    }

    .btn-secondary-sm {
      background: var(--text-main);
      color: white;
    }

    .btn-danger-sm {
      position: absolute;
      top: -8px;
      right: -8px;
      background: var(--error);
      color: white;
      width: 24px;
      height: 24px;
      border-radius: 50%;
      display: none;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .action-btn-sm:active {
      transform: scale(0.95);
    }

    /* Form Content */
    .form-content {
      padding: 40px;
    }

    /* Form Groups & Grid */
    .details-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .form-group {
      margin-bottom: 15px;
      position: relative;
    }

    .form-label {
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
      font-weight: 600;
      color: var(--text-main);
    }

    .form-label i {
      margin-right: 6px;
      color: var(--primary);
    }

    .required::after {
      content: " *";
      color: var(--error);
    }

    .form-control {
      width: 100%;
      padding: 12px 15px;
      font-size: 15px;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      transition: all 0.3s ease;
      font-family: 'Outfit', sans-serif;
      background: #ffffff;
    }

    select.form-control,
    select.table-input {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23999' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 15px center;
      background-size: 12px;
      padding-right: 40px;
      cursor: pointer;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .form-control[readonly] {
      background-color: #f8fafc;
      color: var(--text-light);
      cursor: not-allowed;
    }

    textarea.form-control {
      resize: vertical;
      min-height: 80px;
    }

    /* Validation Error Message */
    .error-msg {
      font-size: 12px;
      color: var(--error);
      margin-top: 5px;
      display: none;
    }

    .form-control.invalid {
      border-color: var(--error);
    }

    .form-control.invalid+.error-msg {
      display: block;
    }

    /* Section Dividers */
    .section-title {
      font-size: 18px;
      font-weight: 700;
      color: var(--text-main);
      margin: 40px 0 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid var(--border-color);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .section-title i {
      color: var(--secondary);
      background: #fdf2f8;
      padding: 8px;
      border-radius: 8px;
    }

    /* Tables */
    .table-wrapper {
      overflow-x: auto;
      border-radius: 12px;
      border: 1px solid var(--border-color);
      margin-bottom: 20px;
    }

    .styled-table {
      width: 100%;
      border-collapse: collapse;
      min-width: 800px;
      /* Force scroll on small screens */
    }

    .styled-table thead {
      background: var(--text-main);
      color: white;
    }

    .styled-table th,
    .styled-table td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid var(--border-color);
      font-size: 14px;
    }

    .styled-table tbody tr:hover {
      background-color: #f9fafb;
    }

    .table-input {
      width: 100%;
      padding: 8px;
      border: 1px solid var(--border-color);
      border-radius: 6px;
      font-size: 13px;
    }

    .check-center {
      text-align: center;
    }

    .check-center input {
      width: 18px;
      height: 18px;
      cursor: pointer;
      accent-color: var(--primary);
    }

    /* Signature & Challan Area */
    .upload-zone {
      background: #f8fafc;
      border: 2px dashed var(--border-color);
      border-radius: 12px;
      padding: 20px;
      text-align: center;
      transition: all 0.3s;
    }

    .upload-zone:hover {
      border-color: var(--primary);
      background: #eef2ff;
    }

    .signature-pad-container canvas {
      background: white;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      cursor: crosshair;
    }

    .signature-type-select {
      margin-bottom: 15px;
    }

    /* Typed Signature */
    #typedSignature {
      font-family: 'Dancing Script', cursive;
      font-size: 24px;
    }

    /* Guidelines Box */
    .guidelines-box {
      background: #fffbeb;
      border: 1px solid #fcd34d;
      border-radius: 12px;
      padding: 20px;
      margin: 30px 0;
      font-size: 14px;
    }

    .guidelines-box h3 {
      color: #b45309;
      margin-top: 0;
      font-size: 16px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .guidelines-box ul {
      margin: 10px 0 0;
      padding-left: 20px;
      color: #78350f;
    }

    .guidelines-box li {
      margin-bottom: 6px;
    }

    /* Submit Section */
    .submit-section {
      background: #f9fafb;
      padding: 30px;
      text-align: center;
      border-top: 1px solid var(--border-color);
      border-radius: 0 0 16px 16px;
    }

    .captcha-row {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 15px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .btn-lg {
      padding: 14px 40px;
      font-size: 18px;
      font-weight: 700;
      border-radius: 50px;
      border: none;
      cursor: pointer;
      transition: all 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-submit {
      background: linear-gradient(135deg, #10b981, #059669);
      color: white;
      width: 100%;
      max-width: 400px;
    }

    .btn-submit:disabled {
      background: #d1d5db;
      cursor: not-allowed;
      box-shadow: none;
    }

    .btn-submit:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 10px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-refresh {
      background: var(--secondary);
      color: white;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: none;
      cursor: pointer;
    }

    /* Selection Modal */
    #selectionModal {
      display: flex;
      position: fixed;
      z-index: 2000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.85);
      backdrop-filter: blur(8px);
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .selection-card {
      background: white;
      padding: 40px;
      border-radius: 24px;
      max-width: 600px;
      width: 100%;
      text-align: center;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
      animation: modalSlideUp 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes modalSlideUp {
      from {
        opacity: 0;
        transform: translateY(50px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .selection-title {
      font-size: 28px;
      font-weight: 800;
      color: var(--text-main);
      margin-bottom: 10px;
    }

    .selection-subtitle {
      color: var(--text-light);
      margin-bottom: 30px;
    }

    .selection-options {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    .selection-btn {
      padding: 30px 20px;
      border: 2px solid var(--border-color);
      border-radius: 16px;
      cursor: pointer;
      transition: all 0.3s;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 15px;
      background: #fafafa;
    }

    .selection-btn:hover {
      border-color: var(--primary);
      background: #f0f4ff;
      transform: translateY(-5px);
    }

    .selection-btn i {
      font-size: 40px;
      color: var(--primary);
    }

    .selection-btn span {
      font-weight: 700;
      font-size: 16px;
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(4px);
      align-items: center;
      justify-content: center;
    }

    .modal-content {
      background: white;
      padding: 30px;
      border-radius: 16px;
      max-width: 800px;
      width: 90%;
      max-height: 90vh;
      overflow-y: auto;
      position: relative;
      animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .close-modal {
      position: absolute;
      top: 15px;
      right: 20px;
      font-size: 24px;
      cursor: pointer;
      color: var(--text-light);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .header-section {
        padding-top: 180px;
      }

      /* Space for badge */
      .photo-badge {
        left: 50%;
        transform: translateX(-50%);
        top: 20px;
        display: flex;
        flex-direction: row;
        align-items: center;
        width: 90%;
        max-width: 350px;
        gap: 15px;
        text-align: left;
        justify-content: space-between;
      }

      .photo-view-box {
        margin: 0;
        width: 80px;
        height: 100px;
      }

      .badge-controls {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 5px;
      }

      .details-grid {
        grid-template-columns: 1fr;
      }

      .captcha-row {
        flex-direction: column;
      }
    }
  </style>
</head>

<body>

  <?php if (!$already_submitted || $can_edit): ?>
    <!-- Year Selection Modal -->
    <div id="selectionModal" <?php if ($can_edit)
      echo 'style="display:none;"'; ?>>
      <div class="selection-card">
        <h2 class="selection-title">Welcome to Exam Registration</h2>
        <p class="selection-subtitle">Please select your current status to continue</p>
        <div class="selection-options">
          <div class="selection-btn" onclick="selectFlow('new')">
            <i class="fas fa-baby"></i>
            <span>1st Semester<br>(New Student)</span>
          </div>
          <div class="selection-btn" onclick="selectFlow('old')">
            <i class="fas fa-user-graduate"></i>
            <span>Senior<br>(2nd/3rd Year)</span>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($already_submitted && !$can_edit): ?>
    <div style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
      <?= $status_message ?>
    </div>
  <?php elseif ($current_time < $start_date): ?>
    <div class="main-container" style="padding: 60px 40px; text-align: center;">
      <div style="font-size: 64px; margin-bottom: 20px;">⏳</div>
      <h2 style="color: var(--primary); font-size: 28px; font-weight: 800; margin-bottom: 10px;">Exam Form Live Soon</h2>
      <p style="color: var(--text-light); font-size: 16px; margin-bottom: 25px;">The registration is scheduled to start
        on:</p>
      <div
        style="display: inline-block; background: #EEF2FF; color: #4F46E5; padding: 15px 30px; border-radius: 12px; font-weight: 700; font-size: 18px; border: 1px solid #C7D2FE;">
        <i class="bi bi-calendar-event-fill mr-2"></i>
        <?= date('F j, Y - h:i A', $start_date) ?>
      </div>
      <div style="margin-top: 30px;">
        <a href="dashboard.php" style="color: #6366f1; text-decoration: none; font-weight: 600;"><i
            class="bi bi-arrow-left"></i> Back to Dashboard</a>
      </div>
    </div>
  <?php elseif ($current_time > $end_date): ?>
    <div class="main-container" style="padding: 60px 40px; text-align: center;">
      <div style="font-size: 64px; margin-bottom: 20px;">⛔</div>
      <h2 style="color: #EF4444; font-size: 28px; font-weight: 800; margin-bottom: 10px;">Submission Closed</h2>
      <p style="color: var(--text-light); font-size: 16px; margin-bottom: 25px;">The deadline for submission has already
        passed on:</p>
      <div
        style="display: inline-block; background: #FEF2F2; color: #EF4444; padding: 15px 30px; border-radius: 12px; font-weight: 700; font-size: 18px; border: 1px solid #FECACA;">
        <i class="bi bi-calendar-x-fill mr-2"></i>
        <?= date('F j, Y - h:i A', $end_date) ?>
      </div>
      <div style="margin-top: 30px;">
        <a href="dashboard.php" style="color: #6366f1; text-decoration: none; font-weight: 600;"><i
            class="bi bi-arrow-left"></i> Back to Dashboard</a>
      </div>
    </div>
  <?php else: ?>

    <div class="main-container">
      <form action="php/student_exam_form.php" method="POST" enctype="multipart/form-data" id="mainForm">

        <!-- Header -->
        <div class="header-section">
          <div class="logo-container">
            <img src="img/rgpv diploma.png" alt="RGPV Logo">
          </div>
          <h1 class="header-title">Rajiv Gandhi Proudyogiki Vishwavidyalay</h1>
          <div class="header-subtitle">Polytechnic Division | Exam Registration Form</div>

          <!-- Photo Badge -->
          <div class="photo-badge">
            <button type="button" class="btn-danger-sm" id="deleteBtn">x</button>
            <div class="photo-view-box" id="photoViewer">
              <img id="uploadedImage" src="img/placeholder-user.png" alt="" style="display: none;">
              <span id="dragText" style="font-size: 11px; color: #999; text-align: center;">Photo<br>Drag & Drop</span>
            </div>
            <div class="badge-controls">
              <input type="file" id="photoInput" name="student_photo" hidden accept="image/*">
              <button type="button" class="action-btn-sm btn-primary-sm"
                onclick="document.getElementById('photoInput').click()">
                <i class="bi bi-upload"></i> Upload
              </button>
              <button type="button" class="action-btn-sm btn-secondary-sm" onclick="openCamera()">
                <i class="bi bi-camera"></i> Camera
              </button>
            </div>
          </div>
        </div>

        <div class="form-content">

          <!-- 1. Student Details -->
          <div class="section-title"><i class="fas fa-user-graduate"></i> Student Details</div>
          <div class="details-grid">

            <div class="form-group">
              <label class="form-label">Institute Name</label>
              <input type="text" class="form-control" value="UJJAIN POLYTECHNIC COLLEGE, UJJAIN" readonly>
            </div>
            <div class="form-group">
              <label class="form-label">Institution Code</label>
              <input type="text" class="form-control" value="030" readonly>
            </div>

            <!-- Registration Type -->
            <div class="form-group">
              <label class="form-label required">I am a...</label>
              <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="reg_type" value="new" onclick="toggleRegType('new')" checked>
                  <span class="text-sm">1st Semester (New Student)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="reg_type" value="old" onclick="toggleRegType('old')">
                  <span class="text-sm">Senior (Enrollment Number Obtained)</span>
                </label>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label required" id="rollLabel">Application Number</label>
              <input type="text" class="form-control" name="roll_no" id="rollNoInput"
                value="<?php echo htmlspecialchars($roll_no); ?>" placeholder="Enter Application No. or Enrollment No."
                required>
              <div class="error-msg" id="rollError">Invalid format. Enrollment should be 11 characters (e.g. 22030C04012).
              </div>
            </div>

            <div class="form-group">
              <label class="form-label required">Student Name</label>
              <input type="text" class="form-control" name="student_name"
                value="<?php echo htmlspecialchars($student_name); ?>" required pattern="[A-Za-z\s]+"
                title="Only letters allowed">
            </div>

            <div class="form-group" id="father_container">
              <label class="form-label required">Father's Name & Permanent Address</label>
              <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                  <label style="font-size:12px; color:#666; display:block; margin-bottom:4px;">Father's Name</label>
                  <input type="text" class="form-control" id="father_name_input" placeholder="Father's Name"
                    oninput="updateFatherDetails()">
                </div>
                <div style="flex: 1.5; min-width: 250px;">
                  <label style="font-size:12px; color:#666; display:block; margin-bottom:4px;">Permanent Address</label>
                  <textarea class="form-control" id="father_addr_input" placeholder="Permanent Address" rows="1"
                    style="resize:none; height:46px;" oninput="updateFatherDetails()"></textarea>
                </div>
              </div>
              <input type="hidden" name="father_address" id="father_address_hidden">
            </div>

            <div class="form-group">
              <label class="form-label required">Mobile No</label>
              <input type="tel" class="form-control" name="mobile_no"
                value="<?php echo htmlspecialchars($master_phone); ?>" readonly
                style="background-color: #f3f4f6; cursor: not-allowed;">
              <p style="font-size: 11px; color: #666; margin-top: 4px;">Primary contact from profile</p>
            </div>

            <div class="form-group">
              <label class="form-label required">Email ID</label>
              <input type="email" class="form-control" name="email_id"
                value="<?php echo htmlspecialchars($user_email); ?>" readonly>
            </div>
          </div>

          <!-- 2. Academic Details -->
          <div class="section-title"><i class="fas fa-university"></i> Academic Details</div>
          <div class="details-grid">
            <div class="form-group">
              <label class="form-label required">Course/Department</label>
              <select class="form-control" name="course" required>
                <option value="">Select Branch</option>
                <option value="Diploma in Mechanical Engineering" <?= ($master_course == 'Diploma in Mechanical Engineering' || $master_course == 'Mechanical Engineering') ? 'selected' : '' ?>>Mechanical Engineering</option>
                <option value="Diploma in Electrical Engineering" <?= ($master_course == 'Diploma in Electrical Engineering' || $master_course == 'Electrical Engineering') ? 'selected' : '' ?>>Electrical Engineering</option>
                <option value="Diploma in Chemical Engineering" <?= ($master_course == 'Diploma in Chemical Engineering' || $master_course == 'Chemical Engineering') ? 'selected' : '' ?>>Chemical Engineering</option>
                <option value="Diploma in Information Technology" <?= ($master_course == 'Diploma in Information Technology' || $master_course == 'Information Technology') ? 'selected' : '' ?>>Information Technology</option>
                <option value="Diploma in Electronics & Tele-Communication Engineering" <?= ($master_course == 'Diploma in Electronics & Tele-Communication Engineering' || strpos($master_course, 'Electronics') !== false) ? 'selected' : '' ?>>Electronics & Tele-Communication Engineering</option>
                <option value="Diploma in Computer Science & Engineering" <?= ($master_course == 'Diploma in Computer Science & Engineering' || strpos($master_course, 'Computer') !== false) ? 'selected' : '' ?>>Computer Science &
                  Engineering</option>
                <option value="Diploma in Construction Technology & Management" <?= ($master_course == 'Diploma in Construction Technology & Management') ? 'selected' : '' ?>>Construction Technology & Management
                </option>
                <option value="Diploma in Plastic Technology" <?= ($master_course == 'Diploma in Plastic Technology') ? 'selected' : '' ?>>Plastic Technology</option>
                <option value="Diploma in Refinery & Petrochemical Engineering" <?= ($master_course == 'Diploma in Refinery & Petrochemical Engineering') ? 'selected' : '' ?>>Refinery & Petrochemical Engineering</option>
                <option value="Other" <?= ($master_course == 'Other') ? 'selected' : '' ?>>Other</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label required">Type of Course</label>
              <div style="position: relative;">
                <select class="form-control" name="course_type">
                  <option value="Grading">Grading</option>
                  <option value="Non Grading">Non Grading</option>
                  <option value="Grading-ITI">Grading-ITI</option>
                  <option value="Non-Grading ITI">Non-Grading ITI</option>
                  <option value="PTDC">PTDC</option>
                </select>
              </div>
            </div>

            <div class="form-group" id="sem_container">
              <label class="form-label required">Semester</label>
              <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                  <label style="font-size:12px; color:#666; display:block; margin-bottom:4px;">Category</label>
                  <select class="form-control" id="sem_type_input" onchange="updateSemesterString()">
                    <option value="">Select</option>
                    <option value="Regular">Regular</option>
                    <option value="Ex">Ex</option>
                  </select>
                </div>
                <div style="flex: 1;">
                  <label style="font-size:12px; color:#666; display:block; margin-bottom:4px;">Semester</label>
                  <select class="form-control" id="sem_num_input" onchange="updateSemesterString()">
                    <option value="">Select</option>
                    <option value="1">1st</option>
                    <option value="2">2nd</option>
                    <option value="3">3rd</option>
                    <option value="4">4th</option>
                    <option value="5">5th</option>
                    <option value="6">6th</option>
                  </select>
                </div>
              </div>
              <input type="hidden" name="current_semester" id="current_semester_hidden">
              <div class="error-msg">Please select both Category and Semester</div>
            </div>

            <div class="form-group">
              <label class="form-label required">Category</label>
              <div style="position: relative;">
                <select class="form-control" name="category">
                  <option value="GEN">General</option>
                  <option value="OBC">OBC</option>
                  <option value="SC">SC</option>
                  <option value="ST">ST</option>
                </select>
              </div>
            </div>



            <div class="form-group" id="fee_container">
              <label class="form-label required">Admission Fees Receipt Details</label>
              <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 120px;">
                  <label style="font-size:12px; color:#666; display:block; margin-bottom:4px;">Amount</label>
                  <input type="text" class="form-control" id="fee_amount" placeholder="e.g. 11980"
                    oninput="updateFeeString()">
                </div>
                <div style="flex: 1; min-width: 120px;">
                  <label style="font-size:12px; color:#666; display:block; margin-bottom:4px;">Receipt No</label>
                  <input type="text" class="form-control" id="fee_receipt" placeholder="e.g. 008919"
                    oninput="updateFeeString()">
                </div>
                <div style="flex: 1; min-width: 120px;">
                  <label style="font-size:12px; color:#666; display:block; margin-bottom:4px;">Date</label>
                  <input type="date" class="form-control" id="fee_date" oninput="updateFeeString()">
                </div>
              </div>
              <input type="hidden" name="admission_fees" id="admission_fees_hidden">
              <div class="error-msg">All fields required. Format Example: 11980/- 008919 8/9/24</div>
            </div>
          </div>

          <!-- 3. Subjects -->
          <div class="section-title"><i class="fas fa-book-open"></i> Regular Subjects</div>
          <div class="table-wrapper">
            <table class="styled-table">
              <thead>
                <tr>
                  <th width="5%">#</th>
                  <th width="35%">Subject Name</th>
                  <th width="15%">Sem</th>
                  <th width="15%">Code</th>
                  <th width="10%" class="check-center">Th</th>
                  <th width="10%" class="check-center">Pr</th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i = 1; $i <= 6; $i++): ?>
                  <tr>
                    <td><?= $i ?></td>
                    <td><input type="text" name="subject<?= $i ?>" class="table-input"></td>
                    <td>
                      <select name="sem<?= $i ?>" class="table-input">
                        <option value="" disabled selected>Sem</option>
                        <?php for ($s = 1; $s <= 6; $s++)
                          echo "<option value='$s'>$s</option>"; ?>
                      </select>
                    </td>
                    <td><input type="text" name="paper_code<?= $i ?>" class="table-input"></td>
                    <td class="check-center"><input type="checkbox" name="theory<?= $i ?>"></td>
                    <td class="check-center"><input type="checkbox" name="practical<?= $i ?>"></td>
                  </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </div>

          <div class="section-title"><i class="fas fa-history"></i> Ex. Subjects</div>
          <div class="table-wrapper">
            <table class="styled-table">
              <thead>
                <tr>
                  <th width="5%">#</th>
                  <th width="35%">Subject Name</th>
                  <th width="15%">Sem</th>
                  <th width="15%">Code</th>
                  <th width="10%" class="check-center">Th</th>
                  <th width="10%" class="check-center">Pr</th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i = 1; $i <= 7; $i++): ?>
                  <tr>
                    <td><?= $i ?></td>
                    <td><input type="text" name="exsubject<?= $i ?>" class="table-input"></td>
                    <td>
                      <select name="exsem<?= $i ?>" class="table-input">
                        <option value="" disabled selected>Sem</option>
                        <?php for ($s = 1; $s <= 6; $s++)
                          echo "<option value='$s'>$s</option>"; ?>
                      </select>
                    </td>
                    <td><input type="text" name="expaper_code<?= $i ?>" class="table-input"></td>
                    <td class="check-center"><input type="checkbox" name="extheory<?= $i ?>"></td>
                    <td class="check-center"><input type="checkbox" name="expractical<?= $i ?>"></td>
                  </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </div>

          <!-- 4. Date & Signature -->
          <div class="details-grid">
            <div class="form-group">
              <label class="form-label required">Exam Form Date</label>
              <input type="date" class="form-control" name="exam_date" id="examDate" required>
            </div>
          </div>

          <div class="section-title"><i class="fas fa-file-signature"></i> Verification & Signature</div>

          <!-- Signature Block -->
          <div class="upload-zone" id="signatureForm">
            <select id="signatureType" name="signature_type" class="form-control signature-type-select">
              <option value="">-- Select Signature Method --</option>
              <option value="upload">Upload Image</option>
              <option value="draw">Draw on Screen</option>
              <option value="typed">Type Name</option>
            </select>

            <!-- Upload -->
            <div id="uploadSignature" class="signature-option" style="display:none;">
              <p>Upload PNG/JPG (Max 2MB)</p>
              <input type="file" name="student_signature" class="form-control" accept="image/png, image/jpeg">
            </div>

            <!-- Draw -->
            <div id="drawSignature" class="signature-option" style="display:none;">
              <div class="signature-pad-container">
                <canvas id="signaturePad" width="300" height="150"></canvas>
              </div>
              <input type="hidden" name="signatureImage" id="signatureImage">
              <button type="button" class="action-btn-sm btn-secondary-sm" onclick="clearCanvas()"
                style="width: 100px; margin: 10px auto;">Clear</button>
            </div>

            <!-- Type -->
            <div id="typedSignatureBox" class="signature-option" style="display:none;">
              <input type="text" id="typedSignature" class="form-control" onblur="saveTypedSignature()"
                placeholder="Type your full name here">
              <input type="hidden" name="typedSignature" id="typedSignatureHidden">
            </div>
          </div>

          <!-- Challan Upload -->
          <div class="section-title"><i class="fas fa-receipt"></i> Fee Challan Upload</div>
          <div class="upload-zone" style="text-align: left; background: #fff7ed; border-color: #fdba74;">
            <div id="challan-list">
              <!-- Challan 1 -->
              <div class="form-group">
                <label class="form-label">Challan 1 *</label>
                <input type="file" name="challan[]" class="form-control" accept=".pdf, .jpg, .png"
                  onchange="displayUploadedFile(event, 1)" required>
                <div id="preview-challan-1"></div>
              </div>
            </div>
            <div id="additional-challan-fields"></div>
            <button type="button" class="action-btn-sm btn-secondary-sm" onclick="addChallanField()"
              style="width: auto; padding: 10px 20px;">
              <i class="bi bi-plus-circle"></i> Add More Challan
            </button>
          </div>

          <!-- Result Upload Section (Moved here) -->
          <div class="section-title"><i class="fas fa-file-invoice"></i> Academic Results Verification</div>
          <div class="form-group" id="result_upload_container">
            <div class="upload-zone" style="text-align: left; background: #eff6ff; border-color: #bfdbfe; padding: 20px;">
              <label class="form-label" id="result_label" style="font-size: 16px; margin-bottom: 15px;">Academic
                Results</label>
              <div id="result-list-container">
                <!-- Default Result Field -->
                <div class="form-group mb-4">
                  <label class="text-[11px] font-bold text-slate-500 uppercase" id="first_result_label">Previous Result
                    *</label>
                  <input type="file" name="results[]" class="form-control" accept=".pdf, image/*"
                    onchange="displayResultPreview(event, 0)">
                  <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider pl-1 mt-1 italic"
                    id="result_hint">Optional for 1st/2nd Sem</p>
                </div>
              </div>

              <div id="additional-results"></div>

              <button type="button" class="action-btn-sm btn-primary-sm" onclick="addResultField()"
                style="width: auto; padding: 10px 20px; background: #2563eb;">
                <i class="bi bi-plus-circle"></i> <span id="add_result_text">Add More Results</span>
              </button>
            </div>
          </div>

          <!-- Guidelines -->
          <div class="guidelines-box">
            <h3><i class="fas fa-exclamation-triangle"></i> Important Guidelines</h3>
            <ul>
              <li>Fields marked with (<strong>*</strong>) are mandatory.</li>
              <li>Photo: Clear passport size, light background, max 2MB.</li>
              <li>Roll No: Must match <code>22030C040**</code> format.</li>
              <li>Challan: Ensure receipt number is clearly visible.</li>
              <li>Double-check subjects before submitting. Edits not allowed after submission.</li>
            </ul>
          </div>

        </div> <!-- End Form Content -->

        <!-- Submit Section -->
        <div class="submit-section">
          <!-- Captcha -->
          <div class="captcha-row">
            <img src="php/captcha.php" alt="CAPTCHA" id="captchaImage" style="height: 50px; border-radius: 6px;">
            <button type="button" class="btn-refresh" id="refresh-captcha"><i class="fas fa-sync-alt"></i></button>
            <input type="text" name="captcha" class="form-control" placeholder="Enter Code" style="width: 150px;"
              required>
          </div>

          <div class="form-group" style="margin-bottom: 25px;">
            <label style="cursor: pointer; font-weight: 500;">
              <input type="checkbox" id="agreeCheckbox" style="transform: scale(1.2); margin-right: 10px;">
              I verify that all the information provided above is correct.
            </label>
          </div>

          <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <button type="button" class="btn-lg"
              style="background:#fff; border: 2px solid var(--primary); color: var(--primary);"
              onclick="showReviewModal()">Review Form</button>
            <button type="submit" class="btn-lg btn-submit flex items-center justify-center gap-3" id="finalSubmitBtn"
              disabled>
              <span id="submitText">Submit Application</span>
              <div id="submitSpinner"
                class="hidden w-6 h-6 border-4 border-white/30 border-t-white rounded-full animate-spin"></div>
            </button>
          </div>

          <p style="margin-top: 20px;">
            <a href="dashboard.php" style="text-decoration: none; color: var(--text-light); font-size: 14px;">&larr;
              Return to Dashboard</a>
          </p>
        </div>

        <!-- Hidden Camera Modal -->
        <div id="cameraModal" class="modal">
          <div class="modal-content" style="width: 400px; text-align: center;">
            <h3 style="margin-bottom: 20px;">Capture Photo</h3>
            <video id="videoStream" autoplay playsinline
              style="width: 100%; border-radius: 12px; background: black; box-shadow: 0 4px 12px rgba(0,0,0,0.2);"></video>
            <div style="margin-top: 25px; display: flex; gap: 12px; justify-content: center;">
              <button type="button" class="action-btn-sm btn-primary-sm" style="width: auto; padding: 12px 24px;"
                onclick="capturePhoto()">
                <i class="bi bi-camera-fill"></i> Capture
              </button>
              <button type="button" class="action-btn-sm btn-secondary-sm" style="width: auto; padding: 12px 24px;"
                onclick="closeCamera()">Cancel</button>
            </div>
          </div>
        </div>

        <!-- Review Modal -->
        <div id="reviewModal" class="modal">
          <div class="modal-content" style="max-width: 600px; border-radius: 20px;">
            <span class="close-modal" onclick="closeReviewModal()">&times;</span>
            <h2
              style="color: var(--primary); border-bottom: 2px solid #f3f4f6; padding-bottom: 15px; margin-bottom: 20px; font-weight: 800;">
              Review Application
            </h2>
            <div id="reviewContent" style="background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
            </div>
            <div style="text-align: center;">
              <button type="button" class="btn-lg btn-primary-sm"
                style="width: 100%; max-width: 300px; padding: 15px; border-radius: 12px;"
                onclick="closeReviewModal()">Proceed to Submit</button>
            </div>
          </div>
        </div>

      </form>
    </div> <!-- End Main Container -->

  <?php endif; ?>

  <!-- Scripts -->
  <script>
    const masterRoll = '<?php echo $master_roll; ?>';
    const masterMobile = '<?php echo $master_phone; ?>';

    // Selection Flow Logic
    window.selectFlow = function (type) {
      const modal = document.getElementById('selectionModal');
      const regNew = document.querySelector('input[name="reg_type"][value="new"]');
      const regOld = document.querySelector('input[name="reg_type"][value="old"]');
      const rollInput = document.getElementById('rollNoInput');

      if (type === 'new') {
        regNew.checked = true;
        toggleRegType('new');
        rollInput.value = ""; // Clear for manual entry
      } else {
        regOld.checked = true;
        toggleRegType('old');
        rollInput.value = masterRoll; // Pre-fill from database
      }
      modal.style.display = 'none';
    }

    // Registration Type Toggling
    function toggleRegType(type) {
      const rollLabel = document.getElementById('rollLabel');
      const rollInput = document.getElementById('rollNoInput');
      const rollError = document.getElementById('rollError');

      if (type === 'new') {
        rollLabel.textContent = 'Application Number';
        rollInput.placeholder = 'Enter Application/Registration Number';
        rollError.textContent = 'Application Number is required';
        // Only clear if it was the master roll (to prevent accidental loss of manual input)
        if (rollInput.value === masterRoll) {
          rollInput.value = "";
        }
      } else {
        rollLabel.textContent = 'Enrollment Number';
        rollInput.placeholder = 'e.g. 22030C04012';
        rollError.textContent = 'Invalid Enrollment format (e.g. 22030C04012)';
        // Only fill if it's empty or doesn't match a new student pattern
        if (rollInput.value === "") {
          rollInput.value = masterRoll;
        }
      }
    }

    <?php if ($can_edit): ?>
      document.addEventListener("DOMContentLoaded", function () {
        const data = <?php echo json_encode($student_data); ?>;

        // Roll No & Type
        const regType = data.roll_no.length === 11 ? 'old' : 'new';
        const regRadio = document.querySelector(`input[name="reg_type"][value="${regType}"]`);
        if (regRadio) regRadio.checked = true;
        toggleRegType(regType);
        document.getElementById('rollNoInput').value = data.roll_no;

        // Name & Contact
        document.querySelector("input[name='student_name']").value = data.student_name;
        document.querySelector("input[name='mobile_no']").value = data.mobile_no;

        // Father Details (Split "Name, Addr")
        if (data.father_address) {
          const parts = data.father_address.split(', ');
          document.getElementById('father_name_input').value = parts[0] || '';
          document.getElementById('father_addr_input').value = parts.slice(1).join(', ') || '';
          updateFatherDetails();
        }

        // Course & Semester
        document.querySelector("select[name='course_type']").value = data.course_type;
        document.querySelector("select[name='category']").value = data.category;

        if (data.current_semester) {
          const sParts = data.current_semester.split(' ');
          if (sParts.length >= 2) {
            document.getElementById('sem_type_input').value = sParts[0];
            document.getElementById('sem_num_input').value = sParts[1].replace(/\D/g, '');
            updateSemesterString();
          }
        }

        // Fees (Split "Amt/- Rcpt Date")
        if (data.admission_fees) {
          const fParts = data.admission_fees.split(' ');
          if (fParts.length >= 3) {
            document.getElementById('fee_amount').value = fParts[0].replace(/\/\-$/, '');
            document.getElementById('fee_receipt').value = fParts[1];
            const dParts = fParts[2].split('/'); // d/m/yy
            if (dParts.length === 3) {
              const fullYear = "20" + dParts[2];
              const formattedDate = `${fullYear}-${dParts[1].padStart(2, '0')}-${dParts[0].padStart(2, '0')}`;
              document.getElementById('fee_date').value = formattedDate;
            }
            updateFeeString();
          }
        }

        // Subjects
        if (data.subjects) {
          try {
            const subs = JSON.parse(data.subjects);
            subs.forEach((s, i) => {
              const idx = i + 1;
              const sInput = document.querySelector(`input[name="subject${idx}"]`);
              if (sInput) {
                sInput.value = s.subject;
                document.querySelector(`select[name="sem${idx}"]`).value = s.semester;
                document.querySelector(`input[name="paper_code${idx}"]`).value = s.paper_code;
                document.querySelector(`input[name="theory${idx}"]`).checked = s.theory == 1;
                document.querySelector(`input[name="practical${idx}"]`).checked = s.practical == 1;
              }
            });
          } catch (e) { }
        }

        // Ex Subjects
        if (data.ex_subjects) {
          try {
            const exSubs = JSON.parse(data.ex_subjects);
            exSubs.forEach((s, i) => {
              const idx = i + 1;
              const esInput = document.querySelector(`input[name="exsubject${idx}"]`);
              if (esInput) {
                esInput.value = s.subject;
                document.querySelector(`select[name="exsem${idx}"]`).value = s.semester;
                document.querySelector(`input[name="expaper_code${idx}"]`).value = s.paper_code;
                document.querySelector(`input[name="extheory${idx}"]`).checked = s.theory == 1;
                document.querySelector(`input[name="expractical${idx}"]`).checked = s.practical == 1;
              }
            });
          } catch (e) { }
        }

        // Photo
        if (data.student_photo) {
          document.getElementById('uploadedImage').src = 'php/' + data.student_photo;
          document.getElementById('uploadedImage').style.display = 'block';
          document.getElementById('dragText').style.display = 'none';
          const delBtn = document.getElementById('deleteBtn');
          if (delBtn) delBtn.style.display = 'flex';
        }

        // Exam Date
        document.getElementById('examDate').value = data.exam_date;

      });
    <?php endif; ?>

    // Today's date logic
    document.addEventListener("DOMContentLoaded", function () {
      let today = new Date();
      today.setMinutes(today.getMinutes() - today.getTimezoneOffset());
      let formattedDate = today.toISOString().split('T')[0];
      let examDateInput = document.getElementById("examDate");
      if (examDateInput) {
        examDateInput.value = formattedDate;
        examDateInput.min = formattedDate;
        examDateInput.max = formattedDate;
      }
    });

    // Captcha
    document.getElementById("refresh-captcha")?.addEventListener("click", function () {
      var captchaImage = document.getElementById("captchaImage");
      captchaImage.src = "php/captcha.php?" + Date.now();
    });

    // Form Validation & Interaction
    const form = document.getElementById("mainForm");
    const agreeCheckbox = document.getElementById("agreeCheckbox");
    const finalSubmitBtn = document.getElementById("finalSubmitBtn");
    const submitText = document.getElementById("submitText");
    const submitSpinner = document.getElementById("submitSpinner");

    if (agreeCheckbox && finalSubmitBtn) {
      agreeCheckbox.addEventListener("change", () => {
        finalSubmitBtn.disabled = !agreeCheckbox.checked;
      });
    }

    function showError(input, message) {
      // Find next sibling error message or alert
      // Using a simple alert fallback if specific error msg div not found
      // But we have .error-msg divs in HTML
      const errorDiv = input.parentElement.querySelector('.error-msg');
      if (errorDiv) {
        errorDiv.style.display = 'block';
        input.classList.add('invalid');
        errorDiv.textContent = message;
      } else {
        alert(message);
      }
    }

    function clearErrors() {
      document.querySelectorAll('.error-msg').forEach(el => el.style.display = 'none');
      document.querySelectorAll('.form-control').forEach(el => el.classList.remove('invalid'));
    }

    form?.addEventListener("submit", function (event) {
      clearErrors();
      let isValid = true;

      // 1. Roll No / Application No Validation
      const regType = document.querySelector('input[name="reg_type"]:checked').value;
      const rollInput = document.getElementById('rollNoInput');
      const rollValue = rollInput.value.trim();

      if (regType === 'old') {
        // Enrollment pattern: 11 characters (typical for MP Polytechnic)
        const enrollmentRegex = /^[A-Z0-9]{11}$/i;
        if (!enrollmentRegex.test(rollValue)) {
          showError(rollInput, "Enrollment Number must be 11 characters (e.g. 22030C04012)");
          isValid = false;
        }
      } else {
        // Application ID: Usually non-standard but shouldn't be too short
        if (rollValue.length < 5) {
          showError(rollInput, "Please enter a valid Application/Registration Number");
          isValid = false;
        }
      }

      // 2. Student Name
      const studentName = document.querySelector("input[name='student_name']");
      if (studentName.value.trim().length < 3 || !/^[A-Za-z\s]+$/.test(studentName.value)) {
        showError(studentName, "Please enter a valid name (min 3 characters, alphabets only)");
        isValid = false;
      }

      // 3. Mobile
      const mobile = document.querySelector("input[name='mobile_no']");
      if (!mobile.value.match(/^[6-9]\d{9}$/)) {
        showError(mobile, "Enter valid 10-digit mobile number starting with 6-9");
        isValid = false;
      }

      // 4. Semester Validation
      const semHidden = document.getElementById("current_semester_hidden");
      if (!semHidden.value) {
        const semContainer = document.getElementById("sem_container");
        const errDiv = semContainer.querySelector('.error-msg');
        if (errDiv) errDiv.style.display = 'block';
        isValid = false;
      }

      // 5. Father's Name & Address Validation
      const fatherNameInput = document.getElementById('father_name_input');
      const fatherAddrInput = document.getElementById('father_addr_input');
      if (fatherNameInput.value.trim().length < 3) {
        showError(fatherNameInput, "Father's name is required (min 3 chars)");
        isValid = false;
      }
      if (fatherAddrInput.value.trim().length < 10) {
        showError(fatherAddrInput, "Complete permanent address is required (min 10 chars)");
        isValid = false;
      }

      // 6. Admission Fees Validation
      const feeAmt = document.getElementById('fee_amount');
      const feeRcpt = document.getElementById('fee_receipt');
      const feeDate = document.getElementById('fee_date');
      const feesHidden = document.getElementById("admission_fees_hidden");
      const feesPattern = /^\d+\/-\s.+\s\d{1,2}\/\d{1,2}\/\d{2}$/;

      if (!feesHidden.value || !feesPattern.test(feesHidden.value)) {
        const feeContainer = document.getElementById("fee_container");
        const errDiv = feeContainer.querySelector('.error-msg');
        if (errDiv) errDiv.style.display = 'block';

        if (!feeAmt.value) showError(feeAmt, "Required");
        if (!feeRcpt.value) showError(feeRcpt, "Required");
        if (!feeDate.value) showError(feeDate, "Required");
        isValid = false;
      }

      // 7. Regular Subjects Validation
      let regularSubCount = 0;
      for (let i = 1; i <= 6; i++) {
        const sub = document.querySelector(`input[name="subject${i}"]`);
        const code = document.querySelector(`input[name="paper_code${i}"]`);
        if (sub.value.trim() !== "") {
          regularSubCount++;
          if (code.value.trim() === "") {
            alert(`Paper code missing for subject ${i}`);
            isValid = false;
          }
        }
      }
      if (regularSubCount === 0 && document.getElementById('sem_type_input').value === 'Regular') {
        alert("Please enter at least one regular subject");
        isValid = false;
      }

      // 8. Photo
      const uploadedImage = document.getElementById("uploadedImage");
      if (uploadedImage.style.display === "none") {
        alert("Please upload or capture a student photo.");
        isValid = false;
      }

      // 9. Signature
      const sigType = document.getElementById("signatureType").value;
      if (!sigType) {
        alert("Please select a verification signature method.");
        isValid = false;
      }

      if (!isValid) {
        event.preventDefault();
        // Scroll to first error
        const firstError = document.querySelector('.invalid');
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      } else {
        finalSubmitBtn.disabled = true;
        submitText.textContent = "Submitting Application...";
        submitSpinner.classList.remove("hidden");
      }
    });

    // Signature Logic
    function showSignatureField() {
      const type = document.getElementById("signatureType").value;
      document.querySelectorAll('.signature-option').forEach(el => el.style.display = 'none');
      if (type === 'upload') document.getElementById('uploadSignature').style.display = 'block';
      if (type === 'draw') document.getElementById('drawSignature').style.display = 'block';
      if (type === 'typed') document.getElementById('typedSignatureBox').style.display = 'block';
    }
    document.getElementById("signatureType")?.addEventListener("change", showSignatureField);

    // Canvas Drawing
    const canvas = document.getElementById("signaturePad");
    if (canvas) {
      const ctx = canvas.getContext("2d");
      let drawing = false;

      // Helper for coordinates
      const getPos = (e) => {
        const rect = canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return { x: clientX - rect.left, y: clientY - rect.top };
      }

      const start = (e) => {
        e.preventDefault();
        drawing = true;
        ctx.beginPath();
        const pos = getPos(e);
        ctx.moveTo(pos.x, pos.y);
      };

      const move = (e) => {
        e.preventDefault();
        if (!drawing) return;
        const pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
      };

      const end = () => { drawing = false; saveCanvasSignature(); };

      canvas.addEventListener("mousedown", start);
      canvas.addEventListener("mousemove", move);
      canvas.addEventListener("mouseup", end);
      canvas.addEventListener("mouseleave", end);
      canvas.addEventListener("touchstart", start);
      canvas.addEventListener("touchmove", move);
      canvas.addEventListener("touchend", end);
    }

    window.clearCanvas = function () {
      const ctx = canvas.getContext("2d");
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      document.getElementById("signatureImage").value = "";
    }

    window.saveCanvasSignature = function () {
      document.getElementById("signatureImage").value = canvas.toDataURL("image/png");
    }

    window.saveTypedSignature = function () {
      document.getElementById("typedSignatureHidden").value = document.getElementById("typedSignature").value;
    }

    // Camera Logic
    function openCamera() {
      const modal = document.getElementById('cameraModal');
      const video = document.getElementById('videoStream');
      if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true }).then(stream => {
          modal.style.display = 'flex';
          video.srcObject = stream;
          video.play();
        }).catch(e => alert("Camera Error: " + e.message));
      } else {
        alert("Camera not supported on this browser");
      }
    }

    function closeCamera() {
      const video = document.getElementById('videoStream');
      if (video.srcObject) {
        video.srcObject.getTracks().forEach(t => t.stop());
        video.srcObject = null;
      }
      document.getElementById('cameraModal').style.display = 'none';
    }

    function capturePhoto() {
      const video = document.getElementById('videoStream');
      const canvas = document.createElement('canvas');
      // Aspect ratio 100x130
      canvas.width = 300;
      canvas.height = 390;
      const ctx = canvas.getContext('2d');
      // Simple draw (can improve cropping logc if needed)
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

      const dataUrl = canvas.toDataURL('image/png');
      document.getElementById('uploadedImage').src = dataUrl;
      document.getElementById('uploadedImage').style.display = 'block';
      document.getElementById('dragText').style.display = 'none';
      document.getElementById('deleteBtn').style.display = 'flex';

      // Create file object for input
      fetch(dataUrl).then(res => res.blob()).then(blob => {
        const file = new File([blob], "webcam_capture.png", { type: "image/png" });
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('photoInput').files = dt.files;
      });

      closeCamera();
    }

    // Image Upload Display
    const photoInput = document.getElementById("photoInput");
    photoInput?.addEventListener("change", function () {
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          document.getElementById('uploadedImage').src = e.target.result;
          document.getElementById('uploadedImage').style.display = 'block';
          document.getElementById('dragText').style.display = 'none';
          document.getElementById('deleteBtn').style.display = 'flex';
        };
        reader.readAsDataURL(this.files[0]);
      }
    });

    document.getElementById("deleteBtn")?.addEventListener("click", function () {
      document.getElementById('uploadedImage').src = "";
      document.getElementById('uploadedImage').style.display = 'none';
      document.getElementById('dragText').style.display = 'block';
      this.style.display = 'none';
      document.getElementById('photoInput').value = "";
    });

    // Challan Handling
    let challanCount = 1;
    function addChallanField() {
      if (challanCount >= 7) { alert("Max 7 Challans"); return; }
      challanCount++;
      const div = document.createElement('div');
      div.className = 'form-group';
      div.id = `challan-wrapper-${challanCount}`;
      div.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label class="form-label">Challan ${challanCount}</label>
                    <span style="color:red; cursor:pointer;" onclick="removeChallan(${challanCount})"><i class="bi bi-trash"></i></span>
                </div>
                <input type="file" name="challan[]" class="form-control" accept=".pdf, .jpg, .png" onchange="displayUploadedFile(event, ${challanCount})">
                <div id="preview-challan-${challanCount}" style="font-size:12px; margin-top:5px; color:#666;"></div>
             `;
      document.getElementById('additional-challan-fields').appendChild(div);
    }

    window.removeChallan = function (id) {
      document.getElementById(`challan-wrapper-${id}`).remove();
      challanCount--;
    }

    // Father's Name & Address Merger
    window.updateFatherDetails = function () {
      const name = document.getElementById('father_name_input').value.trim();
      const addr = document.getElementById('father_addr_input').value.trim();

      // Remove individual invalid classes on input
      if (name) document.getElementById('father_name_input').classList.remove('invalid');
      if (addr) document.getElementById('father_addr_input').classList.remove('invalid');

      if (name && addr) {
        document.getElementById('father_address_hidden').value = `${name}, ${addr}`;
      } else {
        document.getElementById('father_address_hidden').value = "";
      }
    }

    // Semester Info Merger
    window.updateSemesterString = function () {
      const type = document.getElementById('sem_type_input').value;
      const num = document.getElementById('sem_num_input').value;

      // Dynamic Requirement for Previous Result
      const resultInputs = document.querySelectorAll('input[name="results[]"]');
      const resultLabel = document.getElementById('result_label');
      const firstLabel = document.getElementById('first_result_label');
      const resultHint = document.getElementById('result_hint');
      const addText = document.getElementById('add_result_text');

      // Update Labels & Requirement based on Regular/Ex
      if (type === 'Ex') {
        resultLabel.innerHTML = '<i class="bi bi-file-earmark-medical"></i> Ex-Exam Backlog Results';
        firstLabel.textContent = "Backlog Result 1 *";
        addText.textContent = "Add Another Backlog Result";
      } else {
        resultLabel.innerHTML = '<i class="bi bi-file-earmark-text"></i> Previous Semester Results';
        firstLabel.textContent = "Previous Result *";
        addText.textContent = "Add More Results";
      }

      const isMandatory = (num && parseInt(num) >= 3);

      resultInputs.forEach((input, index) => {
        if (index === 0) { // Only force first one if mandatory
          input.required = isMandatory;
        }
      });

      if (isMandatory) {
        firstLabel.classList.add('required');
        resultHint.textContent = (type === 'Ex') ? "Mandatory for Ex Students" : "Mandatory for 2nd/3rd Year";
        resultHint.classList.replace('text-blue-600', 'text-red-600');
      } else {
        firstLabel.classList.remove('required');
        resultHint.textContent = "Optional for 1st/2nd Sem";
        resultHint.classList.replace('text-red-600', 'text-blue-600');
      }

      // Remove error styling
      if (type) document.getElementById('sem_type_input').classList.remove('invalid');
      if (num) document.getElementById('sem_num_input').classList.remove('invalid');

      if (type && num) {
        const suffix = (num === '1') ? 'st' : (num === '2') ? 'nd' : (num === '3') ? 'rd' : 'th';
        // Format: "Regular 6th"
        document.getElementById('current_semester_hidden').value = `${type} ${num}${suffix}`;

        // Hide error message
        const err = document.querySelector('#sem_container .error-msg');
        if (err) err.style.display = 'none';
      } else {
        document.getElementById('current_semester_hidden').value = "";
      }
    }

    window.displayUploadedFile = function (e, id) {
      const file = e.target.files[0];
      if (file) {
        document.getElementById(`preview-challan-${id}`).textContent = `Selected: ${file.name}`;
      }
    }

    let resultCounter = 1;
    window.addResultField = function () {
      resultCounter++;
      const type = document.getElementById('sem_type_input').value;
      const labelText = (type === 'Ex') ? `Backlog Result ${resultCounter}` : `Additional Result ${resultCounter}`;

      const div = document.createElement('div');
      div.className = 'form-group mb-3 pb-3 border-b border-blue-100 last:border-0';
      div.innerHTML = `
        <div class="flex justify-between items-center mb-2">
          <label class="text-[11px] font-bold text-slate-500 uppercase">${labelText}</label>
          <button type="button" class="text-red-500 hover:text-red-700" onclick="this.parentElement.parentElement.remove()">
            <i class="bi bi-trash-fill"></i>
          </button>
        </div>
        <input type="file" name="results[]" class="form-control" accept=".pdf, image/*">
      `;
      document.getElementById('additional-results').appendChild(div);
    }

    window.displayResultPreview = function (e, id) {
      // Placeholder if you want to show filename like challans
    }

    // Fee String Merger
    window.updateFeeString = function () {
      let amt = document.getElementById('fee_amount').value.trim();
      // Remove any trailing /- or non-numeric if user entered it (keeping it clean)
      amt = amt.replace(/\/\-$/, '').replace(/[^\d]/g, '');

      const rcpt = document.getElementById('fee_receipt').value.trim();
      const dateInput = document.getElementById('fee_date').value;

      let dateStr = "";
      if (dateInput) {
        const d = new Date(dateInput);
        if (!isNaN(d.getTime())) {
          // Return d/m/yy format
          const day = d.getDate();
          const month = d.getMonth() + 1;
          const year = d.getFullYear().toString().slice(-2);
          dateStr = `${day}/${month}/${year}`;
        }
      }

      const hiddenInput = document.getElementById('admission_fees_hidden');
      if (amt && rcpt && dateStr) {
        hiddenInput.value = `${amt}/- ${rcpt} ${dateStr}`;

        // Real-time valid UI
        const errDiv = document.querySelector('#fee_container .error-msg');
        if (errDiv) errDiv.style.display = 'none';
        document.querySelectorAll('#fee_container .form-control').forEach(el => el.classList.remove('invalid'));
      } else {
        hiddenInput.value = "";
      }
    }

    // Review Modal
    window.showReviewModal = function () {
      const data = new FormData(document.getElementById('mainForm'));
      let html = `<div class="review-grid">
               <p><strong>Name:</strong> ${data.get('student_name')}</p>
               <p><strong>Roll No:</strong> ${data.get('roll_no')}</p>
               <p><strong>Sem:</strong> ${data.get('current_semester')}</p>
               <p><strong>Mobile:</strong> ${data.get('mobile_no')}</p>
               <p><strong>Fees:</strong> ${data.get('admission_fees')}</p>
             </div>
             <h4>Subjects</h4>
             <ul>`;

      // Loop subjects mainly for checking
      for (let i = 1; i <= 6; i++) {
        if (data.get(`subject${i}`)) {
          html += `<li>${data.get(`subject${i}`)} (Sem ${data.get(`sem${i}`)})</li>`;
        }
      }
      html += `</ul>`;
      document.getElementById('reviewContent').innerHTML = html;
      document.getElementById('reviewModal').style.display = 'flex';
    }

    window.closeReviewModal = function () {
      document.getElementById('reviewModal').style.display = 'none';
    }

  </script>
</body>

</html>