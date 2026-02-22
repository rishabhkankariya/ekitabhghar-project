<?php
// Include database connection
require_once __DIR__ . '/../../php/connection.php';

// Handle visitor count reset
if (isset($_POST['reset'])) {
    try {
        $stmt = $pdo->prepare("UPDATE visitor_count SET count = 0 WHERE id = 1");
        $stmt->execute();
        $_SESSION['toast'] = [
            'type' => 'success',
            'message' => 'Visitor Counter Reset Successfully!'
        ];
        // Use relative redirect to avoid path issues
        header("Location: adminpanel.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Failed to Reset Visitor Counter'
        ];
        header("Location: adminpanel.php");
        exit();
    }
}
?>

<?php
// Include database connection again if this is in a separate block (optional redundancy)
require_once __DIR__ . '/../../php/connection.php';

// Auto-increment visitor count
$stmt = $pdo->prepare("UPDATE visitor_count SET count = count + 1 WHERE id = 1");
$stmt->execute();

// Check if update affected any rows; if not, insert the first record
if ($stmt->rowCount() == 0) {
    $checkQuery = $pdo->query("SELECT id FROM visitor_count WHERE id = 1");
    if (!$checkQuery->fetch()) {
        $pdo->exec("INSERT INTO visitor_count (id, count) VALUES (1, 1)");
    }
}

// Get updated count
$query = $pdo->query("SELECT count FROM visitor_count WHERE id = 1");
$visitor = $query->fetch();
$visitor_count = $visitor['count'] ?? 0;
$total_visitors = $visitor_count;
?>
