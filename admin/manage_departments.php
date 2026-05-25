<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

require_once '../php/connection.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'add':
                $stmt = $pdo->prepare("INSERT INTO departments (dept_code, dept_name, short_name, description, is_active) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['dept_code'],
                    $_POST['dept_name'],
                    $_POST['short_name'],
                    $_POST['description'] ?? '',
                    $_POST['is_active'] ?? 1
                ]);
                echo json_encode(['success' => true, 'message' => 'Department added successfully']);
                break;
                
            case 'update':
                $stmt = $pdo->prepare("UPDATE departments SET dept_code=?, dept_name=?, short_name=?, description=?, is_active=? WHERE id=?");
                $stmt->execute([
                    $_POST['dept_code'],
                    $_POST['dept_name'],
                    $_POST['short_name'],
                    $_POST['description'] ?? '',
                    $_POST['is_active'] ?? 1,
                    $_POST['id']
                ]);
                echo json_encode(['success' => true, 'message' => 'Department updated successfully']);
                break;
                
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM departments WHERE id=?");
                $stmt->execute([$_POST['id']]);
                echo json_encode(['success' => true, 'message' => 'Department deleted successfully']);
                break;
                
            case 'toggle_status':
                $stmt = $pdo->prepare("UPDATE departments SET is_active = NOT is_active WHERE id=?");
                $stmt->execute([$_POST['id']]);
                echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
                break;
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}

// Fetch all departments
$departments = $pdo->query("SELECT * FROM departments ORDER BY dept_code")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../../apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="../../favicon.ico">
    <link rel="manifest" href="../../site.webmanifest">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    
    <!-- Header -->
    <nav class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <i class="bi bi-building text-indigo-600 text-2xl"></i>
            <h1 class="text-xl font-bold text-gray-800">Manage Departments</h1>
        </div>
        <div class="flex gap-3">
            <a href="adminpanel.php" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        
        <!-- Add Department Button -->
        <div class="mb-6">
            <button onclick="openModal()" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                <i class="bi bi-plus-circle"></i> Add New Department
            </button>
        </div>

        <!-- Departments Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Department Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Short Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="departmentTable">
                    <?php foreach ($departments as $dept): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono"><?= htmlspecialchars($dept['dept_code']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($dept['dept_name']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($dept['short_name']) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $dept['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= $dept['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <button onclick='editDepartment(<?= json_encode($dept) ?>)' class="text-blue-600 hover:text-blue-800">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <button onclick="toggleStatus(<?= $dept['id'] ?>)" class="text-orange-600 hover:text-orange-800">
                                <i class="bi bi-toggle-on"></i> Toggle
                            </button>
                            <button onclick="deleteDepartment(<?= $dept['id'] ?>)" class="text-red-600 hover:text-red-800">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="deptModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 w-full max-w-md">
            <h2 id="modalTitle" class="text-2xl font-bold mb-6">Add Department</h2>
            <form id="deptForm" class="space-y-4">
                <input type="hidden" id="dept_id" name="id">
                
                <div>
                    <label class="block text-sm font-semibold mb-2">Department Code *</label>
                    <input type="text" id="dept_code" name="dept_code" required 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold mb-2">Department Name *</label>
                    <input type="text" id="dept_name" name="dept_name" required 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold mb-2">Short Name *</label>
                    <input type="text" id="short_name" name="short_name" required 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold mb-2">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked class="w-4 h-4">
                    <label for="is_active" class="text-sm">Active</label>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Save
                    </button>
                    <button type="button" onclick="closeModal()" class="flex-1 px-6 py-3 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="fixed bottom-5 right-5 px-6 py-3 rounded-lg shadow-lg hidden"></div>

    <script>
        let editMode = false;

        function openModal() {
            editMode = false;
            document.getElementById('modalTitle').textContent = 'Add Department';
            document.getElementById('deptForm').reset();
            document.getElementById('dept_id').value = '';
            document.getElementById('deptModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('deptModal').classList.add('hidden');
        }

        function editDepartment(dept) {
            editMode = true;
            document.getElementById('modalTitle').textContent = 'Edit Department';
            document.getElementById('dept_id').value = dept.id;
            document.getElementById('dept_code').value = dept.dept_code;
            document.getElementById('dept_name').value = dept.dept_name;
            document.getElementById('short_name').value = dept.short_name;
            document.getElementById('description').value = dept.description || '';
            document.getElementById('is_active').checked = dept.is_active == 1;
            document.getElementById('deptModal').classList.remove('hidden');
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `fixed bottom-5 right-5 px-6 py-3 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-600' : 'bg-red-600'} text-white`;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3000);
        }

        document.getElementById('deptForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', editMode ? 'update' : 'add');
            formData.set('is_active', document.getElementById('is_active').checked ? 1 : 0);

            try {
                const response = await fetch('manage_departments.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                showToast('An error occurred', 'error');
            }
        });

        async function toggleStatus(id) {
            const formData = new FormData();
            formData.append('action', 'toggle_status');
            formData.append('id', id);

            try {
                const response = await fetch('manage_departments.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                }
            } catch (error) {
                showToast('An error occurred', 'error');
            }
        }

        async function deleteDepartment(id) {
            if (!confirm('Are you sure you want to delete this department? This will also delete all associated courses and subjects.')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);

            try {
                const response = await fetch('manage_departments.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                showToast('An error occurred', 'error');
            }
        }
    </script>
</body>
</html>
