<?php
// Grades & Transcript. Nothing is shown until real data exists for it (no sample content).
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/academics/grades.php';
$user = require_page_access($page);

app_page_start($user, $page, 'Grades & Transcript');
app_page_header('Grades & Transcript', 'View your academic performance');
app_stats([
    ['fa-chart-line', 'primary', '—', 'Current GPA'],
    ['fa-award', 'secondary', '—', 'Cumulative GPA'],
    ['fa-graduation-cap', 'success', '0', 'Units earned'],
]);
app_table_card('fa-table-list', 'Academic Record', ['Course', 'Description', 'Units', 'Midterm', 'Final', 'Grade'], 'fa-chart-bar', 'No grades yet', 'Your grades appear here once your instructors post them.');
app_page_end();
