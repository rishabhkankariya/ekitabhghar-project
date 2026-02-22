<?php
session_start();

$special_admin_password = "poly_ekitabghar@2025"; // Change this to your secure password
$error = ""; // Variable to store error messages

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $special_password = $_POST['special_password'];

    if ($special_password === $special_admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: insert_admin_form.php"); // Redirect to admin panel
        exit;
    } else {
        $error = "Incorrect Security Token. Access Denied.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Authentication | E-KITABGHAR</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .auth-card {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>

<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">

    <!-- Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
        <div class="bg-white rounded-3xl overflow-hidden auth-card px-8 pt-10 pb-12">
            <!-- Header -->
            <div class="text-center mb-10">
                <div
                    class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-indigo-200">
                    <i class="bi bi-shield-lock-fill text-white text-3xl"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Security Access</h1>
                <p class="text-slate-500 text-sm mt-1">Enter your special administrative token.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="mb-6 px-4 py-3 bg-rose-50 border border-rose-100 rounded-xl flex items-center gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-rose-500"></i>
                    <p class="text-xs font-bold text-rose-600"><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Admin
                        Security Pass</label>
                    <div class="relative">
                        <i class="bi bi-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="password" name="special_password" required placeholder="Enter token..."
                            class="w-full pl-12 pr-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-300 transition-all outline-none">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-xl shadow-indigo-200 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    Authorize Access <i class="bi bi-chevron-right text-xs"></i>
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-slate-100 text-center">
                <a href="javascript:history.back()"
                    class="text-xs font-bold text-slate-400 hover:text-slate-600 flex items-center justify-center gap-2 transition-colors">
                    <i class="bi bi-arrow-left"></i> Return to Terminal
                </a>
            </div>
        </div>

        <!-- Footer Info -->
        <p class="text-center text-slate-500 text-[10px] font-bold uppercase tracking-[0.2em] mt-8">
            Internal Systems • SECURE-AUTH-2025
        </p>
    </div>

</body>

</html>
