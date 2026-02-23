<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Unauthorized access! Please Login First'); window.location.href='admin_login.php';</script>";
    exit();
}

require_once '../../php/connection.php';
// session_start() is already called after require_once for autoload.php
// but connection.php might also start session if not started.
if ($conn->connect_error)
    die("DB Error: " . $conn->connect_error);

$imageDir = realpath(__DIR__ . '/../../../php/image/') . '/';
$challanDir = realpath(__DIR__ . '/../../../php/challans/') . '/';
$uploadDir = realpath(__DIR__ . '/../../../php/uploads/') . '/';

$defaultPhoto = $uploadDir . 'default_photo.png';
$defaultSign = $uploadDir . 'default_sign.png';
$hodSignPath = $uploadDir . 'hod_sign.png';
$logoPath = $uploadDir . 'gpcu.png';

function getBase64Img($filePath)
{
    if (!file_exists($filePath))
        return '';
    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    $data = base64_encode(file_get_contents($filePath));
    return 'data:image/' . $ext . ';base64,' . $data;
}

$logoBase64 = getBase64Img($logoPath);
$hodSignBase64 = getBase64Img($hodSignPath);

$mpdf = new \Mpdf\Mpdf([
    'format' => 'A4',
    'margin_top' => 6,
    'margin_bottom' => 10,
    'margin_left' => 8,
    'margin_right' => 8,
    'default_font_size' => 9,
    'tempDir' => __DIR__ . '/tmp'
]);

$mpdf->SetWatermarkText('GOVT UJJAIN POLYTECHNIC UJJAIN COLLEGE [M.P.]', 0.1);
$mpdf->showWatermarkText = true;
$mpdf->SetHTMLFooter('<div style="text-align: center; font-size: 10px; color: #888;">Page {PAGENO} of {nbpg}</div>');

