<?php
// DB Connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "ekitabhghar";

$conn = new mysqli($host, $user, $pass, $db);

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $studentName = mysqli_real_escape_string($conn, $_POST['student_name']);
    $notesTitle = mysqli_real_escape_string($conn, $_POST['note_title']);
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);

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
            $stmt = $conn->prepare("INSERT INTO contributed_notes (student_name, notes_title, semester, file_name) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $studentName, $notesTitle, $semester, $fileName);

            if ($stmt->execute()) {
                echo "<script>
                        window.location.href = '../index.php';
                </script>";

            } else {
                echo "<script>alert('❌ Failed to insert into database');</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('❌ File upload failed');</script>";
        }
    } else {
        echo "<script>alert('❗ Please upload a valid note file');</script>";
    }

    $conn->close();
}
?>
