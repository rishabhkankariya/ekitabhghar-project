<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo "<script>window.location.href = 'admin_login.php';</script>";
    exit();
}

require '../config/send_mail.php';

// Database Connection
require_once '../php/connection.php';

$toast_message = "";
$toast_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_date = $_POST["start_date"] ?? "";
    $end_date = $_POST["end_date"] ?? "";

    if (empty($start_date) || empty($end_date)) {
        $toast_message = "Please provide both start and end dates!";
        $toast_type = "error";
    } else {
        $check = $conn->query("SELECT * FROM exam_settings LIMIT 1");

        if ($check->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE exam_settings SET start_date = ?, end_date = ?");
        } else {
            $stmt = $conn->prepare("INSERT INTO exam_settings (start_date, end_date) VALUES (?, ?)");
        }
        $stmt->bind_param("ss", $start_date, $end_date);

        if ($stmt->execute()) {
            $toast_message = "Exam settings updated successfully!";
            $toast_type = "success";

            // Send email notification - Note: using centralized connection logic
            sendExamNotification($start_date, $end_date);
        } else {
            $toast_message = "Error updating settings: " . $stmt->error;
            $toast_type = "error";
        }

        $stmt->close();
    }
}

$current_start_date = "";
$current_end_date = "";
$exam_query = "SELECT * FROM exam_settings LIMIT 1";
$exam_result = $conn->query($exam_query);
if ($exam_result && $exam_result->num_rows > 0) {
    $exam = $exam_result->fetch_assoc();
    $current_start_date = $exam["start_date"];
    $current_end_date = $exam["end_date"];
}

// Check if currently active
$is_active = false;
$now = date("Y-m-d H:i:s");
if (!empty($current_start_date) && !empty($current_end_date)) {
    if ($now >= $current_start_date && $now <= $current_end_date) {
        $is_active = true;
    }
}


// Send Exam Notification Email
function sendExamNotification($start, $end)
{
    global $conn; // Use the main connection

    $subject = 'Exam Form is Live Don’t Miss Out!';
    $startFormatted = date("F j, Y", strtotime($start));
    $endFormatted = date("F j, Y", strtotime($end));

    $htmlBody = "
        <div style='font-family: Arial, sans-serif; background-color: #fefefe; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
            <h2 style='color: #007BFF;'>🎓 Exam Form Now Open!</h2>
            <p>Hello Student,</p>
            <p>Your exam form is now live and will be available from:</p>
            <p style='font-size: 18px; margin: 10px 0;'><strong>$startFormatted</strong> to <strong>$endFormatted</strong></p>
            <p>Please visit your dashboard and complete the form before the deadline.</p>
            <br>
            <p style='margin-top: 20px;'>Regards,<br><strong>E-Kitabghar Team</strong></p>
        </div>";

    $result = $conn->query("SELECT email, full_name FROM student_accounts WHERE email IS NOT NULL AND email != ''");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            sendEmail($row['email'], $row['full_name'], $subject, $htmlBody);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Scheduler | Admin Portal</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e72d27fd60.js" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .card-shadow {
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }

        .input-focus:focus {
            border-color: #4f46e5;
            ring: 4px;
            ring-color: rgba(79, 70, 229, 0.1);
        }
    </style>
</head>

<body class="min-h-screen py-12 px-4 md:px-8 flex items-center justify-center">

    <div class="w-full max-w-2xl">
        <!-- Floating Navigation -->
        <div class="mb-8 flex items-center justify-between">
            <a href="adminpanel.php"
                class="flex items-center gap-2 text-slate-500 hover:text-indigo-600 font-bold transition-all text-sm">
                <i class="bi bi-arrow-left"></i> Return to Dashboard
            </a>
            <div
                class="flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest <?= $is_active ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200' ?>">
                <span
                    class="w-2 h-2 rounded-full <?= $is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' ?>"></span>
                <?= $is_active ? 'Public Form Live' : 'Form Inactive' ?>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 card-shadow overflow-hidden">
            <!-- Header -->
            <div class="bg-indigo-600 px-8 py-10 text-white relative overflow-hidden">
                <i class="bi bi-calendar-event absolute -bottom-6 -right-6 text-9xl opacity-10 rotate-12"></i>
                <div class="relative z-10">
                    <h1 class="text-3xl font-extrabold tracking-tight">Exam Form Scheduler</h1>
                    <p class="text-indigo-100 mt-2 text-sm leading-relaxed max-w-md">Set the active window for student
                        exam form submissions. Updating this will trigger a campus-wide alert.</p>
                </div>
            </div>

            <div class="p-8">
                <!-- Status Bar -->
                <?php if (!empty($current_start_date)): ?>
                    <div
                        class="mb-10 p-4 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-white rounded-xl card-shadow flex items-center justify-center text-indigo-600">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Active Schedule
                                </p>
                                <p class="text-xs font-bold text-slate-700 mt-0.5">
                                    <?= date("M d", strtotime($current_start_date)) ?> ➔
                                    <?= date("M d, Y", strtotime($current_end_date)) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Start Date -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Opening
                                Date & Time</label>
                            <div class="relative">
                                <i
                                    class="bi bi-calendar-check absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="datetime-local" name="start_date"
                                    value="<?= htmlspecialchars($current_start_date) ?>" required
                                    class="w-full pl-12 pr-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-700 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-400 transition-all">
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Closing
                                Date & Time</label>
                            <div class="relative">
                                <i class="bi bi-calendar-x absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="datetime-local" name="end_date"
                                    value="<?= htmlspecialchars($current_end_date) ?>" required
                                    class="w-full pl-12 pr-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-700 outline-none focus:ring-4 focus:ring-rose-500/10 focus:bg-white focus:border-rose-300 transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full py-4 bg-slate-900 hover:bg-black text-white font-bold rounded-2xl shadow-xl shadow-slate-900/10 active:scale-[0.99] transition-all flex items-center justify-center gap-3">
                            <i class="bi bi-broadcast"></i> Update Settings & Notify Students
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-8 border-t border-slate-50 text-center">
                    <div
                        class="inline-flex items-center gap-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <i class="bi bi-info-circle"></i>
                        Automated email notifications will be sent to all registered students.
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Branding -->
        <p class="text-center text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em] mt-8">
            Campus Ecosystem Manager • EKITABGHAR v2.0
        </p>
    </div>

    <!-- Toast Handler -->
    <?php if (!empty($toast_message)): ?>
        <div id="toast"
            class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-4 animate-bounce z-[100]">
            <i
                class="bi <?= $toast_type === 'success' ? 'bi-check-circle-fill text-emerald-400' : 'bi-exclamation-circle-fill text-rose-400' ?> text-xl"></i>
            <p class="text-sm font-bold"><?= $toast_message ?></p>
        </div>
        <script>setTimeout(() => { document.getElementById('toast').remove(); }, 5000);</script>
    <?php endif; ?>

</body>

</html>