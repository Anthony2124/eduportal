<?php
// Scholarships & Financial Aid. Nothing is shown until real data exists for it (no sample content).
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/admin/scholarships.php';
$user = require_page_access($page);

app_page_start($user, $page, 'Scholarships & Financial Aid');
app_page_header('Scholarships & Financial Aid', 'Explore grants and manage your scholarship applications');
app_empty_card('fa-award', 'My Scholarships', 'fa-award', 'No active scholarships', 'Scholarships you receive will appear here.');
app_empty_card('fa-hand-holding-dollar', 'Available Scholarships', 'fa-graduation-cap', 'No scholarships posted', 'Open scholarships and grants will be listed here.');
app_page_end();
