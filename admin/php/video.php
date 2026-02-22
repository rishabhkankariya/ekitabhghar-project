<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../php/connection.php';

// Handle video upload
if (isset($_POST['create'])) {
    $title = $_POST['title'];
    $video = $_FILES['video'];

    // Check if the video was uploaded successfully
    if ($video['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../img/';
        $original_name = pathinfo($video['name'], PATHINFO_FILENAME);
        $extension = pathinfo($video['name'], PATHINFO_EXTENSION);

        // Generate unique filename to prevent duplicate issues
        $unique_name = $original_name . '_' . time() . '.' . $extension;
        $video_path = $upload_dir . $unique_name;

        // Move uploaded file to the 'img' folder
        if (move_uploaded_file($video['tmp_name'], $video_path)) {
            // Insert into the database
            $stmt = $pdo->prepare("INSERT INTO videos (title, video_path) VALUES (?, ?)");
            if ($stmt->execute([$title, $unique_name])) {
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Video Uploaded Successfully!'
                ];
                header("Location: /website/admin/adminpanel.php");
                exit();
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Failed to insert video into the database.'
                ];
                header("Location: /website/admin/adminpanel.php");
                exit();
            }
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Failed to move uploaded video.'
            ];
            header("Location: /website/admin/adminpanel.php");
            exit();
        }
    } else {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'File upload failed. Error code: ' . $video['error']
        ];
        header("Location: /website/admin/adminpanel.php");
        exit();
    }
}

// Handle delete operation
if (isset($_POST['delete_video'])) {
    $id = $_POST['id'];

    // Fetch video path from the database
    $stmt = $pdo->prepare("SELECT video_path FROM videos WHERE id = ?");
    $stmt->execute([$id]);
    $video = $stmt->fetch();

    if ($video) {
        // Construct the full file path
        $video_path = realpath('../img/' . $video['video_path']);

        if ($video_path && file_exists($video_path)) {
            if (unlink($video_path)) {
                // Delete from the database
                $stmt = $pdo->prepare("DELETE FROM videos WHERE id = ?");
                if ($stmt->execute([$id])) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Video Deleted successfully!'
                ];
                header("Location: /website/admin/adminpanel.php");
                exit();
                } else {
                    $_SESSION['toast'] = [
                        'type' => 'error',
                        'message' => 'Failed to delete video from the database.'
                    ];
                    header("Location: /website/admin/adminpanel.php");
                    exit();
                }
            } else {
                $_SESSION['toast'] = [
                        'type' => 'error',
                        'message' => 'Failed to delete video file from the server.'
                ];
                header("Location: /website/admin/adminpanel.php");
                exit();
            }
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Video file does not exist: ' . $video_path
            ];
            header("Location: /website/admin/adminpanel.php");
            exit();
        }
    } 
}
// Fetch all videos from the database
$query = $pdo->query("SELECT * FROM videos ORDER BY id DESC");
$videos = $query->fetchAll();
?>
