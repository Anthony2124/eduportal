<?php
// Class Schedule. Nothing is shown until real data exists for it (no sample content).
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/academics/schedule.php';
$user = require_page_access($page);

app_page_start($user, $page, 'Class Schedule');
app_page_header('Class Schedule', 'Your weekly timetable');
app_empty_card('fa-calendar-week', 'This Week', 'fa-calendar-xmark', 'No classes scheduled', 'Your weekly timetable appears here once you\'re enrolled in courses.');
app_page_end();
