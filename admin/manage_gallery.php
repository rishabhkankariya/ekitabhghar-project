<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

require_once '../php/connection.php';

// Handle Image Operations
$toast = null;
if (isset($_SESSION['toast'])) {
    $toast = $_SESSION['toast'];
    unset($_SESSION['toast']);
}

// Image Resizing Function (High Quality)
function resizeImage($sourcePath, $destinationPath, $targetWidth = 800, $targetHeight = 600)
{
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

// Logic for Upload & Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['upload'])) {
        $title = $_POST['title'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $image = $_FILES['image'];
            $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
            $newName = 'gallery_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $uploadDir = '../img/';
            $targetPath = $uploadDir . $newName;

            if (move_uploaded_file($image['tmp_name'], $targetPath)) {
                // Optional: Resize can be adjusted or skipped if you want full quality
                // For now, let's keep it 800x600 for consistency on the gallery page
                $resizedPath = $uploadDir . 'res_' . $newName;
                if (resizeImage($targetPath, $resizedPath)) {
                    unlink($targetPath); // Remove original
                    $finalPath = 'res_' . $newName;
                } else {
                    $finalPath = $newName; // Fail back to original if resize fails
                }

                $stmt = $pdo->prepare("INSERT INTO gallery (title, image_path) VALUES (?, ?)");
                if ($stmt->execute([$title, $finalPath])) {
                    $_SESSION['toast'] = ['type' => 'success', 'message' => 'Image Added to Gallery!'];
                }
            }
        }
        header("Location: manage_gallery.php");
        exit();
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("SELECT image_path FROM gallery WHERE id = ?");
        $stmt->execute([$id]);
        $img = $stmt->fetch();
        if ($img) {
            @unlink('../img/' . $img['image_path']);
            $pdo->prepare("DELETE FROM gallery WHERE id = ?")->execute([$id]);
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Image Removed!'];
        }
        header("Location: manage_gallery.php");
        exit();
    }
}

// Fetch Gallery
$gallery = $pdo->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Manage Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        .custom-glass {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .gradient-text {
            background: linear-gradient(135deg, #60a5fa, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .image-card:hover .overlay {
            opacity: 1;
        }
    </style>
</head>

<body class="bg-[#020617] text-slate-100 min-h-screen">

    <!-- 🌟 Glass Header -->
    <header class="sticky top-0 z-50 custom-glass border-b border-white/5 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                <i class="bi bi-images text-xl text-white"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-tight">Gallery <span class="gradient-text">Studio</span></h1>
                <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Admin Management Tool</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <a href="adminpanel.php"
                class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-sm font-semibold transition-all">Back to
                Dashboard</a>
            <div class="h-6 w-px bg-white/10"></div>
            <img src="img/dashboard.png" class="w-8 h-8 rounded-full border border-white/20">
        </div>
    </header>

    <main class="max-w-7xl mx-auto p-6">

        <!-- 📤 Upload Section -->
        <div class="grid lg:grid-cols-4 gap-8 mb-12">
            <div class="lg:col-span-1">
                <div class="sticky top-28">
                    <h2 class="text-2xl font-black mb-2">Publish <span class="text-blue-500">New</span></h2>
                    <p class="text-slate-400 text-sm mb-6">Upload high-quality images to capture the college's best
                        moments. Recommended aspect ratio 4:3.</p>

                    <form action="" method="POST" enctype="multipart/form-data"
                        class="space-y-4 p-6 rounded-3xl bg-white/5 border border-white/10">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Image
                                Title</label>
                            <input type="text" name="title" required placeholder="Event or Class name..."
                                class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Select
                                Image</label>
                            <div class="relative group">
                                <input type="file" name="image" required accept="image/*"
                                    class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                <div
                                    class="w-full bg-slate-900 border-2 border-dashed border-white/10 rounded-2xl py-8 flex flex-col items-center justify-center group-hover:border-blue-500/50 transition-all">
                                    <i
                                        class="bi bi-cloud-arrow-up text-3xl text-slate-500 group-hover:text-blue-500 mb-2"></i>
                                    <span class="text-xs text-slate-400 font-medium">Click or Drop Image</span>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="upload"
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-4 rounded-2xl shadow-xl shadow-blue-500/10 transition-all active:scale-95">
                            Upload Now
                        </button>
                    </form>
                </div>
            </div>

            <!-- 🖼️ Image Catalog -->
            <div class="lg:col-span-3">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold">Manage <span class="text-slate-500">Catalog</span> <span
                            class="ml-2 px-3 py-1 bg-white/5 rounded-full text-xs text-slate-400">
                            <?= count($gallery) ?> Images
                        </span></h3>
                    <div class="flex gap-2">
                        <button
                            class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center hover:bg-white/10"><i
                                class="bi bi-grid-3x3-gap"></i></button>
                        <button
                            class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center hover:bg-white/10"><i
                                class="bi bi-list-ul"></i></button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php if (empty($gallery)): ?>
                        <div class="col-span-full py-20 text-center">
                            <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-image text-4xl text-slate-600"></i>
                            </div>
                            <h4 class="text-lg font-bold text-slate-500">No images found</h4>
                            <p class="text-slate-600">Start uploading to see them here.</p>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($gallery as $img): ?>
                        <div
                            class="image-card relative aspect-[4/3] rounded-3xl overflow-hidden group bg-slate-900 border border-white/5">
                            <img src="../img/<?= $img['image_path'] ?>"
                                class="w-full h-full object-cover grayscale-[0.5] group-hover:grayscale-0 transition-all duration-700 group-hover:scale-110">

                            <div
                                class="overlay absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent opacity-0 transition-opacity duration-300 p-6 flex flex-col justify-end">
                                <h4 class="text-white font-bold mb-1 truncate">
                                    <?= htmlspecialchars($img['title']) ?>
                                </h4>
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] text-slate-400 uppercase font-black tracking-widest">ID: #
                                        <?= $img['id'] ?>
                                    </span>
                                    <form action="" method="POST" onsubmit="return confirm('Archive this image?')">
                                        <input type="hidden" name="id" value="<?= $img['id'] ?>">
                                        <button type="submit" name="delete"
                                            class="w-10 h-10 rounded-xl bg-red-500/20 text-red-500 hover:bg-red-500 hover:text-white transition-all">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- 🍞 Toast Notification -->
    <?php if ($toast): ?>
        <div id="toast" class="fixed bottom-10 right-10 z-[100] animate-bounce">
            <div
                class="px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 <?= $toast['type'] === 'success' ? 'bg-emerald-500' : 'bg-rose-500' ?>">
                <i
                    class="bi <?= $toast['type'] === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> text-xl text-white"></i>
                <p class="text-white font-bold text-sm">
                    <?= $toast['message'] ?>
                </p>
            </div>
        </div>
        <script>setTimeout(() => { document.getElementById('toast').style.display = 'none' }, 4000);</script>
    <?php endif; ?>

</body>

</html>
