<?php
/*
 * Class group chats: every course has one. Its members are the course's faculty and the students enrolled
 * in it, worked out from enrollments - so a student joins the chat the moment they're enrolled and leaves
 * when they're removed. The admin can open every chat.
 */
require_once __DIR__ . '/academics.php';

const CHAT_MAX_LENGTH = 2000;
const CHAT_PAGE_SIZE = 100;

/** Can this user open (and post in) the course's group chat? */
function in_course_chat(array $user, int $courseId): bool
{
    if ($user['role'] !== 'student') {
        return teaches_course($user, $courseId); // faculty: their own courses; admin: every course
    }
    $stmt = db()->prepare('SELECT 1 FROM enrollments WHERE course_id = ? AND student_id = ?');
    $stmt->execute([$courseId, $user['id']]);
    return (bool) $stmt->fetchColumn();
}

/** The user's chats, most recent activity first, with the last message and the unread count. */
function chat_list(array $user): array
{
    [$where, $params] = course_scope($user);
    $stmt = db()->prepare(
        "SELECT c.id, c.code, c.title, c.faculty_id, CONCAT(f.first_name, ' ', f.last_name) AS person_name,
                (SELECT COUNT(*) FROM enrollments x WHERE x.course_id = c.id) AS students,
                m.id AS last_id, m.kind AS last_kind, m.body AS last_body, m.created_at AS last_at, m.user_id AS last_user_id,
                u.first_name AS last_first_name, u.last_name AS last_last_name,
                (SELECT COUNT(*) FROM chat_messages n
                 WHERE n.course_id = c.id AND n.kind = 'message' AND n.id > COALESCE(r.last_read_id, 0)
                   AND (n.user_id IS NULL OR n.user_id <> ?)) AS unread
         FROM courses c
         LEFT JOIN users f ON f.id = c.faculty_id
         LEFT JOIN chat_messages m ON m.id = (SELECT MAX(id) FROM chat_messages WHERE course_id = c.id)
         LEFT JOIN users u ON u.id = m.user_id
         LEFT JOIN chat_reads r ON r.course_id = c.id AND r.user_id = ?" .
        ($where ? " WHERE $where" : '') .
        ' ORDER BY m.id IS NULL, m.id DESC, c.code'
    );
    $stmt->execute([$user['id'], $user['id'], ...$params]);
    return $stmt->fetchAll();
}

/** A chat from chat_list() as sent to the browser. */
function chat_list_json(array $chat, array $user): array
{
    $preview = 'No messages yet';
    if ($chat['last_id'] !== null) {
        $name = $chat['last_first_name'] !== null ? $chat['last_first_name'] . ' ' . $chat['last_last_name'] : 'Deleted account';
        $preview = match ($chat['last_kind']) {
            'added' => "$name was added to the group",
            'removed' => "$name was removed from the group",
            default => ((int) $chat['last_user_id'] === (int) $user['id'] ? 'You' : $name) . ': ' . $chat['last_body'],
        };
    }
    return [
        'id' => (int) $chat['id'],
        'code' => $chat['code'],
        'title' => $chat['title'],
        'members' => (int) $chat['students'] + ($chat['faculty_id'] !== null ? 1 : 0),
        'preview' => mb_strimwidth(preg_replace('/\s+/', ' ', $preview), 0, 90, '…'),
        'time' => $chat['last_at'] !== null ? chat_list_time($chat['last_at']) : '',
        'unread' => (int) $chat['unread'],
    ];
}

/** "9:41 AM" today, "Mon" this week, "Oct 3" before that. */
function chat_list_time(string $timestamp): string
{
    $time = strtotime($timestamp);
    if (date('Y-m-d', $time) === date('Y-m-d')) {
        return date('g:i A', $time);
    }
    return $time > strtotime('-6 days midnight') ? date('D', $time) : date('M j', $time);
}

