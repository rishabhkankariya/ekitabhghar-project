# Kitabghar - Digital Library & Student Portal

> **Status**: Production Ready  
> **Version**: 1.0.0

## 1. Project Description
Kitabghar is a comprehensive web-based platform designed to bridge the gap between students and academic resources. It features a robust student portal for accessing study materials, exam forms, and notes, alongside a powerful admin panel for managing content, users, and announcements.

## 2. Features
- **Student Module**:
    - Secure Registration & Login (w/ OTP Verification).
    - Access to Notes, Question Papers, and Syllabus.
    - Online Exam Form Submission.
    - Feedback & Grievance System.
- **Admin Panel**:
    - Dashboard with Analytics (Visitor Count, User Stats).
    - CRUD Management for Notes, Notices, and Students.
    - Exam & Syllabus Management.
- **Security**:
    - Encrypted Passwords (Bcrypt).
    - SQL Injection Protection (Prepared Statements).
    - Custom CAPTCHA Protection.
- **Utilities**:
    - PDF Generation (Result/Form slips).
    - Email Notifications (SMTP).

## 3. Tech Stack
- **Frontend**: HTML5, Tailwind CSS, JavaScript (Vanilla), Bootstrap Icons.
- **Backend**: Core PHP (7.4+).
- **Database**: MySQL.
- **Libraries**:
    - `PHPMailer` (Email Service).
    - `mPDF` (PDF Generation - likely unused).
    - `TCPDF` (PDF Generation - Active).
    - `AOS` (Scroll Animations).

## 4. Folder Structure
```text
/ (Root)
├── admin/              # Admin panel files & logic
│   ├── php/            # Admin-specific backend scripts
│   └── ...
├── css/                # Custom stylesheets
├── img/                # Images & assets
├── php/                # Core backend logic
│   ├── connection.php  # Database connection
│   ├── fonts/          # Fonts for CAPTCHA (arial.ttf)
│   ├── signup.php      # Registration logic
│   └── ...
├── vendor/             # Composer dependencies (PHPMailer, mPDF)
├── index.php           # Landing Page
├── student_login.html  # Student Login Page
└── README.md           # Documentation
```

## 5. Installation & Setup

### A. Local Setup (XAMPP/WAMP)
1.  **Download Request**: Clone or download this repository.
2.  **Move Files**: Place the project files directly inside `C:\xampp\htdocs\`.
3.  **Database**:
    - Open phpMyAdmin (`http://localhost/phpmyadmin`).
    - Create a database named `ekitabhghar`.
    - Import the SQL file (if provided) or ensure tables `users` and `admin` exist.
4.  **Configuration**:
    - Open `php/connection.php`.
    - Updates credentials if you changed the default root/empty password.
5.  **Run**: Open `http://localhost/index.php` in your browser.

### B. Hosting Setup (cPanel / Shared Hosting)
1.  **Upload Files**:
    - Zip the project files.
    - Upload to `public_html` via File Manager or FTP.
    - Extract properly.
2.  **Database Creation**:
    - Go to **MySQL Databases** in cPanel.
    - Create a new database and a user.
    - Assign the user to the database with **ALL PRIVILEGES**.
3.  **Connect Database**:
    - Edit `php/connection.php` with your new credentials:
    ```php
    $servername = "localhost";
    $username = "your_db_user";
    $password = "your_db_pass";
    $dbname = "your_db_name";
    ```
4.  **PHP Version**: Ensure your hosting is set to **PHP 7.4** or **8.1** via "MultiPHP Manager".

## 6. Environment Configuration
**Important**: Before going live, you must configure the Email Service.
1.  Open `php/signup.php` (and any file used for sending emails).
2.  Locate the SMTP settings:
    ```php
    // In php/signup.php
    $mail->Username = 'your_email@gmail.com';
    $mail->Password = 'your_app_password'; // Use App Password, NOT email password
    ```
3.  **Security Tip**: It is highly recommended to move these credentials to a secure environment file or config file outside the public root in production.

## 7. Database Setup
Ensure your database has the following core tables. Run this SQL if setting up manually:
```sql
CREATE DATABASE IF NOT EXISTS `ekitabhghar`;
USE `ekitabhghar`;

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `admin` (
    `admin_id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL
);
-- Note: Insert a default admin user using password_hash() manually first.
```

## 8. Deployment Steps
1.  **Prepare Files**: Ensure `vendor/` folder is included (contains PHPMailer/mPDF).
2.  **Upload**: Use FileZilla (FTP) or cPanel File Manager.
3.  **Permissions**:
    - Folders: `755`
    - Files: `644`
    - `uploads/` folder (if exists): `755` or `777` (use caution).
4.  **Test**: Visit your domain. Try logging in to verify the session and database work.

## 9. Common Errors & Fixes
| Error | Cause | Fix |
|-------|-------|-----|
| **Connection Failed** | Wrong DB credentials | Check `php/connection.php` username/password. |
| **Email not sending** | SMTP blocked / Wrong Pass | Enable "Less Secure Apps" or Generate "App Password" in Google Account. |
| **CAPTCHA broken** | GD Library missing | Enable `extension=gd` in hosting `php.ini`. |
| **404 on Redirect** | Hardcoded Paths | Search for `/` in code and change to `/` if on root domain. |

## 10. Security Notes
- **HTTPS**: Always use SSL (https://) to protect login data.
- **Passwords**: User passwords are hashed. Do not modify the hashing logic.
- **Admin**: Change the default admin path or protect it with IP restrictions if possible.
- **Credentials**: Detected SMTP credentials in `php/signup.php`. Change these immediately before deployment.

## 11. Author & License
**Developed by**: Kitabghar Team
**License**: MIT License - Free for educational and commercial use.
