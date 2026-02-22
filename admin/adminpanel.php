<?php
// Set session configurations before starting the session
ini_set('session.gc_maxlifetime', 3600); // Set session timeout to 1 hour
session_set_cookie_params(3600); // Ensure session cookies last 1 hour

session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    echo "<script>
            alert('Unauthorized access! Please log in.');
            window.location.href = 'admin_login.php';
          </script>";
    exit();
}

$toast = null;
if (isset($_SESSION['toast'])) {
    $toast = $_SESSION['toast'];
    unset($_SESSION['toast']); // clear so it doesn’t show again on reload
}

include '../php/connection.php';
$admin_id = $_SESSION['admin_id'];

// Fetch admin profile picture
$query = "SELECT profile_pic FROM admin WHERE admin_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

$profile_pic = (!empty($admin['profile_pic'])) ? $admin['profile_pic'] : 'uploads/dummy.png';

require_once 'php/events.php';
require_once 'php/announcements.php';
require_once 'php/modal_announcement.php';
require_once 'php/gallery.php';
require_once 'php/video.php';
require_once 'php/count.php';
require_once 'php/slides.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | E-KITABGHAR</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e72d27fd60.js" crossorigin="anonymous"></script>
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .nav-link-active {
            color: #4f46e5;
            background-color: #eff6ff;
        }

        .card-shadow {
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }

        .card-shadow:hover {
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }
    </style>
</head>

