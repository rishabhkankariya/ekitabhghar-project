<?php
header('Content-Type: application/json');

// DB Connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "ekitabhghar";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

$year = $_GET['year'] ?? 'all';
$response = [];

try {
    $sql = "SELECT * FROM students";
    $params = [];

    if ($year !== 'all') {
        $yearInt = intval($year);
        $likeClauses = [];

        if ($yearInt === 1)
            $likeClauses = ['%1st%', '%2nd%'];
        if ($yearInt === 2)
            $likeClauses = ['%3rd%', '%4th%'];
        if ($yearInt === 3)
            $likeClauses = ['%5th%', '%6th%'];

        $conditions = implode(' OR ', array_fill(0, count($likeClauses), 'current_semester LIKE ?'));
        $sql .= " WHERE ($conditions)";
        $params = $likeClauses;
    }

    // Prepare SQL safely
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
        // Derive year from semester value using fuzzy matching
        $semester = strtolower($row['current_semester']);
        $row['year'] = match (true) {
            str_contains($semester, '1st'), str_contains($semester, '2nd') => '1st Year',
            str_contains($semester, '3rd'), str_contains($semester, '4th') => '2nd Year',
            str_contains($semester, '5th'), str_contains($semester, '6th') => '3rd Year',
            default => 'Unknown',
        };
        $students[] = $row;
    }

    echo json_encode($students);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
