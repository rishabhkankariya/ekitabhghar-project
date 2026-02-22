<?php
// Start Session
session_start();
require('config.php');
require('../admin/tcpdf/tcpdf.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

// Validate Inputs
if (!isset($_GET['payment_id']) || !isset($_GET['email'])) {
    die("❌ Invalid request! Payment ID and Email are required.");
}

$payment_id = htmlspecialchars($_GET['payment_id']);
$email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);

// === [1] VERIFY PAYMENT FROM RAZORPAY === //
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/payments/" . $payment_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ":" . RAZORPAY_SECRET);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if ($data['status'] == "authorized") {
    $capture_url = "https://api.razorpay.com/v1/payments/$payment_id/capture";
    $capture_data = json_encode(["amount" => $data['amount'], "currency" => "INR"]);

    // === [2] CAPTURE PAYMENT === //
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $capture_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ":" . RAZORPAY_SECRET);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $capture_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $capture_response = curl_exec($ch);
    curl_close($ch);

    $capture_result = json_decode($capture_response, true);

    if ($capture_result['status'] == "captured") {

        // === [3] CREATE RECEIPT DIRECTORY IF NOT EXISTS === //
        $receipt_dir = __DIR__ . "/../library/receipts/";
        if (!is_dir($receipt_dir)) {
            mkdir($receipt_dir, 0777, true);
        }

        // Save PDF Path
        $receipt_path = $receipt_dir . "receipt_$payment_id.pdf";
        $signature_path = __DIR__ . "/../library/receipts/signature.png";

        // === [4] GENERATE PDF RECEIPT === //
        $pdf = new TCPDF();
        $pdf->SetCreator("Kitabghar");
        $pdf->SetTitle("Payment Receipt");
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        // Header
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor(0, 102, 204);
        $pdf->Cell(0, 10, "KITABGHAR PAYMENT RECEIPT", 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(5);

        // Payment Details
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, "Payment ID: $payment_id", 0, 1);
        $pdf->Cell(0, 10, "Email: $email", 0, 1);
        $pdf->Cell(0, 10, "Amount Paid: 1 Rs", 0, 1);
        $pdf->Cell(0, 10, "Thank you for supporting Kitabghar!", 0, 1);
        $pdf->Ln(10);

        // === [5] GENERATE QR CODE IN PDF === //
        $qr_code_data = "Payment ID: $payment_id\nAmount: 1 Rs\nEmail: $email";
        $pdf->write2DBarcode($qr_code_data, 'QRCODE,H', 75, 90, 50, 50, [], 'N');

        // === [6] ADD SIGNATURE IMAGE === //
        if (file_exists($signature_path)) {
            $pdf->Ln(50); // Reduce space to prevent overlap
            $pdf->SetFont('helvetica', 'I', 12); // Reduce font size for better alignment
            $pdf->Cell(0, 10, "Authorized Sign:", 0, 1, 'R');

            // Adjust the image position & size for better alignment
            $x_position = 150; // Move image slightly left if needed
            $y_position = $pdf->GetY() + 5; // Adjust Y to align properly below text
            $width = 40; // Reduce width
            $height = 15; // Reduce height

            $pdf->Image($signature_path, $x_position, $y_position, $width, $height, 'PNG');
        }


        // Save PDF
        $pdf->Output($receipt_path, "F");

        // === [7] SEND RECEIPT VIA EMAIL === //
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ekitabghar@gmail.com';
        $mail->Password = 'pdfxjcyzffgskypq';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));

        $mail->setFrom('ekitabghar@gmail.com', 'Kitabghar');
        $mail->addAddress($email);
        $mail->Subject = "Your Payment Receipt - Kitabghar";
        $mail->Body = "Thank you for your support! Please find your receipt attached.";
        $mail->addAttachment($receipt_path);

        // Log Email Errors
        if (!$mail->send()) {
            error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }

        // === [8] STORE RECEIPT IN SESSION & REDIRECT === //
        $_SESSION['receipt'] = $receipt_path;
        header("Location: download.php");
        exit();

    } else {
        die("❌ Payment Capture Failed!");
    }
} else {
    die("❌ Payment verification failed!");
}
?>

