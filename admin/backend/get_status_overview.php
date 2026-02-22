<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$database = "ekitabhghar";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$counts = [
    'Pending' => 0,
    'Approved' => 0,
    'Rejected' => 0,
    'Total' => 0  // Add total manually (Pending + Approved only)
];

// Get Pending and Approved counts from `students` table
$sql = "SELECT status, COUNT(*) as total FROM students WHERE status IN ('pending', 'approved') GROUP BY status";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $status = ucfirst(strtolower($row['status']));
    if (isset($counts[$status])) {
        $counts[$status] = (int) $row['total'];
    }
}

// Get Rejected count from `rejected_students` table
$rejectedResult = $conn->query("SELECT COUNT(*) as total FROM rejected_students");
if ($rejectedResult) {
    $row = $rejectedResult->fetch_assoc();
    $counts['Rejected'] = (int) $row['total'];
}

// Total = Pending + Approved only
$counts['Total'] = $counts['Pending'] + $counts['Approved'];

echo json_encode($counts);
?>
