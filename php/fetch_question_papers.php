<?php
require_once 'connection.php';

header('Content-Type: application/json');

$stmt = $pdo->query("SELECT * FROM question_papers ORDER BY year, semester");
$questionData = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $year = $row['year'];
    $semester = $row['semester'];

    if (!isset($questionData[$year])) {
        $questionData[$year] = [];
    }

    if (!isset($questionData[$year][$semester])) {
        $questionData[$year][$semester] = [];
    }

    $questionData[$year][$semester][] = [
        'name' => $row['subject_name'],
        'pdf' => $row['pdf_path']
    ];
}

echo json_encode($questionData);
?>