$html = '
<style>
    body { font-family: "Poppins", sans-serif; font-size: 9px; color: #333; line-height: 1.4; }
    h1, h2 { text-align: center; margin: 4px 0; font-size: 15px; color: #0e3a61; }
    .page-border {
        border: 2px solid #4b5563;
        padding: 12px;
        border-radius: 6px;
        background-color: #f9fafb;
    }
    .student-block { page-break-after: always; margin-bottom: 10px; }
    .info-table, .subjects-table, .challan-table {
        width: 100%; border-collapse: collapse; margin-top: 6px;
    }
    th, td { border: 1px solid #ccc; padding: 5px; }
    th { background-color: #dbeafe; color: #0c4a6e; }
    .section-title { background-color: #e0f2fe; color: #075985; padding: 4px; font-size: 11px; margin-top: 8px; }
    .img-center { text-align: center; margin-top: 6px; margin-bottom: 6px; }
    .img-center img { border: 1px solid #ccc; border-radius: 3px; padding: 2px; }
    .photo-img { width: 60px; height: 70px; }
    .sign-img { width: 80px; height: 25px; }
    .logo-img { width: 80px; height: auto; margin-bottom: 6px; }
    .hod-sign { width: 100px; height: auto; }
</style>
<div style="text-align: center;">
    <img src="' . $logoBase64 . '" class="logo-img" alt="College Logo">
    <h1>All Students Report</h1>
</div>';

$students = $conn->query("SELECT * FROM students");
while ($student = $students->fetch_assoc()) {
    $id = $student['id'];

    $photoPath = file_exists($imageDir . basename($student['student_photo'])) ? $imageDir . basename($student['student_photo']) : $defaultPhoto;
    $signPath = file_exists($uploadDir . basename($student['student_signature'])) ? $uploadDir . basename($student['student_signature']) : $defaultSign;
    $photo = getBase64Img($photoPath);
    $sign = getBase64Img($signPath);

    $subjects = json_decode($student['subjects'], true) ?: [];
    $exSubjects = json_decode($student['ex_subjects'], true) ?: [];

    $challans = [];
    $stmt = $conn->prepare("SELECT * FROM challans WHERE student_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $challan_result = $stmt->get_result();
    while ($row = $challan_result->fetch_assoc()) {
        $fileFullPath = $challanDir . basename($row['file_path']);
        if (file_exists($fileFullPath)) {
            $row['base64'] = base64_encode(file_get_contents($fileFullPath));
            $row['ext'] = pathinfo($fileFullPath, PATHINFO_EXTENSION);
            $challans[] = $row;
        }
    }

    $html .= '<div class="student-block"><div class="page-border">
        <h2>' . htmlspecialchars($student['student_name']) . ' (' . $student['roll_no'] . ')</h2>

        <div class="img-center">
            <div><strong>Photo</strong></div>
            <img src="' . $photo . '" class="photo-img" alt="Photo">
        </div>
        <div class="img-center">
            <div><strong>Signature</strong></div>
            <img src="' . $sign . '" class="sign-img" alt="Signature">
        </div>

        <div class="section-title">Student Information</div>
        <table class="info-table">
            <tr><th>Roll No</th><td>' . $student['roll_no'] . '</td><th>Name</th><td>' . $student['student_name'] . '</td></tr>
            <tr><th>Father & Address</th><td colspan="3">' . $student['father_address'] . '</td></tr>
            <tr><th>Course Type</th><td>' . $student['course_type'] . '</td><th>Semester</th><td>' . $student['current_semester'] . '</td></tr>
            <tr><th>Category</th><td>' . $student['category'] . '</td><th>Mobile</th><td>' . $student['mobile_no'] . '</td></tr>
            <tr><th>Email</th><td>' . $student['email_id'] . '</td><th>Fees</th><td>' . $student['admission_fees'] . '</td></tr>
        </table>';

    if (!empty($subjects)) {
        $html .= '<div class="section-title">Regular Subjects</div>
        <table class="subjects-table">
            <tr><th>Subject</th><th>Sem</th><th>Code</th><th>Th</th><th>Pr</th></tr>';
        foreach ($subjects as $sub) {
            $html .= "<tr>
                <td>{$sub['subject']}</td>
                <td>{$sub['semester']}</td>
                <td>{$sub['paper_code']}</td>
                <td>" . ($sub['theory'] ? '✓' : '✗') . "</td>
                <td>" . ($sub['practical'] ? '✓' : '✗') . "</td>
            </tr>";
        }
        $html .= '</table>';
    }

    if (!empty($exSubjects)) {
        $html .= '<div class="section-title">Ex-Subjects</div>
        <table class="subjects-table">
            <tr><th>Subject</th><th>Sem</th><th>Code</th><th>Th</th><th>Pr</th></tr>';
        foreach ($exSubjects as $sub) {
            $html .= "<tr>
                <td>{$sub['subject']}</td>
                <td>{$sub['semester']}</td>
                <td>{$sub['paper_code']}</td>
                <td>" . ($sub['theory'] ? '✓' : '✗') . "</td>
                <td>" . ($sub['practical'] ? '✓' : '✗') . "</td>
            </tr>";
        }
        $html .= '</table>';
    }

    if (!empty($challans)) {
        $html .= '<div class="section-title">Uploaded Challans</div>
        <table class="challan-table">
            <tr><th>#</th><th>Filename</th><th>Uploaded On</th><th>Download</th></tr>';
        foreach ($challans as $index => $ch) {
            $fileName = htmlspecialchars(basename($ch['file_path']));
            $uploaded = date('d M Y, h:i A', strtotime($ch['uploaded_at']));
            $downloadUrl = 'data:application/' . htmlspecialchars($ch['ext']) . ';base64,' . $ch['base64'];

            $html .= '<tr>
                <td>' . ($index + 1) . '</td>
                <td>' . $fileName . '</td>
                <td>' . $uploaded . '</td>
                <td><a href="' . $downloadUrl . '" download="' . $fileName . '" style="color: #2563eb; text-decoration: underline;">Download</a></td>
            </tr>';
        }
        $html .= '</table>';
    }

    $html .= '
        <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: flex-end;">
            <div><strong>Exam Form Date:</strong> ' . htmlspecialchars($student['exam_date']) . '</div>
            <div style="text-align: right;">
                <img src="' . $hodSignBase64 . '" class="hod-sign" alt="HOD Signature"><br>
                <div style="font-size: 9px; margin-right:5px;">HOD Signature</div>
            </div>
        </div>
    </div></div>';
}

$mpdf->WriteHTML($html);
$mode = $_GET['mode'] ?? 'I';
if (!in_array($mode, ['I', 'D']))
    $mode = 'I';

$filename = 'Student_Report_' . date('Y-m-d') . '.pdf';
$mpdf->Output($filename, $mode);
?>