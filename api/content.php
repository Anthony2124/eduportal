<?php
/*
 * Add / Edit / Remove for announcements, events, scholarships and courses (called by js/content.js).
 * Which fields each type has and who may change it is set in CONTENT_TYPES (includes/content.php).
 *
 *   POST api/content.php?action=save&type=events     { id?, ...fields }   add (no id) or edit (with id)
 *   POST api/content.php?action=delete&type=events   { id }
 */
declare(strict_types=1);

const EDUPORTAL_API = true;
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../includes/content.php';

$input = read_json_request();

$user = current_user();
if (!$user) {
    fail('Please sign in again.', 401);
}
$type = (string) ($_GET['type'] ?? '');
if (!isset(CONTENT_TYPES[$type])) {
    fail('Unknown type.', 404);
}
if (!can_manage_content($user, $type)) {
    fail("You don't have permission to change " . CONTENT_TYPES[$type]['label'] . 's.', 403);
}

match ($_GET['action'] ?? '') {
    'save' => save_content($user, $type, $input),
    'delete' => delete_content($user, $type, $input),
    default => fail('Unknown action.', 404),
};

function find_content(string $type, int $id): array
{
    $stmt = db()->prepare('SELECT * FROM ' . CONTENT_TYPES[$type]['table'] . ' WHERE id = ?');
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if (!$item) {
        fail('That ' . CONTENT_TYPES[$type]['label'] . ' no longer exists.', 404);
    }
    return $item;
}

function save_content(array $user, string $type, array $input): never
{
    $def = CONTENT_TYPES[$type];
    $values = validate_content($type, $input);
    $id = (int) ($input['id'] ?? 0);

    // Fields that must be different for every item (e.g. course code)
    foreach ($def['fields'] as $name => $field) {
        if (!empty($field['unique']) && $values[$name] !== null) {
            $stmt = db()->prepare("SELECT 1 FROM {$def['table']} WHERE {$name} = ? AND id <> ?");
            $stmt->execute([$values[$name], $id]);
            if ($stmt->fetchColumn()) {
                fail("Another {$def['label']} already uses this " . strtolower($field['label']) . '.', 409, ['field' => $name]);
            }
        }
    }

    if ($id) {
        $item = find_content($type, $id);
        if (!can_edit_content($user, $type, $item)) {
            fail("You can only change {$def['label']}s you posted.", 403);
        }
        $set = implode(', ', array_map(fn($column) => "{$column} = ?", array_keys($values)));
        db()->prepare("UPDATE {$def['table']} SET {$set} WHERE id = ?")->execute([...array_values($values), $id]);
    } else {
        $values[$def['owner']] = (int) $user['id'];
        $columns = implode(', ', array_keys($values));
        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        db()->prepare("INSERT INTO {$def['table']} ({$columns}) VALUES ({$placeholders})")->execute(array_values($values));
        $id = (int) db()->lastInsertId();
    }

    json_response(['ok' => true, 'id' => $id]);
}

function delete_content(array $user, string $type, array $input): never
{
    $def = CONTENT_TYPES[$type];
    $item = find_content($type, (int) ($input['id'] ?? 0));
    if (!can_edit_content($user, $type, $item)) {
        fail("You can only remove {$def['label']}s you posted.", 403);
    }
    db()->prepare("DELETE FROM {$def['table']} WHERE id = ?")->execute([$item['id']]);
    json_response(['ok' => true]);
}
