<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
  echo "<script>
            alert('Unauthorized access! Please log in.');
            window.location.href = 'admin_login.php';
          </script>";
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ADMIN MANAGE EXAM FORM</title>

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- AOS -->
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <link rel="icon" href="img/assembly.png" type="image/x-icon">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f9fafb;
    }

    .navbar-shadow {
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .search-input:focus {
      outline: none;
      box-shadow: 0 0 0 2px #6366f1;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(0.95);
      }

      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .animate-fadeIn {
      animation: fadeIn 0.2s ease-out;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav
    class="w-full bg-white navbar-shadow px-4 sm:px-6 py-3 flex flex-col sm:flex-row gap-4 sm:gap-0 sm:items-center justify-between"
    data-aos="fade-down">
    <a href="manage.php">
      <div class="text-xl font-semibold text-gray-800 flex items-center gap-2">
        <i class="bi bi-kanban text-indigo-600 text-2xl"></i>
        Manage Exam Form
      </div>
    </a>

    <!-- Top Action Bar (search + filter + settings + logout) -->
    <div id="top-action-bar" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
      <!-- Search Input -->
      <div class="relative w-full sm:w-64">
        <input type="text" id="searchQuery" placeholder="Search by Name or Roll No..."
          class="search-input w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition"
          oninput="searchStudents()" />
        <i class="bi bi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
      </div>

      <!-- Filter + Settings + Logout Buttons -->
      <div class="flex gap-2">
        <!-- Filter -->
        <button onclick="toggleFilterModal()"
          class="p-2 rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 w-full sm:w-auto">
          <i class="bi bi-funnel-fill text-xl"></i>
        </button>

        <!-- Settings -->
        <button onclick="openSettingsModal()"
          class="p-2 rounded-xl bg-gray-500 text-white hover:bg-gray-600 w-full sm:w-auto">
          <i class="bi bi-gear-fill text-xl"></i>
        </button>


        <!-- Logout -->
        <a href="adminpanel.php">
          <button class="p-2 rounded-xl bg-red-500 text-white hover:bg-red-600 w-full sm:w-auto">
            <i class="bi bi-box-arrow-right text-xl"></i>
          </button>
        </a>
      </div>
    </div>


    </div>
  </nav>
  <!-- Filter Modal -->
  <div id="filterModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 space-y-4 animate__animated animate__fadeInDown">
      <div class="flex justify-between items-center border-b pb-2">
        <h2 class="text-lg font-semibold text-gray-700">Filter Students</h2>
        <button onclick="toggleFilterModal()" class="text-gray-500 hover:text-red-500 text-xl"><i
            class="bi bi-x-lg"></i></button>
      </div>

      <!-- Filter Fields -->
      <div class="grid grid-cols-1 gap-4">
        <select id="filterSemester" class="input-style">
          <option value="">Select Semester</option>
          <option value="1st">1st</option>
          <option value="2nd">2nd</option>
          <option value="3rd">3rd</option>
          <option value="4th">4th</option>
          <option value="5th">5th</option>
          <option value="6th">6th</option>
        </select>

        <select id="filterCategory" class="input-style">
          <option value="">Select Category</option>
          <option value="GEN">GEN</option>
          <option value="SC">SC</option>
          <option value="ST">ST</option>
          <option value="OBC">OBC</option>
        </select>

        <input type="date" id="filterDate" class="input-style" />
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end gap-3 pt-2">
        <button onclick="clearFilters()" class="px-4 py-2 bg-gray-300 rounded-xl hover:bg-gray-400">Clear</button>
        <button onclick="applyFilters()" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">Apply
          Filters</button>
      </div>
    </div>
  </div>
  <div id="filter-results" class="hidden mt-6 w-full">
    <div class="overflow-x-auto rounded-2xl shadow-lg border border-gray-200 bg-white">
      <table class="min-w-full text-sm text-left">
        <thead class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 font-semibold">
          <tr>
            <th class="px-6 py-3 border-b">S.No</th>
            <th class="px-6 py-3 border-b">Name</th>
            <th class="px-6 py-3 border-b">Roll No</th>
            <th class="px-6 py-3 border-b">Year</th>
            <th class="px-6 py-3 border-b">Semester</th>
            <th class="px-6 py-3 border-b">Status</th>
          </tr>
        </thead>
        <tbody id="filtered-data" class="text-gray-800">
          <!-- Filled by JS -->
        </tbody>
      </table>
    </div>
  </div>


  <!-- Main Dashboard View -->
  <section id="main-dashboard" class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6"
    data-aos="fade-up">
    <!-- Pending -->
    <div onclick="loadContent('pending')"
      class="status-card cursor-pointer bg-white shadow-md rounded-2xl p-5 flex items-center gap-4 border-l-8 border-yellow-400 hover:scale-105 transition">
      <div class="text-yellow-400 text-4xl"><i class="bi bi-hourglass-split"></i></div>
      <div>
        <p class="text-gray-500 text-sm">Pending Students</p>
        <h2 class="text-xl font-semibold text-gray-800" id="pendingCount">0</h2>
      </div>
    </div>
    <!-- Approved -->
    <div onclick="loadContent('approved')"
      class="status-card cursor-pointer bg-white shadow-md rounded-2xl p-5 flex items-center gap-4 border-l-8 border-green-500 hover:scale-105 transition">
      <div class="text-green-500 text-4xl"><i class="bi bi-check-circle-fill"></i></div>
      <div>
        <p class="text-gray-500 text-sm">Approved Students</p>
        <h2 class="text-xl font-semibold text-gray-800" id="approvedCount">0</h2>
      </div>
    </div>
    <!-- Rejected -->
    <div onclick="loadContent('rejected')"
      class="status-card cursor-pointer bg-white shadow-md rounded-2xl p-5 flex items-center gap-4 border-l-8 border-red-500 hover:scale-105 transition">
      <div class="text-red-500 text-4xl"><i class="bi bi-x-circle-fill"></i></div>
      <div>
        <p class="text-gray-500 text-sm">Rejected Students</p>
        <h2 class="text-xl font-semibold text-gray-800" id="rejectedCount">0</h2>
      </div>
    </div>
    <!-- Total -->
    <div onclick="loadContent('total')"
      class="status-card cursor-pointer bg-white shadow-md rounded-2xl p-5 flex items-center gap-4 border-l-8 border-indigo-500 hover:scale-105 transition">
      <div class="text-indigo-500 text-4xl"><i class="bi bi-people-fill"></i></div>
      <div>
        <p class="text-gray-500 text-sm">Total Students</p>
        <h2 class="text-xl font-semibold text-gray-800" id="totalCount">0</h2>
      </div>
    </div>
  </section>

  <!-- Filtered Student View -->
  <section id="filtered-view" class="hidden p-4 sm:p-6" data-aos="fade-up">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
      <h2 class="text-2xl font-semibold text-indigo-700" id="status-heading">Status View</h2>
      <button onclick="goBack1()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition">
        <i class="bi bi-arrow-left"></i> Back
      </button>
    </div>
    <div id="student-data" class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
      <!-- Table will be injected dynamically via JS -->
      <p class="text-center text-gray-500" id="loading-text">Loading data...</p>
    </div>
  </section>

  <!-- Filter Year Cards Section -->
  <section id="year-dashboard" class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6"
    data-aos="fade-up" data-aos-delay="300">
    <!-- All Students -->
    <div onclick="loadStudents('all')"
      class="bg-white shadow-md rounded-2xl p-5 flex items-center gap-4 border-l-8 border-indigo-500 cursor-pointer hover:shadow-lg transition">
      <div class="text-indigo-500 text-4xl">
        <i class="bi bi-people-fill"></i>
      </div>
      <div>
        <p class="text-gray-500 text-sm">All Students</p>
        <h2 class="text-lg font-semibold text-gray-800">View All</h2>
      </div>
    </div>

    <!-- 1st Year Students -->
    <div onclick="loadStudents('1')"
      class="bg-white shadow-md rounded-2xl p-5 flex items-center gap-4 border-l-8 border-yellow-500 cursor-pointer hover:shadow-lg transition">
      <div class="text-yellow-500 text-4xl">
        <i class="bi bi-1-circle-fill"></i>
      </div>
      <div>
        <p class="text-gray-500 text-sm">1st Year Students</p>
        <h2 class="text-lg font-semibold text-gray-800">View List</h2>
      </div>
    </div>

    <!-- 2nd Year Students -->
    <div onclick="loadStudents('2')"
      class="bg-white shadow-md rounded-2xl p-5 flex items-center gap-4 border-l-8 border-green-500 cursor-pointer hover:shadow-lg transition">
      <div class="text-green-500 text-4xl">
        <i class="bi bi-2-circle-fill"></i>
      </div>
      <div>
        <p class="text-gray-500 text-sm">2nd Year Students</p>
        <h2 class="text-lg font-semibold text-gray-800">View List</h2>
      </div>
    </div>

    <!-- 3rd Year Students -->
    <div onclick="loadStudents('3')"
      class="bg-white shadow-md rounded-2xl p-5 flex items-center gap-4 border-l-8 border-red-500 cursor-pointer hover:shadow-lg transition">
      <div class="text-red-500 text-4xl">
        <i class="bi bi-3-circle-fill"></i>
      </div>
      <div>
        <p class="text-gray-500 text-sm">3rd Year Students</p>
        <h2 class="text-lg font-semibold text-gray-800">View List</h2>
      </div>
    </div>
  </section>
  <!-- Students List Display -->
  <section id="studentList" class="p-4 sm:p-6 hidden" data-aos="fade-up" data-aos-delay="400">
    <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 relative">

      <!-- Header with Title and Actions -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
        <div class="flex items-center gap-3">
          <h2 class="text-xl font-semibold text-gray-700" id="studentListTitle">Student List</h2>
          <!-- Icons: Download & Print -->
          <button onclick="downloadStudentList()" class="text-indigo-500 hover:text-indigo-700 transition text-xl"
            data-aos="zoom-in" title="Download List">
            <i class="bi bi-download"></i>
          </button>
          <button onclick="printStudentList()" class="text-red-500 hover:text-red-700 transition text-xl"
            data-aos="zoom-in" title="Print List">
            <i class="bi bi-printer"></i>
          </button>
        </div>
        <button onclick="goBack()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition">
          <i class="bi bi-arrow-left"></i> Back
        </button>
      </div>

      <!-- Dynamic Table Container -->
      <div id="studentTable" class="overflow-x-auto" data-aos="fade-up" data-aos-delay="100">
        <table class="w-full text-sm text-left text-gray-800">
          <thead class="bg-gray-100 text-xs uppercase text-gray-600">
            <tr>
              <th class="px-4 py-2">Roll No</th>
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Email</th>
              <th class="px-4 py-2">Mobile</th>
              <th class="px-4 py-2">Semester</th>
              <th class="px-4 py-2">Category</th>
              <th class="px-4 py-2">Course Type</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody id="studentListBody">
          </tbody>
        </table>
      </div>

    </div>
  </section>


  <!-- Chart Section (Responsive) -->
  <section class="p-4 sm:p-6 pt-0" data-aos="fade-up" data-aos-delay="600">
    <div class="bg-white rounded-2xl shadow-md p-4 sm:p-6">
      <h2 class="text-xl font-semibold text-gray-700 mb-4">Student Status Overview</h2>

      <div class="relative h-72 overflow-x-auto">
        <canvas id="studentBarChart" class="absolute top-0 left-0 w-full h-full"></canvas>
      </div>
    </div>
  </section>

  <!-- Message Modal -->
  <div id="message-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 rounded shadow w-full max-w-md">
      <h2 class="text-xl font-semibold mb-4">📨 Send Message</h2>
      <input type="text" id="message-subject" class="w-full mb-2 border p-2 rounded" placeholder="Subject"
        autocomplete="off" />
      <textarea id="message-body" class="w-full h-32 border p-2 rounded"
        placeholder="Message (HTML allowed)"></textarea>
      <div class="mt-4 text-right">
        <button onclick="sendMessage()" class="bg-blue-600 text-white px-4 py-2 rounded">Send</button>
        <button onclick="closeModal('message-modal')" class="ml-2 text-gray-600 hover:text-red-500">Cancel</button>
      </div>
    </div>
  </div>

  <!-- Approve Confirmation Modal -->
  <div id="approve-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 rounded shadow w-full max-w-md">
      <h2 class="text-xl font-semibold text-green-600 mb-4">✅ Approve Student</h2>
      <p class="mb-4">Are you sure you want to approve this student?</p>
      <div class="flex justify-end">
        <button onclick="confirmApprove()" class="bg-green-600 text-white px-4 py-2 rounded">Yes, Approve</button>
        <button onclick="closeModal('approve-modal')" class="ml-3 text-gray-600 hover:text-red-500">Cancel</button>
      </div>
    </div>
  </div>


  <!-- Reject Reason Modal -->
  <div id="reject-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 rounded shadow w-full max-w-md">
      <h2 class="text-xl font-semibold mb-4 text-red-600">❌ Reject Student</h2>
      <p class="mb-2">Please enter a reason for rejection:</p>
      <textarea id="reject-reason" class="w-full h-24 border p-2 rounded" placeholder="Reason..."></textarea>
      <div class="mt-4 text-right">
        <button onclick="confirmReject()" class="bg-red-600 text-white px-4 py-2 rounded">Reject</button>
        <button onclick="closeModal('reject-modal')" class="ml-2 text-gray-600 hover:text-blue-500">Cancel</button>
      </div>
    </div>
  </div>
  <!-- Settings Modal -->
  <!-- Settings Modal -->
  <div id="settingsModal" class="fixed inset-0 bg-black/40 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md font-[Poppins] animate-fade-in relative">

      <!-- Close Button -->
      <button onclick="closeSettingsModal()"
        class="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-2xl font-bold">
        &times;
      </button>

      <!-- Modal Header -->
      <h2 class="text-xl font-semibold text-gray-800 mb-2">⚙️ Settings</h2>
      <p class="text-sm text-gray-600 mb-4">
        This action is <span class="text-amber-600 font-medium">irreversible</span>. Please confirm before proceeding.
      </p>

      <!-- Confirm Checkbox -->
      <div class="flex items-center gap-2 mb-5">
        <input type="checkbox" id="confirmTruncateCheckbox" class="accent-amber-500 cursor-pointer">
        <label for="confirmTruncateCheckbox" class="text-sm text-gray-700">I understand the consequences</label>
      </div>

      <!-- Action Buttons -->
      <div class="space-y-3">
        <button onclick="truncateTable('students')"
          class="w-full bg-amber-500 hover:bg-amber-400 text-white px-4 py-2 rounded text-sm transition disabled:opacity-50"
          disabled id="truncateStudentsBtn">
          🧨 Truncate Students & Challans
        </button>

        <button onclick="truncateTable('rejected_students')"
          class="w-full bg-amber-500 hover:bg-amber-400 text-white px-4 py-2 rounded text-sm transition disabled:opacity-50"
          disabled id="truncateRejectedBtn">
          🧨 Truncate Rejected Students
        </button>
      </div>
    </div>
  </div>


  <!-- Toast -->
  <div id="toast" class="fixed bottom-5 right-5 bg-gray-800 text-white px-4 py-2 rounded shadow hidden z-[9999]"></div>


  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init();
    function loadContent(status) {
      // UI switches
      document.getElementById('main-dashboard').classList.add('hidden');
      document.getElementById('filtered-view').classList.remove('hidden');
      document.getElementById('top-action-bar').classList.add('hidden');
      document.getElementById('student-data').innerHTML = `<p class="text-center text-gray-500" id="loading-text">Loading data...</p>`;

      const headingMap = {
        pending: "Pending Students",
        approved: "Approved Students",
        rejected: "Rejected Students",
        total: "All Students"
      };

      document.querySelectorAll('section:not(#filtered-view)').forEach(section => {
        section.style.display = 'none';
      });

      document.getElementById('status-heading').innerText = headingMap[status] || "Student List";

      // Endpoint logic
      const url = status === "rejected"
        ? `backend/get_rejected_students.php`
        : `backend/get_students_by_status.php?status=${status}`;

      fetch(url)
        .then(response => response.json())
        .then(data => {
          if (data.length === 0) {
            document.getElementById('student-data').innerHTML = `<p class="text-center text-gray-500 py-6">No students found for this status.</p>`;
            return;
          }

          let tableHTML = `<div class="overflow-x-auto"><table class="w-full text-sm text-left text-gray-700">`;

          // Rejected Students Table
          if (status === "rejected") {
            tableHTML += `
          <thead class="text-xs uppercase bg-gray-100 text-gray-600">
            <tr>
              <th class="px-4 py-2">Roll No</th>
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Semester</th>
              <th class="px-4 py-2">Category</th>
              <th class="px-4 py-2">Mobile</th>
              <th class="px-4 py-2">Email</th>
              <th class="px-4 py-2">Form Date</th>
              <th class"px-4 py-2">Reason</th> 
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">Rejected At</th>
            </tr>
          </thead>
          <tbody>
        `;

            data.forEach(student => {
              tableHTML += `
            <tr class="border-b">
              <td class="px-4 py-2">${student.roll_no}</td>
              <td class="px-4 py-2">${student.student_name}</td>
              <td class="px-4 py-2">${student.current_semester}</td>
              <td class="px-4 py-2">${student.category}</td>
              <td class="px-4 py-2">${student.mobile_no}</td>
              <td class="px-4 py-2">${student.email_id}</td>
              <td class="px-4 py-2">${student.exam_date}</td>
              <td class="px-4 py-2">${student.reason}</td>
              <td class="px-4 py-2 capitalize text-red-500">${student.status}</td>
              <td class="px-4 py-2">${student.rejected_at}</td>
            </tr>
          `;
            });

          } else {
            // Default table for pending/approved/total
            tableHTML += `
          <thead class="text-xs uppercase bg-gray-100 text-gray-600">
            <tr>
              <th class="px-4 py-2">Roll No</th>
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Email</th>
              <th class="px-4 py-2">Status</th>
            </tr>
          </thead>
          <tbody>
        `;

            data.forEach(student => {
              tableHTML += `
            <tr class="border-b">
              <td class="px-4 py-2">${student.roll_no}</td>
              <td class="px-4 py-2">${student.student_name}</td>
              <td class="px-4 py-2">${student.email_id}</td>
              <td class="px-4 py-2 capitalize">${student.status}</td>
            </tr>
          `;
            });
          }

          tableHTML += `</tbody></table></div>`;
          document.getElementById('student-data').innerHTML = tableHTML;
        })
        .catch(error => {
          console.error("Error loading students:", error);
          document.getElementById('student-data').innerHTML = `<p class="text-center text-red-500 py-6">Error loading student data.</p>`;
        });
    }




    function goBack1() {
      document.getElementById('filtered-view').classList.add('hidden');
      document.getElementById('year-dashboard').classList.remove('hidden');
      document.getElementById('main-dashboard').classList.remove('hidden');
      document.getElementById('top-action-bar').classList.remove('hidden');
      document.querySelectorAll('section:not(#filtered-view)').forEach(section => {
        section.style.display = '';
      });
    }
    function loadStudents(year) {
      document.getElementById('studentList').classList.remove('hidden');
      document.getElementById('top-action-bar').classList.add('hidden');

      document.getElementById('studentListTitle').innerText =
        year === 'all' ? 'All Students' : `Year ${year} Students`;

      document.querySelectorAll('section:not(#studentList)').forEach(section => {
        section.style.display = 'none';
      });

      document.getElementById('studentTable').innerHTML = `
    <p class="text-center text-gray-500 font-[Poppins] py-4">Loading students...</p>
  `;

      fetch(`backend/get_students_by_year.php?year=${year}`)
        .then(response => response.json())
        .then(data => {
          if (!data || data.length === 0) {
            document.getElementById('studentTable').innerHTML = `
          <p class="text-center text-gray-500 font-[Poppins] py-6">No students found for this year.</p>
        `;
            return;
          }

          // Sort by last two digits of roll_no
          data.sort((a, b) => {
            const lastA = parseInt(a.roll_no.slice(-2));
            const lastB = parseInt(b.roll_no.slice(-2));
            return lastA - lastB;
          });

          // 💡 Unique alphabet letters and form years
          const letters = [...new Set(data.map(stu => stu.student_name?.[0]?.toUpperCase()).filter(Boolean))].sort();
          const formYears = [...new Set(data.map(stu => new Date(stu.exam_date).getFullYear()))].sort((a, b) => b - a);

          let filterUI = `
        <div class="flex flex-col sm:flex-row flex-wrap justify-between items-start sm:items-center gap-4 mb-4 font-[Poppins]">
          <div>
            <label class="text-sm text-gray-600">🔤 Filter by Name:</label>
            <select id="alphabetFilter" class="ml-2 border px-2 py-[5px] rounded text-sm">
              <option value="all">All</option>
              ${letters.map(l => `<option value="${l}">${l}</option>`).join('')}
            </select>
          </div>    
          <div>
            <label class="text-sm text-gray-600">📅 Filter by Form Year:</label>
            <select id="formYearFilter" class="ml-2 border px-2 py-[5px] rounded text-sm">
              <option value="all">All</option>
              ${formYears.map(y => `<option value="${y}">${y}</option>`).join('')}
            </select>
          </div>    
          <div>
            <input type="text" id="studentSearchInput" placeholder="🔍 Search..." class="border px-3 py-[6px] rounded text-sm w-60" />
          </div>
        </div>
      `;

          let tableHTML = `
        ${filterUI}
        <div class="overflow-x-auto aos-init" data-aos="fade-up" data-aos-duration="700">
          <table class="min-w-full border border-gray-200 text-sm text-gray-700 font-[Poppins] shadow-sm">
            <thead class="bg-[#f5f7fa] text-[#4b5563] text-xs uppercase tracking-wider">
              <tr>
                <th class="px-4 py-2 border">#</th>
                <th class="px-4 py-2 border">Roll No</th>
                <th class="px-4 py-2 border">Name</th>
                <th class="px-4 py-2 border">Semester</th>
                <th class="px-4 py-2 border">Year</th>
                <th class="px-4 py-2 border">Form Year</th>
                <th class="px-4 py-2 border">Email</th>
                <th class="px-4 py-2 border">Status</th>
                <th class="px-4 py-2 border text-center">Actions</th>
              </tr>
            </thead>
            <tbody id="studentListBody">
      `;

          data.forEach((student, index) => {
            const semester = student.current_semester || '';
            const formYear = new Date(student.exam_date).getFullYear();

            let calcYear = "—";
            if (semester.includes("1st") || semester.includes("2nd")) calcYear = "1st Year";
            else if (semester.includes("3rd") || semester.includes("4th")) calcYear = "2nd Year";
            else if (semester.includes("5th") || semester.includes("6th")) calcYear = "3rd Year";

            tableHTML += `
          <tr class="hover:bg-[#f9fafb] transition-all duration-200 ease-in" data-letter="${student.student_name?.[0]?.toUpperCase()}" data-formyear="${formYear}">
            <td class="px-4 py-2 border">${index + 1}</td>
            <td class="px-4 py-2 border">${student.roll_no}</td>
            <td class="px-4 py-2 border">${student.student_name}</td>
            <td class="px-4 py-2 border">${semester}</td>
            <td class="px-4 py-2 border">${calcYear}</td>
            <td class="px-4 py-2 border">${formYear}</td>
            <td class="px-4 py-2 border">${student.email_id}</td>
            <td class="px-4 py-2 border capitalize">${student.status}</td>
            <td class="px-4 py-2 border text-center space-x-1">
              <button class="btn-view text-blue-600 border border-blue-600 rounded px-2 py-1 text-xs hover:bg-blue-50 hover:scale-[1.02] transition" data-id="${student.id}">View</button>
              ${student.status === 'pending'
                ? `
                    <button class="btn-approve text-green-600 border border-green-600 rounded px-2 py-1 text-xs hover:bg-green-50 hover:scale-[1.02] transition" data-id="${student.id}">Approve</button>
                    <button class="btn-reject text-red-600 border border-red-600 rounded px-2 py-1 text-xs hover:bg-red-50 hover:scale-[1.02] transition" data-id="${student.id}">Reject</button>
                  `
                : ''
              }
              <button class="btn-message text-indigo-600 border border-indigo-600 rounded px-2 py-1 text-xs hover:bg-indigo-50 hover:scale-[1.02] transition" data-id="${student.id}">Message</button>
              <button class="btn-edit text-orange-500 border border-orange-500 rounded px-2 py-1 text-xs hover:bg-orange-50 hover:scale-[1.02] transition" onclick="toggleEditAccess(${student.id}, ${student.can_edit || 0}, this)">${(student.can_edit == 1) ? 'Disable Edit' : 'Enable Edit'}</button>
            </td>

          </tr>
        `;
          });

          tableHTML += `</tbody></table></div>`;
          document.getElementById('studentTable').innerHTML = tableHTML;

          // 🔠 Alphabet Filter
          document.getElementById('alphabetFilter').addEventListener('change', applyFilters);
          document.getElementById('formYearFilter').addEventListener('change', applyFilters);

          // 🔍 Search
          document.getElementById('studentSearchInput').addEventListener('input', applyFilters);

          function applyFilters() {
            const selectedLetter = document.getElementById('alphabetFilter').value.toUpperCase();
            const selectedYear = document.getElementById('formYearFilter').value;
            const keyword = document.getElementById('studentSearchInput').value.toLowerCase();

            document.querySelectorAll('#studentListBody tr').forEach(row => {
              const nameLetter = row.dataset.letter;
              const formYear = row.dataset.formyear;
              const rowText = row.innerText.toLowerCase();

              const matchesLetter = (selectedLetter === 'ALL' || nameLetter === selectedLetter);
              const matchesYear = (selectedYear === 'all' || formYear === selectedYear);
              const matchesSearch = rowText.includes(keyword);

              row.style.display = (matchesLetter && matchesYear && matchesSearch) ? '' : 'none';
            });
          }

          attachStudentActionListeners();
        })
        .catch(error => {
          console.error('Error fetching students:', error);
          document.getElementById('studentTable').innerHTML = `
        <p class="text-center text-red-500 font-[Poppins] py-6">Error loading student data.</p>
      `;
        });
    }


    function goBack() {
      document.getElementById('studentList').classList.add('hidden');
      document.getElementById('top-action-bar').classList.remove('hidden');
      document.querySelectorAll('section:not(#studentList)').forEach(section => {
        section.style.display = '';
      });
    }
    document.addEventListener("DOMContentLoaded", () => {
      fetch('backend/get_status_overview.php')
        .then(response => response.json())
        .then(data => {
          const chartCanvas = document.getElementById('studentBarChart');
          if (!chartCanvas) return;

          const ctx = chartCanvas.getContext('2d');

          new Chart(ctx, {
            type: 'bar',
            data: {
              labels: ['Pending', 'Approved', 'Rejected', 'Total'],
              datasets: [{
                label: 'Students',
                data: [
                  data?.Pending ?? 0,
                  data?.Approved ?? 0,
                  data?.Rejected ?? 0,
                  data?.Total ?? ((data?.Pending ?? 0) + (data?.Approved ?? 0)) // ✅ Use Total from PHP
                ],
                backgroundColor: ['#facc15', '#22c55e', '#ef4444', '#6366f1'],
                borderRadius: 12,
                barThickness: 50
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                y: {
                  beginAtZero: true,
                  min: 0,
                  max: 100,
                  ticks: {
                    stepSize: 20,
                    color: '#6b7280', // gray-500
                    font: {
                      family: 'Poppins',
                      size: 12
                    }
                  },
                  grid: {
                    color: '#e5e7eb' // gray-200
                  }
                },
                x: {
                  ticks: {
                    color: '#6b7280',
                    font: {
                      family: 'Poppins',
                      size: 12
                    }
                  },
                  grid: {
                    display: false
                  }
                }
              },
              plugins: {
                legend: {
                  display: false
                },
                tooltip: {
                  enabled: true,
                  titleFont: {
                    family: 'Poppins',
                    size: 13,
                    weight: 'bold'
                  },
                  bodyFont: {
                    family: 'Poppins',
                    size: 12
                  }
                }
              }
            }
          });
        })
        .catch(error => {
          console.error("Chart Load Error:", error);
          document.getElementById('studentBarChart').parentElement.innerHTML =
            `<p class="text-red-500 text-center py-4">Failed to load chart.</p>`;
        });
    });

    function toggleEditAccess(id, currentStatus, btn) {
      const action = currentStatus == 1 ? 'disable' : 'enable';
      if (!confirm(`Are you sure you want to ${action} edit access for this student?`)) return;

      fetch('backend/toggle_edit_access.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, action: action })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            // Update UI
            const newStatus = currentStatus == 1 ? 0 : 1;
            btn.innerText = newStatus == 1 ? 'Disable Edit' : 'Enable Edit';
            // Update the onclick attribute to reflect the new status
            btn.setAttribute('onclick', `toggleEditAccess(${id}, ${newStatus}, this)`);

            // Update styling based on state
            /*
            if (newStatus == 1) {
                btn.classList.add('bg-orange-100'); 
            } else {
                btn.classList.remove('bg-orange-100');
            }
            */

            // Show toast
            showToast(`Edit access ${action}d!`, 'success');
          } else {
            showToast(data.message || 'Failed to update access', 'error');
          }
        })
        .catch(err => {
          console.error(err);
          showToast('Server error', 'error');
        });
    }

    function showToast(message, type = 'info') {
      const toast = document.getElementById('toast');
      toast.innerText = message;
      toast.className = `fixed bottom-5 right-5 px-4 py-2 rounded shadow z-[9999] text-white ${type === 'error' ? 'bg-red-600' : 'bg-green-600'}`;
      toast.classList.remove('hidden');
      setTimeout(() => {
        toast.classList.add('hidden');
      }, 3000);
    }


    function toggleFilterModal() {
      document.getElementById('filterModal').classList.toggle('hidden');
    }

    function clearFilters() {
      // Clear inputs
      document.getElementById('filterSemester').value = '';
      document.getElementById('filterCategory').value = '';
      document.getElementById('filterDate').value = '';

      // Hide results table if it's shown
      document.getElementById("filter-results").classList.add("hidden");
      document.getElementById("filtered-data").innerHTML = '';
    }

    function applyFilters() {
      const semester = document.getElementById('filterSemester').value.trim();
      const category = document.getElementById('filterCategory').value.trim();
      const date = document.getElementById('filterDate').value.trim();

      // Check if all filters are empty
      if (!semester && !category && !date) {
        // Nothing selected, hide the table
        document.getElementById("filter-results").classList.add("hidden");
        document.getElementById("filtered-data").innerHTML = '';
        toggleFilterModal();
        return;
      }

      const filters = { semester, category, date };

      const xhr = new XMLHttpRequest();
      xhr.open("POST", "backend/filters_students.php", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

      const params = Object.keys(filters)
        .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(filters[key])}`)
        .join("&");

      xhr.onload = function () {
        if (xhr.status === 200) {
          // Show table only if some filter was applied
          document.getElementById("filter-results").classList.remove("hidden");
          document.getElementById("filtered-data").innerHTML = xhr.responseText;
        } else {
          console.error("Failed to fetch filtered data.");
        }
      };

      xhr.send(params);
      toggleFilterModal();
    }

    function searchStudents() {
      const query = document.getElementById('searchQuery').value.trim();

      // Skip empty search
      if (query === '') return;

      fetch(`backend/search_students.php?query=${encodeURIComponent(query)}`)
        .then(res => res.text())
        .then(data => {
          document.getElementById('filtered-view').classList.remove('hidden');
          document.getElementById('main-dashboard').classList.add('hidden');
          document.getElementById('year-dashboard').classList.add('hidden');
          document.getElementById('student-data').innerHTML = data;
          document.getElementById('status-heading').innerText = `Search Results for "${query}"`;
        })
        .catch(err => {
          console.error('Search failed:', err);
        });
    }
    document.getElementById('searchQuery').addEventListener('input', () => {
      if (document.getElementById('searchQuery').value.trim() === '') {
        document.getElementById('filtered-view').classList.add('hidden');
        document.getElementById('main-dashboard').classList.remove('hidden');
        document.getElementById('year-dashboard').classList.remove('hidden');
        document.getElementById('student-data').innerHTML = '';
        document.getElementById('status-heading').innerText = '';
      }
    });
    document.addEventListener("DOMContentLoaded", () => {
      fetch("backend/dashboard_stats.php")
        .then(res => res.json())
        .then(data => {
          document.getElementById("pendingCount").textContent = data.pending;
          document.getElementById("approvedCount").textContent = data.approved;
          document.getElementById("rejectedCount").textContent = data.rejected;
          document.getElementById("totalCount").textContent = data.total;
        })
        .catch(err => {
          console.error("Error loading dashboard stats:", err);
        });
    });
    let currentApproveId = null;
    let currentRejectId = null;
    let currentMessageId = null;

    function attachStudentActionListeners() {
      document.querySelectorAll(".btn-view").forEach(button => {
        button.addEventListener("click", () => {
          const studentId = button.getAttribute("data-id");
          if (studentId) {
            // Redirect to the details page
            window.location.href = `exam_details.php?id=${studentId}`;
          }
        });
      });
      document.querySelectorAll('.btn-approve').forEach(btn => {
        btn.addEventListener('click', () => {
          currentApproveId = btn.getAttribute('data-id');
          openModal('approve-modal');
        });
      });

      document.querySelectorAll('.btn-reject').forEach(btn => {
        btn.addEventListener('click', () => {
          currentRejectId = btn.getAttribute('data-id');
          document.getElementById('reject-reason').value = '';
          openModal('reject-modal');
        });
      });

      document.querySelectorAll('.btn-message').forEach(btn => {
        btn.addEventListener('click', () => {
          currentMessageId = btn.getAttribute('data-id');
          document.getElementById('message-subject').value = '';
          document.getElementById('message-body').value = '';
          openModal('message-modal');
        });
      });
    }

    // Modal handlers
    function openModal(id) {
      document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
      document.getElementById(id).classList.add('hidden');
    }
    function sendMessage() {
      const subjectInput = document.getElementById('message-subject');
      const messageInput = document.getElementById('message-body');
      const sendBtn = document.querySelector("#message-modal button.bg-blue-600");

      const subject = subjectInput?.value.trim();
      const message = messageInput?.value.trim();

      if (!subject || !message) {
        showToast("⚠️ Fill both subject and message.", "error");
        return;
      }

      // Disable button + show spinner
      sendBtn.disabled = true;
      sendBtn.innerText = "Sending...";

      // ✅ Use dedicated message endpoint
      fetch('backend/send_custom_mail.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id: currentMessageId,
          subject,
          message
        })
      })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            showToast("✅ Message sent successfully!", "success");
            closeModal('message-modal');
            subjectInput.value = "";
            messageInput.value = "";
          } else {
            const errMsg = data?.message || "❌ Failed to send message.";
            showToast(errMsg, "error");
          }
        })
        .catch(err => {
          console.error("[JS] sendMessage() Error:", err);
          showToast("⚠️ Something went wrong while sending the message.", "error");
        })
        .finally(() => {
          sendBtn.disabled = false;
          sendBtn.innerText = "Send";
        });
    }

    // Approve Student with Reason
    function confirmApprove() {
      if (!currentApproveId) return;
      window.location.href = `backend/approve_reject_student.php?action=approve&id=${currentApproveId}`;
    }

    // Reject Student with Reason
    function confirmReject() {
      const reason = document.getElementById('reject-reason').value.trim();
      if (!reason) return showToast("⚠️ Please enter a reason.", "error");

      fetch(`backend/approve_reject_student.php?action=reject&id=${currentRejectId}&reason=${encodeURIComponent(reason)}`)
        .then(res => res.text())
        .then(response => {
          showToast("❌ Student rejected.", "error");
          closeModal('reject-modal');
          setTimeout(() => location.reload(), 1500);
        })
        .catch(() => showToast("⚠️ Failed to reject student.", "error"));
    }


    // Toasts
    function showToast(msg, type = "info") {
      const toast = document.getElementById("toast");
      toast.innerText = msg;
      toast.classList.remove("hidden");
      toast.classList.remove("bg-red-600", "bg-green-600", "bg-blue-600");
      toast.classList.add(type === 'success' ? "bg-green-600" : type === 'error' ? "bg-red-600" : "bg-blue-600");

      setTimeout(() => toast.classList.add("hidden"), 3000);
    }
    let selectedTable = '';

    function openSettingsModal() {
      document.getElementById('settingsModal').classList.remove('hidden');
    }

    function closeSettingsModal() {
      document.getElementById('settingsModal').classList.add('hidden');
      document.getElementById('confirmTruncateCheckbox').checked = false;
      document.getElementById('truncateStudentsBtn').disabled = true;
      document.getElementById('truncateRejectedBtn').disabled = true;
    }

    document.getElementById('confirmTruncateCheckbox').addEventListener('change', function () {
      const isChecked = this.checked;
      document.getElementById('truncateStudentsBtn').disabled = !isChecked;
      document.getElementById('truncateRejectedBtn').disabled = !isChecked;
    });

    function truncateTable(table) {
      if (!confirm("⚠️ Are you sure? This will delete all data from the selected table.")) return;

      fetch(`backend/truncate_tables.php?table=${table}`)
        .then(res => res.text())
        .then(response => {
          const isSuccess = response.includes("success");
          showToast(
            isSuccess ? "✅ Table truncated successfully!" : "❌ Failed to truncate table.",
            isSuccess
          );
          closeSettingsModal();
        })
        .catch(() => {
          showToast("⚠️ Something went wrong while truncating.", false);
        });
    }
    // Show toast - optional, for UX
    function showToast(message) {
      const toast = document.createElement("div");
      toast.textContent = message;
      toast.className = "fixed top-4 right-4 bg-green-600 text-white py-2 px-4 rounded shadow-md z-50 animate-fadeIn";
      document.body.appendChild(toast);
      setTimeout(() => toast.remove(), 3000);
    }

    // 🚀 DOWNLOAD PDF
    function downloadStudentList() {
      const link = document.createElement('a');
      link.href = "backend/listmaker/generate_student_pdf.php";
      link.download = "Student_List.pdf"; // This only works if headers are set correctly in PHP
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      showToast("📄 Student list is being downloaded...");
    }

    // 🖨️ PRINT PDF
    function printStudentList() {
      const printWindow = window.open("backend/listmaker/generate_student_pdf.php", "_blank");

      if (!printWindow) {
        alert("⚠️ Popup blocked! Please allow popups for this site.");
        return;
      }

      const checkReady = setInterval(() => {
        if (printWindow.document.readyState === 'complete') {
          clearInterval(checkReady);
          printWindow.focus();
          printWindow.print();
          showToast("🖨️ Sending to printer...");
        }
      }, 500);
    }
  </script>
</body>

</html>
