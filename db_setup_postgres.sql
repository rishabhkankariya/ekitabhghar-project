-- PostgreSQL Database Setup Script for E-Kitabghar
-- Run this on your self-hosted PostgreSQL database on Azure VM

-- 1. Table: visitor_count
CREATE TABLE IF NOT EXISTS visitor_count (
    id INT PRIMARY KEY,
    count INT DEFAULT 0
);

-- Seed visitor_count table
INSERT INTO visitor_count (id, count) VALUES (1, 0) ON CONFLICT (id) DO NOTHING;

-- 2. Table: admin (System Administrator)
CREATE TABLE IF NOT EXISTS admin (
    admin_id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_pic VARCHAR(255) DEFAULT 'uploads/dummy.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Table: library_admin
CREATE TABLE IF NOT EXISTS library_admin (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Table: departments
CREATE TABLE IF NOT EXISTS departments (
    id SERIAL PRIMARY KEY,
    dept_code VARCHAR(50) NOT NULL UNIQUE,
    dept_name VARCHAR(150) NOT NULL,
    short_name VARCHAR(50) NOT NULL,
    description TEXT,
    is_active INT DEFAULT 1
);

-- 5. Table: courses
CREATE TABLE IF NOT EXISTS courses (
    id SERIAL PRIMARY KEY,
    dept_id INT NOT NULL REFERENCES departments(id) ON DELETE CASCADE,
    course_code VARCHAR(50) NOT NULL UNIQUE,
    course_name VARCHAR(150) NOT NULL,
    short_name VARCHAR(50) NOT NULL,
    duration_years INT NOT NULL,
    total_semesters INT NOT NULL,
    is_active INT DEFAULT 1
);

-- 6. Table: semesters
CREATE TABLE IF NOT EXISTS semesters (
    id SERIAL PRIMARY KEY,
    course_id INT NOT NULL REFERENCES courses(id) ON DELETE CASCADE,
    semester_number INT NOT NULL,
    semester_name VARCHAR(50) NOT NULL,
    year_level INT NOT NULL,
    is_active INT DEFAULT 1,
    UNIQUE(course_id, semester_number)
);

-- 7. Table: subjects
CREATE TABLE IF NOT EXISTS subjects (
    id SERIAL PRIMARY KEY,
    course_id INT NOT NULL REFERENCES courses(id) ON DELETE CASCADE,
    semester_id INT NOT NULL REFERENCES semesters(id) ON DELETE CASCADE,
    subject_code VARCHAR(50) NOT NULL,
    subject_name VARCHAR(255) NOT NULL,
    has_theory INT DEFAULT 1,
    has_practical INT DEFAULT 0,
    is_elective INT DEFAULT 0,
    credits INT DEFAULT 0,
    is_active INT DEFAULT 1
);

-- 8. Table: student_accounts
CREATE TABLE IF NOT EXISTS student_accounts (
    id SERIAL PRIMARY KEY,
    roll_no VARCHAR(50) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(15),
    course VARCHAR(100) NOT NULL DEFAULT 'Diploma',
    admission_year INT NOT NULL,
    expected_passing_year INT NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_temp_password BOOLEAN DEFAULT TRUE,
    account_status VARCHAR(20) DEFAULT 'active',
    dob VARCHAR(50),
    profile_image VARCHAR(255) DEFAULT 'users.png',
    session_token VARCHAR(255) NULL,
    last_login_at TIMESTAMP NULL,
    last_login_ip VARCHAR(45) NULL,
    last_login_location VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    dept_id INT REFERENCES departments(id) ON DELETE SET NULL,
    course_id INT REFERENCES courses(id) ON DELETE SET NULL,
    semester_id INT REFERENCES semesters(id) ON DELETE SET NULL
);

-- 9. Table: student_login_logs
CREATE TABLE IF NOT EXISTS student_login_logs (
    id SERIAL PRIMARY KEY,
    student_id INT NOT NULL REFERENCES student_accounts(id) ON DELETE CASCADE,
    ip_address VARCHAR(45),
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    location VARCHAR(255)
);

-- 10. Table: students (Submitted Exam Forms)
CREATE TABLE IF NOT EXISTS students (
    id SERIAL PRIMARY KEY,
    roll_no VARCHAR(50) NOT NULL,
    student_name VARCHAR(100) NOT NULL,
    course VARCHAR(150) NOT NULL,
    father_address TEXT NOT NULL,
    course_type VARCHAR(50) NOT NULL,
    current_semester VARCHAR(50) NOT NULL,
    admission_fees NUMERIC(10, 2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    mobile_no VARCHAR(15) NOT NULL,
    email_id VARCHAR(100) NOT NULL UNIQUE,
    exam_date DATE NOT NULL,
    student_signature VARCHAR(255),
    student_photo VARCHAR(255),
    subjects TEXT, -- JSON structure storing array of subjects
    ex_subjects TEXT, -- JSON structure storing array of Ex-subjects
    previous_result TEXT, -- JSON structure storing array of results
    status VARCHAR(20) DEFAULT 'pending',
    can_edit INT DEFAULT 0
);

-- 11. Table: challans
CREATE TABLE IF NOT EXISTS challans (
    id SERIAL PRIMARY KEY,
    student_id INT NOT NULL REFERENCES students(id) ON DELETE CASCADE,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 12. Table: rejected_students (Archive of rejected exam forms)
CREATE TABLE IF NOT EXISTS rejected_students (
    id SERIAL PRIMARY KEY,
    original_id INT NOT NULL,
    roll_no VARCHAR(50) NOT NULL,
    student_name VARCHAR(100) NOT NULL,
    current_semester VARCHAR(50) NOT NULL,
    category VARCHAR(50) NOT NULL,
    mobile_no VARCHAR(15) NOT NULL,
    email_id VARCHAR(100) NOT NULL,
    exam_date DATE,
    reason TEXT,
    status VARCHAR(20) DEFAULT 'rejected',
    rejected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 13. Table: syllabus
CREATE TABLE IF NOT EXISTS syllabus (
    id SERIAL PRIMARY KEY,
    year VARCHAR(50) NOT NULL,
    semester VARCHAR(50) NOT NULL,
    subject_name VARCHAR(255) NOT NULL,
    pdf_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 14. Table: student_notes
CREATE TABLE IF NOT EXISTS student_notes (
    id SERIAL PRIMARY KEY,
    semester VARCHAR(50) NOT NULL,
    subject_name VARCHAR(255) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    notes_link TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 15. Table: contributed_notes
CREATE TABLE IF NOT EXISTS contributed_notes (
    id SERIAL PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    notes_title VARCHAR(255) NOT NULL,
    semester VARCHAR(50) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 16. Table: question_papers
CREATE TABLE IF NOT EXISTS question_papers (
    id SERIAL PRIMARY KEY,
    year VARCHAR(50) NOT NULL,
    semester VARCHAR(50) NOT NULL,
    subject_name VARCHAR(255) NOT NULL,
    pdf_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 17. Table: announcements
CREATE TABLE IF NOT EXISTS announcements (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 18. Table: imp_announcements
CREATE TABLE IF NOT EXISTS imp_announcements (
    id SERIAL PRIMARY KEY,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 19. Table: modal_announcement
CREATE TABLE IF NOT EXISTS modal_announcement (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 20. Table: messages (Chat / Inbox messages between admins and students)
CREATE TABLE IF NOT EXISTS messages (
    id SERIAL PRIMARY KEY,
    admin_id INT,
    admin_name VARCHAR(100),
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 21. Table: events
CREATE TABLE IF NOT EXISTS events (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 22. Table: slides (Carousel banner images)
CREATE TABLE IF NOT EXISTS slides (
    id SERIAL PRIMARY KEY,
    image_url VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 23. Table: videos
CREATE TABLE IF NOT EXISTS videos (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    video_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 24. Table: gallery
CREATE TABLE IF NOT EXISTS gallery (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 25. Table: exam_settings
CREATE TABLE IF NOT EXISTS exam_settings (
    id SERIAL PRIMARY KEY,
    academic_session VARCHAR(50) DEFAULT '2024-25',
    start_date TIMESTAMP,
    end_date TIMESTAMP
);

-- 26. Table: feedback
CREATE TABLE IF NOT EXISTS feedback (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    rating INT NOT NULL,
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 27. Table: contact_messages
CREATE TABLE IF NOT EXISTS contact_messages (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 28. Table: users
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email_verified INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
