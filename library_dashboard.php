<?php
session_start();
include "php/connection.php";

if (!isset($_SESSION["admin_id"]) || !isset($_SESSION["username"])) {
  echo "<script>alert('Unauthorized access! Please login first.'); window.location.href='library_login.html';</script>";
  exit();
}
$admin_id = $_SESSION["admin_id"];
$username = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>E-KITABGHAR | Library Dashboard</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <!-- AOS CSS -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />

  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
  </style>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- Header -->
  <header class="bg-blue-700 text-white shadow-md">
    <div
      class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-3">

      <!-- Branding -->
      <h1 class="text-xl sm:text-2xl font-semibold flex items-center gap-2 text-center sm:text-left">
        <i class="bi bi-journal-bookmark-fill text-white text-3xl"></i>
        <span class="tracking-wide">E-KITABGHAR | LIBRARY DASHBOARD</span>
      </h1>

      <!-- User Section -->
      <div class="flex flex-col sm:flex-row sm:items-center gap-2 text-sm">
        <span class="font-medium text-center sm:text-right">Welcome,
          <?php echo htmlspecialchars($username); ?>
        </span>
        <a href="php/logout.php"
          class="bg-red-500 hover:bg-red-600 transition px-4 py-2 rounded-md flex items-center justify-center gap-1 text-white text-sm shadow-sm">
          <i class="bi bi-box-arrow-right text-base"></i>
          Logout
        </a>
      </div>

    </div>
  </header>


  <!-- Main Content -->
  <main class="flex-1 container mx-auto px-6 py-10">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">

      <!-- Manage Syllabus -->
      <div class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl transition duration-300"
        data-aos="zoom-in">
        <i class="bi bi-book-fill text-blue-600 text-4xl mb-4"></i>
        <h3 class="text-lg font-semibold mb-2">Manage Syllabus</h3>
        <a href="php/admin_manage_syllabus.php" class="text-blue-600 font-medium hover:underline">Go to Page</a>
      </div>

      <!-- Manage Question Papers -->
      <div class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl transition duration-300"
        data-aos="zoom-in" data-aos-delay="100">
        <i class="bi bi-file-earmark-text-fill text-green-600 text-4xl mb-4"></i>
        <h3 class="text-lg font-semibold mb-2">Manage Question Papers</h3>
        <a href="php/admin_manage_question_paper.php" class="text-green-600 font-medium hover:underline">Go to Page</a>
      </div>

      <!-- Student Notes -->
      <div class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl transition duration-300"
        data-aos="zoom-in" data-aos-delay="200">
        <i class="bi bi-journal-text text-purple-600 text-4xl mb-4"></i>
        <h3 class="text-lg font-semibold mb-2">Student Notes</h3>
        <a href="php/admin_manage_student_notes.php" class="text-purple-600 font-medium hover:underline">Go to Page</a>
      </div>

    </div>
  </main>

  <!-- Session Timer -->
  <div id="timer-box"
    class="fixed bottom-5 right-5 bg-yellow-500 text-white px-4 py-2 rounded-md shadow-md text-sm flex items-center gap-2">
    <i class="bi bi-stopwatch-fill animate-pulse"></i> Session ends in <span id="countdown"
      class="font-semibold">180</span>s
  </div>

  <!-- AOS JS -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({ duration: 800, once: true });
  </script>

  <!-- Session Timer Script -->
  <script>
    let inactivityTime = 180;
    let timeout;
    const countdownDisplay = document.getElementById("countdown");

    function updateCountdown() {
      if (inactivityTime > 0) {
        countdownDisplay.textContent = inactivityTime;
        inactivityTime--;
      } else {
        clearInterval(timeout);
        logoutUser();
      }
    }

    function resetTimer() {
      clearInterval(timeout);
      inactivityTime = 180;
      countdownDisplay.textContent = inactivityTime;
      timeout = setInterval(updateCountdown, 1000);
    }

    function logoutUser() {
      fetch("php/logout.php", { method: "POST" }).then(() => {
        sessionStorage.removeItem("activeSession");
        localStorage.removeItem("sessionActive");
        window.location.href = "admin_login.php";
      });
    }

    document.addEventListener("mousemove", resetTimer);
    document.addEventListener("keydown", resetTimer);
    document.addEventListener("click", resetTimer);
    document.addEventListener("scroll", resetTimer);

    window.addEventListener("beforeunload", () => {
      if (!sessionStorage.getItem("activeSession")) {
        navigator.sendBeacon("php/logout.php");
      }
    });

    document.addEventListener("visibilitychange", () => {
      if (document.visibilityState === "hidden") {
        setTimeout(() => {
          if (document.visibilityState === "hidden") {
            navigator.sendBeacon("php/logout.php");
          }
        }, 100);
      }
    });

    if (!sessionStorage.getItem("activeSession")) {
      if (localStorage.getItem("sessionActive") === "true") {
        sessionStorage.setItem("activeSession", "true");
      } else {
        sessionStorage.setItem("activeSession", "true");
        localStorage.setItem("sessionActive", "true");
      }
    }

    if (sessionStorage.getItem("activeSession")) {
      resetTimer();
    }
  </script>
</body>

</html>
