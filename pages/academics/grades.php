<?php
// Grades: students see their own grades and GPA; faculty enter, edit or clear the prelim, midterm and final
// grades for their classes. Scale: 1.00 highest, 3.00 lowest passing grade, 5.00 failed.
require __DIR__ . '/../../includes/app_shell.php';
require __DIR__ . '/../../includes/academics.php';
$page = 'pages/academics/grades.php';
$user = require_page_access($page);

app_page_start($user, $page, 'Grades');

if ($user['role'] === 'student') {
    $courses = student_courses((int) $user['id']);
    $gpa = gpa($courses);
    $graded = count(array_filter($courses, fn($c) => final_rating($c) !== null));

    app_page_header('Grades & Transcript', 'Your grades for the courses you are enrolled in');
    app_stats([
        ['fa-chart-line', 'primary', $gpa === null ? '—' : number_format($gpa, 2), 'GPA'],
        ['fa-graduation-cap', 'success', (string) units_earned($courses), 'Units earned'],
        ['fa-book', 'secondary', $graded . ' / ' . count($courses), 'Courses graded'],
    ]);
?>
            <section class="card section-gap">
                <?php app_card_header('fa-table-list', 'Academic Record', '1.00 is the highest grade, 3.00 the lowest passing grade, 5.00 failed'); ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr><th>Course</th><th>Description</th><th>Units</th><th>Prelim</th><th>Midterm</th><th>Final</th><th>Final rating</th><th>Remarks</th></tr>
                        </thead>
                        <tbody>
                            <?php if (!$courses): ?>
                            <tr><td colspan="8"><?php app_empty('fa-chart-bar', 'No grades yet', 'Your grades appear here once you are enrolled and your instructors post them.'); ?></td></tr>
                            <?php endif; ?>
                            <?php foreach ($courses as $course):
                                $rating = final_rating($course);
                                [$badge, $remark] = grade_remarks($rating);
                            ?>
                            <tr>
                                <td class="nowrap"><strong><?= e(class_name($course)) ?></strong></td>
                                <td><?= e($course['title']) ?></td>
                                <td><?= (int) $course['units'] ?></td>
                                <td><?= e(format_grade($course['prelim'])) ?></td>
                                <td><?= e(format_grade($course['midterm'])) ?></td>
                                <td><?= e(format_grade($course['final'])) ?></td>
                                <td><strong><?= e(format_grade($rating)) ?></strong></td>
                                <td><span class="badge <?= $badge ?>"><?= e($remark) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
<?php
} else {
    $courses = managed_courses($user);
    $course = pick_course($courses);
    $roster = $course ? course_roster((int) $course['id']) : [];

    app_page_header('Grades', $user['role'] === 'admin' ? 'Enter and review grades for every course' : 'Enter grades for the students in your courses');

    if (!$course) {
        if ($user['role'] === 'faculty') {
            app_empty_card('fa-book', 'Your Courses', 'fa-book', 'No classes yet', 'Pick the classes you teach on the Courses page.');
        } else {
            app_empty_card('fa-book', 'Courses', 'fa-book', 'No courses yet', 'Add courses on the Courses page first.');
        }
    } else {
?>
            <section class="card section-gap">
                <div class="card-header card-header-wrap">
                    <div>
                        <div class="card-title"><i class="fa-solid fa-table-list" style="color: var(--primary);"></i> Grades for <?= e(class_name($course)) ?></div>
                        <div class="card-subtitle"><?= e($course['title']) ?> • <?= count($roster) ?> <?= count($roster) === 1 ? 'student' : 'students' ?> • 1.00 highest, 3.00 lowest passing, 5.00 failed</div>
                    </div>
                    <div class="card-tools">
                        <?php course_picker($courses, $course); ?>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr><th>Student</th><th>Student ID</th><th>Prelim</th><th>Midterm</th><th>Final</th><th>Final rating</th><th>Remarks</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php if (!$roster): ?>
                            <tr><td colspan="8"><?php app_empty('fa-user-graduate', 'No students in this course', 'Enroll students on the Enrollment page first.'); ?></td></tr>
                            <?php endif; ?>
                            <?php foreach ($roster as $student):
                                $rating = final_rating($student);
                                [$badge, $remark] = grade_remarks($rating);
                                $name = $student['first_name'] . ' ' . $student['last_name'];
                                $hasGrade = has_grades($student);
                            ?>
                            <tr>
                                <td><?php person_cell($student); ?></td>
                                <td class="nowrap"><?= e($student['id_number']) ?></td>
                                <td><?= e(format_grade($student['prelim'])) ?></td>
                                <td><?= e(format_grade($student['midterm'])) ?></td>
                                <td><?= e(format_grade($student['final'])) ?></td>
                                <td><strong><?= e(format_grade($rating)) ?></strong></td>
                                <td><span class="badge <?= $badge ?>"><?= e($remark) ?></span></td>
                                <td>
                                    <div class="content-actions">
                                        <button type="button" class="btn btn-outline btn-sm" data-grade-edit
                                                data-course="<?= (int) $course['id'] ?>" data-student="<?= (int) $student['id'] ?>" data-name="<?= e($name) ?>"
                                                data-prelim="<?= e($student['prelim'] ?? '') ?>" data-midterm="<?= e($student['midterm'] ?? '') ?>" data-final="<?= e($student['final'] ?? '') ?>">
                                            <i class="fa-solid fa-pen"></i> <?= $hasGrade ? 'Edit grade' : 'Add grade' ?>
                                        </button>
                                        <?php if ($hasGrade): ?>
                                        <button type="button" class="btn btn-outline btn-sm content-remove" data-grade-clear
                                                data-course="<?= (int) $course['id'] ?>" data-student="<?= (int) $student['id'] ?>" data-name="<?= e($name) ?>">
                                            <i class="fa-solid fa-eraser"></i> Clear
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
<?php
        grade_form();
    }
}
app_page_end();
