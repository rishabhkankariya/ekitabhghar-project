<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Database Connection
$servername = "localhost";
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$database = "ekitabhghar";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($conn->real_escape_string($_POST['name'])));
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? $_POST['email'] : null;
    $rating = (int) $_POST['rating'];
    $message = htmlspecialchars(trim($conn->real_escape_string($_POST['message'])));

    if (!$email) {
        echo "<script>alert('Invalid email address!'); window.history.back();</script>";
        exit;
    }

    // Insert into database
    $sql = "INSERT INTO feedback (name, email, rating, message) VALUES ('$name', '$email', '$rating', '$message')";

    if ($conn->query($sql) === TRUE) {
        // Send Email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ekitabghar@gmail.com'; // Your email
            $mail->Password = 'pdfxjcyzffgskypq';  // App Password (Generated from Google)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
            $mail->CharSet = 'UTF-8';

            // Email Headers for better deliverability
            $mail->setFrom('ekitabghar@gmail.com', 'Kitabghar');
            $mail->addReplyTo('support@kitabghar.com', 'Kitabghar Support'); // Custom Reply-To
            $mail->addAddress($email);
            $mail->addCustomHeader("X-Mailer", "PHP/" . phpversion());
            $mail->addCustomHeader("Return-Path", "support@kitabghar.com");

            // Email Content
            $mail->isHTML(true);
            $mail->Subject = 'Thank You for Your Feedback!';
            $mail->Body = "
                <p>Dear <strong>$name</strong>,</p>
                <p>Thank you for your valuable feedback! We appreciate your time and effort in helping us improve our services.</p>
                <p><strong>Your Rating:</strong> $rating/5</p>
                <p><strong>Your Message:</strong> $message</p>
                <hr>
                <p>Best Regards,<br><strong>Kitabghar Team</strong></p>
                <p style='font-size:12px;color:#777;'>This is an automated email. Please do not reply.</p>
            ";

            $mail->send();
            echo "<script>alert('Feedback submitted successfully!'); window.location.href = '../feedback.html';</script>";
        } catch (Exception $e) {
            error_log("Mail Error: " . $mail->ErrorInfo); // Log the error
            echo "<script>alert('Feedback submitted, but email could not be sent.'); window.location.href = '../feedback.html';</script>";
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

