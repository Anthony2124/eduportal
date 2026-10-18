<?php
/*
 * Assignment submissions (called by js/submissions.js; the rules are in includes/submissions.php).
 * Students work on their own submission; the course's faculty member returns it with a score and feedback.
 *
 *   POST api/submissions.php?action=upload       multipart: assignment_id, file   student: attach a file
 *   POST api/submissions.php?action=remove_file  { file_id }                      student: remove an attached file
 *   POST api/submissions.php?action=save         { assignment_id, body }          student: keep the answer as a draft
 *   POST api/submissions.php?action=turn_in      { assignment_id, body }          student
 *   POST api/submissions.php?action=unsubmit     { assignment_id }                student, until it's returned
 *   POST api/submissions.php?action=return       { assignment_id, student_id, score, feedback }   faculty
 *   GET  api/submissions.php?action=file&id=ID                                    the student, or the class's faculty
 */
declare(strict_types=1);

const EDUPORTAL_API = true;
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../includes/submissions.php';

$user = current_user();
if (!$user) {
    fail('Please sign in again.', 401);
}
session_write_close(); // downloads and uploads can take a while; don't hold up the user's other pages

$action = $_GET['action'] ?? '';
if ($action === 'file') {
    download_file($user, (int) ($_GET['id'] ?? 0));
}
if ($action === 'upload') {
    upload_file($user);
}

$input = read_json_request();
$assignmentId = (int) ($input['assignment_id'] ?? 0);
match ($action) {
    'remove_file' => remove_file($user, (int) ($input['file_id'] ?? 0)),
    'save' => save_answer($user, $assignmentId, $input, false),
    'turn_in' => save_answer($user, $assignmentId, $input, true),
    'unsubmit' => unsubmit($user, $assignmentId),
    'return' => return_work($user, $assignmentId, $input),
    default => fail('Unknown action.', 404),
};

/** "Lab 1 (IT 101 (BSIT 2-A))" for the activity log */
function assignment_label(array $assignment): string
{
    return $assignment['title'] . ' (' . class_name($assignment, 'course_') . ')';
}

/** The assignment, for a student enrolled in its class. */
function student_assignment(array $user, int $assignmentId): array
{
    if ($user['role'] !== 'student') {
        fail('Only students turn in work.', 403);
    }
    $assignment = find_assignment($user, $assignmentId);
    if (!$assignment) {
        fail('That assignment no longer exists, or you are not in its class.', 404);
    }
    return $assignment;
}

/** The student's submission, created as a draft if needed; stops if it's turned in or returned. */
function editable_submission(array $user, array $assignment): array
{
    db()->prepare('INSERT IGNORE INTO submissions (assignment_id, student_id) VALUES (?, ?)')->execute([$assignment['id'], $user['id']]);
    $submission = find_submission((int) $assignment['id'], (int) $user['id']);
    if ($submission['returned_at'] !== null) {
        fail('Your instructor already returned this work, so it can no longer be changed.', 409);
    }
    if ($submission['turned_in_at'] !== null) {
        fail('Unsubmit first to change your work.', 409);
    }
    return $submission;
}

