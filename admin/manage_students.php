<?php
session_start();
require '../php/connection.php';
require '../config/send_mail.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

$message = "";
$error = "";

// Helper to generate password
function generateTempPassword($name, $dob)
{
    $cleanName = strtoupper(preg_replace('/[^A-Z]/', '', strtoupper($name)));
    $namePart = substr($cleanName, 0, 4);
    $dobTs = strtotime(str_replace('/', '-', $dob));
    if (!$dobTs)
        return "";
    $dobPart = date('dY', $dobTs); // DDYYYY
    return $namePart . $dobPart;
}

// 1. Bulk Upload
if (isset($_POST['upload_csv']) && $_FILES['student_csv']['name']) {
    $handle = fopen($_FILES['student_csv']['tmp_name'], "r");
    fgetcsv($handle); // skip header
    $count = 0;
    while ($data = fgetcsv($handle)) {
        if (count($data) < 7)
            continue;
        $roll = trim($data[0]);
        $name = trim($data[1]);
        $email = trim($data[2]);
        $dob = trim($data[3]);
        $course = trim($data[4]);
        $admY = intval($data[5]);
        $passY = intval($data[6]);
        $phone = isset($data[7]) ? trim($data[7]) : '';

        $tempPass = generateTempPassword($name, $dob);
        if (!$tempPass)
            continue;
        $hash = password_hash($tempPass, PASSWORD_DEFAULT);

        $sql = "INSERT INTO student_accounts (roll_no, full_name, email, dob, phone_number, password_hash, course, admission_year, expected_passing_year, account_status, is_temp_password) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', 1) 
                ON DUPLICATE KEY UPDATE full_name=VALUES(full_name), email=VALUES(email), dob=VALUES(dob), phone_number=VALUES(phone_number), password_hash=VALUES(password_hash), is_temp_password=1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssii", $roll, $name, $email, $dob, $phone, $hash, $course, $admY, $passY);
        if ($stmt->execute()) {
            // [TESTING MODE] Skip credential email
            // sendCredentialEmail($email, $name, $roll, $tempPass);
            $count++;
        }
    }
    fclose($handle);
    $_SESSION['success_msg'] = "Successfully processed $count students.";
    header("Location: manage_students.php");
    exit;
}

// 2. Individual Add
if (isset($_POST['add_student'])) {
    $roll = trim($_POST['roll_no']);
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $dob = trim($_POST['dob']);
    $phone = trim($_POST['phone']);
    $course = trim($_POST['course']);
    $admY = intval($_POST['admission_year']);
    $passY = intval($_POST['pass_year']);

    $tempPass = generateTempPassword($name, $dob);
    $hash = password_hash($tempPass, PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM student_accounts WHERE roll_no = ? OR email = ?");
    $check->bind_param("ss", $roll, $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $_SESSION['error_msg'] = "Student already exists (Roll/Email).";
    } else {
        $sql = "INSERT INTO student_accounts (roll_no, full_name, email, dob, phone_number, password_hash, course, admission_year, expected_passing_year, account_status, is_temp_password) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssii", $roll, $name, $email, $dob, $phone, $hash, $course, $admY, $passY);
        if ($stmt->execute()) {
            // [TESTING MODE] Skip credential email
            // sendCredentialEmail($email, $name, $roll, $tempPass);
            $_SESSION['success_msg'] = "Student added successfully. (Email disabled in test mode)";
        } else {
            $_SESSION['error_msg'] = "Error: " . $conn->error;
        }
    }
    header("Location: manage_students.php");
    exit;
}

// 3. Delete Action
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    if ($conn->query("DELETE FROM student_accounts WHERE id = $id")) {
        $_SESSION['success_msg'] = "Student deleted successfully.";
    }
    header("Location: manage_students.php");
    exit;
}

