<?php
session_start();

if (!isset($_SESSION['download_link']) || !isset($_SESSION['receipt'])) {
    die("❌ Unauthorized Access!");
}

$book_url = $_SESSION['download_link'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="manifest" href="../site.webmanifest">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Receipt & Book</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="download-container">
        <h2>✅ Payment Successful</h2>
        <p>Thank you for your support! You Can Download Your  Book Below.</p>
        <a href="<?= $book_url ?>" class="download-btn">📚 Download Book</a>
    </div>

</body>
</html>
