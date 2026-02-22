<?php
// Database connection setup
require '../php/connection.php';

// Handle delete request if POST data is provided
if (isset($_POST['delete_id'])) {
  $id = intval($_POST['delete_id']);

  $query = "SELECT file_name FROM contributed_notes WHERE id = $id";
  $result = $conn->query($query);

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $file_name = $row['file_name'];

    $file_path = "../PHP/uploads/notes/" . $file_name;
    if (file_exists($file_path)) {
      unlink($file_path);
    }

    $delete_query = "DELETE FROM contributed_notes WHERE id = $id";
    if ($conn->query($delete_query)) {
      echo json_encode(['status' => 'success', 'message' => 'Note permanently removed.']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Failed to remove entry from registry.']);
    }
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Resource not found.']);
  }
  $conn->close();
  exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Portal Notes | Admin</title>
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8fafc;
      color: #1e293b;
    }

    .card-shadow {
      box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    }

    .btn-shadow:hover {
      box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
      transform: translateY(-1px);
    }
  </style>
</head>

<body class="min-h-screen py-10 px-4 md:px-8">

  <div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
      <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
          <span class="p-2.5 bg-teal-100 text-teal-600 rounded-xl"><i class="bi bi-journal-bookmark-fill"></i></span>
          Contributed Notes
        </h1>
        <p class="text-slate-500 mt-2">Moderate and manage educational materials shared by the student community.</p>
      </div>
      <div class="flex items-center gap-3">
        <a href="adminpanel.php"
          class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-50 transition-all">
          <i class="bi bi-arrow-left"></i> Dashboard View
        </a>
      </div>
    </div>

    <!-- Notes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="notes-list">
      <?php
      $result = mysqli_query($conn, "SELECT * FROM contributed_notes ORDER BY uploaded_at DESC");
      if (mysqli_num_rows($result) > 0):
        while ($row = mysqli_fetch_assoc($result)):
          $id = $row['id'];
          $student_name = htmlspecialchars($row['student_name']);
          $notes_title = htmlspecialchars($row['notes_title']);
          $semester = $row['semester'];
          $file_path = $row['file_name'];
          $date = date("d M Y", strtotime($row['uploaded_at']));
          ?>
          <div
            class="bg-white border border-slate-200 rounded-2xl overflow-hidden card-shadow transition-all hover:border-teal-300 group"
            id="note-<?= $id ?>">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <span
                  class="px-2.5 py-1 bg-teal-50 text-teal-600 rounded text-[10px] font-bold uppercase tracking-widest border border-teal-100">
                  Sem <?= $semester ?>
                </span>
                <div class="w-8 h-8 bg-slate-50 text-slate-400 rounded-lg flex items-center justify-center">
                  <i class="bi bi-file-earmark-pdf"></i>
                </div>
              </div>
              <h2 class="text-md font-bold text-slate-900 leading-tight mb-2 group-hover:text-teal-600 transition-colors">
                <?= $notes_title ?></h2>
              <div class="space-y-1 mb-6">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                  <i class="bi bi-person-fill text-[10px]"></i> <?= $student_name ?>
                </p>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                  <i class="bi bi-calendar3 text-[10px]"></i> <?= $date ?>
                </p>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <a href="../php/uploads/notes/<?= $file_path ?>" target="_blank"
                  class="flex items-center justify-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-700 px-4 py-2.5 rounded-xl text-xs font-bold transition-all border border-slate-200">
                  <i class="bi bi-eye"></i> View
                </a>
                <button onclick="confirmDelete(<?= $id ?>)"
                  class="flex items-center justify-center gap-2 bg-white hover:bg-rose-50 text-rose-500 hover:text-rose-600 px-4 py-2.5 rounded-xl text-xs font-bold transition-all border border-slate-200 hover:border-rose-200">
                  <i class="bi bi-trash3"></i> Delete
                </button>
              </div>
            </div>
          </div>
        <?php
        endwhile;
      else:
        ?>
        <div class="col-span-full py-20 bg-white border border-dashed border-slate-300 rounded-3xl text-center">
          <i class="bi bi-journals text-4xl text-slate-200 mb-4 block"></i>
          <p class="text-slate-400 font-bold tracking-tight">No contributed notes available for moderation.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    <div
      class="relative bg-white rounded-3xl p-8 max-w-sm w-full shadow-2xl transform transition-all scale-95 opacity-0 duration-200"
      id="modalContent">
      <div
        class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl">
        <i class="bi bi-exclamation-triangle"></i>
      </div>
      <h2 class="text-xl font-bold text-center text-slate-900 mb-2">Delete Note?</h2>
      <p class="text-sm text-center text-slate-500 mb-8 leading-relaxed">This action will permanently remove the
        educational material from both the portal and the server registry.</p>
      <div class="flex gap-3">
        <button onclick="closeModal()"
          class="flex-1 py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all">Cancel</button>
        <button onclick="executeDelete()" id="confirmDeleteBtn"
          class="flex-1 py-3 px-4 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-xl transition-all shadow-lg shadow-rose-100">Confirm</button>
      </div>
    </div>
  </div>

  <!-- Toast Component -->
  <div id="toast"
    class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-4 hidden z-[100] animate-bounce">
    <i class="bi bi-check-circle-fill text-emerald-400 text-xl"></i>
    <p class="text-sm font-bold" id="toastMessage"></p>
  </div>

  <script>
    let noteToDelete = null;

    function confirmDelete(id) {
      noteToDelete = id;
      const modal = document.getElementById('deleteModal');
      const content = document.getElementById('modalContent');
      modal.classList.remove('hidden');
      setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
      }, 10);
    }

    function closeModal() {
      const modal = document.getElementById('deleteModal');
      const content = document.getElementById('modalContent');
      content.classList.add('scale-95', 'opacity-0');
      content.classList.remove('scale-100', 'opacity-100');
      setTimeout(() => {
        modal.classList.add('hidden');
        noteToDelete = null;
      }, 200);
    }

    function executeDelete() {
      if (!noteToDelete) return;
      const btn = document.getElementById('confirmDeleteBtn');
      btn.disabled = true;
      btn.innerHTML = '<i class="bi bi-three-dots"></i>';

      $.ajax({
        url: '',
        type: 'POST',
        data: { delete_id: noteToDelete },
        dataType: 'json',
        success: function (res) {
          if (res.status === 'success') {
            $('#note-' + noteToDelete).fadeOut(400, function () { $(this).remove(); });
            showToast(res.message);
          }
          closeModal();
        },
        error: function () {
          showToast('Communication failure. Please Refresh.');
          closeModal();
        },
        complete: function () {
          btn.disabled = false;
          btn.innerHTML = 'Confirm';
        }
      });
    }

    function showToast(msg) {
      const toast = document.getElementById('toast');
      document.getElementById('toastMessage').innerText = msg;
      toast.classList.remove('hidden');
      setTimeout(() => toast.classList.add('hidden'), 5000);
    }
  </script>
</body>

</html>
