<?php
/*
 * Enrollment and grades: which students are in which course, and their grades.
 * Grades use the 1.00 - 5.00 scale (1.00 highest, 3.00 lowest passing grade, 5.00 failed).
 * Admins work with every course; faculty only with the courses assigned to them (teaches_course()).
 */
require_once __DIR__ . '/content.php';

const LOWEST_PASSING_GRADE = 3.00;

/** Students enrolled in a course, with their grades. */
function course_roster(int $courseId): array
{
    $stmt = db()->prepare(
        "SELECT u.id, u.id_number, u.first_name, u.last_name, u.program, u.year_level, u.email, u.picture_url,
                en.created_at AS enrolled_at, g.midterm, g.final
         FROM enrollments en
         JOIN users u ON u.id = en.student_id
         LEFT JOIN grades g ON g.course_id = en.course_id AND g.student_id = en.student_id
         WHERE en.course_id = ?
         ORDER BY u.last_name, u.first_name"
    );
    $stmt->execute([$courseId]);
    return $stmt->fetchAll();
}

/** Students who are not in the course yet (for the Enroll dropdown). */
function students_not_in(int $courseId): array
{
    $stmt = db()->prepare(
        "SELECT id, id_number, first_name, last_name, program FROM users
         WHERE role = 'student' AND id NOT IN (SELECT student_id FROM enrollments WHERE course_id = ?)
         ORDER BY last_name, first_name"
    );
    $stmt->execute([$courseId]);
    return $stmt->fetchAll();
}

/** A student's courses (with faculty name, class size and the student's grades). */
function student_courses(int $studentId): array
{
    $stmt = db()->prepare(
        "SELECT c.*, CONCAT(f.first_name, ' ', f.last_name) AS person_name,
                (SELECT COUNT(*) FROM enrollments x WHERE x.course_id = c.id) AS enrolled,
                g.midterm, g.final, g.updated_at AS graded_at
         FROM enrollments en
         JOIN courses c ON c.id = en.course_id
         LEFT JOIN users f ON f.id = c.faculty_id
         LEFT JOIN grades g ON g.course_id = c.id AND g.student_id = en.student_id
         WHERE en.student_id = ?
         ORDER BY c.code"
    );
    $stmt->execute([$studentId]);
    return $stmt->fetchAll();
}

/** Final rating = average of midterm and final; null until both are in. */
function final_rating(mixed $midterm, mixed $final): ?float
{
    return $midterm !== null && $final !== null ? round(((float) $midterm + (float) $final) / 2, 2) : null;
}

/** [badge class, text] for a final rating. */
function grade_remarks(?float $rating): array
{
    if ($rating === null) {
        return ['badge-outline', 'In progress'];
    }
    return $rating <= LOWEST_PASSING_GRADE ? ['badge-success', 'Passed'] : ['badge-danger', 'Failed'];
}

function format_grade(mixed $value): string
{
    return $value === null ? '—' : number_format((float) $value, 2);
}

/** GPA weighted by units, over courses that have a final rating. */
function gpa(array $courses): ?float
{
    $points = 0.0;
    $units = 0;
    foreach ($courses as $course) {
        $rating = final_rating($course['midterm'], $course['final']);
        if ($rating !== null) {
            $points += $rating * $course['units'];
            $units += $course['units'];
        }
    }
    return $units ? round($points / $units, 2) : null;
}

/** Units of courses passed. */
function units_earned(array $courses): int
{
    $units = 0;
    foreach ($courses as $course) {
        $rating = final_rating($course['midterm'], $course['final']);
        if ($rating !== null && $rating <= LOWEST_PASSING_GRADE) {
            $units += (int) $course['units'];
        }
    }
    return $units;
}

/** The course chosen with ?course=ID (if it is one of $courses), otherwise the first one. */
function pick_course(array $courses): ?array
{
    $wanted = (int) ($_GET['course'] ?? 0);
    foreach ($courses as $course) {
        if ((int) $course['id'] === $wanted) {
            return $course;
        }
    }
    return $courses[0] ?? null;
}

