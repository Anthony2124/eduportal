<?php
// Learning Analytics. Nothing is shown until real data exists for it (no sample content).
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/smart/analytics.php';
$user = require_page_access($page);

app_page_start($user, $page, 'Learning Analytics');
app_page_header('Learning Analytics', 'Insights into academic performance');
app_empty_card('fa-chart-line', 'Performance', 'fa-chart-pie', 'No data yet', 'Analytics appear here once grades are recorded.');
app_page_end();
