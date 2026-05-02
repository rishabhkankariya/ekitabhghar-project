<?php
session_start();
include 'connection.php'; // Database connection

header('Content-Type: application/json');

$action = $_POST['action'] ?? 'fetch';

if ($action === "fetch") {
    $stmt = $pdo->query("SELECT id, year, semester, subject_name, pdf_path FROM syllabus ORDER BY year ASC, semester ASC");
    $syllabusData = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $syllabusData[] = [
            'id' => $row['id'],
            'year' => $row['year'],
            'semester' => $row['semester'],
            'subject_name' => $row['subject_name'],
            'pdf' => '/' . htmlspecialchars($row['pdf_path'])
        ];
    }
    echo json_encode(["status" => "success", "data" => $syllabusData]);
    exit;
}

elseif ($action === "fetch_years_semesters") {
    $years = [];
    $semesters = [];

    $stmt = $pdo->query("SELECT DISTINCT year FROM syllabus ORDER BY year ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $years[] = $row['year'];

    $stmt = $pdo->query("SELECT DISTINCT semester FROM syllabus ORDER BY semester ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $semesters[] = $row['semester'];

    echo json_encode(["status" => "success", "years" => $years, "semesters" => $semesters]);
    exit;
}

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

    $stmt = $pdo->prepare("INSERT INTO syllabus (year, semester, subject_name, pdf_path) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$year, $semester, $subjectName, $webPath])) {
        echo json_encode(["status" => "success", "message" => "Syllabus added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error"]);
    }
    exit;
}

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

    $stmt = $pdo->prepare("SELECT pdf_path FROM syllabus WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentPdfPath = $row['pdf_path'] ?? '';
    $newPdfPath = $currentPdfPath;

    $uploadDir = dirname(__DIR__) . "/pdfs/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $pdfName = time() . "_" . preg_replace("/[^a-zA-Z0-9_.-]/", "_", basename($_FILES["pdf"]["name"]));
        $serverPath = $uploadDir . $pdfName;
        $newPdfPath = "pdfs/" . $pdfName;

        if (!move_uploaded_file($_FILES["pdf"]["tmp_name"], $serverPath)) {
            echo json_encode(["status" => "error", "message" => "Failed to upload new PDF!"]);
            exit;
        }

        $oldServerPath = dirname(__DIR__) . '/' . $currentPdfPath;
        if (!empty($currentPdfPath) && file_exists($oldServerPath)) {
            unlink($oldServerPath);
        }
    }

    $stmt = $pdo->prepare("UPDATE syllabus SET subject_name = ?, pdf_path = ? WHERE id = ?");
    if ($stmt->execute([$newSubject, $newPdfPath, $id])) {
        echo json_encode(["status" => "success", "message" => "Syllabus updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error"]);
    }
    exit;
}

elseif ($action === "delete") {
    $id = $_POST['id'] ?? null;

    if (!$id || !is_numeric($id)) {
        echo json_encode(["status" => "error", "message" => "Invalid ID"]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT pdf_path FROM syllabus WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $pdfPath = $row['pdf_path'] ?? '';

    if (!empty($pdfPath)) {
        $fullPdfPath = __DIR__ . '/../pdfs/' . basename($pdfPath);
        if (file_exists($fullPdfPath) && is_writable($fullPdfPath)) {
            unlink($fullPdfPath);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM syllabus WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo json_encode(["status" => "success", "message" => "Syllabus deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error"]);
    }
    exit;
}
?>
