<?php
require_once '../../php/connection.php';
$status = $_GET['status'] ?? '';
if ($status === 'total') {
    $stmt = $pdo->query("SELECT roll_no, student_name, email_id, status FROM students");
} else {
    $stmt = $pdo->prepare("SELECT roll_no, student_name, email_id, status FROM students WHERE status = ?");
    $stmt->execute([$status]);
}
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
