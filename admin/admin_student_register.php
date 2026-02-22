<?php
session_start();
include '../php/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle Single Delete Action
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM student_accounts WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: admin_student_register.php?msg=Student deleted successfully");
    } else {
        header("Location: admin_student_register.php?error=Failed to delete student");
    }
    exit;
}

// Search Logic
$where = "1";
$search = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where .= " AND (full_name LIKE '%$search%' OR roll_no LIKE '%$search%' OR email LIKE '%$search%' OR course LIKE '%$search%')";
}

// Fetch Students (Simple View)
$sql = "SELECT id, roll_no, full_name, email, course, admission_year, account_status FROM student_accounts WHERE $where ORDER BY id DESC LIMIT 50";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Viewer - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/e72d27fd60.js" crossorigin="anonymous"></script>
    <!-- AOS Animation -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- Bootstrap CSS for button consistency if needed, though Tailwind is primarily used -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <i class="bi bi-people-fill text-blue-600"></i>
                Registered Students Viewer
            </h1>
            <a href="adminpanel.php" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- Search Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <form action="" method="GET" class="flex gap-4">
                <div class="relative flex-1">
                    <i class="bi bi-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                        placeholder="Quick search by Name, Roll No, Course..."
                        class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none">
                </div>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors shadow-lg shadow-blue-500/30">
                    Search
                </button>
                <?php if (!empty($search)): ?>
                    <a href="admin_student_register.php"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-6 py-3 rounded-lg font-semibold transition-colors flex items-center">
                        Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($_GET['msg'])): ?>
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200 flex items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <!-- Student List Table -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-sm uppercase tracking-wider">
                            <th class="p-5 font-semibold">Roll No</th>
                            <th class="p-5 font-semibold">Student Name</th>
                            <th class="p-5 font-semibold">Course & Intake Year</th>
                            <th class="p-5 font-semibold">Contact Email</th>
                            <th class="p-5 font-semibold text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-blue-50/50 transition-colors group">
                                    <td class="p-5 font-mono text-gray-600 font-medium">
                                        <?= htmlspecialchars($row['roll_no']) ?>
                                    </td>
                                    <td class="p-5">
                                        <div class="font-bold text-gray-800 text-lg"><?= htmlspecialchars($row['full_name']) ?>
                                        </div>
                                        <div
                                            class="text-xs text-<?= $row['account_status'] == 'active' ? 'green' : 'red' ?>-500 font-semibold uppercase mt-0.5">
                                            <?= htmlspecialchars($row['account_status']) ?>
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <div
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-purple-50 text-purple-700 rounded-full text-sm font-medium border border-purple-100">
                                            <i class="bi bi-mortarboard-fill text-purple-400"></i>
                                            <?= htmlspecialchars($row['course']) ?>
                                            <span class="text-purple-300">|</span>
                                            <?= htmlspecialchars($row['admission_year']) ?>
                                        </div>
                                    </td>
                                    <td class="p-5 text-gray-600">
                                        <?= htmlspecialchars($row['email']) ?>
                                    </td>
                                    <td class="p-5 text-center">
                                        <a href="admin_student_register.php?delete_id=<?= $row['id'] ?>"
                                            onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars($row['full_name']) ?>? This action is irreversible.')"
                                            class="inline-block p-2 ml-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all"
                                            title="Delete Student">
                                            <i class="bi bi-trash3-fill text-xl"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <i class="bi bi-person-x text-5xl mb-4 text-gray-300"></i>
                                        <p class="text-lg font-medium">No students found.</p>
                                        <p class="text-sm">Try adjusting your search terms.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer / Stats -->
            <div
                class="p-4 bg-gray-50 border-t border-gray-200 text-sm text-gray-500 flex justify-between items-center">
                <span>Showing top 50 results</span>
                <span>Total Active Students:
                    <?= $conn->query("SELECT count(*) FROM student_accounts")->fetch_row()[0] ?></span>
            </div>
        </div>

        <!-- Admin Action Cards Section -->
        <div class="mt-16 mb-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                <i class="bi bi-grid-3x3-gap-fill text-indigo-600"></i>
                Administrative Quick Actions
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Message to Students -->
                <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200" data-aos="fade-up">
                    <div class="flex items-center mb-4">
                        <i class="bi bi-chat-dots-fill text-blue-600 text-2xl mr-3"></i>
                        <h2 class="text-xl font-semibold text-gray-800">Message to Students</h2>
                    </div>
                    <button onclick="window.location.href='admin_message.php'" class="btn btn-primary w-full mt-2">
                        <i class="bi bi-send-fill mr-2"></i> Send Message
                    </button>
                </div>

                <!-- Manage Exams Form -->
                <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200" data-aos="fade-up"
                    data-aos-delay="100">
                    <div class="flex items-center mb-4">
                        <i class="bi bi-journal-text text-green-600 text-2xl mr-3"></i>
                        <h2 class="text-xl font-semibold text-gray-800">Manage Exam Forms</h2>
                    </div>
                    <button onclick="window.location.href='manage.php'" class="btn btn-success w-full mt-2">
                        <i class="bi bi-clipboard-data mr-2"></i> Go to Exams Form
                    </button>
                </div>

                <!-- Schedule Exam -->
                <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200" data-aos="fade-up"
                    data-aos-delay="200">
                    <div class="flex items-center mb-4">
                        <i class="bi bi-calendar-week-fill text-cyan-600 text-2xl mr-3"></i>
                        <h2 class="text-xl font-semibold text-gray-800">Schedule Exam Form</h2>
                    </div>
                    <button onclick="window.location.href='admin_exam_manager.php'" class="btn btn-info w-full mt-2"
                        style="color:white;">
                        <i class="bi bi-clock-fill mr-2" style="color:white;"></i> Go to Scheduler
                    </button>
                </div>

                <!-- Contact Support Section -->
                <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200" data-aos="fade-up"
                    data-aos-delay="400">
                    <div class="flex items-center mb-4">
                        <i class="bi bi-envelope-fill text-orange-600 text-2xl mr-3"></i>
                        <h2 class="text-xl font-semibold text-gray-800">Contact Support</h2>
                    </div>
                    <button onclick="window.location.href='admin_contact_support.php'"
                        class="btn btn-warning w-full mt-2" style="color:white; background-color: orange;">
                        <i class="bi bi-headset mr-2"></i> Get in Touch
                    </button>
                </div>


                <!-- Feedbacks Section -->
                <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200" data-aos="fade-up"
                    data-aos-delay="300">
                    <div class="flex items-center mb-4">
                        <i class="bi bi-chat-left-text-fill text-yellow-600 text-2xl mr-3"></i>
                        <h2 class="text-xl font-semibold text-gray-800">Student Feedbacks</h2>
                    </div>
                    <button onclick="window.location.href='admin_feedbacks.php'" class="btn btn-warning w-full mt-2"
                        style="color:white;">
                        <i class="bi bi-card-text mr-2" style="color: white;"></i> View Feedbacks
                    </button>
                </div>

                <!-- Admin Manage Contribute Notes -->
                <div class="bg-white shadow-md rounded-lg p-6 border border-gray-100" data-aos="fade-up"
                    data-aos-delay="600">
                    <div class="flex items-center mb-4">
                        <i class="bi bi-shield-lock-fill text-emerald-500 text-2xl mr-3"></i>
                        <h2 class="text-xl font-semibold text-gray-800">Admin Manage Contribute Notes</h2>
                    </div>
                    <button onclick="window.location.href='admin_manage_contribute_notes.php'"
                        class="w-full mt-2 text-white py-2 px-4 rounded transition duration-200 hover:shadow-inner"
                        style="background-color: #34D399; cursor: pointer;">
                        <i class="bi bi-journals mr-2"></i> Manage Notes
                    </button>
                </div>

                <!-- Important Announcements -->
                <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200" data-aos="fade-up"
                    data-aos-delay="500">
                    <div class="flex items-center mb-4">
                        <i class="bi bi-megaphone-fill text-blue-600 text-2xl mr-3"></i>
                        <h2 class="text-xl font-semibold text-gray-800">Important Announcement</h2>
                    </div>
                    <button onclick="window.location.href='admin_announcements.php'"
                        class="btn w-full mt-2 text-white transition duration-200 hover:shadow-inner"
                        style="background-color: #3B82F6;">
                        <i class="bi bi-bell-fill mr-2"></i> Manage Announcements
                    </button>
                </div>

                <!-- Admin Auth -->
                <div class="bg-white shadow-md rounded-lg p-6 border border-gray-100" data-aos="fade-up"
                    data-aos-delay="600">
                    <div class="flex items-center mb-4">
                        <i class="bi bi-shield-lock-fill text-violet-600 text-2xl mr-3"></i>
                        <h2 class="text-xl font-semibold text-gray-800">Admin Auth</h2>
                    </div>
                    <button onclick="window.location.href='../admin_auth/admin_login_auth.php'"
                        class="w-full mt-2 text-white py-2 px-4 rounded transition duration-200 hover:shadow-inner"
                        style="background-color: #7C3AED; cursor: pointer;">
                        <i class="bi bi-person-fill-gear mr-2"></i> Manage Admin
                    </button>
                </div>

                <!-- Manage Students Account -->
                <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200" data-aos="fade-up"
                    data-aos-delay="400">
                    <div class="flex items-center mb-4">
                        <i class="bi bi-person-gear text-indigo-600 text-2xl mr-3"></i>
                        <h2 class="text-xl font-semibold text-gray-800">Manage Students</h2>
                    </div>
                    <button onclick="window.location.href='manage_students.php'"
                        class="btn w-full mt-2 text-white transition duration-200 hover:shadow-inner"
                        style="background-color: #6366f1;">
                        <i class="bi bi-people-fill mr-2"></i> Bulk Management
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>AOS.init();</script>
</body>

</html>
