<?php
/*
 * Classes, enrollment and grades (called by js/content.js). Faculty pick the open classes they teach;
 * admins can enroll students in every class; faculty enroll students and enter grades only for their own classes.
 *
 *   POST api/academics.php?action=teach         { course_id }   faculty: become the instructor of an open class
 *   POST api/academics.php?action=drop_class    { course_id }   faculty: stop teaching a class (it becomes open again)
 *   POST api/academics.php?action=enroll        { course_id, student_id }   a student of the course's program
 *   POST api/academics.php?action=unenroll      { course_id, student_id }   also removes that student's grade
 *   POST api/academics.php?action=save_grade    { course_id, student_id, prelim, midterm, final }   1.00 - 5.00, any may be empty
 *   POST api/academics.php?action=clear_grade   { course_id, student_id }
 */
declare(strict_types=1);

const EDUPORTAL_API = true;
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../includes/chat.php';

$input = read_json_request();

$user = current_user();
if (!$user) {
    fail('Please sign in again.', 401);
}
$courseId = (int) ($input['course_id'] ?? 0);
$studentId = (int) ($input['student_id'] ?? 0);
$action = $_GET['action'] ?? '';

// Faculty picking (or dropping) the classes they teach
if ($action === 'teach' || $action === 'drop_class') {
    if ($user['role'] !== 'faculty') {
        fail('Only faculty pick the classes they teach.', 403);
    }
    $action === 'teach' ? teach_class($user, $courseId) : drop_class($user, $courseId);
}

if (!teaches_course($user, $courseId)) {
    fail('You can only do this for your own courses (or the course no longer exists).', 403);
}
if (in_array($action, ['save_grade', 'clear_grade'], true) && $user['role'] !== 'faculty') {
    fail("Grades are entered by the course's faculty.", 403);
}

match ($action) {
    'enroll' => enroll($user, $courseId, $studentId),
    'unenroll' => unenroll($user, $courseId, $studentId),
    'save_grade' => save_grade($user, $courseId, $studentId, $input),
    'clear_grade' => clear_grade($user, $courseId, $studentId),
    default => fail('Unknown action.', 404),
};

/** The faculty member becomes the instructor of an open class, unless it clashes with a class they teach. */
function teach_class(array $user, int $courseId): never
{
    $stmt = db()->prepare('SELECT * FROM courses WHERE id = ?');
    $stmt->execute([$courseId]);
    $course = $stmt->fetch();
    if (!$course) {
        fail('That class no longer exists.', 404);
    }
    if ($course['faculty_id'] !== null) {
        fail((int) $course['faculty_id'] === (int) $user['id'] ? 'You already teach this class.' : 'Another instructor already took this class.', 409);
    }
    if ($clash = schedule_clash('faculty_id', (int) $user['id'], $course, $courseId)) {
        fail('This class clashes with your ' . class_name($clash) . ' (' . class_schedule($clash) . ').', 409);
    }
    // only if nobody took it in the meantime
    $stmt = db()->prepare('UPDATE courses SET faculty_id = ? WHERE id = ? AND faculty_id IS NULL');
    $stmt->execute([$user['id'], $courseId]);
    if (!$stmt->rowCount()) {
        fail('Another instructor already took this class.', 409);
    }
    log_activity($user, 'academics', 'Took a class', log_names($courseId));
    json_response(['ok' => true]);
}

/** The class goes back to the open classes; its students, assignments and grades stay. */
function drop_class(array $user, int $courseId): never
{
    $stmt = db()->prepare('UPDATE courses SET faculty_id = NULL WHERE id = ? AND faculty_id = ?');
    $stmt->execute([$courseId, $user['id']]);
    if (!$stmt->rowCount()) {
        fail("You don't teach this class.", 403);
    }
    log_activity($user, 'academics', 'Dropped a class', log_names($courseId));
    json_response(['ok' => true]);
}

/** "IT 101 (BSIT 2-A) - Title" for the activity log, or with a student "Juan Dela Cruz in IT 101 (BSIT 2-A)". */
function log_names(int $courseId, int $studentId = 0): string
{
    $stmt = db()->prepare('SELECT code, block, title FROM courses WHERE id = ?');
    $stmt->execute([$courseId]);
    $course = $stmt->fetch();
    $class = $course ? class_name($course) : "class #{$courseId}";
    if (!$studentId) {
        return $course ? $class . ' - ' . $course['title'] : $class;
    }
    $stmt = db()->prepare("SELECT CONCAT(first_name, ' ', last_name) FROM users WHERE id = ?");
    $stmt->execute([$studentId]);
    return ($stmt->fetchColumn() ?: "student #{$studentId}") . ' in ' . $class;
}

