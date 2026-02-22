<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("Unauthorized access!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Custom Styles -->
    <style>/* Global Styles */
body {
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 150vh;
    background: linear-gradient(135deg, #e3f2fd, #ffffff); /* Light Blue & White */
    font-family: 'Poppins', sans-serif;
    color: #333;
}

/* Centered Full-Screen Container */
.container {
    width: 100%;
    max-width: 450px;
    padding: 30px;
}

/* Card with Clean Look */
.card {
    background: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: 0.3s ease-in-out;
    animation: fadeIn 1s ease-in-out;
}

/* Form Fields */
.form-control {
    background: rgba(230, 240, 255, 0.7);
    border: 1px solid #b0c4de;
    padding: 12px;
    border-radius: 8px;
    transition: all 0.3s;
    color: #333;
    font-size: 16px;
}

/* Form Field Focus Effects */
.form-control:focus {
    background: rgba(230, 240, 255, 1);
    border: 1px solid #1E3A8A;
    outline: none;
    box-shadow: 0 0 10px rgba(30, 58, 138, 0.4);
}

/* Buttons */
.btn-custom {
    padding: 12px;
    font-weight: 600;
    border-radius: 8px;
    width: 100%;
    transition: 0.3s ease-in-out;
}

/* Primary Button (Dark Blue) */
.btn-primary {
    background: #1E3A8A;
    border: none;
    color: white;
    box-shadow: 0 4px 10px rgba(30, 58, 138, 0.4);
}

.btn-primary:hover {
    background: #152c66;
    box-shadow: 0 6px 15px rgba(30, 58, 138, 0.6);
}

/* Secondary Button (Light Blue & White) */
.btn-secondary {
    background: #b0c4de;
    border: none;
    color: #1E3A8A;
    box-shadow: 0 4px 8px rgba(176, 196, 222, 0.6);
}

.btn-secondary:hover {
    background: #9ab5d3;
    box-shadow: 0 6px 12px rgba(176, 196, 222, 0.8);
}

/* Logout Button (Positioned at Top-Right Corner) */
.btn-logout {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    border-radius: 8px;
    background: #d9534f;
    border: none;
    color: white;
    box-shadow: 0 3px 8px rgba(217, 83, 79, 0.4);
    font-size: 20px;
    transition: 0.3s ease-in-out;
    overflow: hidden;
}

/* Hover Effect */
.btn-logout:hover {
    background: #c9302c;
    box-shadow: 0 5px 12px rgba(217, 83, 79, 0.6);
}

/* Walking Animation */
.btn-logout:hover .logout-icon {
    animation: walkOut 1s forwards;
}

/* Walking Out Keyframes */
@keyframes walkOut {
    0% {
        transform: translateX(0);
        opacity: 1;
    }
    50% {
        transform: translateX(10px);
        opacity: 0.8;
    }
    100% {
        transform: translateX(30px);
        opacity: 0;
    }
}

/* 🔹 Small Screen Adjustments */
@media (max-width: 425px) {
    .btn-logout {
        width: 40px;
        height: 40px;
        font-size: 18px;
        top: 8px;
        right: 8px;
    }

    .btn-logout:hover .logout-icon {
        animation: walkOutSmall 1s forwards;
    }
}

/* 🔹 Extra Small Screens (Max Width: 375px) */
@media (max-width: 375px) {
    .btn-logout {
        width: 38px;
        height: 38px;
        font-size: 16px;
        top: 7px;
        right: 7px;
    }

    .btn-logout:hover .logout-icon {
        animation: walkOutXSmall 1s forwards;
    }
}

/* 🔹 Extra Small Screens (Max Width: 320px) */
@media (max-width: 320px) {
    .btn-logout {
        width: 30px;
        height: 30px;
        font-size: 15px;
        top: 1px;
        right: 1px;
    }

    .btn-logout:hover .logout-icon {
        animation: walkOutXXSmall 1s forwards;
    }
}

/* Adjusted Walking Out Animation for Smaller Screens */
@keyframes walkOutSmall {
    0% {
        transform: translateX(0);
        opacity: 1;
    }
    50% {
        transform: translateX(8px);
        opacity: 0.8;
    }
    100% {
        transform: translateX(25px);
        opacity: 0;
    }
}

@keyframes walkOutXSmall {
    0% {
        transform: translateX(0);
        opacity: 1;
    }
    50% {
        transform: translateX(6px);
        opacity: 0.8;
    }
    100% {
        transform: translateX(20px);
        opacity: 0;
    }
}

@keyframes walkOutXXSmall {
    0% {
        transform: translateX(0);
        opacity: 1;
    }
    50% {
        transform: translateX(5px);
        opacity: 0.8;
    }
    100% {
        transform: translateX(18px);
        opacity: 0;
    }
}

/* Responsive Design */
@media (max-width: 600px) {
    .container {
        max-width: 95%;
    }
    
    .card {
        padding: 20px;
    }
}

/* Fade In Animation */
@keyframes fadeIn {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}


    </style>
</head>
<body>

<div class="container">
    <div class="card position-relative">
        
        <!-- Logout Button (Top-Right Corner) -->
        <a href="logout.php" class="btn btn-danger btn-logout">
            <span class="logout-icon">🚶‍♂️</span> 🚪
        </a>

        <!-- Heading -->
        <h2 class="mb-3">Add New Admin</h2>

        <!-- Form for Library Admin -->
        <form action="../library_admin_insert.php" method="POST" class="mb-3">
            <div class="mb-3">
                <label class="form-label">Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-custom w-100">Add Library Admin</button>
        </form>

        <!-- Form for Regular Admin -->
        <form action="../admin/admin_insert.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-custom w-100">Add Admin</button>
        </form>

        <!-- Back Button (Below Forms) -->
        <div class="mt-3 text-center">
            <a href="javascript:history.back()" class="btn btn-secondary btn-custom w-45">⬅ Back</a>
        </div>

    </div>
</div>


<!-- JavaScript for Auto Logout -->
<script>
    let timeout;

    function resetTimer() {
        clearTimeout(timeout);
        timeout = setTimeout(logout, 1000000); // 5 seconds
    }

    function logout() {
        window.location.href = "logout.php"; 
    }

    // Reset timer on user activity
    document.addEventListener("mousemove", resetTimer);
    document.addEventListener("keydown", resetTimer);
    document.addEventListener("scroll", resetTimer);
    document.addEventListener("click", resetTimer);

    // Detect tab change
    document.addEventListener("visibilitychange", function () {
        if (document.hidden) {
            logout(); // Logout if user leaves tab
        }
    });

    resetTimer(); // Start countdown
</script>

</body>
</html>
    
