<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../php/connection.php';

// Create Event
if (isset($_POST['create_event'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $image = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../img/';
        $filename = uniqid('event_', true) . '_' . basename($_FILES['image']['name']);
        $uploadPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            $image = $filename;
        }
    }

    // Only insert if all fields are filled
    if ($title && $description && $event_date && $image) {
        $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $event_date, $image]);
                 $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Event Added SuccessFully!'
                ];
                header("Location: /website/admin/adminpanel.php");
                exit();
    } else {
        $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Missing Fields Or Image Upload Failed'
                ];
                header("Location: /website/admin/adminpanel.php");
                exit();
    }
}

// Delete Event
if (isset($_POST['delete_event'])) {
    $id = $_POST['id'];

    // Fetch event to delete image
    $stmt = $pdo->prepare("SELECT image FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch();

    if ($event && !empty($event['image'])) {
        $imagePath = '../../img/' . $event['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // Delete the event
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$id]);
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Event Deleted Successfully!'
                ];
                header("Location: /website/admin/adminpanel.php");
                exit();
}

// Fetch all events
$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date DESC");
$events = $stmt->fetchAll();
?>