function is_enrolled(int $courseId, int $studentId): bool
{
    $stmt = db()->prepare('SELECT 1 FROM enrollments WHERE course_id = ? AND student_id = ?');
    $stmt->execute([$courseId, $studentId]);
    return (bool) $stmt->fetchColumn();
}

function enroll(array $user, int $courseId, int $studentId): never
{
    // only students of the course's program (any student for a course that has no program yet)
    $stmt = db()->prepare(
        "SELECT u.program AS student_program, c.program FROM users u JOIN courses c ON c.id = ?
         WHERE u.id = ? AND u.role = 'student'"
    );
    $stmt->execute([$courseId, $studentId]);
    $found = $stmt->fetch();
    if (!$found) {
        fail('Choose a student from the list.', 422, ['field' => 'student_id']);
    }
    if ($found['program'] !== null && $found['student_program'] !== $found['program']) {
        fail("Only {$found['program']} students can be enrolled in this course.", 422, ['field' => 'student_id']);
    }
    if (is_enrolled($courseId, $studentId)) {
        fail('That student is already in this course.', 409, ['field' => 'student_id']);
    }
    db()->prepare('INSERT INTO enrollments (course_id, student_id, enrolled_by) VALUES (?, ?, ?)')
        ->execute([$courseId, $studentId, $user['id']]);
    add_chat_notice($courseId, $studentId, 'added'); // enrolled students are in the class group chat
    log_activity($user, 'academics', 'Enrolled a student', log_names($courseId, $studentId));
    json_response(['ok' => true]);
}

function unenroll(array $user, int $courseId, int $studentId): never
{
    if (!is_enrolled($courseId, $studentId)) {
        fail('That student is not in this course.', 404);
    }
    db()->prepare('DELETE FROM grades WHERE course_id = ? AND student_id = ?')->execute([$courseId, $studentId]);
    db()->prepare('DELETE FROM enrollments WHERE course_id = ? AND student_id = ?')->execute([$courseId, $studentId]);
    add_chat_notice($courseId, $studentId, 'removed');
    log_activity($user, 'academics', 'Removed a student from a class', log_names($courseId, $studentId));
    json_response(['ok' => true]);
}

/** '' -> null, otherwise a grade from 1.00 to 5.00 with at most 2 decimals. */
function read_grade(array $input, string $name, string $label): ?string
{
    $value = trim((string) ($input[$name] ?? ''));
    if ($value === '') {
        return null;
    }
    if (!preg_match('/^\d(\.\d{1,2})?$/', $value) || (float) $value < 1 || (float) $value > 5) {
        fail("{$label} must be a grade from 1.00 to 5.00.", 422, ['field' => $name]);
    }
    return number_format((float) $value, 2, '.', '');
}

function save_grade(array $user, int $courseId, int $studentId, array $input): never
{
    if (!is_enrolled($courseId, $studentId)) {
        fail('That student is not in this course.', 404);
    }
    $prelim = read_grade($input, 'prelim', 'Prelim');
    $midterm = read_grade($input, 'midterm', 'Midterm');
    $final = read_grade($input, 'final', 'Final');
    if ($prelim === null && $midterm === null && $final === null) {
        fail('Enter a prelim, midterm or final grade (or use Clear to remove the grades).', 422, ['field' => 'prelim']);
    }
    db()->prepare(
        'INSERT INTO grades (course_id, student_id, prelim, midterm, final, graded_by) VALUES (?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE prelim = VALUES(prelim), midterm = VALUES(midterm), final = VALUES(final), graded_by = VALUES(graded_by)'
    )->execute([$courseId, $studentId, $prelim, $midterm, $final, $user['id']]);
    log_activity($user, 'academics', 'Saved grades', log_names($courseId, $studentId)
        . ' (prelim ' . ($prelim ?? '-') . ', midterm ' . ($midterm ?? '-') . ', final ' . ($final ?? '-') . ')');
    json_response(['ok' => true]);
}

function clear_grade(array $user, int $courseId, int $studentId): never
{
    db()->prepare('DELETE FROM grades WHERE course_id = ? AND student_id = ?')->execute([$courseId, $studentId]);
    log_activity($user, 'academics', 'Cleared grades', log_names($courseId, $studentId));
    json_response(['ok' => true]);
}
