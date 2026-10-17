<?php
/*
 * Class group chats (called by js/chat.js). Only the course's faculty, its enrolled students and the admin
 * can read or post in a course's chat.
 *
 *   POST api/chat.php?action=list                                 the user's chats, with last message and unread count
 *   POST api/chat.php?action=messages  { course_id, after }       messages newer than id `after` (the latest 100 when 0);
 *                                                                 marks them read
 *   POST api/chat.php?action=send      { course_id, body }
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
session_write_close(); // the page checks for new messages every few seconds; don't hold the session lock

$action = $_GET['action'] ?? '';
if ($action === 'list') {
    json_response(['chats' => array_map(fn($chat) => chat_list_json($chat, $user), chat_list($user))]);
}

$courseId = (int) ($input['course_id'] ?? 0);
if (!in_course_chat($user, $courseId)) {
    fail("You're not in this group chat.", 403);
}

match ($action) {
    'messages' => new_messages($user, $courseId, (int) ($input['after'] ?? 0)),
    'send' => send_message($user, $courseId, $input),
    default => fail('Unknown action.', 404),
};

function new_messages(array $user, int $courseId, int $afterId): never
{
    $messages = chat_messages($courseId, max(0, $afterId));
    if ($messages) {
        mark_chat_read($courseId, (int) $user['id'], (int) end($messages)['id']);
    }
    json_response(['messages' => array_map(fn($message) => chat_message_json($message, $user), $messages)]);
}

function send_message(array $user, int $courseId, array $input): never
{
    $body = trim(str_replace("\r\n", "\n", (string) ($input['body'] ?? '')));
    if ($body === '') {
        fail('Type a message first.', 422);
    }
    if (mb_strlen($body) > CHAT_MAX_LENGTH) {
        fail('Messages can be up to ' . number_format(CHAT_MAX_LENGTH) . ' characters.', 422);
    }
    db()->prepare("INSERT INTO chat_messages (course_id, user_id, kind, body) VALUES (?, ?, 'message', ?)")
        ->execute([$courseId, $user['id'], $body]);
    json_response(['ok' => true, 'id' => (int) db()->lastInsertId()]);
}
