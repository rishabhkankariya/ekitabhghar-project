<?php
header('Content-Type: application/json');
include 'connection.php'; // Include database connection

$query = "SELECT * FROM syllabus ORDER BY year, semester";
$result = $conn->query($query);

$syllabusData = [];

while ($row = $result->fetch_assoc()) {
    $year = $row['year'];
    $semester = $row['semester'];

    if (!isset($syllabusData[$year])) {
        $syllabusData[$year] = [];
    }

    if (!isset($syllabusData[$year][$semester])) {
        $syllabusData[$year][$semester] = [];
    }

    $syllabusData[$year][$semester][] = [
        'name' => $row['subject_name'],
        'pdf' => $row['pdf_path']
    ];
}

echo json_encode($syllabusData);
?>
