<?php
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/academics/schedule.php';
$user = require_page_access($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Schedule - EduPortal</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Main Style -->
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        /* Design System Variables */
        :root {
            --primary: #4F46E5;
            --primary-light: #818CF8;
            --primary-dark: #3730A3;
            --secondary: #06B6D4;
            --accent: #F59E0B;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
            --dark: #1E293B;
            --gray-700: #334155;
            --gray-600: #475569;
            --gray-500: #64748B;
            --gray-400: #94A3B8;
            --gray-300: #CBD5E1;
            --gray-200: #E2E8F0;
            --gray-100: #F1F5F9;
            --light: #F8FAFC;
            --white: #FFFFFF;
            --sidebar-bg: #1E293B;
            --sidebar-text: #CBD5E1;
            --sidebar-active: #4F46E5;
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 16px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            --transition: all 0.25s ease-in-out;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--light);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            transition: var(--transition);
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--white);
            text-decoration: none;
        }

        .sidebar-brand i {
            color: var(--primary-light);
            font-size: 1.5rem;
        }

        .sidebar-close-btn {
            display: none;
            background: none;
            border: none;
            color: var(--gray-400);
            font-size: 1.2rem;
            cursor: pointer;
        }

        .sidebar-nav {
            padding: 16px 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .nav-section-title {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--gray-500);
            padding: 16px 12px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .nav-item i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
            color: var(--gray-400);
            transition: var(--transition);
        }

        .nav-item:hover {
            background-color: rgba(255,255,255,0.06);
            color: var(--white);
        }

        .nav-item:hover i {
            color: var(--white);
        }

        .nav-item.active {
            background-color: var(--sidebar-active);
            color: var(--white);
            font-weight: 600;
        }

        .nav-item.active i {
            color: var(--white);
        }

        /* Main Content Structure */
        .main-content {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: calc(100% - 260px);
            transition: var(--transition);
        }

        /* Top Navigation */
        .top-nav {
            height: 70px;
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .top-nav-left {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
            max-width: 500px;
        }

        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--gray-700);
            cursor: pointer;
            padding: 4px;
        }

        .search-bar {
            position: relative;
            width: 100%;
        }

        .search-bar i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 0.95rem;
        }

        .search-bar input {
            width: 100%;
            padding: 10px 14px 10px 40px;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            font-size: 0.9rem;
            background-color: var(--gray-100);
            color: var(--dark);
            outline: none;
            transition: var(--transition);
        }

        .search-bar input:focus {
            background-color: var(--white);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        .top-nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-btn {
            position: relative;
            background: none;
            border: none;
            color: var(--gray-600);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 6px;
            border-radius: 50%;
            transition: var(--transition);
        }

        .notification-btn:hover {
            color: var(--primary);
            background-color: var(--gray-100);
        }

        .notification-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background-color: var(--danger);
            color: var(--white);
            font-size: 0.65rem;
            font-weight: 700;
            height: 18px;
            width: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--white);
        }

        .user-profile-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: var(--radius-md);
            transition: var(--transition);
        }

        .user-avatar-wrapper {
            position: relative;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-light);
        }

        .user-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .user-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--dark);
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        /* Content Area */
        .content-area {
            padding: 32px;
            flex: 1;
        }

        .schedule-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 1.85rem;
            font-weight: 700;
            color: var(--dark);
            letter-spacing: -0.02em;
        }

        .page-subtitle {
            font-size: 0.95rem;
            color: var(--gray-500);
            margin-top: 4px;
        }

        /* Schedule Controls Toolbar */
        .schedule-toolbar {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            padding: 14px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            box-shadow: var(--shadow-sm);
        }

        /* View Toggle */
        .view-pill-group {
            display: flex;
            background-color: var(--gray-100);
            padding: 4px;
            border-radius: var(--radius-sm);
        }

        .view-pill {
            background: none;
            border: none;
            padding: 6px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-600);
            border-radius: 4px;
            cursor: pointer;
            transition: var(--transition);
        }

        .view-pill.active {
            background: var(--white);
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }

        /* Week Navigation */
        .week-nav {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-arrow-btn {
            background: var(--white);
            border: 1px solid var(--gray-200);
            width: 34px;
            height: 34px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-600);
            cursor: pointer;
            transition: var(--transition);
        }

        .nav-arrow-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .current-week-label {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--dark);
            min-width: 190px;
            text-align: center;
        }

        .btn-today {
            background: var(--gray-100);
            border: 1px solid var(--gray-200);
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--gray-700);
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-today:hover {
            background: var(--white);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Export Calendar Action */
        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-export:hover {
            background: var(--primary-dark);
        }

        /* Main Schedule Container: Grid + Sidebar */
        .schedule-layout-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 24px;
            align-items: start;
        }

        /* Weekly Schedule Table Card */
        .timetable-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .timetable-scroll {
            width: 100%;
            overflow-x: auto;
        }

        .timetable-grid {
            min-width: 720px;
            display: grid;
            grid-template-columns: 75px repeat(6, 1fr);
            border-collapse: collapse;
        }

        /* Grid Header */
        .day-header {
            background: var(--gray-100);
            padding: 14px 10px;
            text-align: center;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--gray-700);
            border-bottom: 2px solid var(--gray-200);
            border-right: 1px solid var(--gray-200);
        }

        .day-header.today {
            background: rgba(79, 70, 229, 0.08);
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
        }

        .day-name {
            display: block;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.05em;
        }

        .day-date {
            display: block;
            font-size: 1.05rem;
            font-weight: 800;
            margin-top: 2px;
        }

        /* Time Column and Grid Rows */
        .time-col-header {
            background: var(--gray-100);
            border-bottom: 2px solid var(--gray-200);
            border-right: 1px solid var(--gray-200);
        }

        .time-slot-label {
            height: 64px;
            padding: 8px 6px;
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--gray-400);
            text-align: center;
            border-bottom: 1px solid var(--gray-200);
            border-right: 1px solid var(--gray-200);
            background: var(--white);
        }

        .schedule-cell {
            height: 64px;
            border-bottom: 1px solid var(--gray-200);
            border-right: 1px solid var(--gray-200);
            position: relative;
            background: var(--white);
            padding: 3px;
        }

        .schedule-cell.today-col {
            background-color: rgba(248, 250, 252, 0.6);
        }

        /* Event Blocks */
        .class-block {
            position: absolute;
            top: 2px;
            left: 3px;
            right: 3px;
            border-radius: var(--radius-sm);
            padding: 6px 8px;
            color: var(--white);
            font-size: 0.78rem;
            overflow: hidden;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .class-block:hover {
            transform: scale(1.02);
            box-shadow: var(--shadow-md);
            z-index: 20;
        }

        /* Block Heights & Colors */
        .block-1hr { height: 58px; }
        .block-1-5hr { height: 90px; }
        .block-3hr { height: 184px; }

        /* Block Color Themes */
        .block-indigo {
            background: linear-gradient(135deg, #4F46E5, #4338CA);
            border-left: 4px solid #818CF8;
        }

        .block-cyan {
            background: linear-gradient(135deg, #0891B2, #0E7490);
            border-left: 4px solid #67E8F9;
        }

        .block-amber {
            background: linear-gradient(135deg, #D97706, #B45309);
            border-left: 4px solid #FCD34D;
        }

        .block-rose {
            background: linear-gradient(135deg, #E11D48, #BE123C);
            border-left: 4px solid #FDA4AF;
        }

        .block-emerald {
            background: linear-gradient(135deg, #059669, #047857);
            border-left: 4px solid #6EE7B7;
        }

        .block-purple {
            background: linear-gradient(135deg, #7C3AED, #6D28D9);
            border-left: 4px solid #C4B5FD;
        }

        .block-title {
            font-weight: 700;
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }

        .block-meta {
            font-size: 0.7rem;
            opacity: 0.95;
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-top: 2px;
        }

        .block-meta span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Today's Classes Sidebar */
        .today-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .today-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 22px;
            box-shadow: var(--shadow-sm);
        }

        .today-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--gray-200);
        }

        .today-card-header h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
        }

        .today-badge {
            font-size: 0.72rem;
            font-weight: 700;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            padding: 3px 8px;
            border-radius: 12px;
        }

        /* Timeline Items */
        .timeline-container {
            position: relative;
            padding-left: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .timeline-container::before {
            content: '';
            position: absolute;
            left: 6px;
            top: 6px;
            bottom: 6px;
            width: 2px;
            background: var(--gray-200);
        }

        .timeline-item {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .timeline-dot {
            position: absolute;
            left: -20px;
            top: 4px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--white);
            border: 3px solid var(--gray-400);
        }

        .timeline-item.completed .timeline-dot {
            border-color: var(--success);
            background: var(--success);
        }

        .timeline-item.active-now .timeline-dot {
            border-color: var(--danger);
            background: var(--danger);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.2);
            animation: pulseDot 1.5s infinite;
        }

        @keyframes pulseDot {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        .timeline-time {
            font-size: 0.76rem;
            font-weight: 600;
            color: var(--gray-500);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge-now {
            background: var(--danger);
            color: var(--white);
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: 0.04em;
        }

        .timeline-course-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--dark);
        }

        .timeline-details {
            font-size: 0.8rem;
            color: var(--gray-600);
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .timeline-details span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Legend Quick Card */
        .legend-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow-sm);
        }

        .legend-card h4 {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 12px;
        }

        .course-legend-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .legend-row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.82rem;
            color: var(--gray-700);
        }

        .legend-color-box {
            width: 14px;
            height: 14px;
            border-radius: 4px;
            flex-shrink: 0;
        }

        /* Class Details Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-card {
            background: var(--white);
            width: 100%;
            max-width: 520px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            animation: modalFadeIn 0.25s ease;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: var(--gray-400);
            cursor: pointer;
            padding: 6px;
            border-radius: var(--radius-sm);
        }

        .modal-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .modal-footer {
            padding: 16px 24px;
            background: var(--light);
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1100px) {
            .schedule-layout-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-close-btn {
                display: block;
            }
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            .sidebar-toggle {
                display: block;
            }
            .top-nav {
                padding: 0 16px;
            }
            .content-area {
                padding: 20px 16px;
            }
        }

        @media (max-width: 600px) {
            .schedule-toolbar {
                flex-direction: column;
                align-items: stretch;
            }
            .week-nav {
                justify-content: space-between;
            }
            .current-week-label {
                min-width: auto;
            }
            .user-info {
                display: none;
            }
        }
    </style>
    <?php app_shell_styles(); ?>
</head>
<body>
<?php app_sidebar($user, $page); ?>

    <!-- Main Content -->
    <div class="main-content app-main">
<?php app_topbar($user, ['id' => 'globalSearch', 'placeholder' => 'Search courses, assignments, documents...']); ?>

        <!-- Page Content -->
        <div class="content-area">
            <!-- Page Header -->
            <div class="schedule-header-row">
                <div class="page-header" style="margin-bottom: 0;">
                    <h1>Class Schedule</h1>
                    <p class="page-subtitle">Your weekly timetable</p>
                </div>
                <div>
                    <button class="btn-export" onclick="alert('Exporting your 1st Sem 2026-2027 schedule to iCal / Google Calendar (.ics format)...')">
                        <i class="fa-solid fa-calendar-arrow-down"></i> Export to Calendar
                    </button>
                </div>
            </div>

            <!-- Toolbar: View toggle & Week Navigation -->
            <div class="schedule-toolbar">
                <!-- View Toggle: Weekly | Daily | Monthly -->
                <div class="view-pill-group">
                    <button class="view-pill active" onclick="setView('weekly')">Weekly</button>
                    <button class="view-pill" onclick="setView('daily')">Daily</button>
                    <button class="view-pill" onclick="setView('monthly')">Monthly</button>
                </div>

                <!-- Week navigation: < Previous | Oct 12 - 18, 2026 | Next > -->
                <div class="week-nav">
                    <button class="nav-arrow-btn" onclick="alert('Navigating to previous week (Oct 5 - 11, 2026)...')" title="Previous Week">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <span class="current-week-label">Oct 12 - 18, 2026</span>
                    <button class="nav-arrow-btn" onclick="alert('Navigating to next week (Oct 19 - 25, 2026)...')" title="Next Week">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                    <button class="btn-today" onclick="alert('Returned to current week.')">Today</button>
                </div>
            </div>

            <!-- Schedule Layout: Grid + Today's Sidebar -->
            <div class="schedule-layout-grid">
                
                <!-- Timetable Grid Card -->
                <div class="timetable-card">
                    <div class="timetable-scroll">
                        <div class="timetable-grid">
                            
                            <!-- Header Row: Time and Days (Mon - Sat) -->
                            <div class="time-col-header"></div>
                            <div class="day-header">
                                <span class="day-name">Mon</span>
                                <span class="day-date">12</span>
                            </div>
                            <div class="day-header today">
                                <span class="day-name">Tue</span>
                                <span class="day-date">13</span>
                            </div>
                            <div class="day-header">
                                <span class="day-name">Wed</span>
                                <span class="day-date">14</span>
                            </div>
                            <div class="day-header">
                                <span class="day-name">Thu</span>
                                <span class="day-date">15</span>
                            </div>
                            <div class="day-header">
                                <span class="day-name">Fri</span>
                                <span class="day-date">16</span>
                            </div>
                            <div class="day-header">
                                <span class="day-name">Sat</span>
                                <span class="day-date">17</span>
                            </div>

                            <!-- 7:00 AM -->
                            <div class="time-slot-label">7:00 AM</div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell today-col"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>

                            <!-- 8:00 AM (CS 201 on MWF 8-9 AM) -->
                            <div class="time-slot-label">8:00 AM</div>
                            <!-- Mon 8-9 AM: CS 201 (indigo) -->
                            <div class="schedule-cell">
                                <div class="class-block block-indigo block-1hr" onclick="openClassModal('CS 201', 'Data Structures & Algorithms', 'MWF 8:00-9:00 AM', 'Room 301', 'Prof. Annie Fe Mendez')">
                                    <div class="block-title">CS 201</div>
                                    <div class="block-meta">
                                        <span>Room 301</span>
                                        <span>Prof. Mendez</span>
                                    </div>
                                </div>
                            </div>
                            <div class="schedule-cell today-col"></div>
                            <!-- Wed 8-9 AM: CS 201 (indigo) -->
                            <div class="schedule-cell">
                                <div class="class-block block-indigo block-1hr" onclick="openClassModal('CS 201', 'Data Structures & Algorithms', 'MWF 8:00-9:00 AM', 'Room 301', 'Prof. Annie Fe Mendez')">
                                    <div class="block-title">CS 201</div>
                                    <div class="block-meta">
                                        <span>Room 301</span>
                                        <span>Prof. Mendez</span>
                                    </div>
                                </div>
                            </div>
                            <div class="schedule-cell"></div>
                            <!-- Fri 8-9 AM: CS 201 (indigo) -->
                            <div class="schedule-cell">
                                <div class="class-block block-indigo block-1hr" onclick="openClassModal('CS 201', 'Data Structures & Algorithms', 'MWF 8:00-9:00 AM', 'Room 301', 'Prof. Annie Fe Mendez')">
                                    <div class="block-title">CS 201</div>
                                    <div class="block-meta">
                                        <span>Room 301</span>
                                        <span>Prof. Mendez</span>
                                    </div>
                                </div>
                            </div>
                            <div class="schedule-cell"></div>

                            <!-- 9:00 AM (HUM 101 on Sat 9-12 PM) -->
                            <div class="time-slot-label">9:00 AM</div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell today-col"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>
                            <!-- Sat 9-12 PM: HUM 101 (purple) -->
                            <div class="schedule-cell">
                                <div class="class-block block-purple block-3hr" onclick="openClassModal('HUM 101', 'Art Appreciation', 'Sat 9:00 AM-12:00 PM', 'Room 415', 'Prof. Sweetzel Abion')">
                                    <div class="block-title">HUM 101</div>
                                    <div class="block-meta">
                                        <span>Room 415</span>
                                        <span>Prof. Sweetzel Abion</span>
                                        <span>9:00 AM - 12:00 PM</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 10:00 AM (NET 101 on MWF 10-11 AM, MATH 202 on TTh 10-11:30 AM) -->
                            <div class="time-slot-label">10:00 AM</div>
                            <!-- Mon 10-11 AM: NET 101 (emerald) -->
                            <div class="schedule-cell">
                                <div class="class-block block-emerald block-1hr" onclick="openClassModal('NET 101', 'Networking I', 'MWF 10:00-11:00 AM', 'Room 108', 'Prof. Christopher Marmol')">
                                    <div class="block-title">NET 101</div>
                                    <div class="block-meta">
                                        <span>Room 108</span>
                                        <span>Prof. Christopher Marmol</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Tue 10-11:30 AM: MATH 202 (cyan) -->
                            <div class="schedule-cell today-col">
                                <div class="class-block block-cyan block-1-5hr" onclick="openClassModal('MATH 202', 'Ethics', 'TTh 10:00-11:30 AM', 'Room 205', 'Prof. Einjela Picart')">
                                    <div class="block-title">MATH 202</div>
                                    <div class="block-meta">
                                        <span>Room 205</span>
                                        <span>Prof. Einjela Picart</span>
                                        <span>10:00-11:30 AM</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Wed 10-11 AM: NET 101 (emerald) -->
                            <div class="schedule-cell">
                                <div class="class-block block-emerald block-1hr" onclick="openClassModal('NET 101', 'Networking I', 'MWF 10:00-11:00 AM', 'Room 108', 'Prof. Christopher Marmol')">
                                    <div class="block-title">NET 101</div>
                                    <div class="block-meta">
                                        <span>Room 108</span>
                                        <span>Prof. Christopher Marmol</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Thu 10-11:30 AM: MATH 202 (cyan) -->
                            <div class="schedule-cell">
                                <div class="class-block block-cyan block-1-5hr" onclick="openClassModal('MATH 202', 'Ethics', 'TTh 10:00-11:30 AM', 'Room 205', 'Prof. Einjela Picart')">
                                    <div class="block-title">MATH 202</div>
                                    <div class="block-meta">
                                        <span>Room 205</span>
                                        <span>Prof. Einjela Picart</span>
                                        <span>10:00-11:30 AM</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Fri 10-11 AM: NET 101 (emerald) -->
                            <div class="schedule-cell">
                                <div class="class-block block-emerald block-1hr" onclick="openClassModal('NET 101', 'Networking I', 'MWF 10:00-11:00 AM', 'Room 108', 'Prof. Christopher Marmol')">
                                    <div class="block-title">NET 101</div>
                                    <div class="block-meta">
                                        <span>Room 108</span>
                                        <span>Prof. Christopher Marmol</span>
                                    </div>
                                </div>
                            </div>
                            <div class="schedule-cell"></div>

                            <!-- 11:00 AM -->
                            <div class="time-slot-label">11:00 AM</div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell today-col"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>

                            <!-- 12:00 PM (Lunch Period) -->
                            <div class="time-slot-label">12:00 PM</div>
                            <div class="schedule-cell" style="background: rgba(241, 245, 249, 0.5);"></div>
                            <div class="schedule-cell today-col" style="background: rgba(241, 245, 249, 0.5);"></div>
                            <div class="schedule-cell" style="background: rgba(241, 245, 249, 0.5);"></div>
                            <div class="schedule-cell" style="background: rgba(241, 245, 249, 0.5);"></div>
                            <div class="schedule-cell" style="background: rgba(241, 245, 249, 0.5);"></div>
                            <div class="schedule-cell" style="background: rgba(241, 245, 249, 0.5);"></div>

                            <!-- 1:00 PM (CS 203 on MWF 1-2 PM) -->
                            <div class="time-slot-label">1:00 PM</div>
                            <!-- Mon 1-2 PM: CS 203 (amber) -->
                            <div class="schedule-cell">
                                <div class="class-block block-amber block-1hr" onclick="openClassModal('CS 203', 'Web Development', 'MWF 1:00-2:00 PM', 'Lab 102', 'Prof. Glenn Kennedy Marzo')">
                                    <div class="block-title">CS 203</div>
                                    <div class="block-meta">
                                        <span>Lab 102</span>
                                        <span>Prof. Glenn Kennedy Marzo</span>
                                    </div>
                                </div>
                            </div>
                            <div class="schedule-cell today-col"></div>
                            <!-- Wed 1-2 PM: CS 203 (amber) -->
                            <div class="schedule-cell">
                                <div class="class-block block-amber block-1hr" onclick="openClassModal('CS 203', 'Web Development', 'MWF 1:00-2:00 PM', 'Lab 102', 'Prof. Glenn Kennedy Marzo')">
                                    <div class="block-title">CS 203</div>
                                    <div class="block-meta">
                                        <span>Lab 102</span>
                                        <span>Prof. Glenn Kennedy Marzo</span>
                                    </div>
                                </div>
                            </div>
                            <div class="schedule-cell"></div>
                            <!-- Fri 1-2 PM: CS 203 (amber) -->
                            <div class="schedule-cell">
                                <div class="class-block block-amber block-1hr" onclick="openClassModal('CS 203', 'Web Development', 'MWF 1:00-2:00 PM', 'Lab 102', 'Prof. Glenn Kennedy Marzo')">
                                    <div class="block-title">CS 203</div>
                                    <div class="block-meta">
                                        <span>Lab 102</span>
                                        <span>Prof. Glenn Kennedy Marzo</span>
                                    </div>
                                </div>
                            </div>
                            <div class="schedule-cell"></div>

                            <!-- 2:00 PM -->
                            <div class="time-slot-label">2:00 PM</div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell today-col"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>

                            <!-- 3:00 PM (DTP 101 on TTh 3-4:30 PM) -->
                            <div class="time-slot-label">3:00 PM</div>
                            <div class="schedule-cell"></div>
                            <!-- Tue 3-4:30 PM: DTP 101 (rose) -->
                            <div class="schedule-cell today-col">
                                <div class="class-block block-rose block-1-5hr" onclick="openClassModal('DTP 101', 'Desktop Publishing (Lecture + Lab)', 'TTh 3:00-4:30 PM', 'Sci Bldg 401', 'Prof. Dale Lyko Abion')">
                                    <div class="block-title">DTP 101</div>
                                    <div class="block-meta">
                                        <span>Sci Bldg 401</span>
                                        <span>Prof. Dale Lyko Abion</span>
                                        <span>3:00-4:30 PM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="schedule-cell"></div>
                            <!-- Thu 3-4:30 PM: DTP 101 (rose) -->
                            <div class="schedule-cell">
                                <div class="class-block block-rose block-1-5hr" onclick="openClassModal('DTP 101', 'Desktop Publishing (Lecture + Lab)', 'TTh 3:00-4:30 PM', 'Sci Bldg 401', 'Prof. Dale Lyko Abion')">
                                    <div class="block-title">DTP 101</div>
                                    <div class="block-meta">
                                        <span>Sci Bldg 401</span>
                                        <span>Prof. Dale Lyko Abion</span>
                                        <span>3:00-4:30 PM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>

                            <!-- 4:00 PM -->
                            <div class="time-slot-label">4:00 PM</div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell today-col"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>

                            <!-- 5:00 PM -->
                            <div class="time-slot-label">5:00 PM</div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell today-col"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>

                            <!-- 6:00 PM -->
                            <div class="time-slot-label">6:00 PM</div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell today-col"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>
                            <div class="schedule-cell"></div>

                        </div>
                    </div>
                </div>

                <!-- Today's Classes Sidebar (Right Column) -->
                <div class="today-sidebar">
                    
                    <!-- Today Timeline Card -->
                    <div class="today-card">
                        <div class="today-card-header">
                            <div>
                                <h3>Today's Classes</h3>
                                <span style="font-size: 0.8rem; color: var(--gray-500);">Tuesday, Oct 13, 2026</span>
                            </div>
                            <span class="today-badge">2 Classes</span>
                        </div>

                        <div class="timeline-container">
                            <!-- Class 1: Completed earlier -->
                            <div class="timeline-item completed">
                                <div class="timeline-dot"></div>
                                <div class="timeline-time">
                                    <span>10:00 AM - 11:30 AM</span>
                                    <span style="color: var(--success); font-size: 0.72rem; font-weight: 700;">&check; Finished</span>
                                </div>
                                <div class="timeline-course-title">MATH 202 - Ethics</div>
                                <div class="timeline-details">
                                    <span><i class="fa-solid fa-location-dot"></i> Room 205</span>
                                    <span><i class="fa-solid fa-chalkboard-user"></i> Prof. Einjela Picart</span>
                                </div>
                            </div>

                            <!-- Free block / lunch -->
                            <div class="timeline-item">
                                <div class="timeline-dot" style="border-color: var(--gray-300);"></div>
                                <div class="timeline-time">
                                    <span>11:30 AM - 3:00 PM</span>
                                    <span style="color: var(--gray-400); font-size: 0.72rem;">Study Break</span>
                                </div>
                                <div class="timeline-course-title" style="font-weight: 600; color: var(--gray-600);">Self-Study &amp; Library</div>
                                <div class="timeline-details">
                                    <span><i class="fa-solid fa-building-columns"></i> Main Campus Library - Level 3</span>
                                </div>
                            </div>

                            <!-- Class 2: Active / Upcoming Now -->
                            <div class="timeline-item active-now">
                                <div class="timeline-dot"></div>
                                <div class="timeline-time">
                                    <span>3:00 PM - 4:30 PM</span>
                                    <span class="badge-now">UPCOMING NEXT</span>
                                </div>
                                <div class="timeline-course-title">DTP 101 - Desktop Publishing</div>
                                <div class="timeline-details">
                                    <span><i class="fa-solid fa-location-dot"></i> Sci Bldg 401 (Lecture + Lab)</span>
                                    <span><i class="fa-solid fa-chalkboard-user"></i> Prof. Dale Lyko Abion</span>
                                    <span style="color: var(--danger); font-weight: 600; margin-top: 2px;">
                                        <i class="fa-regular fa-clock"></i> Starts in ~1 hour
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Legend Card -->
                    <div class="legend-card">
                        <h4>Course Legend</h4>
                        <div class="course-legend-list">
                            <div class="legend-row">
                                <div class="legend-color-box" style="background: var(--primary);"></div>
                                <span><strong>CS 201</strong> - Data Structures (MWF 8-9 AM)</span>
                            </div>
                            <div class="legend-row">
                                <div class="legend-color-box" style="background: var(--secondary);"></div>
                                <span><strong>MATH 202</strong> - Ethics (TTh 10-11:30 AM)</span>
                            </div>
                            <div class="legend-row">
                                <div class="legend-color-box" style="background: var(--accent);"></div>
                                <span><strong>CS 203</strong> - Web Dev (MWF 1-2 PM)</span>
                            </div>
                            <div class="legend-row">
                                <div class="legend-color-box" style="background: #E11D48;"></div>
                                <span><strong>DTP 101</strong> - Desktop Publishing (TTh 3-4:30 PM)</span>
                            </div>
                            <div class="legend-row">
                                <div class="legend-color-box" style="background: var(--success);"></div>
                                <span><strong>NET 101</strong> - Tech Writing (MWF 10-11 AM)</span>
                            </div>
                            <div class="legend-row">
                                <div class="legend-color-box" style="background: #7C3AED;"></div>
                                <span><strong>HUM 101</strong> - Art Apprec (Sat 9-12 PM)</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- Class Detail Popup Modal -->
    <div class="modal-overlay" id="classModal">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <h3 id="modalCode" style="font-size: 1.2rem; color: var(--dark);">Course Details</h3>
                    <p id="modalTitle" style="font-size: 0.85rem; color: var(--gray-500);">Course Name</p>
                </div>
                <button class="modal-close-btn" onclick="closeClassModal()" aria-label="Close Modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <h5 style="font-size: 0.8rem; color: var(--gray-500); text-transform: uppercase;">Class Schedule</h5>
                    <p id="modalSchedule" style="font-size: 0.95rem; font-weight: 600; color: var(--dark);"></p>
                </div>
                <div>
                    <h5 style="font-size: 0.8rem; color: var(--gray-500); text-transform: uppercase;">Location / Room</h5>
                    <p id="modalRoom" style="font-size: 0.95rem; font-weight: 600; color: var(--dark);"></p>
                </div>
                <div>
                    <h5 style="font-size: 0.8rem; color: var(--gray-500); text-transform: uppercase;">Faculty Instructor</h5>
                    <p id="modalInstructor" style="font-size: 0.95rem; font-weight: 600; color: var(--dark);"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-today" onclick="closeClassModal()">Close</button>
                <a href="courses.php" class="btn-export" style="text-decoration: none;">View Course Hub</a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../js/main.js?v=9"></script>
    <script>

        // View pills toggle
        function setView(view) {
            const pills = document.querySelectorAll('.view-pill');
            pills.forEach(p => p.classList.remove('active'));
            event.target.classList.add('active');
            if (view !== 'weekly') {
                alert('Switching to ' + view.toUpperCase() + ' schedule view. (Weekly view is currently active).');
            }
        }

        // Class Modal
        const classModal = document.getElementById('classModal');
        function openClassModal(code, title, schedule, room, instructor) {
            document.getElementById('modalCode').textContent = code;
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalSchedule').textContent = schedule;
            document.getElementById('modalRoom').textContent = room;
            document.getElementById('modalInstructor').textContent = instructor;
            classModal.classList.add('active');
        }

        function closeClassModal() {
            classModal.classList.remove('active');
        }

        window.addEventListener('click', (e) => {
            if (e.target === classModal) closeClassModal();
        });
    </script>
</body>
</html>
