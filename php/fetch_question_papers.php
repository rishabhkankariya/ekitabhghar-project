<?php
require_once 'connection.php'; // Make sure this file has your database connection

header('Content-Type: application/json');

$query = "SELECT * FROM question_papers ORDER BY year, semester";
$result = $conn->query($query);

$questionData = [];

while ($row = $result->fetch_assoc()) {
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
