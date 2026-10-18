<?php
// One assignment, like Google Classroom: its instructions, and
//  - for a student, "Your work": attach files and/or write an answer, turn it in (or unsubmit to change it),
//    then see the score and private feedback once it's returned;
//  - for the class's faculty, every student's work, to review and return with a score and feedback.
// Opened from the Assignments list. js/submissions.js does the uploading, turning in and returning.
require __DIR__ . '/../../includes/app_shell.php';
require __DIR__ . '/../../includes/academics.php'; // also loads includes/submissions.php
$listPage = 'pages/academics/assignments.php';
$user = require_page_access($listPage);

$assignment = find_assignment($user, (int) ($_GET['id'] ?? 0));
$isStudent = $user['role'] === 'student';

app_page_start($user, $listPage, $assignment ? $assignment['title'] : 'Assignment');
?>
            <a href="assignments.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Assignments</a>
<?php
if (!$assignment) {
    app_empty_card('fa-clipboard-list', 'Assignment', 'fa-circle-question', 'Assignment not found',
        'It may have been removed, or it is for a class you are not in.');
    app_page_end();
    return;
}

$points = $assignment['points'] !== null ? (int) $assignment['points'] : null;
$stmt = db()->prepare("SELECT CONCAT(first_name, ' ', last_name) FROM users WHERE id = ?");
$stmt->execute([$assignment['course_faculty_id']]);
$instructor = $stmt->fetchColumn() ?: null;
$accept = '.' . implode(',.', array_keys(SUBMISSION_TYPES));
?>
            <div class="assignment-layout<?= $isStudent ? '' : ' single' ?>">
                <section class="card assignment-main">
                    <div class="assignment-head">
                        <div class="assignment-icon"><i class="fa-solid fa-clipboard-list"></i></div>
                        <div class="assignment-heading">
                            <h1 class="assignment-title"><?= e($assignment['title']) ?></h1>
                            <div class="assignment-sub">
                                <?= e(class_name($assignment, 'course_') . ' - ' . $assignment['course_title']) ?><?= $instructor ? ' • ' . e($instructor) : '' ?>
                            </div>
                            <div class="assignment-facts">
                                <span><?= $points ? $points . ' points' : 'Not graded' ?></span>
                                <span><i class="fa-regular fa-calendar"></i> Due <?= e(format_due($assignment)) ?></span>
                            </div>
                        </div>
                        <?php if (!$isStudent) content_actions($user, 'assignments', $assignment, $assignment['title']); ?>
                    </div>
                    <div class="assignment-instructions">
                        <?php if ($assignment['instructions']): ?>
                        <?= linkify($assignment['instructions']) ?>
                        <?php else: ?>
                        <span class="muted">No instructions.</span>
                        <?php endif; ?>
                    </div>
                </section>

<?php
if ($isStudent):
    $submission = find_submission((int) $assignment['id'], (int) $user['id']);
    $files = $submission ? submission_files((int) $submission['id']) : [];
    [$state, $label, $badge] = submission_status($assignment, $submission);
    $editable = !$submission || ($submission['turned_in_at'] === null && $submission['returned_at'] === null);
    $score = format_score($submission, $points);
