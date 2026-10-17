<?php
/*
 * Page wrapper for the Faculty and Admin dashboards. Uses the same sidebar and top bar as every other page
 * (includes/app_shell.php).
 *
 *   staff_page_start($user, 'Faculty Dashboard', 'Search students...');
 *   ... page content ...
 *   staff_page_end();
 */
require_once __DIR__ . '/app_shell.php';

function staff_page_start(array $user, string $title, string $searchPlaceholder): void
{
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> - EduPortal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/staff.css">
    <?php app_shell_styles(); ?>
</head>
<body>
    <?php app_sidebar($user, DASHBOARDS[$user['role']]); ?>

    <div class="main-content app-main">
        <?php app_topbar($user, ['id' => 'page-search', 'placeholder' => $searchPlaceholder]); ?>

        <main class="content-area" id="top">
    <?php
}

function staff_page_end(): void
{
    ?>
        </main>
    </div>

    <script src="js/main.js?v=9"></script>
</body>
</html>
    <?php
}

/** "Good morning" / "Good afternoon" / "Good evening" for the banner. */
function time_greeting(): string
{
    $hour = (int) date('G');
    return match (true) {
        $hour >= 5 && $hour < 12 => 'Good morning',
        $hour >= 12 && $hour < 17 => 'Good afternoon',
        $hour >= 17 && $hour < 22 => 'Good evening',
        default => 'Hello',
    };
}
