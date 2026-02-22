<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../vendor/autoload.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ekitabhghar";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $action = $_POST['action'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Check if action is send message or send warning
    if ($action === 'message' || $action === 'warning') {
        // Create a PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ekitabghar@gmail.com';  // Replace with actual Gmail address
            $mail->Password = 'pdfxjcyzffgskypq';  // Use the generated App Password, not the actual account password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));

            // Fix for SSL verification issues on local environments
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Sender and recipient
            $mail->setFrom('ekitabghar@gmail.com', 'Kitabghar Admin');
            $mail->addAddress($email);

            // Subject and Body
            $mail->isHTML(true);
            $mail->Subject = $action === 'message' ? 'Your Message from Admin' : 'Warning from Admin';
            $mail->Body = '
    <html>
    <head>
        <style>
            /* General Email Styling */
            body {
                font-family: "Helvetica Neue", Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f7f7f7;
                color: #333333;
            }

            /* Main container */
            .email-container {
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            /* Header Section (Small Heading) */
            .header {
                background-color: #3498db;
                color: white;
                padding: 15px;
                text-align: center;
                border-radius: 8px;
                margin-bottom: 20px;
            }
            .header h1 {
                font-size: 20px;
                margin: 0;
                font-weight: normal;
            }

            /* Content Section */
            .content {
                padding: 15px;
                background-color: #f9f9f9;
                border-radius: 8px;
                box-shadow: 0 1px 5px rgba(0, 0, 0, 0.05);
            }

            .content p {
                font-size: 14px;
                line-height: 1.5;
                color: #555555;
            }

            /* Mobile Responsive */
            @media screen and (max-width: 600px) {
                .email-container {
                    padding: 15px;
                }
                .header h1 {
                    font-size: 18px;
                }
                .content p {
                    font-size: 13px;
                }
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <!-- Header Section with small heading -->
            <div class="header">
                <h1>' . ($action === 'message' ? 'Message from Admin' : 'Warning from Admin') . '</h1>
            </div>

            <!-- Content Section -->
            <div class="content">
                <p>' . nl2br(htmlspecialchars($message)) . '</p>
            </div>
        </div>
    </body>
    </html>
';

            // Send the message
            if (!$mail->send()) {
                throw new Exception("Mailer Error: " . $mail->ErrorInfo);
            }

            $response = ['status' => 'success', 'message' => ucfirst($action) . ' sent successfully!'];
        } catch (Exception $e) {
            $response = ['status' => 'error', 'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Invalid action'];
    }

    // Return response as JSON
    echo json_encode($response);
}
?>