function sendCredentialEmail($to, $name, $roll, $pass)
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $loginLink = $protocol . $_SERVER['HTTP_HOST'] . rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\') . '/student_login.html';

    $subject = 'Your Student Portal Credentials';
    $body = "
    <div style='font-family: Arial; max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px;'>
        <h2 style='color: #4f46e5;'>Student Portal Access</h2>
        <p>Dear $name,</p>
        <p>Your account has been created. Use these credentials to log in:</p>
        <div style='background: #f8f9fa; padding: 15px; border-radius: 8px;'>
            <p><strong>Roll No:</strong> $roll</p>
            <p><strong>Email:</strong> $to</p>
            <p><strong>Temporary Password:</strong> <span style='color: #e63946; font-weight: bold;'>$pass</span></p>
        </div>
        <p>Login here: <a href='$loginLink'>$loginLink</a></p>
        <p><em>Password Format: First 4 letters of Name + Day(DD) + Year(YYYY)</em></p>
        <p style='margin-top: 20px; font-size: 12px; color: #666;'>If you didn't request this, please ignore this email.</p>
    </div>";

    $res = sendEmail($to, $name, $subject, $body);
    return ($res === true);
}


// Bulk Actions
if (isset($_POST['bulk_action']) && isset($_POST['student_ids'])) {
    $ids = implode(",", array_map('intval', $_POST['student_ids']));
    $action = $_POST['action_type'];
    if ($action == 'active')
        $conn->query("UPDATE student_accounts SET account_status = 'active' WHERE id IN ($ids)");
    elseif ($action == 'block')
        $conn->query("UPDATE student_accounts SET account_status = 'blocked' WHERE id IN ($ids)");
    elseif ($action == 'delete')
        $conn->query("DELETE FROM student_accounts WHERE id IN ($ids)");
    $_SESSION['success_msg'] = "Bulk action completed.";
    header("Location: manage_students.php");
    exit;
}

$sql = "SELECT * FROM student_accounts ORDER BY id DESC";
$result_list = $conn->query($sql);

