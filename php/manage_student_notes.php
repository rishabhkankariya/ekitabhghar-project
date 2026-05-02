<?php
session_start();
include 'connection.php';  // Assuming you have a database connection file

// Check if the user is logged in as admin
if (!isset($_SESSION["admin_id"])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access! Please login first.']);
    exit();
}

// Handle different actions
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'fetch_years_semesters':
        fetchYearsSemesters();
        break;

    case 'fetch':
        fetchQuestionPapers();
        break;

    case 'add':
        addQuestionPaper();
        break;

    case 'update':
        updateQuestionPaper();
        break;

    case 'delete':
        deleteQuestionPaper();
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

// Fetch available years and semesters for dropdown
function fetchYearsSemesters()
{
    global $conn;
    $years = ['firstsem', 'secondsem', 'thirdsem', 'fourthsem', 'fifthsem', 'sixthsem']; // Hardcoded for now, can be dynamically fetched
    $semesters = ['1', '2', '3'];

    echo json_encode([
        'status' => 'success',
        'years' => $years,
        'semesters' => $semesters
    ]);
}

// Fetch all question papers
function fetchQuestionPapers()
{
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM student_notes");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $data]);
}

// Add new question paper
function addQuestionPaper()
{
    global $pdo;

    $year = $_POST['year'];
    $semester = $_POST['semester'];
    $subject_name = $_POST['subject_name'];

    $baseNotesDir = "../notes/notes/";
    $baseImagesDir = "../notes/images/";

    if (!is_dir($baseNotesDir)) mkdir($baseNotesDir, 0777, true);
    if (!is_dir($baseImagesDir)) mkdir($baseImagesDir, 0777, true);

    $pdfDestination = "";
    if (isset($_FILES['pdf']) && $_FILES['pdf']['name']) {
        $pdfFileName = time() . "_" . basename($_FILES['pdf']['name']);
        $targetPath = $baseNotesDir . $pdfFileName;
        if (move_uploaded_file($_FILES['pdf']['tmp_name'], $targetPath)) {
            $pdfDestination = "notes/" . $pdfFileName;
        }
    }

    $imageDestination = "images/default.png";
    if (isset($_FILES['image']) && $_FILES['image']['name']) {
        $imageFileName = time() . "_" . basename($_FILES['image']['name']);
        $targetPath = $baseImagesDir . $imageFileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imageDestination = "images/" . $imageFileName;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO student_notes (semester, subject_name, image_url, notes_link) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$year, $subject_name, $imageDestination, $pdfDestination])) {
        echo json_encode(['status' => 'success', 'message' => 'Note added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add note.']);
    }
}

// Update an existing question paper
function updateQuestionPaper()
{
    global $pdo;
    $id = $_POST['id'];
    $subject_name = $_POST['subject_name'];

    $baseNotesDir = "../notes/notes/";
    $baseImagesDir = "../notes/images/";

    $updateFields = [];
    $params = [];

    $updateFields[] = "subject_name = ?";
    $params[] = $subject_name;

    if (isset($_FILES['pdf']) && $_FILES['pdf']['name']) {
        $pdfFileName = time() . "_" . basename($_FILES['pdf']['name']);
        $targetPath = $baseNotesDir . $pdfFileName;
        if (move_uploaded_file($_FILES['pdf']['tmp_name'], $targetPath)) {
            $updateFields[] = "notes_link = ?";
            $params[] = "notes/" . $pdfFileName;
        }
    }

    if (isset($_FILES['image']) && $_FILES['image']['name']) {
        $imageFileName = time() . "_" . basename($_FILES['image']['name']);
        $targetPath = $baseImagesDir . $imageFileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $updateFields[] = "image_url = ?";
            $params[] = "images/" . $imageFileName;
        }
    }

    $params[] = $id;
    $sql = "UPDATE student_notes SET " . implode(", ", $updateFields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute($params)) {
        echo json_encode(['status' => 'success', 'message' => 'Note updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update note.']);
    }
}

// Delete a question paper
function deleteQuestionPaper()
{
    global $pdo;
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM student_notes WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo json_encode(['status' => 'success', 'message' => 'Note deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete note.']);
    }
}
?>
