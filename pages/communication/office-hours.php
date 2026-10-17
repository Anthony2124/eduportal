<?php
// Virtual Office Hours. Nothing is shown until real data exists for it (no sample content).
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/communication/office-hours.php';
$user = require_page_access($page);

app_page_start($user, $page, 'Virtual Office Hours');
app_page_header('Virtual Office Hours', 'Book one-on-one consultation time with your professors');
app_empty_card('fa-calendar-check', 'My Appointments', 'fa-calendar-xmark', 'No appointments', 'Consultations you book will appear here.');
app_empty_card('fa-video', 'Available Office Hours', 'fa-user-clock', 'No office hours posted', 'Professors\' consultation schedules will appear here.');
app_page_end();
