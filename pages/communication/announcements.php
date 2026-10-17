<?php
// Announcements. Nothing is shown until real data exists for it (no sample content).
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/communication/announcements.php';
$user = require_page_access($page);

app_page_start($user, $page, 'Announcements');
app_page_header('Announcements', 'Stay updated with the latest news and school notices');
app_empty_card('fa-bullhorn', 'All Announcements', 'fa-bullhorn', 'No announcements yet', 'News and notices from your school will appear here.');
app_page_end();
