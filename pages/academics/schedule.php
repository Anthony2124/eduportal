<?php
// Class Schedule: built from courses - a student's enrolled courses, the classes a faculty member teaches,
// or every course for the admin. To change a schedule, edit the course (Courses page).
require __DIR__ . '/../../includes/app_shell.php';
require __DIR__ . '/../../includes/academics.php';
$page = 'pages/academics/schedule.php';
$user = require_page_access($page);

[$classes, $subtitle, $emptyText] = match ($user['role']) {
    'student' => [student_courses((int) $user['id']), 'Your classes this semester', 'Your classes appear here once you are enrolled in courses.'],
    'faculty' => [content_items('courses', 'a.faculty_id = ?', [$user['id']]), 'The classes you teach', 'Courses the admin assigns to you will appear here.'],
    default => [content_items('courses'), 'Every class this semester', 'Add courses on the Courses page.'],
};

app_page_start($user, $page, 'Class Schedule');
app_page_header('Class Schedule', $subtitle);
?>
            <section class="card section-gap">
                <?php app_card_header('fa-calendar-week', 'Classes', $user['role'] === 'admin' ? 'Change a schedule by editing the course on the Courses page' : ''); ?>
                <?php course_table($user, $classes, false, 'No classes scheduled', $emptyText); ?>
            </section>
<?php
app_page_end();
