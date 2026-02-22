<?php
// Marquee Announcement (imp_announcements)
try {
    $stmt = $pdo->query("SELECT message FROM imp_announcements WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1");
    $marquee_announcement = $stmt->fetch(PDO::FETCH_ASSOC)['message'] ?? '';
} catch (PDOException $e) {
    $_SESSION['toast'] = [
        'type' => 'error',
        'message' => 'Failed to fetch marquee announcement: ' . $e->getMessage()
    ];
    $marquee_announcement = '';
}
// Announcements
try {
    $stmt = $pdo->query("SELECT * FROM announcements");
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['toast'] = [
        'type' => 'error',
        'message' => 'Failed to load announcements: ' . $e->getMessage()
    ];
}

// Events, Gallery, Videos
try {
    $stmt = $pdo->query("SELECT * FROM events ORDER BY event_date DESC");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $query = $pdo->query("SELECT * FROM gallery ORDER BY id DESC");
    $gallery = $query->fetchAll(PDO::FETCH_ASSOC);

    $query = $pdo->query("SELECT * FROM videos ORDER BY id DESC");
    $videos = $query->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $_SESSION['toast'] = [
        'type' => 'error',
        'message' => 'Failed to load events/gallery/videos: ' . $e->getMessage()
    ];
}

// Modal Announcements
try {
    $query = $pdo->query("SELECT title, message FROM modal_announcement ORDER BY id DESC LIMIT 5");
    $modal_announcements = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['toast'] = [
        'type' => 'error',
        'message' => 'Failed to fetch modal announcements: ' . $e->getMessage()
    ];
}

// Slides
try {
    $stmt = $pdo->query("SELECT image_url FROM slides");
    $slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['toast'] = [
        'type' => 'error',
        'message' => 'Failed to fetch slides: ' . $e->getMessage()
    ];
}

// Visitor Count
try {
    $query = $pdo->query("SELECT count FROM visitor_count WHERE id = 1");
    $visitor = $query->fetch();
    $visitor_count = $visitor['count'];
} catch (PDOException $e) {
    $_SESSION['toast'] = [
        'type' => 'error',
        'message' => 'Failed to fetch visitor count: ' . $e->getMessage()
    ];
}
?>
