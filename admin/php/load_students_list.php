<?php
include '../php/connection.php';

$where = "1=1";
$params = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = '%' . $_GET['search'] . '%';
    $where .= " AND (full_name LIKE ? OR roll_no LIKE ? OR email LIKE ?)";
    $params = array_merge($params, [$search, $search, $search]);
}

if (isset($_GET['status']) && !empty($_GET['status'])) {
    $where .= " AND account_status = ?";
    $params[] = $_GET['status'];
}

$sql = "SELECT id, roll_no, full_name, email, phone_number, account_status, is_temp_password, course, admission_year, expected_passing_year FROM student_accounts WHERE $where ORDER BY id DESC LIMIT 50";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $statusColors = [
            'active' => 'bg-green-100 text-green-800',
            'blocked' => 'bg-red-100 text-red-800',
            'completed' => 'bg-blue-100 text-blue-800',
            'backlog' => 'bg-yellow-100 text-yellow-800'
        ];
        $badgeColor = $statusColors[$row['account_status']] ?? 'bg-gray-100';

        echo "<tr class='hover:bg-gray-50 border-b border-gray-50 group border-l-4 border-transparent " . ($row['account_status'] == 'blocked' ? 'hover:border-red-400' : 'hover:border-blue-400') . "'>";
        echo "<td class='p-4'><input type='checkbox' name='student_ids[]' value='" . $row['id'] . "' class='rounded text-blue-600 focus:ring-blue-500 cursor-pointer'></td>";
        echo "<td class='p-4 font-mono text-xs sm:text-sm font-semibold text-gray-700'>" . htmlspecialchars($row['roll_no']) . "</td>";
        echo "<td class='p-4 font-medium text-gray-900'>" . htmlspecialchars($row['full_name']) . "</td>";

        // New Course & Year Column
        echo "<td class='p-4 text-xs text-gray-600'>";
        echo "<div class='font-semibold text-gray-800'>" . htmlspecialchars($row['course']) . "</div>";
        echo "<div>Admin: " . htmlspecialchars($row['admission_year']) . " | Pass: " . htmlspecialchars($row['expected_passing_year']) . "</div>";
        if ($row['phone_number'])
            echo "<div class='mt-1 italic'><i class='bi bi-phone'></i> " . htmlspecialchars($row['phone_number']) . "</div>";
        echo "</td>";

        echo "<td class='p-4 text-gray-500'>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td class='p-4'><span class='px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide " . $badgeColor . "'>" . $row['account_status'] . "</span></td>";
        echo "<td class='p-4 text-center'>" . ($row['is_temp_password'] ? '<span class="text-orange-500 bg-orange-50 px-2 py-1 rounded text-xs border border-orange-200" title="User has not changed temp password">Active</span>' : '<span class="text-green-500 text-xl" title="Password Changed"><i class="bi bi-check-all"></i></span>') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7' class='p-8 text-center text-gray-400 italic bg-gray-50 rounded-lg border-2 border-dashed'>";
    echo "<i class='bi bi-search text-2xl mb-2 block'></i> No students found matching your criteria.";
    echo "</td></tr>";
}
?>
