<?php
// Discussion Forums. Nothing is shown until real data exists for it (no sample content).
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/communication/forums.php';
$user = require_page_access($page);

app_page_start($user, $page, 'Discussion Forums');
app_page_header('Discussion Forums', 'Discuss academic topics with your classmates');
app_empty_card('fa-comments', 'Discussions', 'fa-comments', 'No discussions yet', 'Forum topics will appear here.');
app_page_end();
