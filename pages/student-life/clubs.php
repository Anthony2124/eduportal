<?php
// Clubs & Organizations. Nothing is shown until real data exists for it (no sample content).
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/student-life/clubs.php';
$user = require_page_access($page);

app_page_start($user, $page, 'Clubs & Organizations');
app_page_header('Clubs & Organizations', 'Find your community and pursue your passions');
app_empty_card('fa-users', 'My Clubs', 'fa-user-group', 'You haven\'t joined any clubs', 'Clubs you join will appear here.');
app_empty_card('fa-people-group', 'All Clubs', 'fa-people-group', 'No clubs listed yet', 'Student organizations will be listed here.');
app_page_end();
