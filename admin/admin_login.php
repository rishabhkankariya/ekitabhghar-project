<?php
session_start();
$toast = $_SESSION['admin_login_toast'] ?? null;
unset($_SESSION['admin_login_toast']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-KITABGHAR | Admin Login</title>
    <link rel="icon" href="../img/E-KITABGHAR.png" type="image/x-icon">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    animation: {
                        'blob': "blob 7s infinite",
                    },
                    keyframes: {
                        blob: {
                            "0%": { transform: "translate(0px, 0px) scale(1)" },
                            "33%": { transform: "translate(30px, -50px) scale(1.1)" },
                            "66%": { transform: "translate(-20px, 20px) scale(0.9)" },
                            "100%": { transform: "translate(0px, 0px) scale(1)" }
                        }
                    }
                }
            }
        }
    </script>
</head>

<body
    class="bg-slate-900 font-sans text-slate-200 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Ambient Background Animation -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div
            class="absolute top-0 left-1/4 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob">
        </div>
        <div
            class="absolute top-0 right-1/4 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000">
        </div>
        <div
            class="absolute -bottom-32 left-1/3 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000">
        </div>
        <div
            class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20">
        </div>
    </div>

    <!-- Centered Card -->
    <div class="relative z-10 w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div
                class="w-20 h-20 mx-auto bg-slate-800 rounded-2xl flex items-center justify-center shadow-2xl mb-4 border border-slate-700 transform rotate-3 hover:rotate-6 transition-transform duration-300">
                <img src="../img/E-KITABGHAR.png" alt="Logo" class="w-12 h-12">
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Admin Portal</h1>
            <p class="text-slate-400 text-sm mt-1">Authorized Personnel Only</p>
        </div>

        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-8 shadow-2xl">
            <form action="php/login.php" method="POST" class="space-y-5">

                <!-- Username -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Admin
                        Username</label>
                    <div class="relative group">
                        <i
                            class="bi bi-person-badge-fill absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-purple-400 transition-colors"></i>
                        <input type="text" name="admin_username" required
                            class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-12 py-3.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all font-medium placeholder:text-slate-600"
                            placeholder="Enter username">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Password</label>
                    <div class="relative group">
                        <i
                            class="bi bi-shield-lock-fill absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-purple-400 transition-colors"></i>
                        <input type="password" id="admin_password" name="admin_password" required
                            class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-12 py-3.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all font-medium placeholder:text-slate-600"
                            placeholder="Enter password">
                        <button type="button" id="togglePassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white transition-colors">
                            <i class="bi bi-eye-slash-fill"></i>
                        </button>
                    </div>
                </div>

                <!-- Captcha -->
                <div class="p-4 bg-slate-900/30 rounded-xl border border-slate-700/50">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2 flex-1">
                            <img id="captcha-img" src="../php/captcha.php" alt="Captcha"
                                class="h-10 rounded-lg border border-slate-600 opacity-80">
                            <button type="button" id="refresh-captcha"
                                class="text-slate-400 hover:text-white transition-colors">
                                <i class="bi bi-arrow-repeat text-xl"></i>
                            </button>
                        </div>
                        <input type="text" name="captcha" placeholder="Code" required
                            class="w-24 bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-center text-sm font-mono tracking-widest text-white focus:ring-2 focus:ring-purple-500/50 focus:outline-none">
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" id="loginBtn"
                    class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-purple-500/25 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2 group">
                    <span id="btnText"><i class="bi bi-box-arrow-in-right mr-2"></i>Secure Login</span>
                    <div id="btnSpinner"
                        class="hidden w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-700/50 text-center">
                <a href="../index.php"
                    class="text-xs font-semibold text-slate-500 hover:text-white transition-colors flex items-center justify-center gap-2">
                    <i class="bi bi-arrow-left"></i> Back to Website
                </a>
            </div>
        </div>
    </div>

    <!-- PHP Toast Check -->
    <?php if ($toast): ?>
        <div id="toast"
            class="fixed top-6 right-6 z-50 flex items-center gap-3 px-6 py-4 bg-slate-800 text-white rounded-2xl shadow-2xl border-l-[6px] <?php echo $toast['type'] === 'error' ? 'border-red-500' : 'border-green-500'; ?> animate-bounce-in">
            <i
                class="bi <?php echo $toast['type'] === 'error' ? 'bi-x-circle-fill text-red-500' : 'bi-check-circle-fill text-green-500'; ?> text-xl"></i>
            <div>
                <h4 class="font-bold text-sm"><?php echo $toast['type'] === 'error' ? 'Access Denied' : 'Success'; ?></h4>
                <p class="text-xs text-slate-400"><?php echo htmlspecialchars($toast['message']); ?></p>
            </div>
        </div>
        <script>
            setTimeout(() => {
                const t = document.getElementById('toast');
                if (t) {
                    t.style.opacity = '0';
                    t.style.transform = 'translateY(-20px)';
                    setTimeout(() => t.remove(), 500);
                }
            }, 4000);
        </script>
    <?php endif; ?>

    <script>
        // Toggle Password
        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('admin_password');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            }
        });

        // Refresh Captcha
        document.getElementById('refresh-captcha').addEventListener('click', function () {
            document.getElementById('captcha-img').src = '../php/captcha.php?' + Date.now();
        });

        // Login Submission
        document.querySelector('form').addEventListener('submit', function () {
            const btn = document.getElementById('loginBtn');
            const txt = document.getElementById('btnText');
            const spin = document.getElementById('btnSpinner');
            btn.disabled = true;
            txt.textContent = "Verifying...";
            spin.classList.remove('hidden');
        });
    </script>
</body>

</html>
