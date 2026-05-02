<?php
require_once '../../php/connection.php';

$table = $_GET['table'] ?? '';
$allowedTables = ['students', 'rejected_students'];

if (!in_array($table, $allowedTables)) {
    http_response_code(400);
    echo "Invalid table.";
    exit;
}

try {
    $pdo->exec("SET session_replication_role = 'replica'");
    if ($table === 'students') {
        $pdo->exec("TRUNCATE TABLE students CASCADE");
        $pdo->exec("TRUNCATE TABLE challans CASCADE");
    } elseif ($table === 'rejected_students') {
        $pdo->exec("TRUNCATE TABLE rejected_students CASCADE");
    }
    $pdo->exec("SET session_replication_role = 'origin'");
    echo "success";
} catch (Exception $e) {
    echo "error";
}
?>
