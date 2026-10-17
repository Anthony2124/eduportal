<?php
/*
 * The shared app frame: ONE sidebar and ONE top bar for every signed-in page (the three dashboards and
 * every page in pages/), so the navigation looks and behaves the same everywhere. Styles: css/app-shell.css.
 * Menus only list what the user's role can open (ROLE_PAGES in api/bootstrap.php).
 *
 * A portal page uses it like this:
 *   <?php
 *   require __DIR__ . '/../../includes/app_shell.php';
 *   $page = 'pages/academics/courses.php';
 *   $user = require_page_access($page);
 *   ?>
 *   ... <body> <?php app_sidebar($user, $page); ?>
 *   <div class="main-content app-main"> <?php app_topbar($user, ['placeholder' => 'Search...']); ?> ...
 */
require_once __DIR__ . '/../api/bootstrap.php';

// Extra sidebar links (parts of their dashboard) for faculty and admins: [href, icon, label]
const ROLE_DASHBOARD_LINKS = [
    'admin' => [['admin.php#add-faculty', 'fa-user-plus', 'Add Faculty'], ['admin.php#accounts', 'fa-users-gear', 'Accounts']],
    'faculty' => [['faculty.php#classes', 'fa-chalkboard-user', 'My Classes'], ['faculty.php#roster', 'fa-users', 'Student Roster']],
    'student' => [],
];
const PORTAL_NAMES = ['admin' => 'Admin Console', 'faculty' => 'Faculty Portal', 'student' => 'Student Portal'];

/** Relative path from the current page back to the site root: '' for root pages, '../../' for pages/x/. */
function site_root(): string
{
    $appRoot = str_replace('\\', '/', realpath(dirname(__DIR__)));
    $scriptDir = str_replace('\\', '/', dirname(realpath($_SERVER['SCRIPT_FILENAME'])));
    $relative = trim(substr($scriptDir, strlen($appRoot)), '/');
    return $relative === '' ? '' : str_repeat('../', substr_count($relative, '/') + 1);
}

/** For portal pages: the signed-in user. Signed-out visitors go to sign in; roles without access go to their dashboard. */
function require_page_access(string $page): array
{
    $user = current_user();
    if (!$user) {
        header('Location: ' . site_root() . 'login.html');
        exit;
    }
    if (!in_array($page, allowed_pages($user['role']), true)) {
        header('Location: ' . site_root() . DASHBOARDS[$user['role']]);
        exit;
    }
    return $user;
}

/** Stylesheet for the frame; put it in <head> after the page's own styles. */
function app_shell_styles(): void
{
    echo '<link rel="stylesheet" href="' . e(site_root()) . 'css/app-shell.css?v=2">' . "\n";
}

function app_nav_link(string $href, string $icon, string $label, bool $active): void
{
    ?>
                <a href="<?= e($href) ?>" class="app-nav-link<?= $active ? ' active' : '' ?>"<?= $active ? ' aria-current="page"' : '' ?>>
                    <i class="fa-solid <?= e($icon) ?>"></i>
                    <span><?= e($label) ?></span>
                </a>
    <?php
}

