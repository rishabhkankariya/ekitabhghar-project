<?php
header("Content-Type: text/html; charset=UTF-8");
require_once __DIR__ . '/../../php/connection.php';

// Try to include Gemini Config
if (file_exists(__DIR__ . '/gemini_config.php')) {
    include_once __DIR__ . '/gemini_config.php';
}

// ✅ Capture User Message
$userMessage = trim($_POST["message"] ?? "");
$userMessageLower = strtolower($userMessage);
$response = "";

// ✅ Helper to format links nicely for Dark Glassmorphism UI
function formatLink($url, $text, $icon = '🔗')
{
    return "<br><a href='$url' class='inline-flex items-center gap-3 mt-3 px-5 py-2.5 bg-white/5 hover:bg-white/10 text-blue-400 rounded-2xl transition-all font-bold border border-white/10 text-[14px] group shadow-lg active:scale-95 text-decoration-none'>
            <span class='text-lg'>$icon</span>
            <span class='group-hover:underline'>$text</span>
            <i class='fa-solid fa-chevron-right text-[10px] opacity-40 group-hover:translate-x-1 transition-transform'></i>
            </a>";
}

// ✅ Context System
function getWebsiteContext($conn)
{
    $examInfo = "No active exam forms.";
    $q = mysqli_query($conn, "SELECT start_date, end_date FROM exam_settings ORDER BY id DESC LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        $start = date("d M Y", strtotime($row["start_date"]));
        $end = date("d M Y", strtotime($row["end_date"]));
        $examInfo = "Exam form collection is from $start to $end.";
    }

    return [
        "exam_info" => $examInfo,
        "website_name" => "Kitabghar",
        "college" => "Govt. Polytechnic College Ujjain",
        "contact_email" => "ekitabghar@gmail.com",
        "contact_phone" => "+91 7697164221",
        "address" => "Dewas Road, Ujjain - 456001 (M.P)",
        "nav_links" => [
            "Home" => "index.php",
            "About" => "about.html",
            "Syllabus" => "syllabus.html",
            "Question Papers" => "question.html",
            "Contact" => "contact.html",
            "Faculty" => "faculty.html"
        ]
    ];
}

// ✅ Gemini API Handler
function callGeminiAPI($message, $context)
{
    if (!defined('GEMINI_API_KEY') || GEMINI_API_KEY === 'YOUR_API_KEY_HERE')
        return null;
    $apiKey = GEMINI_API_KEY;
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=" . $apiKey;

    $systemPrompt = "You are 'E-Know', the AI Assistant for 'Kitabghar'.
    CONTEXT: " . json_encode($context) . "
    Reply professionally in HTML. Use button style for links: <a href='URL' style='display:inline-block; margin-top:8px; padding:8px 16px; background:rgba(255,255,255,0.05); color:#60a5fa; border-radius:12px; text-decoration:none; font-weight:bold; border:1px solid rgba(255,255,255,0.1);'>TEXT</a>. 
    NOTE: If asked to apply for exam, redirect to student_login.html.
    User: $message";

    $data = ["contents" => [["parts" => [["text" => $systemPrompt]]]]];
    $options = ["http" => ["header" => "Content-type: application/json\r\n", "method" => "POST", "content" => json_encode($data)]];
    $streamContext = stream_context_create($options);
    $result = @file_get_contents($url, false, $streamContext);
    if ($result === FALSE)
        return null;
    $json = json_decode($result, true);
    return $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
}

// ✅ Main Logic
$commands = [
    "home" => "🏠 <b>Welcome to Kitabghar!</b>" . formatLink('index.php', 'Go to Homepage', '🏠'),
    "about" => "ℹ️ <b>About Our System</b>" . formatLink('about.html', 'Learn More', '📖'),
    "feedback" => "✍️ <b>Your feedback matters!</b>" . formatLink('index.php#contact', 'Submit Feedback', '📝'),
    "contact" => "📞 <b>Support Hub</b><br>📧 <a href='mailto:ekitabghar@gmail.com' class='text-blue-400 underline'>ekitabghar@gmail.com</a><br>📱 <a href='tel:7697164221' class='text-blue-400 underline'>+91 7697164221</a>",
    "syllabus" => "📚 <b>Syllabus Portal</b>" . formatLink('syllabus.html', 'View Syllabus', '📑'),
    "question papers" => "📝 <b>Archive Papers</b>" . formatLink('question.html', 'Browse Archive', '📄'),
    "faculty" => "👨‍🏫 <b>Faculty Directory</b><br>Meet our distinguished professors and lecturers." . formatLink('faculty.html', 'Meet Faculty', '👨‍🏫'),
    "faculties" => "👨‍🏫 <b>Faculty Directory</b><br>Meet our distinguished professors and lecturers." . formatLink('faculty.html', 'Meet Faculty', '👨‍🏫'),
    "exam" => "📅 <b>Exam Center</b><br>Active dates are available!",
    "thanks" => "😊 <b>Always here to help!</b>",
    "thank you" => "😊 <b>Always here to help!</b>",
    "working" => "✅ <b>System Online</b>",
];

if (empty($userMessage) || $userMessageLower === "help") {
    $response = "👋 <b>E-Know AI Protocol</b><br>Try asking about Exams, Syllabus, or Faculty Info.";
} elseif (isset($commands[$userMessageLower])) {
    $response = $commands[$userMessageLower];
} elseif (strpos($userMessageLower, "exam") !== false) {
    $q = "SELECT start_date, end_date FROM exam_settings ORDER BY id DESC LIMIT 1";
    $result = @mysqli_query($conn, $q);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $s = date("d M Y", strtotime($row["start_date"]));
        $e = date("d M Y", strtotime($row["end_date"]));
        $response = "📅 <b>Exam Update</b><br>Active from <b>$s</b> to <b>$e</b>." . formatLink('student_login.html', 'Apply Now', '✍️');
    } else {
        $response = "📅 <b>Exam Hub</b><br>No active cycles detected.";
    }
} else {
    $context = getWebsiteContext($conn);
    $aiResponse = callGeminiAPI($userMessage, $context);
    if ($aiResponse) {
        $response = $aiResponse;
    } else {
        $greetings = ["hello", "hi", "hey"];
        $isGreeting = false;
        foreach ($greetings as $g) {
            if (strpos($userMessageLower, $g) !== false) {
                $isGreeting = true;
                break;
            }
        }
        if ($isGreeting) {
            $response = "👋 <b>Hello!</b><br>I'm E-Know. How can I assist you?";
        } else {
            $response = "🤖 <b>I'm still learning!</b><br>Try asking about syllabus or exams!";
        }
    }
}

echo $response;
?>
