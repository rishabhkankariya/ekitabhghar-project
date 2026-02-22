<?php
session_start();
if (!isset($_SESSION['user_email'])) {
  header("Location: ../student_login.html");
  exit;
}

require_once 'connection.php';

// Prepare database connection if not already handled by connection.php
if (!isset($conn)) {
  die("Database connection failed.");
}

$email = $_SESSION['user_email'];
$stmt = $conn->prepare("SELECT full_name, profile_image FROM student_accounts WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user_res = $stmt->get_result();
$user = $user_res->fetch_assoc();

// Force Profile Image Update
if (!$user || empty($user['profile_image']) || $user['profile_image'] === 'users.png') {
  header("Location: ../profile_update.php?error=Access Denied! You must upload a profile image first.");
  exit;
}

$sql = "SELECT * FROM messages ORDER BY sent_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Notifications | E-KITABGHAR</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
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
            <a href="../dashboard.php"
              class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition-all font-medium">
              <i class="bi bi-grid-1x2-fill text-xl"></i> Home
            </a>
          </li>
          <li>
            <a href="../exam_form.php"
              class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition-all font-medium">
              <i class="bi bi-journal-text text-xl"></i> Exam Form
            </a>
          </li>
          <li>
            <a href="../profile_update.php"
              class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition-all font-medium">
              <i class="bi bi-person-gear text-xl"></i> Update Account
            </a>
          </li>
          <li>
            <a href="../Year.php"
              class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition-all font-medium">
              <i class="bi bi-book text-xl"></i> Study Material
            </a>
          </li>
          <li>
            <a href="../feedback.html"
              class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition-all font-medium">
              <i class="bi bi-chat-left-dots text-xl"></i> Feedback
            </a>
          </li>
          <li>
            <a href="message.php"
              class="flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-50 text-indigo-600 transition-all font-semibold">
              <i class="bi bi-envelope text-xl"></i> Notifications
            </a>
          </li>
        </ul>
      </nav>

      <div class="pt-6 border-t border-slate-100">
        <a href="logout.php"
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
          <h1 class="text-xl font-semibold text-slate-800 hidden sm:block">Notifications</h1>
          <h1 class="text-lg font-semibold text-slate-800 sm:hidden">Inbox</h1>
        </div>

        <div class="flex items-center gap-4">
          <div class="text-right hidden md:block">
            <p class="text-sm font-medium text-slate-900"><?= htmlspecialchars($user['full_name']); ?></p>
            <p class="text-xs text-slate-500">Student Account</p>
          </div>
          <?php $profile_pic = !empty($user['profile_image']) ? "uploads/" . $user['profile_image'] : "../img/users.png"; ?>
          <img src="<?= htmlspecialchars($profile_pic); ?>"
            class="w-10 h-10 rounded-full object-cover ring-2 ring-indigo-500/20 shadow-sm" alt="Profile" />
        </div>
      </div>
    </header>

    <div class="p-6 lg:p-10 max-w-3xl mx-auto">
      <div class="flex items-center gap-4 mb-10" data-aos="fade-right">
        <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm">
          <i class="bi bi-bell-fill text-2xl"></i>
        </div>
        <div>
          <h1 class="text-3xl font-bold text-slate-900">Notifications</h1>
          <p class="text-slate-500">Stay updated with the latest campus announcements</p>
        </div>
      </div>

      <div class="space-y-6">
        <?php if ($result->num_rows > 0): ?>
          <?php $delay = 0;
          while ($row = $result->fetch_assoc()): ?>
            <div
              class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:border-indigo-100 transition-all duration-300"
              data-aos="fade-up" data-aos-delay="<?= $delay; ?>">
              <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-2">
                  <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span>
                  <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Official Update</span>
                </div>
                <span class="text-xs font-medium text-slate-400 bg-slate-50 px-3 py-1 rounded-full">
                  <i class="bi bi-clock mr-1"></i>
                  <?= date('d M Y, h:i A', strtotime($row['sent_at'])); ?>
                </span>
              </div>
              <div
                class="text-slate-700 leading-relaxed bg-slate-50/50 p-4 rounded-2xl border border-slate-50 group-hover:bg-white transition-colors">
                <?= nl2br(htmlspecialchars($row['message'])); ?>
              </div>
            </div>
            <?php $delay += 50; endwhile; ?>
        <?php else: ?>
          <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-200" data-aos="zoom-in">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto text-slate-300 mb-4">
              <i class="bi bi-envelope-open text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-400">No new notifications</h3>
            <p class="text-slate-300 mt-2">We'll let you know when there's an announcement.</p>
          </div>
        <?php endif; ?>
      </div>

      <div class="mt-12 text-center">
        <a href="../dashboard.php"
          class="inline-flex items-center gap-3 bg-slate-900 text-white px-10 py-4 rounded-2xl font-bold hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 active:scale-95 group">
          <i class="bi bi-arrow-left group-hover:-translate-x-1 transition-transform"></i> Return to Dashboard
        </a>
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

    if (menuBtn) menuBtn.addEventListener("click", openSidebar);
    if (closeBtn) closeBtn.addEventListener("click", closeSidebar);
    if (overlay) overlay.addEventListener("click", closeSidebar);

    // Auto-close sidebar when clicking links (mobile)
    document.querySelectorAll('#sidebar nav a').forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth < 1025) closeSidebar();
      });
    });
  </script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>AOS.init({ duration: 800, once: true });</script>
</body>

</html>
