<?php
// Activity Logs (Admin only): who signed in, changed accounts, posted, changed courses, enrolled students or
// entered grades, newest first. Filter by role, kind of activity or a search; 50 lines per page.
// Lines are written by log_activity() (api/bootstrap.php).
require __DIR__ . '/../../includes/app_shell.php';
require __DIR__ . '/../../includes/content.php';
$page = 'pages/admin/logs.php';
$user = require_page_access($page);

const LOGS_PER_PAGE = 50;
const LOG_ICONS = ['auth' => 'fa-right-to-bracket', 'account' => 'fa-user-gear', 'content' => 'fa-pen-to-square', 'academics' => 'fa-graduation-cap'];
$roleBadges = ['student' => 'badge-primary', 'faculty' => 'badge-success', 'admin' => 'badge-warning'];

$role = isset(ROLE_NAMES[$_GET['role'] ?? '']) ? $_GET['role'] : '';
$category = isset(LOG_CATEGORIES[$_GET['category'] ?? '']) ? $_GET['category'] : '';
$search = trim((string) ($_GET['q'] ?? ''));

$where = [];
$params = [];
if ($role !== '') {
    $where[] = 'role = ?';
    $params[] = $role;
}
if ($category !== '') {
    $where[] = 'category = ?';
    $params[] = $category;
}
if ($search !== '') {
    $where[] = '(user_name LIKE ? OR action LIKE ? OR details LIKE ? OR ip_address LIKE ?)';
    array_push($params, ...array_fill(0, 4, '%' . addcslashes($search, '%_\\') . '%'));
}
$sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$stmt = db()->prepare("SELECT COUNT(*) FROM activity_logs{$sqlWhere}");
$stmt->execute($params);
$total = (int) $stmt->fetchColumn();
$pages = max(1, (int) ceil($total / LOGS_PER_PAGE));
$pageNo = min(max(1, (int) ($_GET['page'] ?? 1)), $pages);

$stmt = db()->prepare("SELECT * FROM activity_logs{$sqlWhere} ORDER BY id DESC LIMIT " . LOGS_PER_PAGE . ' OFFSET ' . (($pageNo - 1) * LOGS_PER_PAGE));
$stmt->execute($params);
$logs = $stmt->fetchAll();

$today = db()->query(
    "SELECT COUNT(*) AS total, COALESCE(SUM(action = 'Signed in'), 0) AS sign_ins, COALESCE(SUM(action = 'Sign-in failed'), 0) AS failed
     FROM activity_logs WHERE created_at >= CURDATE()"
)->fetch();

// Link to another page of results with the same filters
$pageLink = fn(int $to) => '?' . http_build_query(array_filter(['role' => $role, 'category' => $category, 'q' => $search, 'page' => $to > 1 ? $to : null]));
$filtered = $role !== '' || $category !== '' || $search !== '';

app_page_start($user, $page, 'Activity Logs');
app_page_header('Activity Logs', 'Who signed in and what changed in the portal, newest first');
app_stats([
    ['fa-list-check', 'primary', (string) $today['total'], 'Activities today'],
    ['fa-right-to-bracket', 'success', (string) $today['sign_ins'], 'Sign-ins today'],
    ['fa-triangle-exclamation', 'danger', (string) $today['failed'], 'Failed sign-ins today'],
]);
?>
            <section class="card section-gap">
                <div class="card-header card-header-wrap">
                    <div>
                        <div class="card-title"><i class="fa-solid fa-clipboard-list" style="color: var(--primary);"></i> Activity</div>
                        <div class="card-subtitle"><?= number_format($total) ?> <?= $total === 1 ? 'entry' : 'entries' ?><?= $filtered ? ' match your filters' : '' ?></div>
                    </div>
                    <form method="get" class="card-tools log-filters">
                        <label class="sr-only" for="log-role">Role</label>
                        <select class="form-select" id="log-role" name="role" onchange="this.form.submit()">
                            <option value="">Everyone</option>
                            <?php foreach (ROLE_NAMES as $value => $label): ?>
                            <option value="<?= e($value) ?>"<?= $value === $role ? ' selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label class="sr-only" for="log-category">Kind of activity</label>
                        <select class="form-select" id="log-category" name="category" onchange="this.form.submit()">
                            <option value="">All activity</option>
                            <?php foreach (LOG_CATEGORIES as $value => $label): ?>
                            <option value="<?= e($value) ?>"<?= $value === $category ? ' selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label class="sr-only" for="log-search">Search</label>
                        <input class="form-control" id="log-search" name="q" type="search" value="<?= e($search) ?>" placeholder="Search name, action, details or IP">
                        <button type="submit" class="btn btn-outline btn-sm"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                        <?php if ($filtered): ?>
                        <a href="logs.php" class="btn btn-outline btn-sm">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="data-table log-table">
                        <thead>
                            <tr><th>When</th><th>Who</th><th>Activity</th><th>Details</th><th class="hide-mobile">IP address</th></tr>
                        </thead>
                        <tbody>
                            <?php if (!$logs): ?>
                            <tr><td colspan="5"><?php app_empty('fa-clipboard-list', $filtered ? 'Nothing matches' : 'No activity yet',
                                $filtered ? 'Try another filter or search.' : 'Sign-ins and changes in the portal will be listed here.'); ?></td></tr>
                            <?php endif; ?>
                            <?php foreach ($logs as $log): ?>
                            <tr<?= $log['action'] === 'Sign-in failed' ? ' class="log-failed"' : '' ?>>
                                <td class="nowrap">
                                    <?= e(format_date($log['created_at'], 'M j, Y')) ?>
                                    <div class="cell-sub"><?= e(format_date($log['created_at'], 'g:i:s A')) ?></div>
                                </td>
                                <td>
                                    <?= e($log['user_name'] ?? 'Unknown') ?>
                                    <?php if ($log['role']): ?>
                                    <div><span class="badge <?= $roleBadges[$log['role']] ?? 'badge-outline' ?>"><?= e(ROLE_NAMES[$log['role']] ?? $log['role']) ?></span></div>
                                    <?php endif; ?>
                                </td>
                                <td class="nowrap">
                                    <i class="fa-solid <?= LOG_ICONS[$log['category']] ?? 'fa-circle' ?> log-icon"></i>
                                    <?= e($log['action']) ?>
                                </td>
                                <td class="muted"><?= e($log['details']) ?></td>
                                <td class="muted hide-mobile nowrap"><?= e($log['ip_address'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($pages > 1): ?>
                <nav class="log-pages" aria-label="Pages">
                    <?php if ($pageNo > 1): ?>
                    <a class="btn btn-outline btn-sm" href="<?= e($pageLink($pageNo - 1)) ?>"><i class="fa-solid fa-chevron-left"></i> Newer</a>
                    <?php endif; ?>
                    <span class="muted">Page <?= $pageNo ?> of <?= $pages ?></span>
                    <?php if ($pageNo < $pages): ?>
                    <a class="btn btn-outline btn-sm" href="<?= e($pageLink($pageNo + 1)) ?>">Older <i class="fa-solid fa-chevron-right"></i></a>
                    <?php endif; ?>
                </nav>
                <?php endif; ?>
            </section>
<?php
app_page_end();
