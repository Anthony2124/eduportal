<?php
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/communication/messaging.php';
$user = require_page_access($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - EduPortal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <?php app_shell_styles(); ?>
</head>
<body>

<?php app_sidebar($user, $page); ?>

    <!-- Main Content -->
    <div class="main-content app-main">
<?php app_topbar($user, ['placeholder' => 'Search conversations, contacts, or messages...']); ?>

        <!-- Page Content -->
        <main class="content-area">
            <div class="page-header">
                <h1>Messages</h1>
                <p class="page-subtitle">Stay connected with your classmates and professors</p>
            </div>

            <!-- Chat Split-Pane Container -->
            <div class="chat-wrapper">
                <!-- Left Pane: Conversations -->
                <aside class="chat-sidebar">
                    <div class="chat-sidebar-header">
                        <div class="search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" placeholder="Search contacts...">
                        </div>
                        <div class="chat-tabs">
                            <button class="chat-tab-btn active" data-chat-type="all">All</button>
                            <button class="chat-tab-btn" data-chat-type="professors">Professors</button>
                            <button class="chat-tab-btn" data-chat-type="classmates">Classmates</button>
                            <button class="chat-tab-btn" data-chat-type="groups">Groups</button>
                        </div>
                    </div>

                    <ul class="conversation-list">
                        <!-- Item 1: Prof. Annie Fe Mendez -->
                        <li class="conversation-item active" data-chat-type="professors">
                            <div class="convo-avatar-wrap">
                                <div class="convo-avatar" style="background: linear-gradient(135deg, #4F46E5, #818CF8);">MS</div>
                                <span class="online-indicator"></span>
                            </div>
                            <div class="convo-info">
                                <div class="convo-top">
                                    <span class="convo-name">Prof. Annie Fe Mendez</span>
                                    <span class="convo-time">2m ago</span>
                                </div>
                                <div class="convo-bottom">
                                    <span class="convo-last-msg">Please review the updated syllabus...</span>
                                    <span class="unread-dot"></span>
                                </div>
                            </div>
                        </li>

                        <!-- Item 2: CS 201 Study Group -->
                        <li class="conversation-item" data-chat-type="groups">
                            <div class="convo-avatar-wrap">
                                <div class="convo-avatar" style="background: linear-gradient(135deg, #06B6D4, #3B82F6);">
                                    <i class="fa-solid fa-users" style="font-size:0.85rem;"></i>
                                </div>
                            </div>
                            <div class="convo-info">
                                <div class="convo-top">
                                    <span class="convo-name">CS 201 Study Group (5)</span>
                                    <span class="convo-time">15m ago</span>
                                </div>
                                <div class="convo-bottom">
                                    <span class="convo-last-msg">Alex: Has anyone started the report?</span>
                                </div>
                            </div>
                        </li>

                        <!-- Item 3: Prof. Einjela Picart -->
                        <li class="conversation-item" data-chat-type="professors">
                            <div class="convo-avatar-wrap">
                                <div class="convo-avatar" style="background: linear-gradient(135deg, #F59E0B, #D97706);">JG</div>
                            </div>
                            <div class="convo-info">
                                <div class="convo-top">
                                    <span class="convo-name">Prof. Einjela Picart</span>
                                    <span class="convo-time">1h ago</span>
                                </div>
                                <div class="convo-bottom">
                                    <span class="convo-last-msg">Office hours moved to Thursday</span>
                                </div>
                            </div>
                        </li>

                        <!-- Item 4: Jessica Rivera -->
                        <li class="conversation-item" data-chat-type="classmates">
                            <div class="convo-avatar-wrap">
                                <div class="convo-avatar" style="background: linear-gradient(135deg, #EC4899, #8B5CF6);">JR</div>
                                <span class="online-indicator"></span>
                            </div>
                            <div class="convo-info">
                                <div class="convo-top">
                                    <span class="convo-name">Jessica Rivera</span>
                                    <span class="convo-time">3h ago</span>
                                </div>
                                <div class="convo-bottom">
                                    <span class="convo-last-msg">Hey, want to study together?</span>
                                </div>
                            </div>
                        </li>

                        <!-- Item 5: MATH 202 Section A -->
                        <li class="conversation-item" data-chat-type="groups">
                            <div class="convo-avatar-wrap">
                                <div class="convo-avatar" style="background: linear-gradient(135deg, #10B981, #059669);">
                                    <i class="fa-solid fa-chalkboard-user" style="font-size:0.85rem;"></i>
                                </div>
                            </div>
                            <div class="convo-info">
                                <div class="convo-top">
                                    <span class="convo-name">MATH 202 Section A (32)</span>
                                    <span class="convo-time">Yesterday</span>
                                </div>
                                <div class="convo-bottom">
                                    <span class="convo-last-msg">Prof: Reminder about quiz...</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </aside>

                <!-- Right Pane: Active Chat View -->
                <section class="chat-main">
                    <!-- Chat Header -->
                    <div class="chat-view-header">
                        <div class="chat-header-user">
                            <div class="convo-avatar-wrap">
                                <div class="convo-avatar" style="background: linear-gradient(135deg, #4F46E5, #818CF8); width: 42px; height: 42px;">MS</div>
                                <span class="online-indicator"></span>
                            </div>
                            <div>
                                <h3 id="chat-active-name" style="font-size: 1rem; font-weight: 700; color: var(--dark);">Prof. Annie Fe Mendez</h3>
                                <div style="font-size: 0.75rem; color: var(--success); display: flex; align-items: center; gap: 5px;">
                                    <span style="display:inline-block; width:6px; height:6px; background:var(--success); border-radius:50%;"></span>
                                    Online • Data Structures & Algorithms (CS 201)
                                </div>
                            </div>
                        </div>

                        <div class="chat-header-actions">
                            <button class="chat-action-btn" title="Start Video Call" onclick="showToast('Initiating secure video link with Prof. Mendez...', 'info')">
                                <i class="fa-solid fa-video"></i>
                            </button>
                            <button class="chat-action-btn" title="Start Audio Call" onclick="showToast('Connecting audio call...', 'info')">
                                <i class="fa-solid fa-phone"></i>
                            </button>
                            <button class="chat-action-btn" title="Course Info & Details" onclick="showToast('Office hours: MWF 2:00-4:00 PM • Room Tech-304', 'info')">
                                <i class="fa-solid fa-circle-info"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Chat Messages List -->
                    <div class="chat-messages" id="chat-messages">
                        <div class="text-center" style="margin-bottom: 12px;">
                            <span class="badge badge-outline" style="font-size: 0.72rem; color: var(--gray-500); background: var(--white);">
                                Today, October 13, 2026
                            </span>
                        </div>

                        <!-- Prof message 1 -->
                        <div class="message-row received">
                            <div class="message-bubble">
                                <p>Good morning <span data-user="firstName"></span>! I've uploaded the updated syllabus for Chapter 7.</p>
                                <span class="message-meta">9:42 AM</span>
                            </div>
                        </div>

                        <!-- User reply 1 -->
                        <div class="message-row sent">
                            <div class="message-bubble">
                                <p>Thank you, Professor! I'll review it before class tomorrow.</p>
                                <span class="message-meta">9:45 AM · Read</span>
                            </div>
                        </div>

                        <!-- Prof message 2 -->
                        <div class="message-row received">
                            <div class="message-bubble">
                                <p>Great! Also, please review the updated rubric for the midterm project.</p>
                                <span class="message-meta">9:48 AM</span>
                            </div>
                        </div>

                        <!-- Prof message 3 -->
                        <div class="message-row received">
                            <div class="message-bubble">
                                <p>Please review the updated syllabus for next week's topics as well.</p>
                                <span class="message-meta">9:49 AM</span>
                            </div>
                        </div>

                        <!-- User reply 2 -->
                        <div class="message-row sent">
                            <div class="message-bubble">
                                <p>Will do! Is there a specific format for the project report?</p>
                                <span class="message-meta">9:52 AM · Read</span>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Input Area -->
                    <form class="chat-input-bar" id="chat-form">
                        <button type="button" class="chat-action-btn" title="Attach file" onclick="showToast('File upload picker opened', 'info')">
                            <i class="fa-solid fa-paperclip"></i>
                        </button>
                        <input type="text" class="chat-input-field" id="chat-input" placeholder="Type your message to Prof. Mendez..." autocomplete="off">
                        <button type="button" class="chat-action-btn" title="Insert Emoji" onclick="showToast('Emoji tray opened', 'info')">
                            <i class="fa-regular fa-face-smile"></i>
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" style="height: 38px; border-radius: var(--radius-full); padding: 0 16px;">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span style="margin-left: 4px;">Send</span>
                        </button>
                    </form>
                </section>
            </div>
        </main>
    </div>

    <script src="../../js/main.js?v=9"></script>
</body>
</html>
