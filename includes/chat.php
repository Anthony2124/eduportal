<?php
/*
 * Group chats on the Messages page. There are two kinds:
 *  - class chats: one per course, for its faculty member and the students enrolled in it, worked out from
 *    enrollments - so a student joins the chat the moment they're enrolled and leaves when they're removed.
 *  - program chats: one per degree program (PROGRAMS), for the admin and the faculty who teach a class of that
 *    program - picking a class of the program adds a faculty member, dropping their last one removes them.
 * The admin is only in program chats; students only in class chats.
 *
 * A chat is [type, ref]: ['course', 12] or ['program', 'BS Information Technology']. Links and requests name it
 * by its key, the query string "course=12" / "program=BS+Information+Technology".
 */
require_once __DIR__ . '/academics.php';

const CHAT_MAX_LENGTH = 2000;
const CHAT_PAGE_SIZE = 100;

// Where each type of chat keeps its messages and read markers, and the column that says which chat
const CHAT_TABLES = [
    'course' => ['messages' => 'chat_messages', 'reads' => 'chat_reads', 'column' => 'course_id'],
    'program' => ['messages' => 'program_chat_messages', 'reads' => 'program_chat_reads', 'column' => 'program'],
];

/** "course=12" / "program=BS+Information+Technology" */
function chat_key(array $chat): string
{
    return http_build_query([$chat[0] => $chat[1]]);
}

/** The chat named by a key's (or the page's) query values, e.g. ['course' => '12']; null if none. */
function chat_from_query(array $query): ?array
{
    if (is_string($query['course'] ?? null)) {
        return ['course', (int) $query['course']];
    }
    if (in_array($query['program'] ?? null, PROGRAMS, true)) {
        return ['program', $query['program']];
    }
    return null;
}

/** Does the faculty member teach a class of this program? */
function teaches_program(array $user, string $program): bool
{
    $stmt = db()->prepare('SELECT 1 FROM courses WHERE faculty_id = ? AND program = ? LIMIT 1');
    $stmt->execute([$user['id'], $program]);
    return (bool) $stmt->fetchColumn();
}

/** Can this user open (and post in) the chat? */
function in_chat(array $user, array $chat): bool
{
    [$type, $ref] = $chat;
    if ($type === 'program') {
        return $user['role'] === 'admin' || ($user['role'] === 'faculty' && teaches_program($user, $ref));
    }
    if ($user['role'] === 'faculty') {
        return teaches_course($user, $ref);
    }
    if ($user['role'] !== 'student') {
        return false; // the admin isn't in class chats
    }
    $stmt = db()->prepare('SELECT 1 FROM enrollments WHERE course_id = ? AND student_id = ?');
    $stmt->execute([$ref, $user['id']]);
    return (bool) $stmt->fetchColumn();
}

/** The user's chats, most recent activity first, with the last message and the unread count. */
function chat_list(array $user): array
{
    $chats = [...program_chat_list($user), ...class_chat_list($user)];
    // chats without messages go last, in the order above (program chats, then classes by code)
    usort($chats, fn($a, $b) => [$a['last_at'] === null, $b['last_at']] <=> [$b['last_at'] === null, $a['last_at']]);
    return $chats;
}

/** Class chats: faculty get the courses they teach, students the ones they're enrolled in, the admin none. */
function class_chat_list(array $user): array
{
    if ($user['role'] === 'admin') {
        return [];
    }
    [$where, $params] = course_scope($user);
    $stmt = db()->prepare(
        "SELECT 'course' AS type, c.id AS ref, c.code, c.block, c.title, c.faculty_id, CONCAT(f.first_name, ' ', f.last_name) AS person_name,
                (SELECT COUNT(*) FROM enrollments x WHERE x.course_id = c.id) + (c.faculty_id IS NOT NULL) AS members,
                m.id AS last_id, m.kind AS last_kind, m.body AS last_body, m.created_at AS last_at, m.user_id AS last_user_id,
                u.first_name AS last_first_name, u.last_name AS last_last_name,
                (SELECT COUNT(*) FROM chat_messages n
                 WHERE n.course_id = c.id AND n.kind = 'message' AND n.id > COALESCE(r.last_read_id, 0)
                   AND (n.user_id IS NULL OR n.user_id <> ?)) AS unread
         FROM courses c
         LEFT JOIN users f ON f.id = c.faculty_id
         LEFT JOIN chat_messages m ON m.id = (SELECT MAX(id) FROM chat_messages WHERE course_id = c.id)
         LEFT JOIN users u ON u.id = m.user_id
         LEFT JOIN chat_reads r ON r.course_id = c.id AND r.user_id = ?
         WHERE $where
         ORDER BY m.id IS NULL, m.id DESC, c.code, c.block"
    );
    $stmt->execute([$user['id'], $user['id'], ...$params]);
    return $stmt->fetchAll();
}

