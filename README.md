# EduPortal

A school portal for **Admins, Faculty and Students**, built with PHP and MySQL to run on XAMPP.

- **Students** create their own account (details → link Gmail → 6-digit code sent to Gmail) and sign in with their Student ID or Gmail and password.
- **Faculty** accounts are created by the Admin. Faculty sign in with their Employee ID or Gmail, password, and a code sent to their Gmail.
- **Admin** is a built-in account. It manages accounts and can open every page.
- Every page shares one sidebar and top bar, and each role only sees (and can only open) its own pages.
- **Add / Edit / Remove:** the Admin manages Courses (and assigns faculty), Events and Scholarships;
  the Admin and Faculty post Announcements (Faculty can change only their own). Everything is saved in MySQL.
- **Classes:** on **Enrollment** the Admin or the course's Faculty adds students to a course. Faculty then post
  **Assignments** and **Exams** and enter **Grades** for their own courses (the Admin doesn't have these pages).
  Students see only the courses they're enrolled in: their schedule, assignments, exams, grades and GPA.
- **Grades** use the 1.00–5.00 scale: 1.00 is the highest, 3.00 the lowest passing grade, 5.00 failed.
  The final rating is the average of the midterm and final grades, and the GPA is weighted by units.

## Run it on your computer

1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Put this folder in `C:\xampp\htdocs\` and name it `eduportal`.
3. Create the database: open http://localhost/phpmyadmin, choose **Import**, and import `database/eduportal.sql`.
   (Already have an older copy of the database? Import `database/upgrade.sql` instead; it keeps existing accounts.)
4. Copy `api/config.example.php` to `api/config.php`, then fill in the Gmail account that sends the 6-digit codes
   under `smtp`: the Gmail address and a Gmail **App Password**
   (Google Account → Security → turn on 2-Step Verification → App passwords → create one).
   Without this, student sign-up and faculty sign-in show "Verification emails aren't set up".
   Just testing on your own computer? Set `'demo_mode' => true` instead, and codes are shown on screen.
5. Open http://localhost/eduportal/

**Built-in admin:** choose **Admin** on the sign-in page, username `admin`, password `admin123`. Change this before using the portal for real.

## Getting updates

1. In the `eduportal` folder, run `git pull`.
2. Then import `database/upgrade.sql` in phpMyAdmin (**Import**). It adds any new tables and keeps your existing
   accounts and data, and it is safe to import more than once. Skipping it makes pages like Courses or Grades show
   a database error after an update that added tables (the Enrollment, Assignments, Exams and Grades update did).

## Where things are

| Path | What it does |
|---|---|
| `login.html` | Sign in and student sign-up |
| `index.php`, `faculty.php`, `admin.php` | Student, Faculty and Admin dashboards |
| `pages/` | Portal pages (courses, grades, messages, events, …) |
| `includes/app_shell.php`, `css/app-shell.css` | The shared sidebar and top bar |
| `api/auth.php` | Sign-in, sign-up and verification codes |
| `api/admin.php` | Admin actions (create faculty, delete accounts) |
| `includes/content.php`, `api/content.php`, `js/content.js` | Add / Edit / Remove for courses, assignments, exams, events, scholarships and announcements (fields and permissions are in `CONTENT_TYPES`) |
| `includes/academics.php`, `api/academics.php` | Enrollment and grades (rosters, GPA, the grading scale) |
| `api/bootstrap.php` | Shared setup, and `ROLE_PAGES`: which pages each role can open |
| `api/config.php` | Your settings (not in git; copy from `api/config.example.php`) |
| `database/` | Database setup and upgrade scripts |
