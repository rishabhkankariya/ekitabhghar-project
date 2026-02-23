<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  echo "<script>alert('Unauthorized access!'); window.location.href='admin_login.php';</script>";
  exit();
}

require_once '../config/send_mail.php';

require '../php/connection.php';
$conn1 = $conn;
$conn2 = $conn;

// Fetch admin name from admin_system database
$admin_id = $_SESSION['admin_id'];
$stmt_admin = $conn2->prepare("SELECT username FROM admin WHERE admin_id = ?");
$stmt_admin->bind_param("s", $admin_id);
$stmt_admin->execute();
$result_admin = $stmt_admin->get_result();
$admin_data = $result_admin->fetch_assoc();
$admin_name = ($result_admin->num_rows > 0) ? $admin_data['username'] : die("Error: Admin not found.");
$stmt_admin->close();

$toast_message = "";
$toast_type = "";

// Handle message sending
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
  $message = $conn1->real_escape_string($_POST['message']);

  // Insert message into database
  $stmt_insert = $conn1->prepare("INSERT INTO messages (admin_id, admin_name, message) VALUES (?, ?, ?)");
  $stmt_insert->bind_param("sss", $admin_id, $admin_name, $message);

  if ($stmt_insert->execute()) {
    // [TESTING MODE] Skip email notification to all students
    // sendNotificationEmail($message);
    $_SESSION['toast'] = ['type' => 'success', 'message' => 'Message successfully broadcasted!'];
    header("Location: admin_message.php");
    exit();
  } else {
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Failed to send message.'];
    header("Location: admin_message.php");
    exit();
  }
  $stmt_insert->close();
}

if (isset($_SESSION['toast'])) {
  $toast_message = $_SESSION['toast']['message'];
  $toast_type = $_SESSION['toast']['type'];
  unset($_SESSION['toast']);
}

// Fetch all messages
$sql_messages = "SELECT * FROM messages ORDER BY sent_at DESC";
$result_messages = $conn1->query($sql_messages);

// Function to Send Email Notification
function sendNotificationEmail($messageContent)
{
  global $conn1;

  $subject = 'New Notification from E-Kitabghar!';
  $emailBody = "
          <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
              <h2 style='color: #4CAF50;'>📢 New Notification Received</h2>
              <p>Dear Student,</p>
              <p>You have received a new message from <strong>E-Kitabghar</strong>:</p>
              <p>✅ <strong>Please check your student dashboard</strong> for more details.</p>
              <br>
              <p>Best Regards,</p>
              <p><strong>E-Kitabghar Team</strong></p>
          </div>";

  // Fetch all student emails
  $bcc = [];
  $sql = "SELECT email FROM student_accounts";
  $result = $conn1->query($sql);

  if ($result) {
    while ($row = $result->fetch_assoc()) {
      if (!empty($row['email'])) {
        $bcc[] = $row['email'];
      }
    }
  }

  if (!empty($bcc)) {
    sendEmail(null, '', $subject, $emailBody, '', $bcc);
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Communication Center | Admin</title>
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8fafc;
      color: #1e293b;
    }

    .card-shadow {
      box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    }

    .custom-scrollbar::-webkit-scrollbar {
      width: 5px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
      background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #e2e8f0;
      border-radius: 10px;
    }
  </style>
</head>

<body class="min-h-screen py-10 px-4 md:px-8">

  <div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
      <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
          <span class="p-2.5 bg-blue-100 text-blue-600 rounded-xl"><i class="bi bi-chat-left-dots-fill"></i></span>
          Communication HUB
        </h1>
        <p class="text-slate-500 mt-2">Broadcast important notices and updates to all registered students.</p>
      </div>
      <a href="adminpanel.php"
        class="inline-flex items-center gap-2 text-slate-500 hover:text-blue-600 font-bold transition-all text-sm">
        <i class="bi bi-arrow-left"></i> Dashboard
      </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
      <!-- Left: Message Composer -->
      <div class="lg:col-span-5">
        <div class="bg-white rounded-3xl border border-slate-200 p-8 card-shadow sticky top-10">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6 block px-1">Compose Broadcast</h3>
          <form method="POST" class="space-y-6">
            <div class="space-y-2">
              <label class="text-[11px] font-bold text-slate-500 ml-1">Message Content</label>
              <textarea name="message" required placeholder="Type your announcement details here..."
                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:bg-white focus:border-blue-400 transition-all min-h-[200px] resize-none"></textarea>
            </div>
            <button type="submit"
              class="w-full py-4 bg-slate-900 hover:bg-black text-white font-bold rounded-2xl shadow-xl shadow-slate-900/10 active:scale-[0.98] transition-all flex items-center justify-center gap-3">
              <i class="bi bi-send-fill text-sm"></i> Send Broadcast
            </button>
          </form>

          <div class="mt-8 p-4 bg-blue-50 rounded-2xl border border-blue-100">
            <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest flex items-center gap-2 mb-1">
              <i class="bi bi-info-circle-fill"></i> System Note
            </p>
            <p class="text-[11px] text-blue-500 leading-relaxed font-medium">[TEST MODE] Email notifications are
              disabled.
              Messages will be saved and visible on student dashboards.</p>
          </div>
        </div>
      </div>

      <!-- Right: Sent History -->
      <div class="lg:col-span-7">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6 block px-1">Recent Transmissions</h3>
        <div class="space-y-4">
          <?php if ($result_messages->num_rows > 0): ?>
            <?php while ($row = $result_messages->fetch_assoc()): ?>
              <div
                class="bg-white border border-slate-200 rounded-2xl p-6 card-shadow transition-all hover:border-slate-300 group">
                <div class="flex items-start justify-between mb-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-slate-50 text-slate-500 rounded-lg flex items-center justify-center text-sm">
                      <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                      <div class="text-sm font-bold text-slate-900"><?= htmlspecialchars($row['admin_name']) ?></div>
                      <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">
                        <?= date("M d, Y • h:i A", strtotime($row['sent_at'])) ?>
                      </div>
                    </div>
                  </div>
                  <form method="POST" action="../php/delete_message.php"
                    onsubmit="return confirm('Archive this transmission?');">
                    <input type="hidden" name="message_id" value="<?= $row['id'] ?>">
                    <button
                      class="w-8 h-8 flex items-center justify-center text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-all">
                      <i class="bi bi-trash3-fill text-sm"></i>
                    </button>
                  </form>
                </div>
                <div class="text-sm text-slate-600 leading-relaxed whitespace-pre-wrap px-1">
                  <?= htmlspecialchars($row['message']) ?>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="bg-white border border-dashed border-slate-300 rounded-3xl p-20 text-center">
              <div
                class="w-16 h-16 bg-slate-50 text-slate-200 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl">
                <i class="bi bi-chat-square-dots"></i>
              </div>
              <p class="text-slate-400 font-bold tracking-tight">No historical broadcasts found.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast Notification -->
  <?php if (!empty($toast_message)): ?>
    <div id="toast"
      class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-4 animate-bounce z-[100]">
      <i
        class="bi <?= $toast_type === 'success' ? 'bi-check-circle-fill text-emerald-400' : 'bi-exclamation-circle-fill text-rose-400' ?> text-xl"></i>
      <p class="text-sm font-bold"><?= $toast_message ?></p>
    </div>
    <script>setTimeout(() => { document.getElementById('toast').remove(); }, 5000);</script>
  <?php endif; ?>

</body>

</html>