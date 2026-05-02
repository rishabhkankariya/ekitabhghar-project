<?php
require_once '../../php/connection.php';

$filters = [
    'semester' => $_POST['semester'] ?? '',
    'category' => $_POST['category'] ?? '',
    'date'     => $_POST['date'] ?? '',
];

$query = "SELECT * FROM students WHERE 1=1";
$params = [];

if (!empty($filters['semester'])) {
    $query .= " AND current_semester LIKE ?";
    $params[] = '%' . $filters['semester'] . '%';
}
if (!empty($filters['category'])) {
    $query .= " AND category = ?";
    $params[] = $filters['category'];
}
if (!empty($filters['date'])) {
    $query .= " AND exam_date = ?";
    $params[] = $filters['date'];
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($rows) > 0) {
    $sno = 1;
    foreach ($rows as $row) {
        echo "<tr>
          <td class='border px-4 py-2'>" . $sno++ . "</td>
          <td class='border px-4 py-2'>" . htmlspecialchars($row['student_name']) . "</td>
          <td class='border px-4 py-2'>" . htmlspecialchars($row['roll_no']) . "</td>
          <td class='border px-4 py-2'>" . getYearFromSemester($row['current_semester']) . "</td>
          <td class='border px-4 py-2'>" . htmlspecialchars($row['current_semester']) . "</td>
          <td class='px-4 py-2 font-medium'>" . statusLabel($row['status']) . "</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center text-gray-500 p-4'>No matching students found</td></tr>";
}

function getYearFromSemester($sem) {
    $sem = strtolower($sem);
    if (str_contains($sem, '1') || str_contains($sem, '2')) return '1st Year';
    if (str_contains($sem, '3') || str_contains($sem, '4')) return '2nd Year';
    if (str_contains($sem, '5') || str_contains($sem, '6')) return '3rd Year';
    return 'N/A';
}
function statusLabel($status) {
    switch ($status) {
        case 'approved': return "<span class='text-green-600'>Approved</span>";
        case 'rejected': return "<span class='text-red-600'>Rejected</span>";
        default: return "<span class='text-yellow-600'>Pending</span>";
    }
}
?>
