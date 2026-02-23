<?php
session_start();
require_once 'php/connection.php';

require_once 'config/send_mail.php';

if (!isset($_SESSION['user_email'])) {
  header("Location: student_login.html");
  exit;
}

$email = $_SESSION['user_email'];
$message = "";
$messageType = "";

// Single Session Enforcement
$check_sess = $conn->prepare("SELECT session_token FROM student_accounts WHERE email = ?");
$check_sess->bind_param("s", $email);
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
$updateType = "";
$showOtpForm = ($_SESSION['profile_update_otp_sent'] ?? false) && isset($_SESSION['profile_update_data']);

$stmt = $conn->prepare("SELECT id, roll_no, full_name, email, profile_image FROM student_accounts WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $user = $result->fetch_assoc();
} else {
  session_destroy();
  header("Location: student_login.html?error=Account not found. Please login again.");
  exit;
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $step = $_POST['step'] ?? 'init';

  if ($step === 'init') {
    // Initial submission - validate and send OTP if needed
    if (!isset($_POST["captcha"]) || !isset($_SESSION["captcha"])) {
      $message = "CAPTCHA validation failed. Session expired.";
      $messageType = "error";
    } else {
      $entered_captcha = strtoupper(trim($_POST["captcha"]));
      $actual_captcha = $_SESSION["captcha"];
      unset($_SESSION["captcha"]);

      if ($entered_captcha !== $actual_captcha) {
        $message = "Incorrect CAPTCHA. Please try again.";
        $messageType = "error";
      } else {
        // Validate Current Password
        $stmt = $conn->prepare("SELECT password_hash FROM student_accounts WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($current_password_hash);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($_POST['current-password'], $current_password_hash)) {
          $message = "Error: Incorrect current password.";
          $messageType = "error";
        } else {
          $new_username = trim($_POST['username']);
          $new_email = trim($_POST['email']);
          $changingEmail = ($new_email !== $email);
          $changingPassword = !empty($_POST['password']);

          // Check if new email is already taken
          if ($changingEmail) {
            $stmt = $conn->prepare("SELECT id FROM student_accounts WHERE email = ? AND email != ?");
            $stmt->bind_param("ss", $new_email, $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
              $message = "Error: This email is already registered with another account.";
              $messageType = "error";
            }
            $stmt->close();
          }

          if (empty($message)) {
            if ($changingPassword) {
              if ($_POST['password'] !== $_POST['confirm-password']) {
                $message = "Error: New password and confirm password do not match.";
                $messageType = "error";
              }
              $target_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            } else {
              $target_password = $current_password_hash;
            }

            // Profile Image Logic
            $profile_image = $user['profile_image'];
            if (!empty($_FILES["profile_image"]["name"])) {
              $target_dir = "php/uploads/";
              $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
              $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
              if (getimagesize($_FILES["profile_image"]["tmp_name"])) {
                if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                  if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                    $profile_image = basename($_FILES["profile_image"]["name"]);
                  }
                }
              }
            }

            if (empty($message)) {
              if ($changingEmail || $changingPassword) {
                // Request OTP
                $otp = rand(100000, 999999);
                $_SESSION['profile_update_otp'] = $otp;
                $_SESSION['profile_update_expiry'] = time() + 300;
                $_SESSION['profile_update_data'] = [
                  'full_name' => $new_username,
                  'email' => $new_email,
                  'password_hash' => $target_password,
                  'profile_image' => $profile_image,
                  'changing_email' => $changingEmail
                ];

                // [TESTING MODE] Skip email, show OTP on screen
                $_SESSION['profile_update_otp_sent'] = true;
                $showOtpForm = true;
                $message = "🔑 [TEST MODE] Your OTP is: $otp";
                $messageType = "success";
              } else {
                // Just update Name or Image
                $stmt = $conn->prepare("UPDATE student_accounts SET full_name=?, profile_image=? WHERE email=?");
                $stmt->bind_param("sss", $new_username, $profile_image, $email);
                if ($stmt->execute()) {
                  $message = "Profile updated successfully!";
                  $messageType = "success";
                  $updateType = "success";
                }
                $stmt->close();
              }
            }
          }
        }
      }
    }
  } elseif ($step === 'verify') {
    $entered_otp = trim($_POST['otp']);
    if (isset($_SESSION['profile_update_otp']) && time() < $_SESSION['profile_update_expiry']) {
      if ($entered_otp == $_SESSION['profile_update_otp']) {
        if (!isset($_SESSION['profile_update_data'])) {
          $message = "Session data lost. Please start over.";
          $messageType = "error";
          unset($_SESSION['profile_update_otp_sent']);
        } else {
          $data = $_SESSION['profile_update_data'];
          $stmt = $conn->prepare("UPDATE student_accounts SET full_name=?, email=?, password_hash=?, profile_image=? WHERE email=?");
          $stmt->bind_param("sssss", $data['full_name'], $data['email'], $data['password_hash'], $data['profile_image'], $email);

          if ($stmt->execute()) {
            if ($data['changing_email']) {
              $_SESSION['user_email'] = $data['email'];
            }
            $message = "Success! Profile changes verified and applied.";
            $messageType = "success";
            $updateType = "success";
            unset($_SESSION['profile_update_otp']);
            unset($_SESSION['profile_update_otp_sent']);
            unset($_SESSION['profile_update_data']);
          } else {
            $message = "Error updating database.";
            $messageType = "error";
          }
          $stmt->close();
        }
      } else {
        $message = "Incorrect verification code.";
        $messageType = "error";
        $showOtpForm = true;
      }
    } else {
      $message = "Verification code expired. Please start over.";
      $messageType = "error";
      unset($_SESSION['profile_update_otp_sent']);
    }
  } elseif ($step === 'cancel') {
    unset($_SESSION['profile_update_otp_sent']);
    unset($_SESSION['profile_update_otp']);
    unset($_SESSION['profile_update_data']);
    header("Location: profile_update.php");
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Update Profile | KITABGHAR</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

    body {
      font-family: 'Outfit', sans-serif;
    }

    .sidebar-transition {
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @media (max-width: 1024px) {
      .sidebar-hidden {
        transform: translateX(-100%);
      }

      .sidebar-visible {
        transform: translateX(0);
      }

      .main-content {
        margin-left: 0 !important;
      }
    }

    @media (min-width: 1025px) {
      .sidebar-hidden {
        transform: translateX(0);
      }

      .main-content {
        margin-left: 16rem;
      }

      #menu-btn {
        display: none;
      }
    }

    .glass-effect {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
    }

    .toast {
      animation: fadeSlide 0.5s ease-in-out;
    }

    @keyframes fadeSlide {
      0% {
        transform: translateY(-20px);
        opacity: 0;
      }

      100% {
        transform: translateY(0);
        opacity: 1;
      }
    }
  </style>
</head>

<body class="bg-[#f8fafc] text-[#1e293b]">

  <!-- Mobile Overlay -->
  <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"></div>

  <!-- Sidebar -->
  <aside id="sidebar"
    class="fixed top-0 left-0 h-full w-64 bg-white border-r border-slate-200 z-50 sidebar-transition sidebar-hidden shadow-xl lg:shadow-none">
    <div class="p-6 h-full flex flex-col">
      <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-2">
          <div
            class="w-8 h-8 bg-gradient-to-tr from-indigo-600 to-violet-600 rounded-lg flex items-center justify-center text-white shadow-lg shadow-indigo-200">
            <i class="bi bi-book-half text-lg"></i>
          </div>
          <h2 class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">
            Dashboard</h2>
        </div>
        <button id="close-btn" class="lg:hidden text-2xl text-slate-400 hover:text-indigo-600 transition">
          <i class="bi bi-x-lg text-xl"></i>
        </button>
      </div>

      <nav class="flex-grow">
        <ul class="space-y-2">
          <li>
            <a href="dashboard.php"
              class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition-all font-medium">
              <i class="bi bi-grid-1x2-fill text-xl"></i> Home
            </a>
          </li>
          <li>
            <a href="exam_form.php"
              class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition-all font-medium">
              <i class="bi bi-journal-text text-xl"></i> Exam Form
            </a>
          </li>
          <li>
            <a href="profile_update.php"
              class="flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-50 text-indigo-600 transition-all font-semibold">
              <i class="bi bi-person-gear text-xl"></i> Update Account
            </a>
          </li>
          <li>
            <a href="Year.php"
              class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition-all font-medium">
              <i class="bi bi-book text-xl"></i> Study Material
            </a>
          </li>
          <li>
            <a href="feedback.html"
              class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition-all font-medium">
              <i class="bi bi-chat-left-dots text-xl"></i> Feedback
            </a>
          </li>
          <li>
            <a href="php/message.php"
              class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition-all font-medium">
              <i class="bi bi-envelope text-xl"></i> Notifications
            </a>
          </li>
        </ul>
      </nav>

      <div class="pt-6 border-t border-slate-100">
        <a href="php/logout.php"
          class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-500 hover:bg-red-50 transition-all font-medium group">
          <i class="bi bi-box-arrow-right text-xl group-hover:translate-x-1 transition-transform"></i> Logout
        </a>
      </div>
    </div>
  </aside>

  <!-- Main Content -->
  <main id="main" class="main-content transition-all duration-300 min-h-screen">
    <!-- Navbar -->
    <header class="sticky top-0 z-30 glass-effect border-b border-slate-200 px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button id="menu-btn" class="text-2xl text-slate-600 hover:text-indigo-600 transition">
            <i class="bi bi-list"></i>
          </button>
          <div class="flex items-center gap-2 lg:hidden">
            <div
              class="w-8 h-8 bg-gradient-to-tr from-indigo-600 to-violet-600 rounded-lg flex items-center justify-center text-white shadow-lg">
              <i class="bi bi-book-half text-lg"></i>
            </div>
          </div>
          <h1 class="text-xl font-semibold text-slate-800 hidden sm:block">Update Account</h1>
          <h1 class="text-lg font-semibold text-slate-800 sm:hidden">Account</h1>
        </div>

        <div class="flex items-center gap-4">
          <div class="text-right hidden md:block">
            <p class="text-sm font-medium text-slate-900"><?= htmlspecialchars($user['full_name']); ?></p>
            <p class="text-xs text-slate-500">Student Account</p>
          </div>
          <?php $profile_pic = !empty($user['profile_image']) ? "php/uploads/" . $user['profile_image'] : "img/users.png"; ?>
          <img src="<?= htmlspecialchars($profile_pic); ?>"
            class="w-10 h-10 rounded-full object-cover ring-2 ring-indigo-500/20 shadow-sm" alt="Profile" />
        </div>
      </div>
    </header>

    <div class="min-h-[calc(100vh-72px)] py-6 md:py-12 px-2 sm:px-4 flex items-center justify-center">

      <div
        class="w-full max-w-3xl bg-white shadow-2xl shadow-indigo-100 rounded-[2rem] md:rounded-[3rem] p-6 md:p-10 border border-slate-100"
        data-aos="fade-up">
        <div class="text-center mb-8">
          <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mx-auto mb-4">
            <i class="bi bi-person-gear text-3xl"></i>
          </div>
          <h2
            class="text-2xl md:text-3xl font-black bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent inline-block tracking-tight">
            Account Settings</h2>
          <p class="text-slate-500 text-sm mt-1">Update your personal information and security preferences</p>
        </div>

        <?php if ($showOtpForm): ?>
          <!-- OTP Verification Form -->
          <form method="POST" class="space-y-6">
            <input type="hidden" name="step" value="verify">
            <div class="p-6 md:p-10 bg-indigo-50/50 rounded-[2rem] border border-indigo-100 text-center space-y-4">
              <div
                class="w-20 h-20 bg-gradient-to-tr from-indigo-600 to-violet-600 rounded-full flex items-center justify-center mx-auto shadow-xl shadow-indigo-200">
                <i class="bi bi-shield-lock text-3xl text-white"></i>
              </div>
              <h3 class="text-xl md:text-2xl font-bold text-slate-800">Verify Your Identity</h3>
              <p class="text-slate-500 text-sm">To confirm your changes, we've sent a 6-digit verification code to:<br>
                <strong
                  class="text-indigo-600"><?php echo htmlspecialchars($_SESSION['profile_update_data']['email'] ?? 'Enter Email'); ?></strong>
              </p>

              <div class="pt-6">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Verification
                  Code</label>
                <div class="max-w-xs mx-auto">
                  <input type="text" name="otp" required maxlength="6"
                    class="w-full bg-white border border-slate-200 rounded-2xl py-4 text-center text-3xl font-black tracking-[0.5em] focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all shadow-sm"
                    placeholder="000000">
                </div>
              </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 pt-4">
              <button type="submit" name="step" value="cancel"
                class="order-2 sm:order-1 flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-4 rounded-2xl transition-all">
                Cancel
              </button>
              <button type="submit"
                class="order-1 sm:order-2 flex-[2] bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-indigo-200 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                Confirm & Save
              </button>
            </div>
          </form>

        <?php else: ?>
          <!-- Main Profile Update Form -->
          <form method="POST" enctype="multipart/form-data" class="space-y-8">
            <input type="hidden" name="step" value="init">

            <!-- Avatar Upload -->
            <div class="flex flex-col items-center gap-4">
              <div class="relative group">
                <div class="w-32 h-32 md:w-40 md:h-40 rounded-[2rem] overflow-hidden ring-8 ring-indigo-50 shadow-2xl">
                  <img id="imagePreview" src="php/uploads/<?php echo htmlspecialchars($user['profile_image']); ?>"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                </div>
                <label for="profile_image_input"
                  class="absolute -bottom-2 -right-2 bg-indigo-600 text-white w-12 h-12 rounded-2xl shadow-lg border-4 border-white flex items-center justify-center cursor-pointer hover:bg-indigo-700 hover:scale-110 transition-all active:scale-95 z-10">
                  <i class="bi bi-camera-fill text-xl"></i>
                </label>
              </div>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2" id="file-name">Update
                Profile Photo</p>
              <input type="file" name="profile_image" id="profile_image_input" accept="image/*" class="hidden" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Name -->
              <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Full Name</label>
                <div class="relative group">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="bi bi-person text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                  </div>
                  <input type="text" name="username" value="<?php echo htmlspecialchars($user['full_name']); ?>"
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-11 pr-4 py-4 text-sm font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all"
                    required>
                </div>
              </div>

              <!-- Email -->
              <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Email Address</label>
                <div class="relative group">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="bi bi-envelope text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                  </div>
                  <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-11 pr-4 py-4 text-sm font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all"
                    required>
                </div>
                <p class="text-[9px] text-indigo-500 font-bold uppercase tracking-widest pl-1">Verification Required on
                  Change</p>
              </div>
            </div>

            <!-- Current Password Verification Box -->
            <div class="p-6 bg-slate-900 rounded-[2rem] text-white shadow-xl shadow-slate-200 space-y-4">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-500/20 rounded-lg flex items-center justify-center text-indigo-400">
                  <i class="bi bi-shield-check"></i>
                </div>
                <h4 class="text-sm font-bold uppercase tracking-widest">Verify Ownership</h4>
              </div>
              <div class="relative">
                <input type="password" name="current-password" placeholder="Confirm Current Password" required
                  class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-sm text-white placeholder:text-slate-500 focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500/50 outline-none transition-all">
              </div>
            </div>

            <!-- New Password (Optional) -->
            <div class="space-y-4 pt-2">
              <div class="flex items-center gap-2 mb-2">
                <div class="h-px bg-slate-100 flex-1"></div>
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">Change Password
                  (Optional)</span>
                <div class="h-px bg-slate-100 flex-1"></div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">New
                    Password</label>
                  <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                      <i class="bi bi-lock text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                    </div>
                    <input type="password" name="password" id="new_p" placeholder="Enter new password"
                      class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-11 pr-12 py-4 text-sm font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                    <button type="button"
                      class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 transition toggle-pass"
                      data-target="new_p">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Confirm New
                    Password</label>
                  <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                      <i class="bi bi-lock-check text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                    </div>
                    <input type="password" name="confirm-password" id="confirm_p" placeholder="Confirm new password"
                      class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-11 pr-12 py-4 text-sm font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                    <button type="button"
                      class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 transition toggle-pass"
                      data-target="confirm_p">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Captcha -->
            <div class="space-y-3">
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 text-center">Bot
                Verification</label>
              <div
                class="flex flex-col sm:flex-row items-center gap-3 bg-slate-50 p-3 rounded-[1.5rem] border border-slate-200 shadow-sm">
                <div class="flex-shrink-0 bg-white p-2 rounded-xl shadow-sm border border-slate-100">
                  <img src="php/captcha.php" alt="CAPTCHA" class="h-10 w-28 object-contain" id="captcha-img" />
                </div>
                <div class="flex gap-2 w-full">
                  <input type="text" name="captcha" placeholder="CODE" required
                    class="flex-1 bg-white border border-slate-200 rounded-xl py-3.5 px-4 text-center text-sm font-black tracking-[0.3em] focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all uppercase">
                  <button type="button" onclick="document.getElementById('captcha-img').src='php/captcha.php?'+Date.now()"
                    class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center hover:bg-indigo-100 transition-all active:scale-95">
                    <i class="bi bi-arrow-repeat text-xl"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="pt-4">
              <button type="submit" id="saveBtn"
                class="w-full bg-gradient-to-r from-slate-900 to-indigo-900 hover:scale-[1.02] text-white font-bold py-5 rounded-2xl shadow-xl shadow-indigo-200 transition-all duration-300 flex items-center justify-center gap-3 active:scale-[0.98] group">
                <span id="btnText">Update Identity</span>
                <i class="bi bi-check-circle-fill group-hover:rotate-12 transition-transform"></i>
                <div id="btnSpinner"
                  class="hidden w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin">
                </div>
              </button>
            </div>
          </form>
        <?php endif; ?>

        <div class="mt-8 text-center border-t border-slate-100 pt-6">
          <a href="dashboard.php"
            class="inline-flex items-center gap-2 text-xs font-black text-slate-400 hover:text-indigo-600 uppercase tracking-widest transition-all group">
            <i class="bi bi-arrow-left group-hover:-translate-x-1 transition-transform"></i> Back to Control Panel
          </a>
        </div>
      </div>
    </div>
  </main>

  <script>
    // Toggle Password Visibility
    document.querySelectorAll('.toggle-pass').forEach(btn => {
      btn.addEventListener('click', function () {
        const targetId = this.getAttribute('data-target');
        const input = document.getElementById(targetId);
        const icon = this.querySelector('i');
        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
          input.type = 'password';
          icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
      });
    });

    // Mini Alert Function
    function showMiniAlert(msg, iconClass = 'bi-info-circle-fill') {
      const alertDiv = document.createElement('div');
      alertDiv.className = 'fixed top-10 left-1/2 -translate-x-1/2 bg-slate-900/90 backdrop-blur-md text-white px-6 py-3 rounded-2xl shadow-2xl z-[10000] border border-white/10 flex items-center gap-3 transition-all duration-500 opacity-0 translate-y-[-20px]';
      alertDiv.innerHTML = `<i class="bi ${iconClass} text-blue-400"></i> <span class="text-sm font-bold tracking-wide">${msg}</span>`;
      document.body.appendChild(alertDiv);

      // Trigger animation
      requestAnimationFrame(() => {
        alertDiv.classList.remove('opacity-0', 'translate-y-[-20px]');
      });

      setTimeout(() => {
        alertDiv.classList.add('opacity-0', 'translate-y-[-20px]');
        setTimeout(() => alertDiv.remove(), 500);
      }, 3500);
    }

    // Handle Profile Image Preview & Filename
    const fileInput = document.getElementById('profile_image_input');
    const fileName = document.getElementById('file-name');
    const imagePreview = document.getElementById('imagePreview');

    if (fileInput) {
      fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
          const file = this.files[0];
          fileName.textContent = file.name;

          // Show Preview
          const reader = new FileReader();
          reader.onload = function (e) {
            imagePreview.src = e.target.result;
            imagePreview.classList.add('scale-105');
            setTimeout(() => imagePreview.classList.remove('scale-105'), 300);

            // Show Mini Alert
            showMiniAlert('Enter current password to save this photo!', 'bi-shield-lock-fill');
          }
          reader.readAsDataURL(file);
        }
      });
    }

    const sidebar = document.getElementById("sidebar");
    const menuBtn = document.getElementById("menu-btn");
    const closeBtn = document.getElementById("close-btn");
    const overlay = document.getElementById("sidebar-overlay");

    function openSidebar() {
      sidebar.classList.remove("sidebar-hidden");
      sidebar.classList.add("sidebar-visible");
      overlay.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    }

    function closeSidebar() {
      sidebar.classList.add("sidebar-hidden");
      sidebar.classList.remove("sidebar-visible");
      overlay.classList.add("hidden");
      document.body.style.overflow = "auto";
    }

    if (menuBtn) menuBtn.addEventListener("click", openSidebar);
    if (closeBtn) closeBtn.addEventListener("click", closeSidebar);
    if (overlay) overlay.addEventListener("click", closeSidebar);

    // Auto-close sidebar when clicking links (mobile)
    document.querySelectorAll('#sidebar nav a').forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth < 1025) closeSidebar();
      });
    });

    const updateForm = document.querySelector('form');
    if (updateForm) {
      updateForm.addEventListener('submit', function (e) {
        const step = this.querySelector('input[name="step"]').value;
        if (step === 'init') {
          const p1 = document.getElementById('new_p').value;
          const p2 = document.getElementById('confirm_p').value;
          if (p1 && p1 !== p2) {
            e.preventDefault();
            alert("New passwords do not match!");
            return;
          }
        }

        const btn = document.getElementById('saveBtn');
        if (btn) {
          const txt = document.getElementById('btnText');
          const spin = document.getElementById('btnSpinner');
          btn.disabled = true;
          txt.textContent = step === 'init' ? "Processing..." : "Verifying...";
          spin.classList.remove('hidden');
        }
      });
    }

    // Capture URL Errors and show as mini alerts
    document.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      const errorMsg = urlParams.get('error');
      if (errorMsg) {
        showMiniAlert(errorMsg, 'bi-exclamation-triangle-fill');
        // Clean up the URL
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    });
  </script>

  <?php if (!empty($message)): ?>
    <div id="toast" class="fixed top-4 right-4 z-[9999] px-4 py-3 rounded-lg shadow-lg text-white toast
              <?php echo $messageType === 'success' ? 'bg-green-500' : 'bg-red-500'; ?>">
      <div class="flex items-center space-x-2">
        <i
          class="bi <?php echo $messageType === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'; ?>"></i>
        <span><?php echo htmlspecialchars($message); ?></span>
      </div>
    </div>
    <script>
      setTimeout(() => {
        document.getElementById("toast").remove();
        <?php if ($updateType === "success"): ?>
          window.location.href = "dashboard.php";
        <?php endif; ?>
      }, 4000);
    </script>
  <?php endif; ?>

  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>AOS.init();</script>
</body>

</html>