/** Program chats: the admin gets every program, faculty the programs they teach a class of, students none. */
function program_chat_list(array $user): array
{
    if ($user['role'] === 'admin') {
        $programs = PROGRAMS;
    } elseif ($user['role'] === 'faculty') {
        $stmt = db()->prepare('SELECT DISTINCT program FROM courses WHERE faculty_id = ? AND program IS NOT NULL');
        $stmt->execute([$user['id']]);
        $programs = array_intersect(PROGRAMS, $stmt->fetchAll(PDO::FETCH_COLUMN));
    } else {
        return [];
    }

    $stmt = db()->prepare(
        "SELECT (SELECT COUNT(*) FROM users WHERE role = 'admin')
                  + (SELECT COUNT(DISTINCT faculty_id) FROM courses WHERE program = ?) AS members,
                (SELECT COUNT(*) FROM program_chat_messages n
                 LEFT JOIN program_chat_reads r ON r.program = n.program AND r.user_id = ?
                 WHERE n.program = ? AND n.kind = 'message' AND n.id > COALESCE(r.last_read_id, 0)
                   AND (n.user_id IS NULL OR n.user_id <> ?)) AS unread,
                m.id AS last_id, m.kind AS last_kind, m.body AS last_body, m.created_at AS last_at, m.user_id AS last_user_id,
                u.first_name AS last_first_name, u.last_name AS last_last_name
         FROM (SELECT 1) one
         LEFT JOIN program_chat_messages m ON m.id = (SELECT MAX(id) FROM program_chat_messages WHERE program = ?)
         LEFT JOIN users u ON u.id = m.user_id"
    );
    $chats = [];
    foreach ($programs as $program) {
        $stmt->execute([$program, $user['id'], $program, $user['id'], $program]);
        $chats[] = ['type' => 'program', 'ref' => $program, 'title' => $program] + $stmt->fetch();
    }
    return $chats;
}

/** "BSIT" for "BS Information Technology" */
function program_abbreviation(string $program): string
{
    $words = preg_split('/\s+/', preg_replace('/^BS\s+/', '', $program));
    return 'BS' . implode('', array_map(fn($word) => mb_strtoupper(mb_substr($word, 0, 1)), $words));
}

/** Name of a chat from chat_list(): "IT 101 (BSIT 2-A)" or "BS Information Technology". */
function chat_name(array $chat): string
{
    return $chat['type'] === 'program' ? $chat['ref'] : class_name($chat);
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
    $program = $chat['type'] === 'program';
    return [
        'key' => chat_key([$chat['type'], $chat['ref']]),
        'type' => $chat['type'],
        'name' => chat_name($chat),
        'title' => $program ? 'Faculty & admin' : $chat['title'],
        'icon' => $program ? program_abbreviation($chat['ref']) : (substr(preg_replace('/[^A-Za-z]/', '', $chat['code']), 0, 3) ?: '#'),
        'members' => (int) $chat['members'],
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
function chat_messages(array $chat, int $afterId): array
{
    ['messages' => $table, 'column' => $column] = CHAT_TABLES[$chat[0]];
    $select = "SELECT m.id, m.user_id, m.kind, m.body, m.created_at, u.first_name, u.last_name, u.role, u.picture_url
               FROM $table m LEFT JOIN users u ON u.id = m.user_id";
    if ($afterId > 0) {
        $stmt = db()->prepare("$select WHERE m.$column = ? AND m.id > ? ORDER BY m.id LIMIT " . CHAT_PAGE_SIZE);
        $stmt->execute([$chat[1], $afterId]);
        return $stmt->fetchAll();
    }
    $stmt = db()->prepare("$select WHERE m.$column = ? ORDER BY m.id DESC LIMIT " . CHAT_PAGE_SIZE);
    $stmt->execute([$chat[1]]);
    return array_reverse($stmt->fetchAll());
}

/** A message as sent to the browser. In class chats the faculty member is the Instructor. */
function chat_message_json(array $message, array $user, array $chat): array
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
        'role' => match ($message['role']) {
            'faculty' => $chat[0] === 'course' ? 'Instructor' : 'Faculty',
            'admin' => 'Admin',
            default => null,
        },
        'day' => chat_day_label($message['created_at']),
        'time' => date('g:i A', $time),
        'stamp' => $time,
    ];
}

