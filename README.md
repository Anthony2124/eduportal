# EduPortal

A school portal for **Admins, Faculty and Students**, built with PHP and MySQL to run on XAMPP.

- **Students** create their own account (details → link Gmail → 6-digit code sent to Gmail) and sign in with their Student ID or Gmail and password.
- **Faculty** accounts are created by the Admin. Faculty sign in with their Employee ID or Gmail, password, and a code sent to their Gmail.
- **Admin** is a built-in account. It manages accounts and can open every page.
- Every page shares one sidebar and top bar, and each role only sees (and can only open) its own pages.

## Run it on your computer

1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Put this folder in `C:\xampp\htdocs\` and name it `eduportal`.
3. Create the database: open http://localhost/phpmyadmin, choose **Import**, and import `database/eduportal.sql`.
   (Already have an older copy of the database? Import `database/upgrade.sql` instead; it keeps existing accounts.)
4. Copy `api/config.example.php` to `api/config.php`. It works as-is in **demo mode**: codes are shown on screen instead of emailed.
   To email real codes, fill in a Gmail address and a Gmail **App Password** under `smtp`.
5. Open http://localhost/eduportal/

**Built-in admin:** choose **Admin** on the sign-in page, username `admin`, password `admin123`. Change this before using the portal for real.

## Where things are

| Path | What it does |
|---|---|
| `login.html` | Sign in and student sign-up |
| `index.php`, `faculty.php`, `admin.php` | Student, Faculty and Admin dashboards |
| `pages/` | Portal pages (courses, grades, messages, events, …) |
| `includes/app_shell.php`, `css/app-shell.css` | The shared sidebar and top bar |
| `api/auth.php` | Sign-in, sign-up and verification codes |
| `api/admin.php` | Admin actions (create faculty, delete accounts) |
| `api/bootstrap.php` | Shared setup, and `ROLE_PAGES`: which pages each role can open |
| `api/config.php` | Your settings (not in git; copy from `api/config.example.php`) |
| `database/` | Database setup and upgrade scripts |
