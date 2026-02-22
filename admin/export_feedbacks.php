<?php
require_once __DIR__ . '/../php/connection.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=feedbacks.csv');

$output = fopen("php://output", "w");
fputcsv($output, array('ID', 'Name', 'Email', 'Rating', 'Message', 'Submitted At'));

$result = $conn->query("SELECT * FROM feedback ORDER BY submitted_at DESC");

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit;
?>
