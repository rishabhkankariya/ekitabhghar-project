<?php
session_start();
require_once 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION["admin_id"]) || !isset($_SESSION["username"])) {
    echo "<script>alert('Unauthorized access! Please login first.'); window.location.href='../library_login.html';</script>";
    exit();
}

// Fetch admin details
$admin_id = $_SESSION["admin_id"];
$username = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Question Papers</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style type="text/css">
        /* General Page Styling */
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f4f4;
    margin: 0;
    padding: 20px;
}

/* Heading */
h2 {
    text-align: center;
    color: #333;
}

/* Form Styling */
form {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 500px;
    margin: 20px auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

form select, 
form input {
    width: 95%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

/* Button Styling */
button {
    padding: 12px;
    border: none;
    border-radius: 5px;
    background: #007BFF;
    color: white;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #0056b3;
}

/* Table Styling */
table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

table th, table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: center;
}

table th {
    background: #007BFF;
    color: white;
}
.back-btn {
    top: 15px;
    left: 15px;
    background-color: #007bff; /* Blue color */
    color: white;
    text-decoration: none;
    padding: 8px 15px;
    border-radius: 5px;
    font-size: 16px;
    font-weight: bold;
    transition: 0.3s;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
}

.back-btn:hover {
    background-color: #0056b3; /* Darker blue */
    box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.3);
    transform: scale(1.05);
}

/* Responsive Design */
@media (max-width: 600px) {
    form {
        max-width: 100%;
    }

    table {
        font-size: 14px;
    }
}

    </style>
</head>
<body>
    <a href="../library_dashboard.php" class="back-btn">⬅ Back</a>
    <h2>Manage Question Papers</h2>

    <!-- Add New Question Paper Form -->
    <form id="addQuestionForm" enctype="multipart/form-data">
    <!-- Year Selection -->
    <select id="year" name="year" required>
        <option value="">Select Year</option>
        <!-- Filled dynamically -->
    </select>

    <!-- Semester Selection -->
    <select id="semester" name="semester" required>
        <option value="">Select Semester</option>
        <!-- Filled dynamically -->
    </select>

    <!-- Subject Name -->
    <input type="text" id="subject_name" name="subject_name" placeholder="Subject Name" required>

    <!-- Upload PDF -->
    <input type="file" id="pdf" name="pdf" accept=".pdf" required>

    <!-- Submit Button -->
    <button type="submit" id="submitBtn">Add Question Paper</button>
</form>


    <hr>

    <!-- Display Question Papers -->
    <table border="1">
        <thead>
            <tr>
                <th>Year</th>
                <th>Semester</th>
                <th>Subject</th>
                <th>PDF</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="questionPapersTable">
            <!-- Filled dynamically -->
        </tbody>
    </table>

    <script>
        $(document).ready(function () {
            fetchYearsSemesters();
            fetchQuestionPapers();

            // Fetch Years & Semesters
            function fetchYearsSemesters() {
                $.post("manage_question_paper.php", { action: "fetch_years_semesters" }, function (response) {
                    if (response.status === "success") {
                        let yearOptions = '<option value="">Select Year</option>';
                        response.years.forEach(year => {
                            yearOptions += `<option value="${year}">${year}</option>`;
                        });
                        $("#year").html(yearOptions);

                        let semesterOptions = '<option value="">Select Semester</option>';
                        response.semesters.forEach(semester => {
                            semesterOptions += `<option value="${semester}">${semester}</option>`;
                        });
                        $("#semester").html(semesterOptions);
                    }
                }, "json");
            }

            // Fetch Question Papers
            function fetchQuestionPapers() {
                $.post("manage_question_paper.php", { action: "fetch" }, function (response) {
                    if (response.status === "success") {
                        let html = "";
                        response.data.forEach(qp => {
                            html += `
                                <tr data-id="${qp.id}">
                                    <td>${qp.year}</td>
                                    <td>${qp.semester}</td>
                                    <td>
                                        <input type="text" value="${qp.subject_name}" class="editSubject">
                                    </td>
                                    <td><a href="${qp.pdf}" target="_blank">View PDF</a></td>
                                    <td>
                                        <input type="file" class="editPdf" accept=".pdf">
                                        <button class="updateBtn">Update</button>
                                        <button class="deleteBtn">Delete</button>
                                    </td>
                                </tr>`;
                        });
                        $("#questionPapersTable").html(html);
                    } else {
                        alert("Failed to fetch question papers.");
                    }
                }, "json");
            }

            // Add Question Paper
            $("#addQuestionForm").submit(function (e) {
                e.preventDefault();
                let formData = new FormData(this);
                formData.append("action", "add");

                $.ajax({
                    url: "manage_question_paper.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function (response) {
                        alert(response.message);
                        if (response.status === "success") {
                            fetchQuestionPapers();
                            $("#addQuestionForm")[0].reset();
                        }
                    }
                });
            });

            // Update Question Paper
            $(document).on("click", ".updateBtn", function () {
                let row = $(this).closest("tr");
                let id = row.data("id");
                let newSubject = row.find(".editSubject").val();
                let newPdf = row.find(".editPdf")[0].files[0];

                let formData = new FormData();
                formData.append("action", "update");
                formData.append("id", id);
                formData.append("subject_name", newSubject);
                if (newPdf) formData.append("pdf", newPdf);

                $.ajax({
                    url: "manage_question_paper.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function (response) {
                        alert(response.message);
                        if (response.status === "success") fetchQuestionPapers();
                    }
                });
            });

            // Delete Question Paper
            $(document).on("click", ".deleteBtn", function () {
                let row = $(this).closest("tr");
                let id = row.data("id");

                if (confirm("Are you sure you want to delete this question paper?")) {
                    $.post("manage_question_paper.php", { action: "delete", id: id }, function (response) {
                        alert(response.message);
                        if (response.status === "success") fetchQuestionPapers();
                    }, "json");
                }
            });
        });
    </script>
</body>
</html>
