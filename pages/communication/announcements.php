<?php
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/communication/announcements.php';
$user = require_page_access($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - EduPortal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <?php app_shell_styles(); ?>
</head>
<body>

<?php app_sidebar($user, $page); ?>

    <!-- Main Content -->
    <div class="main-content app-main">
<?php app_topbar($user, ['id' => 'announcement-search', 'placeholder' => 'Search notices, memos, and updates...']); ?>

        <!-- Page Content -->
        <main class="content-area">
            <div class="page-header-row">
                <div class="page-header">
                    <h1>Announcements</h1>
                    <p class="page-subtitle">Stay updated with the latest news and university notices</p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button class="btn btn-outline" onclick="showToast('Subscribed to instant email and push digests.', 'success')">
                        <i class="fa-regular fa-bell"></i> Manage Subscriptions
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-tabs" data-filter-group data-filter-target=".announcement-item">
                <button class="filter-tab active" data-filter="all">
                    <span>All Announcements</span>
                    <span class="filter-count">5</span>
                </button>
                <button class="filter-tab" data-filter="academic">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>Academic</span>
                    <span class="filter-count">2</span>
                </button>
                <button class="filter-tab" data-filter="administrative">
                    <i class="fa-solid fa-building-columns"></i>
                    <span>Administrative</span>
                    <span class="filter-count">1</span>
                </button>
                <button class="filter-tab" data-filter="events">
                    <i class="fa-solid fa-calendar-star"></i>
                    <span>Events</span>
                    <span class="filter-count">1</span>
                </button>
                <button class="filter-tab" data-filter="urgent">
                    <i class="fa-solid fa-circle-exclamation text-danger"></i>
                    <span>Urgent</span>
                    <span class="filter-count">1</span>
                </button>
            </div>

            <!-- Announcements List -->
            <div class="announcements-container">
                <!-- Announcement 1: URGENT Midterms -->
                <article class="announcement-card announcement-item urgent-border" data-category="academic urgent">
                    <div class="announcement-meta">
                        <span><i class="fa-solid fa-building"></i> Office of the Registrar</span>
                        <span>•</span>
                        <span><i class="fa-regular fa-calendar"></i> October 12, 2026</span>
                        <span>•</span>
                        <span class="badge badge-urgent"><i class="fa-solid fa-triangle-exclamation"></i> URGENT</span>
                        <span class="badge badge-primary">Academic</span>
                    </div>
                    <h2 class="announcement-title">
                        <span style="color: var(--danger);">🔴 URGENT:</span> Midterm Examination Schedule Released
                    </h2>
                    <p class="announcement-body">
                        Please be informed that the official midterm examination timetable for the First Semester of Academic Year 2026-2027 is now published. Examinations will run from October 20 through October 24, 2026. Students must verify their individual course slots, room assignments, and proctor assignments below. Please make sure to bring your valid Student ID and test permits on examination days.
                    </p>
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                        <a href="#" class="attachment-chip" onclick="showToast('Downloading Midterm_Schedule_2026.pdf...', 'info'); return false;">
                            <i class="fa-solid fa-file-pdf" style="color: var(--danger); font-size:1.1rem;"></i>
                            <span>Midterm_Schedule_2026.pdf (1.8 MB)</span>
                            <i class="fa-solid fa-download" style="margin-left: 6px; font-size: 0.8rem;"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-primary" onclick="showToast('Added exam dates to your calendar.', 'success')">
                            <i class="fa-regular fa-calendar-plus"></i> Add to My Schedule
                        </button>
                    </div>
                </article>

                <!-- Announcement 2: Library Hours -->
                <article class="announcement-card announcement-item" data-category="administrative">
                    <div class="announcement-meta">
                        <span><i class="fa-solid fa-book-open-reader"></i> University Library</span>
                        <span>•</span>
                        <span><i class="fa-regular fa-calendar"></i> October 10, 2026</span>
                        <span>•</span>
                        <span class="badge badge-secondary">Administrative</span>
                    </div>
                    <h2 class="announcement-title">
                        <span>📢</span> Library Extended Hours During Midterms
                    </h2>
                    <p class="announcement-body">
                        To support your study and revision periods, the Main University Library and Science Learning Hub will extend their service hours starting October 20 to October 31, 2026. Operating hours will be from <strong>7:00 AM until 12:00 Midnight</strong> daily, including Saturdays and Sundays. Quiet study pods, group study rooms, and overnight laptop charging stations will be readily accessible.
                    </p>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <span class="badge badge-outline"><i class="fa-solid fa-clock"></i> 7:00 AM – 12:00 MN</span>
                        <span class="badge badge-outline"><i class="fa-solid fa-wifi"></i> 24/7 Campus Wi-Fi Active</span>
                    </div>
                </article>

                <!-- Announcement 3: Career Fair 2026 -->
                <article class="announcement-card announcement-item" data-category="events">
                    <div class="announcement-meta">
                        <span><i class="fa-solid fa-briefcase"></i> Career Development Office</span>
                        <span>•</span>
                        <span><i class="fa-regular fa-calendar"></i> October 9, 2026</span>
                        <span>•</span>
                        <span class="badge badge-warning">Events</span>
                    </div>
                    <h2 class="announcement-title">
                        <span>🎉</span> Career Fair 2026 - Save the Date!
                    </h2>
                    <p class="announcement-body">
                        Get ready to launch your career! EduPortal Career Fair 2026 is scheduled for <strong>October 25, 2026</strong> at the University Gymnasium from 9:00 AM to 5:00 PM. Over 50+ multinational companies and fast-growing tech startups will be conducting on-site interviews, internship recruitment, and professional networking sessions. Bring multiple copies of your resume and dress in business casual attire!
                    </p>
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                        <div style="display: flex; gap: 8px;">
                            <span class="badge badge-outline"><i class="fa-solid fa-location-dot"></i> University Gymnasium</span>
                            <span class="badge badge-outline"><i class="fa-solid fa-building"></i> 50+ Tech & Finance Companies</span>
                        </div>
                        <a href="../../pages/student-life/events.php" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-arrow-right"></i> View in Events
                        </a>
                    </div>
                </article>

                <!-- Announcement 4: Scholarships -->
                <article class="announcement-card announcement-item" data-category="administrative">
                    <div class="announcement-meta">
                        <span><i class="fa-solid fa-hand-holding-dollar"></i> Financial Aid Office</span>
                        <span>•</span>
                        <span><i class="fa-regular fa-calendar"></i> October 7, 2026</span>
                        <span>•</span>
                        <span class="badge badge-success">Financial</span>
                        <span class="badge badge-secondary">Administrative</span>
                    </div>
                    <h2 class="announcement-title">
                        <span>💰</span> New Scholarship Opportunities Available
                    </h2>
                    <p class="announcement-body">
                        The Financial Aid Office is pleased to announce open applications for the <strong>STEM Academic Excellence Grant</strong> and the <strong>Tech Innovators Merit Scholarship</strong> for the second term. Qualified students can receive up to 100% tuition coverage plus a monthly living stipend. Application deadline is October 31, 2026.
                    </p>
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                        <a href="../../pages/admin/scholarships.php" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-graduation-cap"></i> View Scholarship Criteria & Apply
                        </a>
                        <span style="font-size:0.8rem; color:var(--gray-500);">
                            <i class="fa-regular fa-clock"></i> Deadline: Oct 31, 2026
                        </span>
                    </div>
                </article>

                <!-- Announcement 5: Dean's List -->
                <article class="announcement-card announcement-item" data-category="academic">
                    <div class="announcement-meta">
                        <span><i class="fa-solid fa-award"></i> Office of the Dean</span>
                        <span>•</span>
                        <span><i class="fa-regular fa-calendar"></i> October 5, 2026</span>
                        <span>•</span>
                        <span class="badge badge-primary">Academic</span>
                    </div>
                    <h2 class="announcement-title">
                        <span>🏆</span> Congratulations Dean's Listers!
                    </h2>
                    <p class="announcement-body">
                        The College of Information Technology and Engineering congratulates our outstanding scholars who made it to the Dean's Honor Roll for the preceding term! Your relentless passion for excellence and academic discipline inspire the entire EduPortal community. Certificates of recognition may be collected from the Dean's Office.
                    </p>
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                        <span class="badge badge-success"><i class="fa-solid fa-medal"></i> 142 Honorees</span>
                    </div>
                </article>
            </div>
        </main>
    </div>

    <script src="../../js/main.js?v=9"></script>
    <script>
        // Live search filter on announcements
        const searchInput = document.getElementById('announcement-search');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                document.querySelectorAll('.announcement-item').forEach(item => {
                    const text = item.innerText.toLowerCase();
                    if (text.includes(query)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    </script>
</body>
</html>
