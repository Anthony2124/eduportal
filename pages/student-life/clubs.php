<?php
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/student-life/clubs.php';
$user = require_page_access($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clubs & Organizations - EduPortal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <?php app_shell_styles(); ?>
</head>
<body>

<?php app_sidebar($user, $page); ?>

    <!-- Main Content -->
    <div class="main-content app-main">
<?php app_topbar($user, ['id' => 'club-search-input', 'placeholder' => 'Search clubs, societies, sports, or interests...']); ?>

        <!-- Page Content -->
        <main class="content-area">
            <div class="page-header-row">
                <div class="page-header">
                    <h1>Clubs & Organizations</h1>
                    <p class="page-subtitle">Find your community, build leadership skills, and pursue your passions</p>
                </div>
                <button class="btn btn-outline" onclick="showToast('Applications for new student clubs open next month.', 'info')">
                    <i class="fa-solid fa-plus"></i> Register New Club
                </button>
            </div>

            <!-- Category Filters -->
            <div class="filter-tabs" data-filter-group data-filter-target=".club-dir-item">
                <button class="filter-tab active" data-filter="all">All</button>
                <button class="filter-tab" data-filter="academic">Academic</button>
                <button class="filter-tab" data-filter="sports">Sports</button>
                <button class="filter-tab" data-filter="arts">Arts</button>
                <button class="filter-tab" data-filter="service">Service</button>
                <button class="filter-tab" data-filter="technology">Technology</button>
            </div>

            <!-- My Clubs Section -->
            <div style="margin-bottom: 32px;">
                <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom: 16px;">
                    <h2 style="font-size: 1.2rem; font-weight: 700; color: var(--dark);">My Clubs</h2>
                    <span class="badge badge-primary">2 Active Memberships</span>
                </div>

                <div class="grid-2">
                    <!-- My Club 1: GDSC -->
                    <div class="club-card" style="border-left: 4px solid var(--primary);">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div class="club-icon" style="background: linear-gradient(135deg, #4285F4, #34A853);">
                                    <i class="fa-solid fa-laptop-code"></i>
                                </div>
                                <span class="badge badge-success"><i class="fa-solid fa-check"></i> Active Member</span>
                            </div>
                            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--dark); margin-bottom: 4px;">
                                Google Developer Student Club
                            </h3>
                            <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                                <span class="badge badge-primary">Tech</span>
                                <span style="font-size: 0.8rem; color: var(--gray-500); align-self: center;">
                                    Member since 2025 • 120 members
                                </span>
                            </div>
                            <p style="font-size: 0.85rem; color: var(--gray-600); line-height: 1.5; margin-bottom: 16px;">
                                A peer-to-peer university learning community focused on Google developer technologies, open-source projects, and student hackathons.
                            </p>
                        </div>
                        <div style="display:flex; gap: 10px;">
                            <a href="../../pages/communication/messaging.php" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-comments"></i> Group Chat
                            </a>
                            <button class="btn btn-sm btn-outline" onclick="showToast('GDSC meetups occur every Wednesday 5 PM in Lab 3.', 'info')">
                                View Schedule
                            </button>
                        </div>
                    </div>

                    <!-- My Club 2: Chess Club -->
                    <div class="club-card" style="border-left: 4px solid #F59E0B;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div class="club-icon" style="background: linear-gradient(135deg, #F59E0B, #B45309);">
                                    <i class="fa-solid fa-chess"></i>
                                </div>
                                <span class="badge badge-success"><i class="fa-solid fa-check"></i> Active Member</span>
                            </div>
                            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--dark); margin-bottom: 4px;">
                                Chess Club
                            </h3>
                            <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                                <span class="badge badge-warning">Recreation</span>
                                <span style="font-size: 0.8rem; color: var(--gray-500); align-self: center;">
                                    Member since 2026 • 45 members
                                </span>
                            </div>
                            <p style="font-size: 0.85rem; color: var(--gray-600); line-height: 1.5; margin-bottom: 16px;">
                                Tactical strategy, friendly campus blitz tournaments, and analytical game reviews. Open to all skill levels from novices to masters.
                            </p>
                        </div>
                        <div style="display:flex; gap: 10px;">
                            <button class="btn btn-sm btn-primary" onclick="showToast('Next campus chess tournament scheduled for Oct 24!', 'info')">
                                <i class="fa-solid fa-trophy"></i> Tournaments
                            </button>
                            <button class="btn btn-sm btn-outline" onclick="showToast('Chess room open MWF 3-6 PM in Student Lounge B.', 'info')">
                                Club Room
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- All Clubs Directory Grid -->
            <div>
                <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom: 16px;">
                    <h2 style="font-size: 1.2rem; font-weight: 700; color: var(--dark);">All Clubs Directory</h2>
                    <span style="font-size: 0.82rem; color: var(--gray-500);">9 Recognized Student Organizations</span>
                </div>

                <div class="grid-3" id="all-clubs-grid">
                    <!-- Club 1: CS Society -->
                    <div class="club-card club-dir-item" data-category="academic technology">
                        <div>
                            <div class="club-icon" style="background: linear-gradient(135deg, #4F46E5, #3730A3);">
                                <i class="fa-solid fa-terminal"></i>
                            </div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--dark);">Computer Science Society</h3>
                            <div style="margin: 6px 0 10px; display:flex; align-items:center; gap:8px;">
                                <span class="badge badge-primary">Academic</span>
                                <span style="font-size:0.78rem; color:var(--gray-500);"><i class="fa-solid fa-users"></i> 200 members</span>
                            </div>
                            <p style="font-size: 0.85rem; color: var(--gray-600); line-height: 1.45; margin-bottom: 18px;">
                                The premier academic association for CS majors providing technical seminars, coding challenges, and corporate networking.
                            </p>
                        </div>
                        <button class="btn btn-primary btn-sm btn-join-club" style="width: 100%;">Join</button>
                    </div>

                    <!-- Club 2: Basketball Team -->
                    <div class="club-card club-dir-item" data-category="sports">
                        <div>
                            <div class="club-icon" style="background: linear-gradient(135deg, #EA580C, #C2410C);">
                                <i class="fa-solid fa-basketball"></i>
                            </div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--dark);">Basketball Team</h3>
                            <div style="margin: 6px 0 10px; display:flex; align-items:center; gap:8px;">
                                <span class="badge badge-warning">Sports</span>
                                <span style="font-size:0.78rem; color:var(--gray-500);"><i class="fa-solid fa-users"></i> 25 members</span>
                            </div>
                            <p style="font-size: 0.85rem; color: var(--gray-600); line-height: 1.45; margin-bottom: 18px;">
                                Official varsity and developmental squad competing in university athletic leagues and inter-collegiate tournaments.
                            </p>
                        </div>
                        <button class="btn btn-outline-primary btn-sm" onclick="showToast('Varsity tryouts: Oct 20 at 5:00 PM in the Gym. Bring gym attire and medical certificate.', 'info')" style="width: 100%;">
                            <i class="fa-solid fa-circle-info"></i> Tryout Info
                        </button>
                    </div>

                    <!-- Club 3: Photography Club -->
                    <div class="club-card club-dir-item" data-category="arts">
                        <div>
                            <div class="club-icon" style="background: linear-gradient(135deg, #EC4899, #BE185D);">
                                <i class="fa-solid fa-camera-retro"></i>
                            </div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--dark);">Photography Club</h3>
                            <div style="margin: 6px 0 10px; display:flex; align-items:center; gap:8px;">
                                <span class="badge badge-secondary">Arts</span>
                                <span style="font-size:0.78rem; color:var(--gray-500);"><i class="fa-solid fa-users"></i> 65 members</span>
                            </div>
                            <p style="font-size: 0.85rem; color: var(--gray-600); line-height: 1.45; margin-bottom: 18px;">
                                Capturing memories across campus through visual storytelling, photowalks, photo editing workshops, and annual exhibits.
                            </p>
                        </div>
                        <button class="btn btn-primary btn-sm btn-join-club" style="width: 100%;">Join</button>
                    </div>

                    <!-- Club 4: Red Cross Youth -->
                    <div class="club-card club-dir-item" data-category="service">
                        <div>
                            <div class="club-icon" style="background: linear-gradient(135deg, #DC2626, #991B1B);">
                                <i class="fa-solid fa-hand-holding-heart"></i>
                            </div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--dark);">Red Cross Youth</h3>
                            <div style="margin: 6px 0 10px; display:flex; align-items:center; gap:8px;">
                                <span class="badge badge-danger">Service</span>
                                <span style="font-size:0.78rem; color:var(--gray-500);"><i class="fa-solid fa-users"></i> 80 members</span>
                            </div>
                            <p style="font-size: 0.85rem; color: var(--gray-600); line-height: 1.45; margin-bottom: 18px;">
                                Dedicated to humanitarian aid, first aid safety certifications, disaster preparedness training, and university blood drives.
                            </p>
                        </div>
                        <button class="btn btn-primary btn-sm btn-join-club" style="width: 100%;">Join</button>
                    </div>

                    <!-- Club 5: Robotics Club -->
                    <div class="club-card club-dir-item" data-category="technology">
                        <div>
                            <div class="club-icon" style="background: linear-gradient(135deg, #0284C7, #0369A1);">
                                <i class="fa-solid fa-microchip"></i>
                            </div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--dark);">Robotics Club</h3>
                            <div style="margin: 6px 0 10px; display:flex; align-items:center; gap:8px;">
                                <span class="badge badge-primary">Technology</span>
                                <span style="font-size:0.78rem; color:var(--gray-500);"><i class="fa-solid fa-users"></i> 55 members</span>
                            </div>
                            <p style="font-size: 0.85rem; color: var(--gray-600); line-height: 1.45; margin-bottom: 18px;">
                                Designing autonomous bots, IoT hardware prototypes, drone telemetry systems, and competing in national robotics derbies.
                            </p>
                        </div>
                        <button class="btn btn-primary btn-sm btn-join-club" style="width: 100%;">Join</button>
                    </div>

                    <!-- Club 6: Drama & Theater Arts -->
                    <div class="club-card club-dir-item" data-category="arts">
                        <div>
                            <div class="club-icon" style="background: linear-gradient(135deg, #9333EA, #7E22CE);">
                                <i class="fa-solid fa-masks-theater"></i>
                            </div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--dark);">Drama & Theater Arts</h3>
                            <div style="margin: 6px 0 10px; display:flex; align-items:center; gap:8px;">
                                <span class="badge badge-secondary">Arts</span>
                                <span style="font-size:0.78rem; color:var(--gray-500);"><i class="fa-solid fa-users"></i> 40 members</span>
                            </div>
                            <p style="font-size: 0.85rem; color: var(--gray-600); line-height: 1.45; margin-bottom: 18px;">
                                Bringing performing arts to life with stage plays, musicals, voice acting clinics, and production set design.
                            </p>
                        </div>
                        <button class="btn btn-primary btn-sm btn-join-club" style="width: 100%;">Join</button>
                    </div>

                    <!-- Club 7: Environmental Society -->
                    <div class="club-card club-dir-item" data-category="service">
                        <div>
                            <div class="club-icon" style="background: linear-gradient(135deg, #10B981, #047857);">
                                <i class="fa-solid fa-seedling"></i>
                            </div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--dark);">Environmental Society</h3>
                            <div style="margin: 6px 0 10px; display:flex; align-items:center; gap:8px;">
                                <span class="badge badge-success">Service</span>
                                <span style="font-size:0.78rem; color:var(--gray-500);"><i class="fa-solid fa-users"></i> 70 members</span>
                            </div>
                            <p style="font-size: 0.85rem; color: var(--gray-600); line-height: 1.45; margin-bottom: 18px;">
                                Championing environmental sustainability, campus zero-waste recycling programs, tree planting, and ecological education.
                            </p>
                        </div>
                        <button class="btn btn-primary btn-sm btn-join-club" style="width: 100%;">Join</button>
                    </div>

                    <!-- Club 8: E-Sports Club -->
                    <div class="club-card club-dir-item" data-category="technology sports">
                        <div>
                            <div class="club-icon" style="background: linear-gradient(135deg, #6366F1, #4338CA);">
                                <i class="fa-solid fa-gamepad"></i>
                            </div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--dark);">E-Sports Club</h3>
                            <div style="margin: 6px 0 10px; display:flex; align-items:center; gap:8px;">
                                <span class="badge badge-warning">Recreation</span>
                                <span style="font-size:0.78rem; color:var(--gray-500);"><i class="fa-solid fa-users"></i> 150 members</span>
                            </div>
                            <p style="font-size: 0.85rem; color: var(--gray-600); line-height: 1.45; margin-bottom: 18px;">
                                Competitive collegiate gaming in Valorant, League of Legends, Dota 2, and Mobile Legends with streaming setups.
                            </p>
                        </div>
                        <button class="btn btn-primary btn-sm btn-join-club" style="width: 100%;">Join</button>
                    </div>

                    <!-- Club 9: Debate Society -->
                    <div class="club-card club-dir-item" data-category="academic">
                        <div>
                            <div class="club-icon" style="background: linear-gradient(135deg, #0D9488, #115E59);">
                                <i class="fa-solid fa-scale-balanced"></i>
                            </div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--dark);">Debate Society</h3>
                            <div style="margin: 6px 0 10px; display:flex; align-items:center; gap:8px;">
                                <span class="badge badge-primary">Academic</span>
                                <span style="font-size:0.78rem; color:var(--gray-500);"><i class="fa-solid fa-users"></i> 35 members</span>
                            </div>
                            <p style="font-size: 0.85rem; color: var(--gray-600); line-height: 1.45; margin-bottom: 18px;">
                                Enhancing parliamentary argumentation, oratory rhetoric, critical reasoning, and competing at inter-collegiate tournaments.
                            </p>
                        </div>
                        <button class="btn btn-primary btn-sm btn-join-club" style="width: 100%;">Join</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../js/main.js?v=9"></script>
    <script>
        // Real-time search filter for clubs
        const clubSearch = document.getElementById('club-search-input');
        if (clubSearch) {
            clubSearch.addEventListener('input', (e) => {
                const q = e.target.value.toLowerCase().trim();
                document.querySelectorAll('.club-dir-item').forEach(item => {
                    const text = item.innerText.toLowerCase();
                    if (text.includes(q)) {
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
