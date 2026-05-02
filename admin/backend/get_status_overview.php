<?php
header('Content-Type: application/json');
require_once '../../php/connection.php';

$counts = ['Pending' => 0, 'Approved' => 0, 'Rejected' => 0, 'Total' => 0];

$stmt = $pdo->query("SELECT status, COUNT(*) as total FROM students WHERE status IN ('pending', 'approved') GROUP BY status");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $status = ucfirst(strtolower($row['status']));
    if (isset($counts[$status])) $counts[$status] = (int)$row['total'];
}

$row = $pdo->query("SELECT COUNT(*) as total FROM rejected_students")->fetch(PDO::FETCH_ASSOC);
$counts['Rejected'] = (int)$row['total'];
$counts['Total'] = $counts['Pending'] + $counts['Approved'];

echo json_encode($counts);
?>
