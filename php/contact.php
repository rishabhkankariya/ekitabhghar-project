<?php
// --- DB Credentials ---
$host = 'localhost';
$dbname = 'ekitabhghar';
$username = 'root';
$password = '';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php'; // <-- Make sure this is correct path

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Get and sanitize input
        $name = htmlspecialchars(trim($_POST['name']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $message = htmlspecialchars(trim($_POST['message']));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die("Invalid email format.");
        }

        // Store message
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $message]);

        // Send Thanks Email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ekitabghar@gmail.com';  // Your Gmail
            $mail->Password = 'pdfxjcyzffgskypq';   // App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));

            $mail->setFrom('ekitabghar@gmail.com', 'Kitabghar');
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = "Thanks for reaching out to Kitabghar!";

            $mail->Body = '
            <div style="max-width:600px;margin:auto;padding:20px;border:1px solid #eee;border-radius:10px;font-family:Poppins,sans-serif;background:#fefefe;">
                <h2 style="color:#4A90E2;text-align:center;">📬 Thank You, ' . htmlspecialchars($name) . '!</h2>
                <p style="font-size:16px;color:#333;">We have received your message and our team will get back to you shortly.</p>
                <div style="margin:20px 0;padding:15px;background:#f9f9f9;border-left:4px solid #4A90E2;">
                    <strong>Your message:</strong><br>
                    <p style="margin:5px 0;color:#555;">' . nl2br(htmlspecialchars($message)) . '</p>
                </div>
                <p style="font-size:14px;color:#777;">We appreciate you getting in touch with us. If your inquiry is urgent, please email us at <a href="mailto:ekitabghar@gmail.com">ekitabghar@gmail.com</a>.</p>
                <hr style="margin:20px 0;">
                <p style="font-size:13px;text-align:center;color:#aaa;">&copy; ' . date("Y") . ' Kitabghar. All rights reserved.</p>
            </div>
            ';

            $mail->send();
            // Optionally show a success toast on frontend
            header("Location: ../index.php?toast=success");
            exit;

        } catch (Exception $e) {
            // Just insert message, fail silently if email doesn't go
            header("Location: ../index.php?toast=email_fail");
            exit;
        }
    }

} catch (PDOException $e) {
    die("💥 DB Error: " . $e->getMessage());
}
?>
