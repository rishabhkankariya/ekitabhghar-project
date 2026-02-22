<?php
include 'connection.php';

$sql = "CREATE TABLE IF NOT EXISTS `student_accounts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `roll_no` VARCHAR(50) NOT NULL UNIQUE,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `phone_number` VARCHAR(15),
    `course` VARCHAR(50) NOT NULL DEFAULT 'Diploma',
    `admission_year` INT NOT NULL,
    `expected_passing_year` INT NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `is_temp_password` BOOLEAN DEFAULT TRUE,
    `account_status` ENUM('active', 'completed', 'blocked', 'backlog') DEFAULT 'active',
    `last_login_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_status` (`account_status`)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'student_accounts' created successfully.\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}

$sql_admin = "CREATE TABLE IF NOT EXISTS `library_admin` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_admin) === TRUE) {
    echo "Table 'library_admin' created successfully.\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}

$conn->close();
?>
