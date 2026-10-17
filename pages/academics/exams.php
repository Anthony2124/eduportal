<?php
// Examinations. Nothing is shown until real data exists for it (no sample content).
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/academics/exams.php';
$user = require_page_access($page);

app_page_start($user, $page, 'Examinations');
app_page_header('Examinations', 'View exam schedules and results for the current semester');
app_stats([
    ['fa-calendar-day', 'warning', '0', 'Upcoming exams'],
    ['fa-circle-check', 'success', '0', 'Completed exams'],
]);
app_table_card('fa-calendar-alt', 'Exam Schedule', ['Course', 'Date', 'Time', 'Room', 'Type'], 'fa-calendar-xmark', 'No exams scheduled', 'Exam schedules appear here once they\'re released.');
app_table_card('fa-square-poll-vertical', 'Exam Results', ['Course', 'Exam', 'Score', 'Date'], 'fa-file-circle-check', 'No results yet', 'Exam results appear here once they\'re posted.');
app_page_end();