/** Dropdown that switches the page to another course. */
function course_picker(array $courses, ?array $selected): void
{
    ?>
                    <form method="get" class="course-picker">
                        <label for="course-picker">Course</label>
                        <select class="form-select" id="course-picker" name="course" onchange="this.form.submit()">
                            <?php foreach ($courses as $course): ?>
                            <option value="<?= (int) $course['id'] ?>"<?= $selected && (int) $selected['id'] === (int) $course['id'] ? ' selected' : '' ?>>
                                <?= e($course['code'] . ' - ' . $course['title']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <noscript><button class="btn btn-outline btn-sm">Show</button></noscript>
                    </form>
    <?php
}

/** Avatar + name + Gmail for a person in a table. */
function person_cell(array $person): void
{
    ?>
                                    <div class="person-cell">
                                        <div class="person-avatar">
                                            <?php if (!empty($person['picture_url'])): ?>
                                            <img src="<?= e($person['picture_url']) ?>" alt="" referrerpolicy="no-referrer">
                                            <?php else: ?>
                                            <?= e(initials($person)) ?>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="person-name"><?= e($person['first_name'] . ' ' . $person['last_name']) ?></div>
                                            <div class="person-sub"><?= e($person['email'] ?? '') ?></div>
                                        </div>
                                    </div>
    <?php
}

/** Compact list of a student's classes (code, title, schedule, room) for the dashboard. */
function class_list(array $courses): void
{
    if (!$courses) {
        app_empty('fa-calendar-xmark', 'No classes yet', 'Your classes will appear here once you are enrolled.');
        return;
    }
    ?>
                <div class="content-list compact">
                    <?php foreach ($courses as $course): ?>
                    <article class="content-item">
                        <h3 class="content-item-title"><?= e($course['code'] . ' - ' . $course['title']) ?></h3>
                        <div class="content-meta">
                            <span><i class="fa-regular fa-clock"></i> <?= e($course['schedule'] ?: 'Schedule to be announced') ?></span>
                            <?php if ($course['room']): ?>
                            <span><i class="fa-solid fa-door-open"></i> <?= e($course['room']) ?></span>
                            <?php endif; ?>
                            <?php if ($course['person_name']): ?>
                            <span><i class="fa-solid fa-chalkboard-user"></i> <?= e($course['person_name']) ?></span>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
    <?php
}

/** Shared dialog for entering a student's grades (opened by the "Edit grade" buttons). */
function grade_form(): void
{
    ?>
            <div class="modal-overlay" id="grade-modal">
                <div class="modal" role="dialog" aria-modal="true" aria-labelledby="grade-modal-title" style="max-width: 460px;">
                    <div class="modal-header">
                        <h3 class="modal-title" id="grade-modal-title">Grades</h3>
                        <button type="button" class="modal-close-btn" data-modal-close aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <form class="grade-form" novalidate>
                        <input type="hidden" name="course_id">
                        <input type="hidden" name="student_id">
                        <div class="modal-body">
                            <p class="muted" style="margin-bottom: 16px;">
                                Use 1.00 to 5.00: 1.00 is the highest, 3.00 the lowest passing grade, 5.00 failed.
                                Leave a box empty if that grade isn't in yet.
                            </p>
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label class="form-label" for="grade-midterm">Midterm</label>
                                    <input class="form-control" id="grade-midterm" name="midterm" type="number" min="1" max="5" step="0.01" placeholder="e.g. 1.75">
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="grade-final">Final</label>
                                    <input class="form-control" id="grade-final" name="final" type="number" min="1" max="5" step="0.01" placeholder="e.g. 2.00">
                                </div>
                            </div>
                            <div class="form-error" hidden></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                        </div>
                    </form>
                </div>
            </div>
    <?php
}
