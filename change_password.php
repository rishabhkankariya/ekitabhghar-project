<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: student_login.html");
    exit;
}
// Ensure we don't trap users who don't need to change password, 
// unless they navigated here manually. 
// But if forced, they must be here.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | E-KITABGHAR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full border border-slate-100">
        <div class="flex flex-col items-center mb-8">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center text-red-600 mb-4">
                <i class="bi bi-shield-lock-fill text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-800">Action Required</h2>
            <p class="text-slate-500 text-center mt-2">For security reasons, you must change your temporary password
                before proceeding.</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span class="text-sm font-medium"><?= htmlspecialchars($_GET['error']) ?></span>
            </div>
        <?php endif; ?>

        <form action="php/change_password_action.php" method="POST" id="passwordForm">
            <div class="mb-5">
                <label class="block text-slate-700 text-sm font-semibold mb-2 ml-1">New Password</label>
                <div class="relative">
                    <input type="password" name="new_password" id="new_password" required minlength="8"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-slate-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all pr-12"
                        placeholder="Enter new password">
                    <button type="button" onclick="togglePassword('new_password', 'toggleNew')"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 transition">
                        <i class="bi bi-eye" id="toggleNew"></i>
                    </button>
                </div>
                <p class="text-[11px] text-slate-400 mt-1.5 ml-1">At least 8 characters</p>
            </div>

            <div class="mb-8">
                <label class="block text-slate-700 text-sm font-semibold mb-2 ml-1">Confirm Password</label>
                <div class="relative">
                    <input type="password" name="confirm_password" id="confirm_password" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-slate-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all pr-12"
                        placeholder="Confirm your password">
                    <button type="button" onclick="togglePassword('confirm_password', 'toggleConfirm')"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 transition">
                        <i class="bi bi-eye" id="toggleConfirm"></i>
                    </button>
                </div>
            </div>

            <button type="submit" id="updateBtn"
                class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-indigo-200 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                <span id="btnText">Update Password</span>
                <div id="btnSpinner"
                    class="hidden w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
            </button>
        </form>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        document.getElementById('passwordForm').addEventListener('submit', function (e) {
            const newPass = document.getElementById('new_password').value;
            const confirmPass = document.getElementById('confirm_password').value;

            if (newPass !== confirmPass) {
                e.preventDefault();
                alert("Passwords do not match!");
                return;
            }

            const btn = document.getElementById('updateBtn');
            const txt = document.getElementById('btnText');
            const spin = document.getElementById('btnSpinner');
            btn.disabled = true;
            txt.textContent = "Updating...";
            spin.classList.remove('hidden');
        });
    </script>
</body>

</html>
