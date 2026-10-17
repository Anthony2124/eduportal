<?php
/*
 * Enrollment and grades (called by js/content.js). Admins can do this for every course,
 * faculty only for courses assigned to them.
 *
 *   POST api/academics.php?action=enroll        { course_id, student_id }
 *   POST api/academics.php?action=unenroll      { course_id, student_id }   also removes that student's grade
 *   POST api/academics.php?action=save_grade    { course_id, student_id, midterm, final }   1.00 - 5.00, either may be empty
 *   POST api/academics.php?action=clear_grade   { course_id, student_id }
 */
declare(strict_types=1);

const EDUPORTAL_API = true;
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../includes/academics.php';

$input = read_json_request();

$user = current_user();
if (!$user) {
    fail('Please sign in again.', 401);
}
$courseId = (int) ($input['course_id'] ?? 0);
$studentId = (int) ($input['student_id'] ?? 0);
if (!teaches_course($user, $courseId)) {
    fail('You can only do this for your own courses (or the course no longer exists).', 403);
}

match ($_GET['action'] ?? '') {
    'enroll' => enroll($user, $courseId, $studentId),
    'unenroll' => unenroll($courseId, $studentId),
    'save_grade' => save_grade($user, $courseId, $studentId, $input),
    'clear_grade' => clear_grade($courseId, $studentId),
    default => fail('Unknown action.', 404),
};

function is_enrolled(int $courseId, int $studentId): bool
{
    $stmt = db()->prepare('SELECT 1 FROM enrollments WHERE course_id = ? AND student_id = ?');
    $stmt->execute([$courseId, $studentId]);
    return (bool) $stmt->fetchColumn();
}

function enroll(array $user, int $courseId, int $studentId): never
{
    $stmt = db()->prepare("SELECT 1 FROM users WHERE id = ? AND role = 'student'");
    $stmt->execute([$studentId]);
    if (!$stmt->fetchColumn()) {
        fail('Choose a student from the list.', 422, ['field' => 'student_id']);
    }
    if (is_enrolled($courseId, $studentId)) {
        fail('That student is already in this course.', 409, ['field' => 'student_id']);
    }
    db()->prepare('INSERT INTO enrollments (course_id, student_id, enrolled_by) VALUES (?, ?, ?)')
        ->execute([$courseId, $studentId, $user['id']]);
    json_response(['ok' => true]);
}

function unenroll(int $courseId, int $studentId): never
{
    if (!is_enrolled($courseId, $studentId)) {
        fail('That student is not in this course.', 404);
    }
    db()->prepare('DELETE FROM grades WHERE course_id = ? AND student_id = ?')->execute([$courseId, $studentId]);
    db()->prepare('DELETE FROM enrollments WHERE course_id = ? AND student_id = ?')->execute([$courseId, $studentId]);
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
    $midterm = read_grade($input, 'midterm', 'Midterm');
    $final = read_grade($input, 'final', 'Final');
    if ($midterm === null && $final === null) {
        fail('Enter a midterm or final grade (or use Clear to remove the grades).', 422, ['field' => 'midterm']);
    }
    db()->prepare(
        'INSERT INTO grades (course_id, student_id, midterm, final, graded_by) VALUES (?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE midterm = VALUES(midterm), final = VALUES(final), graded_by = VALUES(graded_by)'
    )->execute([$courseId, $studentId, $midterm, $final, $user['id']]);
    json_response(['ok' => true]);
}

function clear_grade(int $courseId, int $studentId): never
{
    db()->prepare('DELETE FROM grades WHERE course_id = ? AND student_id = ?')->execute([$courseId, $studentId]);
    json_response(['ok' => true]);
}
