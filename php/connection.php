<?php
require_once 'env_loader.php';
$servername = getenv('DB_HOST');
$port = getenv('DB_PORT');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');

date_default_timezone_set("Asia/Kolkata");

/* PostgreSQL PDO Connection */
try {
    $dsn = "pgsql:host=$servername;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("PostgreSQL Connection failed: " . $e->getMessage());
}

/* Legacy MySQLi compatibility - Remove this section after migration */
// Note: MySQLi doesn't support PostgreSQL, so we'll use PDO for all database operations
// If you have code using $conn (MySQLi), it needs to be updated to use $pdo instead
$conn = null; // Placeholder to prevent errors during migration
?>