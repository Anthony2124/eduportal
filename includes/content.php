<?php
/*
 * Content managed from the portal with Add / Edit / Remove buttons:
 * announcements, events, scholarships, courses, assignments and exams.
 *
 * Each type is described once in CONTENT_TYPES (fields + who can manage it). The same code then builds
 * the form, checks the input (api/content.php) and shows the lists on the pages.
 */
require_once __DIR__ . '/../api/bootstrap.php';

// Days a class can meet on: stored as e.g. "Mon,Wed,Fri", shown as "MWF"
const CLASS_DAYS = ['Mon' => 'M', 'Tue' => 'T', 'Wed' => 'W', 'Thu' => 'Th', 'Fri' => 'F', 'Sat' => 'S'];

const CONTENT_TYPES = [
    'announcements' => [
        'table' => 'announcements',
        'label' => 'announcement',
        'owner' => 'author_id',
        'manage' => ['admin', 'faculty'],
        'own_only' => ['faculty'],      // faculty can only edit/remove announcements they posted
        'order' => 'a.created_at DESC, a.id DESC',
        'fields' => [
            'title' => ['label' => 'Title', 'type' => 'text', 'required' => true, 'max' => 150],
            'body' => ['label' => 'Message', 'type' => 'textarea', 'required' => true, 'max' => 5000],
        ],
    ],
    'events' => [
        'table' => 'events',
        'label' => 'event',
        'owner' => 'created_by',
        'manage' => ['admin'],
        'own_only' => [],
        'order' => 'a.event_date ASC, a.start_time ASC, a.id ASC',
        'fields' => [
            'title' => ['label' => 'Event name', 'type' => 'text', 'required' => true, 'max' => 150],
            'event_date' => ['label' => 'Date', 'type' => 'date', 'required' => true],
            'start_time' => ['label' => 'Starts', 'type' => 'time'],
            'end_time' => ['label' => 'Ends', 'type' => 'time'],
            'location' => ['label' => 'Location', 'type' => 'text', 'max' => 150],
            'description' => ['label' => 'Details', 'type' => 'textarea', 'max' => 3000],
        ],
    ],
    'scholarships' => [
        'table' => 'scholarships',
        'label' => 'scholarship',
        'owner' => 'created_by',
        'manage' => ['admin'],
        'own_only' => [],
        'order' => 'a.deadline IS NULL, a.deadline ASC, a.name ASC',
        'fields' => [
            'name' => ['label' => 'Scholarship name', 'type' => 'text', 'required' => true, 'max' => 150],
            'sponsor' => ['label' => 'Sponsor', 'type' => 'text', 'max' => 150],
            'amount' => ['label' => 'Amount / benefit', 'type' => 'text', 'max' => 80, 'placeholder' => 'e.g. PHP 15,000 per semester'],
            'deadline' => ['label' => 'Application deadline', 'type' => 'date'],
            'requirements' => ['label' => 'Requirements', 'type' => 'textarea', 'max' => 3000],
        ],
    ],
    // A course offered to a block, with its days, time and room. Leave Faculty empty to let faculty pick it
    // ("Teach this class" on the Courses page). The same course code can be offered to several blocks.
    'courses' => [
        'table' => 'courses',
        'label' => 'course',
        'owner' => 'created_by',
        'manage' => ['admin'],
        'own_only' => [],
        'remove_note' => 'Its enrollments, assignments, exams and grades are removed too.',
        'order' => 'a.code ASC, a.block ASC',
        'fields' => [
            'code' => ['label' => 'Course code', 'type' => 'text', 'required' => true, 'max' => 20, 'upper' => true, 'unique_with' => 'block', 'placeholder' => 'e.g. CS 201'],
            'title' => ['label' => 'Course title', 'type' => 'text', 'required' => true, 'max' => 150],
            'units' => ['label' => 'Units', 'type' => 'number', 'required' => true, 'min' => 1, 'max' => 10],
            'block' => ['label' => 'Block', 'type' => 'text', 'max' => 30, 'upper' => true, 'placeholder' => 'e.g. BSIT 2-A'],
            'days' => ['label' => 'Days', 'type' => 'days'],
            'start_time' => ['label' => 'Class starts', 'type' => 'time'],
            'end_time' => ['label' => 'Class ends', 'type' => 'time'],
            'room' => ['label' => 'Room', 'type' => 'text', 'max' => 60],
            'faculty_id' => ['label' => 'Faculty', 'type' => 'faculty'],
        ],
    ],
    // Course-scoped types: faculty manage them only for courses they teach; students see the ones
    // for courses they're enrolled in
    'assignments' => [
        'table' => 'assignments',
        'label' => 'assignment',
        'owner' => 'created_by',
        'manage' => ['faculty'],
        'own_only' => [],
        'course_scoped' => true,
        'order' => 'a.due_date ASC, a.due_time ASC, a.id ASC',
        'fields' => [
            'course_id' => ['label' => 'Course', 'type' => 'course', 'required' => true],
            'title' => ['label' => 'Title', 'type' => 'text', 'required' => true, 'max' => 150],
            'due_date' => ['label' => 'Due date', 'type' => 'date', 'required' => true],
            'due_time' => ['label' => 'Due time', 'type' => 'time'],
            'instructions' => ['label' => 'Instructions', 'type' => 'textarea', 'max' => 5000],
        ],
    ],
    'exams' => [
        'table' => 'exams',
        'label' => 'exam',
        'owner' => 'created_by',
        'manage' => ['faculty'],
        'own_only' => [],
        'course_scoped' => true,
        'order' => 'a.exam_date ASC, a.start_time ASC, a.id ASC',
        'fields' => [
            'course_id' => ['label' => 'Course', 'type' => 'course', 'required' => true],
            'title' => ['label' => 'Exam name', 'type' => 'text', 'required' => true, 'max' => 150, 'placeholder' => 'e.g. Midterm Exam'],
            'exam_type' => ['label' => 'Type', 'type' => 'select', 'required' => true,
                'options' => ['Quiz', 'Prelim', 'Midterm', 'Pre-final', 'Final', 'Other']],
            'exam_date' => ['label' => 'Date', 'type' => 'date', 'required' => true],
            'start_time' => ['label' => 'Starts', 'type' => 'time'],
            'end_time' => ['label' => 'Ends', 'type' => 'time'],
            'room' => ['label' => 'Room', 'type' => 'text', 'max' => 60],
        ],
    ],
];

