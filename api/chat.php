<?php
/*
 * Group chats (called by js/chat.js). Class chats are for the course's faculty and its enrolled students;
 * program chats for the admin and the faculty teaching a class of that program (see includes/chat.php).
 * `chat` is the chat's key: "course=12" or "program=BS+Information+Technology".
 *
 *   POST api/chat.php?action=list                             the user's chats, with last message and unread count
 *   POST api/chat.php?action=messages  { chat, after }        messages newer than id `after` (the latest 100 when 0);
 *                                                             marks them read
 *   POST api/chat.php?action=send      { chat, body }
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

parse_str((string) ($input['chat'] ?? ''), $query);
$chat = chat_from_query($query);
if (!$chat || !in_chat($user, $chat)) {
    fail("You're not in this group chat.", 403);
}

match ($action) {
    'messages' => new_messages($user, $chat, (int) ($input['after'] ?? 0)),
    'send' => send_message($user, $chat, $input),
    default => fail('Unknown action.', 404),
};

function new_messages(array $user, array $chat, int $afterId): never
{
    $messages = chat_messages($chat, max(0, $afterId));
    if ($messages) {
        mark_chat_read($chat, (int) $user['id'], (int) end($messages)['id']);
    }
    json_response(['messages' => array_map(fn($message) => chat_message_json($message, $user, $chat), $messages)]);
}

function send_message(array $user, array $chat, array $input): never
{
    $body = trim(str_replace("\r\n", "\n", (string) ($input['body'] ?? '')));
    if ($body === '') {
        fail('Type a message first.', 422);
    }
    if (mb_strlen($body) > CHAT_MAX_LENGTH) {
        fail('Messages can be up to ' . number_format(CHAT_MAX_LENGTH) . ' characters.', 422);
    }
    json_response(['ok' => true, 'id' => post_chat_message($chat, (int) $user['id'], $body)]);
}
