<?php
/*
 * Content managed from the portal with Add / Edit / Remove buttons:
 * announcements, events, scholarships and courses.
 *
 * Each type is described once in CONTENT_TYPES (fields + who can manage it). The same code then builds
 * the form, checks the input (api/content.php) and shows the lists on the pages.
 */
require_once __DIR__ . '/../api/bootstrap.php';

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
    'courses' => [
        'table' => 'courses',
        'label' => 'course',
        'owner' => 'created_by',
        'manage' => ['admin'],
        'own_only' => [],
        'order' => 'a.code ASC',
        'fields' => [
            'code' => ['label' => 'Course code', 'type' => 'text', 'required' => true, 'max' => 20, 'upper' => true, 'unique' => true, 'placeholder' => 'e.g. CS 201'],
            'title' => ['label' => 'Course title', 'type' => 'text', 'required' => true, 'max' => 150],
            'units' => ['label' => 'Units', 'type' => 'number', 'required' => true, 'min' => 1, 'max' => 10],
            'schedule' => ['label' => 'Schedule', 'type' => 'text', 'max' => 100, 'placeholder' => 'e.g. MWF 8:00 - 9:00 AM'],
            'room' => ['label' => 'Room', 'type' => 'text', 'max' => 60],
            'faculty_id' => ['label' => 'Faculty', 'type' => 'faculty'],
        ],
    ],
];

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
    $person = $type === 'courses' ? 'faculty_id' : $def['owner'];
    $sql = "SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) AS person_name
            FROM {$def['table']} a LEFT JOIN users u ON u.id = a.{$person}"
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
function validate_content(string $type, array $input): array
{
    $values = [];
    foreach (CONTENT_TYPES[$type]['fields'] as $name => $field) {
        $raw = $input[$name] ?? '';
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

    if ($type === 'events' && $values['start_time'] && $values['end_time'] && $values['end_time'] <= $values['start_time']) {
        fail('The event must end after it starts.', 422, ['field' => 'end_time']);
    }
    return $values;
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
    $data = array_intersect_key($item, CONTENT_TYPES[$type]['fields'] + ['id' => true]);
    if (isset($data['start_time'])) {
        $data['start_time'] = substr((string) $data['start_time'], 0, 5);
    }
    if (isset($data['end_time'])) {
        $data['end_time'] = substr((string) $data['end_time'], 0, 5);
    }
    ?>
                            <div class="content-actions">
                                <button type="button" class="btn btn-outline btn-sm" data-content-edit="<?= e($type) ?>" data-item="<?= e(json_encode($data)) ?>">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                                <button type="button" class="btn btn-outline btn-sm content-remove" data-content-delete="<?= e($type) ?>" data-id="<?= (int) $item['id'] ?>" data-name="<?= e($name) ?>">
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
    $faculty = array_filter($def['fields'], fn($f) => $f['type'] === 'faculty') ? faculty_options() : [];
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
                                <label class="form-label" for="<?= e($id) ?>"><?= e($field['label']) ?><?= $req ? ' <span class="required">*</span>' : '' ?></label>
                                <?php if ($field['type'] === 'textarea'): ?>
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

function course_table(array $user, array $items, bool $showActions, string $emptyTitle, string $emptyText): void
{
    $showActions = $showActions && can_manage_content($user, 'courses');
    $columns = ['Code', 'Course', 'Units', 'Schedule', 'Room', 'Faculty'];
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
                                <td><?= (int) $item['units'] ?></td>
                                <td class="muted"><?= $orDash($item['schedule']) ?></td>
                                <td class="muted"><?= $orDash($item['room']) ?></td>
                                <td class="muted"><?= $orDash($item['person_name'], 'Not assigned') ?></td>
                                <?php if ($showActions): ?>
                                <td style="text-align: right;"><?php content_actions($user, 'courses', $item, $item['code'] . ' ' . $item['title']); ?></td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
    <?php
}
