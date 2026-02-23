<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../php/connection.php';

// Create Modal Announcement
if (isset($_POST['create'])) {
    $title = $_POST['title'];
    $message = $_POST['message'];

    // Prepare SQL query to insert data
    $stmt = $pdo->prepare("INSERT INTO modal_announcement (title, message) VALUES (?, ?)");
    if ($stmt->execute([$title, $message])) {
        $_SESSION['toast'] = [
            'type' => 'success',
            'message' => 'Modal announcement created successfully!'
        ];
    } else {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Failed to create modal announcement.'
        ];
    }
    header("Location: /admin/adminpanel.php");
    exit();
}

// Delete Modal Announcement
if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    // Prepare SQL query to delete the modal announcement
    $stmt = $pdo->prepare("DELETE FROM modal_announcement WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Modal announcement deleted successfully!'
        ];
    } else {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Failed to delete modal announcement.'
        ];
    }

   header("Location: /admin/adminpanel.php");
   exit();
}

// Fetch all modal announcements from the database
$stmt = $pdo->query("SELECT * FROM modal_announcement ORDER BY created_at DESC");
$modal_announcements = $stmt->fetchAll();
?>
