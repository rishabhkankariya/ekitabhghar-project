<?php
/**
 * API Endpoint: Get Departments, Courses, Semesters, and Subjects
 * This file provides dynamic data for the exam form
 */

header('Content-Type: application/json');
require_once '../connection.php';

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'departments':
            // Get all active departments
            $stmt = $pdo->query("SELECT id, dept_code, dept_name, short_name FROM departments WHERE is_active = 1 ORDER BY dept_name");
            $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $departments]);
            break;

        case 'courses':
            // Get courses by department
            $dept_id = $_GET['dept_id'] ?? 0;
            $stmt = $pdo->prepare("SELECT id, course_code, course_name, short_name, duration_years, total_semesters FROM courses WHERE dept_id = ? AND is_active = 1 ORDER BY course_name");
            $stmt->execute([$dept_id]);
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $courses]);
            break;

        case 'semesters':
            // Get semesters by course
            $course_id = $_GET['course_id'] ?? 0;
            $stmt = $pdo->prepare("SELECT id, semester_number, semester_name, year_level FROM semesters WHERE course_id = ? AND is_active = 1 ORDER BY semester_number");
            $stmt->execute([$course_id]);
            $semesters = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $semesters]);
            break;

        case 'subjects':
            // Get subjects by semester
            $semester_id = $_GET['semester_id'] ?? 0;
            $stmt = $pdo->prepare("
                SELECT 
                    id, 
                    subject_code, 
                    subject_name, 
                    has_theory, 
                    has_practical, 
                    is_elective, 
                    credits 
                FROM subjects 
                WHERE semester_id = ? AND is_active = 1 
                ORDER BY subject_name
            ");
            $stmt->execute([$semester_id]);
            $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $subjects]);
            break;

        case 'all_subjects_by_course':
            // Get all subjects for a course (grouped by semester)
            $course_id = $_GET['course_id'] ?? 0;
            $stmt = $pdo->prepare("
                SELECT 
                    sub.id,
                    sub.subject_code,
                    sub.subject_name,
                    sub.has_theory,
                    sub.has_practical,
                    sub.is_elective,
                    sub.credits,
                    sem.semester_number,
                    sem.semester_name
                FROM subjects sub
                JOIN semesters sem ON sub.semester_id = sem.id
                WHERE sub.course_id = ? AND sub.is_active = 1
                ORDER BY sem.semester_number, sub.subject_name
            ");
            $stmt->execute([$course_id]);
            $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Group by semester
            $grouped = [];
            foreach ($subjects as $subject) {
                $sem_num = $subject['semester_number'];
                if (!isset($grouped[$sem_num])) {
                    $grouped[$sem_num] = [
                        'semester_number' => $sem_num,
                        'semester_name' => $subject['semester_name'],
                        'subjects' => []
                    ];
                }
                $grouped[$sem_num]['subjects'][] = $subject;
            }
            
            echo json_encode(['success' => true, 'data' => array_values($grouped)]);
            break;

        case 'student_course_info':
            // Get student's current course information
            $email = $_GET['email'] ?? '';
            $stmt = $pdo->prepare("
                SELECT 
                    s.id,
                    s.roll_no,
                    s.student_name,
                    d.id as dept_id,
                    d.dept_name,
                    c.id as course_id,
                    c.course_name,
                    sem.id as semester_id,
                    sem.semester_name,
                    sem.semester_number
                FROM student_accounts s
                LEFT JOIN departments d ON s.dept_id = d.id
                LEFT JOIN courses c ON s.course_id = c.id
                LEFT JOIN semesters sem ON s.semester_id = sem.id
                WHERE s.email = ?
            ");
            $stmt->execute([$email]);
            $info = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $info]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
