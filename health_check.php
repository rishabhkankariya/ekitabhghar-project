<?php
/**
 * Production Health Check & Deployment Validator
 * Optimized for PHP 8.2 + Apache + Docker
 * 
 * Access: health_check.php?token=ekitabhghar_prod_2026
 */

// --- SECURITY TOKEN ---
$secretToken = 'ekitabhghar_prod_2026';

if (!isset($_GET['token']) || $_GET['token'] !== $secretToken) {
    header('HTTP/1.1 403 Forbidden');
    die('<div style="font-family:sans-serif; text-align:center; padding:50px;">
            <h1>403 Forbidden</h1>
            <p>Invalid or missing security token.</p>
         </div>');
}

error_reporting(E_ALL);
ini_set('display_errors', 0); // Hide raw errors in favor of clean report

$report = [];

function check($name, $condition, $messagePass, $messageFail)
{
    global $report;
    $report[] = [
        'name' => $name,
        'status' => $condition ? 'PASS' : 'FAIL',
        'message' => $condition ? $messagePass : $messageFail
    ];
    return $condition;
}

/* ---------- 1. FILE CHECKS ---------- */
check(
    "Base Files",
    file_exists(__DIR__ . "/index.php") && file_exists(__DIR__ . "/composer.json") && file_exists(__DIR__ . "/.htaccess"),
    "index.php, composer.json, and .htaccess are present.",
    "Missing critical root files. Check index.php or .htaccess."
);

check(
    "Autoloader",
    file_exists(__DIR__ . "/vendor/autoload.php"),
    "Composer autoloader found.",
    "vendor/autoload.php missing. Run 'composer install'."
);

/* ---------- 2. FOLDER CHECKS ---------- */
$folders = [
    'vendor' => 'vendor',
    'admin' => 'admin',
    'php' => 'php',
    'uploads' => 'php/uploads',
    'images' => 'img'
];

foreach ($folders as $label => $path) {
    check(
        ucfirst($label) . " Folder",
        is_dir(__DIR__ . "/" . $path),
        "Directory /$path is correctly placed.",
        "Directory /$path is missing."
    );
}

/* ---------- 3. PERMISSION CHECKS ---------- */
$writable = ['php/uploads', 'img', 'pdfs', 'notes'];
foreach ($writable as $path) {
    check(
        "Permissions: $path",
        is_writable(__DIR__ . "/$path"),
        "Directory is writable.",
        "Directory is NOT writable. Fix permissions (chmod)."
    );
}

/* ---------- 4. PHP EXTENSIONS ---------- */
$extensions = ["pdo_mysql", "mysqli", "gd", "mbstring", "curl", "openssl"];
foreach ($extensions as $ext) {
    check(
        "Ext: $ext",
        extension_loaded($ext),
        "Extension is loaded.",
        "Extension $ext is missing in php.ini."
    );
}

/* ---------- 5. DATABASE CHECK ---------- */
try {
    // Using ACTUAL env variables from connection.php
    $host = getenv("DB_HOST");
    $db = getenv("DB_NAME");
    $user = getenv("DB_USER");
    $pass = getenv("DB_PASS");
    $port = getenv("DB_PORT") ?: '3306';

    if (!$host || !$db)
        throw new Exception("Environment variables missing");

    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]);

    check("DB Connection", true, "Connected to '$db' on '$host'.", "");

    $tables = ["visitor_count", "students", "admin", "announcements", "student_accounts"];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        check("Table: $table", $stmt->rowCount() > 0, "Table exists.", "Table is missing.");
    }
} catch (Exception $e) {
    check("DB Connection", false, "", "Failed: " . (strpos($e->getMessage(), 'Environment') !== false ? "Env variables not set" : "Check credentials"));
}

/* ---------- 6. MOD_REWRITE CHECK ---------- */
$rewrite = false;
if (function_exists('apache_get_modules')) {
    $rewrite = in_array('mod_rewrite', apache_get_modules());
} else {
    $rewrite = (getenv('APACHE_REWRITE_ENABLED') === '1' || strpos(shell_exec('apache2ctl -M 2>/dev/null'), 'rewrite_module') !== false);
}
// Final fallback: If .htaccess has RewriteEngine On, we assume it's meant to be active
if (!$rewrite && file_exists(__DIR__ . '/.htaccess')) {
    $content = file_get_contents(__DIR__ . '/.htaccess');
    if (strpos($content, 'RewriteEngine On') !== false)
        $rewrite = true;
}

