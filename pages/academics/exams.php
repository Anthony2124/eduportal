<?php
// Exams: faculty (their courses) and the admin (every course) schedule, edit and remove them;
// students see the ones for courses they're enrolled in.
require __DIR__ . '/../../includes/app_shell.php';
require __DIR__ . '/../../includes/content.php';
$page = 'pages/academics/exams.php';
$user = require_page_access($page);

[$where, $params] = scoped($user, 'a.exam_date >= CURDATE()');
$upcoming = content_items('exams', $where, $params);
[$where, $params] = scoped($user, 'a.exam_date < CURDATE()');
$past = array_reverse(content_items('exams', $where, $params)); // most recent first

$isStudent = $user['role'] === 'student';
$subtitle = match ($user['role']) {
    'student' => 'Exam schedules for the courses you are enrolled in',
    'faculty' => 'Exams for the courses you teach',
    default => 'Exams for every course',
};

app_page_start($user, $page, 'Examinations');
app_page_header('Examinations', $subtitle);
app_stats([
    ['fa-calendar-day', 'warning', (string) count($upcoming), 'Upcoming exams'],
    ['fa-circle-check', 'success', (string) count($past), 'Completed exams'],
]);
?>
            <section class="card section-gap">
                <?php content_card_header($user, 'exams', 'fa-calendar-alt', 'Upcoming Exams'); ?>
                <?php exam_list($user, $upcoming, false, 'No exams scheduled',
                    $isStudent ? "Exam schedules appear here once they're released." : 'Use "Add exam" to schedule one for a course.'); ?>
            </section>

            <?php if ($past): ?>
            <section class="card section-gap">
                <?php app_card_header('fa-clock-rotate-left', 'Past Exams'); ?>
                <?php exam_list($user, $past); ?>
            </section>
            <?php endif; ?>
<?php
content_form($user, 'exams');
app_page_end();
