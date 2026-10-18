<?php
/*
 * Assignment submissions, like Google Classroom: a student adds files and/or an answer, turns it in (and can
 * unsubmit to change it until it's returned); the course's faculty member reviews it and returns it with a
 * score and private feedback. Used by pages/academics/assignment.php and api/submissions.php.
 *
 * A submission's state: not turned in (no row, or turned_in_at NULL) -> turned in (turned_in_at set) ->
 * returned (returned_at set). Work that was never turned in can be returned too (e.g. a 0 for missing work).
 */
require_once __DIR__ . '/content.php';

const SUBMISSION_MAX_BYTES = 10 * 1024 * 1024; // per file
const SUBMISSION_MAX_FILES = 10;
const SUBMISSION_MAX_TEXT = 10000;

// File types students can upload: extension => the type it is served as. Anything that a browser could run
// (HTML, SVG, scripts) is only ever downloaded, never shown in the browser (see submission_file_inline()).
const SUBMISSION_TYPES = [
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'ppt' => 'application/vnd.ms-powerpoint',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'odt' => 'application/vnd.oasis.opendocument.text',
    'txt' => 'text/plain',
    'csv' => 'text/csv',
    'md' => 'text/plain',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'zip' => 'application/zip',
    'rar' => 'application/vnd.rar',
    '7z' => 'application/x-7z-compressed',
    // code, for programming classes (always downloaded as plain text)
    'py' => 'text/plain', 'java' => 'text/plain', 'c' => 'text/plain', 'cpp' => 'text/plain', 'h' => 'text/plain',
    'cs' => 'text/plain', 'js' => 'text/plain', 'html' => 'text/plain', 'css' => 'text/plain', 'php' => 'text/plain',
    'sql' => 'text/plain', 'json' => 'text/plain', 'xml' => 'text/plain',
];

/** Where uploaded files are kept (uploads/.htaccess blocks direct access). */
function submission_dir(): string
{
    return dirname(__DIR__) . '/uploads/submissions';
}

/** The assignment (with its course) if this user can see it: faculty of the course, or a student enrolled in it. */
function find_assignment(array $user, int $id): ?array
{
    [$where, $params] = scoped($user, 'a.id = ?', [$id]);
    return content_items('assignments', $where, $params)[0] ?? null;
}

/** When the assignment is due: its due date and time, or the end of the due date. */
function due_timestamp(array $assignment): int
{
    return strtotime($assignment['due_date'] . ' ' . ($assignment['due_time'] ?? '23:59:59'));
}

/**
 * [state, label, badge class] of a student's work: returned, turned_in, late (turned in after the due time),
 * missing (not turned in and past due) or assigned.
 */
function submission_status(array $assignment, ?array $submission): array
{
    if ($submission && $submission['returned_at'] !== null) {
        return ['returned', 'Returned', 'badge-success'];
    }
    if ($submission && $submission['turned_in_at'] !== null) {
        return strtotime($submission['turned_in_at']) > due_timestamp($assignment)
            ? ['late', 'Turned in late', 'badge-warning']
            : ['turned_in', 'Turned in', 'badge-success'];
    }
    return time() > due_timestamp($assignment) ? ['missing', 'Missing', 'badge-danger'] : ['assigned', 'Assigned', 'badge-outline'];
}

/** "85 / 100" (or "85" when the assignment has no points); '' before it's graded. */
function format_score(?array $submission, ?int $points): string
{
    if (!$submission || $submission['score'] === null) {
        return '';
    }
    $score = rtrim(rtrim(number_format((float) $submission['score'], 2, '.', ''), '0'), '.');
    return $points ? "{$score} / {$points}" : $score;
}

function find_submission(int $assignmentId, int $studentId): ?array
{
    $stmt = db()->prepare('SELECT * FROM submissions WHERE assignment_id = ? AND student_id = ?');
    $stmt->execute([$assignmentId, $studentId]);
    return $stmt->fetch() ?: null;
}

function submission_files(int $submissionId): array
{
    $stmt = db()->prepare('SELECT id, original_name, size FROM submission_files WHERE submission_id = ? ORDER BY id');
    $stmt->execute([$submissionId]);
    return $stmt->fetchAll();
}

