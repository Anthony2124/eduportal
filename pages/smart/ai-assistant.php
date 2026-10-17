<?php
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/smart/ai-assistant.php';
$user = require_page_access($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Study Assistant - EduPortal</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Base Stylesheet -->
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        /* AI Assistant Page-Specific Styles */
        .ai-features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .feature-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .feature-card:hover {
            transform: translateY(-3px);
            border-color: var(--primary);
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.15);
        }

        .feature-icon-box {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .icon-summarize { background: rgba(79, 70, 229, 0.1); color: var(--primary); }
        .icon-quiz { background: rgba(245, 158, 11, 0.1); color: var(--accent); }
        .icon-explain { background: rgba(6, 182, 212, 0.1); color: var(--secondary); }
        .icon-planner { background: rgba(16, 185, 129, 0.1); color: var(--success); }

        .feature-info h3 {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }

        .feature-info p {
            font-size: 0.8125rem;
            color: var(--gray-500);
            line-height: 1.35;
        }

        /* Chat Layout */
        .chat-main-container {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 1.5rem;
            align-items: start;
        }

        .chat-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            height: 680px;
            overflow: hidden;
        }

        .chat-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #FAFAFC;
        }

        .chat-bot-identity {
            display: flex;
            align-items: center;
            gap: 0.875rem;
        }

        .bot-avatar {
            width: 42px;
            height: 42px;
            border-radius: var(--radius-full);
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.25rem;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
        }

        .bot-title-group h2 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .bot-status {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--success);
        }

        .bot-status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: var(--success);
        }

        .chat-header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .model-select {
            padding: 0.4rem 0.75rem;
            font-size: 0.8125rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--gray-300);
            background: var(--white);
            color: var(--gray-700);
            font-weight: 500;
            outline: none;
        }

        .chat-action-icon {
            width: 34px;
            height: 34px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-500);
            border: 1px solid var(--gray-200);
            transition: all var(--transition-fast);
        }

        .chat-action-icon:hover {
            color: var(--primary);
            background: var(--gray-100);
        }

        /* Message Scroll Area */
        .chat-messages-scroll {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            background: #F8FAFC;
        }

        .chat-msg {
            display: flex;
            gap: 0.875rem;
            max-width: 88%;
        }

        .chat-msg.msg-bot {
            align-self: flex-start;
        }

        .chat-msg.msg-user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .msg-sender-avatar {
            width: 34px;
            height: 34px;
            border-radius: var(--radius-full);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .msg-bot .msg-sender-avatar {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: var(--white);
        }

        .msg-user .msg-sender-avatar img {
            width: 100%;
            height: 100%;
            border-radius: var(--radius-full);
            object-fit: cover;
        }

        .msg-bubble {
            background: var(--white);
            padding: 1rem 1.25rem;
            border-radius: 16px;
            border-top-left-radius: 4px;
            font-size: 0.875rem;
            line-height: 1.6;
            color: var(--gray-700);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid var(--gray-200);
        }

        .msg-user .msg-bubble {
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 16px;
            border-top-right-radius: 4px;
        }

        .msg-meta {
            font-size: 0.6875rem;
            color: var(--gray-400);
            margin-top: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .msg-user .msg-meta {
            justify-content: flex-end;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Message Content Formatting */
        .msg-bubble p {
            margin-bottom: 0.625rem;
        }
        .msg-bubble p:last-child {
            margin-bottom: 0;
        }

        .complexity-list {
            background: var(--gray-100);
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            margin: 0.625rem 0;
            font-size: 0.8125rem;
        }

        .complexity-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.25rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .complexity-item:last-child {
            border-bottom: none;
        }

        .code-snippet {
            background: #1E293B;
            color: #38BDF8;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.8125rem;
            margin: 0.625rem 0;
            white-space: pre;
            overflow-x: auto;
        }

        .quiz-box {
            background: #F1F5F9;
            border-left: 3px solid var(--primary);
            padding: 0.875rem 1rem;
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
            margin: 0.75rem 0;
        }

        .quiz-title {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.35rem;
        }

        .quiz-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .quiz-opt-btn {
            background: var(--white);
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm);
            padding: 0.4rem 0.6rem;
            font-size: 0.8125rem;
            font-weight: 500;
            text-align: left;
            transition: all var(--transition-fast);
        }

        .quiz-opt-btn:hover {
            border-color: var(--primary);
            background: rgba(79, 70, 229, 0.05);
            color: var(--primary);
        }

        /* Chat Input Bar */
        .chat-input-bar {
            padding: 1rem 1.25rem;
            background: var(--white);
            border-top: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .chat-tool-btn {
            width: 38px;
            height: 38px;
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-500);
            font-size: 1rem;
            transition: all var(--transition-fast);
        }

        .chat-tool-btn:hover {
            color: var(--primary);
            background: var(--gray-100);
        }

        .chat-tool-btn.active {
            color: var(--danger);
            background: rgba(239, 68, 68, 0.1);
            animation: pulse 1.5s infinite;
        }

        .chat-input-field {
            flex: 1;
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius-full);
            border: 1px solid var(--gray-300);
            outline: none;
            font-size: 0.875rem;
            background: var(--gray-100);
            transition: all var(--transition-fast);
        }

        .chat-input-field:focus {
            background: var(--white);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        .chat-send-btn {
            width: 42px;
            height: 42px;
            border-radius: var(--radius-full);
            background: var(--primary);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: all var(--transition-fast);
            flex-shrink: 0;
        }

        .chat-send-btn:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
        }

        /* Quick Actions Panel */
        .quick-actions-panel {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .side-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
            padding: 1.25rem;
        }

        .side-card-title {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .side-card-title i {
            color: var(--primary);
        }

        .topic-pill-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .topic-pill {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0.75rem;
            background: var(--gray-100);
            border-radius: var(--radius-md);
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--gray-700);
            transition: all var(--transition-fast);
            cursor: pointer;
        }

        .topic-pill:hover {
            background: rgba(79, 70, 229, 0.08);
            color: var(--primary);
        }

        .prompt-chip-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .prompt-chip {
            background: var(--white);
            border: 1px dashed var(--gray-300);
            border-radius: var(--radius-md);
            padding: 0.6rem 0.875rem;
            font-size: 0.8125rem;
            color: var(--gray-600);
            cursor: pointer;
            text-align: left;
            transition: all var(--transition-fast);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .prompt-chip:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(79, 70, 229, 0.04);
            transform: translateX(3px);
        }

        .stat-metrics-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            text-align: center;
        }

        .stat-metric-box {
            background: var(--gray-100);
            border-radius: var(--radius-md);
            padding: 0.75rem 0.5rem;
        }

        .stat-metric-num {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--primary);
        }

        .stat-metric-lbl {
            font-size: 0.6875rem;
            color: var(--gray-500);
            font-weight: 500;
            line-height: 1.2;
            margin-top: 0.25rem;
        }

        /* Typing indicator */
        .typing-indicator {
            display: none;
            align-items: center;
            gap: 4px;
            padding: 8px 14px;
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 14px;
            width: fit-content;
        }
        .typing-dot {
            width: 6px;
            height: 6px;
            background: var(--gray-400);
            border-radius: 50%;
            animation: bounce 1.4s infinite ease-in-out;
        }
        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }
        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1.0); }
        }

        @media (max-width: 1100px) {
            .ai-features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .chat-main-container {
                grid-template-columns: 1fr;
            }
            .chat-card {
                height: 580px;
            }
        }

        @media (max-width: 600px) {
            .ai-features-grid {
                grid-template-columns: 1fr;
            }
            .quiz-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <?php app_shell_styles(); ?>
</head>
<body>
<?php app_sidebar($user, $page); ?>

    <!-- Main Content -->
    <div class="main-content app-main">
<?php app_topbar($user, ['placeholder' => 'Search courses, concepts, notes...']); ?>

        <!-- Page Content -->
        <div class="content-area">
            <div class="page-header">
                <h1>AI Study Assistant</h1>
                <p class="page-subtitle">Your personal AI-powered learning companion</p>
            </div>

            <!-- Feature Cards Row (top) -->
            <div class="ai-features-grid">
                <div class="feature-card" onclick="insertPrompt('Summarize my recent lecture notes for CS 201')">
                    <div class="feature-icon-box icon-summarize">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div class="feature-info">
                        <h3>📝 Summarize Notes</h3>
                        <p>Get AI summaries of your lecture notes</p>
                    </div>
                </div>

                <div class="feature-card" onclick="insertPrompt('Generate a 5-question practice quiz on Binary Search Trees')">
                    <div class="feature-icon-box icon-quiz">
                        <i class="fa-solid fa-circle-question"></i>
                    </div>
                    <div class="feature-info">
                        <h3>❓ Practice Quiz</h3>
                        <p>Generate practice questions from any topic</p>
                    </div>
                </div>

                <div class="feature-card" onclick="insertPrompt('Explain Dijkstra\'s shortest path algorithm in simple terms')">
                    <div class="feature-icon-box icon-explain">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <div class="feature-info">
                        <h3>📖 Explain Concept</h3>
                        <p>Get simple explanations of complex topics</p>
                    </div>
                </div>

                <div class="feature-card" onclick="insertPrompt('Create a balanced study schedule for my upcoming midterm exams')">
                    <div class="feature-icon-box icon-planner">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div class="feature-info">
                        <h3>📅 Study Planner</h3>
                        <p>AI-optimized study schedule</p>
                    </div>
                </div>
            </div>

            <!-- Chat Area & Quick Actions -->
            <div class="chat-main-container">
                <!-- Main Chat Box -->
                <div class="chat-card">
                    <!-- Chat Header -->
                    <div class="chat-header">
                        <div class="chat-bot-identity">
                            <div class="bot-avatar">
                                <i class="fa-solid fa-robot"></i>
                            </div>
                            <div class="bot-title-group">
                                <h2>EduBot 🤖</h2>
                                <span class="bot-status">
                                    <span class="bot-status-dot"></span>
                                    Online · Ready to assist
                                </span>
                            </div>
                        </div>

                        <div class="chat-header-actions">
                            <select class="model-select" aria-label="Select AI Model">
                                <option value="academic">EduBot 4.0 Academic (Default)</option>
                                <option value="fast">EduBot Turbo (Fast)</option>
                                <option value="deep">EduBot Reasoner (Math & Code)</option>
                            </select>
                            <button class="chat-action-icon" title="Clear Chat History" onclick="clearChat()">
                                <i class="fa-solid fa-rotate-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Messages Scroll Thread -->
                    <div class="chat-messages-scroll" id="chatThread">
                        <!-- Message 1: Bot Introduction -->
                        <div class="chat-msg msg-bot">
                            <div class="msg-sender-avatar">
                                <i class="fa-solid fa-robot"></i>
                            </div>
                            <div class="msg-bubble-wrap">
                                <div class="msg-bubble">
                                    <p>Hello <span data-user="firstName"></span>! 👋 I'm <strong>EduBot</strong>, your AI study assistant. How can I help you today?</p>
                                    <p>I can help you with:</p>
                                    <ul style="padding-left: 1.25rem; margin: 0.5rem 0;">
                                        <li>Summarizing lecture notes</li>
                                        <li>Creating practice quizzes</li>
                                        <li>Explaining difficult concepts</li>
                                        <li>Planning your study schedule</li>
                                        <li>Answering academic questions</li>
                                    </ul>
                                </div>
                                <div class="msg-meta">
                                    <span>EduBot · 10:14 AM</span>
                                </div>
                            </div>
                        </div>

                        <!-- Message 2: User Query -->
                        <div class="chat-msg msg-user">
                            <div class="msg-sender-avatar">
                                <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="" data-user="avatar">
                            </div>
                            <div class="msg-bubble-wrap">
                                <div class="msg-bubble">
                                    Can you help me understand Big O notation for my Data Structures midterm?
                                </div>
                                <div class="msg-meta">
                                    <span>10:15 AM · Read</span>
                                </div>
                            </div>
                        </div>

                        <!-- Message 3: Bot Explanation -->
                        <div class="chat-msg msg-bot">
                            <div class="msg-sender-avatar">
                                <i class="fa-solid fa-robot"></i>
                            </div>
                            <div class="msg-bubble-wrap">
                                <div class="msg-bubble">
                                    <p>Of course! Let me break down Big O notation for you:</p>
                                    <p><strong>Big O Notation</strong> measures the worst-case time complexity of an algorithm as the input size <em>n</em> grows.</p>
                                    
                                    <div class="complexity-list">
                                        <div class="complexity-item">
                                            <span><strong>• O(1)</strong> - Constant</span>
                                            <span style="color: var(--success); font-weight: 600;">Array index lookup</span>
                                        </div>
                                        <div class="complexity-item">
                                            <span><strong>• O(log n)</strong> - Logarithmic</span>
                                            <span style="color: var(--secondary); font-weight: 600;">Binary search</span>
                                        </div>
                                        <div class="complexity-item">
                                            <span><strong>• O(n)</strong> - Linear</span>
                                            <span style="color: var(--primary); font-weight: 600;">Simple single loop</span>
                                        </div>
                                        <div class="complexity-item">
                                            <span><strong>• O(n log n)</strong> - Linearithmic</span>
                                            <span style="color: var(--warning); font-weight: 600;">Merge Sort / Quick Sort</span>
                                        </div>
                                        <div class="complexity-item">
                                            <span><strong>• O(n²)</strong> - Quadratic</span>
                                            <span style="color: var(--danger); font-weight: 600;">Nested loops</span>
                                        </div>
                                        <div class="complexity-item">
                                            <span><strong>• O(2ⁿ)</strong> - Exponential</span>
                                            <span style="color: #9333EA; font-weight: 600;">Recursive Fibonacci</span>
                                        </div>
                                    </div>
                                    <p>Would you like me to generate practice problems on this topic?</p>
                                </div>
                                <div class="msg-meta">
                                    <span>EduBot · 10:15 AM</span>
                                </div>
                            </div>
                        </div>

                        <!-- Message 4: User Confirmation -->
                        <div class="chat-msg msg-user">
                            <div class="msg-sender-avatar">
                                <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="" data-user="avatar">
                            </div>
                            <div class="msg-bubble-wrap">
                                <div class="msg-bubble">
                                    Yes, give me a practice quiz!
                                </div>
                                <div class="msg-meta">
                                    <span>10:16 AM · Read</span>
                                </div>
                            </div>
                        </div>

                        <!-- Message 5: Bot Quiz -->
                        <div class="chat-msg msg-bot">
                            <div class="msg-sender-avatar">
                                <i class="fa-solid fa-robot"></i>
                            </div>
                            <div class="msg-bubble-wrap">
                                <div class="msg-bubble">
                                    <p>Here's a quick quiz! 📝</p>

                                    <div class="quiz-box">
                                        <div class="quiz-title">Q1: What is the time complexity of searching in a balanced BST?</div>
                                        <div class="quiz-options">
                                            <button class="quiz-opt-btn" onclick="answerQuiz(1, 'A')">A) O(1)</button>
                                            <button class="quiz-opt-btn" onclick="answerQuiz(1, 'B')">B) O(log n) ✨</button>
                                            <button class="quiz-opt-btn" onclick="answerQuiz(1, 'C')">C) O(n)</button>
                                            <button class="quiz-opt-btn" onclick="answerQuiz(1, 'D')">D) O(n²)</button>
                                        </div>
                                    </div>

                                    <div class="quiz-box">
                                        <div class="quiz-title">Q2: What is the Big O of this code?</div>
                                        <div class="code-snippet">for i in range(n):
    for j in range(n):
        print(i, j)</div>
                                        <div class="quiz-options">
                                            <button class="quiz-opt-btn" onclick="answerQuiz(2, 'A')">A) O(n)</button>
                                            <button class="quiz-opt-btn" onclick="answerQuiz(2, 'B')">B) O(n log n)</button>
                                            <button class="quiz-opt-btn" onclick="answerQuiz(2, 'C')">C) O(n²) ✨</button>
                                            <button class="quiz-opt-btn" onclick="answerQuiz(2, 'D')">D) O(2ⁿ)</button>
                                        </div>
                                    </div>

                                    <p style="margin-top: 0.5rem;">Click your answers above or type them in the box!</p>
                                </div>
                                <div class="msg-meta">
                                    <span>EduBot · 10:16 AM</span>
                                </div>
                            </div>
                        </div>

                        <!-- Typing Indicator -->
                        <div class="typing-indicator" id="typingIndicator">
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                        </div>
                    </div>

                    <!-- Chat Input Area -->
                    <form class="chat-input-bar" id="chatForm" onsubmit="handleSend(event)">
                        <button type="button" class="chat-tool-btn" title="Attach file or lecture slides" onclick="triggerAttachment()">
                            <i class="fa-solid fa-paperclip"></i>
                        </button>
                        <button type="button" class="chat-tool-btn" id="micBtn" title="Voice dictation" onclick="toggleMic()">
                            <i class="fa-solid fa-microphone"></i>
                        </button>
                        <input type="text" id="chatInput" class="chat-input-field" placeholder="Ask EduBot a question or request a quiz..." autocomplete="off">
                        <button type="submit" class="chat-send-btn" title="Send message">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                </div>

                <!-- Quick Actions & Stats Sidebar -->
                <div class="quick-actions-panel">
                    <!-- Recent Topics -->
                    <div class="side-card">
                        <h3 class="side-card-title">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            Recent Topics
                        </h3>
                        <div class="topic-pill-list">
                            <div class="topic-pill" onclick="insertPrompt('Can you review Big O notation summary?')">
                                <span>Big O Notation</span>
                                <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
                            </div>
                            <div class="topic-pill" onclick="insertPrompt('Explain Singly vs Doubly Linked Lists')">
                                <span>Linked Lists</span>
                                <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
                            </div>
                            <div class="topic-pill" onclick="insertPrompt('Show step-by-step Integration by Parts example')">
                                <span>Calculus Integrals</span>
                                <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Suggested Prompts -->
                    <div class="side-card">
                        <h3 class="side-card-title">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                            Suggested Prompts
                        </h3>
                        <div class="prompt-chip-list">
                            <button type="button" class="prompt-chip" onclick="insertPrompt('Explain recursion with a visual analogy')">
                                <i class="fa-regular fa-lightbulb"></i>
                                <span>Explain recursion</span>
                            </button>
                            <button type="button" class="prompt-chip" onclick="insertPrompt('Quiz me on Desktop Publishing Chapter 3: Newton\'s Laws')">
                                <i class="fa-solid fa-atom"></i>
                                <span>Quiz me on Desktop Publishing</span>
                            </button>
                            <button type="button" class="prompt-chip" onclick="insertPrompt('Summarize Chapter 5: Database Normalization')">
                                <i class="fa-solid fa-file-lines"></i>
                                <span>Summarize Chapter 5</span>
                            </button>
                        </div>
                    </div>

                    <!-- Study Stats -->
                    <div class="side-card">
                        <h3 class="side-card-title">
                            <i class="fa-solid fa-chart-pie"></i>
                            Study Stats
                        </h3>
                        <div class="stat-metrics-grid">
                            <div class="stat-metric-box">
                                <div class="stat-metric-num">45</div>
                                <div class="stat-metric-lbl">Questions Asked</div>
                            </div>
                            <div class="stat-metric-box">
                                <div class="stat-metric-num">12</div>
                                <div class="stat-metric-lbl">Topics Covered</div>
                            </div>
                            <div class="stat-metric-box">
                                <div class="stat-metric-num">3</div>
                                <div class="stat-metric-lbl">Quizzes Taken</div>
                            </div>
                        </div>

                        <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-size: 0.75rem; color: var(--gray-500);">Weekly AI Token Allowance</span>
                            <span class="badge badge-success">82% Remaining</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../js/main.js?v=9"></script>
    <script>
        // Interactive Chat Handling
        const chatThread = document.getElementById('chatThread');
        const chatInput = document.getElementById('chatInput');
        const typingIndicator = document.getElementById('typingIndicator');
        const micBtn = document.getElementById('micBtn');

        function insertPrompt(text) {
            chatInput.value = text;
            chatInput.focus();
        }

        function triggerAttachment() {
            showToast('Document uploaded: Lecture_Notes_Week5.pdf', 'success');
            setTimeout(() => {
                appendUserMessage('Attached: Lecture_Notes_Week5.pdf. Can you summarize key definitions?');
                showBotTypingAndReply('I have analyzed your uploaded PDF! Key definitions include: **1. Amortized Analysis**, **2. Red-Black Tree Balancing**, and **3. Hash Collisions via Chaining**.');
            }, 600);
        }

        let isRecording = false;
        function toggleMic() {
            isRecording = !isRecording;
            if (isRecording) {
                micBtn.classList.add('active');
                showToast('Listening... Speak now.', 'indigo');
                setTimeout(() => {
                    chatInput.value = 'How does dynamic programming reduce exponential time to polynomial time?';
                    micBtn.classList.remove('active');
                    isRecording = false;
                }, 2200);
            } else {
                micBtn.classList.remove('active');
            }
        }

        function answerQuiz(questionNumber, selectedOption) {
            let reply = '';
            if (questionNumber === 1) {
                if (selectedOption === 'B') {
                    reply = '🎉 **Correct on Q1!** Searching in a balanced Binary Search Tree cuts the search space in half at each step, yielding **O(log n)**.';
                } else {
                    reply = `Option ${selectedOption} is incorrect for Q1. In a balanced BST, each comparison halves remaining nodes, so the answer is **B) O(log n)**.`;
                }
            } else if (questionNumber === 2) {
                if (selectedOption === 'C') {
                    reply = '🎉 **Spot on for Q2!** Two nested loops iterating up to n execute n × n = n² times, resulting in **O(n²)** quadratic complexity.';
                } else {
                    reply = `Option ${selectedOption} is incorrect for Q2. Nested loops each of size n result in **O(n²)** complexity.`;
                }
            }
            appendUserMessage(`Selected: Q${questionNumber} Option ${selectedOption}`);
            showBotTypingAndReply(reply);
        }

        function handleSend(e) {
            e.preventDefault();
            const text = chatInput.value.trim();
            if (!text) return;

            appendUserMessage(text);
            chatInput.value = '';

            // Generate contextual response
            let botReply = "That's a great question! Let's explore the key principles behind this concept and break it down step-by-step for your review.";
            const lower = text.toLowerCase();
            if (lower.includes('recursion')) {
                botReply = "**Recursion** is when a function calls itself to solve a smaller subproblem of the same type. Always remember two parts: 1) **Base Case** (when to stop), and 2) **Recursive Case** (moving towards the base case).";
            } else if (lower.includes('Desktop Publishing')) {
                botReply = "**Newton's 3 Laws of Motion**: 1) Law of Inertia, 2) F = ma, 3) For every action, there is an equal and opposite reaction.";
            } else if (lower.includes('planner') || lower.includes('schedule')) {
                botReply = "I have drafted a study schedule: **Mon/Wed**: Data Structures (2h), **Tue/Thu**: Discrete Math (2h), **Fri**: Desktop Publishing (1.5h). Review flashcards on weekends!";
            } else if (lower.includes('q1') || lower.includes('b') || lower.includes('answer')) {
                botReply = "Great attempt! Keep practicing with active recall and you will ace your upcoming midterm examinations!";
            }

            showBotTypingAndReply(botReply);
        }

        function appendUserMessage(text) {
            const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const msgEl = document.createElement('div');
            msgEl.className = 'chat-msg msg-user';
            msgEl.innerHTML = `
                <div class="msg-sender-avatar">
                    <img src="${document.querySelector('[data-user="avatar"]').src}" alt="">
                </div>
                <div class="msg-bubble-wrap">
                    <div class="msg-bubble">${escapeHtml(text)}</div>
                    <div class="msg-meta"><span>${time} · Sent</span></div>
                </div>
            `;
            chatThread.insertBefore(msgEl, typingIndicator);
            scrollToBottom();
        }

        function showBotTypingAndReply(text) {
            typingIndicator.style.display = 'flex';
            scrollToBottom();

            setTimeout(() => {
                typingIndicator.style.display = 'none';
                const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                const msgEl = document.createElement('div');
                msgEl.className = 'chat-msg msg-bot';
                msgEl.innerHTML = `
                    <div class="msg-sender-avatar">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <div class="msg-bubble-wrap">
                        <div class="msg-bubble">${text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')}</div>
                        <div class="msg-meta"><span>EduBot · ${time}</span></div>
                    </div>
                `;
                chatThread.insertBefore(msgEl, typingIndicator);
                scrollToBottom();
            }, 900);
        }

        function clearChat() {
            if (confirm('Clear current chat session history?')) {
                showToast('Chat history cleared', 'success');
            }
        }

        function scrollToBottom() {
            chatThread.scrollTop = chatThread.scrollHeight;
        }

        function escapeHtml(string) {
            return String(string).replace(/[&<>"'`=\/]/g, function (s) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;',
                    '/': '&#x2F;',
                    '`': '&#x60;',
                    '=': '&#x3D;'
                }[s];
            });
        }
    </script>
</body>
</html>
