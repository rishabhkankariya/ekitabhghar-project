<?php
session_start();
include '../php/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// 2b. Individual Edit
if (isset($_POST['edit_student'])) {
    $id = intval($_POST['edit_student_id']);
    $roll = trim($_POST['roll_no']);
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $dob = trim($_POST['dob']);
    $phone = trim($_POST['phone']);
    $course = trim($_POST['course']);
    $admY = intval($_POST['admission_year']);
    $passY = intval($_POST['pass_year']);
    $status = trim($_POST['account_status']);

    // Clean phone number
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($cleanPhone) == 11 && substr($cleanPhone, 0, 1) === '0') {
        $cleanPhone = substr($cleanPhone, 1);
    }
    if (strlen($cleanPhone) == 12 && substr($cleanPhone, 0, 2) === '91') {
        $cleanPhone = substr($cleanPhone, 2);
    }

    if (empty($roll) || empty($name) || empty($email) || empty($dob) || empty($course) || empty($admY) || empty($passY) || empty($status)) {
        $_SESSION['error_msg'] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_msg'] = "Invalid email format.";
    } elseif ($phone !== '' && !preg_match('/^[0-9]{10}$/', $cleanPhone)) {
        $_SESSION['error_msg'] = "Phone number must be exactly 10 digits (got '$phone').";
    } else {
        $check = $pdo->prepare("SELECT id FROM student_accounts WHERE (roll_no = ? OR email = ?) AND id != ?");
        $check->execute([$roll, $email, $id]);
        if ($check->rowCount() > 0) {
            $_SESSION['error_msg'] = "Another student with the same Roll Number or Email already exists.";
        } else {
            $sql = "UPDATE student_accounts SET roll_no=?, full_name=?, email=?, dob=?, phone_number=?, course=?, admission_year=?, expected_passing_year=?, account_status=?, updated_at=CURRENT_TIMESTAMP WHERE id=?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$roll, $name, $email, $dob, $cleanPhone, $course, $admY, $passY, $status, $id])) {
                $_SESSION['success_msg'] = "Student details updated successfully.";
            } else {
                $_SESSION['error_msg'] = "Failed to update student details.";
            }
        }
    }
    header("Location: admin_student_register.php" . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

// Handle Single Delete Action
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM student_accounts WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['success_msg'] = "Student deleted successfully.";
    } else {
        $_SESSION['error_msg'] = "Failed to delete student.";
    }
    header("Location: admin_student_register.php");
    exit;
}

// Search Logic
$where = "1";
$search = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = htmlspecialchars(trim($_GET['search']));
}

// Fetch Students (Simple View)
$searchParam = "%$search%";
$stmt_list = $pdo->prepare("SELECT id, roll_no, full_name, email, phone_number, course, admission_year, expected_passing_year, dob, account_status FROM student_accounts WHERE 1=1" . (!empty($search) ? " AND (full_name LIKE ? OR roll_no LIKE ? OR email LIKE ? OR course LIKE ?)" : "") . " ORDER BY id DESC LIMIT 50");
if (!empty($search)) { 
    $stmt_list->execute([$searchParam, $searchParam, $searchParam, $searchParam]); 
} else { 
    $stmt_list->execute(); 
}
$result = $stmt_list;

$branches = [
    "Civil Engineering",
    "Mechanical Engineering", 
    "Electrical Engineering",
    "Electronics & Telecomm. (EJ)",
    "Computer Engineering (CO)",
    "Information Technology (IF)",
    "Automobile Engineering",
    "Chemical Engineering",
    "Instrumentation & Control"
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../../apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../favicon.ico">
    <link rel="manifest" href="../../site.webmanifest">
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
        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200 flex items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($_SESSION['success_msg']) ?>
            </div>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg border border-red-200 flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($_SESSION['error_msg']) ?>
            </div>
            <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>
        <?php if (isset($_GET['msg'])): ?>
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200 flex items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg border border-red-200 flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($_GET['error']) ?>
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
                        <?php if ($result && $result->rowCount() > 0): ?>
                            <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
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
                                        <div class="flex justify-center gap-2">
                                            <button type="button" class="inline-block p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-all" title="Edit Student" onclick='openEditModal(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                                <i class="bi bi-pencil-square text-xl"></i>
                                            </button>
                                            <a href="admin_student_register.php?delete_id=<?= $row['id'] ?>"
                                                onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars($row['full_name']) ?>? This action is irreversible.')"
                                                class="inline-block p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all"
                                                title="Delete Student">
                                                <i class="bi bi-trash3-fill text-xl"></i>
                                            </a>
                                        </div>
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
                    <?= $pdo->query("SELECT COUNT(*) FROM student_accounts")->fetchColumn() ?></span>
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
    <!-- Edit Student Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" class="modal-content" id="editStudentForm">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Student Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="edit_student_id" id="edit_student_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Roll Number</label>
                            <input type="text" name="roll_no" id="edit_roll_no" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control" required pattern="[0-9]{10}" title="Exactly 10 digits">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" id="edit_dob" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Course</label>
                            <select name="course" id="edit_course" class="form-select" required>
                                <option value="">Select Course</option>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= $branch ?>"><?= $branch ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Admission Year</label>
                            <input type="number" name="admission_year" id="edit_admission_year" class="form-control" min="2020" max="2030" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expected Passing Year</label>
                            <input type="number" name="pass_year" id="edit_pass_year" class="form-control" min="2023" max="2035" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Account Status</label>
                            <select name="account_status" id="edit_account_status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="blocked">Blocked</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_student" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap Bundle JS (necessary for modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();

        // openEditModal function
        function openEditModal(student) {
            document.getElementById('edit_student_id').value = student.id;
            document.getElementById('edit_roll_no').value = student.roll_no;
            document.getElementById('edit_full_name').value = student.full_name;
            document.getElementById('edit_email').value = student.email;
            document.getElementById('edit_phone').value = student.phone_number || '';
            document.getElementById('edit_dob').value = student.dob || '';
            document.getElementById('edit_course').value = student.course;
            document.getElementById('edit_admission_year').value = student.admission_year;
            document.getElementById('edit_pass_year').value = student.expected_passing_year;
            document.getElementById('edit_account_status').value = student.account_status || 'active';
            
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }

        // Show global loader on student edit form submit if loader is loaded
        document.getElementById('editStudentForm')?.addEventListener('submit', function() {
            if (typeof showGlobalLoader === 'function') {
                showGlobalLoader("Updating student details. Please wait...");
            }
        });
    </script>
    <script src="../js/loader.js"></script>
</body>

</html>