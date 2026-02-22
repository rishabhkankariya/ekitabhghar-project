<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["captcha"]) || !isset($_SESSION["captcha"])) {
        die("CAPTCHA validation failed.");
    }

    // Check if CAPTCHA is correct
    if (strtoupper(trim($_POST["captcha"])) !== $_SESSION["captcha"]) {
        die("Incorrect CAPTCHA. Please try again.");
        unset($_SESSION["captcha"]);
    }

    // One-time CAPTCHA use
    unset($_SESSION["captcha"]);

    // Time-based security
    if (isset($_SESSION["captcha_time"]) && (time() - $_SESSION["captcha_time"]) > 240) { 
        die("CAPTCHA expired. Try again.");
    }

    // Process form data
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);

    echo "Form submitted successfully!<br>";
    echo "Name: $name <br>";
    echo "Email: $email";
}
?>
