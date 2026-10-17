<?php
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/student-life/events.php';
$user = require_page_access($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Calendar - EduPortal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <?php app_shell_styles(); ?>
</head>
<body>

<?php app_sidebar($user, $page); ?>

    <!-- Main Content -->
    <div class="main-content app-main">
<?php app_topbar($user, ['placeholder' => 'Search events, workshops, festivals...']); ?>

        <!-- Page Content -->
        <main class="content-area">
            <div class="page-header-row">
                <div class="page-header">
                    <h1>Events Calendar</h1>
                    <p class="page-subtitle">Discover what's happening on campus and never miss out on key activities</p>
                </div>

                <!-- View Toggle Buttons -->
                <div style="display: flex; background: var(--white); border: 1px solid var(--gray-300); border-radius: var(--radius-sm); padding: 3px;">
                    <button class="btn btn-sm btn-outline cal-view-btn active" data-view="calendar" style="border:none;">
                        <i class="fa-solid fa-calendar-days"></i> Calendar
                    </button>
                    <button class="btn btn-sm btn-outline cal-view-btn" data-view="list" style="border:none;">
                        <i class="fa-solid fa-list-ul"></i> List
                    </button>
                    <button class="btn btn-sm btn-outline cal-view-btn" data-view="card" style="border:none;">
                        <i class="fa-solid fa-table-cells-large"></i> Cards
                    </button>
                </div>
            </div>

            <!-- Month Header & Controls -->
            <div class="calendar-view-header">
                <div class="month-nav">
                    <button class="month-nav-btn" onclick="showToast('Showing September 2026 archive', 'info')">
                        <i class="fa-solid fa-chevron-left"></i> Sep
                    </button>
                    <span>October 2026</span>
                    <button class="month-nav-btn" onclick="showToast('Showing November 2026 schedule', 'info')">
                        Nov <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>

                <!-- Category Filters -->
                <div class="filter-tabs" data-filter-group data-filter-target=".event-card-item" style="margin-bottom: 0;">
                    <button class="filter-tab active" data-filter="all">All</button>
                    <button class="filter-tab" data-filter="academic">Academic</button>
                    <button class="filter-tab" data-filter="sports">Sports</button>
                    <button class="filter-tab" data-filter="cultural">Cultural</button>
                    <button class="filter-tab" data-filter="social">Social</button>
                    <button class="filter-tab" data-filter="career">Career</button>
                </div>
            </div>

            <!-- View 1: Calendar Grid -->
            <div id="calendar-grid-view">
                <div class="calendar-grid">
                    <div class="calendar-weekdays">
                        <div>Sun</div>
                        <div>Mon</div>
                        <div>Tue</div>
                        <div>Wed</div>
                        <div>Thu</div>
                        <div>Fri</div>
                        <div>Sat</div>
                    </div>

                    <div class="calendar-days">
                        <!-- Week 1 (Oct 2026 starts on Thu Oct 1) -->
                        <div class="calendar-cell muted"><span class="day-num">27</span></div>
                        <div class="calendar-cell muted"><span class="day-num">28</span></div>
                        <div class="calendar-cell muted"><span class="day-num">29</span></div>
                        <div class="calendar-cell muted"><span class="day-num">30</span></div>
                        <div class="calendar-cell"><span class="day-num">1</span></div>
                        <div class="calendar-cell"><span class="day-num">2</span></div>
                        <div class="calendar-cell"><span class="day-num">3</span></div>

                        <!-- Week 2 -->
                        <div class="calendar-cell"><span class="day-num">4</span></div>
                        <div class="calendar-cell"><span class="day-num">5</span></div>
                        <div class="calendar-cell"><span class="day-num">6</span></div>
                        <div class="calendar-cell"><span class="day-num">7</span></div>
                        <div class="calendar-cell"><span class="day-num">8</span></div>
                        <div class="calendar-cell"><span class="day-num">9</span></div>
                        <div class="calendar-cell"><span class="day-num">10</span></div>

                        <!-- Week 3: Oct 13 is Today -->
                        <div class="calendar-cell"><span class="day-num">11</span></div>
                        <div class="calendar-cell"><span class="day-num">12</span></div>
                        <div class="calendar-cell today">
                            <span class="day-num">13</span>
                            <span class="cal-event-pill cal-pill-primary" style="font-size:0.62rem;">TODAY</span>
                        </div>
                        <div class="calendar-cell"><span class="day-num">14</span></div>
                        <div class="calendar-cell">
                            <span class="day-num">15</span>
                            <span class="cal-event-pill cal-pill-primary" title="Tech Talk: AI in Education">
                                🤖 Tech Talk: AI
                            </span>
                        </div>
                        <div class="calendar-cell"><span class="day-num">16</span></div>
                        <div class="calendar-cell"><span class="day-num">17</span></div>

                        <!-- Week 4 -->
                        <div class="calendar-cell">
                            <span class="day-num">18</span>
                            <span class="cal-event-pill cal-pill-warning" title="Basketball: EduPortal vs State U">
                                🏀 EduPortal vs State
                            </span>
                        </div>
                        <div class="calendar-cell"><span class="day-num">19</span></div>
                        <div class="calendar-cell">
                            <span class="day-num">20</span>
                            <span class="cal-event-pill cal-pill-success" title="Cultural Night: Music Fest">
                                🎵 Cultural Night
                            </span>
                        </div>
                        <div class="calendar-cell"><span class="day-num">21</span></div>
                        <div class="calendar-cell">
                            <span class="day-num">22</span>
                            <span class="cal-event-pill cal-pill-primary" title="Resume Writing Workshop">
                                📝 Resume Workshop
                            </span>
                        </div>
                        <div class="calendar-cell"><span class="day-num">23</span></div>
                        <div class="calendar-cell"><span class="day-num">24</span></div>

                        <!-- Week 5 -->
                        <div class="calendar-cell">
                            <span class="day-num">25</span>
                            <span class="cal-event-pill cal-pill-primary" title="Career Fair 2026">
                                💼 Career Fair 2026
                            </span>
                        </div>
                        <div class="calendar-cell"><span class="day-num">26</span></div>
                        <div class="calendar-cell"><span class="day-num">27</span></div>
                        <div class="calendar-cell">
                            <span class="day-num">28</span>
                            <span class="cal-event-pill cal-pill-danger" title="Halloween Party">
                                🎃 Halloween Bash
                            </span>
                        </div>
                        <div class="calendar-cell"><span class="day-num">29</span></div>
                        <div class="calendar-cell"><span class="day-num">30</span></div>
                        <div class="calendar-cell">
                            <span class="day-num">31</span>
                            <span class="cal-event-pill cal-pill-warning" title="Deadline: Scholarship Applications">
                                ⚠️ Scholarship Due
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View 2: Upcoming Events List (Also default display underneath calendar) -->
            <div id="calendar-list-view">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 18px;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--dark);">Upcoming Events</h2>
                    <span style="font-size: 0.82rem; color: var(--gray-500);">7 Events Scheduled for October</span>
                </div>

                <div class="events-list-container">
                    <!-- Event 1 -->
                    <div class="event-card event-card-item" data-category="academic">
                        <div class="event-date-badge">
                            <span class="month">Oct</span>
                            <span class="day">15</span>
                        </div>
                        <div style="flex:1;">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                                <span class="badge badge-primary">Academic</span>
                                <span style="font-size: 0.78rem; color: var(--success); font-weight: 600;">Free Admission</span>
                            </div>
                            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 4px;">
                                Tech Talk: AI in Education
                            </h3>
                            <div style="font-size: 0.85rem; color: var(--gray-600); display: flex; gap: 16px; flex-wrap: wrap;">
                                <span><i class="fa-regular fa-clock text-primary"></i> 3:00 PM - 5:00 PM</span>
                                <span><i class="fa-solid fa-location-dot text-danger"></i> University Auditorium</span>
                                <span><i class="fa-solid fa-users text-secondary"></i> 142 attending</span>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-primary btn-rsvp">
                                <i class="fa-solid fa-calendar-check"></i> RSVP
                            </button>
                        </div>
                    </div>

                    <!-- Event 2 -->
                    <div class="event-card event-card-item" data-category="sports">
                        <div class="event-date-badge" style="background: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.3); color: #d97706;">
                            <span class="month">Oct</span>
                            <span class="day">18</span>
                        </div>
                        <div style="flex:1;">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                                <span class="badge badge-warning">Sports</span>
                                <span style="font-size: 0.78rem; color: var(--success); font-weight: 600;">Free Admission</span>
                            </div>
                            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 4px;">
                                Basketball: EduPortal vs State U
                            </h3>
                            <div style="font-size: 0.85rem; color: var(--gray-600); display: flex; gap: 16px; flex-wrap: wrap;">
                                <span><i class="fa-regular fa-clock text-primary"></i> 4:00 PM - 7:00 PM</span>
                                <span><i class="fa-solid fa-location-dot text-danger"></i> University Gym Main Arena</span>
                                <span><i class="fa-solid fa-users text-secondary"></i> 480 attending</span>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-primary btn-rsvp">
                                <i class="fa-solid fa-calendar-check"></i> RSVP
                            </button>
                        </div>
                    </div>

                    <!-- Event 3 -->
                    <div class="event-card event-card-item" data-category="cultural">
                        <div class="event-date-badge" style="background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.3); color: #059669;">
                            <span class="month">Oct</span>
                            <span class="day">20</span>
                        </div>
                        <div style="flex:1;">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                                <span class="badge badge-success">Cultural</span>
                                <span style="font-size: 0.78rem; color: var(--gray-700); font-weight: 600;">Fee: ₱50</span>
                            </div>
                            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 4px;">
                                Cultural Night: Music Festival
                            </h3>
                            <div style="font-size: 0.85rem; color: var(--gray-600); display: flex; gap: 16px; flex-wrap: wrap;">
                                <span><i class="fa-regular fa-clock text-primary"></i> 6:00 PM - 10:00 PM</span>
                                <span><i class="fa-solid fa-location-dot text-danger"></i> Campus Amphitheater</span>
                                <span><i class="fa-solid fa-users text-secondary"></i> 320 attending</span>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-primary btn-rsvp">
                                <i class="fa-solid fa-ticket"></i> Register (₱50)
                            </button>
                        </div>
                    </div>

                    <!-- Event 4 -->
                    <div class="event-card event-card-item" data-category="career">
                        <div class="event-date-badge">
                            <span class="month">Oct</span>
                            <span class="day">22</span>
                        </div>
                        <div style="flex:1;">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                                <span class="badge badge-primary">Career</span>
                                <span style="font-size: 0.78rem; color: var(--success); font-weight: 600;">Free Admission</span>
                            </div>
                            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 4px;">
                                Workshop: Resume Writing
                            </h3>
                            <div style="font-size: 0.85rem; color: var(--gray-600); display: flex; gap: 16px; flex-wrap: wrap;">
                                <span><i class="fa-regular fa-clock text-primary"></i> 1:00 PM - 3:00 PM</span>
                                <span><i class="fa-solid fa-location-dot text-danger"></i> Career Hall Room 301</span>
                                <span><i class="fa-solid fa-users text-secondary"></i> 65 attending</span>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-primary btn-rsvp">
                                <i class="fa-solid fa-calendar-check"></i> RSVP
                            </button>
                        </div>
                    </div>

                    <!-- Event 5 -->
                    <div class="event-card event-card-item" data-category="career">
                        <div class="event-date-badge">
                            <span class="month">Oct</span>
                            <span class="day">25</span>
                        </div>
                        <div style="flex:1;">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                                <span class="badge badge-primary">Career</span>
                                <span class="badge badge-warning">Featured</span>
                                <span style="font-size: 0.78rem; color: var(--success); font-weight: 600;">Free Admission</span>
                            </div>
                            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 4px;">
                                Career Fair 2026
                            </h3>
                            <div style="font-size: 0.85rem; color: var(--gray-600); display: flex; gap: 16px; flex-wrap: wrap;">
                                <span><i class="fa-regular fa-clock text-primary"></i> 9:00 AM - 5:00 PM</span>
                                <span><i class="fa-solid fa-location-dot text-danger"></i> University Gym</span>
                                <span><i class="fa-solid fa-users text-secondary"></i> 750 attending</span>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-primary btn-rsvp">
                                <i class="fa-solid fa-calendar-check"></i> RSVP
                            </button>
                        </div>
                    </div>

                    <!-- Event 6 -->
                    <div class="event-card event-card-item" data-category="social">
                        <div class="event-date-badge" style="background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3); color: var(--danger);">
                            <span class="month">Oct</span>
                            <span class="day">28</span>
                        </div>
                        <div style="flex:1;">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                                <span class="badge badge-danger">Social</span>
                                <span style="font-size: 0.78rem; color: var(--gray-700); font-weight: 600;">Ticket: ₱100</span>
                            </div>
                            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 4px;">
                                Halloween Party
                            </h3>
                            <div style="font-size: 0.85rem; color: var(--gray-600); display: flex; gap: 16px; flex-wrap: wrap;">
                                <span><i class="fa-regular fa-clock text-primary"></i> 7:00 PM - 11:00 PM</span>
                                <span><i class="fa-solid fa-location-dot text-danger"></i> Student Center Plaza</span>
                                <span><i class="fa-solid fa-users text-secondary"></i> 290 attending</span>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-primary btn-rsvp">
                                <i class="fa-solid fa-ticket"></i> Get Ticket (₱100)
                            </button>
                        </div>
                    </div>

                    <!-- Event 7 -->
                    <div class="event-card event-card-item" data-category="academic">
                        <div class="event-date-badge" style="background: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.3); color: #d97706;">
                            <span class="month">Oct</span>
                            <span class="day">31</span>
                        </div>
                        <div style="flex:1;">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                                <span class="badge badge-primary">Academic</span>
                                <span class="badge badge-urgent">Important Deadline</span>
                            </div>
                            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 4px;">
                                Deadline: Scholarship Applications
                            </h3>
                            <div style="font-size: 0.85rem; color: var(--gray-600); display: flex; gap: 16px; flex-wrap: wrap;">
                                <span><i class="fa-regular fa-clock text-primary"></i> 11:59 PM Online Portal</span>
                                <span><i class="fa-solid fa-location-dot text-danger"></i> Financial Aid Portal</span>
                                <span><i class="fa-solid fa-users text-secondary"></i> 210 submitted</span>
                            </div>
                        </div>
                        <div>
                            <a href="../../pages/admin/scholarships.php" class="btn btn-outline-primary">
                                <i class="fa-solid fa-file-arrow-up"></i> Submit Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View 3: Cards Grid (Hidden initially, switches with view toggle) -->
            <div id="calendar-cards-view" style="display: none;">
                <div class="grid-3">
                    <div class="card">
                        <div class="card-header">
                            <span class="badge badge-primary">Academic</span>
                            <span style="font-size:0.8rem;color:var(--gray-500);">Oct 15 • Free</span>
                        </div>
                        <div class="card-body">
                            <h3 style="font-size:1.05rem;font-weight:700;">Tech Talk: AI in Education</h3>
                            <p style="font-size:0.85rem;color:var(--gray-600);margin:8px 0 16px;">Auditorium • 3:00 PM</p>
                            <button class="btn btn-sm btn-primary btn-block btn-rsvp" style="width:100%;">RSVP</button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <span class="badge badge-warning">Sports</span>
                            <span style="font-size:0.8rem;color:var(--gray-500);">Oct 18 • Free</span>
                        </div>
                        <div class="card-body">
                            <h3 style="font-size:1.05rem;font-weight:700;">Basketball: EduPortal vs State U</h3>
                            <p style="font-size:0.85rem;color:var(--gray-600);margin:8px 0 16px;">Main Gym • 4:00 PM</p>
                            <button class="btn btn-sm btn-primary btn-block btn-rsvp" style="width:100%;">RSVP</button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <span class="badge badge-success">Cultural</span>
                            <span style="font-size:0.8rem;color:var(--gray-500);">Oct 20 • ₱50</span>
                        </div>
                        <div class="card-body">
                            <h3 style="font-size:1.05rem;font-weight:700;">Cultural Night: Music Festival</h3>
                            <p style="font-size:0.85rem;color:var(--gray-600);margin:8px 0 16px;">Amphitheater • 6:00 PM</p>
                            <button class="btn btn-sm btn-primary btn-block btn-rsvp" style="width:100%;">Register (₱50)</button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <span class="badge badge-primary">Career</span>
                            <span style="font-size:0.8rem;color:var(--gray-500);">Oct 22 • Free</span>
                        </div>
                        <div class="card-body">
                            <h3 style="font-size:1.05rem;font-weight:700;">Workshop: Resume Writing</h3>
                            <p style="font-size:0.85rem;color:var(--gray-600);margin:8px 0 16px;">Room 301 • 1:00 PM</p>
                            <button class="btn btn-sm btn-primary btn-block btn-rsvp" style="width:100%;">RSVP</button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <span class="badge badge-primary">Career</span>
                            <span style="font-size:0.8rem;color:var(--gray-500);">Oct 25 • Free</span>
                        </div>
                        <div class="card-body">
                            <h3 style="font-size:1.05rem;font-weight:700;">Career Fair 2026</h3>
                            <p style="font-size:0.85rem;color:var(--gray-600);margin:8px 0 16px;">Gymnasium • 9:00 AM</p>
                            <button class="btn btn-sm btn-primary btn-block btn-rsvp" style="width:100%;">RSVP</button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <span class="badge badge-danger">Social</span>
                            <span style="font-size:0.8rem;color:var(--gray-500);">Oct 28 • ₱100</span>
                        </div>
                        <div class="card-body">
                            <h3 style="font-size:1.05rem;font-weight:700;">Halloween Party</h3>
                            <p style="font-size:0.85rem;color:var(--gray-600);margin:8px 0 16px;">Student Center • 7:00 PM</p>
                            <button class="btn btn-sm btn-primary btn-block btn-rsvp" style="width:100%;">Get Ticket (₱100)</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../js/main.js?v=9"></script>
</body>
</html>
