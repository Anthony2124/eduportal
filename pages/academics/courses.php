<?php
// Courses: the Admin adds, edits and removes courses and assigns faculty; everyone sees the list.
require __DIR__ . '/../../includes/app_shell.php';
require __DIR__ . '/../../includes/content.php';
$page = 'pages/academics/courses.php';
$user = require_page_access($page);

$courses = content_items('courses');

app_page_start($user, $page, 'Courses');
app_page_header('Courses', 'Courses offered this semester');
app_stats([
    ['fa-book', 'primary', (string) count($courses), 'Courses offered'],
    ['fa-layer-group', 'secondary', (string) array_sum(array_column($courses, 'units')), 'Total units'],
]);
?>
            <section class="card section-gap">
                <?php content_card_header($user, 'courses', 'fa-book', 'Course List'); ?>
                <?php course_table($user, $courses, true, 'No courses yet', 'Courses offered this semester will appear here.'); ?>
            </section>
<?php
content_form($user, 'courses');
app_page_end();
