<?php
// Admin dashboard. Signed-out visitors go to sign in; students and faculty go to their own dashboards.
require __DIR__ . '/api/bootstrap.php';
require __DIR__ . '/includes/app_shell.php';
require __DIR__ . '/includes/content.php';

$user = require_page_role('admin');

$accounts = db()->query('SELECT * FROM users ORDER BY created_at DESC, id DESC')->fetchAll();
$roleCounts = ['student' => 0, 'faculty' => 0, 'admin' => 0];
foreach ($accounts as $account) {
    $roleCounts[$account['role']]++;
}
$newThisWeek = (int) db()->query('SELECT COUNT(*) FROM users WHERE created_at >= NOW() - INTERVAL 7 DAY')->fetchColumn();
$studentsByProgram = db()->query(
    "SELECT program, COUNT(*) AS total FROM users WHERE role = 'student' GROUP BY program ORDER BY total DESC, program"
)->fetchAll();

// Content the admin manages, with how many of each exist
$contentCount = fn(string $type) => (int) db()->query('SELECT COUNT(*) FROM ' . CONTENT_TYPES[$type]['table'])->fetchColumn();
$contentLinks = [
    ['pages/academics/courses.php', 'fa-book', 'Courses', $contentCount('courses')],
    ['pages/communication/announcements.php', 'fa-bullhorn', 'Announcements', $contentCount('announcements')],
    ['pages/student-life/events.php', 'fa-calendar', 'Events', $contentCount('events')],
    ['pages/admin/scholarships.php', 'fa-graduation-cap', 'Scholarships', $contentCount('scholarships')],
];

// The latest lines of the activity log (Activity Logs page has them all)
$recentActivity = db()->query('SELECT * FROM activity_logs ORDER BY id DESC LIMIT 6')->fetchAll();

$roleBadges = ['student' => 'badge-primary', 'faculty' => 'badge-success', 'admin' => 'badge-warning'];
$roleIcons = ['student' => 'fa-user-graduate', 'faculty' => 'fa-chalkboard-user', 'admin' => 'fa-user-shield'];

