<?php
// Enrollment (Admin and Faculty): add or remove students in a course. Faculty only see the courses they teach;
// the Admin can narrow the courses to one program. Only students of the course's program can be enrolled.
require __DIR__ . '/../../includes/app_shell.php';
require __DIR__ . '/../../includes/academics.php';
$page = 'pages/admin/enrollment.php';
$user = require_page_access($page);

$courses = managed_courses($user);
$program = null; // the admin's Program filter ('' = all programs); faculty don't get one
if ($user['role'] === 'admin') {
    $program = in_array($_GET['program'] ?? '', PROGRAMS, true) ? $_GET['program'] : '';
    if ($program !== '') {
        $courses = array_values(array_filter($courses, fn($c) => $c['program'] === $program));
    }
}
$course = pick_course($courses);
$roster = $course ? course_roster((int) $course['id']) : [];
$available = $course ? students_not_in($course) : [];
$yearNames = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];

app_page_start($user, $page, 'Enrollment');
app_page_header('Enrollment', 'Add or remove students in a course. Enrolled students are added to the class group chat automatically.');

if (!$course && !$program) {
    if ($user['role'] === 'faculty') {
        app_empty_card('fa-book', 'Your Courses', 'fa-book', 'No classes yet', 'Pick the classes you teach on the Courses page.');
    } else {
        app_empty_card('fa-book', 'Courses', 'fa-book', 'No courses yet', 'Add courses on the Courses page first.');
    }
} elseif (!$course) {
?>
            <section class="card section-gap">
                <div class="card-header card-header-wrap">
                    <div>
                        <div class="card-title"><i class="fa-solid fa-users" style="color: var(--primary);"></i> <?= e($program) ?></div>
                    </div>
                    <div class="card-tools">
                        <?php course_picker($courses, null, $program); ?>
                    </div>
                </div>
                <?php app_empty('fa-book', 'No courses yet', "Add {$program} courses on the Courses page first."); ?>
            </section>
<?php
} else {
    $details = array_filter([$course['title'], $course['program'], count($roster) . ' enrolled', class_schedule($course)]);
?>
            <section class="card section-gap">
                <div class="card-header card-header-wrap">
                    <div>
                        <div class="card-title"><i class="fa-solid fa-users" style="color: var(--primary);"></i> Students in <?= e(class_name($course)) ?></div>
                        <div class="card-subtitle"><?= e(implode(' • ', $details)) ?></div>
                    </div>
                    <div class="card-tools">
                        <?php course_picker($courses, $course, $program); ?>
                        <button type="button" class="btn btn-primary btn-sm" data-enroll-add="<?= (int) $course['id'] ?>">
                            <i class="fa-solid fa-user-plus"></i> Enroll student
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Student ID</th>
                                <th>Program</th>
                                <th class="hide-mobile">Enrolled on</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$roster): ?>
                            <tr><td colspan="5"><?php app_empty('fa-user-graduate', 'No students yet', 'Use "Enroll student" to add students to this course.'); ?></td></tr>
                            <?php endif; ?>
                            <?php foreach ($roster as $student): ?>
                            <tr>
                                <td><?php person_cell($student); ?></td>
                                <td class="nowrap"><?= e($student['id_number']) ?></td>
                                <td class="muted"><?= e($student['program']) ?> • <?= e($yearNames[$student['year_level'] - 1] ?? '') ?></td>
                                <td class="muted hide-mobile nowrap"><?= e(format_date($student['enrolled_at'])) ?></td>
                                <td style="text-align: right;">
                                    <button type="button" class="btn btn-outline btn-sm content-remove" data-unenroll
                                            data-course="<?= (int) $course['id'] ?>" data-student="<?= (int) $student['id'] ?>"
                                            data-name="<?= e($student['first_name'] . ' ' . $student['last_name']) ?>" data-course-name="<?= e(class_name($course)) ?>">
                                        <i class="fa-solid fa-user-minus"></i> Remove
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="modal-overlay" id="enroll-modal">
                <div class="modal" role="dialog" aria-modal="true" aria-labelledby="enroll-modal-title" style="max-width: 480px;">
                    <div class="modal-header">
                        <h3 class="modal-title" id="enroll-modal-title">Enroll a student in <?= e(class_name($course)) ?></h3>
                        <button type="button" class="modal-close-btn" data-modal-close aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <form novalidate>
                        <input type="hidden" name="course_id" value="<?= (int) $course['id'] ?>">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="form-label" for="enroll-student">Student <span class="required">*</span></label>
                                <select class="form-select" id="enroll-student" name="student_id">
                                    <option value="">Select a student</option>
                                    <?php foreach ($available as $student): ?>
                                    <option value="<?= (int) $student['id'] ?>"><?= e($student['last_name'] . ', ' . $student['first_name'] . ' (' . $student['id_number'] . ') - ' . $student['program']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($course['program'] === null): ?>
                                <div class="field-note">This course has no program yet, so every student is listed. Set its program on the Courses page.</div>
                                <?php elseif (!$available): ?>
                                <div class="field-note">Every <?= e($course['program']) ?> student is already in this course (or none have signed up yet).</div>
                                <?php else: ?>
                                <div class="field-note">Only <?= e($course['program']) ?> students are listed.</div>
                                <?php endif; ?>
                            </div>
                            <div class="form-error" hidden></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Enroll</button>
                        </div>
                    </form>
                </div>
            </div>
<?php
}
app_page_end();
