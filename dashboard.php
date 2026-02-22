<?php
session_start();

// 1. Auth Check
if (!isset($_SESSION['user_id'])) {
  header("Location: student_login.html");
  exit;
}

// 2. Force Password Change Check
if (isset($_SESSION['force_password_change'])) {
  header("Location: change_password.php");
  exit;
}

require_once 'php/connection.php';

// Single Session Enforcement
$check_sess = $conn->prepare("SELECT session_token FROM student_accounts WHERE id = ?");
$check_sess->bind_param("i", $_SESSION['user_id']);
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

// 3. Fetch User Details from new table
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT full_name, email, profile_image, last_login_at, last_login_ip, last_login_location, admission_year, expected_passing_year FROM student_accounts WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
  session_destroy();
  header("Location: student_login.html");
  exit;
}

// Calculate Student-Specific Academic Session from student_accounts data
$student_session = "";
if (!empty($user['admission_year']) && !empty($user['expected_passing_year'])) {
  $student_session = $user['admission_year'] . "-" . substr($user['expected_passing_year'], -2);
}

// 4. Force Profile Image Update (If missing or default)
if (empty($user['profile_image']) || $user['profile_image'] === 'users.png') {
  // Check if we are already on the update page handled in profile_update.php
  header("Location: profile_update.php?error=Profile image is compulsory for the first time!");
  exit;
}

$username = $user['full_name'];
$profile_pic = "php/uploads/" . $user['profile_image'];

// 5. Intelligent Security Log Handling
$last_ip = $user['last_login_ip'] ?? '';
$last_time = $user['last_login_at'] ?? '';
$last_loc = $user['last_login_location'] ?? '';

