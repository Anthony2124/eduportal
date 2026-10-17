<?php
// Announcements: Admin and Faculty post them (Faculty can change only their own); everyone reads them.
require __DIR__ . '/../../includes/app_shell.php';
require __DIR__ . '/../../includes/content.php';
$page = 'pages/communication/announcements.php';
$user = require_page_access($page);

$announcements = content_items('announcements');

app_page_start($user, $page, 'Announcements');
app_page_header('Announcements', 'Stay updated with the latest news and school notices');
?>
            <section class="card section-gap">
                <?php content_card_header($user, 'announcements', 'fa-bullhorn', 'All Announcements'); ?>
                <?php announcement_list($user, $announcements); ?>
            </section>
<?php
content_form($user, 'announcements');
app_page_end();
