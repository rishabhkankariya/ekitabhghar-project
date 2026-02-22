<?php
$servername = "db";
$username   = "ekitabhghar_admin";
$password   = "admin_pass";
$dbname     = "ekitabhghar";

date_default_timezone_set("Asia/Kolkata");

/* ---------- MySQLi Connection ---------- */
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("MySQLi Connection failed: " . $conn->connect_error);
}
$conn->query("SET time_zone = '+05:30'");

/* ---------- PDO Connection ---------- */
try {
    $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("SET time_zone = '+05:30'");
} catch (PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}
