<?php
// Messages: group chats. Every course has a class chat for its faculty member and enrolled students (added
// automatically when enrolled), and every program a chat for the admin and the faculty teaching its classes.
// The admin is only in program chats. Messages are loaded and sent by js/chat.js.
require __DIR__ . '/../../includes/app_shell.php';
require __DIR__ . '/../../includes/chat.php';
$page = 'pages/communication/messaging.php';
$user = require_page_access($page);

$chats = chat_list($user);
$current = pick_chat($chats);
$chat = $current ? [$current['type'], $current['ref']] : null;
$isProgram = $current && $current['type'] === 'program';
$members = $chat ? chat_members($chat) : [];
$name = $current ? chat_name($current) : '';

app_page_start($user, $page, 'Messages');
app_page_header('Messages', match ($user['role']) {
    'student' => 'Group chats for your classes. You join a class chat automatically when you are enrolled.',
    'faculty' => 'Group chats for the classes you teach, and for each program you teach in with its faculty and the admin',
    default => 'A group chat for each program with the faculty who teach its classes',
});

if (!$chats) {
    app_empty_card('fa-comments', 'Group Chats', 'fa-comments', 'No group chats yet', match ($user['role']) {
        'student' => "You're added to a class group chat automatically when you're enrolled in a course.",
        default => 'A group chat appears for each class you teach and its program. Pick classes on the Courses page.',
    });
    app_page_end();
    return;
}
?>
            <div class="chat-app<?= chat_from_query($_GET) ? ' chat-open' : '' ?>" id="chat-app">
                <aside class="chat-list-pane card" aria-label="Group chats">
                    <div class="chat-list-header">
                        <i class="fa-solid fa-comments"></i> Group Chats
                    </div>
                    <nav class="chat-list" id="chat-list"></nav>
                </aside>

                <section class="chat-pane card" aria-label="<?= e($name) ?> group chat">
                    <header class="chat-header">
                        <a href="messaging.php" class="chat-back" aria-label="Back to chats"><i class="fa-solid fa-arrow-left"></i></a>
                        <div class="chat-icon<?= $isProgram ? ' program' : '' ?>"><i class="fa-solid <?= $isProgram ? 'fa-user-tie' : 'fa-users' ?>"></i></div>
                        <div class="chat-header-text">
                            <h2><?= e($isProgram ? $name . ' - Faculty & admin' : $name . ' - ' . $current['title']) ?></h2>
                            <p>
                                <?= count($members) ?> <?= count($members) === 1 ? 'member' : 'members' ?>
                                <?php if (!$isProgram && $current['person_name']): ?> • Instructor: <?= e($current['person_name']) ?><?php endif; ?>
                                <?php if ($isProgram): ?> • The admin and the faculty teaching <?= e(program_abbreviation($name)) ?> classes<?php endif; ?>
                            </p>
                        </div>
                        <button type="button" class="btn btn-outline btn-sm" data-modal-open="chat-members-modal">
                            <i class="fa-solid fa-user-group"></i> <span>Members</span>
                        </button>
                    </header>

                    <div class="chat-messages" id="chat-messages" aria-live="polite">
                        <div class="chat-status"><i class="fa-solid fa-spinner fa-spin"></i> Loading messages…</div>
                    </div>

                    <form class="chat-composer" id="chat-composer" autocomplete="off">
                        <textarea name="body" rows="1" maxlength="<?= CHAT_MAX_LENGTH ?>" aria-label="Message"
                                  placeholder="Message <?= e($isProgram ? program_abbreviation($name) . ' faculty' : $name) ?>…"></textarea>
                        <button type="submit" class="chat-send" aria-label="Send" disabled><i class="fa-solid fa-paper-plane"></i></button>
                    </form>
                </section>
            </div>

            <div class="modal-overlay" id="chat-members-modal">
                <div class="modal" role="dialog" aria-modal="true" aria-labelledby="chat-members-title" style="max-width: 460px;">
                    <div class="modal-header">
                        <h3 class="modal-title" id="chat-members-title"><?= e($name) ?> members</h3>
                        <button type="button" class="modal-close-btn" data-modal-close aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body chat-members">
                        <?php if (!$members): ?>
                        <?php app_empty('fa-user-group', 'No members yet', 'Students join this chat when they are enrolled in the course.'); ?>
                        <?php endif; ?>
                        <?php foreach ($members as $member): ?>
                        <div class="chat-member">
                            <?php // program or department under the name (members don't see each other's Gmail)
                            person_cell(['email' => $member['role'] === 'student' ? $member['program'] : $member['department']] + $member); ?>
                            <?php if ($member['role'] !== 'student'): ?>
                            <span class="badge badge-primary"><?= $member['role'] === 'admin' ? 'Admin' : ($isProgram ? 'Faculty' : 'Instructor') ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <script type="application/json" id="chat-data"><?= json_encode([
                'chat' => chat_key($chat),
                'chats' => array_map(fn($item) => chat_list_json($item, $user), $chats),
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<?php
app_page_end(['js/chat.js?v=2']);
