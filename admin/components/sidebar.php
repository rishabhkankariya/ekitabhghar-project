<?php
// admin/components/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 -translate-x-full overflow-y-auto custom-scrollbar">
    <div class="flex flex-col h-full">
        <!-- Logo Area -->
        <div class="p-6 flex items-center gap-3">
            <div
                class="w-10 h-10 bg-primary-500 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                <i class="bi bi-mortarboard-fill text-white text-xl"></i>
            </div>
            <span class="text-xl font-display font-bold text-white tracking-tight">EKITAB<span
                    class="text-primary-400">GHAR</span></span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-4 space-y-1">
            <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Main Menu</p>

            <a href="adminpanel.php"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 <?php echo $current_page == 'adminpanel.php' ? 'sidebar-item-active text-white' : 'hover:bg-slate-800 hover:text-white'; ?>">
                <i class="bi bi-grid-1x2"></i>
                <span class="font-medium text-sm">Dashboard</span>
            </a>

            <div class="pt-4">
                <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Student Management
                </p>
                <a href="manage_students.php"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 <?php echo $current_page == 'manage_students.php' ? 'sidebar-item-active text-white' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i class="bi bi-people"></i>
                    <span class="font-medium text-sm">Bulk Management</span>
                </a>
                <a href="admin_student_register.php"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 <?php echo $current_page == 'admin_student_register.php' ? 'sidebar-item-active text-white' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i class="bi bi-person-badge"></i>
                    <span class="font-medium text-sm">Student Viewer</span>
                </a>
            </div>

            <div class="pt-4">
                <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Academics</p>
                <a href="manage.php"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 <?php echo $current_page == 'manage.php' ? 'sidebar-item-active text-white' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i class="bi bi-file-earmark-text"></i>
                    <span class="font-medium text-sm">Exam Forms</span>
                </a>
                <a href="admin_exam_manager.php"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 <?php echo $current_page == 'admin_exam_manager.php' ? 'sidebar-item-active text-white' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i class="bi bi-calendar-event"></i>
                    <span class="font-medium text-sm">Exam Scheduler</span>
                </a>
                <a href="admin_manage_contribute_notes.php"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 <?php echo $current_page == 'admin_manage_contribute_notes.php' ? 'sidebar-item-active text-white' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i class="bi bi-journals"></i>
                    <span class="font-medium text-sm">Contribute Notes</span>
                </a>
            </div>

            <div class="pt-4">
                <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Communication</p>
                <a href="admin_message.php"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 <?php echo $current_page == 'admin_message.php' ? 'sidebar-item-active text-white' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i class="bi bi-chat-left-dots"></i>
                    <span class="font-medium text-sm">Messages</span>
                </a>
                <a href="admin_announcements.php"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 <?php echo $current_page == 'admin_announcements.php' ? 'sidebar-item-active text-white' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i class="bi bi-megaphone"></i>
                    <span class="font-medium text-sm">Announcements</span>
                </a>
                <a href="admin_feedbacks.php"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 <?php echo $current_page == 'admin_feedbacks.php' ? 'sidebar-item-active text-white' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i class="bi bi-star"></i>
                    <span class="font-medium text-sm">Feedbacks</span>
                </a>
            </div>

            <div class="pt-4">
                <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Settings</p>
                <a href="../admin_auth/admin_login_auth.php"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 hover:bg-slate-800 hover:text-white">
                    <i class="bi bi-shield-lock"></i>
                    <span class="font-medium text-sm">Admin Auth</span>
                </a>
            </div>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-800">
            <div class="bg-slate-800/50 rounded-2xl p-4">
                <div class="flex items-center gap-3 mb-3">
                    <img src="<?php echo $profile_pic; ?>" alt="Admin"
                        class="w-10 h-10 rounded-xl object-cover ring-2 ring-primary-500/20">
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-white truncate">
                            <?php echo $admin_name; ?>
                        </p>
                        <p class="text-[10px] text-slate-400">Super Admin</p>
                    </div>
                </div>
                <a href="php/logout.php"
                    class="flex items-center justify-center gap-2 w-full py-2 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white rounded-xl text-xs font-bold transition-all duration-300">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>
</aside>