/** Admins can work with every (existing) course; faculty only with courses assigned to them. */
function teaches_course(array $user, int $courseId): bool
{
    if ($user['role'] === 'admin') {
        $stmt = db()->prepare('SELECT 1 FROM courses WHERE id = ?');
        $stmt->execute([$courseId]);
    } elseif ($user['role'] === 'faculty') {
        $stmt = db()->prepare('SELECT 1 FROM courses WHERE id = ? AND faculty_id = ?');
        $stmt->execute([$courseId, $user['id']]);
    } else {
        return false;
    }
    return (bool) $stmt->fetchColumn();
}

/** Courses this user can manage things for (admin: all, faculty: the ones they teach). */
function managed_courses(array $user): array
{
    if ($user['role'] === 'admin') {
        return content_items('courses');
    }
    return $user['role'] === 'faculty' ? content_items('courses', 'a.faculty_id = ?', [$user['id']]) : [];
}

/**
 * SQL filter (on courses alias "c") for course-scoped lists: admins see all, faculty their courses,
 * students the courses they're enrolled in. Returns [where, params].
 */
function course_scope(array $user): array
{
    return match ($user['role']) {
        'admin' => ['', []],
        'faculty' => ['c.faculty_id = ?', [$user['id']]],
        default => ['c.id IN (SELECT course_id FROM enrollments WHERE student_id = ?)', [$user['id']]],
    };
}

/** Combines a course_scope() filter with an extra condition. */
function scoped(array $user, string $extra = '', array $extraParams = []): array
{
    [$where, $params] = course_scope($user);
    $parts = array_filter([$where, $extra]);
    return [implode(' AND ', $parts), [...$params, ...$extraParams]];
}

/** Can this user add items of this type? */
function can_manage_content(array $user, string $type): bool
{
    return in_array($user['role'], CONTENT_TYPES[$type]['manage'], true);
}

/** Can this user edit / remove this particular item? */
function can_edit_content(array $user, string $type, array $item): bool
{
    $def = CONTENT_TYPES[$type];
    if (!can_manage_content($user, $type)) {
        return false;
    }
    if (!empty($def['course_scoped'])) {
        return teaches_course($user, (int) $item['course_id']);
    }
    if (in_array($user['role'], $def['own_only'], true)) {
        return (int) ($item[$def['owner']] ?? 0) === (int) $user['id'];
    }
    return true;
}

