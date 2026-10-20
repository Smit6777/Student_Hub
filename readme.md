# 🎓 StudentHub — Digital Campus Portal

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6%2B-F7DF1E?logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![CSS3](https://img.shields.io/badge/CSS3-Responsive%20%26%20Dark%20Theme-1572B6?logo=css3&logoColor=white)](https://www.w3.org/Style/CSS/)
[![License](https://img.shields.io/badge/License-Academic%20Use-green)](#license)

A modern, responsive, full-stack digital campus portal designed to centralize academic resources, student records, campus events, administrative metrics, and data privacy controls into a single intuitive web application.

---

## 📌 Project Overview

StudentHub bridges the communication gap between university students, faculty members, and campus organizers. It replaces fragmented paper-based and disjointed campus systems with a secure, unified digital hub.

Developed across practical milestones covering:
- **Practicals 1–8**: Semantic HTML5 markup, responsive CSS3 grid/flexbox layouts, JavaScript DOM manipulation, image carousels, FAQ accordions, and persistent Dark Mode.
- **Practicals 9–10**: MySQL database integration, secure user authentication with Bcrypt password hashing, session expiration, and Role-Based Access Control (RBAC).
- **Practicals 11–12**: Relational student record CRUD operations, course mapping, multi-attribute filtering, and event management with poster image uploads.
- **Practicals 13–14**: Asynchronous Fetch API student management with live search and pagination, admin dashboard with aggregated SQL statistics, dynamic role-based menus, and persistent system audit logging.
- **Compliance & Security Hardening**: WCAG 2.1 AA accessibility, comprehensive Privacy Policy, Terms of Service, Cookie Policy, cookie consent banner, CSRF protection, login rate limiting, and GDPR-compliant account deletion.

---

## ✨ Key Features

### 1. 🔐 Authentication & Role-Based Access Control (RBAC)
- **Multi-Role User Architecture**: Dedicated access tiers for `student` and `admin` accounts.
- **Bcrypt Password Security**: Passwords salted and hashed with `password_hash(..., PASSWORD_DEFAULT)`.
- **Session Management**: Session regeneration (`session_regenerate_id(true)`) and automatic 15-minute inactivity timeout.
- **Persistent Login**: Optional 30-day "Remember Me" using cryptographically hashed tokens (`remember_token`) stored with `HttpOnly` flags.
- **Dynamic Menus**: Navigation dynamically renders relevant links based on the authenticated user's role.

### 2. 📊 Administrative Dashboard & System Audit Logging
- **Real-Time SQL Aggregates**: Displays total registered users, active students, upcoming events, and recent registrations.
- **Administrative Audit Logs**: Automatically records administrative actions (logins, student additions/edits/deletions, event updates, account deletions) with timestamps and IP addresses.
- **Role Verification Middleware**: Unauthorized users attempting to access `/pages/admin.php` are immediately redirected with appropriate error notices.

### 3. 👥 Student Directory Management (CRUD & Fetch API)
- **Synchronous & Asynchronous Views**: Supports traditional PHP form processing as well as live asynchronous Fetch API loading (`pages/students_ajax.php`).
- **Real-Time Search & Multi-Filters**: Filter students by enrolled course and academic year, or perform instant search by name and email.
- **Modal Add/Edit & Inline Delete**: Add or edit student records seamlessly with immediate UI updates.

### 4. 📅 Campus Events & Media Management
- **Event Management**: Create, update, and remove campus workshops, seminars, hackathons, and tech talks.
- **File Upload Security**: Secure event poster image uploading with MIME validation, file size limits, and sanitized storage paths.
- **Client-Side Filtering**: Live search and category-based event filtering.

### 5. 🛡️ Data Privacy, Legal Compliance & Right to Erasure
- **Legal Documentation**:
  - [Privacy Policy](pages/privacy.html): Discloses data collection, minimization, storage policies, and student rights.
  - [Terms of Service](pages/terms.html): Sets campus code of conduct, acceptable use, and academic integrity policies.
  - [Cookie Policy](pages/cookie-policy.html): Explains `PHPSESSID`, `remember_token`, and local storage preferences.
- **Cookie Consent Banner**: Accessible floating notification banner with "Accept All" and "Essential Only" options persisted in `localStorage`.
- **Permanent Account Deletion**: Self-service account and personal data erasure (`pages/delete_account.php` & `php/delete_account_process.php`) protected by password re-verification and audit logging.
- **Explicit Consent**: Consent checkboxes linking to legal terms on user registration, student registration, and contact forms.
- **Age Verification**: Registration forms enforce a minimum student age of 16 years.
- **Ethical UI**: Removed dark patterns; sample testimonials and demo statistics carry clear demonstration disclaimers.

### 6. 🔒 Security Hardening
- **Anti-CSRF Protection**: Form submissions validated against cryptographic session tokens (`php/csrf.php`).
- **Login Rate Limiting**: Throttles brute-force attempts — 5 consecutive failed login attempts locks login for 5 minutes (`php/process_login.php`).
- **SQL Injection Prevention**: Parameterized prepared statements (`bind_param` and PDO `execute`) applied across all database operations.
- **XSS Sanitization**: User outputs escaped with `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- **Defensive HTTP Headers**: Emits `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, and `Referrer-Policy: strict-origin-when-cross-origin` on all database connections.

### 7. ♿ Accessibility (WCAG 2.1 AA) & Design
- **Skip Link**: Off-screen `.skip-link` allows keyboard and screen reader users to jump directly to `#main-content`.
- **Keyboard Navigation**: High-visibility `:focus-visible` outline rings on all interactive elements.
- **Descriptive Alt Text**: Detailed `alt` descriptions on all portal imagery, sliders, and logos.
- **Color Contrast & Dark Mode**: Compliant contrast ratios across light and dark color schemes, switchable via header toggle.

---

## 📁 Project Structure

```text
studenthub/
├── .env.example                  # Environment configuration template
├── .gitignore                    # Git ignore file for local secrets and uploads
├── studenthub_schema.sql         # Complete MySQL relational database schema
├── readme.md                     # Comprehensive project documentation
├── css/
│   └── style.css                 # Master stylesheet (theme variables, layouts, WCAG focus, modal)
├── javascripts/
│   └── scripts.js                # Theme toggle, hamburger menu, slider, FAQ accordion, cookie banner
├── image/                        # Portal brand logos and event placeholder images
├── uploads/                      # Uploaded event poster images
├── data/
│   └── faqs.json                 # Static JSON data for campus FAQ module
├── pages/
│   ├── index.html                # Campus landing page with hero carousel and announcements
│   ├── about.html                # About StudentHub, mission, story, and campus stats
│   ├── login.php                 # User authentication page with CSRF and error alerts
│   ├── register_user.html        # Account creation form with age verification and consent
│   ├── register.html             # Student directory enrollment form
│   ├── dashboard.php             # Protected student dashboard with role menus and privacy links
│   ├── admin.php                 # Restricted administrative dashboard with metrics and audit logs
│   ├── students.php              # Full student directory with server-side CRUD and filters
│   ├── students_ajax.php         # Asynchronous student directory powered by Fetch API
│   ├── student_add.php           # Add new student form
│   ├── student_edit.php          # Edit existing student form
│   ├── events.html               # Public campus events directory with category filter
│   ├── events_manage.php         # Event management panel
│   ├── event_add.php             # Add event form with poster upload
│   ├── event_edit.php            # Edit event details and poster
│   ├── contact.html              # Contact and inquiry form with privacy consent
│   ├── feedback.html             # Student feedback submission page
│   ├── faq.html                  # Searchable and sortable campus FAQ
│   ├── profile.html              # Student profile management page
│   ├── privacy.html              # Official Privacy Policy
│   ├── terms.html                # Official Terms of Service
│   ├── cookie-policy.html        # Official Cookie Policy
│   └── delete_account.php        # Self-service account deletion confirmation page
└── php/
    ├── security_headers.php      # Defensive HTTP security headers helper
    ├── csrf.php                  # Anti-CSRF token generator and validator
    ├── auth_check.php            # Session validation, remember-token check, and RBAC helper
    ├── db_connect.php            # PDO database connection helper
    ├── db_connect_mysqli.php     # MySQLi database connection helper
    ├── audit_logger.php          # Centralized audit logging utility
    ├── role_menu.php             # Dynamic menu generator based on user role
    ├── process_login.php         # Login processor with rate limiting and CSRF check
    ├── process_user_registration.php # User account registration processor
    ├── process_registration_db.php   # Student directory registration processor
    ├── delete_account_process.php    # Permanent account deletion handler
    ├── logout.php                # Session destruction and cookie cleanup handler
    ├── student_add_process.php   # Student addition backend handler
    ├── student_edit_process.php  # Student update backend handler
    ├── student_delete.php        # Student deletion backend handler
    ├── event_add_process.php     # Event creation handler with file upload validation
    ├── event_edit_process.php    # Event update handler
    ├── event_delete.php          # Event deletion handler
    ├── view_registrations.php    # View submitted registrations
    └── view_students_db.php      # Query students helper
```

---

## 🗄️ Database Architecture

The relational database (`studenthub`) comprises five primary tables:

1. **`users`**: User account credentials, email, Bcrypt hashed password, role (`student` or `admin`), and hashed remember tokens.
2. **`courses`**: Academic degree programs (BCA, BSc IT, B.Tech, MCA, etc.).
3. **`students`**: Academic profile information including mobile, course foreign key, year, and gender.
4. **`events`**: Campus events, categories, locations, dates, descriptions, and uploaded poster paths.
5. **`audit_logs`**: System audit trail logging administrative actions, user IDs, action names, details, IP addresses, and timestamps.

---

## 🚀 Installation & Local Setup

### 1. Prerequisites
- [XAMPP](https://www.apachefriends.org/) (Apache 2.4+ and MySQL 8.0+ / MariaDB)
- PHP 8.0 or higher
- Git

### 2. Clone Repository
Clone into your local web server document root (e.g. `htdocs`):
```bash
cd "C:/xampp/htdocs"
git clone https://github.com/Smit6777/Student_Hub.git studenthub
cd studenthub
```

### 3. Setup Database
1. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Open **phpMyAdmin** (`http://localhost/phpmyadmin`) or MySQL CLI.
3. Create a new database named `studenthub`:
   ```sql
   CREATE DATABASE studenthub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
4. Import the provided schema file:
   ```bash
   mysql -u root studenthub < studenthub_schema.sql
   ```

### 4. Configure Environment
Copy the example environment configuration:
```bash
cp .env.example .env
```
Ensure your database parameters in `.env` match your local environment:
```ini
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=studenthub
```

### 5. Access the Application
Open your web browser and navigate to:
- **Landing Page**: `http://localhost/studenthub/pages/index.html`
- **Login Portal**: `http://localhost/studenthub/pages/login.php`
- **Admin Dashboard**: `http://localhost/studenthub/pages/admin.php`

---

## 🔑 Default Credentials (for Evaluation)

| Role | Email | Password | Access Level |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@studenthub.com` | `admin123` | Full administrative dashboard, user metrics, audit logs, CRUD |
| **Student** | `student@studenthub.com` | `student123` | Academic overview, event participation, profile, account deletion |

*(You can also create new student accounts anytime via the [Register Page](pages/register_user.html).)*

---

## 📜 Compliance & Accessibility Summary

- **WCAG 2.1 AA**: Form controls linked with explicit `<label for="">`, high contrast focus rings, skip navigation links, and screen-reader ARIA landmarks.
- **Privacy Compliance**: Full transparent disclosures on data retention, no third-party ad trackers, and self-service account deletion under the Right to Erasure.
- **Security Audit**: Prepared statements throughout, Bcrypt password hashing, session regeneration, CSRF mitigation, and login throttling.

---

## 👤 Author & Institutional Credits

- **Developer**: Smit Makwana
- **Enrollment / ID**: 25IT052
- **Institution**: CHARUSAT University Campus, Changa, Gujarat - 388421
- **Support Contact**: `smitmakwana6777@gmail.com` | +91 9998403012

---

## 📄 License

This project is developed for educational and academic campus demonstration purposes at CHARUSAT University. All rights reserved.
