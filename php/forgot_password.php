<?php
session_start();
require_once 'connection.php';
require_once '../config/send_mail.php';

$message = "";
$messageType = "";
$resetType = "";

// Reset state on fresh GET request (user leaves and comes back)
if ($_SERVER["REQUEST_METHOD"] === "GET") {
  unset($_SESSION["show_otp_form"]);
  unset($_SESSION["otp_verified"]);
  unset($_SESSION["reset_otp"]);
  unset($_SESSION["reset_email"]);
  unset($_SESSION["reset_name"]);
  unset($_SESSION["otp_expiry"]);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST["step"]) && $_POST["step"] === "request_otp") {
    $user_input = trim($_POST["user_input"]);
    // Update table to student_accounts and check by email or roll_no
    $stmt = $conn->prepare("SELECT email, full_name FROM student_accounts WHERE roll_no = ? OR email = ?");
    $stmt->bind_param("ss", $user_input, $user_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $row = $result->fetch_assoc();
      $email = $row["email"];
      $full_name = $row["full_name"];
      $otp = rand(100000, 999999);
      $_SESSION["reset_otp"] = $otp;
      $_SESSION["reset_email"] = $email;
      $_SESSION["reset_name"] = $full_name;
      $_SESSION["otp_expiry"] = time() + 300; // OTP expires in 5 minutes

      // [TESTING MODE] Skip email, show OTP on screen
      $message = "🔑 [TEST MODE] Your OTP is: <strong>$otp</strong>";
      $messageType = "success";
      $_SESSION["show_otp_form"] = true;
    } else {
      $message = "No student account found with this Email/Roll No.";
      $messageType = "error";
    }
  } elseif (isset($_POST["step"]) && $_POST["step"] === "verify_otp") {
    $otp_entered = trim($_POST["otp"]);
    if (isset($_SESSION["reset_otp"]) && isset($_SESSION["otp_expiry"]) && time() < $_SESSION["otp_expiry"]) {
      if ($otp_entered == $_SESSION["reset_otp"]) {
        $_SESSION["otp_verified"] = true;
        $message = "OTP verified! Set your new password.";
        $messageType = "success";
      } else {
        $message = "Incorrect OTP code. Please check again.";
        $messageType = "error";
      }
    } else {
      $message = "OTP has expired. Request a new one.";
      $messageType = "error";
    }
  } elseif (isset($_POST["step"]) && $_POST["step"] === "reset_password") {
    if (!isset($_SESSION["otp_verified"]) || !$_SESSION["otp_verified"]) {
      $message = "Access denied! Please verify OTP first.";
      $messageType = "error";
    } else {
      $new_password = password_hash(trim($_POST["new_password"]), PASSWORD_BCRYPT);
      $email = $_SESSION["reset_email"];
      // Use student_accounts and password_hash
      $stmt = $conn->prepare("UPDATE student_accounts SET password_hash = ?, is_temp_password = 0 WHERE email = ?");
      $stmt->bind_param("ss", $new_password, $email);

      if ($stmt->execute()) {
        $message = "Success! Your password has been updated.";
        $resetType = "success";

        // [TESTING MODE] Skip confirmation email
        // sendEmail($email, $_SESSION['reset_name'], $subject, $body);

        session_destroy();
      } else {
        $message = "System error. Could not update password.";
        $messageType = "error";
        $resetType = "error";
        session_destroy();
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password | Kitabghar</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
          animation: { 'pulse-slow': 'pulse 3s infinite' }
        }
      }
    }
  </script>
</head>

