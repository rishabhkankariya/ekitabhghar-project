<?php
session_start();
require_once 'connection.php';

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
header('Content-Type: application/json');

// Check if the user is coming from the registration process
if (!isset($_SESSION['temp_user'])) {
    echo json_encode(["success" => false, "message" => "⚠ Unauthorized access. Redirecting...", "redirect" => "../student_sign.html"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if OTP is set and not expired
    if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_expiry']) || time() > $_SESSION['otp_expiry']) {
        echo json_encode(["success" => false, "message" => "⏳ OTP expired. Please sign up again.", "redirect" => "../student_sign.html"]);
        exit;
    }

    $userOtp = trim($_POST['otp']);

    // Verify OTP
    if ($userOtp != $_SESSION['otp']) {
        echo json_encode(["success" => false, "message" => "❌ Invalid OTP."]);
        exit;
    }

    // Insert user data into database after OTP verification
    if (isset($_SESSION['temp_user'])) {
        $username = $_SESSION['temp_user']['username'];
        $email = $_SESSION['temp_user']['email'];
        $hashedPassword = $_SESSION['temp_user']['password'];

        $stmt = $conn->prepare("INSERT INTO users (username, email, password, email_verified) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);
        if ($stmt->execute()) {
            unset($_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['temp_user']);
            // Send Verification Success Email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'ekitabghar@gmail.com';
                $mail->Password = 'pdfxjcyzffgskypq';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));

                $mail->setFrom('ekitabghar@gmail.com', 'Kitabghar');
                $mail->addAddress($email, $username);
                $mail->isHTML(true);
                $mail->Subject = 'Account Verified Successfully - Welcome to Kitabghar!';

                $mail->Body = "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Account Verification - Kitabghar</title><style>body{font-family:'Arial',sans-serif;background-color:#f9f9f9;color:#333;margin:0;padding:0;display:flex;justify-content:center;align-items:center;height:100vh}.container{background-color:#ffffff;width:100%;max-width:600px;margin:40px auto;padding:30px;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,0.1);text-align:center}h2{color:#4CAF50;font-size:32px;margin-bottom:10px;text-transform:uppercase}p{font-size:16px;line-height:1.5;color:#555;margin-bottom:25px}.message{font-size:20px;font-weight:bold;color:#2196F3;background:#f1f8ff;padding:15px;border-radius:8px;display:inline-block;margin-top:10px}.footer{font-size:12px;color:#888;margin-top:40px}.footer a{color:#4CAF50;text-decoration:none}.footer a:hover{text-decoration:underline}@media(max-width:600px){h2{font-size:28px}.message{font-size:18px;padding:12px}p{font-size:14px}.container{padding:20px}.footer p{font-size:12px}}</style></head><body><div class='container'><h2> Congratulations, $username! <img src='https://media-hosting.imagekit.io/1e070437351a4f41/party-popper.png?Expires=1839052097&Key-Pair-Id=K2ZIVPTIP2VGHC&Signature=jhVwbXl0ubGy8YuOJ79Za-Kzz7I1tPxpGXkVrJ4i2r6auAHbKQjHVO6RrWLl1PyeSYgejxEAjWjTOnNQMqq05sgUL-JDS14DBElPzHHpiS98sOB60rQyEwGVCMp-ZFRTvLeTwZDJVpn-MqHfb89RuVgzaTHLZ5rmwLSoE9dD5n2v-I7WoJMvHcpLZVmX8taWz14Fced1whZR~KfleQT3Uy6dJygc8yYKtyo9VR2cEa8TYGyO6tF26wjAAVFgclKp96fes8X5UEgOXb0qWx3jLHWT7duL9mzWDlevG11obLTzEE0oLLpIUMkYQ9hP~esR~SZY2ygZvGCd85a6zb1P9Q__' alt='Party Popper' style='width:30px;vertical-align:middle;margin-left:10px;'></h2><p>Your account on <strong>Kitabghar</strong> has been successfully verified.</p><div class='message'> Your Account is Now Active & Ready to Use! <img src='https://media-hosting.imagekit.io/c3b40bb67a554514/check-mark.png?Expires=1839052097&Key-Pair-Id=K2ZIVPTIP2VGHC&Signature=QbrcuyJE5n9K2hCaODMkch7MJN7Zoz-PKoMPAzMN2TqTHu~-0jL-13~Q0xQieXEKiEtJYuQIOfs~JtyrJSYvSnkKmy2pA5LsyrIiS-oBbI-DY1Eemsi~ZZFNTI-0l2p2aeVUdoLkMydZPmvBOWM5e2bpuWU83VsLLen6hg5BBqHrrknDLMypWL3U0gih4sI5dRyURcqZkAxjFqbRE--9uv4tBjRPaNgYYVRaWVLpw0UlpgMXLu~isofoLQIibx8znMyWnEBub5hffIVKQIEdZjUbN4AUGNLkX8MJi3V8P4aFdUFGiA9OEuKiXF~hQuuagN5~Q7N5cjhJmrS-Sva4ig__' alt='Check Mark' style='width:30px;vertical-align:middle;margin-left:10px;'></div><p>You can now log in and explore all the amazing features we have to offer.</p><p>Thank you for joining us!<br><strong>Kitabghar Team</strong></p></div></body></html>";
                $mail->send();
                echo json_encode(["success" => true, "message" => "Email verified & account created!", "redirect" => "student_login.html"]);
            } catch (Exception $e) {
                error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
            }
            exit;
        } else {
            echo json_encode(["success" => false, "message" => "⚠ Database error."]);
            exit;
        }
    } else {
        echo json_encode(["success" => false, "message" => "⚠ Session expired. Please register again.", "redirect" => "student_sign.html"]);
        exit;
    }
}
?>

