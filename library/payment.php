<?php
session_start();
require('config.php');

if (!isset($_SESSION['download_link'])) {
    die("❌ No book selected!");
}

$amount = 100;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitabghar | Donation Payment</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>

<body>

    <div class="payment-container">
        <h2>🔐 Secure Payment</h2>
        <p>Small Donation! Support us and access your book instantly.</p>

        <form id="payment-form">
            <input type="email" id="email" name="email" placeholder="Enter your email" required
                style="width: 90%; padding: 12px; font-size: 16px;font-family: 'Poppins', sans-serif;  border: 2px solid #ff6ec4; border-radius: 5px; margin-bottom: 15px; outline: none; text-align: center; background: rgba(255, 255, 255, 0.1); color: teal; transition: 0.3s;">
            <button type="button" id="pay-btn">💳 Pay ₹1</button>
        </form>
    </div>

    <script>
        document.getElementById("pay-btn").onclick = function () {
            let email = document.getElementById("email").value;
            if (!email) {
                alert("Please enter your email!");
                return;
            }

            let options = {
                key: "<?= RAZORPAY_KEY_ID ?>",
                amount: "<?= $amount ?>",
                currency: "INR",
                name: "Kitabghar",
                image: "../img/kitabghar.png",
                description: "Support Us",
                prefill: { email: email },
                handler: function (response) {
                    window.location.href = "verify_payment.php?payment_id=" + response.razorpay_payment_id + "&email=" + encodeURIComponent(email);
                }
            };

            let rzp = new Razorpay(options);
            rzp.open();
        };
        document.getElementById("email").addEventListener("focus", function () {
            this.style.borderColor = "#ff3ca6";
            this.style.boxShadow = "0 0 8px rgba(255, 110, 196, 0.7)";
        });

        document.getElementById("email").addEventListener("blur", function () {
            this.style.borderColor = "#ff6ec4";
            this.style.boxShadow = "none";
        });
    </script>

</body>

</html>
