<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../php/connection.php';

// Create Announcement
if (isset($_POST['create'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    try {
        $stmt = $pdo->prepare("INSERT INTO announcements (title, description, date) VALUES (?, ?, ?)");
        $stmt->execute([$title, $description, $date]);

        $_SESSION['toast'] = [
            'type' => 'success',
            'message' => 'Announcement Created Successfully!'
        ];
    } catch (PDOException $e) {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Error Creating Announcement!'
        ];
    }

    header("Location: /website/admin/adminpanel.php");
    exit();
}

// Delete Announcement
if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['toast'] = [
            'type' => 'success',
            'message' => 'Announcement Deleted Successfully!'
        ];
    } catch (PDOException $e) {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Error Deleting Announcement!'
        ];
    }

    header("Location: /website/admin/adminpanel.php");
    exit();
}

// Fetch all announcements
$stmt = $pdo->query("SELECT * FROM announcements ORDER BY date DESC");
$announcements = $stmt->fetchAll();
?>
