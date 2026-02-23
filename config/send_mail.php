<?php
// [TESTING MODE] Email sending completely disabled.
// All calls to sendEmail() will return true without sending anything.
// To re-enable email, restore the original send_mail.php with PHPMailer logic.

/**
 * Stub email function - always returns true (no email sent).
 * 
 * @param string $toEmail   Recipient email
 * @param string $toName    Recipient name
 * @param string $subject   Email subject
 * @param string $htmlBody  HTML content of the email
 * @param string $altBody   (Optional) Plain text version
 * @param array  $bcc       (Optional) BCC recipients
 * @param array  $attachments (Optional) File attachments
 * @return bool  Always returns true
 */
function sendEmail($toEmail, $toName, $subject, $htmlBody, $altBody = '', $bcc = [], $attachments = [])
{
    // Log for debugging (optional)
    error_log("[TEST MODE] sendEmail called - To: $toEmail | Subject: $subject (NOT SENT)");
    return true;
}
