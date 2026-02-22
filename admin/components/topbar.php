<?php
// admin/components/topbar.php
?>
<!-- Topbar -->
<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200 py-3 px-4 lg:px-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <button id="sidebarToggle"
                class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition-colors">
                <i class="bi bi-list text-2xl"></i>
            </button>
            <div class="hidden sm:flex items-center relative">
                <i class="bi bi-search absolute left-3 text-slate-400"></i>
                <input type="text" placeholder="Search anything..."
                    class="pl-10 pr-4 py-2 bg-slate-100 border-none rounded-xl text-sm w-64 focus:ring-2 focus:ring-primary-500/20 transition-all outline-none">
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-4">
            <!-- Notifications -->
            <button class="relative p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition-colors">
                <i class="bi bi-bell text-xl"></i>
                <span class="absolute top-2 right-2.5 w-2 h-2 bg-primary-500 rounded-full border-2 border-white"></span>
            </button>

            <!-- Session Timer -->
            <div
                class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-primary-50 text-primary-700 rounded-xl border border-primary-100">
                <i class="bi bi-clock-history text-sm"></i>
                <span id="session-countdown" class="text-xs font-bold">180s</span>
            </div>

            <div class="h-8 w-px bg-slate-200 mx-1"></div>

            <!-- Profile Dropdown -->
            <div class="relative group">
                <button class="flex items-center gap-2 p-1 rounded-xl hover:bg-slate-100 transition-all">
                    <img src="<?php echo $profile_pic; ?>" alt="Admin"
                        class="w-8 h-8 rounded-lg object-cover ring-2 ring-primary-500/10">
                    <div class="hidden sm:block text-left">
                        <p class="text-xs font-bold text-slate-900 leading-none">
                            <?php echo $admin_name; ?>
                        </p>
                        <p class="text-[10px] text-slate-500 mt-0.5">Admin</p>
                    </div>
                    <i class="bi bi-chevron-down text-xs text-slate-400"></i>
                </button>

                <!-- Simple Dropdown (Hover) -->
                <div
                    class="absolute right-0 top-full mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right scale-95 group-hover:scale-100 z-50 overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-50">
                        <p class="text-xs text-slate-500">Welcome back,</p>
                        <p class="text-sm font-bold text-slate-900 truncate">
                            <?php echo $admin_name; ?>
                        </p>
                    </div>
                    <div class="p-2">
                        <a href="admin_profile.php"
                            class="flex items-center gap-3 px-3 py-2 text-sm text-slate-600 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-all">
                            <i class="bi bi-person"></i> My Profile
                        </a>
                        <a href="admin_auth/admin_login_auth.php"
                            class="flex items-center gap-3 px-3 py-2 text-sm text-slate-600 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-all">
                            <i class="bi bi-shield-lock"></i> Security
                        </a>
                    </div>
                    <div class="p-2 border-t border-slate-50">
                        <a href="php/logout.php"
                            class="flex items-center gap-3 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-xl transition-all font-medium">
                            <i class="bi bi-box-arrow-right"></i> Sign Out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