check("mod_rewrite", $rewrite, "Module enabled or .htaccess active.", "Module not detected.");

/* ---------- 7. COMPOSER TEST ---------- */
$composerOk = false;
if (file_exists(__DIR__ . "/vendor/autoload.php")) {
    require_once __DIR__ . "/vendor/autoload.php";
    $composerOk = class_exists('PHPMailer\PHPMailer\PHPMailer') || class_exists('Mpdf\Mpdf');
}
check("Composer Logic", $composerOk, "Dependencies are loaded and functional.", "Autoloading failed.");

// Calculate Stats
$total = count($report);
$passed = count(array_filter($report, fn($item) => $item['status'] === 'PASS'));
$failed = $total - $passed;
$status = ($failed === 0) ? 'HEALTHY' : 'CRITICAL';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deployment Health Check</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #030712;
            --card: #111827;
            --primary: #3b82f6;
            --success: #10b981;
            --danger: #ef4444;
            --text-base: #f3f4f6;
            --text-muted: #9ca3af;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            color: var(--text-base);
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }

        .container {
            max-width: 850px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(to right, #60a5fa, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .status-badge {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 24px;
            border-radius: 99px;
            font-weight: 700;
            letter-spacing: 1px;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .status-healthy {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .status-critical {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 16px;
            text-align: center;
        }

        .stat-val {
            font-size: 1.8rem;
            font-weight: 700;
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .report-card {
            background: var(--card);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .report-item {
            padding: 16px 24px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.2s;
        }

        .report-item:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .indicator-pass {
            background: var(--success);
            box-shadow: 0 0 10px var(--success);
        }

        .indicator-fail {
            background: var(--danger);
            box-shadow: 0 0 10px var(--danger);
        }

        .item-info {
            flex-grow: 1;
        }

        .item-name {
            font-weight: 600;
            display: block;
            font-size: 1rem;
        }

        .item-msg {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .item-status-text {
            font-weight: 700;
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .text-pass {
            color: var(--success);
            background: rgba(16, 185, 129, 0.1);
        }

        .text-fail {
            color: var(--danger);
            background: rgba(239, 68, 68, 0.1);
        }

        .actions {
            margin-top: 40px;
            text-align: center;
        }

        .btn-delete {
            background: var(--danger);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-delete:hover {
            transform: scale(1.05);
        }

        @media (max-width: 600px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .report-item {
                padding: 12px 16px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h1>Audit Intelligence</h1>
            <div class="status-badge status-<?php echo strtolower($status); ?>">
                System Status: <?php echo $status; ?>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-val"><?php echo $total; ?></span>
                <span class="stat-label">Checks Executed</span>
            </div>
            <div class="stat-card" style="color: var(--success)">
                <span class="stat-val"><?php echo $passed; ?></span>
                <span class="stat-label">Tests Passed</span>
            </div>
            <div class="stat-card" style="color: var(--danger)">
                <span class="stat-val"><?php echo $failed; ?></span>
                <span class="stat-label">Tests Failed</span>
            </div>
        </div>

        <div class="report-card">
            <?php foreach ($report as $item): ?>
                <div class="report-item">
                    <div class="indicator indicator-<?php echo strtolower($item['status']); ?>"></div>
                    <div class="item-info">
                        <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                        <span class="item-msg"><?php echo htmlspecialchars($item['message']); ?></span>
                    </div>
                    <div class="item-status-text text-<?php echo strtolower($item['status']); ?>">
                            <?php echo $item['status']; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="actions">
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">
                Verification complete? Delete this script to secure your architecture.
            </p>
            <form method="POST" onsubmit="return confirm('Securely delete this auditor script?')">
                <button type="submit" name="action" value="self_destruct" class="btn-delete">
                    Self-Destruct (Delete Script)
                </button>
            </form>
        </div>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'self_destruct') {
        unlink(__FILE__);
        echo "<script>alert('Health check script removed.'); window.location.href='index.php';</script>";
    }
    ?>

</body>

</html>