/**
 * Items of a type, with the poster's / faculty member's name. $where is extra SQL on alias "a"
 * (e.g. 'a.event_date >= CURDATE()'), $params its values.
 */
function content_items(string $type, string $where = '', array $params = [], int $limit = 0): array
{
    $def = CONTENT_TYPES[$type];
    if ($type === 'courses') {
        // faculty name + how many students are enrolled
        $select = "a.*, CONCAT(u.first_name, ' ', u.last_name) AS person_name,
                   (SELECT COUNT(*) FROM enrollments en WHERE en.course_id = a.id) AS enrolled";
        $from = "courses a LEFT JOIN users u ON u.id = a.faculty_id";
    } elseif (!empty($def['course_scoped'])) {
        // course code/title (alias c, also used by course_scope())
        $select = 'a.*, c.code AS course_code, c.block AS course_block, c.title AS course_title, c.faculty_id AS course_faculty_id';
        $from = "{$def['table']} a JOIN courses c ON c.id = a.course_id";
    } else {
        $select = "a.*, CONCAT(u.first_name, ' ', u.last_name) AS person_name";
        $from = "{$def['table']} a LEFT JOIN users u ON u.id = a.{$def['owner']}";
    }
    $sql = "SELECT {$select} FROM {$from}"
        . ($where !== '' ? " WHERE {$where}" : '')
        . " ORDER BY {$def['order']}"
        . ($limit > 0 ? ' LIMIT ' . $limit : '');
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/** Faculty accounts, for the course form's Faculty dropdown. */
function faculty_options(): array
{
    return db()->query(
        "SELECT id, CONCAT(first_name, ' ', last_name, ' (', department, ')') AS name FROM users WHERE role = 'faculty' ORDER BY last_name, first_name"
    )->fetchAll();
}

/**
 * Checks submitted values against the type's fields. Returns [column => value] ready to save,
 * or stops with a 422 error naming the field.
 */
function validate_content(array $user, string $type, array $input): array
{
    $values = [];
    foreach (CONTENT_TYPES[$type]['fields'] as $name => $field) {
        $raw = $input[$name] ?? '';
        if ($field['type'] === 'days' && is_array($raw)) {
            $raw = implode(',', array_filter($raw, 'is_string')); // checkboxes arrive as ["Mon", "Wed"]
        }
        $value = is_string($raw) ? trim($raw) : (is_int($raw) ? (string) $raw : '');
        $label = $field['label'];

        if ($value === '') {
            if (!empty($field['required'])) {
                fail("{$label} is required.", 422, ['field' => $name]);
            }
            $values[$name] = null;
            continue;
        }

        switch ($field['type']) {
            case 'date':
                $date = DateTime::createFromFormat('!Y-m-d', $value);
                if (!$date || $date->format('Y-m-d') !== $value) {
                    fail("{$label} must be a valid date.", 422, ['field' => $name]);
                }
                break;
            case 'time':
                if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', substr($value, 0, 5))) {
                    fail("{$label} must be a valid time.", 422, ['field' => $name]);
                }
                $value = substr($value, 0, 5);
                break;
            case 'number':
                if (!preg_match('/^\d+$/', $value) || (int) $value < $field['min'] || (int) $value > $field['max']) {
                    fail("{$label} must be a whole number from {$field['min']} to {$field['max']}.", 422, ['field' => $name]);
                }
                $value = (int) $value;
                break;
            case 'faculty':
                $stmt = db()->prepare("SELECT 1 FROM users WHERE id = ? AND role = 'faculty'");
                $stmt->execute([(int) $value]);
                if (!$stmt->fetchColumn()) {
                    fail('Choose a faculty member from the list.', 422, ['field' => $name]);
                }
                $value = (int) $value;
                break;
            case 'course':
                // faculty can only pick courses they teach
                if (!teaches_course($user, (int) $value)) {
                    fail('Choose one of your courses from the list.', 422, ['field' => $name]);
                }
                $value = (int) $value;
                break;
            case 'select':
                if (!in_array($value, $field['options'], true)) {
                    fail("Choose a {$label} from the list.", 422, ['field' => $name]);
                }
                break;
            case 'days':
                $picked = array_map('trim', explode(',', $value));
                if (array_diff($picked, array_keys(CLASS_DAYS))) {
                    fail("Choose the {$label} from the list.", 422, ['field' => $name]);
                }
                $value = implode(',', array_intersect(array_keys(CLASS_DAYS), $picked)); // in week order
                break;
            default: // text, textarea
                if (!empty($field['upper'])) {
                    $value = mb_strtoupper($value);
                }
                if (mb_strlen($value) > $field['max']) {
                    fail("{$label} can be at most {$field['max']} characters.", 422, ['field' => $name]);
                }
        }
        $values[$name] = $value;
    }

    if (in_array($type, ['events', 'exams'], true) && $values['start_time'] && $values['end_time'] && $values['end_time'] <= $values['start_time']) {
        fail('It must end after it starts.', 422, ['field' => 'end_time']);
    }
    if ($type === 'courses') {
        check_class_schedule($values, (int) ($input['id'] ?? 0));
    }
    return $values;
}

