<?php
// My Courses. Nothing is shown until real data exists for it (no sample content).
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/academics/courses.php';
$user = require_page_access($page);

app_page_start($user, $page, 'My Courses');
app_page_header('My Courses', 'Access your course materials and resources');
app_stats([
    ['fa-book', 'primary', '0', 'Enrolled courses'],
    ['fa-layer-group', 'secondary', '0', 'Total units'],
]);
app_empty_card('fa-book', 'Courses', 'fa-book-open', 'No courses yet', 'Your enrolled courses and their materials will appear here.');
app_page_end();
