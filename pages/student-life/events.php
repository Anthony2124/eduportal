<?php
// Events Calendar. Nothing is shown until real data exists for it (no sample content).
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/student-life/events.php';
$user = require_page_access($page);

app_page_start($user, $page, 'Events Calendar');
app_page_header('Events Calendar', 'Discover what\'s happening on campus');
app_empty_card('fa-calendar', 'Upcoming Events', 'fa-calendar-xmark', 'No upcoming events', 'School events will appear here.');
app_page_end();
