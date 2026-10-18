<?php
// Student dashboard. Signed-out visitors go to sign in; faculty and admins go to their own dashboards.
require __DIR__ . '/api/bootstrap.php';
require __DIR__ . '/includes/app_shell.php';
require __DIR__ . '/includes/academics.php';

$user = require_page_role('student');

// Everything below is this student's real data
$myCourses = student_courses((int) $user['id']);
$gpa = gpa($myCourses);
[$where, $params] = scoped($user, 'a.due_date >= CURDATE()');
$assignmentsDue = content_items('assignments', $where, $params);
// still to do: not turned in (or returned) yet
$myWork = student_submissions((int) $user['id'], array_map('intval', array_column($assignmentsDue, 'id')));
$assignmentsDue = array_values(array_filter($assignmentsDue,
    fn($a) => in_array(submission_status($a, $myWork[$a['id']] ?? null)[0], ['assigned', 'missing'], true)));
[$where, $params] = scoped($user, 'a.exam_date >= CURDATE()');
$upcomingExams = content_items('exams', $where, $params);
$recentGrades = array_filter($myCourses, 'has_grades');
usort($recentGrades, fn($a, $b) => strcmp((string) $b['graded_at'], (string) $a['graded_at']));
$recentGrades = array_slice($recentGrades, 0, 5);
$latestAnnouncements = content_items('announcements', '', [], 3);
$upcomingEvents = content_items('events', 'a.event_date >= CURDATE()', [], 3);

// Shortcuts to the student's own pages (only ones this role can open)
$quickLinks = [
    ['pages/academics/courses.php', 'fa-book', 'Courses'],
    ['pages/academics/grades.php', 'fa-chart-bar', 'Grades'],
    ['pages/academics/schedule.php', 'fa-calendar-alt', 'Schedule'],
    ['pages/communication/messaging.php', 'fa-envelope', 'Messages'],
    ['pages/admin/scholarships.php', 'fa-graduation-cap', 'Scholarships'],
    ['pages/academics/exams.php', 'fa-file-alt', 'Exams'],
];
$allowed = allowed_pages('student');

// Card header with a "View all" link
$cardHeader = function (string $icon, string $title, string $href) {
    ?>
                        <div class="card-header">
                            <div class="card-title"><i class="fa-solid <?= e($icon) ?>" style="color: var(--primary);"></i> <?= e($title) ?></div>
                            <a href="<?= e($href) ?>" class="card-link">View all</a>
                        </div>
    <?php
};

app_page_start($user, 'index.php', 'Dashboard');
?>
            <section class="staff-banner">
                <div>
                    <h2><?= e(time_greeting()) ?>, <?= e($user['first_name']) ?>!</h2>
                    <p><?= e(user_subtitle($user)) ?> • Student ID <?= e($user['id_number']) ?></p>
                </div>
                <div class="banner-chips">
                    <span class="banner-chip"><i class="fa-regular fa-calendar"></i> <?= e(date('l, F j, Y')) ?></span>
                </div>
            </section>

<?php
app_stats([
    ['fa-book', 'primary', (string) count($myCourses), 'Courses enrolled'],
    ['fa-tasks', 'warning', (string) count($assignmentsDue), 'Assignments due'],
    ['fa-chart-line', 'success', $gpa === null ? '—' : number_format($gpa, 2), 'Current GPA'],
    ['fa-file-alt', 'secondary', (string) count($upcomingExams), 'Upcoming exams'],
]);
?>
            <div class="dashboard-columns">
                <div>
                    <section class="card section-gap">
                        <?php $cardHeader('fa-calendar-day', 'My Classes', 'pages/academics/schedule.php'); ?>
                        <?php class_list($user, $myCourses); ?>
                    </section>

                    <section class="card section-gap">
                        <?php $cardHeader('fa-tasks', 'Upcoming Assignments', 'pages/academics/assignments.php'); ?>
                        <?php assignment_list($user, array_slice($assignmentsDue, 0, 4), true, 'No assignments due', 'Assignments from your courses will appear here.'); ?>
                    </section>

                    <section class="card section-gap">
                        <?php $cardHeader('fa-chart-bar', 'Recent Grades', 'pages/academics/grades.php'); ?>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead><tr><th>Course</th><th>Prelim</th><th>Midterm</th><th>Final</th><th>Final rating</th><th>Remarks</th></tr></thead>
                                <tbody>
                                    <?php if (!$recentGrades): ?>
                                    <tr><td colspan="6"><?php app_empty('fa-chart-bar', 'No grades yet', 'Grades appear here once your instructors post them.'); ?></td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($recentGrades as $course):
                                        $rating = final_rating($course);
                                        [$badge, $remark] = grade_remarks($rating);
                                    ?>
                                    <tr<?= detail_attr(grade_detail($user, $course, true)) ?>>
                                        <td><strong><?= detail_button(class_name($course)) ?></strong> <span class="muted"><?= e($course['title']) ?></span></td>
                                        <td><?= e(format_grade($course['prelim'])) ?></td>
                                        <td><?= e(format_grade($course['midterm'])) ?></td>
                                        <td><?= e(format_grade($course['final'])) ?></td>
                                        <td><strong><?= e(format_grade($rating)) ?></strong></td>
                                        <td><span class="badge <?= $badge ?>"><?= e($remark) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
                <div>
                    <section class="card section-gap">
                        <?php $cardHeader('fa-bullhorn', 'Announcements', 'pages/communication/announcements.php'); ?>
                        <?php announcement_list($user, $latestAnnouncements, true); ?>
                    </section>

                    <section class="card section-gap">
                        <?php $cardHeader('fa-calendar-alt', 'Upcoming Exams', 'pages/academics/exams.php'); ?>
                        <?php exam_list($user, array_slice($upcomingExams, 0, 3), true); ?>
                    </section>

                    <section class="card section-gap">
                        <?php app_card_header('fa-bolt', 'Quick Links'); ?>
                        <div class="card-body">
                            <div class="quick-links">
                                <?php foreach ($quickLinks as [$href, $icon, $label]):
                                    if (!in_array($href, $allowed, true)) {
                                        continue;
                                    }
                                ?>
                                <a href="<?= e($href) ?>" class="quick-link">
                                    <i class="fa-solid <?= e($icon) ?>"></i>
                                    <span><?= e($label) ?></span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>

                    <section class="card section-gap">
                        <?php $cardHeader('fa-calendar-check', 'Upcoming Events', 'pages/student-life/events.php'); ?>
                        <?php event_list($user, $upcomingEvents, true); ?>
                    </section>
                </div>
            </div>
<?php
app_page_end();
