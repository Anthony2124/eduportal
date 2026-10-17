<?php
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/communication/forums.php';
$user = require_page_access($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussion Forums - EduPortal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <?php app_shell_styles(); ?>
</head>
<body>

<?php app_sidebar($user, $page); ?>

    <!-- Main Content -->
    <div class="main-content app-main">
<?php app_topbar($user, ['placeholder' => 'Search forum threads, topics, and authors...']); ?>

        <!-- Page Content -->
        <main class="content-area">
            <div class="page-header-row">
                <div class="page-header">
                    <h1>Discussion Forums</h1>
                    <p class="page-subtitle">Collaborate, exchange ideas, and discuss academic topics with peers</p>
                </div>
                <button class="btn btn-primary" data-modal-open="new-topic-modal">
                    <i class="fa-solid fa-plus"></i>
                    <span>New Topic</span>
                </button>
            </div>

            <!-- Forum Categories Section -->
            <div style="margin-bottom: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 16px;">
                    <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--dark);">Forum Categories</h2>
                    <span style="font-size: 0.82rem; color: var(--gray-500);">6 Active Boards</span>
                </div>

                <div class="grid-3">
                    <!-- Cat 1: Academic Discussions -->
                    <div class="forum-cat-card" onclick="showToast('Filtered topics to Academic Discussions', 'info')">
                        <div class="forum-cat-icon" style="background: rgba(79, 70, 229, 0.12); color: var(--primary);">
                            <i class="fa-solid fa-book-bookmark"></i>
                        </div>
                        <div style="flex:1;">
                            <h3 style="font-size: 1rem; font-weight: 700; color: var(--dark);">📚 Academic Discussions</h3>
                            <div style="font-size: 0.78rem; color: var(--gray-500); margin-top: 3px;">
                                <strong>145</strong> topics • <strong>1,234</strong> posts
                            </div>
                            <div class="subforums-pills">
                                <span class="subforum-pill">CS Dept</span>
                                <span class="subforum-pill">Math Dept</span>
                                <span class="subforum-pill">Sciences</span>
                                <span class="subforum-pill">Humanities</span>
                            </div>
                        </div>
                    </div>

                    <!-- Cat 2: Study Groups -->
                    <div class="forum-cat-card" onclick="showToast('Filtered topics to Study Groups', 'info')">
                        <div class="forum-cat-icon" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                            <i class="fa-solid fa-lightbulb"></i>
                        </div>
                        <div style="flex:1;">
                            <h3 style="font-size: 1rem; font-weight: 700; color: var(--dark);">💡 Study Groups</h3>
                            <div style="font-size: 0.78rem; color: var(--gray-500); margin-top: 3px;">
                                <strong>67</strong> topics • <strong>543</strong> posts
                            </div>
                            <p style="font-size: 0.75rem; color: var(--gray-600); margin-top: 6px;">
                                Find exam review partners and collaborative project squads.
                            </p>
                        </div>
                    </div>

                    <!-- Cat 3: Campus Life -->
                    <div class="forum-cat-card" onclick="showToast('Filtered topics to Campus Life', 'info')">
                        <div class="forum-cat-icon" style="background: rgba(6, 182, 212, 0.12); color: var(--secondary);">
                            <i class="fa-solid fa-school"></i>
                        </div>
                        <div style="flex:1;">
                            <h3 style="font-size: 1rem; font-weight: 700; color: var(--dark);">🏫 Campus Life</h3>
                            <div style="font-size: 0.78rem; color: var(--gray-500); margin-top: 3px;">
                                <strong>89</strong> topics • <strong>876</strong> posts
                            </div>
                            <p style="font-size: 0.75rem; color: var(--gray-600); margin-top: 6px;">
                                Housing, campus dining, clubs, and university facilities discussion.
                            </p>
                        </div>
                    </div>

                    <!-- Cat 4: Career & Internships -->
                    <div class="forum-cat-card" onclick="showToast('Filtered topics to Career & Internships', 'info')">
                        <div class="forum-cat-icon" style="background: rgba(16, 185, 129, 0.12); color: var(--success);">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                        <div style="flex:1;">
                            <h3 style="font-size: 1rem; font-weight: 700; color: var(--dark);">💼 Career & Internships</h3>
                            <div style="font-size: 0.78rem; color: var(--gray-500); margin-top: 3px;">
                                <strong>34</strong> topics • <strong>210</strong> posts
                            </div>
                            <p style="font-size: 0.75rem; color: var(--gray-600); margin-top: 6px;">
                                Tech interview tips, resume reviews, and internship experiences.
                            </p>
                        </div>
                    </div>

                    <!-- Cat 5: Off-Topic & Social -->
                    <div class="forum-cat-card" onclick="showToast('Filtered topics to Off-Topic & Social', 'info')">
                        <div class="forum-cat-icon" style="background: rgba(139, 92, 246, 0.12); color: #8B5CF6;">
                            <i class="fa-solid fa-gamepad"></i>
                        </div>
                        <div style="flex:1;">
                            <h3 style="font-size: 1rem; font-weight: 700; color: var(--dark);">🎮 Off-Topic & Social</h3>
                            <div style="font-size: 0.78rem; color: var(--gray-500); margin-top: 3px;">
                                <strong>156</strong> topics • <strong>2,100</strong> posts
                            </div>
                            <p style="font-size: 0.75rem; color: var(--gray-600); margin-top: 6px;">
                                Gaming, tech gear recommendations, hobbies, and casual banter.
                            </p>
                        </div>
                    </div>

                    <!-- Cat 6: Help & Support -->
                    <div class="forum-cat-card" onclick="showToast('Filtered topics to Help & Support', 'info')">
                        <div class="forum-cat-icon" style="background: rgba(239, 68, 68, 0.12); color: var(--danger);">
                            <i class="fa-solid fa-circle-question"></i>
                        </div>
                        <div style="flex:1;">
                            <h3 style="font-size: 1rem; font-weight: 700; color: var(--dark);">❓ Help & Support</h3>
                            <div style="font-size: 0.78rem; color: var(--gray-500); margin-top: 3px;">
                                <strong>45</strong> topics • <strong>380</strong> posts
                            </div>
                            <p style="font-size: 0.75rem; color: var(--gray-600); margin-top: 6px;">
                                Technical assistance, portal queries, and campus IT support.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent/Trending Topics Table -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-fire text-primary"></i>
                        <span>Recent & Trending Topics</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="badge badge-primary">Live Discussions</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="data-table" id="topics-table">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Topic</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th style="text-align: center;">Replies</th>
                                <th style="text-align: center;">Views</th>
                                <th>Last Activity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <i class="fa-regular fa-comment-dots text-primary" style="font-size: 1.1rem;"></i>
                                        <div>
                                            <a href="#" style="font-weight: 600; color: var(--dark); font-size: 0.92rem; display: block;" onclick="showToast('Opening thread: Tips for Data Structures Midterm', 'info'); return false;">
                                                Tips for Data Structures Midterm
                                            </a>
                                            <span style="font-size: 0.75rem; color: var(--gray-500);">Started by Annie Fe Mendez in CS 201</span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge badge-primary">Academic</span></td>
                                <td>
                                    <span style="font-weight: 600; font-size: 0.85rem; color: var(--primary);">@maria_s</span>
                                </td>
                                <td style="text-align: center; font-weight: 600;">23</td>
                                <td style="text-align: center; color: var(--gray-500);">456</td>
                                <td>
                                    <span style="font-size: 0.82rem; color: var(--gray-500);"><i class="fa-regular fa-clock"></i> 5 min ago</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <i class="fa-regular fa-comment-dots text-warning" style="font-size: 1.1rem;"></i>
                                        <div>
                                            <a href="#" style="font-weight: 600; color: var(--dark); font-size: 0.92rem; display: block;" onclick="showToast('Opening thread: Study group for Ethics?', 'info'); return false;">
                                                Study group for Ethics?
                                            </a>
                                            <span style="font-size: 0.75rem; color: var(--gray-500);">Meeting weekly on Discord or library pod</span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge badge-warning">Study Groups</span></td>
                                <td>
                                    <span style="font-weight: 600; font-size: 0.85rem; color: var(--dark);">@alex_b</span>
                                </td>
                                <td style="text-align: center; font-weight: 600;">12</td>
                                <td style="text-align: center; color: var(--gray-500);">234</td>
                                <td>
                                    <span style="font-size: 0.82rem; color: var(--gray-500);"><i class="fa-regular fa-clock"></i> 20 min ago</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <i class="fa-regular fa-comment-dots" style="font-size: 1.1rem; color: #8B5CF6;"></i>
                                        <div>
                                            <a href="#" style="font-weight: 600; color: var(--dark); font-size: 0.92rem; display: block;" onclick="showToast('Opening thread: Best laptop for CS students?', 'info'); return false;">
                                                Best laptop for CS students?
                                            </a>
                                            <span style="font-size: 0.75rem; color: var(--gray-500);">M-series MacBook vs ThinkPad for Linux/compilers</span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge" style="background: rgba(139, 92, 246, 0.12); color: #8B5CF6;">Off-Topic</span></td>
                                <td>
                                    <span style="font-weight: 600; font-size: 0.85rem; color: var(--dark);">@james_l</span>
                                </td>
                                <td style="text-align: center; font-weight: 600;">45</td>
                                <td style="text-align: center; color: var(--gray-500);">1.2k</td>
                                <td>
                                    <span style="font-size: 0.82rem; color: var(--gray-500);"><i class="fa-regular fa-clock"></i> 1h ago</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <i class="fa-regular fa-comment-dots text-success" style="font-size: 1.1rem;"></i>
                                        <div>
                                            <a href="#" style="font-weight: 600; color: var(--dark); font-size: 0.92rem; display: block;" onclick="showToast('Opening thread: Internship at TechCorp - AMA', 'info'); return false;">
                                                Internship at TechCorp - AMA
                                            </a>
                                            <span style="font-size: 0.75rem; color: var(--gray-500);">Happy to answer questions on coding rounds & behavioral interview</span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge badge-success">Career</span></td>
                                <td>
                                    <span style="font-weight: 600; font-size: 0.85rem; color: var(--dark);">@sarah_k</span>
                                </td>
                                <td style="text-align: center; font-weight: 600;">34</td>
                                <td style="text-align: center; color: var(--gray-500);">890</td>
                                <td>
                                    <span style="font-size: 0.82rem; color: var(--gray-500);"><i class="fa-regular fa-clock"></i> 2h ago</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Topic Creation Modal -->
    <div class="modal-overlay" id="new-topic-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-pen-to-square text-primary"></i> Create New Topic</h3>
                <button class="modal-close-btn" data-modal-close aria-label="Close Modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="create-topic-form" data-toast-success="Topic posted successfully to the community forum!">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="topic-title">Topic Title</label>
                        <input type="text" id="topic-title" class="form-control" placeholder="e.g. Tips for Data Structures Midterm..." required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="topic-category">Category</label>
                        <select id="topic-category" class="form-select" required>
                            <option value="">Select a category...</option>
                            <option value="Academic">Academic Discussions</option>
                            <option value="Study Groups">Study Groups</option>
                            <option value="Campus Life">Campus Life</option>
                            <option value="Career">Career & Internships</option>
                            <option value="Off-Topic">Off-Topic & Social</option>
                            <option value="Help & Support">Help & Support</option>
                        </select>
                    </div>

                    <!-- Rich text editor simulator -->
                    <div class="form-group">
                        <label class="form-label">Topic Content</label>
                        <div style="border: 1px solid var(--gray-300); border-radius: var(--radius-sm); overflow: hidden;">
                            <!-- Editor Toolbar -->
                            <div style="background: var(--light); padding: 8px 12px; border-bottom: 1px solid var(--gray-300); display: flex; gap: 8px; flex-wrap: wrap;">
                                <button type="button" class="btn btn-sm btn-outline" style="padding: 4px 8px;" title="Bold"><i class="fa-solid fa-bold"></i></button>
                                <button type="button" class="btn btn-sm btn-outline" style="padding: 4px 8px;" title="Italic"><i class="fa-solid fa-italic"></i></button>
                                <button type="button" class="btn btn-sm btn-outline" style="padding: 4px 8px;" title="Code Block"><i class="fa-solid fa-code"></i></button>
                                <button type="button" class="btn btn-sm btn-outline" style="padding: 4px 8px;" title="Insert Link"><i class="fa-solid fa-link"></i></button>
                                <button type="button" class="btn btn-sm btn-outline" style="padding: 4px 8px;" title="Bullet List"><i class="fa-solid fa-list-ul"></i></button>
                                <button type="button" class="btn btn-sm btn-outline" style="padding: 4px 8px;" title="Insert Image"><i class="fa-regular fa-image"></i></button>
                            </div>
                            <textarea id="topic-content" class="form-textarea" style="border: none; border-radius: 0; min-height: 140px;" placeholder="Write your question, insight, or discussion topic..." required></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="topic-tags">Tags</label>
                        <input type="text" id="topic-tags" class="form-control" placeholder="Comma separated, e.g. cs201, midterm, algorithms">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-paper-plane"></i> Post Topic
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../js/main.js?v=9"></script>
    <script>
        // Custom handler to add new topic to table dynamically
        const topicForm = document.getElementById('create-topic-form');
        if (topicForm) {
            topicForm.addEventListener('submit', (e) => {
                const title = document.getElementById('topic-title').value;
                const category = document.getElementById('topic-category').value;
                const tbody = document.querySelector('#topics-table tbody');

                if (tbody && title && category) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="fa-regular fa-comment-dots text-primary" style="font-size: 1.1rem;"></i>
                                <div>
                                    <a href="#" style="font-weight: 600; color: var(--dark); font-size: 0.92rem; display: block;">
                                        ${title}
                                    </a>
                                    <span style="font-size: 0.75rem; color: var(--gray-500);">Started just now by you</span>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge badge-primary">${category}</span></td>
                        <td><span style="font-weight: 600; font-size: 0.85rem; color: var(--primary);">@alex_m</span></td>
                        <td style="text-align: center; font-weight: 600;">0</td>
                        <td style="text-align: center; color: var(--gray-500);">1</td>
                        <td><span style="font-size: 0.82rem; color: var(--gray-500);"><i class="fa-regular fa-clock"></i> Just now</span></td>
                    `;
                    tbody.insertBefore(tr, tbody.firstChild);
                }
            });
        }
    </script>
</body>
</html>
