<?php
require_once '../../php/connection.php';
$stmt = $pdo->query("SELECT DISTINCT EXTRACT(YEAR FROM exam_date) as year FROM students WHERE exam_date IS NOT NULL ORDER BY year DESC");
$years = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (!empty($row['year'])) $years[] = $row['year'];
}
echo json_encode($years);
?>