?>
                <aside class="card your-work" id="your-work" data-assignment="<?= (int) $assignment['id'] ?>">
                    <div class="your-work-head">
                        <h2>Your work</h2>
                        <span class="badge <?= e($badge) ?>"><?= e($label) ?></span>
                    </div>

                    <?php if ($state === 'returned'): ?>
                    <div class="your-grade">
                        <span>Grade</span>
                        <strong><?= $score !== '' ? e($score) : 'No score' ?></strong>
                    </div>
                    <?php endif; ?>

                    <ul class="work-files" id="work-files">
                        <?php foreach ($files as $file): ?>
                        <li>
                            <a href="<?= e(file_link($file)) ?>" target="_blank" rel="noopener">
                                <i class="fa-solid <?= e(file_icon($file['original_name'])) ?>"></i>
                                <span class="work-file-name"><?= e($file['original_name']) ?></span>
                                <small><?= e(format_bytes((int) $file['size'])) ?></small>
                            </a>
                            <?php if ($editable): ?>
                            <button type="button" class="work-file-remove" data-file-remove="<?= (int) $file['id'] ?>" aria-label="Remove <?= e($file['original_name']) ?>"><i class="fa-solid fa-xmark"></i></button>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if ($editable): ?>
                    <label class="btn btn-outline work-add">
                        <i class="fa-solid fa-plus"></i> Add files
                        <input type="file" id="work-file-input" multiple accept="<?= e($accept) ?>">
                    </label>
                    <div class="field-note">Up to <?= SUBMISSION_MAX_BYTES / 1048576 ?> MB each: PDF, Word, PowerPoint, Excel, text, pictures, ZIP or code.</div>

                    <label class="form-label work-answer-label" for="work-body">Your answer or links <span class="muted">(optional)</span></label>
                    <textarea class="form-textarea" id="work-body" rows="4" maxlength="<?= SUBMISSION_MAX_TEXT ?>"
                              placeholder="Type your answer or paste a link"><?= e($submission['body'] ?? '') ?></textarea>
                    <div class="field-note work-saved" id="work-saved" aria-live="polite"></div>

                    <button type="button" class="btn btn-primary work-submit" id="work-turn-in" data-title="<?= e($assignment['title']) ?>">
                        <?= $files || ($submission['body'] ?? '') !== '' ? 'Turn in' : 'Mark as done' ?>
                    </button>
                    <?php if ($state === 'missing'): ?>
                    <div class="field-note">It's past due, so it will be marked as turned in late.</div>
                    <?php endif; ?>
                    <?php else: ?>
                    <?php if ($submission['body']): ?>
                    <div class="work-answer"><?= linkify($submission['body']) ?></div>
                    <?php endif; ?>
                    <?php if ($submission['turned_in_at']): ?>
                    <div class="field-note">Turned in <?= e(format_date($submission['turned_in_at'], 'M j, Y g:i A')) ?></div>
                    <?php endif; ?>
                    <?php if ($state !== 'returned'): ?>
                    <button type="button" class="btn btn-outline work-submit" id="work-unsubmit">Unsubmit</button>
                    <div class="field-note">Unsubmit to add or change your work. Turn it in again before your instructor returns it.</div>
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($state === 'returned' && $submission['feedback']): ?>
                    <div class="work-feedback">
                        <h3><i class="fa-regular fa-comment"></i> Private comment from your instructor</h3>
                        <p><?= linkify($submission['feedback']) ?></p>
                    </div>
                    <?php endif; ?>
                </aside>
            </div>
<?php
else:
    $roster = assignment_roster($assignment);
    $stmt = db()->prepare(
        'SELECT f.id, f.submission_id, f.original_name, f.size FROM submission_files f
         JOIN submissions s ON s.id = f.submission_id WHERE s.assignment_id = ? ORDER BY f.id'
    );
    $stmt->execute([$assignment['id']]);
    $filesBySubmission = [];
    foreach ($stmt->fetchAll() as $file) {
        $filesBySubmission[$file['submission_id']][] = $file;
    }
    $tally = ['turned_in' => 0, 'assigned' => 0, 'missing' => 0, 'returned' => 0];
    foreach ($roster as &$row) {
        $row['status'] = submission_status($assignment, $row);
        $tally[$row['status'][0] === 'late' ? 'turned_in' : $row['status'][0]]++;
    }
    unset($row);
