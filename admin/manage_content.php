<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

require_once '../php/connection.php';

// --- SHARED TOAST HANDLER ---
$toast = null;
if (isset($_SESSION['toast'])) {
    $toast = $_SESSION['toast'];
    unset($_SESSION['toast']);
}

// --- LOGIC HANDLING ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 0. Main Gallery
    if (isset($_POST['create_gallery'])) {
        $title = $_POST['title'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $newName = uniqid("gal_", true) . "." . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], "../img/" . $newName)) {
                $pdo->prepare("INSERT INTO gallery (title, image_path) VALUES (?, ?)")->execute([$title, $newName]);
                $_SESSION['toast'] = ['type' => 'success', 'message' => 'Image added to Gallery!'];
            }
        }
    }
    if (isset($_POST['delete_gallery'])) {
        $stmt = $pdo->prepare("SELECT image_path FROM gallery WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $img = $stmt->fetch();
        if ($img) {
            @unlink("../img/" . $img['image_path']);
        }
        $pdo->prepare("DELETE FROM gallery WHERE id = ?")->execute([$_POST['id']]);
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Gallery image removed.'];
    }

    // 1. AlertAnnouncements (Modal)
    if (isset($_POST['create_alert'])) {
        $pdo->prepare("INSERT INTO modal_announcement (title, message) VALUES (?, ?)")
            ->execute([$_POST['title'], $_POST['message']]);
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Alert Announcement published!'];
    }
    if (isset($_POST['delete_alert'])) {
        $pdo->prepare("DELETE FROM modal_announcement WHERE id = ?")->execute([$_POST['id']]);
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Alert removed.'];
    }

    // 2. Standard Announcements
    if (isset($_POST['create_announcement'])) {
        $pdo->prepare("INSERT INTO announcements (title, description, date) VALUES (?, ?, ?)")
            ->execute([$_POST['title'], $_POST['description'], $_POST['date']]);
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Announcement posted!'];
    }
    if (isset($_POST['delete_announcement'])) {
        $pdo->prepare("DELETE FROM announcements WHERE id = ?")->execute([$_POST['id']]);
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Announcement deleted.'];
    }

    // 3. Slide Images
    if (isset($_POST['create_slide'])) {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = "img/slides/";
            if (!is_dir($targetDir))
                mkdir($targetDir, 0777, true);
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $newName = uniqid("slide_", true) . "." . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $newName)) {
                $pdo->prepare("INSERT INTO slides (image_url) VALUES (?)")->execute([$newName]);
                $_SESSION['toast'] = ['type' => 'success', 'message' => 'Slide uploaded!'];
            }
        }
    }
    if (isset($_POST['delete_slide'])) {
        $stmt = $pdo->prepare("SELECT image_url FROM slides WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $slide = $stmt->fetch();
        if ($slide) {
            @unlink("img/slides/" . $slide['image_url']);
        }
        $pdo->prepare("DELETE FROM slides WHERE id = ?")->execute([$_POST['id']]);
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Slide deleted.'];
    }

    // 4. Upcoming Events
    if (isset($_POST['create_event'])) {
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $image = uniqid('ev_', true) . '_' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], '../img/' . $image);
        }
        if ($image) {
            $pdo->prepare("INSERT INTO events (title, description, event_date, image) VALUES (?, ?, ?, ?)")
                ->execute([$_POST['title'], $_POST['description'], $_POST['event_date'], $image]);
            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Event scheduled!'];
        }
    }
    if (isset($_POST['delete_event'])) {
        $stmt = $pdo->prepare("SELECT image FROM events WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $ev = $stmt->fetch();
        if ($ev) {
            @unlink('../img/' . $ev['image']);
        }
        $pdo->prepare("DELETE FROM events WHERE id = ?")->execute([$_POST['id']]);
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Event removed.'];
    }

    // 5. Video Gallery
    if (isset($_POST['create_video'])) {
        if ($_FILES['video']['error'] === 0) {
            $newName = time() . '_' . $_FILES['video']['name'];
            if (move_uploaded_file($_FILES['video']['tmp_name'], '../img/' . $newName)) {
                $pdo->prepare("INSERT INTO videos (title, video_path) VALUES (?, ?)")->execute([$_POST['title'], $newName]);
                $_SESSION['toast'] = ['type' => 'success', 'message' => 'Video uploaded!'];
            }
        }
    }
    if (isset($_POST['delete_video'])) {
        $stmt = $pdo->prepare("SELECT video_path FROM videos WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $vid = $stmt->fetch();
        if ($vid) {
            @unlink('../img/' . $vid['video_path']);
        }
        $pdo->prepare("DELETE FROM videos WHERE id = ?")->execute([$_POST['id']]);
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Video deleted.'];
    }

    header("Location: manage_content.php");
    exit();
}

// --- FETCHING DATA ---
$gallery = $pdo->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();
$alerts = $pdo->query("SELECT * FROM modal_announcement ORDER BY created_at DESC")->fetchAll();
$announcements = $pdo->query("SELECT * FROM announcements ORDER BY date DESC")->fetchAll();
$slides = $pdo->query("SELECT * FROM slides ORDER BY id DESC")->fetchAll();
$events = $pdo->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll();
$videos = $pdo->query("SELECT * FROM videos ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio | Central Workspace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #020617;
            color: #f8fafc;
            overflow-x: hidden;
        }

        .glass-nav {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .section-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .section-card:hover {
            border-color: rgba(59, 130, 246, 0.4);
            background: rgba(255, 255, 255, 0.04);
            transform: translateY(-5px);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <nav class="sticky top-0 z-[100] glass-nav px-8 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div
                class="w-12 h-12 bg-gradient-to-tr from-blue-600 to-violet-600 rounded-2xl flex items-center justify-center shadow-[0_0_30px_rgba(37,99,235,0.3)]">
                <i class="bi bi-grid-1x2-fill text-xl text-white"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black tracking-tighter">Central <span class="text-blue-500">Studio</span></h1>
                <p class="text-[10px] uppercase tracking-[0.3em] font-bold text-slate-500">Unified Management Portal</p>
            </div>
        </div>
        <a href="adminpanel.php"
            class="px-6 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 font-bold text-xs uppercase tracking-widest transition-all border border-white/5">Back
            to Dashboard</a>
    </nav>

    <div class="max-w-7xl mx-auto p-4 md:p-10 flex flex-col lg:flex-row gap-12">

        <!-- 🧭 Smart Sidebar -->
        <aside class="lg:w-80 shrink-0">
            <div class="sticky top-32 space-y-1">
                <p class="px-4 text-[10px] uppercase font-black text-slate-600 tracking-widest mb-4">Operations</p>

                <?php
                $navItems = [
                    ['id' => 'gallery', 'icon' => 'bi-images', 'color' => 'text-blue-500', 'label' => 'Photo Gallery'],
                    ['id' => 'news', 'icon' => 'bi-megaphone-fill', 'color' => 'text-indigo-500', 'label' => 'Announcements'],
                    ['id' => 'alerts', 'icon' => 'bi-bell-fill', 'color' => 'text-amber-500', 'label' => 'Modal Alerts'],
                    ['id' => 'slides', 'icon' => 'bi-play-btn-fill', 'color' => 'text-pink-500', 'label' => 'Hero Slides'],
                    ['id' => 'events', 'icon' => 'bi-calendar-event-fill', 'color' => 'text-emerald-500', 'label' => 'Campus Events'],
                    ['id' => 'videos', 'icon' => 'bi-camera-reels-fill', 'color' => 'text-orange-500', 'label' => 'Video Archive']
                ];
                foreach ($navItems as $item): ?>
                    <button onclick="scrollToSection('<?= $item['id'] ?>')"
                        class="w-full text-left px-5 py-4 rounded-2xl hover:bg-white/5 transition-all flex items-center gap-4 group">
                        <i class="bi <?= $item['icon'] ?> <?= $item['color'] ?> text-lg"></i>
                        <span class="font-bold text-sm"><?= $item['label'] ?></span>
                        <i
                            class="bi bi-chevron-right ml-auto opacity-0 group-hover:opacity-100 transition-all text-[10px]"></i>
                    </button>
                <?php endforeach; ?>
            </div>
        </aside>

        <!-- 🌊 Main Flow -->
        <div class="flex-1 space-y-32">

            <!-- 🖼️ Section: Gallery -->
            <section id="gallery" class="scroll-mt-32">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h2 class="text-4xl font-black mb-2 tracking-tight">Photo <span
                                class="text-blue-500">Gallery</span></h2>
                        <p class="text-slate-500 text-sm">Visual moments for the Home Page Mosaic.</p>
                    </div>
                    <button onclick="toggleForm('galForm')"
                        class="px-6 py-3 rounded-xl bg-blue-500/10 text-blue-500 font-bold text-xs uppercase tracking-widest hover:bg-blue-500 hover:text-white transition-all">
                        <i class="bi bi-plus-lg mr-2"></i> Add Photo
                    </button>
                </div>

                <div id="galForm"
                    class="hidden mb-12 p-8 rounded-[2.5rem] bg-white/5 border border-white/10 shadow-2xl">
                    <form action="" method="POST" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] uppercase font-black text-slate-500 ml-1">Image Title</label>
                            <input type="text" name="title" required
                                class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-4 text-sm focus:border-blue-500 outline-none transition-all">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] uppercase font-black text-slate-500 ml-1">Choose File</label>
                            <input type="file" name="image" required
                                class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-3 text-sm focus:border-blue-500 outline-none transition-all">
                        </div>
                        <button type="submit" name="create_gallery"
                            class="md:col-span-2 py-5 bg-blue-600 text-white font-black rounded-2xl shadow-xl shadow-blue-500/20 active:scale-95 transition-all uppercase tracking-widest">Upload
                            to Gallery</button>
                    </form>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($gallery as $g): ?>
                        <div
                            class="group relative aspect-square rounded-[2rem] overflow-hidden bg-slate-900 border border-white/5">
                            <img src="../img/<?= $g['image_path'] ?>"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent flex flex-col justify-end p-6 opacity-0 group-hover:opacity-100 transition-all">
                                <h4 class="text-xs font-bold text-white mb-4 line-clamp-2">
                                    <?= htmlspecialchars($g['title']) ?>
                                </h4>
                                <form action="" method="POST" class="flex">
                                    <input type="hidden" name="id" value="<?= $g['id'] ?>">
                                    <button name="delete_gallery"
                                        class="w-10 h-10 rounded-xl bg-red-500 text-white shadow-xl hover:scale-110 transition-all"><i
                                            class="bi bi-trash3"></i></button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- 📢 Section: Announcements -->
            <section id="news" class="scroll-mt-32">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h2 class="text-4xl font-black mb-2 tracking-tight">Main <span
                                class="text-indigo-500">Announcements</span></h2>
                        <p class="text-slate-500 text-sm">Text-based updates for the notice board section.</p>
                    </div>
                    <button onclick="toggleForm('newsForm')"
                        class="px-6 py-3 rounded-xl bg-indigo-500/10 text-indigo-500 font-bold text-xs uppercase tracking-widest hover:bg-indigo-500 hover:text-white transition-all">
                        <i class="bi bi-journal-plus mr-2"></i> Post News
                    </button>
                </div>

                <div id="newsForm" class="hidden mb-12 p-8 rounded-[2.5rem] bg-white/5 border border-white/10">
                    <form action="" method="POST" class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-black text-slate-500">Headline</label>
                                <input type="text" name="title" required
                                    class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-4 text-sm outline-none focus:border-indigo-500">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-black text-slate-500">Publish Date</label>
                                <input type="date" name="date" required
                                    class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-4 text-sm outline-none focus:border-indigo-500">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-black text-slate-500">Description / URL
                                (Optional)</label>
                            <textarea name="description" rows="3"
                                class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-4 text-sm outline-none focus:border-indigo-500"></textarea>
                        </div>
                        <button type="submit" name="create_announcement"
                            class="w-full py-5 bg-indigo-600 text-white font-black rounded-2xl transition-all active:scale-95">Post
                            Announcement</button>
                    </form>
                </div>

                <div class="space-y-4">
                    <?php foreach ($announcements as $an): ?>
                        <div
                            class="section-card p-6 md:p-8 rounded-[2.5rem] flex flex-col md:flex-row gap-6 items-center justify-between">
                            <div class="flex-1">
                                <span
                                    class="px-3 py-1 bg-indigo-500/10 text-indigo-500 text-[10px] font-black tracking-widest uppercase rounded-full mb-3 inline-block"><?= $an['date'] ?></span>
                                <h3 class="text-xl font-bold"><?= htmlspecialchars($an['title']) ?></h3>
                            </div>
                            <form action="" method="POST" class="shrink-0">
                                <input type="hidden" name="id" value="<?= $an['id'] ?>">
                                <button name="delete_announcement"
                                    class="px-6 py-3 rounded-xl bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white font-bold text-xs transition-all flex items-center gap-2">
                                    <i class="bi bi-trash3"></i> Delete
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- 🔔 Section: Modal Alerts -->
            <section id="alerts" class="scroll-mt-32">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-4xl font-black mb-2 tracking-tight">Alert <span
                                class="text-amber-500">Announcements</span></h2>
                        <p class="text-slate-500 text-sm">Pop-up alerts for urgent campus information.</p>
                    </div>
                    <button onclick="toggleForm('alertForm')"
                        class="px-6 py-3 rounded-xl bg-amber-500/10 text-amber-500 font-bold text-xs uppercase tracking-widest hover:bg-amber-500 hover:text-white transition-all">
                        New Alert
                    </button>
                </div>

                <div id="alertForm"
                    class="hidden mb-10 p-8 rounded-[2.5rem] bg-white/5 border border-white/10 animate-fade-in">
                    <form action="" method="POST" class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-slate-500">Alert Title</label>
                            <input type="text" name="title" required
                                class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-4 text-sm focus:border-amber-500 outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-slate-500">Detailed Message</label>
                            <input type="text" name="message" required
                                class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-4 text-sm focus:border-amber-500 outline-none">
                        </div>
                        <button type="submit" name="create_alert"
                            class="md:col-span-2 py-4 bg-amber-500 text-slate-950 font-black rounded-2xl active:scale-95 transition-all">Broadcast
                            Alert</button>
                    </form>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($alerts as $a): ?>
                        <div class="section-card p-6 md:p-10 rounded-[3rem] flex flex-col justify-between min-h-[250px]">
                            <div>
                                <div class="flex items-center gap-2 mb-6">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span class="text-[10px] font-black uppercase text-amber-500 tracking-widest">Active
                                        System Alert</span>
                                </div>
                                <h3 class="text-2xl font-bold mb-3"><?= htmlspecialchars($a['title']) ?></h3>
                                <p class="text-slate-400 text-sm leading-relaxed mb-6 line-clamp-3">
                                    <?= htmlspecialchars($a['message']) ?>
                                </p>
                            </div>
                            <div class="flex items-center justify-between pt-8 border-t border-white/5">
                                <span class="text-[10px] font-bold text-slate-600 uppercase tracking-widest">Issued:
                                    <?= $a['created_at'] ?></span>
                                <form action="" method="POST" onsubmit="return confirm('Kill alert?')">
                                    <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                    <button name="delete_alert"
                                        class="w-12 h-12 rounded-2xl bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all"><i
                                            class="bi bi-trash3 text-lg"></i></button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- 📽️ Section: Slides -->
            <section id="slides" class="scroll-mt-32">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-4xl font-black mb-2 tracking-tight">Main <span
                                class="text-pink-500">Slides</span></h2>
                        <p class="text-slate-500 text-sm">Full-bleed Hero section imagery.</p>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data" class="flex gap-4">
                        <label
                            class="px-6 py-3 rounded-xl bg-pink-500/10 text-pink-500 font-bold text-xs uppercase tracking-widest hover:bg-pink-500 hover:text-white transition-all cursor-pointer">
                            <i class="bi bi-upload mr-2"></i> Add Slide
                            <input type="file" name="image" class="hidden" onchange="this.form.submit()" required>
                        </label>
                        <input type="hidden" name="create_slide">
                    </form>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($slides as $s): ?>
                        <div
                            class="group relative aspect-video rounded-[2.5rem] overflow-hidden bg-slate-900 border border-white/5">
                            <img src="img/slides/<?= $s['image_url'] ?>"
                                class="w-full h-full object-cover grayscale-[0.2] group-hover:grayscale-0 transition-all duration-1000 group-hover:scale-110">
                            <div
                                class="absolute inset-x-0 bottom-0 p-6 bg-gradient-to-t from-slate-950 flex justify-end opacity-0 group-hover:opacity-100 transition-all">
                                <form action="" method="POST">
                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                    <button name="delete_slide"
                                        class="w-14 h-14 rounded-full bg-red-600 text-white shadow-2xl hover:scale-110 active:scale-95 transition-all"><i
                                            class="bi bi-trash3 text-lg"></i></button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- 🗓️ Section: Events -->
            <section id="events" class="scroll-mt-32">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-4xl font-black mb-2 tracking-tight">Upcoming <span
                                class="text-emerald-500">Events</span></h2>
                        <p class="text-slate-500 text-sm">Chronological campus timeline entries.</p>
                    </div>
                    <button onclick="toggleForm('eventForm')"
                        class="px-6 py-3 rounded-xl bg-emerald-500/10 text-emerald-500 font-bold text-xs uppercase tracking-widest hover:bg-emerald-500 hover:text-white transition-all">
                        <i class="bi bi-calendar-check mr-2"></i> Create Event
                    </button>
                </div>

                <div id="eventForm" class="hidden mb-12 p-10 rounded-[3rem] bg-white/5 border border-white/10">
                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-8">
                        <div class="grid md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-black text-slate-500">Title</label>
                                <input type="text" name="title" required
                                    class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-4 text-sm outline-none focus:border-emerald-500">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-black text-slate-500">Event Date</label>
                                <input type="date" name="event_date" required
                                    class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-4 text-sm outline-none focus:border-emerald-500">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-black text-slate-500">Cover Visual</label>
                                <input type="file" name="image" required
                                    class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-3 text-sm outline-none focus:border-emerald-500">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-black text-slate-500">Event Details</label>
                            <textarea name="description" rows="3" required
                                class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-4 text-sm outline-none focus:border-emerald-500"></textarea>
                        </div>
                        <button type="submit" name="create_event"
                            class="w-full py-5 bg-emerald-600 text-white font-black rounded-3xl active:scale-95 transition-all">Commit
                            Event to Timeline</button>
                    </form>
                </div>

                <div class="space-y-8">
                    <?php foreach ($events as $e): ?>
                        <div class="section-card p-6 rounded-[3.5rem] flex flex-col md:flex-row gap-10 items-center">
                            <div class="w-full md:w-56 h-40 shrink-0 rounded-[2.5rem] overflow-hidden shadow-2xl">
                                <img src="../img/<?= $e['image'] ?>" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span
                                        class="px-4 py-1.5 bg-emerald-500/10 text-emerald-500 text-[10px] font-extrabold tracking-widest uppercase rounded-full"><?= $e['event_date'] ?></span>
                                </div>
                                <h3 class="text-2xl font-black mb-2"><?= htmlspecialchars($e['title']) ?></h3>
                                <p class="text-slate-500 text-sm line-clamp-2 leading-relaxed">
                                    <?= htmlspecialchars($e['description']) ?>
                                </p>
                            </div>
                            <form action="" method="POST" class="shrink-0">
                                <input type="hidden" name="id" value="<?= $e['id'] ?>">
                                <button name="delete_event"
                                    class="w-16 h-16 rounded-full bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all transform hover:rotate-12"><i
                                        class="bi bi-trash3 text-xl"></i></button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- 🎬 Section: Videos -->
            <section id="videos" class="scroll-mt-32">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h2 class="text-4xl font-black mb-2 tracking-tight">Video <span
                                class="text-orange-500">Archive</span></h2>
                        <p class="text-slate-500 text-sm">Portrait-optimized video stories.</p>
                    </div>
                    <button onclick="toggleForm('videoForm')"
                        class="px-6 py-3 rounded-xl bg-orange-500/10 text-orange-500 font-bold text-xs uppercase tracking-widest hover:bg-orange-500 hover:text-white transition-all">
                        <i class="bi bi-play-circle mr-2"></i> New Upload
                    </button>
                </div>

                <div id="videoForm" class="hidden mb-12 p-8 rounded-[2.5rem] bg-white/5 border border-white/10">
                    <form action="" method="POST" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-black text-slate-500">Production Title</label>
                            <input type="text" name="title" required
                                class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-4 text-sm outline-none focus:border-orange-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-black text-slate-500">Select MP4 Source</label>
                            <input type="file" name="video" accept="video/mp4" required
                                class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-3 text-sm outline-none focus:border-orange-500">
                        </div>
                        <button type="submit" name="create_video"
                            class="md:col-span-2 py-5 bg-orange-600 text-white font-black rounded-2xl uppercase tracking-widest shadow-xl shadow-orange-500/10">Sync
                            to Video Wall</button>
                    </form>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <?php foreach ($videos as $v): ?>
                        <div class="section-card p-5 rounded-[3.5rem] space-y-6">
                            <div class="relative aspect-[9/16] rounded-[2.5rem] overflow-hidden bg-black shadow-inner">
                                <video class="w-full h-full object-cover" controls>
                                    <source src="../img/<?= $v['video_path'] ?>" type="video/mp4">
                                </video>
                            </div>
                            <div class="flex items-center justify-between px-3">
                                <h4 class="font-extrabold text-sm truncate max-w-[140px]">
                                    <?= htmlspecialchars($v['title']) ?>
                                </h4>
                                <form action="" method="POST">
                                    <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                    <button name="delete_video"
                                        class="text-red-500 hover:text-red-400 font-black text-[10px] uppercase tracking-tighter">Remove</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

        </div>
    </div>

    <!-- 🍞 Toasts -->
    <?php if ($toast): ?>
        <div id="toast" class="fixed bottom-10 right-10 z-[1000] animate-bounce">
            <div
                class="px-8 py-5 rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.5)] flex items-center gap-4 <?= $toast['type'] === 'success' ? 'bg-emerald-600' : 'bg-rose-600' ?> border border-white/10">
                <i
                    class="bi <?= $toast['type'] === 'success' ? 'bi-star-fill' : 'bi-exclamation-octagon-fill' ?> text-white text-xl"></i>
                <p class="text-white font-black text-[11px] uppercase tracking-widest"><?= $toast['message'] ?></p>
            </div>
        </div>
        <script>setTimeout(() => { document.getElementById('toast').style.display = 'none' }, 5000);</script>
    <?php endif; ?>

    <script>
        function scrollToSection(id) {
            document.getElementById(id).scrollIntoView({ behavior: 'smooth' });
        }
        function toggleForm(id) {
            const f = document.getElementById(id);
            f.classList.toggle('hidden');
            if (!f.classList.contains('hidden')) f.scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</body>

</html>
