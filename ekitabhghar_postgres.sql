-- PostgreSQL Schema for ekitabhghar
-- Converted from MySQL/MariaDB
-- Generated: 2026

BEGIN;

-- --------------------------------------------------------
-- Table: admin
-- --------------------------------------------------------
CREATE TABLE admin (
  admin_id SERIAL PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  profile_pic VARCHAR(255) DEFAULT 'uploads/default.jpg'
);

INSERT INTO admin (admin_id, username, password, profile_pic) VALUES
(1, 'megha_malviya', '$2y$10$e3.2vAumlLoDe.KOi3IwpevKW0JpcUpci9hkgDo7fT/rg8yJaZTQu', 'uploads/6939752ad1efe_BHARAT_POSTER.jpg'),
(2, 'rishabh_jn', '$2y$10$moEYpXDjB2J16rkUtqXAYOk3ltMxB3HuWJXNIr3aEenzbREIA384W', 'uploads/67fc906b6ac99_67fbec3b683c6_02816342a15185d66f875a858fe530e1.jpg');
SELECT setval('admin_admin_id_seq', 3);

-- --------------------------------------------------------
-- Table: announcements
-- --------------------------------------------------------
CREATE TABLE announcements (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  date DATE NOT NULL
);

INSERT INTO announcements (id, title, description, date) VALUES
(7, 'New CSE Notes for 3rd Year Available!', 'Check out the newly uploaded notes for CSE third-year students. Visit the Student Notes section for more details.', '2025-03-10'),
(11, 'Exam Timetable Released', 'The final exam schedule for all semesters has been published in the Exam Section.', '2025-04-10'),
(12, 'Library Hours Extended', 'Library will now remain open till 8 PM on weekdays to support exam preparations.', '2025-04-10');
SELECT setval('announcements_id_seq', 14);

