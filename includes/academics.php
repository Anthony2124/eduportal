<?php
/*
 * Enrollment and grades: which students are in which course, and their grades.
 * Grades (prelim, midterm, final) use the 1.00 - 5.00 scale (1.00 highest, 3.00 lowest passing grade, 5.00 failed).
 * Admins work with every course; faculty only with the courses assigned to them (teaches_course()).
 */
require_once __DIR__ . '/content.php';
require_once __DIR__ . '/submissions.php'; // assignment lists show the student's work

const LOWEST_PASSING_GRADE = 3.00;

// Schedule chart: [bar color, label color] per subject, given out in this order. It's a categorical palette
// checked to stay tell-apart-able for colorblind viewers when neighbors touch; don't add colors or reorder.
// From the 9th subject on, bars share the neutral gray (their labels, the legend and the table still tell them apart).
const SCHEDULE_COLORS = [
    ['#2a78d6', '#ffffff'], ['#eb6834', '#0b0b0b'], ['#1baf7a', '#0b0b0b'], ['#eda100', '#0b0b0b'],
    ['#e87ba4', '#0b0b0b'], ['#008300', '#ffffff'], ['#4a3aa7', '#ffffff'], ['#e34948', '#0b0b0b'],
];
const SCHEDULE_OTHER_COLOR = ['#64748b', '#ffffff'];

/** Students enrolled in a course, with their grades. */
function course_roster(int $courseId): array
{
    $stmt = db()->prepare(
        "SELECT u.id, u.id_number, u.first_name, u.last_name, u.program, u.year_level, u.email, u.picture_url,
                en.created_at AS enrolled_at, g.prelim, g.midterm, g.final
         FROM enrollments en
         JOIN users u ON u.id = en.student_id
         LEFT JOIN grades g ON g.course_id = en.course_id AND g.student_id = en.student_id
         WHERE en.course_id = ?
         ORDER BY u.last_name, u.first_name"
    );
    $stmt->execute([$courseId]);
    return $stmt->fetchAll();
}

/**
 * Students who are not in the course yet (for the Enroll dropdown): those in the course's program, or every
 * student for a course from before courses had a program.
 */
function students_not_in(array $course): array
{
    $stmt = db()->prepare(
        "SELECT id, id_number, first_name, last_name, program FROM users
         WHERE role = 'student' AND id NOT IN (SELECT student_id FROM enrollments WHERE course_id = ?)
           AND (? IS NULL OR program = ?)
         ORDER BY last_name, first_name"
    );
    $stmt->execute([$course['id'], $course['program'], $course['program']]);
    return $stmt->fetchAll();
}

