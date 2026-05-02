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
    $stmt = $pdo->query("SELECT id, year, semester, subject_name, pdf_path FROM question_papers ORDER BY year ASC, semester ASC");
    $papers = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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

    $stmt = $pdo->query("SELECT DISTINCT year FROM question_papers ORDER BY year ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $years[] = $row['year'];

    $stmt = $pdo->query("SELECT DISTINCT semester FROM question_papers ORDER BY semester ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $semesters[] = $row['semester'];

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
    $stmt = $pdo->prepare("INSERT INTO question_papers (year, semester, subject_name, pdf_path) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$year, $semester, $subject, $pdfPath])) {
        respond("success", ["message" => "Question paper added successfully"]);
    } else {
        respond("error", ["message" => "DB error"]);
    }
}

// Update paper
elseif ($action === "update") {
    $id = $_POST['id'] ?? null;
    $subject = trim($_POST['subject_name'] ?? '');

    if (!$id || !is_numeric($id)) respond("error", ["message" => "Invalid ID"]);
    if (!$subject) respond("error", ["message" => "Subject name is required"]);

    // Get current path
    $stmt = $pdo->prepare("SELECT pdf_path FROM question_papers WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentPath = $row['pdf_path'] ?? '';

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

    $stmt = $pdo->prepare("UPDATE question_papers SET subject_name = ?, pdf_path = ? WHERE id = ?");
    if ($stmt->execute([$subject, $newPath, $id])) {
        respond("success", ["message" => "Question paper updated"]);
    } else {
        respond("error", ["message" => "Update failed"]);
    }
}

// Delete paper
elseif ($action === "delete") {
    $id = $_POST['id'] ?? null;

    if (!$id || !is_numeric($id)) respond("error", ["message" => "Invalid ID"]);

    $stmt = $pdo->prepare("SELECT pdf_path FROM question_papers WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $pdfPath = $row['pdf_path'] ?? '';

    if ($pdfPath) {
        $absolutePath = __DIR__ . '/../' . ltrim($pdfPath, '/');
        if (file_exists($absolutePath) && is_writable($absolutePath)) {
            unlink($absolutePath);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM question_papers WHERE id = ?");
    if ($stmt->execute([$id])) {
        respond("success", ["message" => "Question paper deleted"]);
    } else {
        respond("error", ["message" => "Deletion failed"]);
    }
}
?>