/** "Today", "Yesterday" or "Mon, Oct 3, 2026" (the separators between days in a chat). */
function chat_day_label(string $timestamp): string
{
    $day = date('Y-m-d', strtotime($timestamp));
    return match ($day) {
        date('Y-m-d') => 'Today',
        date('Y-m-d', strtotime('-1 day')) => 'Yesterday',
        default => date('D, M j, Y', strtotime($timestamp)),
    };
}

/** Messages newer than $afterId (oldest first); the latest CHAT_PAGE_SIZE when $afterId is 0. */
function chat_messages(int $courseId, int $afterId): array
{
    $select = 'SELECT m.id, m.user_id, m.kind, m.body, m.created_at, u.first_name, u.last_name, u.role, u.picture_url
               FROM chat_messages m LEFT JOIN users u ON u.id = m.user_id';
    if ($afterId > 0) {
        $stmt = db()->prepare("$select WHERE m.course_id = ? AND m.id > ? ORDER BY m.id LIMIT " . CHAT_PAGE_SIZE);
        $stmt->execute([$courseId, $afterId]);
        return $stmt->fetchAll();
    }
    $stmt = db()->prepare("$select WHERE m.course_id = ? ORDER BY m.id DESC LIMIT " . CHAT_PAGE_SIZE);
    $stmt->execute([$courseId]);
    return array_reverse($stmt->fetchAll());
}

/** A message as sent to the browser. */
function chat_message_json(array $message, array $user): array
{
    $deleted = $message['first_name'] === null;
    $time = strtotime($message['created_at']);
    return [
        'id' => (int) $message['id'],
        'kind' => $message['kind'],
        'body' => $message['body'],
        'sender' => $message['user_id'] === null ? null : (int) $message['user_id'],
        'mine' => (int) $message['user_id'] === (int) $user['id'],
        'name' => $deleted ? 'Deleted account' : $message['first_name'] . ' ' . $message['last_name'],
        'initials' => $deleted ? '?' : initials($message),
        'picture' => $message['picture_url'] ?? null,
        'role' => match ($message['role']) { 'faculty' => 'Instructor', 'admin' => 'Admin', default => null },
        'day' => chat_day_label($message['created_at']),
        'time' => date('g:i A', $time),
        'stamp' => $time,
    ];
}

/** Remembers the last message the user has seen in a chat. */
function mark_chat_read(int $courseId, int $userId, int $messageId): void
{
    db()->prepare(
        'INSERT INTO chat_reads (course_id, user_id, last_read_id) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE last_read_id = GREATEST(last_read_id, VALUES(last_read_id))'
    )->execute([$courseId, $userId, $messageId]);
}

/**
 * "... was added to / removed from the group" notice, posted when a student is enrolled or removed.
 * A newly added student can read the earlier messages, but they don't count as unread.
 */
function add_chat_notice(int $courseId, int $studentId, string $kind): void
{
    db()->prepare('INSERT INTO chat_messages (course_id, user_id, kind) VALUES (?, ?, ?)')->execute([$courseId, $studentId, $kind]);
    if ($kind === 'added') {
        mark_chat_read($courseId, $studentId, (int) db()->lastInsertId());
    } else {
        db()->prepare('DELETE FROM chat_reads WHERE course_id = ? AND user_id = ?')->execute([$courseId, $studentId]);
    }
}

/** The course's faculty member (first) and its students. */
function chat_members(int $courseId): array
{
    $stmt = db()->prepare(
        "SELECT u.id, u.role, u.first_name, u.last_name, u.picture_url, u.program, u.department, 0 AS sort
         FROM courses c JOIN users u ON u.id = c.faculty_id WHERE c.id = ?
         UNION ALL
         SELECT u.id, u.role, u.first_name, u.last_name, u.picture_url, u.program, u.department, 1 AS sort
         FROM enrollments en JOIN users u ON u.id = en.student_id WHERE en.course_id = ?
         ORDER BY sort, last_name, first_name"
    );
    $stmt->execute([$courseId, $courseId]);
    return $stmt->fetchAll();
}