<body class="bg-slate-50 font-sans min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
  <!-- Decorative background -->
  <div class="absolute inset-0 z-0">
    <div
      class="absolute top-0 -left-4 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse-slow">
    </div>
    <div
      class="absolute bottom-0 -right-4 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse-slow">
    </div>
  </div>

  <div class="relative z-10 w-full max-w-[440px]" data-aos="zoom-in" data-aos-duration="600">
    <div
      class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] shadow-2xl shadow-blue-500/10 border border-white p-8 md:p-10">
      <!-- Header -->
      <div class="text-center mb-8">
        <div
          class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-blue-600/30">
          <i class="bi bi-shield-lock-fill text-3xl text-white"></i>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
          <?php
          if (!isset($_SESSION["show_otp_form"]))
            echo "Forgot Password";
          elseif (!isset($_SESSION["otp_verified"]))
            echo "Verify Identity";
          else
            echo "New Password";
          ?>
        </h1>
        <p class="text-slate-500 mt-2 text-sm font-medium">
          <?php
          if (!isset($_SESSION["show_otp_form"]))
            echo "Enter your details to receive a reset code";
          elseif (!isset($_SESSION["otp_verified"]))
            echo "We've sent a 6-digit code to your email";
          else
            echo "Create a strong password for your account";
          ?>
        </p>
      </div>

      <!-- Forms -->
      <?php if (!isset($_SESSION["show_otp_form"]) && !isset($_SESSION["otp_verified"])): ?>
        <form method="post" class="space-y-5 auth-form">
          <input type="hidden" name="step" value="request_otp">
          <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Email or Roll No</label>
            <div class="relative">
              <i class="bi bi-person-fill absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
              <input type="text" name="user_input" required
                class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-12 py-4 text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-semibold"
                placeholder="e.g. 21010101 or email@domain.com">
            </div>
          </div>
          <button type="submit"
            class="w-full bg-slate-900 hover:bg-blue-600 text-white font-bold py-4 rounded-2xl transition-all duration-300 shadow-xl shadow-slate-900/10 flex items-center justify-center gap-2 group submit-btn">
            <span class="btn-text">Send Code</span>
            <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform btn-icon"></i>
            <div class="spinner hidden w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
          </button>
        </form>

      <?php elseif (!isset($_SESSION["otp_verified"])): ?>
        <div class="space-y-5">
          <form method="post" class="auth-form space-y-5">
            <input type="hidden" name="step" value="verify_otp">
            <div class="space-y-2 text-center">
              <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Authentication Code</label>
              <input type="text" name="otp" required maxlength="6"
                class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 text-center text-2xl font-black tracking-[1em] focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all"
                placeholder="000000">
            </div>
            <button type="submit"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl transition-all duration-300 shadow-xl shadow-blue-600/20 flex items-center justify-center gap-2 submit-btn">
              <i class="bi bi-shield-check-fill btn-icon"></i>
              <span class="btn-text">Verify & Continue</span>
              <div class="spinner hidden w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
            </button>
          </form>
          <div class="text-center pt-2">
            <form method="post" class="auth-form inline">
              <input type="hidden" name="step" value="request_otp">
              <input type="hidden" name="user_input" value="<?php echo htmlspecialchars($_SESSION['reset_email']); ?>">
              <button type="submit"
                class="text-sm font-bold text-blue-600 hover:text-blue-700 flex items-center justify-center gap-2 mx-auto submit-btn">
                <span class="btn-text">Didn't get code? Resend</span>
                <div
                  class="spinner hidden w-3 h-3 border-2 border-blue-600/30 border-t-blue-600 rounded-full animate-spin">
                </div>
              </button>
            </form>
          </div>
        </div>

      <?php else: ?>
        <form method="post" id="resetForm" class="space-y-5 auth-form">
          <input type="hidden" name="step" value="reset_password">
          <div class="space-y-4">
            <div class="space-y-2">
              <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">New Password</label>
              <div class="relative group">
                <i
                  class="bi bi-lock-fill absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                <input type="password" name="new_password" id="new_p" required
                  class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-12 py-4 text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-semibold"
                  placeholder="••••••••">
                <button type="button"
                  class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors toggle-pass"
                  data-target="new_p">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Confirm Password</label>
              <div class="relative group">
                <i
                  class="bi bi-shield-check absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                <input type="password" name="confirm_password" id="confirm_p" required
                  class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-12 py-4 text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-semibold"
                  placeholder="••••••••">
                <button type="button"
                  class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors toggle-pass"
                  data-target="confirm_p">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>
          </div>
          <button type="submit"
            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl transition-all duration-300 shadow-xl shadow-green-600/20 flex items-center justify-center gap-2 submit-btn">
            <i class="bi bi-shield-fill-check btn-icon"></i>
            <span class="btn-text">Save Password</span>
            <div class="spinner hidden w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
          </button>
        </form>
      <?php endif; ?>

      <script>
        document.querySelectorAll('.auth-form').forEach(form => {
          form.addEventListener('submit', function () {
            const btn = this.querySelector('.submit-btn');
            if (btn) {
              btn.disabled = true;
              btn.querySelector('.btn-text').textContent = 'Processing...';
              const icon = btn.querySelector('.btn-icon');
              if (icon) icon.classList.add('hidden');
              btn.querySelector('.spinner').classList.remove('hidden');
            }
          });
        });
      </script>

      <!-- Navigation -->
      <div class="mt-8 pt-6 border-t border-slate-100 space-y-3">
        <a href="../student_login.html"
          class="flex items-center justify-center gap-2 text-slate-500 hover:text-slate-900 font-bold text-sm transition-colors py-2">
          <i class="bi bi-arrow-left"></i> Back to Login
        </a>
      </div>
    </div>
  </div>

  <!-- Alert Modal -->
  <?php if ($message): ?>
    <div id="alertModal"
      class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
      <div
        class="bg-white rounded-[2rem] p-8 max-w-sm w-full text-center shadow-2xl border border-slate-100 animate-in zoom-in duration-300">
        <div
          class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center <?php echo ($messageType === 'success') ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'; ?>">
          <i
            class="bi <?php echo ($messageType === 'success') ? 'bi-check2-circle' : 'bi-exclamation-circle'; ?> text-3xl"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">
          <?php echo ($messageType === 'success') ? 'Success!' : 'Oops!'; ?>
        </h3>
        <p class="text-slate-600 text-sm font-medium mb-8 leading-relaxed"><?php echo $message; ?></p>
        <button onclick="handleModalClose()"
          class="w-full py-4 rounded-2xl font-bold transition-all <?php echo ($messageType === 'success') ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-slate-900 hover:bg-slate-800 text-white'; ?>">
          Got it
        </button>
      </div>
    </div>
    <script>
      function handleModalClose() {
        const resetType = "<?php echo $resetType; ?>";
        if (resetType === "success") {
          window.location.href = "../student_login.html";
        } else {
          document.getElementById('alertModal').remove();
        }
      }
    </script>
  <?php endif; ?>

  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init();

    // Passwords Match Validation
    const resetForm = document.getElementById('resetForm');
    if (resetForm) {
      resetForm.addEventListener('submit', (e) => {
        const p1 = document.getElementById('new_p').value;
        const p2 = document.getElementById('confirm_p').value;
        if (p1 !== p2) {
          e.preventDefault();
          alert("Passwords do not match!");
        }
      });
    }

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
  </script>
</body>

</html>