<?php
session_start();
// 0. Centralized Mail Helper
require_once '../config/send_mail.php';
// 1. Centralized Connection
include 'connection.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["captcha"]) || !isset($_SESSION["captcha"]) || !isset($_SESSION["captcha_time"])) {
        $message = "CAPTCHA validation failed.";
        $messageType = "error";
    } else {
        $userCaptcha = strtoupper(trim($_POST["captcha"]));
        $storedCaptcha = $_SESSION["captcha"];
        $captchaAge = time() - $_SESSION["captcha_time"]; // Calculate age

        // Check if CAPTCHA is expired (4 minutes = 240 seconds)
        if ($captchaAge > 600) {
            unset($_SESSION["captcha"]);
            unset($_SESSION["captcha_time"]);
            $message = "CAPTCHA expired. Please try again.";
            $messageType = "error";
        } elseif ($userCaptcha !== $storedCaptcha) {
            $message = "Incorrect CAPTCHA. Please try again.";
            $messageType = "error";
        } else {
            // One-time CAPTCHA use
            unset($_SESSION["captcha"]);
            unset($_SESSION["captcha_time"]);

            $message = "CAPTCHA verified successfully!";
            $messageType = "success";

            $uploadDir = "uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $savedSignature = "";

            // Handle Signature Upload
            if (isset($_POST["signature_type"]) && $_POST["signature_type"] === "upload" && !empty($_FILES["student_signature"]["name"])) {
                $fileTmpPath = $_FILES["student_signature"]["tmp_name"];
                $fileName = time() . "_" . basename($_FILES["student_signature"]["name"]);
                $fileDest = $uploadDir . $fileName;

                $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
                if (!in_array($_FILES["student_signature"]["type"], $allowedTypes) || $_FILES["student_signature"]["size"] > 2 * 1024 * 1024) {
                    $message = "Invalid file. Only JPG, JPEG, PNG allowed & max size 2MB.";
                    $messageType = "error";
                } elseif (move_uploaded_file($fileTmpPath, $fileDest)) {
                    $savedSignature = $fileDest;
                }
            }

            // Handle Typed Signature
            if (isset($_POST["signature_type"]) && $_POST["signature_type"] === "typed" && !empty($_POST["typedSignature"])) {
                $typedSignature = trim($_POST["typedSignature"]);
                $fileName = $uploadDir . "typed_signature_" . time() . ".png";

                $width = 400;
                $height = 100;
                $image = imagecreatetruecolor($width, $height);

                // Set white background
                $white = imagecolorallocate($image, 255, 255, 255);
                imagefilledrectangle($image, 0, 0, $width, $height, $white);

                // Set text color (black)
                $textColor = imagecolorallocate($image, 0, 0, 0);

                $font = __DIR__ . "/fonts/DancingScript-Regular.ttf";
                if (!file_exists($font)) {
                    $message = "Font file not found! Please check the path.";
                    $messageType = "error";
                }

                $fontSize = 30;
                $x = 50;
                $y = 60;

                imagettftext($image, $fontSize, 0, $x, $y, $textColor, $font, $typedSignature);

                // Save image as PNG
                imagepng($image, $fileName);
                imagedestroy($image);

                $savedSignature = $fileName;
            }

            // Handle Drawn Signature
            if (isset($_POST["signature_type"]) && $_POST["signature_type"] === "draw" && !empty($_POST["signatureImage"])) {
                $imageData = str_replace("data:image/png;base64,", "", $_POST["signatureImage"]);
                $imageData = base64_decode($imageData);
                $fileName = $uploadDir . "drawn_signature_" . time() . ".png";
                file_put_contents($fileName, $imageData);
                $savedSignature = $fileName;
            }
            $uploadDirr = "image/"; // Folder to store images
            if (!is_dir($uploadDirr)) {
                mkdir($uploadDirr, 0777, true); // Create the folder if it doesn’t exist
            }

            $savedPhoto = ""; // Variable to store image path

            // Handle Student Photo Upload
            if (!empty($_FILES["student_photo"]["name"])) {
                $fileTmpPath = $_FILES["student_photo"]["tmp_name"];
                $fileName = time() . "_" . basename($_FILES["student_photo"]["name"]); // Unique file name
                $fileDest = $uploadDirr . $fileName;

                $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
                if (!in_array($_FILES["student_photo"]["type"], $allowedTypes) || $_FILES["student_photo"]["size"] > 2 * 1024 * 1024) {
                    $message = "Invalid file. Only JPG, JPEG, PNG allowed & max size 2MB.";
                    $messageType = "error";
                } elseif (move_uploaded_file($fileTmpPath, $fileDest)) {
                    $savedPhoto = $fileDest; // Save file path
                }
            }
            $resultsDir = "results/";
            if (!is_dir($resultsDir)) {
                mkdir($resultsDir, 0777, true);
            }

            $savedResults = [];
            if (isset($_FILES['results']['name']) && is_array($_FILES['results']['name'])) {
                $current_semester = $_POST['current_semester'] ?? '';
                $res_category = (stripos($current_semester, 'Ex') !== false) ? 'Ex' : 'Regular';
                foreach ($_FILES['results']['name'] as $index => $name) {
                    if (empty($name))
                        continue;

                    $tmpName = $_FILES['results']['tmp_name'][$index];
                    $fileType = $_FILES['results']['type'][$index];
                    $fileSize = $_FILES['results']['size'][$index];
                    $error = $_FILES['results']['error'][$index];

                    if ($error === UPLOAD_ERR_OK) {
                        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                        if (!in_array($fileType, $allowedTypes) || $fileSize > 5 * 1024 * 1024)
                            continue;

                        $uniqueName = time() . "_res_" . $index . "_" . basename($name);
                        $destination = $resultsDir . $uniqueName;

                        if (move_uploaded_file($tmpName, $destination)) {
                            $savedResults[] = [
                                'file_path' => $destination,
                                'type' => $res_category,
                                'uploaded_at' => date('Y-m-d H:i:s')
                            ];
                        }
                    }
                }
            }
            $results_json = json_encode($savedResults);

            // Collect form data
            $roll_no = $_POST['roll_no'] ?? '';
            $student_name = $_POST['student_name'] ?? '';
            $father_address = $_POST['father_address'] ?? '';
            $course_type = $_POST['course_type'] ?? '';
            $current_semester = $_POST['current_semester'] ?? '';
            $admission_fees = $_POST['admission_fees'] ?? '';
            $category = $_POST['category'] ?? '';
            $mobile_no = $_POST['mobile_no'] ?? '';
            $email_id = $_POST['email_id'] ?? '';
            $exam_date = $_POST['exam_date'] ?? '';
            $course = $_POST['course'] ?? '';

            // Check if student already submitted
            $check_sql = "SELECT id, can_edit, student_photo, student_signature, previous_result FROM students WHERE email_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("s", $email_id);
            $check_stmt->execute();
            $existing = $check_stmt->get_result()->fetch_assoc();
            $check_stmt->close();

            if ($existing && $existing['can_edit'] == 0) {
                $message = "⚠ You have already submitted the exam form and it is under review.";
                $messageType = "error";
            } else {
                // If editing, retain old files if new ones aren't uploaded
                if ($existing) {
                    if (empty($savedPhoto))
                        $savedPhoto = $existing['student_photo'];
                    if (empty($savedSignature))
                        $savedSignature = $existing['student_signature'];
                    if (empty($savedResults))
                        $results_json = $existing['previous_result'];
                }

                // Collect Subjects as JSON
                $subjects = [];
                for ($i = 1; $i <= 6; $i++) {
                    if (!empty($_POST["subject$i"])) {
                        $subjects[] = [
                            "subject" => trim($_POST["subject$i"]),
                            "semester" => trim($_POST["sem$i"] ?? ""),
                            "paper_code" => trim($_POST["paper_code$i"] ?? ""),
                            "theory" => isset($_POST["theory$i"]) ? 1 : 0,
                            "practical" => isset($_POST["practical$i"]) ? 1 : 0
                        ];
                    }
                }
                $subjects_json = json_encode($subjects, JSON_UNESCAPED_UNICODE) ?: "[]";

                // Collect Extra Subjects as JSON
                $ex_subjects = [];
                for ($i = 1; $i <= 7; $i++) {
                    if (!empty($_POST["exsubject$i"])) {
                        $ex_subjects[] = [
                            "subject" => trim($_POST["exsubject$i"]),
                            "semester" => trim($_POST["exsem$i"] ?? ""),
                            "paper_code" => trim($_POST["expaper_code$i"] ?? ""),
                            "theory" => isset($_POST["extheory$i"]) ? 1 : 0,
                            "practical" => isset($_POST["expractical$i"]) ? 1 : 0
                        ];
                    }
                }
                $ex_subjects_json = json_encode($ex_subjects, JSON_UNESCAPED_UNICODE) ?: "[]";

                if ($existing) {
                    // UPDATE
                    $sql = "UPDATE students SET roll_no=?, student_name=?, course=?, father_address=?, course_type=?, current_semester=?, admission_fees=?, category=?, mobile_no=?, email_id=?, exam_date=?, student_signature=?, subjects=?, ex_subjects=?, student_photo=?, previous_result=?, status='pending', can_edit=0 WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param(
                        "ssssssssssssssssi",
                        $roll_no,
                        $student_name,
                        $course,
                        $father_address,
                        $course_type,
                        $current_semester,
                        $admission_fees,
                        $category,
                        $mobile_no,
                        $email_id,
                        $exam_date,
                        $savedSignature,
                        $subjects_json,
                        $ex_subjects_json,
                        $savedPhoto,
                        $results_json,
                        $existing['id']
                    );
                } else {
                    // INSERT
                    $sql = "INSERT INTO students (roll_no, student_name, course, father_address, course_type, current_semester, admission_fees, category, mobile_no, email_id, exam_date, student_signature, subjects, ex_subjects, student_photo, previous_result) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param(
                        "ssssssssssssssss",
                        $roll_no,
                        $student_name,
                        $course,
                        $father_address,
                        $course_type,
                        $current_semester,
                        $admission_fees,
                        $category,
                        $mobile_no,
                        $email_id,
                        $exam_date,
                        $savedSignature,
                        $subjects_json,
                        $ex_subjects_json,
                        $savedPhoto,
                        $results_json
                    );
                }
                $challanUploadDir = "challans/";
                $challanPaths = [];

                if (!is_dir($challanUploadDir)) {
                    mkdir($challanUploadDir, 0777, true);
                }

                if (isset($_FILES['challan']['name']) && is_array($_FILES['challan']['name'])) {
                    foreach ($_FILES['challan']['name'] as $index => $name) {
                        if (empty($name))
                            continue; // Skip empty fields

                        $tmpName = $_FILES['challan']['tmp_name'][$index];
                        $fileType = $_FILES['challan']['type'][$index];
                        $fileSize = $_FILES['challan']['size'][$index];
                        $error = $_FILES['challan']['error'][$index];

                        if ($error === UPLOAD_ERR_OK) {
                            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];

                            if (!in_array($fileType, $allowedTypes) || $fileSize > 5 * 1024 * 1024) {
                                $message .= "<br>⚠ Challan '$name' skipped: Invalid type or file too big.";
                                continue;
                            }

                            $uniqueName = time() . "_" . $index . "_" . basename($name); // Added index to ensure uniqueness for simultaneous uploads
                            $destination = $challanUploadDir . $uniqueName;

                            if (move_uploaded_file($tmpName, $destination)) {
                                $challanPaths[] = $destination; // Save for inserting later
                            }
                        }
                    }
                }
                if ($stmt->execute()) {
                    $message = "✅ Exam Form Submitted successfully!";
                    $messageType = "success";

                    // Correctly get student ID for both INSERT and UPDATE
                    $student_id = $existing ? $existing['id'] : $stmt->insert_id;

                    // 🔁 Insert uploaded challans into challans table
                    if (!empty($challanPaths)) {
                        foreach ($challanPaths as $path) {
                            $challanStmt = $conn->prepare("INSERT INTO challans (student_id, file_path) VALUES (?, ?)");
                            if ($challanStmt) {
                                $challanStmt->bind_param("is", $student_id, $path);
                                $challanStmt->execute();
                                $challanStmt->close();
                            }
                        }
                    }
                    $subject = 'Exam Form Submission Confirmation - E-Kitabghar';
                    $body = "
                    <!DOCTYPE html>
                    <html>
                    <head>
                      <style>
                        body { font-family: Arial, sans-serif; background-color: #f2f2f2; padding: 0; margin: 0; }
                        .email-container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
                        .title { font-size: 22px; color: #2c3e50; margin-bottom: 20px; display: flex; align-items: center; }
                        .info-section { font-size: 18px; color: #34495e; margin-top: 25px; margin-bottom: 10px; }
                        .info-row { margin: 5px 0; }
                        .label { font-weight: bold; color: #2c3e50; }
                        .value { color: #555; }
                      </style>
                    </head>
                    <body>
                      <div class='email-container'>
                        <div class='title'><span>🎉 Exam Form Submitted Successfully!</span></div>
                        <div class='info-section'>
                          <div class='info-row'><span class='label'>🎓 Name:</span> <span class='value'>$student_name</span></div>
                          <div class='info-row'><span class='label'>🔢 Roll No:</span> <span class='value'>$roll_no</span></div>
                          <div class='info-row'><span class='label'>🗓️  Semester:</span> <span class='value'>$current_semester</span></div>
                        </div>
                        <div class='footer'>Thank you for choosing E-Kitabghar!</div>
                      </div>
                    </body>
                    </html>";

                    $res = sendEmail($email_id, $student_name, $subject, $body);
                    if ($res !== true) {
                        $message .= " However, confirmation email could not be sent.";
                    }
                } else {
                    $message = "⚠ Submission failed: " . $stmt->error;
                    $messageType = "error";
                }
                $stmt->close();
            }
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status | E-Kitabghar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 overflow-hidden">
    <div class="fixed inset-0 z-0 text-slate-100">
        <div
            class="absolute top-0 -left-20 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-pulse">
        </div>
        <div
            class="absolute bottom-0 -right-20 w-96 h-96 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-pulse">
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div
            class="relative z-10 w-full max-w-lg bg-white/80 backdrop-blur-2xl rounded-[2.5rem] shadow-2xl border border-white p-8 md:p-12 text-center transform transition-all duration-500">

            <div class="mb-8 flex justify-center">
                <?php if ($messageType === 'success'): ?>
                    <div class="w-32 h-32 flex items-center justify-center">
                        <dotlottie-player src="https://lottie.host/15078e87-394d-4d63-a49e-77e4df2b244a/7KzXhhk4tp.lottie"
                            background="transparent" speed="1" style="width: 200px; height: 200px;" loop
                            autoplay></dotlottie-player>
                    </div>
                <?php else: ?>
                    <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center">
                        <i class="bi bi-exclamation-triangle-fill text-5xl text-red-500"></i>
                    </div>
                <?php endif; ?>
            </div>

            <h2 class="text-3xl font-extrabold text-slate-900 mb-4 tracking-tight">
                <?php echo ($messageType === 'success') ? 'Success!' : 'Oops!'; ?>
            </h2>

            <p class="text-slate-600 text-lg font-medium mb-10 leading-relaxed">
                <?php echo $message; ?>
            </p>

            <button onclick="closeModal()"
                class="w-full inline-flex items-center justify-center gap-3 px-8 py-5 <?php echo ($messageType === 'success') ? 'bg-green-600 hover:bg-green-700' : 'bg-slate-900 hover:bg-slate-800'; ?> text-white text-lg font-bold rounded-2xl transition-all duration-300 shadow-xl active:scale-[0.98]">
                <span><?php echo ($messageType === 'success') ? "Go to Dashboard" : "Try Again"; ?></span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </div>

        <script>
            function closeModal() {
                const type = "<?php echo $messageType; ?>";
                if (type === "success") {
                    window.location.href = "../dashboard.php";
                } else {
                    window.location.href = "../exam_form.php";
                }
            }
        </script>
    <?php endif; ?>
</body>

</html>