<?php

require_once '../config/send_mail.php';
require_once 'connection.php';

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
        $subject = 'Thank You for Your Feedback!';
        $body = "
            <p>Dear <strong>$name</strong>,</p>
            <p>Thank you for your valuable feedback! We appreciate your time and effort in helping us improve our services.</p>
            <p><strong>Your Rating:</strong> $rating/5</p>
            <p><strong>Your Message:</strong> $message</p>
            <hr>
            <p>Best Regards,<br><strong>Kitabghar Team</strong></p>
            <p style='font-size:12px;color:#777;'>This is an automated email. Please do not reply.</p>";

        $res = sendEmail($email, $name, $subject, $body);
        if ($res === true) {
            echo "<script>alert('Feedback submitted successfully!'); window.location.href = '../feedback.html';</script>";
        } else {
            echo "<script>alert('Feedback submitted, but email could not be sent.'); window.location.href = '../feedback.html';</script>";
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>