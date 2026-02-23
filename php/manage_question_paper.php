<?php
session_start();
require_once 'connection.php'; // Database connection

header('Content-Type: application/json');

$action = $_POST['action'] ?? 'fetch';
$uploadDir = realpath(__DIR__ . '/../pdfs') . DIRECTORY_SEPARATOR;

function respond($status, $data = []) {
    echo json_encode(array_merge(["status" => $status], $data));
    exit;
}

// Fetch all question papers
if ($action === "fetch") {
    $query = "SELECT id, year, semester, subject_name, pdf_path 
              FROM question_papers 
              ORDER BY CAST(year AS UNSIGNED) ASC, CAST(semester AS UNSIGNED) ASC";
    $result = $conn->query($query);

    $papers = [];
    while ($row = $result->fetch_assoc()) {
        $papers[] = [
            'id' => $row['id'],
            'year' => $row['year'],
            'semester' => $row['semester'],
            'subject_name' => $row['subject_name'],
            'pdf' => '/' . htmlspecialchars($row['pdf_path'])
        ];
    }

    respond("success", ["data" => $papers]);
}

// Fetch years and semesters
elseif ($action === "fetch_years_semesters") {
    $years = [];
    $semesters = [];

    $yrRes = $conn->query("SELECT DISTINCT year FROM question_papers ORDER BY year ASC");
    while ($row = $yrRes->fetch_assoc()) $years[] = $row['year'];

    $semRes = $conn->query("SELECT DISTINCT semester FROM question_papers ORDER BY semester ASC");
    while ($row = $semRes->fetch_assoc()) $semesters[] = $row['semester'];

    respond("success", ["years" => $years, "semesters" => $semesters]);
}

// Add question paper
elseif ($action === "add") {
    $year = $_POST['year'] ?? '';
    $semester = $_POST['semester'] ?? '';
    $subject = trim($_POST['subject_name'] ?? '');

    if (!$year || !$semester || !$subject) {
        respond("error", ["message" => "All fields are required!"]);
    }

    if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
        respond("error", ["message" => "PDF upload failed!"]);
    }

    // Safe filename
    $safeName = time() . "_" . preg_replace("/[^a-zA-Z0-9_.-]/", "_", basename($_FILES["pdf"]["name"]));
    $finalPath = $uploadDir . $safeName;

    if (!move_uploaded_file($_FILES["pdf"]["tmp_name"], $finalPath)) {
        respond("error", ["message" => "Unable to save the uploaded PDF."]);
    }

    $pdfPath = "pdfs/" . $safeName;
    $stmt = $conn->prepare("INSERT INTO question_papers (year, semester, subject_name, pdf_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $year, $semester, $subject, $pdfPath);

    if ($stmt->execute()) {
        respond("success", ["message" => "Question paper added successfully"]);
    } else {
        respond("error", ["message" => "DB error: " . $stmt->error]);
    }
}

// Update paper
elseif ($action === "update") {
    $id = $_POST['id'] ?? null;
    $subject = trim($_POST['subject_name'] ?? '');

    if (!$id || !is_numeric($id)) respond("error", ["message" => "Invalid ID"]);
    if (!$subject) respond("error", ["message" => "Subject name is required"]);

    // Get current path
    $stmt = $conn->prepare("SELECT pdf_path FROM question_papers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($currentPath);
    $stmt->fetch();
    $stmt->close();

    $newPath = $currentPath;

    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $safeName = time() . "_" . preg_replace("/[^a-zA-Z0-9_.-]/", "_", basename($_FILES["pdf"]["name"]));
        $fullNewPath = $uploadDir . $safeName;

        if (!move_uploaded_file($_FILES["pdf"]["tmp_name"], $fullNewPath)) {
            respond("error", ["message" => "New PDF upload failed"]);
        }

        if ($currentPath && file_exists(__DIR__ . '/../' . ltrim($currentPath, '/'))) {
            unlink(__DIR__ . '/../' . ltrim($currentPath, '/'));
        }

        $newPath = "pdfs/" . $safeName;
    }

    $stmt = $conn->prepare("UPDATE question_papers SET subject_name = ?, pdf_path = ? WHERE id = ?");
    $stmt->bind_param("ssi", $subject, $newPath, $id);

    if ($stmt->execute()) {
        respond("success", ["message" => "Question paper updated"]);
    } else {
        respond("error", ["message" => "Update failed: " . $stmt->error]);
    }
}

// Delete paper
elseif ($action === "delete") {
    $id = $_POST['id'] ?? null;

    if (!$id || !is_numeric($id)) respond("error", ["message" => "Invalid ID"]);

    $stmt = $conn->prepare("SELECT pdf_path FROM question_papers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($pdfPath);
    $stmt->fetch();
    $stmt->close();

    if ($pdfPath) {
        $absolutePath = __DIR__ . '/../' . ltrim($pdfPath, '/');
        if (file_exists($absolutePath) && is_writable($absolutePath)) {
            unlink($absolutePath);
        }
    }

    $stmt = $conn->prepare("DELETE FROM question_papers WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        respond("success", ["message" => "Question paper deleted"]);
    } else {
        respond("error", ["message" => "Deletion failed: " . $stmt->error]);
    }
}

$conn->close();
?>
