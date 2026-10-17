<?php
// Account Settings: the signed-in user's real account details from the database.
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/settings/profile.php';
$user = require_page_access($page);

$role = $user['role'];
$idLabels = ['student' => 'Student ID', 'faculty' => 'Employee ID', 'admin' => 'Username'];
$yearNames = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];
$formatDate = fn(?string $value, string $format) => $value ? date($format, strtotime($value)) : 'Never';

$details = [
    ['Full name', $user['first_name'] . ' ' . $user['last_name']],
    [$idLabels[$role], $user['id_number']],
    ['Role', ROLE_NAMES[$role]],
];
if ($role === 'student') {
    $details[] = ['Program', $user['program']];
    $details[] = ['Year level', $yearNames[$user['year_level'] - 1] ?? ''];
} else {
    $details[] = [$role === 'faculty' ? 'Department' : 'Office', $user['department']];
}
$details[] = ['Linked Gmail', $user['email'] ?? 'None'];
$details[] = ['Member since', $formatDate($user['created_at'], 'F j, Y')];
$details[] = ['Last sign-in', $formatDate($user['last_login_at'], 'F j, Y g:i A')];

app_page_start($user, $page, 'Account Settings');
app_page_header('Account Settings', 'Your account details');
?>
            <section class="card section-gap">
                <?php app_card_header('fa-id-card', 'Profile'); ?>
                <div class="card-body">
                    <div class="profile-summary">
                        <div class="profile-avatar">
                            <?php if ($user['picture_url']): ?>
                            <img src="<?= e($user['picture_url']) ?>" alt="" referrerpolicy="no-referrer">
                            <?php else: ?>
                            <?= e(initials($user)) ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="profile-name"><?= e($user['first_name'] . ' ' . $user['last_name']) ?></div>
                            <div class="muted"><?= e(user_subtitle($user)) ?></div>
                        </div>
                    </div>

                    <dl class="detail-list">
                        <?php foreach ($details as [$label, $value]): ?>
                        <div>
                            <dt><?= e($label) ?></dt>
                            <dd><?= e($value) ?></dd>
                        </div>
                        <?php endforeach; ?>
                    </dl>
                </div>
            </section>
<?php
app_page_end();