<body class="min-h-screen">

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white border-b border-slate-200">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left -->
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-600 p-2 rounded-lg">
                        <i class="bi bi-speedometer2 text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 leading-none">Admin Panel</h1>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mt-1">E-KITABGHAR</p>
                    </div>
                </div>

                <!-- Right -->
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-col text-right mr-2">
                        <span class="text-sm font-semibold text-slate-700">Administrator</span>
                        <span class="text-[10px] text-emerald-600 font-bold flex items-center justify-end gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online
                        </span>
                    </div>
                    <div class="relative group">
                        <img src="<?php echo $profile_pic; ?>"
                            class="w-10 h-10 rounded-full border border-slate-200 ring-2 ring-slate-50" alt="Admin">
                    </div>
                    <a href="php/logout.php" class="p-2 text-slate-400 hover:text-red-600 transition-colors">
                        <i class="fa-solid fa-power-off text-lg"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-[1400px] mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Welcome Header -->
        <div class="mb-10">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">System Controls</h2>
            <p class="text-slate-500 mt-1">Welcome back. Manage your platform settings and content from here.</p>
        </div>

        <!-- Quick Stats / Actions -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 card-shadow">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                        <i class="bi bi-eye text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Visitor Count</p>
                        <p class="text-2xl font-bold text-slate-900"><?= $visitor_count ?? 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-indigo-600 p-5 rounded-2xl shadow-lg shadow-indigo-100 flex items-center justify-between group cursor-pointer"
                onclick="window.location.href='manage_content.php'">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white/10 rounded-xl text-white">
                        <i class="bi bi-grid-fill text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-indigo-100">Content Studio</p>
                        <p class="text-lg font-bold text-white">Open Workspace</p>
                    </div>
                </div>
                <i class="bi bi-chevron-right text-white opacity-0 group-hover:opacity-100 transition-opacity"></i>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 card-shadow flex items-center justify-between group cursor-pointer"
                onclick="window.location.href='../index.php'">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-slate-50 rounded-xl text-slate-600">
                        <i class="bi bi-display text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Live Website</p>
                        <p class="text-lg font-bold text-slate-900">View Site</p>
                    </div>
                </div>
                <i class="bi bi-box-arrow-up-right text-slate-300"></i>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 card-shadow flex items-center justify-between group cursor-pointer"
                onclick="window.location.href='admin_profile.php'">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-slate-50 rounded-xl text-slate-600">
                        <i class="bi bi-person-gear text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Settings</p>
                        <p class="text-lg font-bold text-slate-900">My Account</p>
                    </div>
                </div>
                <i class="bi bi-gear-fill text-slate-300"></i>
            </div>
        </div>

        <!-- Section Title -->
        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
            <span class="w-1 h-6 bg-indigo-600 rounded-full"></span>
            Administrative Modules
        </h3>

        <!-- Main Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">

            <!-- Message to Students -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 card-shadow transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                        <i class="bi bi-chat-dots-fill text-xl"></i>
                    </div>
                    <span
                        class="text-xs font-bold text-blue-600 uppercase bg-blue-50 px-2 py-1 rounded">Messaging</span>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Message Students</h4>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">Broadcast official updates or send targeted
                    emails to your student body.</p>
                <button onclick="window.location.href='admin_message.php'"
                    class="w-full py-3 bg-slate-900 hover:bg-black text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    Open Manager <i class="bi bi-arrow-right text-sm"></i>
                </button>
            </div>

            <!-- Manage Exam Forms -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 card-shadow transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                        <i class="bi bi-file-earmark-check-fill text-xl"></i>
                    </div>
                    <span
                        class="text-xs font-bold text-emerald-600 uppercase bg-emerald-50 px-2 py-1 rounded">Examinations</span>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Exam Form Approvals</h4>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">Review and approve examination enrollment forms
                    submitted by students.</p>
                <button onclick="window.location.href='manage.php'"
                    class="w-full py-3 bg-slate-900 hover:bg-black text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    Manage Forms <i class="bi bi-arrow-right text-sm"></i>
                </button>
            </div>

            <!-- Schedule Exams -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 card-shadow transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-cyan-50 text-cyan-600 rounded-xl">
                        <i class="bi bi-calendar-check-fill text-xl"></i>
                    </div>
                    <span
                        class="text-xs font-bold text-cyan-600 uppercase bg-cyan-50 px-2 py-1 rounded">Scheduler</span>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Form Submission Dates</h4>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">Control the timeline for when students can submit
                    their exam forms.</p>
                <button onclick="window.location.href='admin_exam_manager.php'"
                    class="w-full py-3 bg-slate-900 hover:bg-black text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    Open Planner <i class="bi bi-arrow-right text-sm"></i>
                </button>
            </div>

            <!-- Contact Support -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 card-shadow transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-orange-50 text-orange-600 rounded-xl">
                        <i class="bi bi-headset text-xl"></i>
                    </div>
                    <span
                        class="text-xs font-bold text-orange-600 uppercase bg-orange-50 px-2 py-1 rounded">Support</span>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">User Helpdesk</h4>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">Respond to student inquiries and technical
                    assistance requests.</p>
                <button onclick="window.location.href='admin_contact_support.php'"
                    class="w-full py-3 bg-slate-900 hover:bg-black text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    View Tickets <i class="bi bi-arrow-right text-sm"></i>
                </button>
            </div>

            <!-- Feedbacks -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 card-shadow transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-yellow-50 text-yellow-600 rounded-xl">
                        <i class="bi bi-star-fill text-xl"></i>
                    </div>
                    <span
                        class="text-xs font-bold text-yellow-600 uppercase bg-yellow-50 px-2 py-1 rounded">Insights</span>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Student Feedback</h4>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">Monitor feedback logs to improve campus services
                    and digital portal.</p>
                <button onclick="window.location.href='admin_feedbacks.php'"
                    class="w-full py-3 bg-slate-900 hover:bg-black text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    Analyze Data <i class="bi bi-arrow-right text-sm"></i>
                </button>
            </div>

            <!-- Contribute Notes -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 card-shadow transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-teal-50 text-teal-600 rounded-xl">
                        <i class="bi bi-journals text-xl"></i>
                    </div>
                    <span
                        class="text-xs font-bold text-teal-600 uppercase bg-teal-50 px-2 py-1 rounded">Resources</span>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Library Notes</h4>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">Approve or moderate educational materials shared
                    by the community.</p>
                <button onclick="window.location.href='admin_manage_contribute_notes.php'"
                    class="w-full py-3 bg-slate-900 hover:bg-black text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    Manage Notes <i class="bi bi-arrow-right text-sm"></i>
                </button>
            </div>

            <!-- Student Register -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 card-shadow transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                        <i class="bi bi-person-badge text-xl"></i>
                    </div>
                    <span
                        class="text-xs font-bold text-indigo-600 uppercase bg-indigo-50 px-2 py-1 rounded">Registry</span>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Student Directory</h4>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">Quick access to a filterable list of all
                    currently registered students.</p>
                <button onclick="window.location.href='admin_student_register.php'"
                    class="w-full py-3 bg-slate-900 hover:bg-black text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    Open Registry <i class="bi bi-arrow-right text-sm"></i>
                </button>
            </div>

            <!-- Manage Students -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 card-shadow transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-violet-50 text-violet-600 rounded-xl">
                        <i class="bi bi-person-gear text-xl"></i>
                    </div>
                    <span
                        class="text-xs font-bold text-violet-600 uppercase bg-violet-50 px-2 py-1 rounded">Operations</span>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Account Management</h4>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">Handle batch uploads, password resets, and core
                    account operations.</p>
                <button onclick="window.location.href='manage_students.php'"
                    class="w-full py-3 bg-slate-900 hover:bg-black text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    Admin Portal <i class="bi bi-arrow-right text-sm"></i>
                </button>
            </div>

            <!-- Important Announcements -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 card-shadow transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-rose-50 text-rose-600 rounded-xl">
                        <i class="bi bi-megaphone-fill text-xl"></i>
                    </div>
                    <span
                        class="text-xs font-bold text-rose-600 uppercase bg-rose-50 px-2 py-1 rounded">Publicity</span>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Banner News</h4>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">Pin important notices directly to the public
                    website announcements bar.</p>
                <button onclick="window.location.href='admin_announcements.php'"
                    class="w-full py-3 bg-slate-900 hover:bg-black text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    Manage Bar <i class="bi bi-arrow-right text-sm"></i>
                </button>
            </div>

            <!-- Admin Auth -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 card-shadow transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-slate-100 text-slate-900 rounded-xl">
                        <i class="bi bi-shield-lock-fill text-xl"></i>
                    </div>
                    <span
                        class="text-xs font-bold text-slate-500 uppercase bg-slate-100 px-2 py-1 rounded">Security</span>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">System Credentials</h4>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">Update administrative access and manage superuser
                    security settings.</p>
                <button onclick="window.location.href='../admin_auth/admin_login_auth.php'"
                    class="w-full py-3 border-2 border-slate-900 text-slate-900 hover:bg-slate-900 hover:text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    Auth Portal <i class="bi bi-arrow-right text-sm"></i>
                </button>
            </div>

            <!-- Visitor Counter (Special Case for Form) -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 card-shadow transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-red-50 text-red-600 rounded-xl">
                        <i class="bi bi-bar-chart-fill text-xl"></i>
                    </div>
                    <span class="text-xs font-bold text-red-600 uppercase bg-red-50 px-2 py-1 rounded">Analytics</span>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Visitor Analytics</h4>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">Reset unique visitor metrics for a fresh tracking
                    cycle.</p>
                <form method="POST" action="adminpanel.php">
                    <button type="submit" name="reset"
                        class="w-full py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset Counter
                    </button>
                </form>
            </div>
        </div>
    </main>

    <!-- Floating Timer -->
    <div id="countdown-banner"
        class="fixed bottom-6 right-6 bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-4 z-[100]">
        <div class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></div>
        <p class="text-sm font-medium">Session Timeout: <span id="countdown"
                class="font-bold text-yellow-400">180</span>s</p>
    </div>

    <!-- Background Scripts -->
    <script>
        let inactivityTime = 180;
        let countdownDisplay = document.getElementById("countdown");

        function updateCountdown() {
            if (inactivityTime > 0) {
                inactivityTime--;
                countdownDisplay.innerText = inactivityTime;
            } else {
                window.location.href = "php/logout.php";
            }
        }
        setInterval(updateCountdown, 1000);

        // Toast Handler
        <?php if ($toast): ?>
            // Simple console log for now, can add a better UI toast if needed
            console.log("Toast: <?= $toast['message'] ?>");
        <?php endif; ?>
    </script>
</body>

</html>