/** The student's submissions for these assignments, by assignment id (for the Assignments list). */
function student_submissions(int $studentId, array $assignmentIds): array
{
    if (!$assignmentIds) {
        return [];
    }
    $marks = implode(',', array_fill(0, count($assignmentIds), '?'));
    $stmt = db()->prepare("SELECT * FROM submissions WHERE student_id = ? AND assignment_id IN ({$marks})");
    $stmt->execute([$studentId, ...$assignmentIds]);
    return array_column($stmt->fetchAll(), null, 'assignment_id');
}

/** How many of the enrolled students turned in each assignment, by assignment id. */
function turned_in_counts(array $assignmentIds): array
{
    if (!$assignmentIds) {
        return [];
    }
    $marks = implode(',', array_fill(0, count($assignmentIds), '?'));
    $stmt = db()->prepare(
        "SELECT a.id, COUNT(en.student_id) AS students,
                COALESCE(SUM(s.turned_in_at IS NOT NULL), 0) AS done
         FROM assignments a
         LEFT JOIN enrollments en ON en.course_id = a.course_id
         LEFT JOIN submissions s ON s.assignment_id = a.id AND s.student_id = en.student_id
         WHERE a.id IN ({$marks})
         GROUP BY a.id"
    );
    $stmt->execute($assignmentIds);
    return array_column($stmt->fetchAll(), null, 'id');
}

/** The course's students with their work for the assignment (faculty view), with how many files each attached. */
function assignment_roster(array $assignment): array
{
    $stmt = db()->prepare(
        "SELECT u.id AS student_id, u.first_name, u.last_name, u.id_number, u.email, u.picture_url,
                s.id, s.body, s.turned_in_at, s.score, s.feedback, s.returned_at,
                (SELECT COUNT(*) FROM submission_files f WHERE f.submission_id = s.id) AS file_count
         FROM enrollments en
         JOIN users u ON u.id = en.student_id
         LEFT JOIN submissions s ON s.assignment_id = ? AND s.student_id = u.id
         WHERE en.course_id = ?
         ORDER BY u.last_name, u.first_name"
    );
    $stmt->execute([$assignment['id'], $assignment['course_id']]);
    return $stmt->fetchAll();
}

/** "2.4 MB" */
function format_bytes(int $bytes): string
{
    return $bytes >= 1048576 ? round($bytes / 1048576, 1) . ' MB' : max(1, (int) round($bytes / 1024)) . ' KB';
}

/** Font Awesome icon for a file, by its type. */
function file_icon(string $name): string
{
    return match (strtolower(pathinfo($name, PATHINFO_EXTENSION))) {
        'pdf' => 'fa-file-pdf',
        'doc', 'docx', 'odt' => 'fa-file-word',
        'xls', 'xlsx', 'csv' => 'fa-file-excel',
        'ppt', 'pptx' => 'fa-file-powerpoint',
        'jpg', 'jpeg', 'png', 'gif', 'webp' => 'fa-file-image',
        'zip', 'rar', '7z' => 'fa-file-zipper',
        'txt', 'md' => 'fa-file-lines',
        default => 'fa-file-code',
    };
}

/** A link that opens (pictures, PDFs) or downloads an attached file; only its student and their faculty can. */
function file_link(array $file): string
{
    return site_root() . 'api/submissions.php?action=file&id=' . (int) $file['id'];
}

