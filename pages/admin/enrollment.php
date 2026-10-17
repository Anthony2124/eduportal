<?php
// Enrollment. Nothing is shown until real data exists for it (no sample content).
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/admin/enrollment.php';
$user = require_page_access($page);

app_page_start($user, $page, 'Enrollment');
app_page_header('Enrollment', 'Course registration and academic advising');
app_table_card('fa-list-check', 'Enrolled Courses', ['Code', 'Course', 'Units', 'Schedule', 'Instructor'], 'fa-book', 'No enrolled courses', 'Courses appear here once enrollment opens.');
app_table_card('fa-book-open', 'Available Courses', ['Code', 'Course', 'Units', 'Slots'], 'fa-folder-open', 'No courses open for enrollment', 'Courses offered this semester will be listed here.');
app_page_end();