?>
            </div>

            <section class="card section-gap">
                <div class="card-header card-header-wrap">
                    <div>
                        <div class="card-title"><i class="fa-solid fa-inbox" style="color: var(--primary);"></i> Student Work</div>
                        <div class="card-subtitle"><?= count($roster) ?> student<?= count($roster) === 1 ? '' : 's' ?> in <?= e(class_name($assignment, 'course_')) ?></div>
                    </div>
                    <div class="work-tally">
                        <span><strong><?= $tally['turned_in'] ?></strong> Turned in</span>
                        <span><strong><?= $tally['assigned'] ?></strong> Assigned</span>
                        <span><strong><?= $tally['missing'] ?></strong> Missing</span>
                        <span><strong><?= $tally['returned'] ?></strong> Returned</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr><th>Student</th><th>Status</th><th class="hide-mobile">Turned in</th><th>Work</th><th>Grade</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php if (!$roster): ?>
                            <tr><td colspan="6"><?php app_empty('fa-user-graduate', 'No students yet', 'Students enrolled in this class will be listed here.'); ?></td></tr>
                            <?php endif; ?>
                            <?php foreach ($roster as $row):
                                [, $label, $badge] = $row['status'];
                                $files = $row['id'] ? ($filesBySubmission[$row['id']] ?? []) : [];
                                $name = $row['first_name'] . ' ' . $row['last_name'];
                            ?>
                            <tr>
                                <td><?php person_cell($row); ?></td>
                                <td><span class="badge <?= e($badge) ?>"><?= e($label) ?></span></td>
                                <td class="muted nowrap hide-mobile"><?= $row['turned_in_at'] ? e(format_date($row['turned_in_at'], 'M j, g:i A')) : '—' ?></td>
                                <td class="muted nowrap">
                                    <?= count($files) ? '<i class="fa-solid fa-paperclip"></i> ' . count($files) : '' ?>
                                    <?= $row['body'] ? ' <i class="fa-solid fa-align-left" title="Wrote an answer"></i>' : '' ?>
                                    <?= !$files && !$row['body'] ? '—' : '' ?>
                                </td>
                                <td class="nowrap"><strong><?= e(format_score($row, $points) ?: '—') ?></strong></td>
                                <td style="text-align: right;">
                                    <button type="button" class="btn btn-outline btn-sm nowrap" data-review="<?= e(json_encode([
                                        'student' => (int) $row['student_id'],
                                        'name' => $name,
                                        'status' => $label,
                                        'turnedIn' => $row['turned_in_at'] ? format_date($row['turned_in_at'], 'M j, Y g:i A') : '',
                                        'score' => $row['score'] !== null ? rtrim(rtrim($row['score'], '0'), '.') : '',
                                        'feedback' => $row['feedback'] ?? '',
                                    ])) ?>">
                                        <i class="fa-solid fa-magnifying-glass"></i> Review
                                    </button>
                                    <template id="review-work-<?= (int) $row['student_id'] ?>">
                                        <?php if ($row['body']): ?>
                                        <div class="work-answer"><?= linkify($row['body']) ?></div>
                                        <?php endif; ?>
                                        <?php if ($files): ?>
                                        <ul class="work-files">
                                            <?php foreach ($files as $file): ?>
                                            <li>
                                                <a href="<?= e(file_link($file)) ?>" target="_blank" rel="noopener">
                                                    <i class="fa-solid <?= e(file_icon($file['original_name'])) ?>"></i>
                                                    <span class="work-file-name"><?= e($file['original_name']) ?></span>
                                                    <small><?= e(format_bytes((int) $file['size'])) ?></small>
                                                </a>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <?php endif; ?>
                                        <?php if (!$row['body'] && !$files): ?>
                                        <p class="muted"><?= $row['turned_in_at'] ? 'Marked as done with nothing attached.' : 'Nothing turned in yet.' ?></p>
                                        <?php endif; ?>
                                    </template>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="modal-overlay" id="review-modal">
                <div class="modal" role="dialog" aria-modal="true" aria-labelledby="review-modal-title" style="max-width: 560px;">
                    <div class="modal-header">
                        <div>
                            <h3 class="modal-title" id="review-modal-title">Student work</h3>
                            <div class="muted" id="review-modal-status"></div>
                        </div>
                        <button type="button" class="modal-close-btn" data-modal-close aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <form novalidate>
                        <input type="hidden" name="assignment_id" value="<?= (int) $assignment['id'] ?>">
                        <input type="hidden" name="student_id">
                        <div class="modal-body">
                            <div class="review-work" id="review-work"></div>
                            <?php if ($points): ?>
                            <div class="form-group">
                                <label class="form-label" for="review-score">Score</label>
                                <div class="review-score">
                                    <input class="form-control" id="review-score" name="score" type="number" min="0" max="<?= $points ?>" step="0.01" placeholder="0">
                                    <span>/ <?= $points ?></span>
                                </div>
                            </div>
                            <?php else: ?>
                            <input type="hidden" name="score" value="">
                            <?php endif; ?>
                            <div class="form-group">
                                <label class="form-label" for="review-feedback">Private comment <span class="muted">(only this student sees it)</span></label>
                                <textarea class="form-textarea" id="review-feedback" name="feedback" rows="3" maxlength="5000"></textarea>
                            </div>
                            <div class="form-error" hidden></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-reply"></i> Return</button>
                        </div>
                    </form>
                </div>
            </div>
<?php
    content_form($user, 'assignments');
endif;
app_page_end(['js/submissions.js?v=1']);
