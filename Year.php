<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: student_login.html");
    exit;
}
require_once 'php/connection.php';

$email = $_SESSION['user_email'];
$stmt = $conn->prepare("SELECT full_name, profile_image FROM student_accounts WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user_res = $stmt->get_result();
$user = $user_res->fetch_assoc();

// Force Profile Image Update
if (!$user || empty($user['profile_image']) || $user['profile_image'] === 'users.png') {
    header("Location: profile_update.php?error=Access Denied! You must upload a profile image first.");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Material | Kitabghar</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicon_logoai/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_logoai/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_logoai/favicon-16x16.png">
    <link rel="manifest" href="favicon_logoai/site.webmanifest">
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

        .y-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
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
                    <h2
                        class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">
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
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition-all font-medium">
                            <i class="bi bi-person-gear text-xl"></i> Update Account
                        </a>
                    </li>
                    <li>
                        <a href="Year.php"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-50 text-indigo-600 transition-all font-semibold">
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
                    <h1 class="text-xl font-semibold text-slate-800 hidden sm:block">Study Material</h1>
                    <h1 class="text-lg font-semibold text-slate-800 sm:hidden">Library</h1>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-medium text-slate-900">
                            <?= htmlspecialchars($user['full_name']); ?>
                        </p>
                        <p class="text-xs text-slate-500">Student Account</p>
                    </div>
                    <?php $profile_pic = !empty($user['profile_image']) ? "php/uploads/" . $user['profile_image'] : "img/users.png"; ?>
                    <img src="<?= htmlspecialchars($profile_pic); ?>"
                        class="w-10 h-10 rounded-full object-cover ring-2 ring-indigo-500/20 shadow-sm" alt="Profile" />
                </div>
            </div>
        </header>

        <div class="p-6 lg:p-10 max-w-7xl mx-auto">
            <div class="text-center mb-16" data-aos="fade-down">
                <span
                    class="inline-block px-4 py-1.5 mb-4 text-xs font-bold tracking-widest text-indigo-600 uppercase bg-indigo-50 rounded-full">Academic
                    Resources</span>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">Level Up Your <span
                        class="text-indigo-600">Learning</span></h1>
                <p class="text-lg text-slate-500 max-w-2xl mx-auto">Select your current academic year to access
                    organized study materials and notes.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Year 1 -->
                <div class="group relative" data-aos="fade-up">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-violet-600/20 rounded-[3rem] blur-2xl group-hover:blur-3xl transition-all opacity-0 group-hover:opacity-100">
                    </div>
                    <div
                        class="relative bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100 hover:border-indigo-100 transition-all duration-500 h-full flex flex-col">
                        <div
                            class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-8 group-hover:scale-110 transition-transform">
                            <i class="bi bi-mortarboard-fill text-3xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 mb-2">1st Year</h2>
                        <p class="text-slate-500 text-sm mb-8">Foundation subjects and introductory modules.</p>
                        <div class="mt-auto space-y-4">
                            <a href="notes/firstsem.html" class="flex items-center justify-between p-2 group/link">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover/link:bg-indigo-600 group-hover/link:text-white transition-all">
                                        <span class="font-bold text-sm">01</span>
                                    </div>
                                    <span
                                        class="font-bold text-slate-700 group-hover/link:text-indigo-600 transition-colors">Semester
                                        I</span>
                                </div>
                                <i
                                    class="bi bi-chevron-right text-slate-300 group-hover/link:text-indigo-600 group-hover/link:translate-x-1 transition-all"></i>
                            </a>
                            <a href="notes/secondsem.html" class="flex items-center justify-between p-2 group/link">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover/link:bg-indigo-600 group-hover/link:text-white transition-all">
                                        <span class="font-bold text-sm">02</span>
                                    </div>
                                    <span
                                        class="font-bold text-slate-700 group-hover/link:text-indigo-600 transition-colors">Semester
                                        II</span>
                                </div>
                                <i
                                    class="bi bi-chevron-right text-slate-300 group-hover/link:text-indigo-600 group-hover/link:translate-x-1 transition-all"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Year 2 -->
                <div class="group relative" data-aos="fade-up" data-aos-delay="100">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-violet-600/20 to-fuchsia-600/20 rounded-[3rem] blur-2xl group-hover:blur-3xl transition-all opacity-0 group-hover:opacity-100">
                    </div>
                    <div
                        class="relative bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100 hover:border-violet-100 transition-all duration-500 h-full flex flex-col">
                        <div
                            class="w-16 h-16 bg-violet-50 rounded-2xl flex items-center justify-center text-violet-600 mb-8 group-hover:scale-110 transition-transform">
                            <i class="bi bi-palette-fill text-3xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 mb-2">2nd Year</h2>
                        <p class="text-slate-500 text-sm mb-8">Core specialization and intermediate concepts.</p>
                        <div class="mt-auto space-y-4">
                            <a href="notes/thirdsem.html" class="flex items-center justify-between p-2 group/link">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover/link:bg-violet-600 group-hover/link:text-white transition-all">
                                        <span class="font-bold text-sm">03</span>
                                    </div>
                                    <span
                                        class="font-bold text-slate-700 group-hover/link:text-violet-600 transition-colors">Semester
                                        III</span>
                                </div>
                                <i
                                    class="bi bi-chevron-right text-slate-300 group-hover/link:text-violet-600 group-hover/link:translate-x-1 transition-all"></i>
                            </a>
                            <a href="notes/fourthsem.html" class="flex items-center justify-between p-2 group/link">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover/link:bg-violet-600 group-hover/link:text-white transition-all">
                                        <span class="font-bold text-sm">04</span>
                                    </div>
                                    <span
                                        class="font-bold text-slate-700 group-hover/link:text-violet-600 transition-colors">Semester
                                        IV</span>
                                </div>
                                <i
                                    class="bi bi-chevron-right text-slate-300 group-hover/link:text-violet-600 group-hover/link:translate-x-1 transition-all"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Year 3 -->
                <div class="group relative" data-aos="fade-up" data-aos-delay="200">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 to-teal-600/20 rounded-[3rem] blur-2xl group-hover:blur-3xl transition-all opacity-0 group-hover:opacity-100">
                    </div>
                    <div
                        class="relative bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100 hover:border-emerald-100 transition-all duration-500 h-full flex flex-col">
                        <div
                            class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 mb-8 group-hover:scale-110 transition-transform">
                            <i class="bi bi-award-fill text-3xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 mb-2">3rd Year</h2>
                        <p class="text-slate-500 text-sm mb-8">Advanced modules and graduation projects.</p>
                        <div class="mt-auto space-y-4">
                            <a href="notes/fifthsem.html" class="flex items-center justify-between p-2 group/link">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover/link:bg-emerald-600 group-hover/link:text-white transition-all">
                                        <span class="font-bold text-sm">05</span>
                                    </div>
                                    <span
                                        class="font-bold text-slate-700 group-hover/link:text-emerald-600 transition-colors">Semester
                                        V</span>
                                </div>
                                <i
                                    class="bi bi-chevron-right text-slate-300 group-hover/link:text-emerald-600 group-hover/link:translate-x-1 transition-all"></i>
                            </a>
                            <a href="notes/sixthsem.html" class="flex items-center justify-between p-2 group/link">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover/link:bg-emerald-600 group-hover/link:text-white transition-all">
                                        <span class="font-bold text-sm">06</span>
                                    </div>
                                    <span
                                        class="font-bold text-slate-700 group-hover/link:text-emerald-600 transition-colors">Semester
                                        VI</span>
                                </div>
                                <i
                                    class="bi bi-chevron-right text-slate-300 group-hover/link:text-emerald-600 group-hover/link:translate-x-1 transition-all"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-20 text-center">
                <a href="dashboard.php"
                    class="inline-flex items-center gap-3 bg-slate-900 text-white px-10 py-4 rounded-2xl font-bold hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 active:scale-95 group">
                    <i class="bi bi-arrow-left group-hover:-translate-x-1 transition-transform"></i> Return to Dashboard
                </a>
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
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

        AOS.init({ duration: 800, once: true });
    </script>
</body>

</html>