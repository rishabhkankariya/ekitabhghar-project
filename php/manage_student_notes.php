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
    global $conn;
    $query = "SELECT * FROM student_notes";
    $result = mysqli_query($conn, $query);
    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode(['status' => 'success', 'data' => $data]);
}

// Add new question paper
function addQuestionPaper()
{
    global $conn;

    $year = $_POST['year'];
    $semester = $_POST['semester'];
    $subject_name = $_POST['subject_name'];

    // Paths relative to this php file (php/)
    $baseNotesDir = "../notes/notes/";
    $baseImagesDir = "../notes/images/";

    // Check/Create directories
    if (!is_dir($baseNotesDir))
        mkdir($baseNotesDir, 0777, true);
    if (!is_dir($baseImagesDir))
        mkdir($baseImagesDir, 0777, true);

    $pdfDestination = "";
    if (isset($_FILES['pdf']) && $_FILES['pdf']['name']) {
        $pdfFileName = time() . "_" . basename($_FILES['pdf']['name']);
        $targetPath = $baseNotesDir . $pdfFileName;
        if (move_uploaded_file($_FILES['pdf']['tmp_name'], $targetPath)) {
            $pdfDestination = "notes/" . $pdfFileName; // Path stored in DB
        }
    }

    $imageDestination = "images/default.png"; // Default image
    if (isset($_FILES['image']) && $_FILES['image']['name']) {
        $imageFileName = time() . "_" . basename($_FILES['image']['name']);
        $targetPath = $baseImagesDir . $imageFileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imageDestination = "images/" . $imageFileName; // Path stored in DB
        }
    }

    $query = "INSERT INTO student_notes (semester, subject_name, image_url, notes_link)
              VALUES ('$year', '$subject_name', '$imageDestination', '$pdfDestination')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Question paper added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add question paper: ' . mysqli_error($conn)]);
    }
}

// Update an existing question paper
function updateQuestionPaper()
{
    global $conn;
    $id = $_POST['id'];
    $subject_name = $_POST['subject_name'];

    $baseNotesDir = "../notes/notes/";
    $baseImagesDir = "../notes/images/";

    $updateFields = ["subject_name = '$subject_name'"];

    // Update PDF if provided
    if (isset($_FILES['pdf']) && $_FILES['pdf']['name']) {
        $pdfFileName = time() . "_" . basename($_FILES['pdf']['name']);
        $targetPath = $baseNotesDir . $pdfFileName;
        if (move_uploaded_file($_FILES['pdf']['tmp_name'], $targetPath)) {
            $pdfDestination = "notes/" . $pdfFileName;
            $updateFields[] = "notes_link = '$pdfDestination'";
        }
    }

    // Update Image if provided
    if (isset($_FILES['image']) && $_FILES['image']['name']) {
        $imageFileName = time() . "_" . basename($_FILES['image']['name']);
        $targetPath = $baseImagesDir . $imageFileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imageDestination = "images/" . $imageFileName;
            $updateFields[] = "image_url = '$imageDestination'";
        }
    }

    $sql = "UPDATE student_notes SET " . implode(", ", $updateFields) . " WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Question paper updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update question paper: ' . mysqli_error($conn)]);
    }
}

// Delete a question paper
function deleteQuestionPaper()
{
    global $conn;
    $id = $_POST['id'];

    $query = "DELETE FROM student_notes WHERE id = '$id'";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Question paper deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete question paper.']);
    }
}
?>
