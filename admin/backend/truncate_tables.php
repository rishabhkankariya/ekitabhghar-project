<?php

// DB Config
$servername = "localhost";
$username = "root";
$password = "";
$database = "ekitabhghar";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}


$table = $_GET['table'] ?? '';
$allowedTables = ['students', 'rejected_students'];

if (!in_array($table, $allowedTables)) {
  http_response_code(400);
  echo "Invalid table.";
  exit;
}

$queries = ["SET FOREIGN_KEY_CHECKS = 0"];

if ($table === 'students') {
  $queries[] = "TRUNCATE TABLE students";
  $queries[] = "TRUNCATE TABLE challans";
} elseif ($table === 'rejected_students') {
  $queries[] = "TRUNCATE TABLE rejected_students";
}

$queries[] = "SET FOREIGN_KEY_CHECKS = 1";

try {
  foreach ($queries as $sql) {
    if (!$conn->query($sql)) {
      throw new Exception("Query failed: " . $conn->error);
    }
  }
  echo "success";
} catch (Exception $e) {
  // Optional: Uncomment below line to debug
  // echo "Error: " . $e->getMessage();
  echo "error";
}
?>