/** The sidebar. $current is the page's path (e.g. 'pages/academics/courses.php' or 'index.php'). */
function app_sidebar(array $user, string $current): void
{
    $root = site_root();
    $role = $user['role'];
    $dashboard = DASHBOARDS[$role];
    $allowed = allowed_pages($role);
    ?>
    <div class="app-sidebar-overlay" id="sidebar-overlay"></div>
    <aside class="app-sidebar" id="sidebar" aria-label="Main navigation">
        <div class="app-brand-row">
            <a href="<?= e($root . $dashboard) ?>" class="app-brand">
                <span class="app-brand-icon"><i class="fa-solid fa-graduation-cap"></i></span>
                <span class="app-brand-text">EduPortal<small><?= e(PORTAL_NAMES[$role]) ?></small></span>
            </a>
            <button type="button" class="app-sidebar-close" id="sidebar-close" aria-label="Close menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <nav class="app-nav">
            <div class="app-nav-section">
                <?php app_nav_link($root . $dashboard, 'fa-house', 'Dashboard', $current === $dashboard); ?>
                <?php foreach (ROLE_DASHBOARD_LINKS[$role] as [$href, $icon, $label]) {
                    app_nav_link($root . $href, $icon, $label, false);
                } ?>
            </div>
            <?php foreach (PAGE_SECTIONS as $section => $pages):
                $pages = array_intersect_key($pages, array_flip($allowed));
                if (!$pages) {
                    continue;
                }
            ?>
            <div class="app-nav-section">
                <div class="app-nav-title"><?= e($section) ?></div>
                <?php foreach ($pages as $path => [$icon, $label]) {
                    app_nav_link($root . $path, $icon, $label, $current === $path);
                } ?>
            </div>
            <?php endforeach; ?>
        </nav>

        <div class="app-sidebar-footer">
            <?php app_nav_link($root . 'logout.php', 'fa-right-from-bracket', 'Logout', false); ?>
        </div>
    </aside>
    <?php
}

/**
 * The top bar. $search: ['id' => ..., 'placeholder' => ...] - keep the id a page's own script uses
 * (e.g. announcement-search) so that page's search keeps working.
 */
function app_topbar(array $user, array $search = []): void
{
    $root = site_root();
    $allowed = allowed_pages($user['role']);
    $fullName = $user['first_name'] . ' ' . $user['last_name'];
    $announcements = 'pages/communication/announcements.php';
    $settings = 'pages/settings/profile.php';
    ?>
        <header class="app-topbar">
            <div class="app-topbar-left">
                <button type="button" class="app-icon-btn app-menu-btn" id="sidebar-toggle" aria-label="Open menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <label class="app-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search"<?= !empty($search['id']) ? ' id="' . e($search['id']) . '"' : '' ?>
                           placeholder="<?= e($search['placeholder'] ?? 'Search...') ?>" aria-label="Search">
                </label>
            </div>

            <div class="app-topbar-right">
                <span class="app-term-badge"><i class="fa-solid fa-calendar-days"></i> AY 2026-2027 • 1st Sem</span>
                <?php if (in_array($announcements, $allowed, true)): ?>
                <a href="<?= e($root . $announcements) ?>" class="app-icon-btn" title="Announcements" aria-label="Announcements">
                    <i class="fa-regular fa-bell"></i>
                </a>
                <?php endif; ?>

                <div class="dropdown-container app-user-menu">
                    <button type="button" class="dropdown-trigger app-user-btn" aria-label="Account menu">
                        <span class="app-avatar">
                            <?php if ($user['picture_url']): ?>
                            <img src="<?= e($user['picture_url']) ?>" alt="" referrerpolicy="no-referrer">
                            <?php else: ?>
                            <?= e(initials($user)) ?>
                            <?php endif; ?>
                        </span>
                        <span class="app-user-text">
                            <span class="app-user-name"><?= e($fullName) ?></span>
                            <span class="app-user-role"><?= e(user_subtitle($user)) ?></span>
                        </span>
                        <i class="fa-solid fa-chevron-down app-chevron"></i>
                    </button>
                    <div class="app-menu-panel">
                        <div class="app-menu-header">
                            <div class="app-user-name"><?= e($fullName) ?></div>
                            <div class="app-user-role">ID: <?= e($user['id_number']) ?> • <?= e($user['email'] ?? 'No Gmail linked') ?></div>
                        </div>
                        <?php if (in_array($settings, $allowed, true)): ?>
                        <a href="<?= e($root . $settings) ?>" class="app-menu-item"><i class="fa-solid fa-gear"></i> Settings</a>
                        <?php endif; ?>
                        <a href="<?= e($root . 'logout.php') ?>" class="app-menu-item app-menu-danger"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
                    </div>
                </div>
            </div>
        </header>
    <?php
}
