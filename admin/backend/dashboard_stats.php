<?php
require_once '../../php/connection.php';
header('Content-Type: application/json');

$response = ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'total' => 0];

$stmt = $pdo->query("SELECT status, COUNT(*) as count FROM students GROUP BY status");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $status = strtolower($row['status']);
    if (in_array($status, ['pending', 'approved'])) $response[$status] = (int)$row['count'];
}

$row = $pdo->query("SELECT COUNT(*) as count FROM rejected_students")->fetch(PDO::FETCH_ASSOC);
$response['rejected'] = (int)$row['count'];
$response['total'] = $response['pending'] + $response['approved'];

echo json_encode($response);
?>
