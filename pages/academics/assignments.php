<?php
// Assignments: faculty add, edit and remove them for their courses; students see the ones for courses they're
// enrolled in, with where their work is. Each opens its own page (pages/academics/assignment.php) to turn in work.
require __DIR__ . '/../../includes/app_shell.php';
require __DIR__ . '/../../includes/submissions.php';
$page = 'pages/academics/assignments.php';
$user = require_page_access($page);

[$where, $params] = scoped($user, 'a.due_date >= CURDATE()');
$upcoming = content_items('assignments', $where, $params);
[$where, $params] = scoped($user, 'a.due_date < CURDATE()');
$past = array_reverse(content_items('assignments', $where, $params)); // most recent first

$isStudent = $user['role'] === 'student';
$subtitle = match ($user['role']) {
    'student' => 'Coursework from the courses you are enrolled in',
    'faculty' => 'Assignments for the courses you teach',
    default => 'Assignments for every course',
};

app_page_start($user, $page, 'Assignments');
app_page_header('Assignments', $subtitle);
app_stats([
    ['fa-hourglass-half', 'warning', (string) count($upcoming), 'Upcoming'],
    ['fa-clock', 'danger', (string) count($past), $isStudent ? 'Past due' : 'Past'],
]);
?>
            <section class="card section-gap">
                <?php content_card_header($user, 'assignments', 'fa-tasks', 'Upcoming Assignments'); ?>
                <?php assignment_list($user, $upcoming, false, 'No upcoming assignments',
                    $isStudent ? 'Assignments from your courses will appear here.' : 'Use "Add assignment" to post one for a course.'); ?>
            </section>

            <?php if ($past): ?>
            <section class="card section-gap">
                <?php app_card_header('fa-clock-rotate-left', 'Past Assignments'); ?>
                <?php assignment_list($user, $past); ?>
            </section>
            <?php endif; ?>
<?php
content_form($user, 'assignments');
app_page_end();