// If data is missing (first login after update), capture it now
if (empty($last_ip) || $last_ip === '0.0.0.0' || $last_ip === '::1' || $last_ip === '127.0.0.1') {

  if (!function_exists('getRealIP')) {
    function getRealIP()
    {
      if (!empty($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];
      if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
      return $_SERVER['REMOTE_ADDR'];
    }
  }

  $last_ip = getRealIP();
  $last_time = date('Y-m-d H:i:s');
  $last_loc = "Local Network Access";

  // Check for Localhost and fetch Public IP
  if ($last_ip === '127.0.0.1' || $last_ip === '::1' || $last_ip === 'localhost') {
    $context = stream_context_create(['http' => ['timeout' => 3]]);
    $external_ip = @file_get_contents('https://api.ipify.org', false, $context);

    if ($external_ip) {
      $last_ip = trim($external_ip); // Update the main IP variable to show public IP
    }
  }

  // Geo Lookup
  if (filter_var($last_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
    $context = stream_context_create(['http' => ['timeout' => 3]]);
    $details = @json_decode(file_get_contents("http://ip-api.com/json/{$last_ip}?fields=status,message,country,regionName,city,zip", false, $context));

    if ($details && $details->status === 'success') {
      $last_loc = "{$details->city}, {$details->regionName}, {$details->country} ({$details->zip})";
    }
  }

  // Create Logs Table if not exists (Safety check)
  $conn->query("CREATE TABLE IF NOT EXISTS student_login_logs (
      id INT AUTO_INCREMENT PRIMARY KEY,
      student_id INT NOT NULL,
      ip_address VARCHAR(45),
      login_time DATETIME DEFAULT CURRENT_TIMESTAMP,
      location VARCHAR(255),
      INDEX (student_id)
  )");

  // Update DB with the (possibly public) IP and correct location
  $upd = $conn->prepare("UPDATE student_accounts SET last_login_ip = ?, last_login_at = ?, last_login_location = ? WHERE id = ?");
  $upd->bind_param("sssi", $last_ip, $last_time, $last_loc, $user_id);
  $upd->execute();

  // Smart Log Update: Check if we have a recent log entry (last 2 mins) that might be 'Localhost Access' or '::1'
  // If yes, update it. If no, insert new.
  $check_log = $conn->prepare("SELECT id FROM student_login_logs WHERE student_id = ? AND login_time >= DATE_SUB(NOW(), INTERVAL 2 MINUTE) ORDER BY id DESC LIMIT 1");
  $check_log->bind_param("i", $user_id);
  $check_log->execute();
  $log_res = $check_log->get_result();

  if ($existing_log = $log_res->fetch_assoc()) {
    // Update existing recent log
    $log_stmt = $conn->prepare("UPDATE student_login_logs SET ip_address = ?, location = ?, login_time = ? WHERE id = ?");
    $log_stmt->bind_param("sssi", $last_ip, $last_loc, $last_time, $existing_log['id']);
    $log_stmt->execute();
  } else {
    // Insert new log if no recent one found
    $log_stmt = $conn->prepare("INSERT INTO student_login_logs (student_id, ip_address, login_time, location) VALUES (?, ?, ?, ?)");
    $log_stmt->bind_param("isss", $user_id, $last_ip, $last_time, $last_loc);
    $log_stmt->execute();
  }
}

$last_login = [
  'time' => $last_time,
  'ip' => $last_ip,
  'location' => $last_loc
];

// 6. Finalize Display Session
$display_session = "2024-25"; // Hard fallback

// Priority 1: Student Specific Data from student_accounts
if (!empty($student_session)) {
  $display_session = $student_session;
} else {
  // Priority 2: Admin set global session from exam_settings
  $session_query = $conn->query("SELECT academic_session FROM exam_settings LIMIT 1");
  if ($session_query && $row = $session_query->fetch_assoc()) {
    $display_session = $row['academic_session'];
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>E-KITABGHAR | Student Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
              class="flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-50 text-indigo-600 transition-all font-semibold">
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
              class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition-all font-medium">
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
    <header class="sticky top-0 z-30 glass-effect border-b border-slate-200/50 px-6 py-4 backdrop-blur-xl">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button id="menu-btn"
            class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100 hover:text-indigo-600 transition-all duration-300">
            <i class="bi bi-list text-2xl"></i>
          </button>
          <div class="flex items-center gap-2 lg:hidden">
            <div
              class="w-10 h-10 bg-gradient-to-tr from-indigo-600 to-violet-600 rounded-xl flex items-center justify-center text-white shadow-lg">
              <i class="bi bi-book-half text-xl"></i>
            </div>
          </div>
          <div class="hidden sm:block">
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Welcome, <?= htmlspecialchars($username); ?>
            </h1>
            <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-widest leading-none mt-0.5">Academic
              Session <?= htmlspecialchars($display_session); ?></p>
          </div>
          <div class="sm:hidden">
            <h1 class="text-lg font-black text-slate-900 tracking-tight">Hi,
              <?= explode(' ', htmlspecialchars($username))[0]; ?>
            </h1>
            <p class="text-[9px] text-indigo-500 font-bold uppercase tracking-widest leading-none mt-0.5">
              <?= htmlspecialchars($display_session); ?>
            </p>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <div class="text-right hidden md:block">
            <p class="text-xs font-bold text-slate-900"><?= htmlspecialchars($username); ?></p>
            <div class="flex items-center justify-end gap-1.5 mt-0.5">
              <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Verified Student</p>
            </div>
          </div>
          <div class="relative">
            <img src="<?= htmlspecialchars($profile_pic); ?>"
              class="w-12 h-12 rounded-2xl object-cover ring-4 ring-indigo-50 shadow-md hover:scale-105 transition-transform cursor-pointer"
              alt="Profile" />
          </div>
        </div>
      </div>
    </header>

    <!-- Dashboard Content -->
    <div class="p-6 lg:p-10 max-w-7xl mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        <!-- Exam Card -->
        <div
          class="group bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-2xl hover:shadow-indigo-500/5 hover:-translate-y-2 transition-all duration-500 flex flex-col h-full"
          data-aos="fade-up" data-aos-delay="100">
          <div class="relative">
            <div
              class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-8 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
              <i class="bi bi-journal-text text-3xl"></i>
            </div>
            <h4 class="text-2xl font-black text-slate-800 mb-4 tracking-tight">Exam Form</h4>
            <p class="text-slate-500 text-sm leading-relaxed mb-8">Secure your academic semester progress. Review
              eligibility criteria and submit your examination form for the current academic session.</p>
          </div>
          <div class="mt-auto">
            <a href="exam_form.php"
              class="inline-flex items-center justify-center gap-3 bg-indigo-600 text-white w-full py-4 rounded-2xl font-bold hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 group/btn">
              Register Now <i class="bi bi-arrow-right group-hover/btn:translate-x-1 transition-transform"></i>
            </a>
          </div>
        </div>

        <!-- Profile Card -->
        <div
          class="group bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-2xl hover:shadow-violet-500/5 hover:-translate-y-2 transition-all duration-500 flex flex-col h-full"
          data-aos="fade-up" data-aos-delay="200">
          <div class="relative flex-grow">
            <div
              class="w-14 h-14 bg-violet-50 rounded-2xl flex items-center justify-center text-violet-600 mb-8 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
              <i class="bi bi-person-gear text-3xl"></i>
            </div>
            <h4 class="text-2xl font-black text-slate-800 mb-4 tracking-tight">Update Account</h4>
            <p class="text-slate-500 text-sm leading-relaxed mb-8">Keep your personal and academic information up to
              date for university records. Update contact details and profile images.</p>
          </div>
          <div class="mt-auto">
            <a href="profile_update.php"
              class="inline-flex items-center justify-center gap-3 bg-violet-600 text-white w-full py-4 rounded-2xl font-bold hover:bg-violet-700 transition-all shadow-xl shadow-violet-100 group/btn">
              Configure Now <i class="bi bi-arrow-right group-hover/btn:translate-x-1 transition-transform"></i>
            </a>
          </div>
        </div>

        <!-- Syllabus Card -->
        <div
          class="group bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-2xl hover:shadow-emerald-500/5 hover:-translate-y-2 transition-all duration-500 flex flex-col h-full"
          data-aos="fade-up" data-aos-delay="300">
          <div class="relative flex-grow">
            <div
              class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 mb-8 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
              <i class="bi bi-book text-3xl"></i>
            </div>
            <h4 class="text-2xl font-black text-slate-800 mb-4 tracking-tight">Syllabus</h4>
            <p class="text-slate-500 text-sm leading-relaxed mb-8">Download and review your comprehensive academic
              syllabus. Stay organized with semester-wise course objectives and reading lists.</p>
          </div>
          <div class="mt-auto">
            <a href="syllabus.html"
              class="inline-flex items-center justify-center gap-3 bg-emerald-600 text-white w-full py-4 rounded-2xl font-bold hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-100 group/btn">
              Explore Now <i class="bi bi-eye group-hover/btn:scale-110 transition-transform"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Security Logs Row (Below Cards) -->
      <div class="mt-12" data-aos="fade-up">
        <div
          class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden">
          <div class="absolute -top-10 -right-10 opacity-[0.03]">
            <i class="bi bi-shield-lock-fill text-[12rem]"></i>
          </div>

          <?php
          // Fetch Last 4 Logs
          $recent_logs = [];
          $log_query = $conn->prepare("SELECT ip_address, login_time, location FROM student_login_logs WHERE student_id = ? ORDER BY login_time DESC LIMIT 4");
          if ($log_query) {
            $log_query->bind_param("i", $user_id);
            $log_query->execute();
            $logs_result = $log_query->get_result();
            while ($row = $logs_result->fetch_assoc()) {
              $recent_logs[] = $row;
            }
          }
          // Fallback
          if (empty($recent_logs)) {
            $recent_logs[] = ['ip_address' => $last_ip, 'login_time' => $last_time, 'location' => $last_loc ?? 'Local Network'];
          }
          ?>

          <div class="flex flex-col gap-6 relative z-10">
            <div class="flex items-center gap-5">
              <div
                class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center text-indigo-300 border border-white/10 shadow-inner">
                <i class="bi bi-shield-lock text-3xl"></i>
              </div>
              <div>
                <h4 class="text-2xl font-bold text-white tracking-tight">Security Access Log</h4>
                <p class="text-[10px] text-indigo-400 font-bold uppercase tracking-[0.2em] mt-1">Recent 4 Login
                  Activities</p>
              </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="bg-white/5 text-[10px] text-indigo-300 font-black uppercase tracking-widest">
                    <th class="p-4 border-b border-white/10">Time</th>
                    <th class="p-4 border-b border-white/10">IP Address</th>
                    <th class="p-4 border-b border-white/10">Location</th>
                  </tr>
                </thead>
                <tbody class="text-sm text-indigo-50 font-medium">
                  <?php foreach ($recent_logs as $log): ?>
                    <tr class="hover:bg-white/5 transition-colors group">
                      <td class="p-4 border-b border-white/5 group-last:border-0">
                        <?= date('M d, H:i A', strtotime($log['login_time'])); ?>
                      </td>
                      <td class="p-4 border-b border-white/5 group-last:border-0 font-mono text-xs opacity-70">
                        <?= htmlspecialchars($log['ip_address']); ?>
                      </td>
                      <td class="p-4 border-b border-white/5 group-last:border-0 flex items-center gap-2">
                        <i class="bi bi-geo-alt text-indigo-400"></i>
                        <span class="truncate max-w-[150px] sm:max-w-none">
                          <?= htmlspecialchars($log['location']); ?>
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-16 text-center" data-aos="fade-up">
        <div class="inline-flex items-center gap-6 bg-white border border-slate-100 px-8 py-5 rounded-[2rem] shadow-sm">
          <div class="flex items-center gap-3">
            <div class="relative flex h-3 w-3">
              <span
                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Session Health</p>
          </div>
          <div class="w-px h-6 bg-slate-100"></div>
          <div class="flex items-center gap-3 text-indigo-600">
            <i class="bi bi-clock-history text-lg"></i>
            <span class="text-sm font-black tracking-widest"><span id="countdown">180</span>s remaining</span>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
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

    menuBtn.addEventListener("click", openSidebar);
    closeBtn.addEventListener("click", closeSidebar);
    overlay.addEventListener("click", closeSidebar);

    // Auto-close sidebar when clicking links (mobile)
    document.querySelectorAll('#sidebar nav a').forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth < 1025) closeSidebar();
      });
    });

    AOS.init({
      duration: 800,
      once: true
    });

    // Inactivity Timer
    let inactivityTime = 180;
    let timer;
    const countdownDisplay = document.getElementById("countdown");

    function updateCountdown() {
      if (inactivityTime > 0) {
        inactivityTime--;
        countdownDisplay.innerText = inactivityTime;
      } else {
        logoutUser();
      }
    }

    function resetTimer() {
      inactivityTime = 180;
      if (countdownDisplay) countdownDisplay.innerText = inactivityTime;
    }

    function logoutUser() {
      fetch('php/logout.php', { method: 'POST' }).then(() => {
        window.location.href = "student_login.html";
      });
    }

    // Start timer
    timer = setInterval(updateCountdown, 1000);

    // Reset on interaction
    ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(event => {
      document.addEventListener(event, resetTimer);
    });
  </script>
</body>

</html>