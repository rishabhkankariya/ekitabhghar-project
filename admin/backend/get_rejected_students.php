<?php
// get_rejected_students.php

header('Content-Type: application/json');

// DB Config
$servername = "localhost";
$username = "root";
$password = "";
$database = "ekitabhghar";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "DB connection failed"]);
    exit();
}

// Select ALL necessary fields
$sql = "SELECT 
            roll_no,
            student_name,
            current_semester,
            category,
            mobile_no,
            email_id,
            exam_date,
            reason,
            status,
            rejected_at 
        FROM rejected_students 
        ORDER BY rejected_at DESC";

$result = $conn->query($sql);
$students = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Format date fields to readable strings if needed
        $row['exam_date'] = $row['exam_date'] ? date("d M Y", strtotime($row['exam_date'])) : null;
        $row['rejected_at'] = $row['rejected_at'] ? date("d M Y h:i A", strtotime($row['rejected_at'])) : null;

        $students[] = $row;
    }
}

echo json_encode($students);
$conn->close();
?>