// Handle CSV Template Download
if (isset($_GET['download_template'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="student_template.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['RollNo', 'FullName', 'Email', 'DOB(YYYY-MM-DD)', 'Course', 'AdmissionYear', 'PassYear', 'PhoneNumber']);
    fputcsv($output, ['22030C04001', 'John Doe', 'john@example.com', '2004-12-15', 'Computer Science', '2022', '2025', '9876543210']);
    fclose($output);
    exit;
}

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management | Kitabghar Admin</title>
    <!-- Modern Frameworks -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Outfit', sans-serif;
            background: #f0f2f5;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-premium {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.2);
            transition: all 0.3s ease;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.3);
        }

        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4f46e5;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* DataTables Custom Styling */
        .dataTables_wrapper .dataTables_length select {
            padding-right: 2rem !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            padding: 0.4rem 1rem;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen">

    <div id="loader" class="loader-overlay">
        <div class="text-center">
            <div class="spinner mb-3"></div>
            <p class="text-indigo-600 font-medium">Processing... Please Wait</p>
        </div>
    </div>

    <div class="max-w-[1400px] mx-auto p-4 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div class="animate__animated animate__fadeInLeft">
                <h1 class="text-3xl font-bold text-slate-800">Student Management</h1>
                <p class="text-slate-500">Add, manage and monitor student accounts</p>
            </div>
            <div class="flex gap-3 animate__animated animate__fadeInRight">
                <a href="adminpanel.php"
                    class="flex items-center gap-2 px-4 py-2 bg-white text-slate-700 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-medium">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar Actions -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Bulk Upload Card -->
                <div class="glass-card p-6 rounded-2xl shadow-sm animate__animated animate__fadeInUp">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            <i class="bi bi-file-earmark-spreadsheet text-emerald-500"></i> Bulk Upload
                        </h3>
                        <a href="?download_template=1"
                            class="text-indigo-600 text-[11px] font-semibold hover:text-indigo-800 flex items-center gap-1 bg-indigo-50 px-2 py-1 rounded-lg transition-colors">
                            <i class="bi bi-download"></i> Template
                        </a>
                    </div>
                    <form method="POST" enctype="multipart/form-data" onsubmit="showLoader()">
                        <div class="relative group">
                            <input type="file" name="student_csv" id="csvFile"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required
                                onchange="updateFileName(this)">
                            <div
                                class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center group-hover:border-indigo-400 transition-colors">
                                <i class="bi bi-cloud-arrow-up text-3xl text-slate-400 mb-2 block" id="fileIcon"></i>
                                <span
                                    class="text-xs text-slate-500 font-medium block overflow-hidden text-ellipsis whitespace-nowrap"
                                    id="fileName">Select CSV File</span>
                            </div>
                        </div>
                        <button type="submit" name="upload_csv"
                            class="btn-premium w-full mt-4 text-white py-3 rounded-xl font-semibold flex items-center justify-center gap-2">
                            <i class="bi bi-upload"></i> Upload & Invite
                        </button>
                    </form>
                    <div class="mt-4 p-3 bg-slate-50 rounded-lg border border-slate-100">
                        <p class="text-[10px] text-slate-400 font-medium leading-relaxed">
                            <span class="text-slate-600 font-bold">INFO:</span> Passwords format is name initials + DOB
                            digits. [TEST MODE] Emails are disabled.
                        </p>
                    </div>
                </div>

                <!-- Manual Add Card -->
                <div class="glass-card p-6 rounded-2xl shadow-sm animate__animated animate__fadeInUp"
                    style="animation-delay: 0.1s;">
                    <h3 class="font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <i class="bi bi-person-plus text-indigo-500"></i> Individual Entry
                    </h3>
                    <p class="text-sm text-slate-500 mb-4">Add a single student manually to the database.</p>
                    <button
                        class="w-full py-3 bg-slate-800 text-white rounded-xl font-semibold hover:bg-slate-900 transition-all flex items-center justify-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-lg"></i> Add New Student
                    </button>
                </div>
            </div>

            <!-- Main Data Table -->
            <div class="lg:col-span-3 space-y-6">
                <div class="glass-card p-6 rounded-2xl shadow-sm animate__animated animate__fadeIn">
                    <form method="POST" id="bulkActionForm" onsubmit="return confirmBulkAction()">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                            <div class="flex items-center gap-3">
                                <select name="action_type" id="actionSelect"
                                    class="bg-white border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5 outline-none">
                                    <option value="" disabled selected>Bulk Actions</option>
                                    <option value="active">🟢 Mark Active</option>
                                    <option value="block">🔴 Mark Blocked</option>
                                    <option value="delete">🗑️ Delete Selected</option>
                                </select>
                                <button type="submit" name="bulk_action"
                                    class="px-5 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-900 transition-colors disabled:opacity-50"
                                    id="applyBtn" disabled>
                                    Apply
                                </button>
                            </div>
                            <div class="text-slate-500 text-sm font-medium">
                                Total Students: <span class="text-indigo-600"><?= $result_list->num_rows ?></span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table id="studentsTable" class="w-full text-sm text-left">
                                <thead class="text-xs text-slate-500 uppercase bg-slate-50/50">
                                    <tr>
                                        <th class="px-4 py-4 w-4">
                                            <input type="checkbox" id="selectAll"
                                                class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                        </th>
                                        <th class="px-4 py-4">Student Identity</th>
                                        <th class="px-4 py-4">Course Info</th>
                                        <th class="px-4 py-4">Contact</th>
                                        <th class="px-4 py-4">Status</th>
                                        <th class="px-4 py-4 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php while ($row = $result_list->fetch_assoc()): ?>
                                        <tr class="hover:bg-slate-50/50 transition-colors group">
                                            <td class="px-4 py-4">
                                                <input type="checkbox" name="student_ids[]" value="<?= $row['id'] ?>"
                                                    class="student-checkbox w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold border border-indigo-100">
                                                        <?= strtoupper(substr($row['full_name'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-slate-800">
                                                            <?= htmlspecialchars($row['full_name']) ?>
                                                        </div>
                                                        <div
                                                            class="text-[11px] text-slate-400 font-medium tracking-wider uppercase">
                                                            <?= $row['roll_no'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="text-slate-700 font-medium">
                                                    <?= htmlspecialchars($row['course']) ?>
                                                </div>
                                                <div class="text-[11px] text-slate-400"><?= $row['admission_year'] ?> -
                                                    <?= $row['expected_passing_year'] ?>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="text-slate-600"><?= htmlspecialchars($row['email']) ?></div>
                                                <div class="text-[11px] text-slate-400">
                                                    <?= $row['phone_number'] ?: 'No Phone' ?>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <?php if ($row['account_status'] == 'active'): ?>
                                                    <span
                                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-600 border border-emerald-100">Active</span>
                                                <?php else: ?>
                                                    <span
                                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-rose-50 text-rose-600 border border-rose-100">Blocked</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-4 text-right">
                                                <div class="flex justify-end gap-2">
                                                    <button type="button" onclick="confirmDelete(<?= $row['id'] ?>)"
                                                        class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-transparent hover:border-rose-100">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modern Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" class="modal-content overflow-hidden border-0 rounded-2xl shadow-2xl"
                onsubmit="showLoader()">
                <div class="modal-header border-0 bg-slate-50 p-6">
                    <div>
                        <h5 class="modal-title font-bold text-slate-800 text-xl">New Student Entry</h5>
                        <p class="text-sm text-slate-500">Enter student details for portal access</p>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Roll
                                Number</label>
                            <input type="text" name="roll_no" placeholder="e.g. 22030C04001"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-sm font-medium"
                                required>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Full
                                Name</label>
                            <input type="text" name="full_name" placeholder="Full Name"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-sm font-medium"
                                required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Email
                                Address</label>
                            <input type="email" name="email" placeholder="email@example.com"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-sm font-medium"
                                required>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Phone
                                Number</label>
                            <input type="text" name="phone" placeholder="10 Digit Number"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-sm font-medium">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Date of
                                Birth</label>
                            <input type="date" name="dob"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-sm font-medium uppercase"
                                required>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Branch /
                                Course</label>
                            <select name="course"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-sm font-medium"
                                required>
                                <option value="" disabled selected>Select Branch</option>
                                <?php foreach ($branches as $b): ?>
                                    <option value="<?= $b ?>"><?= $b ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Admission
                                Year</label>
                            <input type="number" name="admission_year" placeholder="2022"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-sm font-medium"
                                required>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Passing
                                Year</label>
                            <input type="number" name="pass_year" placeholder="2025"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-sm font-medium"
                                required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-slate-50 p-6 pt-0">
                    <button type="submit" name="add_student"
                        class="btn-premium w-full text-white py-4 rounded-xl font-bold text-lg">
                        Create Student Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwind.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize DataTable
            $('#studentsTable').DataTable({
                "pageLength": 10,
                "order": [[1, "asc"]],
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search anything..."
                },
                "dom": '<"flex flex-col sm:flex-row justify-between items-center mb-4"<"text-slate-500"l><"w-full sm:w-auto"f>>rt<"flex flex-col sm:flex-row justify-between items-center mt-4"ip>'
            });

            // Handle Select All
            $('#selectAll').on('change', function () {
                $('.student-checkbox').prop('checked', this.checked);
                updateBulkActions();
            });

            $('.student-checkbox').on('change', function () {
                updateBulkActions();
            });

            function updateBulkActions() {
                const checkedCount = $('.student-checkbox:checked').length;
                $('#applyBtn').prop('disabled', checkedCount === 0);
            }
        });

        function showLoader() {
            $('#loader').css('display', 'flex');
        }

        function updateFileName(input) {
            const file = input.files[0];
            if (file) {
                $('#fileName').text(file.name);
                $('#fileIcon').removeClass('bi-cloud-arrow-up text-slate-400').addClass('bi-file-check-fill text-emerald-500');
            }
        }

        function confirmBulkAction() {
            const action = $('#actionSelect').val();
            if (!action) {
                Swal.fire('Error', 'Please select an action first', 'error');
                return false;
            }
            return true;
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting a student is permanent!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '?delete_id=' + id;
                }
            })
        }

        // Show Session Messages
        <?php if (isset($_SESSION['success_msg'])): ?>
            Swal.fire('Success!', '<?= $_SESSION['success_msg'] ?>', 'success');
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_msg'])): ?>
            Swal.fire('Error!', '<?= $_SESSION['error_msg'] ?>', 'error');
            <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>
    </script>
</body>

</html>