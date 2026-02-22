<?php

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "ekitabhghar";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$status = $_GET['status'] ?? '';

$query = ($status === 'total')
  ? "SELECT roll_no, student_name, email_id, status FROM students"
  : "SELECT roll_no, student_name, email_id, status FROM students WHERE status = ?";

$stmt = $conn->prepare($query);

if ($status !== 'total') {
  $stmt->bind_param("s", $status);
}

$stmt->execute();
$result = $stmt->get_result();

$students = [];

while ($row = $result->fetch_assoc()) {
  $students[] = $row;
}

echo json_encode($students);
?>
