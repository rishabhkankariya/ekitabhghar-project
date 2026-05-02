<?php
require_once '../../php/connection.php';

$query = isset($_GET['query']) ? '%' . $_GET['query'] . '%' : '%';

$stmt = $pdo->prepare("SELECT * FROM students 
    WHERE student_name LIKE ? OR roll_no LIKE ? OR course_type LIKE ?
    OR current_semester LIKE ? OR category LIKE ? OR status LIKE ? OR CAST(exam_date AS TEXT) LIKE ?");
$stmt->execute([$query, $query, $query, $query, $query, $query, $query]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($rows) > 0) {
    echo "<table class='min-w-full border border-gray-200 rounded-xl overflow-hidden'>";
    echo "<thead><tr class='bg-gray-100 text-left'>
            <th class='px-4 py-2'>S.No</th><th class='px-4 py-2'>Name</th>
            <th class='px-4 py-2'>Roll No</th><th class='px-4 py-2'>Course</th>
            <th class='px-4 py-2'>Year</th><th class='px-4 py-2'>Semester</th>
            <th class='px-4 py-2'>Category</th><th class='px-4 py-2'>Status</th>
          </tr></thead><tbody>";
    $sno = 1;
    foreach ($rows as $row) {
        echo "<tr class='border-t'>
                <td class='px-4 py-2'>" . $sno++ . "</td>
                <td class='px-4 py-2'>" . htmlspecialchars($row['student_name']) . "</td>
                <td class='px-4 py-2'>" . htmlspecialchars($row['roll_no']) . "</td>
                <td class='px-4 py-2'>" . htmlspecialchars($row['course_type']) . "</td>
                <td class='px-4 py-2'>" . getYearFromSemester($row['current_semester']) . "</td>
                <td class='px-4 py-2'>" . htmlspecialchars($row['current_semester']) . "</td>
                <td class='px-4 py-2'>" . htmlspecialchars($row['category']) . "</td>
                <td class='px-4 py-2 font-medium'>" .
                  ($row['status'] === 'approved' ? "<span class='text-green-600'>Approved</span>" :
                  ($row['status'] === 'rejected' ? "<span class='text-red-600'>Rejected</span>" :
                  "<span class='text-yellow-600'>Pending</span>")) .
                "</td></tr>";
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
