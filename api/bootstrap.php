<?php
/*
 * Shared setup for every PHP file: settings, session, database and small helpers.
 */
declare(strict_types=1);

function config(): array
{
    static $config = null;
    if ($config === null && !is_file(__DIR__ . '/config.php')) {
        http_response_code(500);
        exit('EduPortal is not set up yet: copy api/config.example.php to api/config.php and fill it in.');
    }
    return $config ??= require __DIR__ . '/config.php';
}

date_default_timezone_set(config()['timezone']);

// Sessions live in their own folder so other XAMPP apps (e.g. phpMyAdmin) can't clean them
// up early and break "Remember me".
$sessionDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'eduportal_sessions';
if (!is_dir($sessionDir)) {
    mkdir($sessionDir, 0700, true);
}
session_save_path($sessionDir);
ini_set('session.gc_maxlifetime', (string) (config()['remember_days'] * 86400));
session_name('EDUPORTALSESSID');
session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
session_start();

function db(): PDO
{
    static $pdo = null;
    if ($pdo) {
        return $pdo;
    }

    $db = config()['db'];
    try {
        $pdo = new PDO(
            "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4",
            $db['user'],
            $db['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $e) {
        error_log('[EduPortal] Database connection failed: ' . $e->getMessage());
        $message = 'Cannot connect to the database. Start MySQL in the XAMPP Control Panel '
            . 'and import database/eduportal.sql in phpMyAdmin.';
        if (defined('EDUPORTAL_API')) {
            fail($message, 500);
        }
        http_response_code(500);
        exit($message);
    }
    return $pdo;
}

function json_response(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data);
    exit;
}

function fail(string $message, int $status = 400, array $extra = []): never
{
    json_response(['error' => $message] + $extra, $status);
}

function find_user_by_id(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

const ROLE_NAMES = ['admin' => 'Admin', 'faculty' => 'Faculty', 'student' => 'Student'];

// Each role's home page
const DASHBOARDS = ['student' => 'index.php', 'faculty' => 'faculty.php', 'admin' => 'admin.php'];

// Every portal page, grouped like the sidebar: path => [icon, label]
const PAGE_SECTIONS = [
    'Academics' => [
        'pages/academics/courses.php' => ['fa-book', 'Courses'],
        'pages/academics/assignments.php' => ['fa-tasks', 'Assignments'],
        'pages/academics/grades.php' => ['fa-chart-bar', 'Grades'],
        'pages/academics/schedule.php' => ['fa-calendar-alt', 'Schedule'],
        'pages/academics/exams.php' => ['fa-file-alt', 'Exams'],
    ],
    'Administrative' => [
        'pages/admin/enrollment.php' => ['fa-user-plus', 'Enrollment'],
        'pages/admin/scholarships.php' => ['fa-graduation-cap', 'Scholarships'],
    ],
    'Communication' => [
        'pages/communication/messaging.php' => ['fa-envelope', 'Messages'],
        'pages/communication/announcements.php' => ['fa-bullhorn', 'Announcements'],
        'pages/communication/forums.php' => ['fa-comments', 'Forums'],
        'pages/communication/office-hours.php' => ['fa-video', 'Office Hours'],
    ],
    'Student Life' => [
        'pages/student-life/events.php' => ['fa-calendar', 'Events'],
        'pages/student-life/clubs.php' => ['fa-users', 'Clubs'],
    ],
    'Smart Features' => [
        'pages/smart/analytics.php' => ['fa-chart-line', 'Analytics'],
    ],
    'Settings' => [
        'pages/settings/profile.php' => ['fa-cog', 'Settings'],
    ],
];

// Which of those pages each role can open ('*' = all of them). Edit this to change access.
// Only admins can manage accounts (admin.php) no matter what is listed here.
const ROLE_PAGES = [
    // Admin: no Assignments, Grades, Schedule or Exams (faculty run their classes). Courses stays so the
    // admin can set up courses and assign faculty; it's linked at the top of the admin menu instead.
    'admin' => [
        'pages/academics/courses.php',
        'pages/admin/enrollment.php',
        'pages/admin/scholarships.php',
        'pages/communication/messaging.php',
        'pages/communication/announcements.php',
        'pages/communication/forums.php',
        'pages/communication/office-hours.php',
        'pages/student-life/events.php',
        'pages/student-life/clubs.php',
        'pages/smart/analytics.php',
        'pages/settings/profile.php',
    ],
    'faculty' => [
        'pages/academics/courses.php',
        'pages/academics/assignments.php',
        'pages/academics/grades.php',
        'pages/academics/schedule.php',
        'pages/academics/exams.php',
        'pages/admin/enrollment.php',
        'pages/communication/messaging.php',
        'pages/communication/announcements.php',
        'pages/communication/forums.php',
        'pages/communication/office-hours.php',
        'pages/student-life/events.php',
        'pages/settings/profile.php',
    ],
    // Students: no Enrollment (admin + faculty) or Analytics (admin only)
    'student' => [
        'pages/academics/courses.php',
        'pages/academics/assignments.php',
        'pages/academics/grades.php',
        'pages/academics/schedule.php',
        'pages/academics/exams.php',
        'pages/admin/scholarships.php',
        'pages/communication/messaging.php',
        'pages/communication/announcements.php',
        'pages/communication/forums.php',
        'pages/communication/office-hours.php',
        'pages/student-life/events.php',
        'pages/student-life/clubs.php',
        'pages/settings/profile.php',
    ],
];

/** The pages (paths from PAGE_SECTIONS) a role may open. */
function allowed_pages(string $role): array
{
    $allPages = array_merge(...array_map('array_keys', array_values(PAGE_SECTIONS)));
    return ROLE_PAGES[$role] === '*' ? $allPages : array_values(array_intersect($allPages, ROLE_PAGES[$role]));
}

// Departments a faculty account can belong to (chosen by the admin when creating it)
const DEPARTMENTS = [
    'College of Computer Studies',
    'College of Business and Accountancy',
    'College of Arts and Sciences',
    'College of Engineering',
    'College of Education',
];

/** Password rule for every account: at least 8 characters with a letter and a number. */
function is_valid_password(string $password): bool
{
    return strlen($password) >= 8 && preg_match('/[A-Za-z]/', $password) && preg_match('/\d/', $password);
}

/**
 * For dashboard pages: returns the signed-in user. Signed-out visitors go to the sign-in page,
 * and users of another role go to their own dashboard.
 */
function require_page_role(string $role): array
{
    $user = current_user();
    if (!$user) {
        header('Location: login.html');
        exit;
    }
    if ($user['role'] !== $role) {
        header('Location: ' . DASHBOARDS[$user['role']]);
        exit;
    }
    return $user;
}

/** Reads the JSON body of an API call (POST + application/json only). */
function read_json_request(): array
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        fail('Method not allowed.', 405);
    }
    // Requiring a JSON body blocks plain cross-site form posts
    if (!str_starts_with($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
        fail('Expected a JSON request.', 415);
    }
    $input = json_decode(file_get_contents('php://input') ?: '{}', true);
    if (!is_array($input)) {
        fail('Invalid JSON.', 400);
    }
    return $input;
}

/** HTML-escapes a value for output in a page. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function initials(array $user): string
{
    return strtoupper(mb_substr($user['first_name'], 0, 1) . mb_substr($user['last_name'], 0, 1));
}

/** Line shown under the user's name, e.g. "BS Computer Science IV" or "Faculty · College of Engineering". */
function user_subtitle(array $user): string
{
    if ($user['role'] === 'student') {
        $yearRoman = ['I', 'II', 'III', 'IV', 'V'][(int) $user['year_level'] - 1] ?? '';
        return trim($user['program'] . ' ' . $yearRoman);
    }
    return ROLE_NAMES[$user['role']] . ' · ' . $user['department'];
}

/** Finds a user by ID number (Student ID / Employee ID) or linked Gmail (both are stored normalized). */
function find_user_by_login(string $identifier): ?array
{
    $identifier = trim($identifier);
    $stmt = db()->prepare('SELECT * FROM users WHERE id_number = ? OR email = ? LIMIT 1');
    $stmt->execute([strtoupper($identifier), strtolower($identifier)]);
    return $stmt->fetch() ?: null;
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    return find_user_by_id((int) $_SESSION['user_id']);
}

function start_user_session(array $user, bool $remember): void
{
    session_regenerate_id(true);
    $_SESSION = ['user_id' => (int) $user['id']];

    if ($remember) {
        setcookie(session_name(), session_id(), [
            'expires' => time() + config()['remember_days'] * 86400,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    db()->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?')->execute([$user['id']]);
}

function end_user_session(): void
{
    $_SESSION = [];
    setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => '/']);
    session_destroy();
}

function mask_email(string $email): string
{
    [$name, $domain] = explode('@', $email, 2);
    return substr($name, 0, 2) . '***@' . $domain;
}