/**
 * A class's days, start and end time go together (or are all left empty), it must end after it starts,
 * and it can't overlap another class of the same instructor or the same block.
 */
function check_class_schedule(array $class, int $courseId): void
{
    $given = array_filter([$class['days'], $class['start_time'], $class['end_time']], fn($part) => $part !== null);
    if (!$given) {
        return;
    }
    if (count($given) < 3) {
        $missing = $class['days'] === null ? 'days' : ($class['start_time'] === null ? 'start_time' : 'end_time');
        fail('Pick the class days and its start and end time (or leave all three empty).', 422, ['field' => $missing]);
    }
    if ($class['end_time'] <= $class['start_time']) {
        fail('The class must end after it starts.', 422, ['field' => 'end_time']);
    }
    if ($clash = schedule_clash('faculty_id', $class['faculty_id'], $class, $courseId)) {
        fail('This instructor already teaches ' . class_name($clash) . ' at that time (' . class_schedule($clash) . ').', 422, ['field' => 'faculty_id']);
    }
    if ($clash = schedule_clash('block', $class['block'], $class, $courseId)) {
        fail("{$class['block']} already has " . class_name($clash) . ' at that time (' . class_schedule($clash) . ').', 422, ['field' => 'start_time']);
    }
}

/**
 * Another class, among those with $column = $value (the same instructor or the same block), that meets on
 * one of the same days at an overlapping time - or null.
 */
function schedule_clash(string $column, mixed $value, array $class, int $exceptId = 0): ?array
{
    if ($value === null || empty($class['days']) || empty($class['start_time']) || empty($class['end_time'])) {
        return null;
    }
    $stmt = db()->prepare("SELECT * FROM courses WHERE {$column} = ? AND id <> ? AND days IS NOT NULL AND start_time < ? AND end_time > ?");
    $stmt->execute([$value, $exceptId, $class['end_time'], $class['start_time']]);
    foreach ($stmt->fetchAll() as $other) {
        if (array_intersect(explode(',', $other['days']), explode(',', $class['days']))) {
            return $other;
        }
    }
    return null;
}

/** "IT 101 (BSIT 2-A)": the course code and block, which tell classes of the same course apart. */
function class_name(array $course, string $prefix = ''): string
{
    $block = $course[$prefix . 'block'] ?? null;
    return $course[$prefix . 'code'] . ($block ? " ({$block})" : '');
}

/**
 * "MWF 8:00 AM - 9:00 AM" from a class's days and times. Classes from before days and times existed
 * show their old free-text schedule.
 */
function class_schedule(array $course): string
{
    if (!empty($course['days']) && !empty($course['start_time']) && !empty($course['end_time'])) {
        $days = implode('', array_map(fn($day) => CLASS_DAYS[$day] ?? $day, explode(',', $course['days'])));
        return $days . ' ' . format_time_range($course['start_time'], $course['end_time']);
    }
    return trim((string) ($course['schedule'] ?? ''));
}

/* ---------------------------------------------------------------- display helpers */

function format_date(?string $date, string $format = 'M j, Y'): string
{
    return $date ? date($format, strtotime($date)) : '';
}

function format_time_range(?string $start, ?string $end): string
{
    $fmt = fn(string $time) => date('g:i A', strtotime($time));
    if ($start && $end) {
        return $fmt($start) . ' - ' . $fmt($end);
    }
    return $start ? $fmt($start) : ($end ? 'Until ' . $fmt($end) : 'All day');
}

