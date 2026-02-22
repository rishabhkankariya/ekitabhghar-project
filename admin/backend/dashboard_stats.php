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

// Initialize response array
$response = [
  'pending' => 0,
  'approved' => 0,
  'rejected' => 0,
  'total' => 0
];

// Get counts for 'pending' and 'approved' from `students` table
$sql = "SELECT status, COUNT(*) as count FROM students GROUP BY status";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $status = strtolower($row['status']);
    if (in_array($status, ['pending', 'approved'])) {
      $response[$status] = (int)$row['count'];
    }
  }
}

// Get rejected count from `rejected_students` table
$rejectedResult = $conn->query("SELECT COUNT(*) as count FROM rejected_students");
if ($rejectedResult) {
  $row = $rejectedResult->fetch_assoc();
  $response['rejected'] = (int)$row['count'];
}

// Total should be only pending + approved
$response['total'] = $response['pending'] + $response['approved'];

// Return JSON
echo json_encode($response);
?>
