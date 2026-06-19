<?php
/**
 * E-Kitabghar Database Setup & Permission Fix Script
 * Bypasses psql SSL issues by connecting via PHP PDO (which is already configured on the server).
 */

header('Content-Type: text/plain');

// Ensure we only run from CLI or check security if run via Web
if (php_sapi_name() !== 'cli') {
    // If run via web, require the health check token to prevent unauthorized access
    require_once __DIR__ . '/php/env_loader.php';
    $secretToken = getenv('HEALTH_CHECK_TOKEN');
    if (!isset($_GET['token']) || $_GET['token'] !== $secretToken) {
        header('HTTP/1.1 403 Forbidden');
        die("Error: Unauthorized access. Please run from CLI or supply the correct token parameter.");
    }
}

require_once __DIR__ . '/php/connection.php';

echo "=== E-Kitabghar Database Diagnostics & Setup ===\n\n";

// 1. Diagnostics: Show connection details
try {
    $stmt = $pdo->query("SELECT current_user, current_database(), version()");
    $info = $stmt->fetch();
    echo "Connected User: " . $info['current_user'] . "\n";
    echo "Database Name:  " . $info['current_database'] . "\n";
    echo "PostgreSQL Ver: " . strstr($info['version'], ',', true) . "\n\n";
} catch (PDOException $e) {
    die("Connection diagnostics failed: " . $e->getMessage() . "\n");
}

// 2. Create student_login_logs table
echo "Step 1: Creating 'student_login_logs' table...\n";
try {
    // We include the foreign key constraint referencing student_accounts
    $sqlTable = "CREATE TABLE IF NOT EXISTS student_login_logs (
        id SERIAL PRIMARY KEY,
        student_id INT NOT NULL REFERENCES student_accounts(id) ON DELETE CASCADE,
        ip_address VARCHAR(45),
        login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        location VARCHAR(255)
    );";
    
    $pdo->exec($sqlTable);
    echo "✅ Success: 'student_login_logs' table is ready.\n\n";
} catch (PDOException $e) {
    echo "⚠️ Warning (FK constraint): Failed to create table with foreign key constraint: " . $e->getMessage() . "\n";
    echo "Retrying without foreign key constraint...\n";
    try {
        $sqlTableFallback = "CREATE TABLE IF NOT EXISTS student_login_logs (
            id SERIAL PRIMARY KEY,
            student_id INT NOT NULL,
            ip_address VARCHAR(45),
            login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            location VARCHAR(255)
        );";
        $pdo->exec($sqlTableFallback);
        echo "✅ Success: 'student_login_logs' table created (without FK constraint).\n\n";
    } catch (PDOException $ex) {
        echo "❌ Error: Failed to create table entirely: " . $ex->getMessage() . "\n\n";
    }
}

// 3. Fix Permission Issues (e.g., visitor_count permission denied)
echo "Step 2: Repairing schema permissions...\n";
try {
    $currentUser = $info['current_user'];
    
    // Grant privileges to the current user
    echo "Attempting to grant privileges on all tables/sequences to '$currentUser'...\n";
    $pdo->exec("GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO \"$currentUser\"");
    $pdo->exec("GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO \"$currentUser\"");
    
    // Explicitly grant permissions on visitor_count in case schema-wide fails
    $pdo->exec("GRANT ALL PRIVILEGES ON TABLE visitor_count TO \"$currentUser\"");
    $pdo->exec("GRANT ALL PRIVILEGES ON TABLE student_login_logs TO \"$currentUser\"");
    
    // Set default privileges for future tables
    $pdo->exec("ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO \"$currentUser\"");
    $pdo->exec("ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO \"$currentUser\"");
    
    echo "✅ Success: All database privileges granted and updated for '$currentUser'.\n\n";
} catch (PDOException $e) {
    echo "⚠️ Privilege setup output: " . $e->getMessage() . "\n";
    echo "This can occur if '$currentUser' is not a superuser/owner. If the application works now, this warning can be ignored.\n\n";
}

// 4. List all tables and verify existence
echo "Step 3: Verifying table existence...\n";
try {
    $stmt = $pdo->query("SELECT tablename, tableowner FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename");
    $tables = $stmt->fetchAll();
    echo "Tables in database:\n";
    foreach ($tables as $table) {
        echo "  - " . $table['tablename'] . " (Owner: " . $table['tableowner'] . ")\n";
    }
} catch (PDOException $e) {
    echo "❌ Error listing tables: " . $e->getMessage() . "\n";
}

echo "\n=== Database Fix Complete ===\n";
?>
