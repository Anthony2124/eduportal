<?php
// Student dashboard. Signed-out visitors go to sign in; faculty and admins go to their own dashboards.
require __DIR__ . '/api/bootstrap.php';
require __DIR__ . '/includes/app_shell.php';

$user = require_page_role('student');

// Shortcuts to the student's own pages (only ones this role can open)
$quickLinks = [
    ['pages/academics/courses.php', 'fa-book', 'Courses'],
    ['pages/academics/grades.php', 'fa-chart-bar', 'Grades'],
    ['pages/academics/schedule.php', 'fa-calendar-alt', 'Schedule'],
    ['pages/communication/messaging.php', 'fa-envelope', 'Messages'],
    ['pages/admin/scholarships.php', 'fa-graduation-cap', 'Scholarships'],
    ['pages/communication/office-hours.php', 'fa-video', 'Office Hours'],
];
$allowed = allowed_pages('student');

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
    ['fa-book', 'primary', '0', 'Courses enrolled'],
    ['fa-tasks', 'warning', '0', 'Assignments due'],
    ['fa-chart-line', 'success', '—', 'Current GPA'],
    ['fa-file-alt', 'secondary', '0', 'Upcoming exams'],
]);
?>
            <div class="dashboard-columns">
                <div>
<?php
app_empty_card('fa-calendar-day', "Today's Schedule", 'fa-calendar-xmark', 'No classes today',
    'Your classes will appear here once you are enrolled.');
app_table_card('fa-chart-bar', 'Recent Grades', ['Course', 'Assessment', 'Grade', 'Date'], 'fa-chart-bar', 'No grades yet',
    'Grades appear here once your instructors post them.');
?>
                </div>
                <div>
<?php
app_empty_card('fa-bullhorn', 'Announcements', 'fa-bullhorn', 'No announcements yet',
    'News from your school will appear here.');
?>
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
<?php
app_empty_card('fa-calendar-check', 'Upcoming Events', 'fa-calendar', 'No upcoming events',
    'School events will appear here.');
?>
                </div>
            </div>
<?php
app_page_end();
