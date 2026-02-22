<?php
require_once('tcpdf/tcpdf.php'); // Include TCPDF library

// Database Connection
include '../../php/connection.php';

// Fetch all student records
$sql = "SELECT id, roll_no, student_name, father_address, course_type, current_semester, admission_fees, category, mobile_no, email_id, exam_date, student_signature, status FROM students ORDER BY id DESC";
$result = $conn->query($sql);

// Create new PDF document
$pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle("Exam Form Records");
$pdf->SetHeaderData('', 0, 'Exam Form Records', '');
$pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
$pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
$pdf->SetDefaultMonospacedFont('helvetica');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 10);
$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage();

// Table header
$html = '<h2 style="text-align:center;">Exam Form Records</h2>
        <table border="1" cellpadding="5">
        <tr style="background-color:#f2f2f2;">
            <th>ID</th>
            <th>Roll No</th>
            <th>Student Name</th>
            <th>Father Address</th>
            <th>Course Type</th>
            <th>Current Semester</th>
            <th>Admission Fees</th>
            <th>Category</th>
            <th>Mobile No</th>
            <th>Email ID</th>
            <th>Exam Date</th>
            <th>Status</th>
            <th>Signature</th>
        </tr>';

// Table Data
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['id']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['roll_no']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['student_name']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['father_address']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['course_type']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['current_semester']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['admission_fees']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['category']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['mobile_no']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['email_id']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['exam_date']) . '</td>';
        $html .= '<td>' . ucfirst(htmlspecialchars($row['status'])) . '</td>';

        // Signature Path Fix
        $signaturePath = '../php/uploads/' . htmlspecialchars(basename($row['student_signature']));
        if (!empty($row['student_signature']) && file_exists($signaturePath)) {
            $html .= '<td><img src="' . $signaturePath . '" width="60" height="30"></td>';
        } else {
            $html .= '<td style="color:red; text-align:center;">No Signature</td>';
        }

        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="13" style="text-align:center;">No records found.</td></tr>';
}

$html .= '</table>';

// Output PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('exam_form_records.pdf', 'D');
?>