-- --------------------------------------------------------
-- Table: challans
-- --------------------------------------------------------
CREATE TABLE challans (
  id SERIAL PRIMARY KEY,
  student_id INT NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO challans (id, student_id, file_path, uploaded_at) VALUES
(1, 1, 'challans/1767811607_0_dbms_presentation[1][1].pdf', '2026-01-07 18:46:47');
SELECT setval('challans_id_seq', 3);

-- --------------------------------------------------------
-- Table: contact_messages
-- --------------------------------------------------------
CREATE TABLE contact_messages (
  id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
SELECT setval('contact_messages_id_seq', 13);

-- --------------------------------------------------------
-- Table: contributed_notes
-- --------------------------------------------------------
CREATE TABLE contributed_notes (
  id SERIAL PRIMARY KEY,
  student_name VARCHAR(100) NOT NULL,
  notes_title VARCHAR(255) NOT NULL,
  semester VARCHAR(10) NOT NULL CHECK (semester IN ('1','2','3','4','5','6')),
  file_name VARCHAR(255) NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
SELECT setval('contributed_notes_id_seq', 8);

-- --------------------------------------------------------
-- Table: events
-- --------------------------------------------------------
CREATE TABLE events (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  event_date DATE NOT NULL,
  image VARCHAR(255) NOT NULL
);

INSERT INTO events (id, title, description, event_date, image) VALUES
(2, 'Online Lecture Series: Advanced Programming', 'A series of online lectures focused on advanced programming techniques, starting from Dec 20, 2024.', '2024-12-20', 'event2.webp'),
(7, 'Semester Exam Preparation Workshop', 'Join us for a comprehensive workshop to prepare for the semester exams. Tips and strategies will be discussed.', '2024-11-20', 'event1.png'),
(17, 'Interactive CSE Quiz Competition', 'Test your knowledge in computer science through a fun and interactive quiz competition on January 5, 2025.', '2025-03-10', 'event3.webp');
SELECT setval('events_id_seq', 20);

-- --------------------------------------------------------
-- Table: exam_settings
-- --------------------------------------------------------
CREATE TABLE exam_settings (
  id SERIAL PRIMARY KEY,
  start_date TIMESTAMP NOT NULL,
  end_date TIMESTAMP NOT NULL,
  academic_session VARCHAR(50) DEFAULT '2024-25'
);

INSERT INTO exam_settings (id, start_date, end_date, academic_session) VALUES
(1, '2026-01-06 22:18:00', '2026-02-12 10:00:00', '2024-25');
SELECT setval('exam_settings_id_seq', 2);

-- --------------------------------------------------------
-- Table: feedback
-- --------------------------------------------------------
CREATE TABLE feedback (
  id SERIAL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  rating INT NOT NULL,
  message TEXT NOT NULL,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO feedback (id, name, email, rating, message, submitted_at) VALUES
(7, 'SOMESHWAR SONGARA', 'somesh.songara1@gmail.com', 5, 'OKKKKKKKKKKKKK', '2026-01-06 19:40:50');
SELECT setval('feedback_id_seq', 8);

-- --------------------------------------------------------
-- Table: gallery
-- --------------------------------------------------------
CREATE TABLE gallery (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  image_path VARCHAR(255) NOT NULL
);

INSERT INTO gallery (id, title, image_path) VALUES
(21, 'Fresh air, fresh ideas', 'gal_6967f82e474b44.74834417.heic'),
(22, 'Knowledge under open skies', 'gal_6967f846e99013.19713016.jpg'),
(23, 'Learning feels lighter here', 'gal_6967f85ed2c5b2.32004849.heic'),
(24, 'Calm spaces, curious minds', 'gal_6967f8709193e9.09815093.jpg'),
(25, 'A natural way to learn', 'gal_6967f886154119.59327303.jpg'),
(26, 'Quiet places, loud dreams', 'gal_6967f8a3980412.49840194.webp'),
(27, 'Let nature teach you too', 'gal_6967f8bce39228.32923953.heic'),
(28, 'Image 2', 'gal_697619734667e3.76449839.jpg');
SELECT setval('gallery_id_seq', 29);

-- --------------------------------------------------------
-- Table: imp_announcements
-- --------------------------------------------------------
CREATE TABLE imp_announcements (
  id SERIAL PRIMARY KEY,
  message TEXT NOT NULL,
  is_active SMALLINT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO imp_announcements (id, message, is_active, created_at) VALUES
(1, 'Important Announcement: New Exam Dates Released!| Free Study Materials Available!| Internship Opportunities Open!', 1, '2025-04-14 09:56:00');
SELECT setval('imp_announcements_id_seq', 2);

-- --------------------------------------------------------
-- Table: library_admin
-- --------------------------------------------------------
CREATE TABLE library_admin (
  id SERIAL PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO library_admin (id, username, password, created_at) VALUES
(1, 'admin', '$2y$10$SCVq4vSi6kQEOQ1H9imwlOuhF0PWAYecY6fLhqgGFDs2pqNKdsP8i', '2025-12-20 12:34:53');
SELECT setval('library_admin_id_seq', 2);

-- --------------------------------------------------------
-- Table: messages
-- --------------------------------------------------------
CREATE TABLE messages (
  id SERIAL PRIMARY KEY,
  admin_id INT NOT NULL,
  message TEXT NOT NULL,
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  admin_name VARCHAR(100) NOT NULL
);

INSERT INTO messages (id, admin_id, message, sent_at, admin_name) VALUES
(15, 1, 'Welcome, Dear Students! Step into your Student Dashboard - your gateway to a world of learning, resources, and opportunities! Stay updated, explore, and make the most of your journey with E-Kitabghar. Happy Learning!', '2025-03-18 09:41:26', 'megha_malviya');
SELECT setval('messages_id_seq', 19);

-- --------------------------------------------------------
-- Table: modal_announcement
-- --------------------------------------------------------
CREATE TABLE modal_announcement (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO modal_announcement (id, title, message, created_at) VALUES
(1, 'New Notes Available', 'CSE 3rd-year notes are now updated in the Student Notes section.', '2025-03-13 09:59:34'),
(2, 'Exam Schedule Released', 'The mid-term exam schedule has been posted in the student portal.', '2025-03-13 09:59:34');
SELECT setval('modal_announcement_id_seq', 10);

-- --------------------------------------------------------
-- Table: question_papers
-- --------------------------------------------------------
CREATE TABLE question_papers (
  id SERIAL PRIMARY KEY,
  year VARCHAR(10) NOT NULL,
  semester VARCHAR(20) NOT NULL,
  subject_name VARCHAR(255) NOT NULL,
  pdf_path VARCHAR(255) NOT NULL
);

INSERT INTO question_papers (id, year, semester, subject_name, pdf_path) VALUES
(1, '1 Year', '1 Semester', 'Mathematics I', 'pdfs/mathematics_1_Paper.pdf'),
(2, '1 Year', '1 Semester', 'Chemistry', 'pdfs/Chemistry_Paper.pdf'),
(3, '1 Year', '1 Semester', 'Communication Skills & English', 'pdfs/Communication_Skills_Paper.pdf'),
(4, '1 Year', '1 Semester', 'Physics 1', 'pdfs/Applied_Physics_1_Paper.pdf'),
(5, '1 Year', '2 Semester', 'Mathematics II', 'pdfs/mathematics_2_Paper.pdf'),
(6, '1 Year', '2 Semester', 'Introduction To IT System', 'pdfs/Intro_to_IT_System_Paper.pdf'),
(7, '1 Year', '2 Semester', 'FEEE', 'pdfs/FEEE_Paper.pdf'),
(8, '1 Year', '2 Semester', 'Physics 2', 'pdfs/Applied_Physics_2_Paper.pdf'),
(9, '1 Year', '2 Semester', 'Engineering Mechanics', 'pdfs/.pdf'),
(10, '2 Year', '3 Semester', 'Scripting Languages', 'pdfs/.pdf'),
(11, '2 Year', '3 Semester', 'Data Structures', 'pdfs/.pdf'),
(12, '2 Year', '3 Semester', 'Algorithm', 'pdfs/.pdf'),
(13, '2 Year', '3 Semester', 'Computer Programming', 'pdfs/Programming_in_C_paper.pdf'),
(14, '2 Year', '3 Semester', 'Computer System Organisation', 'pdfs/.pdf'),
(15, '2 Year', '4 Semester', 'Computer Networks', 'pdfs/Computer_Network_Paper.pdf'),
(16, '2 Year', '4 Semester', 'Operating Systems', 'pdfs/Operating_System_Paper.pdf'),
(17, '2 Year', '4 Semester', 'Introduction to DBMS', 'pdfs/DBMS_Paper.pdf'),
(18, '2 Year', '4 Semester', 'Web Technologies', 'pdfs/Web-tech_Paper.pdf'),
(19, '2 Year', '4 Semester', 'SSAD/Software Engineering', 'pdfs/SSAD_Paper.pdf'),
(20, '3 Year', '5 Semester', 'Operation Research', 'pdfs/.pdf'),
(21, '3 Year', '5 Semester', 'Intro. To E-governance', 'pdfs/E-governance_Paper.pdf'),
(22, '3 Year', '5 Semester', 'Internet of Things', 'pdfs/.pdf'),
(23, '3 Year', '5 Semester', 'Information Security', 'pdfs/Information_Security_Paper.pdf'),
(24, '3 Year', '5 Semester', 'MultiMedia Technologies', 'pdfs/.pdf'),
(25, '3 Year', '5 Semester', 'AD.Computer Networks', 'pdfs/.pdf'),
(26, '3 Year', '5 Semester', 'Data Sciences', 'pdfs/.pdf'),
(27, '3 Year', '5 Semester', 'Renewable Energy tech.', 'pdfs/Renewable_Energy_Paper.pdf'),
(28, '3 Year', '6 Semester', 'Entrepreneurship & Start-up', 'pdfs/Entrepreneurship_StartUps.pdf.pdf'),
(29, '3 Year', '6 Semester', 'Mobile Computing', 'pdfs/Mobile_Computing_Paper.pdf'),
(30, '3 Year', '6 Semester', 'Network Forensics', 'pdfs/.pdf'),
(31, '3 Year', '6 Semester', 'Software Testing', 'pdfs/Software_Testing_Paper.pdf'),
(32, '3 Year', '6 Semester', 'Free & Open Source Software', 'pdfs/.pdf'),
(33, '3 Year', '6 Semester', 'Disaster Management', 'pdfs/.pdf'),
(34, '3 Year', '6 Semester', 'Project Management', 'pdfs/.pdf'),
(35, '3 Year', '6 Semester', 'Artificial Intelligence', 'pdfs/artificial_Intelligence_Paper.pdf'),
(36, '3 Year', '6 Semester', 'Engg.Eco & Accountancy', 'pdfs/.pdf'),
(37, '3 Year', '6 Semester', 'Indian Constitution', 'pdfs/.pdf');
SELECT setval('question_papers_id_seq', 55);

-- --------------------------------------------------------
-- Table: rejected_students
-- --------------------------------------------------------
CREATE TABLE rejected_students (
  id SERIAL PRIMARY KEY,
  original_id INT NOT NULL,
  roll_no VARCHAR(50) DEFAULT NULL,
  student_name VARCHAR(100) DEFAULT NULL,
  current_semester VARCHAR(50) DEFAULT NULL,
  category VARCHAR(50) DEFAULT NULL,
  mobile_no VARCHAR(20) DEFAULT NULL,
  email_id VARCHAR(100) DEFAULT NULL,
  exam_date DATE DEFAULT NULL,
  reason TEXT DEFAULT NULL,
  status VARCHAR(20) DEFAULT 'rejected' CHECK (status IN ('rejected')),
  rejected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- Table: slides
-- --------------------------------------------------------
CREATE TABLE slides (
  id SERIAL PRIMARY KEY,
  image_url VARCHAR(255) NOT NULL
);

INSERT INTO slides (id, image_url) VALUES
(7, 'slide_67d64c456774c6.40459077.jpeg'),
(29, 'slide_67d90386844086.76950686.jpg'),
(32, 'slide_694590d08b41d5.85304066.png'),
(33, 'slide_6945956f8cf789.78778884.jpg');
SELECT setval('slides_id_seq', 34);

-- --------------------------------------------------------
-- Table: students
-- --------------------------------------------------------
CREATE TABLE students (
  id SERIAL PRIMARY KEY,
  roll_no VARCHAR(50) DEFAULT NULL,
  student_name VARCHAR(100) DEFAULT NULL,
  father_address TEXT DEFAULT NULL,
  course_type VARCHAR(50) DEFAULT NULL,
  current_semester VARCHAR(50) DEFAULT NULL,
  admission_fees VARCHAR(100) DEFAULT NULL,
  category VARCHAR(10) DEFAULT NULL,
  mobile_no VARCHAR(15) DEFAULT NULL,
  email_id VARCHAR(100) DEFAULT NULL,
  exam_date DATE DEFAULT NULL,
  student_signature VARCHAR(255) DEFAULT NULL,
  subjects TEXT DEFAULT NULL,
  ex_subjects TEXT DEFAULT NULL,
  status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending','approved','rejected')),
  student_photo VARCHAR(255) DEFAULT NULL,
  previous_result TEXT DEFAULT NULL,
  can_edit SMALLINT DEFAULT 0
);

INSERT INTO students (id, roll_no, student_name, father_address, course_type, current_semester, admission_fees, category, mobile_no, email_id, exam_date, student_signature, subjects, ex_subjects, status, student_photo, previous_result, can_edit) VALUES
(1, '22030C04053', 'RISHABH KANKARIYA', 'Ramesh Kumar Kankariya, 58 Dani Gate Chandra Shekhar Azar marg Ujjain 456006', 'Grading', 'Regular 3rd', '11980/- 85455 14/10/25', 'GEN', '9876543210', 'rishabhkankariya69@gmail.com', '2026-01-08', 'uploads/typed_signature_1767813383.png', '[{"subject":"algorithm","semester":"3","paper_code":"1234","theory":1,"practical":1},{"subject":"computer network","semester":"3","paper_code":"1234","theory":1,"practical":1},{"subject":"dbms","semester":"3","paper_code":"1234","theory":1,"practical":1}]', '[]', 'pending', 'image/1767811607_webcam_capture.png', '[{"file_path":"results/1767813383_res_0_DocScanner 25 Aug 2025 7-33pm.pdf","type":"Regular","uploaded_at":"2026-01-08 00:46:23"}]', 1);
SELECT setval('students_id_seq', 2);

-- --------------------------------------------------------
-- Table: student_accounts
-- --------------------------------------------------------
CREATE TABLE student_accounts (
  id SERIAL PRIMARY KEY,
  roll_no VARCHAR(50) NOT NULL UNIQUE,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  dob DATE DEFAULT NULL,
  phone_number VARCHAR(15) DEFAULT NULL,
  course VARCHAR(50) NOT NULL DEFAULT 'Diploma',
  admission_year INT NOT NULL,
  expected_passing_year INT NOT NULL,
  profile_image VARCHAR(255) DEFAULT 'users.png',
  password_hash VARCHAR(255) NOT NULL,
  is_temp_password SMALLINT DEFAULT 1,
  account_status VARCHAR(20) DEFAULT 'active' CHECK (account_status IN ('active','completed','blocked','backlog')),
  last_login_at TIMESTAMP DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_login_ip VARCHAR(45) DEFAULT NULL,
  last_login_location VARCHAR(255) DEFAULT NULL,
  session_token VARCHAR(255) DEFAULT NULL
);

CREATE INDEX idx_student_accounts_email ON student_accounts(email);
CREATE INDEX idx_student_accounts_status ON student_accounts(account_status);

INSERT INTO student_accounts (id, roll_no, full_name, email, dob, phone_number, course, admission_year, expected_passing_year, profile_image, password_hash, is_temp_password, account_status, last_login_at, created_at, updated_at, last_login_ip, last_login_location, session_token) VALUES
(18, '22030C04053', 'Rishabh Kankariya', 'rishabhkankariya69@gmail.com', '2006-11-24', NULL, 'Computer Science', 2022, 2025, '2147483647_162556_176.jpg', '$2y$10$2TBGaE9Lg3CG0Ap2HDCTTeoFJjO5FqYcQOVrWADopxVrW9lC2sA8W', 0, 'active', '2026-01-14 01:31:46', '2026-01-13 20:00:06', '2026-01-13 20:01:47', '103.185.109.217', 'Pune, Maharashtra, India (411015)', NULL);
SELECT setval('student_accounts_id_seq', 19);

-- --------------------------------------------------------
-- Table: student_login_logs
-- --------------------------------------------------------
CREATE TABLE student_login_logs (
  id SERIAL PRIMARY KEY,
  student_id INT NOT NULL,
  ip_address VARCHAR(45) DEFAULT NULL,
  login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  location VARCHAR(255) DEFAULT NULL
);

CREATE INDEX idx_student_login_logs_student_id ON student_login_logs(student_id);

INSERT INTO student_login_logs (id, student_id, ip_address, login_time, location) VALUES
(1, 10, '::1', '2026-01-07 01:21:40', 'Localhost Access'),
(2, 10, '103.185.109.195', '2026-01-07 01:21:40', 'Pune, Maharashtra, India (411006)'),
(3, 10, '2405:201:3016:9b1a:f425:25cb:19c9:58b7', '2026-01-07 01:22:18', 'Indore, Madhya Pradesh, India (452001)'),
(4, 10, '::1', '2026-01-07 01:23:03', 'Localhost Access'),
(5, 10, '103.185.109.195', '2026-01-07 01:23:03', 'Pune, Maharashtra, India (411006)'),
(6, 10, '2405:201:3016:9b1a:f425:25cb:19c9:58b7', '2026-01-07 01:23:59', 'Indore, Madhya Pradesh, India (452001)'),
(7, 10, '103.185.109.195', '2026-01-07 01:24:13', 'Pune, Maharashtra, India (411006)'),
(8, 10, '2405:201:3016:9b1a:f425:25cb:19c9:58b7', '2026-01-07 01:24:46', 'Indore, Madhya Pradesh, India (452001)'),
(9, 10, '103.185.109.195', '2026-01-07 01:25:59', 'Pune, Maharashtra, India (411006)'),
(10, 10, '103.185.109.195', '2026-01-07 23:03:51', 'Pune, Maharashtra, India (411006)'),
(11, 10, '103.185.109.195', '2026-01-07 23:38:38', 'Pune, Maharashtra, India (411006)'),
(12, 10, '103.185.109.195', '2026-01-07 23:39:54', 'Pune, Maharashtra, India (411006)'),
(13, 6, '103.185.109.195', '2026-01-08 00:13:30', 'Pune, Maharashtra, India (411006)'),
(14, 6, '103.185.109.195', '2026-01-08 00:44:39', 'Pune, Maharashtra, India (411006)'),
(15, 6, '::1', '2026-01-10 22:40:12', 'Localhost Access'),
(16, 6, '49.36.56.106', '2026-01-10 22:40:12', 'Pune, Maharashtra, India (411007)'),
(17, 6, '103.185.109.217', '2026-01-11 17:30:02', 'Pune, Maharashtra, India (411015)'),
(18, 6, '103.185.109.217', '2026-01-11 17:36:02', 'Pune, Maharashtra, India (411015)'),
(19, 6, '103.185.109.217', '2026-01-12 01:07:16', 'Pune, Maharashtra, India (411015)'),
(20, 6, '103.185.109.217', '2026-01-12 01:13:25', 'Pune, Maharashtra, India (411015)'),
(21, 6, '103.185.109.217', '2026-01-14 01:15:27', 'Pune, Maharashtra, India (411015)'),
(22, 18, '103.185.109.217', '2026-01-14 01:31:46', 'Pune, Maharashtra, India (411015)');
SELECT setval('student_login_logs_id_seq', 23);

-- --------------------------------------------------------
-- Table: student_notes
-- --------------------------------------------------------
CREATE TABLE student_notes (
  id SERIAL PRIMARY KEY,
  semester VARCHAR(20) NOT NULL,
  subject_name VARCHAR(100) NOT NULL,
  image_url VARCHAR(255) NOT NULL,
  notes_link VARCHAR(255) NOT NULL
);

INSERT INTO student_notes (id, semester, subject_name, image_url, notes_link) VALUES
(1, 'firstsem', 'Mathematics -1', 'images/math.png', 'notes/1767731221_DS_All_9_Assignments_Cpp.pdf'),
(2, 'firstsem', 'Physics -1', 'images/Physics.png', 'notes/1768131630_Problem Statements related to University- Dr. Santosh Darade.pdf'),
(3, 'firstsem', 'Communication Skills & English', 'images/English.png', 'notes/cprogramming.pdf'),
(4, 'firstsem', 'Chemistry', 'images/Chemistry.png', 'notes/electrical.pdf'),
(5, 'secondsem', 'Mathematics -2', 'images/math2.png', 'notes/mechanics.pdf'),
(6, 'secondsem', 'Physics -2', 'images/Physics2.png', 'notes/digital.pdf'),
(9, 'secondsem', 'Introduction To IT System', 'images/Itsystem.png', 'notes/computer_org.pdf'),
(10, 'secondsem', 'FEEE', 'images/fee.png', 'notes/discrete_math.pdf'),
(11, 'secondsem', 'Engineering Mechanics', 'images/mechanics.png', 'notes/computer_org.pdf'),
(12, 'thirdsem', 'Scripting Languages', 'images/scripting.png', 'pdfs/.pdf'),
(13, 'thirdsem', 'Data Structures', 'images/data_structure.png', 'pdfs/.pdf'),
(14, 'thirdsem', 'Algorithm', 'images/algorithm.png', 'pdfs/.pdf'),
(15, 'thirdsem', 'Computer Programming', 'images/C.png', 'pdfs/Programming_in_C_paper.pdf'),
(16, 'thirdsem', 'Computer System Organisation', 'images/computer_sys.png', 'pdfs/.pdf'),
(17, '4thsem', 'Computer Networks', 'images/networks.png', 'pdfs/Computer_Network_Paper.pdf'),
(18, '4thsem', 'Operating Systems', 'images/os.png', 'pdfs/Operating_System_Paper.pdf'),
(19, '4thsem', 'Introduction to DBMS', 'images/dbms.png', 'pdfs/DBMS_Paper.pdf'),
(20, '4thsem', 'Web Technologies', 'images/web.png', 'pdfs/Web-tech_Paper.pdf'),
(21, '4thsem', 'SSAD/Software Engineering', 'images/ssad.png', 'pdfs/SSAD_Paper.pdf'),
(22, '5thsem', 'Operation Research', 'images/or.png', 'pdfs/.pdf'),
(23, '5thsem', 'Intro. To E-governance', 'images/egov.png', 'pdfs/E-governance_Paper.pdf'),
(24, '5thsem', 'Internet of Things', 'images/iot.png', 'pdfs/.pdf'),
(25, '5thsem', 'Information Security', 'images/security.png', 'pdfs/Information_Security_Paper.pdf'),
(26, '5thsem', 'MultiMedia Technologies', 'images/multimedia.png', 'pdfs/.pdf'),
(27, '5thsem', 'AD.Computer Networks', 'images/adnet.png', 'pdfs/.pdf'),
(28, '5thsem', 'Data Sciences', 'images/datasci.png', 'pdfs/.pdf'),
(29, '5thsem', 'Renewable Energy tech.', 'images/energy.png', 'pdfs/Renewable_Energy_Paper.pdf');
SELECT setval('student_notes_id_seq', 34);

-- --------------------------------------------------------
-- Table: syllabus
-- --------------------------------------------------------
CREATE TABLE syllabus (
  id SERIAL PRIMARY KEY,
  year VARCHAR(10) NOT NULL,
  semester VARCHAR(20) NOT NULL,
  subject_name VARCHAR(255) NOT NULL,
  pdf_path VARCHAR(255) NOT NULL
);

INSERT INTO syllabus (id, year, semester, subject_name, pdf_path) VALUES
(1, '1 Year', '1 Semester', 'Mathematics I', 'pdfs/Mathematics_I.pdf'),
(2, '1 Year', '1 Semester', 'Chemistry', 'pdfs/Chemistry.pdf'),
(3, '1 Year', '1 Semester', 'Engineering Graphics', 'pdfs/Engineering_Graphics.pdf'),
(4, '1 Year', '1 Semester', 'Communication Skills & English', 'pdfs/Communication_Skills.pdf'),
(5, '1 Year', '1 Semester', 'Physics 1', 'pdfs/Physics_1.pdf'),
(6, '1 Year', '1 Semester', 'Engineering WorkShop Practice', 'pdfs/Engineering_Workshop.pdf'),
(7, '1 Year', '1 Semester', 'Sports & Yoga', 'pdfs/Sports_Yoga.pdf'),
(8, '1 Year', '2 Semester', 'Mathematics II', 'pdfs/Mathematics_II.pdf'),
(9, '1 Year', '2 Semester', 'Introduction To IT System', 'pdfs/IT_System.pdf'),
(10, '1 Year', '2 Semester', 'FEEE', 'pdfs/FEEE.pdf'),
(11, '1 Year', '2 Semester', 'Physics 2', 'pdfs/Physics_2.pdf'),
(12, '1 Year', '2 Semester', 'Engineering Mechanics', 'pdfs/Engineering_Mechanics.pdf'),
(13, '1 Year', '2 Semester', 'Environmental Science', 'pdfs/Environmental_Science.pdf'),
(14, '2 Year', '3 Semester', 'Professional Development', 'pdfs/Professional_Development.pdf'),
(15, '2 Year', '3 Semester', 'Summer Internship I', 'pdfs/Summer_Internship_I.pdf'),
(16, '2 Year', '3 Semester', 'Scripting Languages', 'pdfs/Scripting_Languages.pdf'),
(17, '2 Year', '3 Semester', 'Data Structures', 'pdfs/Data_Structures.pdf'),
(18, '2 Year', '3 Semester', 'Algorithm', 'pdfs/Algorithm.pdf'),
(19, '2 Year', '3 Semester', 'Computer Programming', 'pdfs/Computer_Programming.pdf'),
(20, '2 Year', '3 Semester', 'Computer System Organisation', 'pdfs/CSO.pdf'),
(21, '2 Year', '4 Semester', 'Computer Networks', 'pdfs/Computer_Network.pdf'),
(22, '2 Year', '4 Semester', 'Operating Systems', 'pdfs/Operating_System.pdf'),
(23, '2 Year', '4 Semester', 'Introduction to DBMS', 'pdfs/DBMS.pdf'),
(24, '2 Year', '4 Semester', 'Indian Knowledge', 'pdfs/Indian_Knowledge.pdf'),
(25, '2 Year', '4 Semester', 'Web Technologies', 'pdfs/Web_Technologies.pdf'),
(26, '2 Year', '4 Semester', 'Minor Project', 'pdfs/Minor_Project.pdf'),
(27, '2 Year', '4 Semester', 'SSAD/Software Engineering', 'pdfs/Software_Engineering.pdf'),
(28, '3 Year', '5 Semester', 'Intro. To E-governance', 'pdfs/E_Governance.pdf'),
(29, '3 Year', '5 Semester', 'Internet of Things', 'pdfs/IoT.pdf'),
(30, '3 Year', '5 Semester', 'Information Security', 'pdfs/Information_Security.pdf'),
(31, '3 Year', '5 Semester', 'MultiMedia Technologies', 'pdfs/Multimedia_Technologies.pdf'),
(32, '3 Year', '5 Semester', 'AD.Computer Networks', 'pdfs/Advance_Computer_Network.pdf'),
(33, '3 Year', '5 Semester', 'Data Sciences', 'pdfs/Data_Science.pdf'),
(34, '3 Year', '5 Semester', 'Renewable Energy tech.', 'pdfs/Renewable_Energy.pdf'),
(35, '3 Year', '5 Semester', 'Operation Research', 'pdfs/Operational_Research.pdf'),
(36, '3 Year', '5 Semester', 'Summer Internship 2', 'pdfs/Summer_Internship_2.pdf'),
(37, '3 Year', '5 Semester', 'Major Project', 'pdfs/Major_Project.pdf'),
(38, '3 Year', '6 Semester', 'Entrepreneurship & Start-up', 'pdfs/Entrepreneurship and Start-UPS.pdf'),
(39, '3 Year', '6 Semester', 'Mobile Computing', 'pdfs/Mobile Computing.pdf'),
(40, '3 Year', '6 Semester', 'Network Forensics', 'pdfs/Network Forensic.pdf'),
(41, '3 Year', '6 Semester', 'Software Testing', 'pdfs/Software Testing.pdf'),
(42, '3 Year', '6 Semester', 'Free & Open Source Software', 'pdfs/Free Open source Software (Foss).pdf'),
(43, '3 Year', '6 Semester', 'Disaster Management', 'pdfs/DISASTER MANAGEMENT.pdf'),
(44, '3 Year', '6 Semester', 'Project Management', 'pdfs/Project management.pdf'),
(45, '3 Year', '6 Semester', 'Artificial Intelligence', 'pdfs/Artificial Intelligence.pdf'),
(46, '3 Year', '6 Semester', 'Engg.Eco & Accountancy', 'pdfs/Engineering Economics & Accountancy.pdf'),
(47, '3 Year', '6 Semester', 'Indian Constitution', 'pdfs/Indian constitution.pdf'),
(48, '3 Year', '6 Semester', 'Major Project', 'pdfs/Major Project.pdf'),
(49, '3 Year', '6 Semester', 'Seminar', 'pdfs/SEMINAR.pdf'),
(63, '1 Year', '2 Semester', 'project', 'pdfs/1744885497_Screenshot_2025-04-11_205923.png');
SELECT setval('syllabus_id_seq', 65);

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  email_verified SMALLINT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  profile_image VARCHAR(255) NOT NULL DEFAULT 'users.png'
);

INSERT INTO users (id, username, email, password, email_verified, created_at, profile_image) VALUES
(2, 'Rishabh', 'rishabhkankariya69@gmail.com', '$2y$10$BoD/hCEpjt1h9n/M1M6MCu/R7itbIauCsUl/nKqLgru99A4mtaEy6', 1, '2025-04-14 08:19:53', 'users.png');
SELECT setval('users_id_seq', 7);

-- --------------------------------------------------------
-- Table: videos
-- --------------------------------------------------------
CREATE TABLE videos (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  video_path VARCHAR(255) NOT NULL
);

INSERT INTO videos (id, title, video_path) VALUES
(43, 'VIDEO 3', 'video4_1744382170.mp4'),
(46, 'VIDEO 2', 'video2_1744383407.mp4'),
(49, 'VIDEO 1', 'video1_1744383721.mp4');
SELECT setval('videos_id_seq', 54);

-- --------------------------------------------------------
-- Table: visitor_count
-- --------------------------------------------------------
CREATE TABLE visitor_count (
  id SERIAL PRIMARY KEY,
  count INT NOT NULL
);

INSERT INTO visitor_count (id, count) VALUES
(1, 50);
SELECT setval('visitor_count_id_seq', 2);

-- --------------------------------------------------------
-- Foreign Keys
-- --------------------------------------------------------
ALTER TABLE challans
  ADD CONSTRAINT challans_student_fk FOREIGN KEY (student_id) REFERENCES students (id) ON DELETE CASCADE;

-- Note: student_login_logs does not enforce FK to allow historical/orphaned log entries
-- ALTER TABLE student_login_logs
--   ADD CONSTRAINT student_login_logs_student_fk FOREIGN KEY (student_id) REFERENCES student_accounts (id) ON DELETE CASCADE;

COMMIT;