/** Edit / Remove buttons for one item (only for people allowed to change it). */
function content_actions(array $user, string $type, array $item, string $name): void
{
    if (!can_edit_content($user, $type, $item)) {
        return;
    }
    $fields = CONTENT_TYPES[$type]['fields'];
    $data = array_intersect_key($item, $fields + ['id' => true]);
    foreach ($fields as $field => $spec) {
        if ($spec['type'] === 'time' && isset($data[$field])) {
            $data[$field] = substr((string) $data[$field], 0, 5); // "14:30:00" -> "14:30" for the time box
        }
    }
    ?>
                            <div class="content-actions">
                                <button type="button" class="btn btn-outline btn-sm" data-content-edit="<?= e($type) ?>" data-item="<?= e(json_encode($data)) ?>">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                                <button type="button" class="btn btn-outline btn-sm content-remove" data-content-delete="<?= e($type) ?>" data-id="<?= (int) $item['id'] ?>" data-name="<?= e($name) ?>" data-note="<?= e(CONTENT_TYPES[$type]['remove_note'] ?? '') ?>">
                                    <i class="fa-regular fa-trash-can"></i> Remove
                                </button>
                            </div>
    <?php
}

/** Card header with an "Add ..." button for people who can manage this type. */
function content_card_header(array $user, string $type, string $icon, string $title, string $subtitle = ''): void
{
    ?>
                <div class="card-header">
                    <div>
                        <div class="card-title"><i class="fa-solid <?= e($icon) ?>" style="color: var(--primary);"></i> <?= e($title) ?></div>
                        <?php if ($subtitle !== ''): ?>
                        <div class="card-subtitle"><?= e($subtitle) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if (can_manage_content($user, $type)): ?>
                    <button type="button" class="btn btn-primary btn-sm" data-content-add="<?= e($type) ?>">
                        <i class="fa-solid fa-plus"></i> Add <?= e(CONTENT_TYPES[$type]['label']) ?>
                    </button>
                    <?php endif; ?>
                </div>
    <?php
}

