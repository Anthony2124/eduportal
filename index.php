<?php
// Student dashboard. Signed-out visitors go to sign in; faculty and admins go to their own dashboards.
require __DIR__ . '/api/bootstrap.php';
require __DIR__ . '/includes/app_shell.php';
require __DIR__ . '/includes/content.php';

$user = require_page_role('student');

$latestAnnouncements = content_items('announcements', '', [], 3);
$upcomingEvents = content_items('events', 'a.event_date >= CURDATE()', [], 3);

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
                    <section class="card section-gap">
                        <div class="card-header">
                            <div class="card-title"><i class="fa-solid fa-bullhorn" style="color: var(--primary);"></i> Announcements</div>
                            <a href="pages/communication/announcements.php" class="card-link">View all</a>
                        </div>
                        <?php announcement_list($user, $latestAnnouncements, true); ?>
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
                        <div class="card-header">
                            <div class="card-title"><i class="fa-solid fa-calendar-check" style="color: var(--primary);"></i> Upcoming Events</div>
                            <a href="pages/student-life/events.php" class="card-link">View all</a>
                        </div>
                        <?php event_list($user, $upcomingEvents, true); ?>
                    </section>
                </div>
            </div>
<?php
app_page_end();
