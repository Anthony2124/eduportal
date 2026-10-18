<?php
/*
 * The signed-in user's own account settings (called by pages/settings/profile.php).
 *
 *   POST api/profile.php?action=gender   { gender }   students: male, female or unspecified (prefer not to say)
 */
declare(strict_types=1);

const EDUPORTAL_API = true;
require __DIR__ . '/bootstrap.php';

$input = read_json_request();

$user = current_user();
if (!$user) {
    fail('Please sign in again.', 401);
}

match ($_GET['action'] ?? '') {
    'gender' => save_gender($user, (string) ($input['gender'] ?? '')),
    default => fail('Unknown action.', 404),
};

function save_gender(array $user, string $gender): never
{
    if ($user['role'] !== 'student') {
        fail('Only student accounts have a gender.', 403);
    }
    if (!isset(GENDERS[$gender])) {
        fail('Select your gender.', 422, ['field' => 'gender']);
    }
    db()->prepare('UPDATE users SET gender = ? WHERE id = ?')->execute([$gender, $user['id']]);
    log_activity($user, 'account', 'Updated their profile', 'Gender');
    json_response(['ok' => true]);
}
