<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Unauthorized access! Please Login First'); window.location.href='admin_login.php';</script>";
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "ekitabhghar";

// Database Connection
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Fetch challans for the student
$challans = [];
$challan_sql = "SELECT * FROM challans WHERE student_id = ?";
$challan_stmt = $conn->prepare($challan_sql);
$challan_stmt->bind_param("i", $id);
$challan_stmt->execute();
$challan_result = $challan_stmt->get_result();
while ($row = $challan_result->fetch_assoc()) {
    $challans[] = $row;
}

if (!$student) {
    die("Student not found.");
}

$subjects = json_decode($student['subjects'], true) ?: [];
$ex_subjects = json_decode($student['ex_subjects'], true) ?: [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>E-KITABGHAR | Exam Form Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            AOS.init();
        });

        function toggleSection(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }
    </script>
</head>

<body class="bg-gray-100 text-gray-800 p-4 font-[Poppins]">

    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl font-bold text-center text-blue-600 mb-6" data-aos="fade-down">Student Exam Form Details
        </h2>

        <div class="bg-white rounded-2xl shadow-md p-6 border-2 border-blue-500" data-aos="zoom-in">

            <!-- Institute Info -->
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="font-semibold">Institute Name</label>
                    <input type="text" readonly value="UJJAIN POLYTECHNIC COLLEGE, UJJAIN"
                        class="w-full mt-1 p-2 border border-blue-500 rounded-md bg-gray-50 font-medium" />
                </div>
                <div>
                    <label class="font-semibold">Institution Code</label>
                    <input type="text" readonly value="030"
                        class="w-full mt-1 p-2 border border-blue-500 rounded-md bg-gray-50 font-medium" />
                </div>
            </div>

            <!-- Student Info -->
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="font-semibold">Student Name</label>
                    <input type="text" readonly value="<?= htmlspecialchars($student['student_name']) ?>"
                        class="w-full mt-1 p-2 border border-blue-500 rounded-md bg-gray-50 font-medium" />
                </div>
                <div>
                    <label class="font-semibold">Roll No</label>
                    <input type="text" readonly value="<?= htmlspecialchars($student['roll_no']) ?>"
                        class="w-full mt-1 p-2 border border-blue-500 rounded-md bg-gray-50 font-medium" />
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="font-semibold">Course Type</label>
                    <input type="text" readonly value="<?= htmlspecialchars($student['course_type']) ?>"
                        class="w-full mt-1 p-2 border border-blue-500 rounded-md bg-gray-50 font-medium" />
                </div>
                <div>
                    <label class="font-semibold">Semester</label>
                    <input type="text" readonly value="<?= htmlspecialchars($student['current_semester']) ?>"
                        class="w-full mt-1 p-2 border border-blue-500 rounded-md bg-gray-50 font-medium" />
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="font-semibold">Category</label>
                    <input type="text" readonly value="<?= htmlspecialchars($student['category']) ?>"
                        class="w-full mt-1 p-2 border border-blue-500 rounded-md bg-gray-50 font-medium" />
                </div>
                <div>
                    <label class="font-semibold">Admission Fees</label>
                    <input type="text" readonly value="<?= htmlspecialchars($student['admission_fees']) ?>"
                        class="w-full mt-1 p-2 border border-blue-500 rounded-md bg-gray-50 font-medium" />
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="font-semibold">Email</label>
                    <input type="email" readonly value="<?= htmlspecialchars($student['email_id']) ?>"
                        class="w-full mt-1 p-2 border border-blue-500 rounded-md bg-gray-50 font-medium" />
                </div>
                <div>
                    <label class="font-semibold">Mobile</label>
                    <input type="text" readonly value="<?= htmlspecialchars($student['mobile_no']) ?>"
                        class="w-full mt-1 p-2 border border-blue-500 rounded-md bg-gray-50 font-medium" />
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="font-semibold">Exam Form Date</label>
                    <input type="text" readonly value="<?= htmlspecialchars($student['exam_date']) ?>"
                        class="w-full mt-1 p-2 border border-blue-500 rounded-md bg-gray-50 font-medium" />
                </div>
                <div>
                    <label class="font-semibold">Father's Name & Address</label>
                    <input type="text" readonly value="<?= htmlspecialchars($student['father_address']) ?>"
                        class="w-full mt-1 p-2 border border-blue-500 rounded-md bg-gray-50 font-medium" />
                </div>
            </div>
            <!-- Images -->
            <div class="grid gap-6 mb-6">
                <!-- Student Photo -->
                <div class="text-center">
                    <label class="font-semibold block mb-2">Student Photo</label>
                    <img src="<?= '../php/image/' . htmlspecialchars(basename($student['student_photo'])) ?>"
                        class="mx-auto rounded-lg border-2 border-blue-500 max-w-[150px]" alt="Student Photo">
                </div>

                <!-- Student Signature -->
                <div class="text-center">
                    <label class="font-semibold block mb-2">Signature</label>
                    <img src="<?= '../php/uploads/' . htmlspecialchars(basename($student['student_signature'])) ?>"
                        class="mx-auto rounded-lg border-2 border-blue-500 max-w-[150px]" alt="Student Signature">
                </div>
            </div>

            <!-- Subjects Toggle -->
            <div class="mb-4">
                <button onclick="toggleSection('subjectsTable')"
                    class="text-blue-600 font-semibold flex items-center gap-2">
                    <i class="bi bi-caret-down-square transition-transform duration-300"></i>View Regular
                    Subjects</button>
                <div id="subjectsTable" class="hidden mt-2 overflow-x-auto">
                    <table class="min-w-full bg-white text-sm border mt-2">
                        <thead class="bg-blue-600 text-white uppercase">
                            <tr>
                                <th class="p-2 border">Subject</th>
                                <th class="p-2 border">Paper Code</th>
                                <th class="p-2 border">Semester</th>
                                <th class="p-2 border">Theory</th>
                                <th class="p-2 border">Practical</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subjects as $sub): ?>
                                <tr class="text-center">
                                    <td class="border p-2"><?= htmlspecialchars($sub['subject']) ?></td>
                                    <td class="border p-2"><?= htmlspecialchars($sub['paper_code']) ?></td>
                                    <td class="border p-2"><?= htmlspecialchars($sub['semester']) ?></td>
                                    <td class="border p-2"><?= $sub['theory'] ? '✅' : '❌' ?></td>
                                    <td class="border p-2"><?= $sub['practical'] ? '✅' : '❌' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-4">
                <button onclick="toggleSection('ExsubjectsTable')"
                    class="text-blue-600 font-semibold flex items-center gap-2">
                    <i class="bi bi-caret-down-square transition-transform duration-300"></i> View Ex Subjects</button>
                <div id="ExsubjectsTable" class="hidden mt-2 overflow-x-auto">
                    <table class="min-w-full bg-white text-sm border mt-2">
                        <thead class="bg-blue-600 text-white uppercase">
                            <tr>
                                <th class="p-2 border">Subject</th>
                                <th class="p-2 border">Paper Code</th>
                                <th class="p-2 border">Semester</th>
                                <th class="p-2 border">Theory</th>
                                <th class="p-2 border">Practical</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ex_subjects as $sub): ?>
                                <tr class="text-center">
                                    <td class="border p-2"><?= htmlspecialchars($sub['subject']) ?></td>
                                    <td class="border p-2"><?= htmlspecialchars($sub['paper_code']) ?></td>
                                    <td class="border p-2"><?= htmlspecialchars($sub['semester']) ?></td>
                                    <td class="border p-2"><?= $sub['theory'] ? '✅' : '❌' ?></td>
                                    <td class="border p-2"><?= $sub['practical'] ? '✅' : '❌' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Academic Results Verification -->
            <div class="mb-4">
                <button onclick="toggleSection('resultsSection')"
                    class="text-blue-600 font-semibold flex items-center gap-2">
                    <i class="bi bi-caret-down-square transition-transform duration-300"></i> View Academic Results
                </button>
                <div id="resultsSection" class="hidden mt-2 overflow-x-auto">
                    <table class="min-w-full bg-white text-sm border mt-2">
                        <thead class="bg-blue-600 text-white uppercase">
                            <tr>
                                <th class="p-2 border">#</th>
                                <th class="p-2 border">Result File</th>
                                <th class="p-2 border">Type</th>
                                <th class="p-2 border">Uploaded On</th>
                                <th class="p-2 border">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $results_data = json_decode($student['previous_result'], true) ?: [];
                            if (!empty($results_data)):
                                foreach ($results_data as $index => $res):
                                    $file = $res['file_path'];
                                    $res_type = $res['type'] ?? 'Regular';
                                    $uploadedAt = isset($res['uploaded_at']) ? date('d M Y, h:i A', strtotime($res['uploaded_at'])) : 'N/A';
                                    ?>
                                    <tr class="text-center">
                                        <td class="border p-2"><?= $index + 1 ?></td>
                                        <td class="border p-2"><?= htmlspecialchars(basename($file)) ?></td>
                                        <td class="border p-2">
                                            <span
                                                class="px-2 py-1 rounded text-xs font-bold <?= $res_type == 'Ex' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' ?>">
                                                <?= $res_type ?>
                                            </span>
                                        </td>
                                        <td class="border p-2 text-gray-700"><?= $uploadedAt ?></td>
                                        <td class="border p-2">
                                            <a href="<?= '../php/results/' . htmlspecialchars(basename($file)) ?>"
                                                target="_blank" class="text-blue-600 underline hover:text-blue-800">
                                                Open Result
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                endforeach;
                            else: ?>
                                <tr class="text-center">
                                    <td colspan="5" class="border p-2 text-red-500">No academic results uploaded.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Challan Files -->
            <div class="mb-4">
                <button onclick="toggleSection('challanSection')"
                    class="text-blue-600 font-semibold flex items-center gap-2">
                    <i class="bi bi-caret-down-square transition-transform duration-300"></i> View Uploaded Challans
                </button>
                <div id="challanSection" class="hidden mt-2 overflow-x-auto">
                    <table class="min-w-full bg-white text-sm border mt-2">
                        <thead class="bg-blue-600 text-white uppercase">
                            <tr>
                                <th class="p-2 border">#</th>
                                <th class="p-2 border">Challan File</th>
                                <th class="p-2 border">Uploaded On</th>
                                <th class="p-2 border">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($challans)):
                                foreach ($challans as $index => $row):
                                    $file = $row['file_path'];
                                    $uploadedAt = date('d M Y, h:i A', strtotime($row['uploaded_at']));
                                    ?>
                                    <tr class="text-center">
                                        <td class="border p-2"><?= $index + 1 ?></td>
                                        <td class="border p-2"><?= htmlspecialchars(basename($file)) ?></td>
                                        <td class="border p-2 text-gray-700"><?= $uploadedAt ?></td>
                                        <td class="border p-2">
                                            <a href="<?= '../php/challans/' . htmlspecialchars(basename($file)) ?>"
                                                target="_blank" class="text-blue-600 underline hover:text-blue-800">
                                                Open Challan
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                endforeach;
                            else: ?>
                                <tr class="text-center">
                                    <td colspan="4" class="border p-2 text-red-500">No challan files uploaded.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>



            <!-- Back Button -->
            <div class="text-center">
                <a href="manage.php"
                    class="inline-block mt-4 bg-blue-600 hover:bg-blue-800 text-white px-6 py-2 rounded-lg font-semibold transition-all"><i
                        class="bi bi-arrow-left"></i> Back to List</a>
            </div>

        </div>
    </div>
    <script>
        function toggleSection(id) {
            const section = document.getElementById(id);
            const icon = document.querySelector(`#${id}`).previousElementSibling.querySelector('i');

            section.classList.toggle('hidden');

            // Rotate icon when section is toggled
            if (section.classList.contains('hidden')) {
                icon.classList.remove('rotate-180');
            } else {
                icon.classList.add('rotate-180');
            }
        }

    </script>
</body>

</html>
