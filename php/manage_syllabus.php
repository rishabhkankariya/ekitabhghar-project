<?php
session_start();
include 'connection.php'; // Database connection

header('Content-Type: application/json');

$action = $_POST['action'] ?? 'fetch';

if ($action === "fetch") {
    $query = "SELECT id, year, semester, subject_name, pdf_path 
              FROM syllabus 
              ORDER BY CAST(year AS UNSIGNED) ASC, CAST(semester AS UNSIGNED) ASC";
    $result = $conn->query($query);

    $syllabusData = [];
    while ($row = $result->fetch_assoc()) {
        $syllabusData[] = [
            'id' => $row['id'],
            'year' => $row['year'],
            'semester' => $row['semester'],
            'subject_name' => $row['subject_name'],
            'pdf' => '/website/' . htmlspecialchars($row['pdf_path'])
        ];
    }

    echo json_encode(["status" => "success", "data" => $syllabusData]);
    exit;
}

// Fetch distinct Years & Semesters for dropdowns
elseif ($action === "fetch_years_semesters") {
    $years = [];
    $semesters = [];

    // Fetch distinct years in ascending order
    $result = $conn->query("SELECT DISTINCT year FROM syllabus ORDER BY year ASC");

    while ($row = $result->fetch_assoc()) {
        $years[] = $row['year'];
    }

    $result = $conn->query("SELECT DISTINCT semester FROM syllabus ORDER BY semester ASC");
    while ($row = $result->fetch_assoc()) {
        $semesters[] = $row['semester'];
    }

    echo json_encode(["status" => "success", "years" => $years, "semesters" => $semesters]);
    exit;
}

// Add new syllabus
elseif ($action === "add") {
    $year = $_POST['year'] ?? '';
    $semester = $_POST['semester'] ?? '';
    $subjectName = trim($_POST['subject_name'] ?? '');

    if (empty($year) || empty($semester) || empty($subjectName)) {
        echo json_encode(["status" => "error", "message" => "All fields are required!"]);
        exit;
    }

    if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["status" => "error", "message" => "File upload error!"]);
        exit;
    }

    // Use absolute path to server-side folder
    $uploadDir = dirname(__DIR__) . "/pdfs/";
    $webPathPrefix = "pdfs/";

    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            echo json_encode(["status" => "error", "message" => "Failed to create upload directory!"]);
            exit;
        }
    }

    $pdfName = time() . "_" . preg_replace("/[^a-zA-Z0-9_.-]/", "_", basename($_FILES["pdf"]["name"]));
    $serverPath = $uploadDir . $pdfName;
    $webPath = $webPathPrefix . $pdfName;

    if (!move_uploaded_file($_FILES["pdf"]["tmp_name"], $serverPath)) {
        echo json_encode(["status" => "error", "message" => "Failed to upload PDF!"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO syllabus (year, semester, subject_name, pdf_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $year, $semester, $subjectName, $webPath);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Syllabus added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
    }

    $stmt->close();
    exit;
}

// Update syllabus
elseif ($action === "update") {
    $id = $_POST['id'] ?? null;
    $newSubject = trim($_POST['subject_name'] ?? '');

    if (!$id || !is_numeric($id)) {
        echo json_encode(["status" => "error", "message" => "Invalid ID"]);
        exit;
    }

    if (empty($newSubject)) {
        echo json_encode(["status" => "error", "message" => "Subject name cannot be empty"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT pdf_path FROM syllabus WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($currentPdfPath);
    $stmt->fetch();
    $stmt->close();

    $newPdfPath = $currentPdfPath;

    $uploadDir = dirname(__DIR__) . "/pdfs/";
    $webPathPrefix = "pdfs/";

    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            echo json_encode(["status" => "error", "message" => "Failed to create upload directory!"]);
            exit;
        }
    }

    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $pdfName = time() . "_" . preg_replace("/[^a-zA-Z0-9_.-]/", "_", basename($_FILES["pdf"]["name"]));
        $serverPath = $uploadDir . $pdfName;
        $newPdfPath = $webPathPrefix . $pdfName;

        if (!move_uploaded_file($_FILES["pdf"]["tmp_name"], $serverPath)) {
            echo json_encode(["status" => "error", "message" => "Failed to upload new PDF!"]);
            exit;
        }

        // Delete old file
        $oldServerPath = dirname(__DIR__) . $currentPdfPath;
        if (!empty($currentPdfPath) && file_exists($oldServerPath)) {
            unlink($oldServerPath);
        }
    }

    $stmt = $conn->prepare("UPDATE syllabus SET subject_name = ?, pdf_path = ? WHERE id = ?");
    $stmt->bind_param("ssi", $newSubject, $newPdfPath, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Syllabus updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
    }

    $stmt->close();
    exit;
}



// Delete syllabus
elseif ($action === "delete") {
    $id = $_POST['id'] ?? null;

    if (!$id || !is_numeric($id)) {
        echo json_encode(["status" => "error", "message" => "Invalid ID"]);
        exit();
    }

    // Fetch file path
    $stmt = $conn->prepare("SELECT pdf_path FROM syllabus WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($pdfPath);
    $stmt->fetch();
    $stmt->close();

    // Check if file exists before attempting to delete
    if (!empty($pdfPath)) {
        $fullPdfPath = __DIR__ . '/../pdfs/' . basename($pdfPath);
        if (file_exists($fullPdfPath) && is_writable($fullPdfPath)) {
            unlink($fullPdfPath);
        }
    }

    // Delete from database
    $stmt = $conn->prepare("DELETE FROM syllabus WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Syllabus deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
    }

    $stmt->close();
    exit();
}

$conn->close();
?>
