<?php
header('Content-Type: application/json');
require_once '../../php/connection.php';

$year = $_GET['year'] ?? 'all';

try {
    $sql = "SELECT * FROM students";
    $params = [];

    if ($year !== 'all') {
        $yearInt = intval($year);
        $likeClauses = [];
        if ($yearInt === 1) $likeClauses = ['%1st%', '%2nd%'];
        if ($yearInt === 2) $likeClauses = ['%3rd%', '%4th%'];
        if ($yearInt === 3) $likeClauses = ['%5th%', '%6th%'];

        $conditions = implode(' OR ', array_fill(0, count($likeClauses), 'current_semester LIKE ?'));
        $sql .= " WHERE ($conditions)";
        $params = $likeClauses;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $students = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
