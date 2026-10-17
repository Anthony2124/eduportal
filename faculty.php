<?php
// Faculty dashboard. Signed-out visitors go to sign in; students and admins go to their own dashboards.
require __DIR__ . '/api/bootstrap.php';
require __DIR__ . '/includes/app_shell.php';
require __DIR__ . '/includes/content.php';

$user = require_page_role('faculty');

// Which student programs belong to each department (programs are the ones offered on the sign-up form)
const DEPARTMENT_PROGRAMS = [
    'College of Computer Studies' => ['BS Computer Science', 'BS Information Technology', 'BS Information Systems', 'BS Computer Engineering'],
    'College of Business and Accountancy' => ['BS Business Administration', 'BS Accountancy'],
    'College of Arts and Sciences' => ['BS Psychology'],
    'College of Engineering' => ['BS Computer Engineering'],
    'College of Education' => [],
];

$department = $user['department'];
$programs = DEPARTMENT_PROGRAMS[$department] ?? [];

// Real data: students registered in this department's programs
$roster = [];
if ($programs) {
    $placeholders = implode(',', array_fill(0, count($programs), '?'));
    $stmt = db()->prepare(
        "SELECT * FROM users WHERE role = 'student' AND program IN ({$placeholders}) ORDER BY last_name, first_name"
    );
    $stmt->execute($programs);
    $roster = $stmt->fetchAll();
}
$myClasses = content_items('courses', 'a.faculty_id = ?', [$user['id']]);
$allStudents = (int) db()->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
$yearNames = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];

app_page_start($user, 'faculty.php', 'Faculty Dashboard', ['id' => 'page-search', 'placeholder' => 'Search students by name, ID or program...']);
?>
            <section class="staff-banner">
                <div>
                    <h2><?= e(time_greeting()) ?>, Prof. <?= e($user['last_name']) ?>!</h2>
                    <p><?= e($department) ?> • Faculty Portal</p>
                </div>
                <div class="banner-chips">
                    <span class="banner-chip"><i class="fa-solid fa-user-graduate"></i> <?= count($roster) ?> students in your department</span>
                </div>
            </section>

<?php
app_stats([
    ['fa-chalkboard', 'primary', count($myClasses), 'Classes assigned to you'],
    ['fa-user-graduate', 'success', count($roster), 'Students in your department'],
    ['fa-school', 'warning', $allStudents, 'Registered students (all programs)'],
]);
?>
            <section class="card section-gap" id="classes">
                <?php app_card_header('fa-chalkboard-user', 'My Classes', 'Courses the admin assigned to you'); ?>
                <?php course_table($user, $myClasses, false, 'No classes assigned yet', 'Courses the admin assigns to you will appear here.'); ?>
            </section>

            <section class="card section-gap" id="roster">
                <div class="card-header">
                    <div>
                        <div class="card-title"><i class="fa-solid fa-users" style="color: var(--success);"></i> Student Roster</div>
                        <div class="card-subtitle">
                            Students registered in <?= e($department) ?><?= $programs ? ' (' . e(implode(', ', $programs)) . ')' : '' ?>
                        </div>
                    </div>
                    <span class="badge badge-success"><?= count($roster) ?> students</span>
                </div>
                <?php if ($roster): ?>
                <div class="table-responsive">
                    <table class="data-table" id="roster-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Student ID</th>
                                <th>Program</th>
                                <th>Year</th>
                                <th class="hide-mobile">Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roster as $student): ?>
                            <tr data-searchable>
                                <td>
                                    <div class="person-cell">
                                        <div class="person-avatar">
                                            <?php if ($student['picture_url']): ?>
                                            <img src="<?= e($student['picture_url']) ?>" alt="" referrerpolicy="no-referrer">
                                            <?php else: ?>
                                            <?= e(initials($student)) ?>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="person-name"><?= e($student['first_name'] . ' ' . $student['last_name']) ?></div>
                                            <div class="person-sub"><?= e($student['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= e($student['id_number']) ?></td>
                                <td class="muted"><?= e($student['program']) ?></td>
                                <td><span class="badge badge-outline"><?= e($yearNames[$student['year_level'] - 1] ?? '') ?></span></td>
                                <td class="muted hide-mobile"><?= e(date('M j, Y', strtotime($student['created_at']))) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr id="no-match" hidden>
                                <td colspan="5" class="empty-state">No students match your search.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fa-solid fa-user-graduate"></i>
                    No students from <?= e($department) ?> have created an account yet.
                </div>
                <?php endif; ?>
            </section>

            <script>
                // Top search box filters the student roster
                document.getElementById('page-search').addEventListener('input', (e) => {
                    const query = e.target.value.trim().toLowerCase();
                    const rows = document.querySelectorAll('#roster-table tr[data-searchable]');
                    let shown = 0;
                    rows.forEach(row => {
                        row.hidden = !row.textContent.toLowerCase().includes(query);
                        if (!row.hidden) shown++;
                    });
                    const noMatch = document.getElementById('no-match');
                    if (noMatch) noMatch.hidden = shown > 0;
                    if (query) document.getElementById('roster').scrollIntoView();
                });
            </script>
<?php
app_page_end();
