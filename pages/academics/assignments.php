<?php
// Assignments. Nothing is shown until real data exists for it (no sample content).
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/academics/assignments.php';
$user = require_page_access($page);

app_page_start($user, $page, 'Assignments');
app_page_header('Assignments', 'Track and submit your coursework');
app_stats([
    ['fa-hourglass-half', 'warning', '0', 'Pending'],
    ['fa-paper-plane', 'primary', '0', 'Submitted'],
    ['fa-check-double', 'success', '0', 'Graded'],
    ['fa-clock', 'danger', '0', 'Late'],
]);
app_table_card('fa-tasks', 'Assignments', ['Assignment', 'Course', 'Due date', 'Status'], 'fa-clipboard-list', 'No assignments yet', 'Assignments from your courses will appear here.');
app_page_end();
