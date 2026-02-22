<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../php/connection.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Create Slide Entry
    if (isset($_POST['create_slide'])) {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = "../img/slides/";
            $fileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            if (in_array($fileType, $allowedTypes)) {
                $newFileName = uniqid("slide_", true) . "." . $fileType;
                $targetFilePath = $targetDir . $newFileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                    $stmt = $pdo->prepare("INSERT INTO slides (image_url) VALUES (?)");
                    $stmt->execute([$newFileName]);

                    // ✅ Success Toast Message
                    $_SESSION['toast'] = [
                        'type' => 'success',
                        'message' => 'Slide uploaded successfully!'
                    ];
                } else {
                    $_SESSION['toast'] = [
                        'type' => 'error',
                        'message' => 'Failed to upload slide image.'
                    ];
                }
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Invalid image format. Allowed: JPG, PNG, WEBP.'
                ];
            }
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'No file selected or upload error occurred.'
            ];
        }
    }

    // Delete Slide Entry
    if (isset($_POST['delete_slide'])) {
        $id = $_POST['id'];

        $stmt = $pdo->prepare("SELECT image_url FROM slides WHERE id = ?");
        $stmt->execute([$id]);
        $slide = $stmt->fetch();

        if ($slide) {
            $imagePath = "../img/slides/" . $slide['image_url'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $stmt = $pdo->prepare("DELETE FROM slides WHERE id = ?");
            $stmt->execute([$id]);

            // ✅ Delete Toast Message
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Slide deleted successfully!'
            ];
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Slide not found or already deleted.'
            ];
        }
    }

    header("Location: /website/admin/adminpanel.php");
    exit();
}

// Fetch all slides from the database
$stmt = $pdo->query("SELECT * FROM slides ORDER BY id DESC");
$slides = $stmt->fetchAll();
?>
