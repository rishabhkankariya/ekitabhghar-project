<?php

// DB Connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "ekitabhghar";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

// Only get non-null valid years from exam_date
$query = "SELECT DISTINCT YEAR(exam_date) as year FROM students WHERE exam_date IS NOT NULL ORDER BY year DESC";
$result = $conn->query($query);

$years = [];

while ($row = $result->fetch_assoc()) {
    if (!empty($row['year'])) {
        $years[] = $row['year'];
    }
}

echo json_encode($years);
?>
