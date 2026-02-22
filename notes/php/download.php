<?php
session_start();
include '../../php/connection.php';

// 1. Privacy Check: Must be logged in as a student
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    http_response_code(403);
    die("Unauthorized Access: Student login required for downloading notes.");
}

// 2. Parameter Validation
if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    die("Invalid Request: Note ID is required.");
}

$note_id = mysqli_real_escape_string($conn, $_GET['id']);

// 3. Fetch Note Details from Database
$query = "SELECT subject_name, notes_link FROM student_notes WHERE id = '$note_id'";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    $file_path = $row['notes_link'];
    $full_path = '../' . $file_path; // notes_link is like 'notes/filename.pdf' relative to the 'notes' dir

    // 4. File Existence Check
    if (file_exists($full_path)) {
        // 5. Serve the File
        $filename = htmlspecialchars($row['subject_name']) . '_Notes.pdf';

        // Clean output buffer
        if (ob_get_level())
            ob_end_clean();

        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($full_path));

        readfile($full_path);
        exit;
    } else {
        http_response_code(404);
        die("Error: File not found on server.");
    }
} else {
    http_response_code(404);
    die("Error: Note record not found.");
}

mysqli_close($conn);
?>
