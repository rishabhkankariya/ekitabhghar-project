<?php
// Ensure this script can only be run via CLI or by admin
if (php_sapi_name() !== 'cli') {
    session_start();
    if (!isset($_SESSION['admin_logged_in']) && !isset($_SESSION['admin_id'])) {
        die("Access denied.");
    }
}

// Disable time limit for background sending
set_time_limit(0);

require_once __DIR__ . '/../php/connection.php';
require_once __DIR__ . '/send_mail.php';

// Try to acquire an advisory lock to prevent concurrent runs in PostgreSQL
try {
    $stmt = $pdo->query("SELECT pg_try_advisory_lock(987654)");
    $lockAcquired = $stmt->fetchColumn();
    if (!$lockAcquired) {
        // Lock already held by another process
        exit("Another email sender instance is already running.\n");
    }
} catch (PDOException $e) {
    // If pg_try_advisory_lock is not supported, proceed using status checks
}

// Generate unique session token for this process run
$sessionToken = uniqid('email_proc_', true);

// Select pending or retryable failed emails and mark them under this process session
$updateStmt = $pdo->prepare("UPDATE email_queue SET status = 'sending', error_message = ? WHERE status = 'pending' OR (status = 'failed' AND attempts < 3)");
$updateStmt->execute([$sessionToken]);

// Fetch the emails marked by this process session
$fetchStmt = $pdo->prepare("SELECT * FROM email_queue WHERE status = 'sending' AND error_message = ? ORDER BY id ASC");
$fetchStmt->execute([$sessionToken]);
$queuedEmails = $fetchStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($queuedEmails as $emailRow) {
    $id = $emailRow['id'];
    $toEmail = $emailRow['to_email'];
    $toName = $emailRow['to_name'];
    $subject = $emailRow['subject'];
    $body = $emailRow['body'];
    $altBody = $emailRow['alt_body'] ?? '';
    $bcc = json_decode($emailRow['bcc'] ?? '[]', true) ?: [];
    $attachments = json_decode($emailRow['attachments'] ?? '[]', true) ?: [];

    // Call the actual synchronous SMTP sending function
    $res = sendEmailReal($toEmail, $toName, $subject, $body, $altBody, $bcc, $attachments);

    if ($res === true) {
        // Successfully sent
        $successStmt = $pdo->prepare("UPDATE email_queue SET status = 'sent', error_message = NULL, sent_at = CURRENT_TIMESTAMP WHERE id = ?");
        $successStmt->execute([$id]);

        // Clean up persistent queue attachments
        foreach ($attachments as $file) {
            $filePath = is_array($file) ? ($file['path'] ?? '') : $file;
            if ($filePath && strpos($filePath, 'email_attachments') !== false && file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    } else {
        // Failed sending attempt - log failure and retry later
        $attempts = $emailRow['attempts'] + 1;
        $status = ($attempts >= 3) ? 'failed_permanently' : 'failed';
        $failStmt = $pdo->prepare("UPDATE email_queue SET status = ?, attempts = ?, error_message = ? WHERE id = ?");
        $failStmt->execute([$status, $attempts, substr($res, 0, 500), $id]);
    }
}

// Release PostgreSQL advisory lock
try {
    $pdo->exec("SELECT pg_advisory_unlock(987654)");
} catch (PDOException $e) {}
?>