function upload_file(array $user): never
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        fail('Method not allowed.', 405);
    }
    // Like the JSON requests: a header that a form on another website can't send
    if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'fetch') {
        fail('Upload files from the assignment page.', 400);
    }
    $maxMb = SUBMISSION_MAX_BYTES / 1048576;
    $file = $_FILES['file'] ?? null;
    if (!$file) {
        // a request bigger than PHP's post_max_size arrives with nothing in it
        fail(empty($_POST) ? "That file is too big. Files can be up to {$maxMb} MB." : 'Choose a file to upload.', 422);
    }
    if (is_array($file['error'])) {
        fail('Upload one file at a time.', 422);
    }
    if (in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) || $file['size'] > SUBMISSION_MAX_BYTES) {
        fail("That file is too big. Files can be up to {$maxMb} MB.", 422);
    }
    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        fail('The upload did not finish. Please try again.', 422);
    }

    $assignment = student_assignment($user, (int) ($_POST['assignment_id'] ?? 0));
    $submission = editable_submission($user, $assignment);
    if (count(submission_files((int) $submission['id'])) >= SUBMISSION_MAX_FILES) {
        fail('You can attach up to ' . SUBMISSION_MAX_FILES . ' files. Remove one first.', 422);
    }

    $name = trim(preg_replace('/[\x00-\x1F\x7F]/u', '', basename(str_replace('\\', '/', (string) $file['name']))));
    $name = mb_substr($name, -200) ?: 'file';
    $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!isset(SUBMISSION_TYPES[$extension])) {
        fail("{$name} can't be uploaded. Use PDF, Word, PowerPoint, Excel, text, pictures, ZIP or code files.", 422);
    }

    $dir = submission_dir();
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        fail('Uploads are not set up on this server (the uploads folder could not be created).', 500);
    }
    $stored = bin2hex(random_bytes(16)) . '.' . $extension;
    if (!move_uploaded_file($file['tmp_name'], "{$dir}/{$stored}")) {
        fail('The file could not be saved. Please try again.', 500);
    }
    db()->prepare('INSERT INTO submission_files (submission_id, original_name, stored_name, size) VALUES (?, ?, ?, ?)')
        ->execute([$submission['id'], $name, $stored, (int) $file['size']]);

    json_response(['ok' => true, 'file' => [
        'id' => (int) db()->lastInsertId(),
        'name' => $name,
        'size' => format_bytes((int) $file['size']),
    ]]);
}

function remove_file(array $user, int $fileId): never
{
    $stmt = db()->prepare(
        'SELECT f.id, f.stored_name, s.assignment_id FROM submission_files f JOIN submissions s ON s.id = f.submission_id
         WHERE f.id = ? AND s.student_id = ?'
    );
    $stmt->execute([$fileId, $user['id']]);
    $file = $stmt->fetch();
    if (!$file) {
        fail('That file was already removed.', 404);
    }
    editable_submission($user, student_assignment($user, (int) $file['assignment_id']));

    db()->prepare('DELETE FROM submission_files WHERE id = ?')->execute([$file['id']]);
    $path = submission_dir() . '/' . basename($file['stored_name']);
    if (is_file($path)) {
        unlink($path);
    }
    json_response(['ok' => true]);
}

/** Saves the student's answer; with $turnIn, also turns the work in. */
function save_answer(array $user, int $assignmentId, array $input, bool $turnIn): never
{
    $assignment = student_assignment($user, $assignmentId);
    $body = trim(str_replace("\r\n", "\n", (string) ($input['body'] ?? '')));
    if (mb_strlen($body) > SUBMISSION_MAX_TEXT) {
        fail('Your answer can be up to ' . number_format(SUBMISSION_MAX_TEXT) . ' characters.', 422, ['field' => 'body']);
    }
    $submission = editable_submission($user, $assignment);

    db()->prepare('UPDATE submissions SET body = ?' . ($turnIn ? ', turned_in_at = NOW()' : '') . ' WHERE id = ?')
        ->execute([$body === '' ? null : $body, $submission['id']]);
    if ($turnIn) {
        log_activity($user, 'academics', 'Turned in an assignment', assignment_label($assignment));
    }
    json_response(['ok' => true]);
}

function unsubmit(array $user, int $assignmentId): never
{
    $assignment = student_assignment($user, $assignmentId);
    $submission = find_submission((int) $assignment['id'], (int) $user['id']);
    if (!$submission || $submission['turned_in_at'] === null) {
        fail("This work isn't turned in.", 409);
    }
    if ($submission['returned_at'] !== null) {
        fail('Your instructor already returned this work, so it can no longer be unsubmitted.', 409);
    }
    db()->prepare('UPDATE submissions SET turned_in_at = NULL WHERE id = ?')->execute([$submission['id']]);
    log_activity($user, 'academics', 'Unsubmitted an assignment', assignment_label($assignment));
    json_response(['ok' => true]);
}

