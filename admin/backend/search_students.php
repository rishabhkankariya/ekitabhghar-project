<?php

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "ekitabhghar";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get query
$query = isset($_GET['query']) ? $_GET['query'] : '';
$query = mysqli_real_escape_string($conn, $query);

// Match query with multiple fields
$sql = "SELECT * FROM students 
        WHERE student_name LIKE '%$query%' 
        OR roll_no LIKE '%$query%' 
        OR course_type LIKE '%$query%'
        OR current_semester LIKE '%$query%' 
        OR category LIKE '%$query%' 
        OR status LIKE '%$query%'
        OR exam_date LIKE '%$query%'";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table class='min-w-full border border-gray-200 rounded-xl overflow-hidden'>";
    echo "<thead><tr class='bg-gray-100 text-left'>
            <th class='px-4 py-2'>S.No</th>
            <th class='px-4 py-2'>Name</th>
            <th class='px-4 py-2'>Roll No</th>
            <th class='px-4 py-2'>Course</th>
            <th class='px-4 py-2'>Year</th>
            <th class='px-4 py-2'>Semester</th>
            <th class='px-4 py-2'>Category</th>
            <th class='px-4 py-2'>Status</th>
          </tr></thead><tbody>";

    $sno = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr class='border-t'>
                <td class='px-4 py-2'>" . $sno++ . "</td>
                <td class='px-4 py-2'>" . $row['student_name'] . "</td>
                <td class='px-4 py-2'>" . $row['roll_no'] . "</td>
                <td class='px-4 py-2'>" . $row['course_type'] . "</td>
                <td class='px-4 py-2'>" . getYearFromSemester($row['current_semester']) . "</td>
                <td class='px-4 py-2'>" . $row['current_semester'] . "</td>
                <td class='px-4 py-2'>" . $row['category'] . "</td>
                <td class='px-4 py-2 font-medium'>" .
                  ($row['status'] === 'approved' ? "<span class='text-green-600'>Approved</span>" :
                  ($row['status'] === 'rejected' ? "<span class='text-red-600'>Rejected</span>" :
                  "<span class='text-yellow-600'>Pending</span>")) .
                "</td>
              </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p class='text-center text-gray-500'>No students found matching your search.</p>";
}

function getYearFromSemester($semester) {
    if (str_contains($semester, '1st') || str_contains($semester, '2nd')) return "1st Year";
    if (str_contains($semester, '3rd') || str_contains($semester, '4th')) return "2nd Year";
    if (str_contains($semester, '5th') || str_contains($semester, '6th')) return "3rd Year";
    return "Unknown";
}
?>
