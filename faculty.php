<?php
// Faculty dashboard. Signed-out visitors go to sign in; students and admins go to their own dashboards.
require __DIR__ . '/api/bootstrap.php';
require __DIR__ . '/includes/staff_layout.php';

$user = require_page_role('faculty');

// Which student programs belong to each department (programs are the ones offered on the sign-up form)
const DEPARTMENT_PROGRAMS = [
    'College of Computer Studies' => ['BS Computer Science', 'BS Information Technology', 'BS Information Systems', 'BS Computer Engineering'],
    'College of Business and Accountancy' => ['BS Business Administration', 'BS Accountancy'],
    'College of Arts and Sciences' => ['BS Psychology'],
    'College of Engineering' => ['BS Computer Engineering'],
    'College of Education' => [],
];

// Sample teaching loads per department: [code, title, schedule, room, enrolled]. Classes aren't stored in the database yet.
const SAMPLE_CLASSES = [
    'College of Computer Studies' => [
        ['CS 201', 'Data Structures & Algorithms', 'MWF 8:00 - 9:00 AM', 'Room 301', 38],
        ['CS 203', 'Web Development', 'TTh 1:00 - 2:30 PM', 'Lab 4', 32],
        ['NET 101', 'Networking I', 'MWF 10:00 - 11:00 AM', 'Room 108', 35],
    ],
    'College of Business and Accountancy' => [
        ['ACC 101', 'Financial Accounting', 'MWF 9:00 - 10:00 AM', 'Room 204', 40],
        ['BA 210', 'Principles of Marketing', 'TTh 10:00 - 11:30 AM', 'Room 212', 36],
        ['ECO 101', 'Basic Microeconomics', 'MWF 1:00 - 2:00 PM', 'Room 207', 42],
    ],
    'College of Arts and Sciences' => [
        ['PSY 101', 'General Psychology', 'MWF 8:00 - 9:00 AM', 'Room 110', 45],
        ['ENG 102', 'Purposive Communication', 'TTh 9:00 - 10:30 AM', 'Room 115', 38],
        ['HUM 101', 'Art Appreciation', 'Sat 9:00 AM - 12:00 PM', 'Room 415', 40],
    ],
    'College of Engineering' => [
        ['CPE 211', 'Digital Logic Design', 'MWF 10:00 - 11:00 AM', 'Eng Lab 2', 30],
        ['MATH 202', 'Differential Equations', 'TTh 1:00 - 2:30 PM', 'Room 305', 41],
        ['ES 101', 'Engineering Drawing', 'Fri 1:00 - 4:00 PM', 'Drafting Room', 28],
    ],
    'College of Education' => [
        ['EDUC 101', 'The Teaching Profession', 'MWF 9:00 - 10:00 AM', 'Room 120', 35],
        ['EDUC 205', 'Assessment of Learning', 'TTh 10:00 - 11:30 AM', 'Room 122', 33],
        ['FIL 101', 'Komunikasyon sa Akademikong Filipino', 'MWF 2:00 - 3:00 PM', 'Room 118', 39],
    ],
];

$department = $user['department'];
$programs = DEPARTMENT_PROGRAMS[$department] ?? [];
$classes = SAMPLE_CLASSES[$department] ?? [];
$classStudents = array_sum(array_column($classes, 4));

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
$allStudents = (int) db()->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
$yearNames = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];

staff_page_start($user, 'Faculty Dashboard', 'Search students by name, ID or program...');
?>
            <section class="staff-banner">
                <div>
                    <h2><?= e(time_greeting()) ?>, Prof. <?= e($user['last_name']) ?>!</h2>
                    <p><?= e($department) ?> • Faculty Portal</p>
                </div>
                <div class="banner-chips">
                    <span class="banner-chip"><i class="fa-solid fa-chalkboard"></i> <?= count($classes) ?> classes this semester</span>
                    <span class="banner-chip"><i class="fa-solid fa-user-graduate"></i> <?= count($roster) ?> students in your department</span>
                </div>
            </section>

            <section class="grid-4 section-gap">
                <div class="stat-tile">
                    <div class="stat-tile-icon tone-primary"><i class="fa-solid fa-chalkboard"></i></div>
                    <div>
                        <div class="stat-tile-value"><?= count($classes) ?></div>
                        <div class="stat-tile-label">Classes this semester</div>
                    </div>
                </div>
                <div class="stat-tile">
                    <div class="stat-tile-icon tone-secondary"><i class="fa-solid fa-people-group"></i></div>
                    <div>
                        <div class="stat-tile-value"><?= $classStudents ?></div>
                        <div class="stat-tile-label">Students in your classes</div>
                    </div>
                </div>
                <div class="stat-tile">
                    <div class="stat-tile-icon tone-success"><i class="fa-solid fa-user-graduate"></i></div>
                    <div>
                        <div class="stat-tile-value"><?= count($roster) ?></div>
                        <div class="stat-tile-label">Registered in your department</div>
                    </div>
                </div>
                <div class="stat-tile">
                    <div class="stat-tile-icon tone-warning"><i class="fa-solid fa-school"></i></div>
                    <div>
                        <div class="stat-tile-value"><?= $allStudents ?></div>
                        <div class="stat-tile-label">Registered students (all)</div>
                    </div>
                </div>
            </section>

            <div class="dashboard-columns">
                <section class="card" id="classes">
                    <div class="card-header">
                        <div>
                            <div class="card-title"><i class="fa-solid fa-chalkboard-user" style="color: var(--primary);"></i> My Classes</div>
                            <div class="card-subtitle">Sample teaching load - classes aren't saved in the database yet</div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Schedule</th>
                                    <th class="hide-mobile">Room</th>
                                    <th>Enrolled</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($classes as [$code, $title, $schedule, $room, $enrolled]): ?>
                                <tr>
                                    <td>
                                        <div class="person-name"><?= e($code) ?></div>
                                        <div class="person-sub"><?= e($title) ?></div>
                                    </td>
                                    <td class="muted"><?= e($schedule) ?></td>
                                    <td class="muted hide-mobile"><?= e($room) ?></td>
                                    <td><span class="badge badge-primary"><?= $enrolled ?> students</span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fa-solid fa-bell" style="color: var(--accent);"></i> Reminders</div>
                    </div>
                    <div class="card-body">
                        <ul class="quick-list">
                            <li>
                                <span class="stat-tile-icon tone-danger"><i class="fa-solid fa-file-pen"></i></span>
                                <span><strong>Submit midterm grades</strong>Due Oct 30, 2026 via the Registrar</span>
                            </li>
                            <li>
                                <span class="stat-tile-icon tone-primary"><i class="fa-solid fa-people-roof"></i></span>
                                <span><strong>Department meeting</strong>Friday, 3:00 PM • Faculty Lounge</span>
                            </li>
                            <li>
                                <span class="stat-tile-icon tone-success"><i class="fa-solid fa-clipboard-check"></i></span>
                                <span><strong>Midterm exams week</strong>Oct 20 - 24, 2026 • check proctoring slots</span>
                            </li>
                        </ul>
                    </div>
                </section>
            </div>

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
staff_page_end();
