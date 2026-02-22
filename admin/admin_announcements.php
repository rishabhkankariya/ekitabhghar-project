<?php
// DB Connection
require '../php/connection.php';

// Add announcement
if (isset($_POST['add'])) {
    $msg = $conn->real_escape_string($_POST['message']);
    $conn->query("INSERT INTO imp_announcements (message) VALUES ('$msg')");
    echo "<script>window.location.href='admin_announcements.php';</script>";
    exit;
}

// Delete announcement
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM imp_announcements WHERE id = $id");
    echo "<script>window.location.href='admin_announcements.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements | Admin</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
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

        .btn-shadow:hover {
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            transform: translateY(-1px);
        }
    </style>
</head>

<body class="min-h-screen py-10 px-4 md:px-8">

    <div class="max-w-4xl mx-auto">
        <!-- Dashboard Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 mb-10">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="p-2.5 bg-rose-100 text-rose-600 rounded-xl"><i class="bi bi-megaphone-fill"></i></span>
                    Portal Announcements
                </h1>
                <p class="text-slate-500 mt-2">Publish critical updates directly to the college homepage.</p>
            </div>
            <a href="adminpanel.php"
                class="inline-flex items-center gap-2 text-slate-600 hover:text-rose-600 font-bold transition-all transition-colors">
                <i class="bi bi-arrow-left"></i> Dashboard View
            </a>
        </div>

        <!-- Add New Section -->
        <div class="bg-white border border-slate-200 rounded-2xl p-8 card-shadow mb-10">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6 block px-1">Compose New Notice
            </h3>
            <form method="POST">
                <div class="mb-5">
                    <textarea name="message" rows="3" required placeholder="Type the announcement details here..."
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:bg-white focus:border-rose-300 transition-all"></textarea>
                </div>
                <button type="submit" name="add"
                    class="inline-flex items-center gap-2 px-8 py-3.5 bg-slate-900 hover:bg-black text-white text-sm font-bold rounded-xl transition-all btn-shadow">
                    <i class="bi bi-plus-lg"></i> Dispatch Announcement
                </button>
            </form>
        </div>

        <!-- List Section -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6 block px-1">Active Announcements
            </h3>

            <?php
            $result = $conn->query("SELECT * FROM imp_announcements ORDER BY created_at DESC");
            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    $id = $row['id'];
                    $msg = htmlspecialchars($row['message']);
                    $date = date("M d, Y • h:i A", strtotime($row['created_at']));
                    ?>
                    <div
                        class="bg-white border border-slate-200 rounded-2xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 card-shadow transition-all border-l-4 border-l-rose-500">
                        <div class="flex-1">
                            <p class="text-slate-700 text-sm font-medium leading-relaxed mb-2"><?= $msg ?></p>
                            <div class="flex items-center gap-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <span class="flex items-center gap-1.5"><i class="bi bi-clock"></i> <?= $date ?></span>
                                <span class="flex items-center gap-1.5 text-emerald-500"><i class="bi bi-patch-check-fill"></i>
                                    Public Live</span>
                            </div>
                        </div>
                        <div class="flex shrink-0">
                            <a href="?delete=<?= $id ?>"
                                onclick="return confirm('Archive this announcement? It will be removed from the public page.')"
                                class="w-10 h-10 flex items-center justify-center bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all transition-colors group">
                                <i class="bi bi-trash3-fill"></i>
                            </a>
                        </div>
                    </div>
                <?php
                endwhile;
            else:
                ?>
                <div class="bg-white border border-dashed border-slate-300 rounded-2xl p-16 text-center">
                    <div class="w-16 h-16 bg-slate-50 flex items-center justify-center rounded-2xl mx-auto mb-4">
                        <i class="bi bi-megaphone text-slate-300 text-2xl"></i>
                    </div>
                    <p class="text-slate-400 font-bold tracking-tight">Current announcement queue is empty.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>
