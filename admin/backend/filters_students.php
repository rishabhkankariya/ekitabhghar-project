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

// Sanitize and fetch filters
$filters = [
  'semester' => $_POST['semester'] ?? '',
  'category' => $_POST['category'] ?? '',
  'date' => $_POST['date'] ?? '',
];

$query = "SELECT * FROM students WHERE 1=1";

// Smart semester matching
if (!empty($filters['semester'])) {
  $sem = mysqli_real_escape_string($conn, $filters['semester']);
  $query .= " AND current_semester LIKE '%$sem%'";
}

// Exact category match
if (!empty($filters['category'])) {
  $cat = mysqli_real_escape_string($conn, $filters['category']);
  $query .= " AND category = '$cat'";
}

// Exact date match
if (!empty($filters['date'])) {
  $date = mysqli_real_escape_string($conn, $filters['date']);
  $query .= " AND exam_date = '$date'";
}

$result = mysqli_query($conn, $query);
if (!$result) {
  die("Query Failed: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0) {
  $sno = 1;
  while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
      <td class='border px-4 py-2'>" . $sno++ . "</td>
      <td class='border px-4 py-2'>" . htmlspecialchars($row['student_name']) . "</td>
      <td class='border px-4 py-2'>" . htmlspecialchars($row['roll_no']) . "</td>
      <td class='border px-4 py-2'>" . getYearFromSemester($row['current_semester']) . "</td>
      <td class='border px-4 py-2'>" . htmlspecialchars($row['current_semester']) . "</td>
      <td class='px-4 py-2 font-medium'>" .
        statusLabel($row['status']) .
      "</td>
    </tr>";
  }
} else {
  echo "<tr><td colspan='6' class='text-center text-gray-500 p-4'>No matching students found 😔</td></tr>";
}

// Utility functions
function getYearFromSemester($sem) {
  $sem = strtolower($sem);
  if (str_contains($sem, '1') || str_contains($sem, '2')) return '1st Year';
  if (str_contains($sem, '3') || str_contains($sem, '4')) return '2nd Year';
  if (str_contains($sem, '5') || str_contains($sem, '6')) return '3rd Year';
  return 'N/A';
}

function statusLabel($status) {
  switch ($status) {
    case 'approved':
      return "<span class='text-green-600'>Approved</span>";
    case 'rejected':
      return "<span class='text-red-600'>Rejected</span>";
    default:
      return "<span class='text-yellow-600'>Pending</span>";
  }
}
?>
