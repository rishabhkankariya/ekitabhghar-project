<?php
header('Content-Type: application/json');
require_once '../../php/connection.php';

$stmt = $pdo->query("SELECT roll_no, student_name, current_semester, category, mobile_no, email_id, exam_date, reason, status, rejected_at FROM rejected_students ORDER BY rejected_at DESC");
$students = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $row['exam_date'] = $row['exam_date'] ? date("d M Y", strtotime($row['exam_date'])) : null;
    $row['rejected_at'] = $row['rejected_at'] ? date("d M Y h:i A", strtotime($row['rejected_at'])) : null;
    $students[] = $row;
}
echo json_encode($students);
?>
