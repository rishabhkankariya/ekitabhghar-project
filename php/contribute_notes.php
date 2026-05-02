<?php
require_once 'connection.php';

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $studentName = trim($_POST['student_name']);
    $notesTitle = trim($_POST['note_title']);
    $semester = trim($_POST['semester']);

    // File Upload
    if (isset($_FILES['note_file']) && $_FILES['note_file']['error'] === 0) {
        $fileTmp = $_FILES['note_file']['tmp_name'];
        $fileName = time() . "_" . basename($_FILES['note_file']['name']);
        $uploadDir = "uploads/notes/";

        // Create dir if not exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmp, $targetPath)) {
            // Insert into DB
            $stmt = $pdo->prepare("INSERT INTO contributed_notes (student_name, notes_title, semester, file_name) VALUES (?, ?, ?, ?)");

            if ($stmt->execute([$studentName, $notesTitle, $semester, $fileName])) {
                echo "<script>
                        window.location.href = '../index.php';
                </script>";
            } else {
                echo "<script>alert('❌ Failed to insert into database');</script>";
            }
        } else {
            echo "<script>alert('❌ File upload failed');</script>";
        }
    } else {
        echo "<script>alert('❗ Please upload a valid note file');</script>";
    }
}
?>
