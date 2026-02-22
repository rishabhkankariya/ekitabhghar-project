<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="student_upload_template.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['roll_no', 'full_name', 'email', 'phone', 'course', 'admission_year', 'passing_year', 'dob']);
fputcsv($output, ['22030C04001', 'Rishabh Kankariya', 'rishabh@example.com', '9876543210', 'Computer Science', '2022', '2025', '24/11/2006']);
fclose($output);
?>