/** Faculty: return a student's work with a score (out of the assignment's points) and private feedback. */
function return_work(array $user, int $assignmentId, array $input): never
{
    if ($user['role'] !== 'faculty') {
        fail("Work is returned by the class's faculty.", 403);
    }
    $assignment = find_assignment($user, $assignmentId);
    if (!$assignment) {
        fail('That assignment no longer exists, or it is not for a class you teach.', 404);
    }
    $studentId = (int) ($input['student_id'] ?? 0);
    $stmt = db()->prepare(
        "SELECT CONCAT(u.first_name, ' ', u.last_name) FROM enrollments en JOIN users u ON u.id = en.student_id
         WHERE en.course_id = ? AND en.student_id = ?"
    );
    $stmt->execute([$assignment['course_id'], $studentId]);
    $studentName = $stmt->fetchColumn();
    if ($studentName === false) {
        fail('That student is not in this class.', 404);
    }

    $points = $assignment['points'] !== null ? (int) $assignment['points'] : null;
    $raw = trim((string) ($input['score'] ?? ''));
    $score = null;
    if ($raw !== '') {
        if ($points === null) {
            fail('This assignment has no points, so leave the score empty (or edit the assignment to give it points).', 422, ['field' => 'score']);
        }
        if (!preg_match('/^\d{1,4}(\.\d{1,2})?$/', $raw) || (float) $raw > $points) {
            fail("Enter a score from 0 to {$points}.", 422, ['field' => 'score']);
        }
        $score = number_format((float) $raw, 2, '.', '');
    }
    $feedback = trim(str_replace("\r\n", "\n", (string) ($input['feedback'] ?? '')));
    if (mb_strlen($feedback) > 5000) {
        fail('Feedback can be up to 5,000 characters.', 422, ['field' => 'feedback']);
    }

    db()->prepare(
        'INSERT INTO submissions (assignment_id, student_id, score, feedback, returned_at, graded_by) VALUES (?, ?, ?, ?, NOW(), ?)
         ON DUPLICATE KEY UPDATE score = VALUES(score), feedback = VALUES(feedback), returned_at = NOW(), graded_by = VALUES(graded_by)'
    )->execute([$assignment['id'], $studentId, $score, $feedback === '' ? null : $feedback, $user['id']]);

    log_activity($user, 'academics', 'Returned an assignment',
        "{$studentName}: " . assignment_label($assignment) . ($score !== null ? ' - ' . format_score(['score' => $score], $points) : ''));
    json_response(['ok' => true]);
}

/** Sends an attached file to the student who uploaded it or to the class's faculty member. */
function download_file(array $user, int $fileId): never
{
    $stmt = db()->prepare(
        'SELECT f.original_name, f.stored_name, s.student_id, s.assignment_id FROM submission_files f
         JOIN submissions s ON s.id = f.submission_id WHERE f.id = ?'
    );
    $stmt->execute([$fileId]);
    $file = $stmt->fetch();
    $allowed = $file && ((int) $file['student_id'] === (int) $user['id']
        || ($user['role'] === 'faculty' && find_assignment($user, (int) $file['assignment_id'])));
    $path = $file ? submission_dir() . '/' . basename($file['stored_name']) : '';
    if (!$allowed || !is_file($path)) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=utf-8');
        exit('File not found.');
    }

    $name = $file['original_name'];
    $ascii = preg_replace('/[^A-Za-z0-9._ -]/', '_', $name);
    $disposition = submission_file_inline($name) ? 'inline' : 'attachment';
    header('Content-Type: ' . (SUBMISSION_TYPES[strtolower(pathinfo($name, PATHINFO_EXTENSION))] ?? 'application/octet-stream'));
    header('Content-Length: ' . filesize($path));
    header("Content-Disposition: {$disposition}; filename=\"{$ascii}\"; filename*=UTF-8''" . rawurlencode($name));
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store');
    readfile($path);
    exit;
}