app_page_start($user, 'admin.php', 'Admin Dashboard', ['id' => 'page-search', 'placeholder' => 'Search accounts by name, Gmail or ID...']);
?>
            <section class="staff-banner">
                <div>
                    <h2><?= e(time_greeting()) ?>, <?= e($user['first_name']) ?>!</h2>
                    <p><?= e($user['department']) ?> • Admin Console</p>
                </div>
                <div class="banner-chips">
                    <span class="banner-chip"><i class="fa-solid fa-user-plus"></i> <?= $newThisWeek ?> new account<?= $newThisWeek === 1 ? '' : 's' ?> this week</span>
                    <span class="banner-chip"><i class="fa-solid fa-database"></i> <?= count($accounts) ?> accounts in total</span>
                </div>
            </section>

            <section class="grid-4 section-gap">
                <div class="stat-tile">
                    <div class="stat-tile-icon tone-secondary"><i class="fa-solid fa-users"></i></div>
                    <div>
                        <div class="stat-tile-value"><?= count($accounts) ?></div>
                        <div class="stat-tile-label">Total accounts</div>
                    </div>
                </div>
                <div class="stat-tile">
                    <div class="stat-tile-icon tone-primary"><i class="fa-solid fa-user-graduate"></i></div>
                    <div>
                        <div class="stat-tile-value"><?= $roleCounts['student'] ?></div>
                        <div class="stat-tile-label">Students</div>
                    </div>
                </div>
                <div class="stat-tile">
                    <div class="stat-tile-icon tone-success"><i class="fa-solid fa-chalkboard-user"></i></div>
                    <div>
                        <div class="stat-tile-value"><?= $roleCounts['faculty'] ?></div>
                        <div class="stat-tile-label">Faculty</div>
                    </div>
                </div>
                <div class="stat-tile">
                    <div class="stat-tile-icon tone-warning"><i class="fa-solid fa-user-shield"></i></div>
                    <div>
                        <div class="stat-tile-value"><?= $roleCounts['admin'] ?></div>
                        <div class="stat-tile-label">Admins</div>
                    </div>
                </div>
            </section>

            <section class="card section-gap" id="content">
                <?php app_card_header('fa-pen-to-square', 'Portal Content', 'Add, edit or remove what everyone sees'); ?>
                <div class="card-body">
                    <div class="manage-links">
                        <?php foreach ($contentLinks as [$href, $icon, $label, $count]): ?>
                        <a href="<?= e($href) ?>" class="manage-link">
                            <i class="fa-solid <?= e($icon) ?>"></i>
                            <span><strong><?= (int) $count ?></strong> <?= e($label) ?></span>
                            <i class="fa-solid fa-arrow-right manage-arrow"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <div class="grid-2 section-gap">
                <section class="card" id="add-faculty">
                    <div class="card-header">
                        <div>
                            <div class="card-title"><i class="fa-solid fa-user-plus" style="color: var(--success);"></i> Add Faculty</div>
                            <div class="card-subtitle">Faculty can't sign up themselves - create their account here</div>
                        </div>
                    </div>
                    <form class="card-body" id="faculty-form" novalidate>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="form-label" for="fac-first">First Name</label>
                                <input class="form-control" id="fac-first" name="firstName" placeholder="Maria" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="fac-last">Last Name</label>
                                <input class="form-control" id="fac-last" name="lastName" placeholder="Santos" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="form-label" for="fac-id">Employee ID</label>
                                <input class="form-control" id="fac-id" name="idNumber" placeholder="FAC-2026-014" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="fac-dept">Department</label>
                                <select class="form-select" id="fac-dept" name="department">
                                    <option value="">Select department</option>
                                    <?php foreach (DEPARTMENTS as $department): ?>
                                    <option><?= e($department) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="form-label" for="fac-email">Gmail</label>
                                <input class="form-control" type="email" id="fac-email" name="email" placeholder="maria.santos@gmail.com" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="fac-password">Initial Password</label>
                                <input class="form-control" id="fac-password" name="password" value="faculty123" autocomplete="off">
                            </div>
                        </div>
                        <p class="person-sub" style="margin-bottom: 14px;">
                            Give the faculty member their Employee ID and password. They sign in on the Faculty tab,
                            then enter the code sent to this Gmail.
                        </p>
                        <div class="form-error" id="faculty-error" hidden></div>
                        <button type="submit" class="btn btn-primary" id="faculty-submit">
                            <i class="fa-solid fa-user-plus"></i> Create Faculty Account
                        </button>
                    </form>
                </section>

                <section class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fa-solid fa-chart-simple" style="color: var(--primary);"></i> Students by Program</div>
                        <a href="pages/smart/analytics.php" class="card-link">Analytics</a>
                    </div>
                    <div class="card-body">
                        <?php if ($studentsByProgram): ?>
                        <ul class="quick-list">
                            <?php foreach ($studentsByProgram as $row): ?>
                            <li style="justify-content: space-between; align-items: center;">
                                <span><?= e($row['program']) ?></span>
                                <span class="badge badge-primary"><?= (int) $row['total'] ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                        <div class="empty-state"><i class="fa-solid fa-user-graduate"></i>No students yet.</div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <section class="card section-gap">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-clipboard-list" style="color: var(--primary);"></i> Recent Activity</div>
                    <a href="pages/admin/logs.php" class="card-link">View all</a>
                </div>
                <?php if (!$recentActivity): ?>
                <?php app_empty('fa-clipboard-list', 'No activity yet', 'Sign-ins and changes in the portal will be listed here.'); ?>
                <?php else: ?>
                <ul class="activity-feed">
                    <?php foreach ($recentActivity as $log): ?>
                    <li<?= $log['action'] === 'Sign-in failed' ? ' class="log-failed"' : '' ?>>
                        <span class="activity-time"><?= e(format_date($log['created_at'], 'M j, g:i A')) ?></span>
                        <span><strong><?= e($log['user_name'] ?? 'Unknown') ?></strong> <?= e(lcfirst($log['action'])) ?><?= $log['details'] !== '' ? ': ' . e($log['details']) : '' ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </section>

            <section class="card section-gap" id="accounts">
                <div class="card-header">
                    <div>
                        <div class="card-title"><i class="fa-solid fa-users-gear" style="color: var(--secondary);"></i> Accounts</div>
                        <div class="card-subtitle">Everyone with an EduPortal account, newest first</div>
                    </div>
                </div>
                <div class="table-toolbar">
                    <div class="filter-tabs" id="role-filters">
                        <button class="filter-tab active" data-role-filter="all">All <span class="filter-count"><?= count($accounts) ?></span></button>
                        <button class="filter-tab" data-role-filter="student"><i class="fa-solid fa-user-graduate"></i> Students <span class="filter-count"><?= $roleCounts['student'] ?></span></button>
                        <button class="filter-tab" data-role-filter="faculty"><i class="fa-solid fa-chalkboard-user"></i> Faculty <span class="filter-count"><?= $roleCounts['faculty'] ?></span></button>
                        <button class="filter-tab" data-role-filter="admin"><i class="fa-solid fa-user-shield"></i> Admins <span class="filter-count"><?= $roleCounts['admin'] ?></span></button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="data-table" id="accounts-table">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th>Role</th>
                                <th>ID Number</th>
                                <th>Program / Department</th>
                                <th class="hide-mobile">Activity</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($accounts as $account):
                                $name = $account['first_name'] . ' ' . $account['last_name'];
                                $isMe = (int) $account['id'] === (int) $user['id'];
                            ?>
                            <tr data-role="<?= e($account['role']) ?>">
                                <td>
                                    <div class="person-cell">
                                        <div class="person-avatar">
                                            <?php if ($account['picture_url']): ?>
                                            <img src="<?= e($account['picture_url']) ?>" alt="" referrerpolicy="no-referrer">
                                            <?php else: ?>
                                            <?= e(initials($account)) ?>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="person-name"><?= e($name) ?></div>
                                            <div class="person-sub"><?= e($account['email'] ?? 'No Gmail linked') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="nowrap">
                                    <span class="badge <?= $roleBadges[$account['role']] ?>">
                                        <i class="fa-solid <?= $roleIcons[$account['role']] ?>"></i> <?= e(ROLE_NAMES[$account['role']]) ?>
                                    </span>
                                </td>
                                <td class="nowrap"><?= e($account['id_number']) ?></td>
                                <td class="muted"><?= e(user_subtitle($account)) ?></td>
                                <td class="hide-mobile nowrap">
                                    <div class="person-sub">Joined <?= e(date('M j, Y', strtotime($account['created_at']))) ?></div>
                                    <div class="person-sub">Last in: <?= $account['last_login_at'] ? e(date('M j, g:i A', strtotime($account['last_login_at']))) : 'never' ?></div>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($isMe): ?>
                                    <span class="badge badge-outline">You</span>
                                    <?php else: ?>
                                    <button class="btn btn-outline btn-sm delete-account" data-id="<?= (int) $account['id'] ?>" data-name="<?= e($name) ?>" aria-label="Delete <?= e($name) ?>">
                                        <i class="fa-regular fa-trash-can" style="color: var(--danger);"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr id="no-match" hidden>
                                <td colspan="6" class="empty-state">No accounts match.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Delete confirmation -->
            <div class="modal-overlay" id="delete-modal">
                <div class="modal" style="max-width: 440px;">
                    <div class="modal-header">
                        <h3 class="modal-title">Delete account?</h3>
                        <button class="modal-close-btn" data-modal-close aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <strong id="delete-name"></strong>'s account will be removed and they will be signed out
                        of EduPortal. This can't be undone.
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline" data-modal-close>Cancel</button>
                        <button class="btn btn-danger" id="confirm-delete"><i class="fa-regular fa-trash-can"></i> Delete</button>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const rows = [...document.querySelectorAll('#accounts-table tbody tr[data-role]')];
                    const search = document.getElementById('page-search');
                    const noMatch = document.getElementById('no-match');
                    let roleFilter = 'all';

                    // Role tabs + top search box filter the accounts table together
                    function applyFilters() {
                        const query = search.value.trim().toLowerCase();
                        let shown = 0;
                        rows.forEach(row => {
                            const match = (roleFilter === 'all' || row.dataset.role === roleFilter)
                                && row.textContent.toLowerCase().includes(query);
                            row.hidden = !match;
                            if (match) shown++;
                        });
                        noMatch.hidden = shown > 0;
                    }

                    document.querySelectorAll('[data-role-filter]').forEach(tab => {
                        tab.addEventListener('click', () => {
                            document.querySelectorAll('[data-role-filter]').forEach(t => t.classList.toggle('active', t === tab));
                            roleFilter = tab.dataset.roleFilter;
                            applyFilters();
                        });
                    });
                    search.addEventListener('input', () => {
                        applyFilters();
                        if (search.value.trim()) document.getElementById('accounts').scrollIntoView();
                    });

                    // POST to api/admin.php; throws with the server's message (and field) on failure
                    async function adminApi(action, payload) {
                        const response = await fetch(`api/admin.php?action=${action}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(payload)
                        });
                        const data = await response.json().catch(() => ({}));
                        if (!response.ok) {
                            throw Object.assign(new Error(data.error || `Server error (${response.status}).`), { field: data.field });
                        }
                        return data;
                    }

                    // Create a faculty account
                    const facultyForm = document.getElementById('faculty-form');
                    const facultyError = document.getElementById('faculty-error');
                    const facultySubmit = document.getElementById('faculty-submit');

                    facultyForm.addEventListener('input', (e) => e.target.classList.remove('is-invalid'));

                    facultyForm.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        facultyForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                        facultyError.hidden = true;
                        facultySubmit.disabled = true;
                        try {
                            const data = await adminApi('create_faculty', Object.fromEntries(new FormData(facultyForm)));
                            showToast(`Faculty account created for ${data.name}.`, 'success');
                            setTimeout(() => window.location.reload(), 900); // show them in the Accounts table
                        } catch (err) {
                            facultyError.textContent = err.message;
                            facultyError.hidden = false;
                            const field = err.field && facultyForm.elements[err.field];
                            if (field) {
                                field.classList.add('is-invalid');
                                field.focus();
                            }
                            facultySubmit.disabled = false;
                        }
                    });

                    // Delete an account (after confirming)
                    const modal = document.getElementById('delete-modal');
                    const confirmBtn = document.getElementById('confirm-delete');
                    let pendingDelete = null;

                    document.querySelectorAll('.delete-account').forEach(btn => {
                        btn.addEventListener('click', () => {
                            pendingDelete = btn;
                            document.getElementById('delete-name').textContent = btn.dataset.name;
                            modal.classList.add('active');
                        });
                    });

                    confirmBtn.addEventListener('click', async () => {
                        if (!pendingDelete) return;
                        confirmBtn.disabled = true;
                        try {
                            await adminApi('delete', { id: Number(pendingDelete.dataset.id) });
                            showToast(`${pendingDelete.dataset.name}'s account was deleted.`, 'success');
                            setTimeout(() => window.location.reload(), 900); // refresh the counts
                        } catch (err) {
                            showToast(err.message, 'danger');
                            confirmBtn.disabled = false;
                        }
                        modal.classList.remove('active');
                    });
                });
            </script>
<?php
app_page_end();