/** A student's courses (with faculty name, class size and the student's grades). */
function student_courses(int $studentId): array
{
    $stmt = db()->prepare(
        "SELECT c.*, CONCAT(f.first_name, ' ', f.last_name) AS person_name,
                (SELECT COUNT(*) FROM enrollments x WHERE x.course_id = c.id) AS enrolled,
                g.prelim, g.midterm, g.final, g.updated_at AS graded_at
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

/** Final rating = average of the prelim, midterm and final grades; null until all three are in. */
function final_rating(array $grades): ?float
{
    $periods = [$grades['prelim'], $grades['midterm'], $grades['final']];
    return in_array(null, $periods, true) ? null : round(array_sum(array_map('floatval', $periods)) / 3, 2);
}

/** Has any grade (prelim, midterm or final) been entered? */
function has_grades(array $grades): bool
{
    return $grades['prelim'] !== null || $grades['midterm'] !== null || $grades['final'] !== null;
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
        $rating = final_rating($course);
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
        $rating = final_rating($course);
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

/** Dropdown that switches the page to another course; given a $program ('' = all), a Program dropdown before it. */
function course_picker(array $courses, ?array $selected, ?string $program = null): void
{
    ?>
                    <form method="get" class="course-picker">
                        <?php if ($program !== null): ?>
                        <label for="program-picker">Program</label>
                        <select class="form-select" id="program-picker" name="program" onchange="this.form.submit()">
                            <option value="">All programs</option>
                            <?php foreach (PROGRAMS as $option): ?>
                            <option<?= $option === $program ? ' selected' : '' ?>><?= e($option) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php endif; ?>
                        <?php if ($courses): ?>
                        <label for="course-picker">Course</label>
                        <select class="form-select" id="course-picker" name="course" onchange="this.form.submit()">
                            <?php foreach ($courses as $course): ?>
                            <option value="<?= (int) $course['id'] ?>"<?= $selected && (int) $selected['id'] === (int) $course['id'] ? ' selected' : '' ?>>
                                <?= e(class_name($course) . ' - ' . $course['title']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php endif; ?>
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

/** Details of a student's course with its grades, for the details dialog (Grades page, dashboard). */
function grade_detail(array $user, array $course, bool $withLink): array
{
    $rating = final_rating($course);
    [$badge, $remark] = grade_remarks($rating);
    return course_detail($course, [
        'kicker' => 'Grades',
        'badges' => [[$badge, $remark]],
        'facts' => [
            ['Prelim', format_grade($course['prelim'])],
            ['Midterm', format_grade($course['midterm'])],
            ['Final', format_grade($course['final'])],
            ['Final rating', format_grade($rating)],
        ],
        'link' => $withLink ? detail_link($user, 'pages/academics/grades.php', 'All grades') : null,
    ]);
}

/** Compact list of a student's classes (code, title, schedule, room) for the dashboard. */
function class_list(array $user, array $courses): void
{
    if (!$courses) {
        app_empty('fa-calendar-xmark', 'No classes yet', 'Your classes will appear here once you are enrolled.');
        return;
    }
    ?>
                <div class="content-list compact">
                    <?php foreach ($courses as $course): ?>
                    <article class="content-item"<?= detail_attr(course_detail($course, ['link' => detail_link($user, 'pages/academics/schedule.php', 'Weekly schedule')])) ?>>
                        <h3 class="content-item-title"><?= detail_button($course['code'] . ' - ' . $course['title']) ?></h3>
                        <div class="content-meta">
                            <?php if ($course['block']): ?>
                            <span><i class="fa-solid fa-users-rectangle"></i> <?= e($course['block']) ?></span>
                            <?php endif; ?>
                            <span><i class="fa-regular fa-clock"></i> <?= e(class_schedule($course) ?: 'Schedule to be announced') ?></span>
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

/**
 * The week as a bar chart: a row per day, the time of day across, a bar per class meeting and one color per
 * subject (the same subject in two blocks shares its color). Clicking a bar shows the class details; classes
 * without days and times are listed under the chart.
 */
function schedule_chart(array $classes): void
{
    $hasTime = fn($c) => $c['days'] && $c['start_time'] && $c['end_time'];
    $scheduled = array_values(array_filter($classes, $hasTime));
    $unscheduled = array_values(array_filter($classes, fn($c) => !$hasTime($c)));

    // a color per subject, in course-code order
    $codes = array_values(array_unique(array_column($scheduled, 'code')));
    sort($codes, SORT_NATURAL | SORT_FLAG_CASE);
    $colors = [];
    foreach ($codes as $i => $code) {
        $colors[$code] = SCHEDULE_COLORS[$i] ?? SCHEDULE_OTHER_COLOR;
    }
    $titles = array_column($scheduled, 'title', 'code');

    // hours shown: at least 7 AM to 5 PM, widened to fit every class
    $hours = fn(string $time) => (int) substr($time, 0, 2) + (int) substr($time, 3, 2) / 60;
    $first = 7;
    $last = 17;
    foreach ($scheduled as $class) {
        $first = min($first, (int) floor($hours($class['start_time'])));
        $last = max($last, (int) ceil($hours($class['end_time'])));
    }
    $span = $last - $first;
    $percent = fn(float $hour) => round(($hour - $first) / $span * 100, 3);

    // Monday to Friday, and Saturday when a class meets then. Classes that overlap on a day get their own lanes.
    $days = array_keys(CLASS_DAYS);
    if (!array_filter($scheduled, fn($c) => in_array('Sat', explode(',', $c['days']), true))) {
        $days = array_diff($days, ['Sat']);
    }
    $rows = [];
    foreach ($days as $day) {
        $meetings = array_filter($scheduled, fn($c) => in_array($day, explode(',', $c['days']), true));
        usort($meetings, fn($a, $b) => strcmp($a['start_time'], $b['start_time']));
        $laneEnds = [];
        $bars = [];
        foreach ($meetings as $class) {
            $lane = 0;
            while (isset($laneEnds[$lane]) && $laneEnds[$lane] > $class['start_time']) {
                $lane++;
            }
            $laneEnds[$lane] = $class['end_time'];
            $bars[] = [$class, $lane];
        }
        $rows[$day] = [$bars, max(1, count($laneEnds))];
    }
    $today = date('D');
    ?>
                <div class="sched-chart">
                    <?php if (!$scheduled): ?>
                    <?php app_empty('fa-calendar-week', 'No class times yet', 'Classes show up here once they have days and times.'); ?>
                    <?php else: ?>
                    <div class="sched-scroll">
                        <div class="sched-grid" style="--hours: <?= $span ?>;">
                            <div class="sched-row sched-axis" aria-hidden="true">
                                <div class="sched-day"></div>
                                <div class="sched-track">
                                    <?php for ($hour = $first; $hour <= $last; $hour += $span > 12 ? 2 : 1): ?>
                                    <span style="left: <?= $percent($hour) ?>%;"><?= e(date('g A', mktime($hour, 0))) ?></span>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <?php foreach ($rows as $day => [$bars, $lanes]): ?>
                            <div class="sched-row<?= $day === $today ? ' today' : '' ?>" style="--lanes: <?= $lanes ?>;">
                                <div class="sched-day"><?= e($day) ?><?php if ($day === $today): ?><small>Today</small><?php endif; ?></div>
                                <div class="sched-track">
                                    <?php foreach ($bars as [$class, $lane]):
                                        [$fill, $ink] = $colors[$class['code']];
                                        $start = $hours($class['start_time']);
                                        $time = format_time_range($class['start_time'], $class['end_time']);
                                        $room = $class['room'] ? 'Room ' . $class['room'] : '';
                                    ?>
                                    <button type="button" class="sched-bar" data-detail-trigger<?= detail_attr(course_detail($class, ['color' => $fill])) ?>
                                            data-tip="<?= e(json_encode([$day . ' ' . $time, class_name($class) . ' - ' . $class['title'], $room])) ?>"
                                            aria-label="<?= e(class_name($class) . ', ' . date('l', strtotime($day)) . ' ' . $time . ($room ? ', ' . $room : '')) ?>"
                                            style="left: <?= $percent($start) ?>%; width: calc(<?= round(($hours($class['end_time']) - $start) / $span * 100, 3) ?>% - 2px); --lane: <?= $lane ?>; --bar: <?= $fill ?>; --ink: <?= $ink ?>;">
                                        <span class="sched-bar-label"><?= e($class['code']) ?></span>
                                    </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php if (count($codes) > 1): ?>
                    <ul class="sched-legend" aria-label="Subjects">
                        <?php foreach ($codes as $code): ?>
                        <li><span class="sched-swatch" style="background: <?= $colors[$code][0] ?>;"></span><strong><?= e($code) ?></strong> <span class="muted"><?= e($titles[$code]) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($unscheduled): ?>
                    <div class="sched-unscheduled">
                        <span><i class="fa-regular fa-clock"></i> Schedule to be announced:</span>
                        <?php foreach ($unscheduled as $class): ?>
                        <span class="sched-chip"<?= detail_attr(course_detail($class)) ?>><?= detail_button(class_name($class)) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
    <?php
}

/** Shared dialog for entering a student's grades (opened by the "Edit grade" buttons). */
function grade_form(): void
{
    ?>
            <div class="modal-overlay" id="grade-modal">
                <div class="modal" role="dialog" aria-modal="true" aria-labelledby="grade-modal-title" style="max-width: 540px;">
                    <div class="modal-header">
                        <h3 class="modal-title" id="grade-modal-title">Grades</h3>
                        <button type="button" class="modal-close-btn" data-modal-close aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <form class="grade-form" novalidate>
                        <input type="hidden" name="course_id">
                        <input type="hidden" name="student_id">
                        <div class="modal-body">
                            <p class="muted" style="margin-bottom: 16px;">
                                Use 1.00 to 5.00: 1.00 is the highest, 3.00 the lowest passing grade, 5.00 failed. The final rating is the average of the three.
                                Leave a box empty if that grade isn't in yet.
                            </p>
                            <div class="form-grid-3">
                                <div class="form-group">
                                    <label class="form-label" for="grade-prelim">Prelim</label>
                                    <input class="form-control" id="grade-prelim" name="prelim" type="number" min="1" max="5" step="0.01" placeholder="e.g. 1.50">
                                </div>
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
