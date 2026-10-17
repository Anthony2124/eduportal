<?php
// Scholarships: the Admin adds, edits and removes them; students see the list.
require __DIR__ . '/../../includes/app_shell.php';
require __DIR__ . '/../../includes/content.php';
$page = 'pages/admin/scholarships.php';
$user = require_page_access($page);

$scholarships = content_items('scholarships');

app_page_start($user, $page, 'Scholarships & Financial Aid');
app_page_header('Scholarships & Financial Aid', 'Explore grants and scholarships you can apply for');

if ($user['role'] === 'student') {
    app_empty_card('fa-award', 'My Scholarships', 'fa-award', 'No active scholarships', 'Scholarships you receive will appear here.');
}
?>
            <section class="card section-gap">
                <?php content_card_header($user, 'scholarships', 'fa-hand-holding-dollar', 'Available Scholarships'); ?>
                <?php scholarship_list($user, $scholarships); ?>
            </section>
<?php
content_form($user, 'scholarships');
app_page_end();
