# E-Kitabghar - Diploma Exam Form System

## 📋 Project Overview
Online diploma exam form submission portal for 3-year diploma programs with multi-department support.

## 🚀 Quick Start

### Student Access
- **Registration**: `student_register.html`
- **Login**: `student_login.html`
- **Dashboard**: `dashboard.php`
- **Exam Form**: `exam_form.php`

### Admin Access
- **Login**: `admin/admin_login.php`
- **Dashboard**: `admin/adminpanel.php`
- **Manage Forms**: `admin/manage.php`
- **Departments**: `admin/manage_departments.php`

## 🗄️ Database
- **Type**: PostgreSQL
- **Connection**: `php/connection.php`
- **Tables**: students, departments, courses, semesters, subjects, admin, exam_settings

## 🎓 Program Structure
- **Duration**: 3 Years
- **Semesters**: 6 (2 per year)
- **Departments**: 9 (CSE, ME, EE, CE, ECE, IT, CHE, CTM, PT)

## 📁 Project Structure
```
website - Copy/
├── index.php                 # Homepage
├── student_login.html        # Student login
├── dashboard.php             # Student dashboard
├── exam_form.php             # Exam form submission
├── profile_update.php        # Profile management
│
├── admin/                    # Admin module
│   ├── admin_login.php       # Admin login
│   ├── adminpanel.php        # Admin dashboard
│   ├── manage.php            # Exam form management
│   ├── manage_departments.php # Department CRUD
│   ├── manage_students.php   # Student management
│   └── backend/              # API endpoints
│
├── php/                      # Backend scripts
│   ├── connection.php        # Database connection
│   ├── student_exam_form.php # Form submission handler
│   └── api/                  # API endpoints
│
├── notes/                    # Semester notes
├── library/                  # Library module
├── image/                    # Images
├── uploads/                  # Student uploads
└── config/                   # Configuration files
```

## 🔧 Configuration
1. Copy `.env.example` to `.env`
2. Update database credentials
3. Configure email settings in `config/send_mail.php`

## 🎨 Branding
- **Favicon**: Root directory (favicon.ico, apple-touch-icon.png, etc.)
- **Logo**: `image/` directory
- **Theme Color**: #4F46E5 (Indigo)

## 📧 Email Notifications
- Registration confirmation
- Form submission confirmation
- Approval/Rejection notifications
- Custom admin messages

## 🔐 Security Features
- Password hashing (bcrypt)
- Session management
- Single device login
- SQL injection prevention (PDO)
- File upload validation
- CSRF protection

## 🌐 Browser Support
- Chrome/Edge ✅
- Firefox ✅
- Safari ✅
- Mobile browsers ✅

## 📱 Mobile Support
- Responsive design
- PWA-ready
- Mobile-optimized forms

## 🛠️ Maintenance
- Clear browser cache after updates
- Backup database regularly
- Monitor error logs
- Update dependencies

## 📞 Support
For issues or questions, contact the system administrator.

---

**Version**: 1.0  
**Last Updated**: May 25, 2026  
**Status**: Production Ready
