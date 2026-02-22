<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit();
}

require_once __DIR__ . '/../php/connection.php';

$admin_id = $_SESSION['admin_id'];

// Fetch admin details
$sql = "SELECT username, profile_pic FROM admin WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

// Update Username
if (isset($_POST['update_username'])) {
  $new_username = trim(htmlspecialchars($_POST['new_username']));
  if (!empty($new_username)) {
    $sql = "UPDATE admin SET username = ? WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_username, $admin_id);
    if ($stmt->execute()) {
      $_SESSION['toast'] = ['type' => 'success', 'message' => 'Username updated successfully!'];
    }
    $stmt->close();
    header('Location: admin_profile.php');
    exit();
  }
}

// Update Profile Picture
if (isset($_POST['update_profile']) && isset($_FILES["profile_pic"])) {
  $target_dir = "uploads/";
  if (!file_exists($target_dir))
    mkdir($target_dir, 0777, true);

  $file_ext = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
  $file_name = "admin_" . $admin_id . "_" . time() . "." . $file_ext;
  $target_file = $target_dir . $file_name;

  $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);

  if (!$check || !in_array($file_ext, ['jpg', 'jpeg', 'png']) || $_FILES["profile_pic"]["size"] > 2000000) {
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Invalid image! Use JPG/PNG under 2MB.'];
  } else {
    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
      $sql = "UPDATE admin SET profile_pic = ? WHERE admin_id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("si", $target_file, $admin_id);
      if ($stmt->execute()) {
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Profile picture updated!'];
      }
      $stmt->close();
      header('Location: admin_profile.php');
      exit();
    }
  }
}

// Change Password
if (isset($_POST['change_password'])) {
  $current_password = trim($_POST['current_password']);
  $new_password = trim($_POST['new_password']);
  $confirm_password = trim($_POST['confirm_password']);

  $sql = "SELECT password FROM admin WHERE admin_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $admin_id);
  $stmt->execute();
  $admin_data = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  if (!password_verify($current_password, $admin_data['password'])) {
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Incorrect current password!'];
  } elseif ($new_password !== $confirm_password) {
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Passwords do not match!'];
  } elseif (strlen($new_password) < 6) {
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Min 6 characters required.'];
  } else {
    $hashed = password_hash($new_password, PASSWORD_BCRYPT);
    $sql = "UPDATE admin SET password = ? WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashed, $admin_id);
    if ($stmt->execute()) {
      $_SESSION['toast'] = ['type' => 'success', 'message' => 'Password updated successfully!'];
    }
    $stmt->close();
    header('Location: admin_profile.php');
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile Settings | Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/e72d27fd60.js" crossorigin="anonymous"></script>
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
  </style>
</head>

<body class="min-h-screen py-10 px-4">

  <div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Account Settings</h1>
        <p class="text-slate-500 mt-1">Manage your administrative credentials and profile.</p>
      </div>
      <a href="adminpanel.php"
        class="flex items-center gap-2 text-slate-600 hover:text-indigo-600 font-bold transition-all">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
      </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left: Profile Preview -->
      <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-8 card-shadow text-center">
          <div class="relative inline-block mb-6">
            <img src="<?= $admin['profile_pic'] ?: 'uploads/dummy.png' ?>"
              class="w-32 h-32 rounded-2xl object-cover border-4 border-slate-50 shadow-lg mx-auto" alt="Admin">
            <div
              class="absolute -bottom-2 -right-2 bg-emerald-500 text-white w-8 h-8 rounded-lg flex items-center justify-center border-4 border-white">
              <i class="bi bi-shield-check text-xs"></i>
            </div>
          </div>
          <h2 class="text-xl font-bold text-slate-900"><?= htmlspecialchars($admin['username']) ?></h2>
          <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Access: Administrator</p>

          <form method="POST" enctype="multipart/form-data" class="mt-8">
            <label class="block w-full cursor-pointer">
              <input type="file" name="profile_pic" onchange="this.form.submit()" class="hidden">
              <input type="hidden" name="update_profile" value="1">
              <div
                class="py-3 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-2">
                <i class="bi bi-camera"></i> Change Avatar
              </div>
            </label>
          </form>
        </div>

        <div class="bg-indigo-600 rounded-2xl p-6 text-white shadow-lg shadow-indigo-100">
          <h4 class="font-bold flex items-center gap-2 mb-2">
            <i class="bi bi-info-circle"></i> Security Note
          </h4>
          <p class="text-xs text-indigo-100 leading-relaxed font-medium"> Ensure you use a strong, unique password for
            your administrative portal to prevent unauthorized access.</p>
        </div>
      </div>

      <!-- Right: Update Forms -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Username Update -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 card-shadow">
          <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
            <i class="bi bi-person-badge text-indigo-600"></i> Identity Information
          </h3>
          <form method="POST">
            <div class="mb-6">
              <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Current
                Username</label>
              <input type="text" name="new_username" value="<?= htmlspecialchars($admin['username']) ?>" required
                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all">
            </div>
            <button type="submit" name="update_username"
              class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-indigo-100">
              Update Identity
            </button>
          </form>
        </div>

        <!-- Password Update -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 card-shadow">
          <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
            <i class="bi bi-lock text-rose-500"></i> Authentication Credentials
          </h3>
          <form method="POST" class="space-y-6">
            <div>
              <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Confirm Current
                Password</label>
              <input type="password" name="current_password" required placeholder="••••••••"
                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">New
                  Password</label>
                <input type="password" name="new_password" required placeholder="Min 6 characters"
                  class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all">
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Confirm New
                  Password</label>
                <input type="password" name="confirm_password" required placeholder="Re-type password"
                  class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all">
              </div>
            </div>
            <button type="submit" name="change_password"
              class="px-8 py-3 bg-slate-900 hover:bg-black text-white text-sm font-bold rounded-xl transition-all">
              Authorize Password Reset
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Simple Toast Notification (PHP Triggered) -->
  <?php if (isset($_SESSION['toast'])): ?>
    <div id="toast"
      class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-6 py-3 rounded-2xl shadow-2xl flex items-center gap-3 animate-bounce">
      <i class="bi bi-info-circle text-indigo-400"></i>
      <p class="text-sm font-medium"><?= $_SESSION['toast']['message'] ?></p>
      <?php unset($_SESSION['toast']); ?>
    </div>
    <script>setTimeout(() => { document.getElementById('toast').remove(); }, 4000);</script>
  <?php endif; ?>

</body>

</html>
