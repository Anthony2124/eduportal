<?php
// Messages. Nothing is shown until real data exists for it (no sample content).
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/communication/messaging.php';
$user = require_page_access($page);

app_page_start($user, $page, 'Messages');
app_page_header('Messages', 'Stay connected with your classmates and professors');
app_empty_card('fa-envelope', 'Conversations', 'fa-comments', 'No messages yet', 'Your conversations will appear here.');
app_page_end();