function post_chat_message(array $chat, int $userId, string $body): int
{
    ['messages' => $table, 'column' => $column] = CHAT_TABLES[$chat[0]];
    db()->prepare("INSERT INTO $table ($column, user_id, kind, body) VALUES (?, ?, 'message', ?)")->execute([$chat[1], $userId, $body]);
    return (int) db()->lastInsertId();
}

/** Remembers the last message the user has seen in a chat. */
function mark_chat_read(array $chat, int $userId, int $messageId): void
{
    ['reads' => $table, 'column' => $column] = CHAT_TABLES[$chat[0]];
    db()->prepare(
        "INSERT INTO $table ($column, user_id, last_read_id) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE last_read_id = GREATEST(last_read_id, VALUES(last_read_id))"
    )->execute([$chat[1], $userId, $messageId]);
}

/**
 * "... was added to / removed from the group" notice in a class chat, posted when a student is enrolled or removed.
 * A newly added student can read the earlier messages, but they don't count as unread.
 */
function add_chat_notice(int $courseId, int $studentId, string $kind): void
{
    db()->prepare('INSERT INTO chat_messages (course_id, user_id, kind) VALUES (?, ?, ?)')->execute([$courseId, $studentId, $kind]);
    if ($kind === 'added') {
        mark_chat_read(['course', $courseId], $studentId, (int) db()->lastInsertId());
    } else {
        db()->prepare('DELETE FROM chat_reads WHERE course_id = ? AND user_id = ?')->execute([$courseId, $studentId]);
    }
}

/** Class chat: the course's faculty member (first), then its students. Program chat: the admin, then its faculty. */
function chat_members(array $chat): array
{
    $columns = 'u.id, u.role, u.first_name, u.last_name, u.picture_url, u.program, u.department';
    if ($chat[0] === 'program') {
        $stmt = db()->prepare(
            "SELECT $columns, 0 AS sort FROM users u WHERE u.role = 'admin'
             UNION ALL
             SELECT $columns, 1 AS sort FROM users u WHERE u.id IN (SELECT faculty_id FROM courses WHERE program = ?)
             ORDER BY sort, last_name, first_name"
        );
        $stmt->execute([$chat[1]]);
        return $stmt->fetchAll();
    }
    $stmt = db()->prepare(
        "SELECT $columns, 0 AS sort FROM courses c JOIN users u ON u.id = c.faculty_id WHERE c.id = ?
         UNION ALL
         SELECT $columns, 1 AS sort FROM enrollments en JOIN users u ON u.id = en.student_id WHERE en.course_id = ?
         ORDER BY sort, last_name, first_name"
    );
    $stmt->execute([$chat[1], $chat[1]]);
    return $stmt->fetchAll();
}

/** The chat chosen with ?course=ID or ?program=NAME (if it is one of $chats), otherwise the first one. */
function pick_chat(array $chats): ?array
{
    $wanted = chat_from_query($_GET);
    foreach ($chats as $chat) {
        if ($wanted && $chat['type'] === $wanted[0] && (string) $chat['ref'] === (string) $wanted[1]) {
            return $chat;
        }
    }
    return $chats[0] ?? null;
}
