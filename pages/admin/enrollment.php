<?php
// Enrollment (Admin and Faculty): the courses open this semester; enrolling students comes later.
require __DIR__ . '/../../includes/app_shell.php';
require __DIR__ . '/../../includes/content.php';
$page = 'pages/admin/enrollment.php';
$user = require_page_access($page);

$courses = content_items('courses');

app_page_start($user, $page, 'Enrollment');
app_page_header('Enrollment', 'Course registration and academic advising');
?>
            <section class="card section-gap">
                <?php app_card_header('fa-book-open', 'Courses Open for Enrollment', can_manage_content($user, 'courses') ? 'Add, edit or remove courses on the Courses page' : ''); ?>
                <?php course_table($user, $courses, false, 'No courses open for enrollment', 'Courses offered this semester will be listed here.'); ?>
            </section>
<?php
app_table_card('fa-list-check', 'Enrolled Students', ['Student', 'Student ID', 'Course', 'Date'], 'fa-user-graduate', 'No enrollments yet',
    'Student enrollments will appear here once enrollment opens.');
app_page_end();
