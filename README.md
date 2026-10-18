# EduPortal

A school portal for **Admins, Faculty and Students**, built with PHP and MySQL to run on XAMPP.

- **Students** create their own account (details, including program, year level and gender → link Gmail → 6-digit code sent to Gmail) and sign in with their Student ID or Gmail and password.
- **Faculty** accounts are created by the Admin. Faculty sign in with their Employee ID or Gmail, password, and a code sent to their Gmail.
- **Admin** is a built-in account. It manages accounts and can open every page.
- Every page shares one sidebar and top bar, and each role only sees (and can only open) its own pages.
- **Add / Edit / Remove:** the Admin manages Courses, Events and Scholarships;
  the Admin and Faculty post Announcements (Faculty can change only their own). Everything is saved in MySQL.
- **Courses** are classes: a course of a **program** (e.g. BS Information Technology; the Admin picks the program
  first, then adds the course) offered to a **block** (e.g. BSIT 2-A) with its days, time and room. The same
  course can be offered to several blocks. The Admin can assign the instructor, or leave it open: Faculty then pick
  open classes on the Courses page (**Teach this class**) and take on that block and schedule. A class can't be
  picked if it clashes with one the instructor already teaches, and a block can't have two classes at once.
- **Classes:** on **Enrollment** the Admin or the course's Faculty adds students of the course's program to it. Faculty then post
  **Assignments** and **Exams** and enter **Grades** for their own courses (the Admin doesn't have these pages).
  Students see only the courses they're enrolled in: their schedule, assignments, exams, grades and GPA.
- **Group chats** (Messages): every course has a class chat. Students are added automatically when they're enrolled
  (and removed when they leave the course), together with the course's Faculty. Every program also has a chat for
  the Admin and the Faculty who teach its classes. The Admin is only in the program chats, not the class chats.
  New messages show up within a few seconds, with unread counts in the chat list.
- **Click for details:** announcements, events, scholarships, courses, assignments, exams and grades open a
  details dialog when clicked (on the dashboard too, with a link to the full page).
- **Schedule** shows the week as a bar chart: a row per day, a bar per class, a color per subject. Click a bar
  for the class details.
- **Activity Logs** (Admin): sign-ins (and failed ones), account changes, posts, courses, enrollment and grades,
  with who did it, when and from which IP. Filter by role or kind of activity, or search. The latest show on the
  Admin dashboard.
- **Analytics** (Admin): students per program (bar chart), by gender (pie chart) and by year level (column chart).
  Clicking a bar, slice or column filters the other charts; hovering shows the numbers, and a table repeats them.
  Students from before gender was asked can set it in Settings.
- **Grades** (prelim, midterm and final) use the 1.00–5.00 scale: 1.00 is the highest, 3.00 the lowest passing
  grade, 5.00 failed. The final rating is the average of the three, and the GPA is weighted by units.

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
2. Then import `database/upgrade.sql` in phpMyAdmin (**Import**). It adds any new tables and columns and keeps your
   existing accounts and data, and it is safe to import more than once. Skipping it makes pages like Courses or
   Grades show a database error after an update that changed the database (the Enrollment/Grades, group chat, program and
   blocks/prelim updates did).

## Where things are

| Path | What it does |
|---|---|
| `login.html` | Sign in and student sign-up |
| `index.php`, `faculty.php`, `admin.php` | Student, Faculty and Admin dashboards |
| `pages/` | Portal pages (courses, grades, messages, events, …) |
| `includes/app_shell.php`, `css/app-shell.css` | The shared sidebar and top bar |
| `api/auth.php` | Sign-in, sign-up and verification codes |
| `api/admin.php` | Admin actions (create faculty, delete accounts) |
| `pages/admin/logs.php` | Activity Logs; entries are written by `log_activity()` in `api/bootstrap.php` |
| `pages/smart/analytics.php`, `js/analytics.js` | Analytics charts |
| `api/profile.php` | Students set their gender (Settings) |
| `includes/content.php`, `api/content.php`, `js/content.js` | Add / Edit / Remove for courses, assignments, exams, events, scholarships and announcements (fields and permissions are in `CONTENT_TYPES`) |
| `includes/academics.php`, `api/academics.php` | Enrollment and grades (rosters, GPA, the grading scale) |
| `includes/chat.php`, `api/chat.php`, `js/chat.js` | Class and program group chats on the Messages page (who is in each chat, sending, unread counts) |
| `api/bootstrap.php` | Shared setup, and `ROLE_PAGES`: which pages each role can open |
| `api/config.php` | Your settings (not in git; copy from `api/config.example.php`) |
| `database/` | Database setup and upgrade scripts |
