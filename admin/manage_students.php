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
                ON CONFLICT (roll_no) DO UPDATE SET full_name=EXCLUDED.full_name, email=EXCLUDED.email, dob=EXCLUDED.dob, phone_number=EXCLUDED.phone_number, password_hash=EXCLUDED.password_hash, is_temp_password=1";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$roll, $name, $email, $dob, $phone, $hash, $course, $admY, $passY])) {
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

    $check = $pdo->prepare("SELECT id FROM student_accounts WHERE roll_no = ? OR email = ?"); 
    $check->execute([$roll, $email]); 
    if ($check->rowCount() > 0) {
        $_SESSION['error_msg'] = "Student already exists (Roll/Email).";
    } else {
        $sql = "INSERT INTO student_accounts (roll_no, full_name, email, dob, phone_number, password_hash, course, admission_year, expected_passing_year, account_status, is_temp_password) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', 1)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$roll, $name, $email, $dob, $phone, $hash, $course, $admY, $passY])) {
            // [TESTING MODE] Skip credential email
            // sendCredentialEmail($email, $name, $roll, $tempPass);
            $_SESSION['success_msg'] = "Student added successfully. (Email disabled in test mode)";
        } else {
            $_SESSION['error_msg'] = "Error inserting student.";
        }
    }
    header("Location: manage_students.php");
    exit;
}

// 3. Delete Action
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    if ($pdo->prepare("DELETE FROM student_accounts WHERE id = ?")->execute([$id])) {
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
        $pdo->exec("UPDATE student_accounts SET account_status = 'active' WHERE id IN ($ids)");
    elseif ($action == 'block')
        $pdo->exec("UPDATE student_accounts SET account_status = 'blocked' WHERE id IN ($ids)");
    elseif ($action == 'delete')
        $pdo->exec("DELETE FROM student_accounts WHERE id IN ($ids)");
    $_SESSION['success_msg'] = "Bulk action completed.";
    header("Location: manage_students.php");
    exit;
}

$sql = "SELECT * FROM student_accounts ORDER BY id DESC";
$result_list = $pdo->query($sql);

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
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../../apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../favicon.ico">
    <link rel="manifest" href="../../site.webmanifest">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management | Kitabghar Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="max-w-[1400px] mx-auto p-4 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Student Management</h1>
                <p class="text-slate-500">Add, manage and monitor student accounts</p>
            </div>
            <div class="flex gap-3">
                <a href="adminpanel.php" class="flex items-center gap-2 px-4 py-2 bg-white text-slate-700 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-medium">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200">
                <?= htmlspecialchars($_SESSION['success_msg']) ?>
            </div>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg border border-red-200">
                <?= htmlspecialchars($_SESSION['error_msg']) ?>
            </div>
            <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar Actions -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Bulk Upload Card -->
                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            <i class="bi bi-file-earmark-spreadsheet text-emerald-500"></i> Bulk Upload
                        </h3>
                        <a href="?download_template=1" class="text-indigo-600 text-xs font-semibold hover:text-indigo-800 flex items-center gap-1 bg-indigo-50 px-2 py-1 rounded-lg transition-colors">
                            <i class="bi bi-download"></i> Template
                        </a>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="relative group">
                            <input type="file" name="student_csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required>
                            <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center group-hover:border-indigo-400 transition-colors">
                                <i class="bi bi-cloud-arrow-up text-3xl text-slate-400 mb-2 block"></i>
                                <span class="text-xs text-slate-500 font-medium">Select CSV File</span>
                            </div>
                        </div>
                        <button type="submit" name="upload_csv" class="w-full mt-4 text-white py-3 rounded-xl font-semibold bg-indigo-600 hover:bg-indigo-700 transition-colors">
                            <i class="bi bi-upload"></i> Upload & Process
                        </button>
                    </form>
                </div>

                <!-- Manual Add Card -->
                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <h3 class="font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <i class="bi bi-person-plus text-indigo-500"></i> Individual Entry
                    </h3>
                    <button class="w-full py-3 bg-slate-800 text-white rounded-xl font-semibold hover:bg-slate-900 transition-all" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-lg"></i> Add New Student
                    </button>
                </div>
            </div>

            <!-- Main Data Table -->
            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <form method="POST" id="bulkActionForm">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                            <div class="flex items-center gap-3">
                                <select name="action_type" class="bg-white border border-slate-200 text-slate-700 text-sm rounded-lg p-2.5">
                                    <option value="" disabled selected>Bulk Actions</option>
                                    <option value="active">🟢 Mark Active</option>
                                    <option value="block">🔴 Mark Blocked</option>
                                    <option value="delete">🗑️ Delete Selected</option>
                                </select>
                                <button type="submit" name="bulk_action" class="px-5 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-900 transition-colors">
                                    Apply
                                </button>
                            </div>
                            <div class="text-slate-500 text-sm font-medium">
                                Total Students: <span class="text-indigo-600"><?= $result_list->rowCount() ?></span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-slate-500 uppercase bg-slate-50/50">
                                    <tr>
                                        <th class="px-4 py-4 w-4">
                                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-indigo-600 rounded border-slate-300">
                                        </th>
                                        <th class="px-4 py-4">Student Identity</th>
                                        <th class="px-4 py-4">Course Info</th>
                                        <th class="px-4 py-4">Contact</th>
                                        <th class="px-4 py-4">Status</th>
                                        <th class="px-4 py-4 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php while ($row = $result_list->fetch(PDO::FETCH_ASSOC)): ?>
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-4 py-4">
                                                <input type="checkbox" name="student_ids[]" value="<?= $row['id'] ?>" class="w-4 h-4 text-indigo-600 rounded border-slate-300">
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold">
                                                        <?= strtoupper(substr($row['full_name'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-slate-800"><?= htmlspecialchars($row['full_name']) ?></div>
                                                        <div class="text-xs text-slate-400 font-medium"><?= $row['roll_no'] ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="text-slate-700 font-medium"><?= htmlspecialchars($row['course']) ?></div>
                                                <div class="text-xs text-slate-400"><?= $row['admission_year'] ?> - <?= $row['expected_passing_year'] ?></div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="text-slate-600"><?= htmlspecialchars($row['email']) ?></div>
                                                <div class="text-xs text-slate-400"><?= $row['phone_number'] ?: 'No Phone' ?></div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <?php if ($row['account_status'] == 'active'): ?>
                                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase bg-emerald-50 text-emerald-600">Active</span>
                                                <?php else: ?>
                                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase bg-rose-50 text-rose-600">Blocked</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-4 text-right">
                                                <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Delete this student?')" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                                    <i class="bi bi-trash3"></i>
                                                </a>
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

    <!-- Add Student Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Roll Number</label>
                            <input type="text" name="roll_no" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Course</label>
                            <select name="course" class="form-select" required>
                                <option value="">Select Course</option>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= $branch ?>"><?= $branch ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Admission Year</label>
                            <input type="number" name="admission_year" class="form-control" min="2020" max="2030" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expected Passing Year</label>
                            <input type="number" name="pass_year" class="form-control" min="2023" max="2035" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_student" class="btn btn-primary">Add Student</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="student_ids[]"]');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
</body>
</html>