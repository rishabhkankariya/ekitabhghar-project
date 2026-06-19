<?php
include 'connection.php';

// Note: This file creates MySQL-style tables. For PostgreSQL, use appropriate syntax.
// These CREATE TABLE statements are kept for reference but may need adjustment for PostgreSQL.

$sql = "CREATE TABLE IF NOT EXISTS student_accounts (
    id SERIAL PRIMARY KEY,
    roll_no VARCHAR(50) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(15),
    course VARCHAR(50) NOT NULL DEFAULT 'Diploma',
    admission_year INT NOT NULL,
    expected_passing_year INT NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_temp_password INT DEFAULT 1,
    account_status VARCHAR(20) DEFAULT 'active',
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

try {
    $pdo->exec($sql);
    echo "Table 'student_accounts' created successfully.\n";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}

$sql_admin = "CREATE TABLE IF NOT EXISTS library_admin (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

try {
    $pdo->exec($sql_admin);
    echo "Table 'library_admin' created successfully.\n";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
?>
