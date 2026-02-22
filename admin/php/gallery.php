<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../php/connection.php';

function resizeImage($sourcePath, $destinationPath, $targetWidth = 310, $targetHeight = 386) {
    list($originalWidth, $originalHeight, $imageType) = getimagesize($sourcePath);

    $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);

    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $originalImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $originalImage = imagecreatefrompng($sourcePath);
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            break;
        case IMAGETYPE_GIF:
            $originalImage = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }

    imagecopyresampled($resizedImage, $originalImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $originalWidth, $originalHeight);

    switch ($imageType) {
        case IMAGETYPE_JPEG:
            imagejpeg($resizedImage, $destinationPath, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($resizedImage, $destinationPath, 9);
            break;
        case IMAGETYPE_GIF:
            imagegif($resizedImage, $destinationPath);
            break;
    }

    imagedestroy($originalImage);
    imagedestroy($resizedImage);

    return true;
}

// Handle image upload and insert into the database
if (isset($_POST['create'])) {
    $title = $_POST['title'];

    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image_path'];
        $uploadDir = '../../img/';
        $originalPath = $uploadDir . basename($image['name']);
        $resizedPath = $uploadDir . 'resized_' . basename($image['name']);

        if (move_uploaded_file($image['tmp_name'], $originalPath)) {
            if (resizeImage($originalPath, $resizedPath)) {
                unlink($originalPath);

                $stmt = $pdo->prepare("INSERT INTO gallery (title, image_path) VALUES (?, ?)");
                if ($stmt->execute([$title, 'resized_' . basename($image['name'])])) {
                    $_SESSION['toast'] = [
                        'type' => 'success',
                        'message' => 'Image uploaded and resized successfully!'
                    ];
                } else {
                    $_SESSION['toast'] = [
                        'type' => 'error',
                        'message' => 'Failed to insert image into database.'
                    ];
                }

                header("Location: /website/admin/adminpanel.php");
                exit();
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Image resize failed.'
                ];
                header("Location: /website/admin/adminpanel.php");
                exit();
            }
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Failed to move uploaded image.'
            ];
            header("Location: /website/admin/adminpanel.php");
            exit();
        }
    } else {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Image upload error. Please try again.'
        ];
        header("Location: /website/admin/adminpanel.php");
        exit();
    }
}

// Handle delete operation
if (isset($_POST['delete_image'])) {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("SELECT image_path FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetch();

    if ($image) {
        $image_path = '../../img/' . $image['image_path'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
        if ($stmt->execute([$id])) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Image deleted successfully!'
            ];
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Failed to delete image from database.'
            ];
        }
    } else {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Image not found.'
        ];
    }

    header("Location: /website/admin/adminpanel.php");
    exit();
}

// Fetch all gallery images for display
$query = $pdo->query("SELECT * FROM gallery ORDER BY id DESC");
$gallery = $query->fetchAll();
?>
