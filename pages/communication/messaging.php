<?php
// Messages: a group chat for every course. Students are in the chats of the courses they're enrolled in
// (added automatically when enrolled), faculty in the chats of the courses they teach, and the admin can open
// every chat. Messages are loaded and sent by js/chat.js.
require __DIR__ . '/../../includes/app_shell.php';
require __DIR__ . '/../../includes/chat.php';
$page = 'pages/communication/messaging.php';
$user = require_page_access($page);

$chats = chat_list($user);
$current = pick_course($chats);
$members = $current ? chat_members((int) $current['id']) : [];
$instructor = $current && $current['person_name'] ? $current['person_name'] : null;

app_page_start($user, $page, 'Messages');
app_page_header('Messages', match ($user['role']) {
    'student' => 'Group chats for your classes. You join a class chat automatically when you are enrolled.',
    'faculty' => 'Group chats for the classes you teach, with every student enrolled in them',
    default => 'Every class group chat',
});

if (!$chats) {
    app_empty_card('fa-comments', 'Class Group Chats', 'fa-comments', 'No group chats yet', match ($user['role']) {
        'student' => "You're added to a class group chat automatically when you're enrolled in a course.",
        'faculty' => 'A group chat appears for each class you teach. Pick classes on the Courses page.',
        default => 'Every course gets a group chat. Add courses on the Courses page.',
    });
    app_page_end();
    return;
}
?>
            <div class="chat-app<?= isset($_GET['course']) ? ' chat-open' : '' ?>" id="chat-app">
                <aside class="chat-list-pane card" aria-label="Group chats">
                    <div class="chat-list-header">
                        <i class="fa-solid fa-comments"></i> Class Group Chats
                    </div>
                    <nav class="chat-list" id="chat-list"></nav>
                </aside>

                <section class="chat-pane card" aria-label="<?= e(class_name($current)) ?> group chat">
                    <header class="chat-header">
                        <a href="messaging.php" class="chat-back" aria-label="Back to chats"><i class="fa-solid fa-arrow-left"></i></a>
                        <div class="chat-icon"><i class="fa-solid fa-users"></i></div>
                        <div class="chat-header-text">
                            <h2><?= e(class_name($current) . ' - ' . $current['title']) ?></h2>
                            <p>
                                <?= count($members) ?> <?= count($members) === 1 ? 'member' : 'members' ?>
                                <?php if ($instructor): ?> • Instructor: <?= e($instructor) ?><?php endif; ?>
                                <?php if ($user['role'] === 'admin'): ?> • You're viewing as the admin<?php endif; ?>
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
                                  placeholder="Message <?= e(class_name($current)) ?>…"></textarea>
                        <button type="submit" class="chat-send" aria-label="Send" disabled><i class="fa-solid fa-paper-plane"></i></button>
                    </form>
                </section>
            </div>

            <div class="modal-overlay" id="chat-members-modal">
                <div class="modal" role="dialog" aria-modal="true" aria-labelledby="chat-members-title" style="max-width: 460px;">
                    <div class="modal-header">
                        <h3 class="modal-title" id="chat-members-title"><?= e(class_name($current)) ?> members</h3>
                        <button type="button" class="modal-close-btn" data-modal-close aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body chat-members">
                        <?php if (!$members): ?>
                        <?php app_empty('fa-user-group', 'No members yet', 'Students join this chat when they are enrolled in the course.'); ?>
                        <?php endif; ?>
                        <?php foreach ($members as $member): ?>
                        <div class="chat-member">
                            <?php // program or department under the name (members don't see each other's Gmail)
                            person_cell(['email' => $member['role'] === 'faculty' ? $member['department'] : $member['program']] + $member); ?>
                            <?php if ($member['role'] === 'faculty'): ?>
                            <span class="badge badge-primary">Instructor</span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <script type="application/json" id="chat-data"><?= json_encode([
                'course' => (int) $current['id'],
                'chats' => array_map(fn($chat) => chat_list_json($chat, $user), $chats),
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<?php
app_page_end(['js/chat.js?v=1']);
