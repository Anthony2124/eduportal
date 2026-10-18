<?php
// Courses: the Admin picks a program, then adds, edits and removes its classes (course + block, days, time, room)
// and can assign faculty.
// Faculty pick open classes to teach ("Teach this class") and can drop their own. Students see the classes
// they're enrolled in and everything offered to their program.
require __DIR__ . '/../../includes/app_shell.php';
require __DIR__ . '/../../includes/academics.php';
$page = 'pages/academics/courses.php';
$user = require_page_access($page);

$courses = content_items('courses');

app_page_start($user, $page, 'Courses');

if ($user['role'] === 'student') {
    $myCourses = student_courses((int) $user['id']);
    app_page_header('Courses', 'Your enrolled courses and everything offered this semester');
    app_stats([
        ['fa-book', 'primary', (string) count($myCourses), 'Enrolled courses'],
        ['fa-layer-group', 'secondary', (string) array_sum(array_column($myCourses, 'units')), 'Units enrolled'],
    ]);
?>
            <section class="card section-gap">
                <?php app_card_header('fa-book-open-reader', 'My Courses'); ?>
                <?php course_table($user, $myCourses, false, 'No courses yet', 'Courses you are enrolled in will appear here.'); ?>
            </section>

            <section class="card section-gap">
                <?php app_card_header('fa-book', 'Courses Offered', 'The ' . $user['program'] . ' courses this semester'); ?>
                <?php course_table($user, array_values(array_filter($courses, fn($c) => $c['program'] === $user['program'])), false,
                    'No courses yet', "Courses offered to {$user['program']} this semester will appear here."); ?>
            </section>
<?php
} elseif ($user['role'] === 'faculty') {
    $mine = array_values(array_filter($courses, fn($c) => (int) $c['faculty_id'] === (int) $user['id']));
    $open = array_values(array_filter($courses, fn($c) => $c['faculty_id'] === null));
    app_page_header('Courses', 'Pick the classes you will teach. You take on the class with its block, schedule and room.');
    app_stats([
        ['fa-chalkboard-user', 'primary', (string) count($mine), 'Classes you teach'],
        ['fa-layer-group', 'secondary', (string) array_sum(array_column($mine, 'units')), 'Units you teach'],
        ['fa-door-open', 'warning', (string) count($open), 'Open classes'],
    ]);
?>
            <section class="card section-gap" id="my-classes">
                <?php app_card_header('fa-chalkboard-user', 'My Classes', 'Drop a class to give it back to the open classes'); ?>
                <?php course_table($user, $mine, true, 'No classes yet', 'Pick a class from Open Classes below.'); ?>
            </section>

            <section class="card section-gap" id="open-classes">
                <?php app_card_header('fa-door-open', 'Open Classes', "Classes without an instructor. Ones that clash with your schedule can't be picked."); ?>
                <?php course_table($user, $open, true, 'No open classes', 'Every class has an instructor. New classes appear here when the admin adds them.'); ?>
            </section>
<?php
} else {
    // ?program= shows one program's courses, and "Add course" then starts with that program already picked
    $program = in_array($_GET['program'] ?? '', PROGRAMS, true) ? $_GET['program'] : '';
    $shown = $program === '' ? $courses : array_values(array_filter($courses, fn($c) => $c['program'] === $program));
    $open = array_filter($shown, fn($c) => $c['faculty_id'] === null);
    app_page_header('Courses', 'Pick a program, then add its courses. Leave Faculty empty to let faculty pick the class.');
    app_stats([
        ['fa-book', 'primary', (string) count($shown), 'Classes offered'],
        ['fa-layer-group', 'secondary', (string) array_sum(array_column($shown, 'units')), 'Total units'],
        ['fa-door-open', 'warning', (string) count($open), 'Open classes (no instructor)'],
    ]);
?>
            <section class="card section-gap">
                <div class="card-header card-header-wrap">
                    <div>
                        <div class="card-title"><i class="fa-solid fa-book" style="color: var(--primary);"></i> Course List</div>
                        <div class="card-subtitle"><?= $program === '' ? 'All programs' : e($program) ?></div>
                    </div>
                    <div class="card-tools">
                        <?php course_picker([], null, $program); ?>
                        <button type="button" class="btn btn-primary btn-sm" data-content-add="courses"
                                <?= $program !== '' ? 'data-preset="' . e(json_encode(['program' => $program])) . '"' : '' ?>>
                            <i class="fa-solid fa-plus"></i> Add course
                        </button>
                    </div>
                </div>
                <?php course_table($user, $shown, true, 'No courses yet',
                    $program === '' ? 'Courses offered this semester will appear here.' : "Use \"Add course\" to add {$program} courses."); ?>
            </section>
<?php
    content_form($user, 'courses');
}
app_page_end();
