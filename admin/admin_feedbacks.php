<?php
session_start();
require '../php/connection.php';

if (!isset($_SESSION['admin_id'])) {
    echo "<script>
        alert('Unauthorized access! Please log in.');
        window.location.href = 'admin_login.php';
    </script>";
    exit();
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM feedback WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: admin_feedbacks.php");
    exit;
}

$limit = 8;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM feedback ORDER BY submitted_at DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

$total_sql = "SELECT COUNT(*) AS total FROM feedback";
$total_result = $conn->query($total_sql);
$total_feedbacks = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_feedbacks / $limit);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-KITABGHAR | Student Feedback</title>
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

        .table-shadow {
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
</head>

<body class="min-h-screen py-10 px-4 md:px-8">

    <div class="max-w-[1400px] mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="p-2.5 bg-indigo-100 text-indigo-600 rounded-xl"><i
                            class="bi bi-chat-square-text-fill"></i></span>
                    Student Feedbacks
                </h1>
                <p class="text-slate-500 mt-2">Monitor real-time academic and system feedback from the student body.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="export_feedbacks.php"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-emerald-100">
                    <i class="bi bi-download"></i> Export Data
                </a>
                <a href="adminpanel.php"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-50 transition-all">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>

        <!-- Table Container -->
        <div class="bg-white border border-slate-200 rounded-2xl table-shadow overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Student
                            </th>
                            <th
                                class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">
                                Satisfaction</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Message Log
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">
                                Registration</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center font-bold text-sm">
                                                <?= strtoupper(substr($row['name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-slate-900">
                                                    <?= htmlspecialchars($row['name']) ?></div>
                                                <div class="text-[11px] font-medium text-slate-500">
                                                    <?= htmlspecialchars($row['email']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-50 text-yellow-700 rounded-full text-xs font-bold border border-yellow-100">
                                            <?= $row['rating'] ?> <i class="bi bi-star-fill text-[10px]"></i>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-slate-600 max-w-sm truncate"
                                            title="<?= htmlspecialchars($row['message']) ?>">
                                            <?= htmlspecialchars($row['message']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                                            <i class="bi bi-calendar3"></i>
                                            <?= date("d M Y | h:i A", strtotime($row['submitted_at'])) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="?delete_id=<?= $row['id'] ?>"
                                            onclick="return confirm('Archive this feedback permanently?')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-all font-bold text-xs uppercase">
                                            <i class="bi bi-trash3-fill"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center">
                                    <i class="bi bi-chat-square-dots text-4xl text-slate-200 mb-3 block"></i>
                                    <p class="text-slate-400 font-bold tracking-tight">No submissions recorded yet.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Section -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-8 flex items-center justify-center gap-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>"
                        class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all cursor-pointer">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>"
                        class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-bold transition-all <?= ($i == $page) ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>"
                        class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all cursor-pointer">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>
