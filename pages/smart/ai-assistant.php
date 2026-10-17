<?php
// AI Study Assistant. Nothing is shown until real data exists for it (no sample content).
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/smart/ai-assistant.php';
$user = require_page_access($page);

app_page_start($user, $page, 'AI Study Assistant');
app_page_header('AI Study Assistant', 'An AI-powered learning companion');
app_empty_card('fa-robot', 'Assistant', 'fa-robot', 'The AI Assistant isn\'t available yet', 'This feature hasn\'t been set up.');
app_page_end();
