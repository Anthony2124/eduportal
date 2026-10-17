<?php
/*
 * Admin-only actions, called by admin.php with fetch().
 *
 *   POST api/admin.php?action=create_faculty  { firstName, lastName, idNumber, department, email, password }
 *   POST api/admin.php?action=delete          { id }   delete an account (not your own)
 */
declare(strict_types=1);

const EDUPORTAL_API = true;
require __DIR__ . '/bootstrap.php';

$input = read_json_request();

$admin = current_user();
if (!$admin || $admin['role'] !== 'admin') {
    fail('Only admins can do this.', 403);
}

match ($_GET['action'] ?? '') {
    'create_faculty' => action_create_faculty($input),
    'delete' => action_delete_user($admin, $input),
    default => fail('Unknown action.', 404),
};

/** Faculty can't sign up themselves; the admin creates their account here. */
function action_create_faculty(array $input): never
{
    $firstName = trim((string) ($input['firstName'] ?? ''));
    $lastName = trim((string) ($input['lastName'] ?? ''));
    $idNumber = strtoupper(trim((string) ($input['idNumber'] ?? '')));
    $department = (string) ($input['department'] ?? '');
    $email = strtolower(trim((string) ($input['email'] ?? '')));
    $password = (string) ($input['password'] ?? '');

    if (mb_strlen($firstName) < 2 || mb_strlen($firstName) > 60) {
        fail('Enter the first name.', 422, ['field' => 'firstName']);
    }
    if (mb_strlen($lastName) < 2 || mb_strlen($lastName) > 60) {
        fail('Enter the last name.', 422, ['field' => 'lastName']);
    }
    if (!preg_match('/^[A-Z0-9-]{5,20}$/', $idNumber)) {
        fail('Use letters, numbers and dashes only, e.g. FAC-2026-014.', 422, ['field' => 'idNumber']);
    }
    if (!in_array($department, DEPARTMENTS, true)) {
        fail('Select a department.', 422, ['field' => 'department']);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        fail('Enter a valid Gmail address (sign-in codes are sent there).', 422, ['field' => 'email']);
    }
    if (!is_valid_password($password)) {
        fail('Use at least 8 characters with a letter and a number.', 422, ['field' => 'password']);
    }

    $stmt = db()->prepare('SELECT id_number = ? AS same_id, email = ? AS same_email FROM users WHERE id_number = ? OR email = ? LIMIT 1');
    $stmt->execute([$idNumber, $email, $idNumber, $email]);
    $taken = $stmt->fetch();
    if ($taken && $taken['same_id']) {
        fail('Another account already uses this Employee ID.', 409, ['field' => 'idNumber']);
    }
    if ($taken) {
        fail('Another account already uses this Gmail.', 409, ['field' => 'email']);
    }

    db()->prepare(
        'INSERT INTO users (role, id_number, first_name, last_name, department, email, password_hash)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    )->execute(['faculty', $idNumber, $firstName, $lastName, $department, $email, password_hash($password, PASSWORD_DEFAULT)]);

    json_response(['ok' => true, 'name' => "{$firstName} {$lastName}"]);
}

function action_delete_user(array $admin, array $input): never
{
    $id = (int) ($input['id'] ?? 0);
    if ($id === (int) $admin['id']) {
        fail("You can't delete your own account.", 400);
    }

    $user = find_user_by_id($id);
    if (!$user) {
        fail('That account no longer exists.', 404);
    }

    // The deleted user's open sessions stop working on their next request (current_user() finds no row)
    db()->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
    json_response(['ok' => true, 'role' => $user['role']]);
}