/** The Add / Edit form (a dialog) for a type. Put it once on any page that has Add or Edit buttons for it. */
function content_form(array $user, string $type): void
{
    if (!can_manage_content($user, $type)) {
        return;
    }
    $def = CONTENT_TYPES[$type];
    $types = array_column($def['fields'], 'type');
    $faculty = in_array('faculty', $types, true) ? faculty_options() : [];
    $courses = in_array('course', $types, true) ? managed_courses($user) : [];
    ?>
            <div class="modal-overlay" id="content-modal-<?= e($type) ?>" data-content-modal="<?= e($type) ?>">
                <div class="modal" role="dialog" aria-modal="true" aria-labelledby="content-modal-title-<?= e($type) ?>">
                    <div class="modal-header">
                        <h3 class="modal-title" id="content-modal-title-<?= e($type) ?>" data-add-title="Add <?= e($def['label']) ?>" data-edit-title="Edit <?= e($def['label']) ?>">Add <?= e($def['label']) ?></h3>
                        <button type="button" class="modal-close-btn" data-modal-close aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <form class="content-form" novalidate>
                        <input type="hidden" name="id" value="">
                        <div class="modal-body">
                            <?php foreach ($def['fields'] as $name => $field):
                                $id = "cf-{$type}-{$name}";
                                $req = !empty($field['required']);
                            ?>
                            <div class="form-group">
                                <label class="form-label"<?= $field['type'] !== 'days' ? ' for="' . e($id) . '"' : '' ?>><?= e($field['label']) ?><?= $req ? ' <span class="required">*</span>' : '' ?></label>
                                <?php if ($field['type'] === 'days'): ?>
                                <div class="day-picker" id="<?= e($id) ?>" role="group" aria-label="<?= e($field['label']) ?>">
                                    <?php foreach (array_keys(CLASS_DAYS) as $day): ?>
                                    <label><input type="checkbox" name="<?= e($name) ?>" value="<?= e($day) ?>"> <?= e($day) ?></label>
                                    <?php endforeach; ?>
                                </div>
                                <?php elseif ($field['type'] === 'textarea'): ?>
                                <textarea class="form-textarea" id="<?= e($id) ?>" name="<?= e($name) ?>" maxlength="<?= (int) $field['max'] ?>" rows="4"></textarea>
                                <?php elseif ($field['type'] === 'faculty'): ?>
                                <select class="form-select" id="<?= e($id) ?>" name="<?= e($name) ?>">
                                    <option value="">Not assigned yet</option>
                                    <?php foreach ($faculty as $option): ?>
                                    <option value="<?= (int) $option['id'] ?>"><?= e($option['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!$faculty): ?>
                                <div class="field-note">No faculty accounts yet. Add them on the Admin dashboard first.</div>
                                <?php endif; ?>
                                <?php elseif ($field['type'] === 'course'): ?>
                                <select class="form-select" id="<?= e($id) ?>" name="<?= e($name) ?>">
                                    <option value="">Select a course</option>
                                    <?php foreach ($courses as $course): ?>
                                    <option value="<?= (int) $course['id'] ?>"><?= e(class_name($course) . ' - ' . $course['title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!$courses): ?>
                                <div class="field-note"><?= $user['role'] === 'faculty' ? "You don't teach any classes yet. Pick yours on the Courses page." : 'No courses yet. Add one on the Courses page first.' ?></div>
                                <?php endif; ?>
                                <?php elseif ($field['type'] === 'select'): ?>
                                <select class="form-select" id="<?= e($id) ?>" name="<?= e($name) ?>">
                                    <option value="">Select</option>
                                    <?php foreach ($field['options'] as $option): ?>
                                    <option><?= e($option) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php else: ?>
                                <input class="form-control" id="<?= e($id) ?>" name="<?= e($name) ?>"
                                       type="<?= e($field['type']) ?>"
                                       <?= isset($field['max']) && $field['type'] === 'text' ? 'maxlength="' . (int) $field['max'] . '"' : '' ?>
                                       <?= $field['type'] === 'number' ? 'min="' . (int) $field['min'] . '" max="' . (int) $field['max'] . '"' : '' ?>
                                       <?= isset($field['placeholder']) ? 'placeholder="' . e($field['placeholder']) . '"' : '' ?>>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
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

/* ---------------------------------------------------------------- lists shown on the pages */

function announcement_list(array $user, array $items, bool $compact = false): void
{
    if (!$items) {
        app_empty('fa-bullhorn', 'No announcements yet', 'News and notices from your school will appear here.');
        return;
    }
    ?>
                <div class="content-list<?= $compact ? ' compact' : '' ?>">
                    <?php foreach ($items as $item): ?>
                    <article class="content-item">
                        <div class="content-item-head">
                            <div>
                                <h3 class="content-item-title"><?= e($item['title']) ?></h3>
                                <div class="content-meta">
                                    <span><i class="fa-regular fa-user"></i> <?= e($item['person_name'] ?? 'Removed account') ?></span>
                                    <span><i class="fa-regular fa-clock"></i> <?= e(format_date($item['created_at'], 'M j, Y g:i A')) ?><?= $item['updated_at'] ? ' (edited)' : '' ?></span>
                                </div>
                            </div>
                            <?php if (!$compact) content_actions($user, 'announcements', $item, $item['title']); ?>
                        </div>
                        <div class="content-body<?= $compact ? ' clamp' : '' ?>"><?= nl2br(e($item['body'])) ?></div>
                    </article>
                    <?php endforeach; ?>
                </div>
    <?php
}

function event_list(array $user, array $items, bool $compact = false, string $emptyTitle = 'No upcoming events'): void
{
    if (!$items) {
        app_empty('fa-calendar-xmark', $emptyTitle, 'School events will appear here.');
        return;
    }
    ?>
                <div class="content-list<?= $compact ? ' compact' : '' ?>">
                    <?php foreach ($items as $item): ?>
                    <article class="content-item event-item">
                        <div class="date-badge">
                            <span><?= e(format_date($item['event_date'], 'M')) ?></span>
                            <strong><?= e(format_date($item['event_date'], 'j')) ?></strong>
                        </div>
                        <div class="content-item-main">
                            <div class="content-item-head">
                                <div>
                                    <h3 class="content-item-title"><?= e($item['title']) ?></h3>
                                    <div class="content-meta">
                                        <span><i class="fa-regular fa-calendar"></i> <?= e(format_date($item['event_date'], 'l, M j, Y')) ?></span>
                                        <span><i class="fa-regular fa-clock"></i> <?= e(format_time_range($item['start_time'], $item['end_time'])) ?></span>
                                        <?php if ($item['location']): ?>
                                        <span><i class="fa-solid fa-location-dot"></i> <?= e($item['location']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (!$compact) content_actions($user, 'events', $item, $item['title']); ?>
                            </div>
                            <?php if (!$compact && $item['description']): ?>
                            <div class="content-body"><?= nl2br(e($item['description'])) ?></div>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
    <?php
}

function scholarship_list(array $user, array $items): void
{
    if (!$items) {
        app_empty('fa-graduation-cap', 'No scholarships posted', 'Open scholarships and grants will be listed here.');
        return;
    }
    $today = date('Y-m-d');
    ?>
                <div class="content-list">
                    <?php foreach ($items as $item):
                        $closed = $item['deadline'] && $item['deadline'] < $today;
                    ?>
                    <article class="content-item">
                        <div class="content-item-head">
                            <div>
                                <h3 class="content-item-title"><?= e($item['name']) ?></h3>
                                <div class="content-meta">
                                    <?php if ($item['sponsor']): ?>
                                    <span><i class="fa-solid fa-building-columns"></i> <?= e($item['sponsor']) ?></span>
                                    <?php endif; ?>
                                    <?php if ($item['amount']): ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-coins"></i> <?= e($item['amount']) ?></span>
                                    <?php endif; ?>
                                    <?php if ($item['deadline']): ?>
                                    <span class="badge <?= $closed ? 'badge-danger' : 'badge-warning' ?>">
                                        <i class="fa-regular fa-calendar"></i> <?= $closed ? 'Closed' : 'Apply by' ?> <?= e(format_date($item['deadline'])) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php content_actions($user, 'scholarships', $item, $item['name']); ?>
                        </div>
                        <?php if ($item['requirements']): ?>
                        <div class="content-body"><strong>Requirements:</strong><br><?= nl2br(e($item['requirements'])) ?></div>
                        <?php endif; ?>
                    </article>
                    <?php endforeach; ?>
                </div>
    <?php
}

/** Faculty buttons on a class: "Teach this class" when it has no instructor (unless it clashes), "Drop" on their own. */
function teach_actions(array $user, array $course): void
{
    $name = class_name($course) . ' - ' . $course['title'];
    $details = implode(' • ', array_filter([
        $course['block'] ? 'Block ' . $course['block'] : null,
        class_schedule($course) ?: 'Schedule to be announced',
        $course['room'],
    ]));
    if ($course['faculty_id'] === null) {
        $clash = schedule_clash('faculty_id', (int) $user['id'], $course, (int) $course['id']);
        if ($clash) {
            ?>
                                    <div class="clash-note"><i class="fa-solid fa-triangle-exclamation"></i> Clashes with your <?= e(class_name($clash)) ?></div>
            <?php
            return;
        }
        ?>
                                    <button type="button" class="btn btn-primary btn-sm nowrap" data-class-teach="<?= (int) $course['id'] ?>" data-name="<?= e($name) ?>" data-details="<?= e($details) ?>">
                                        <i class="fa-solid fa-chalkboard-user"></i> Teach this class
                                    </button>
        <?php
    } elseif ((int) $course['faculty_id'] === (int) $user['id']) {
        ?>
                                    <button type="button" class="btn btn-outline btn-sm content-remove nowrap" data-class-drop="<?= (int) $course['id'] ?>" data-name="<?= e($name) ?>">
                                        <i class="fa-solid fa-right-from-bracket"></i> Drop
                                    </button>
        <?php
    }
}

/** Table of classes. With $showActions: Edit / Remove for the admin, Teach / Drop for faculty. */
function course_table(array $user, array $items, bool $showActions, string $emptyTitle, string $emptyText): void
{
    $showActions = $showActions && (can_manage_content($user, 'courses') || $user['role'] === 'faculty');
    // Faculty picking classes: every row is either theirs or open, so the Faculty column says nothing new
    $showFaculty = !($showActions && $user['role'] === 'faculty');
    $columns = ['Code', 'Course', 'Block', 'Units', 'Schedule', 'Room', ...($showFaculty ? ['Faculty'] : []), 'Students'];
    $orDash = fn(?string $value, string $dash = '—') => ($value ?? '') !== '' ? e($value) : $dash;
    ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <?php foreach ($columns as $column): ?>
                                <th><?= e($column) ?></th>
                                <?php endforeach; ?>
                                <?php if ($showActions): ?><th></th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$items): ?>
                            <tr><td colspan="<?= count($columns) + ($showActions ? 1 : 0) ?>"><?php app_empty('fa-book', $emptyTitle, $emptyText); ?></td></tr>
                            <?php endif; ?>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="nowrap"><strong><?= e($item['code']) ?></strong></td>
                                <td><?= e($item['title']) ?></td>
                                <td class="nowrap"><?= $orDash($item['block']) ?></td>
                                <td><?= (int) $item['units'] ?></td>
                                <td class="muted nowrap"><?= $orDash(class_schedule($item)) ?></td>
                                <td class="muted"><?= $orDash($item['room']) ?></td>
                                <?php if ($showFaculty): ?>
                                <td class="muted"><?= $orDash($item['person_name'], 'Open class') ?></td>
                                <?php endif; ?>
                                <td><span class="badge badge-outline"><?= (int) ($item['enrolled'] ?? 0) ?></span></td>
                                <?php if ($showActions): ?>
                                <td style="text-align: right;">
                                    <?php if ($user['role'] === 'faculty') {
                                        teach_actions($user, $item);
                                    } else {
                                        content_actions($user, 'courses', $item, class_name($item) . ' ' . $item['title']);
                                    } ?>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
    <?php
}

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

function assignment_list(array $user, array $items, bool $compact = false, string $emptyTitle = 'No assignments yet',
                         string $emptyText = 'Assignments from your courses will appear here.'): void
{
    if (!$items) {
        app_empty('fa-clipboard-list', $emptyTitle, $emptyText);
        return;
    }
    ?>
                <div class="content-list<?= $compact ? ' compact' : '' ?>">
                    <?php foreach ($items as $item):
                        [$badgeClass, $badgeText] = due_badge($item['due_date']);
                        $due = format_date($item['due_date'], 'D, M j, Y') . ($item['due_time'] ? ' at ' . date('g:i A', strtotime($item['due_time'])) : '');
                    ?>
                    <article class="content-item">
                        <div class="content-item-head">
                            <div>
                                <h3 class="content-item-title"><?= e($item['title']) ?></h3>
                                <div class="content-meta">
                                    <span class="badge badge-outline" title="<?= e($item['course_title']) ?>"><?= e(class_name($item, 'course_')) ?></span>
                                    <span><i class="fa-regular fa-calendar"></i> Due <?= e($due) ?></span>
                                    <span class="badge <?= $badgeClass ?>"><?= e($badgeText) ?></span>
                                </div>
                            </div>
                            <?php if (!$compact) content_actions($user, 'assignments', $item, $item['title']); ?>
                        </div>
                        <?php if (!$compact && $item['instructions']): ?>
                        <div class="content-body"><?= nl2br(e($item['instructions'])) ?></div>
                        <?php endif; ?>
                    </article>
                    <?php endforeach; ?>
                </div>
    <?php
}

function exam_list(array $user, array $items, bool $compact = false, string $emptyTitle = 'No exams scheduled',
                   string $emptyText = "Exam schedules appear here once they're released."): void
{
    if (!$items) {
        app_empty('fa-calendar-xmark', $emptyTitle, $emptyText);
        return;
    }
    ?>
                <div class="content-list<?= $compact ? ' compact' : '' ?>">
                    <?php foreach ($items as $item): ?>
                    <article class="content-item event-item">
                        <div class="date-badge">
                            <span><?= e(format_date($item['exam_date'], 'M')) ?></span>
                            <strong><?= e(format_date($item['exam_date'], 'j')) ?></strong>
                        </div>
                        <div class="content-item-main">
                            <div class="content-item-head">
                                <div>
                                    <h3 class="content-item-title"><?= e($item['title']) ?></h3>
                                    <div class="content-meta">
                                        <span class="badge badge-outline" title="<?= e($item['course_title']) ?>"><?= e(class_name($item, 'course_')) ?></span>
                                        <span class="badge badge-primary"><?= e($item['exam_type']) ?></span>
                                        <span><i class="fa-regular fa-calendar"></i> <?= e(format_date($item['exam_date'], 'l, M j, Y')) ?></span>
                                        <span><i class="fa-regular fa-clock"></i> <?= e(format_time_range($item['start_time'], $item['end_time'])) ?></span>
                                        <?php if ($item['room']): ?>
                                        <span><i class="fa-solid fa-door-open"></i> <?= e($item['room']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (!$compact) content_actions($user, 'exams', $item, $item['title']); ?>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
    <?php
}
