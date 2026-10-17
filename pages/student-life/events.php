<?php
// Events: the Admin adds, edits and removes them; everyone sees them.
require __DIR__ . '/../../includes/app_shell.php';
require __DIR__ . '/../../includes/content.php';
$page = 'pages/student-life/events.php';
$user = require_page_access($page);

$upcoming = content_items('events', 'a.event_date >= CURDATE()');
$past = array_slice(array_reverse(content_items('events', 'a.event_date < CURDATE()')), 0, 20); // newest first

app_page_start($user, $page, 'Events Calendar');
app_page_header('Events Calendar', "Discover what's happening on campus");
?>
            <section class="card section-gap">
                <?php content_card_header($user, 'events', 'fa-calendar', 'Upcoming Events'); ?>
                <?php event_list($user, $upcoming); ?>
            </section>

            <?php if ($past): ?>
            <section class="card section-gap">
                <?php app_card_header('fa-clock-rotate-left', 'Past Events'); ?>
                <?php event_list($user, $past); ?>
            </section>
            <?php endif; ?>
<?php
content_form($user, 'events');
app_page_end();
