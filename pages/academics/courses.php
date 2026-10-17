<?php
// Courses: the Admin adds, edits and removes courses and assigns faculty; everyone sees the list.
// Students also see the courses they're enrolled in.
require __DIR__ . '/../../includes/app_shell.php';
require __DIR__ . '/../../includes/academics.php';
$page = 'pages/academics/courses.php';
$user = require_page_access($page);

$courses = content_items('courses');

app_page_start($user, $page, 'Courses');

if ($user['role'] === 'student') {
    $myCourses = student_courses((int) $user['id']);
    app_page_header('Courses', 'Your enrolled courses and everything offered this semester');
    app_stats([
        ['fa-book', 'primary', (string) count($myCourses), 'Enrolled courses'],
        ['fa-layer-group', 'secondary', (string) array_sum(array_column($myCourses, 'units')), 'Units enrolled'],
    ]);
?>
            <section class="card section-gap">
                <?php app_card_header('fa-book-open-reader', 'My Courses'); ?>
                <?php course_table($user, $myCourses, false, 'No courses yet', 'Courses you are enrolled in will appear here.'); ?>
            </section>
<?php
} else {
    app_page_header('Courses', 'Courses offered this semester');
    app_stats([
        ['fa-book', 'primary', (string) count($courses), 'Courses offered'],
        ['fa-layer-group', 'secondary', (string) array_sum(array_column($courses, 'units')), 'Total units'],
    ]);
}
?>
            <section class="card section-gap">
                <?php content_card_header($user, 'courses', 'fa-book', $user['role'] === 'student' ? 'All Courses Offered' : 'Course List'); ?>
                <?php course_table($user, $courses, true, 'No courses yet', 'Courses offered this semester will appear here.'); ?>
            </section>
<?php
content_form($user, 'courses');
app_page_end();