/** Pictures and PDFs open in the browser; everything else is downloaded. */
function submission_file_inline(string $name): bool
{
    return in_array(strtolower(pathinfo($name, PATHINFO_EXTENSION)), ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'], true);
}

/** Text with line breaks kept and http(s) addresses turned into links; every piece is escaped. */
function linkify(string $text): string
{
    $parts = preg_split('~(https?://[^\s<>"\']*[^\s<>"\'.,;:!?)\]])~i', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    $html = '';
    foreach ($parts as $i => $part) {
        // odd parts are the addresses
        $html .= $i % 2 ? '<a href="' . e($part) . '" target="_blank" rel="noopener noreferrer">' . e($part) . '</a>' : e($part);
    }
    return nl2br($html);
}

/**
 * Deletes from the disk the uploaded files of the submissions matching $where (SQL on submissions "s" and
 * assignments "a"), before the rows go away with an assignment, a course or a student account.
 */
function delete_submission_files(string $where, array $params): void
{
    $stmt = db()->prepare(
        "SELECT f.stored_name FROM submission_files f
         JOIN submissions s ON s.id = f.submission_id
         JOIN assignments a ON a.id = s.assignment_id
         WHERE {$where}"
    );
    $stmt->execute($params);
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $name) {
        $path = submission_dir() . '/' . basename($name);
        if (is_file($path)) {
            unlink($path);
        }
    }
}

/* ---------------------------------------------------------------- the Assignments list */

/** "Due in 3 days" / "Due today" / "Past due" badge for an assignment. */
function due_badge(string $dueDate): array
{
    $days = (int) round((strtotime($dueDate) - strtotime(date('Y-m-d'))) / 86400);
    return match (true) {
        $days < 0 => ['badge-danger', 'Past due'],
        $days === 0 => ['badge-warning', 'Due today'],
        $days === 1 => ['badge-warning', 'Due tomorrow'],
        $days <= 3 => ['badge-warning', "Due in {$days} days"],
        default => ['badge-primary', "Due in {$days} days"],
    };
}

/** "Fri, Oct 23, 2026 at 11:59 PM" */
function format_due(array $assignment): string
{
    return format_date($assignment['due_date'], 'D, M j, Y') . ($assignment['due_time'] ? ' at ' . date('g:i A', strtotime($assignment['due_time'])) : '');
}

/**
 * Assignments, each opening its own page (instructions and the students' work). Students see where their work
 * is (assigned, missing, turned in, returned with the score); faculty see how many students turned it in.
 */
function assignment_list(array $user, array $items, bool $compact = false, string $emptyTitle = 'No assignments yet',
                         string $emptyText = 'Assignments from your courses will appear here.'): void
{
    if (!$items) {
        app_empty('fa-clipboard-list', $emptyTitle, $emptyText);
        return;
    }
    $ids = array_map('intval', array_column($items, 'id'));
    $work = $user['role'] === 'student' ? student_submissions((int) $user['id'], $ids) : [];
    $counts = $user['role'] === 'faculty' ? turned_in_counts($ids) : [];
    ?>
                <div class="content-list<?= $compact ? ' compact' : '' ?>">
                    <?php foreach ($items as $item):
                        $href = site_root() . 'pages/academics/assignment.php?id=' . (int) $item['id'];
                        $points = $item['points'] !== null ? (int) $item['points'] : null;
                        if ($user['role'] === 'student') {
                            $submission = $work[$item['id']] ?? null;
                            [$state, $label, $badge] = submission_status($item, $submission);
                            // still to do and not late yet: say when it's due instead
                            [$badge, $label] = $state === 'assigned' ? due_badge($item['due_date']) : [$badge, $label];
                            $score = format_score($submission, $points);
                            $label .= $state === 'returned' && $score !== '' ? ': ' . $score : '';
                        } else {
                            [$badge, $label] = due_badge($item['due_date']);
                        }
                    ?>
                    <article class="content-item" data-href="<?= e($href) ?>">
                        <div class="content-item-head">
                            <div>
                                <h3 class="content-item-title"><a class="item-link" href="<?= e($href) ?>"><?= e($item['title']) ?></a></h3>
                                <div class="content-meta">
                                    <span class="badge badge-outline" title="<?= e($item['course_title']) ?>"><?= e(class_name($item, 'course_')) ?></span>
                                    <span><i class="fa-regular fa-calendar"></i> Due <?= e(format_due($item)) ?></span>
                                    <?php if ($points): ?>
                                    <span><i class="fa-solid fa-star-half-stroke"></i> <?= $points ?> points</span>
                                    <?php endif; ?>
                                    <span class="badge <?= e($badge) ?>"><?= e($label) ?></span>
                                    <?php if (isset($counts[$item['id']])): ?>
                                    <span class="badge badge-outline"><i class="fa-solid fa-inbox"></i> <?= (int) $counts[$item['id']]['done'] ?> of <?= (int) $counts[$item['id']]['students'] ?> turned in</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!$compact) content_actions($user, 'assignments', $item, $item['title']); ?>
                        </div>
                        <?php if (!$compact && $item['instructions']): ?>
                        <div class="content-body clamp"><?= nl2br(e($item['instructions'])) ?></div>
                        <?php endif; ?>
                    </article>
                    <?php endforeach; ?>
                </div>
    <?php
}
