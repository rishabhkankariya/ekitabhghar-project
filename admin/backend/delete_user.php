<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ekitabhghar";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['id'];

    // Prepare the DELETE query to remove the student
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        $response = ['status' => 'success', 'message' => 'User deleted successfully!'];
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to delete user. Please try again.'];
    }

    $stmt->close();
    $conn->close();

    // Return response as JSON
    echo json_encode($response);
}
?>
