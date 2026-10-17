<?php
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/academics/courses.php';
$user = require_page_access($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - EduPortal</title>
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

        .user-profile-menu:hover {
            background-color: var(--gray-100);
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

        .page-header {
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

        /* Quick Stat Highlights */
        .stats-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 16px 20px;
            border: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--shadow-sm);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-icon.primary { background: rgba(79, 70, 229, 0.1); color: var(--primary); }
        .stat-icon.secondary { background: rgba(6, 182, 212, 0.1); color: var(--secondary); }
        .stat-icon.accent { background: rgba(245, 158, 11, 0.1); color: var(--accent); }
        .stat-icon.success { background: rgba(16, 185, 129, 0.1); color: var(--success); }

        .stat-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--dark);
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--gray-500);
            font-weight: 500;
        }

        /* Filter Bar */
        .filter-toolbar {
            background: var(--white);
            padding: 16px 20px;
            border-radius: var(--radius-md);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
        }

        .filter-left {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 14px;
            flex: 1;
        }

        .select-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .select-group label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-600);
            white-space: nowrap;
        }

        .filter-select {
            padding: 8px 32px 8px 12px;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-sm);
            background-color: var(--white);
            font-size: 0.88rem;
            color: var(--gray-700);
            font-weight: 500;
            cursor: pointer;
            outline: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2364748B%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.4-12.8z%22%2F%3E%3C%2Fsvg%3E");
            background-repeat: no-repeat;
            background-position: right 10px top 50%;
            background-size: 10px auto;
        }

        .filter-select:focus {
            border-color: var(--primary);
        }

        .filter-search-box {
            position: relative;
            flex: 1;
            min-width: 220px;
            max-width: 340px;
        }

        .filter-search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 0.85rem;
        }

        .filter-search-box input {
            width: 100%;
            padding: 8px 12px 8px 34px;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-sm);
            font-size: 0.88rem;
            color: var(--dark);
            outline: none;
        }

        .filter-search-box input:focus {
            border-color: var(--primary);
        }

        .filter-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .view-toggle {
            display: flex;
            background-color: var(--gray-100);
            padding: 3px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--gray-200);
        }

        .view-btn {
            background: none;
            border: none;
            padding: 6px 12px;
            font-size: 0.9rem;
            color: var(--gray-500);
            cursor: pointer;
            border-radius: 4px;
            transition: var(--transition);
        }

        .view-btn.active {
            background: var(--white);
            color: var(--primary);
            box-shadow: var(--shadow-sm);
            font-weight: 600;
        }

        /* Course Grid Layout */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            transition: var(--transition);
        }

        /* Course Card */
        .course-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
            border-top: 5px solid var(--primary);
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            overflow: hidden;
            position: relative;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
            border-color: var(--gray-300);
        }

        /* Specific Top Border Colors */
        .course-card.color-indigo { border-top-color: var(--primary); }
        .course-card.color-cyan   { border-top-color: var(--secondary); }
        .course-card.color-amber  { border-top-color: var(--accent); }
        .course-card.color-red    { border-top-color: var(--danger); }
        .course-card.color-emerald{ border-top-color: var(--success); }
        .course-card.color-purple { border-top-color: #8B5CF6; }

        .card-body {
            padding: 22px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .card-top-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .course-code-badge {
            font-size: 0.8rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            background-color: var(--gray-100);
            color: var(--gray-700);
            letter-spacing: 0.03em;
        }

        .course-tags {
            display: flex;
            gap: 6px;
        }

        .tag-badge {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 4px;
        }

        .tag-units {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
        }

        .tag-type {
            background: rgba(6, 182, 212, 0.1);
            color: #0891B2;
        }

        .course-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 12px;
            line-height: 1.35;
        }

        .course-instructor {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.88rem;
            color: var(--gray-600);
            margin-bottom: 8px;
        }

        .course-instructor i {
            color: var(--gray-400);
            font-size: 0.95rem;
        }

        .course-schedule {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.82rem;
            color: var(--gray-500);
            margin-bottom: 18px;
        }

        .course-schedule i {
            color: var(--gray-400);
        }

        /* Progress Area */
        .progress-wrapper {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid var(--gray-100);
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--gray-600);
        }

        .progress-bar-bg {
            height: 7px;
            background-color: var(--gray-200);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        .progress-fill.indigo { background: var(--primary); }
        .progress-fill.cyan   { background: var(--secondary); }
        .progress-fill.amber  { background: var(--accent); }
        .progress-fill.red    { background: var(--danger); }
        .progress-fill.emerald{ background: var(--success); }
        .progress-fill.purple { background: #8B5CF6; }

        /* Card Quick Links Footer */
        .card-footer {
            background-color: var(--gray-100);
            padding: 12px 20px;
            border-top: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .quick-link-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--gray-600);
            text-decoration: none;
            padding: 6px 8px;
            border-radius: var(--radius-sm);
            transition: var(--transition);
            background: none;
            border: none;
            cursor: pointer;
        }

        .quick-link-btn i {
            font-size: 0.85rem;
            color: var(--gray-500);
        }

        .quick-link-btn:hover {
            color: var(--primary);
            background-color: var(--white);
        }

        .quick-link-btn:hover i {
            color: var(--primary);
        }

        /* List View Modifier */
        .courses-grid.list-view {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .courses-grid.list-view .course-card {
            flex-direction: row;
            border-top: 1px solid var(--gray-200);
            border-left: 6px solid var(--primary);
        }

        .courses-grid.list-view .course-card.color-indigo { border-left-color: var(--primary); }
        .courses-grid.list-view .course-card.color-cyan   { border-left-color: var(--secondary); }
        .courses-grid.list-view .course-card.color-amber  { border-left-color: var(--accent); }
        .courses-grid.list-view .course-card.color-red    { border-left-color: var(--danger); }
        .courses-grid.list-view .course-card.color-emerald{ border-left-color: var(--success); }
        .courses-grid.list-view .course-card.color-purple { border-left-color: #8B5CF6; }

        .courses-grid.list-view .card-body {
            flex: 2;
        }

        .courses-grid.list-view .card-footer {
            flex: 1;
            flex-direction: column;
            justify-content: center;
            border-top: none;
            border-left: 1px solid var(--gray-200);
            gap: 10px;
        }

        .courses-grid.list-view .quick-link-btn {
            width: 100%;
            justify-content: center;
        }

        /* Materials Modal */
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
            max-width: 640px;
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

        .modal-title-group h3 {
            font-size: 1.25rem;
            color: var(--dark);
        }

        .modal-title-group p {
            font-size: 0.85rem;
            color: var(--gray-500);
            margin-top: 2px;
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

        .modal-close-btn:hover {
            color: var(--dark);
            background: var(--gray-100);
        }

        .modal-body {
            padding: 24px;
            max-height: 480px;
            overflow-y: auto;
        }

        .material-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border-radius: var(--radius-md);
            border: 1px solid var(--gray-200);
            margin-bottom: 10px;
            transition: var(--transition);
        }

        .material-item:hover {
            background-color: var(--light);
            border-color: var(--gray-300);
        }

        .material-info {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .material-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-sm);
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .material-details h5 {
            font-size: 0.92rem;
            font-weight: 600;
            color: var(--dark);
        }

        .material-details span {
            font-size: 0.76rem;
            color: var(--gray-500);
        }

        .download-btn {
            padding: 7px 14px;
            border-radius: var(--radius-sm);
            background: var(--primary);
            color: var(--white);
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .download-btn:hover {
            background: var(--primary-dark);
        }

        /* Responsive Breakpoints */
        @media (max-width: 1200px) {
            .courses-grid {
                grid-template-columns: repeat(2, 1fr);
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

        @media (max-width: 680px) {
            .courses-grid {
                grid-template-columns: 1fr;
            }
            .filter-toolbar {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-left {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-search-box {
                max-width: 100%;
            }
            .filter-right {
                justify-content: flex-end;
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
            <div class="page-header">
                <h1>My Courses</h1>
                <p class="page-subtitle">Access your course materials and resources</p>
            </div>

            <!-- Stats Highlights -->
            <div class="stats-strip">
                <div class="stat-card">
                    <div class="stat-icon primary"><i class="fa-solid fa-book-open"></i></div>
                    <div>
                        <div class="stat-value">6</div>
                        <div class="stat-label">Enrolled Courses</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon secondary"><i class="fa-solid fa-layer-group"></i></div>
                    <div>
                        <div class="stat-value">20</div>
                        <div class="stat-label">Total Academic Units</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon accent"><i class="fa-solid fa-chart-pie"></i></div>
                    <div>
                        <div class="stat-value">63.3%</div>
                        <div class="stat-label">Average Term Progress</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon success"><i class="fa-solid fa-cloud-arrow-down"></i></div>
                    <div>
                        <div class="stat-value">56</div>
                        <div class="stat-label">Materials Available</div>
                    </div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="filter-toolbar">
                <div class="filter-left">
                    <div class="select-group">
                        <label for="semesterSelect">Term:</label>
                        <select id="semesterSelect" class="filter-select">
                            <option value="1st-2026-2027" selected>1st Sem 2026-2027</option>
                            <option value="2nd-2025-2026">2nd Sem 2025-2026</option>
                            <option value="1st-2025-2026">1st Sem 2025-2026</option>
                        </select>
                    </div>

                    <div class="filter-search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="courseSearch" placeholder="Search by course code or title...">
                    </div>
                </div>

                <div class="filter-right">
                    <div class="view-toggle">
                        <button class="view-btn active" id="gridViewBtn" title="Grid View">
                            <i class="fa-solid fa-grip"></i>
                        </button>
                        <button class="view-btn" id="listViewBtn" title="List View">
                            <i class="fa-solid fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Course Cards Grid (3 columns, responsive) -->
            <div class="courses-grid" id="coursesGrid">
                
                <!-- CARD 1: CS 201 -->
                <div class="course-card color-indigo" data-code="CS 201" data-name="Data Structures & Algorithms">
                    <div class="card-body">
                        <div class="card-top-row">
                            <span class="course-code-badge">CS 201</span>
                            <div class="course-tags">
                                <span class="tag-badge tag-units">3 Units</span>
                                <span class="tag-badge tag-type">Lecture</span>
                            </div>
                        </div>
                        <h2 class="course-title">Data Structures &amp; Algorithms</h2>
                        <div class="course-instructor">
                            <i class="fa-solid fa-chalkboard-user"></i>
                            <span>Prof. Annie Fe Mendez</span>
                        </div>
                        <div class="course-schedule">
                            <i class="fa-regular fa-clock"></i>
                            <span>MWF 8:00-9:00 AM | Room 301</span>
                        </div>
                        
                        <div class="progress-wrapper">
                            <div class="progress-info">
                                <span>Course Progress</span>
                                <span>65%</span>
                            </div>
                            <div class="progress-bar-bg">
                                <div class="progress-fill indigo" style="width: 65%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="quick-link-btn" onclick="openMaterialsModal('CS 201 - Data Structures & Algorithms', 'Prof. Annie Fe Mendez')">
                            <i class="fa-solid fa-folder-open"></i> Materials
                        </button>
                        <a href="assignments.php" class="quick-link-btn">
                            <i class="fa-solid fa-tasks"></i> Assignments
                        </a>
                        <a href="grades.php" class="quick-link-btn">
                            <i class="fa-solid fa-chart-bar"></i> Grades
                        </a>
                    </div>
                </div>

                <!-- CARD 2: MATH 202 -->
                <div class="course-card color-cyan" data-code="MATH 202" data-name="Ethics">
                    <div class="card-body">
                        <div class="card-top-row">
                            <span class="course-code-badge">MATH 202</span>
                            <div class="course-tags">
                                <span class="tag-badge tag-units">4 Units</span>
                                <span class="tag-badge tag-type">Lecture</span>
                            </div>
                        </div>
                        <h2 class="course-title">Ethics</h2>
                        <div class="course-instructor">
                            <i class="fa-solid fa-chalkboard-user"></i>
                            <span>Prof. Einjela Picart</span>
                        </div>
                        <div class="course-schedule">
                            <i class="fa-regular fa-clock"></i>
                            <span>TTh 10:00-11:30 AM | Room 205</span>
                        </div>
                        
                        <div class="progress-wrapper">
                            <div class="progress-info">
                                <span>Course Progress</span>
                                <span>58%</span>
                            </div>
                            <div class="progress-bar-bg">
                                <div class="progress-fill cyan" style="width: 58%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="quick-link-btn" onclick="openMaterialsModal('MATH 202 - Ethics', 'Prof. Einjela Picart')">
                            <i class="fa-solid fa-folder-open"></i> Materials
                        </button>
                        <a href="assignments.php" class="quick-link-btn">
                            <i class="fa-solid fa-tasks"></i> Assignments
                        </a>
                        <a href="grades.php" class="quick-link-btn">
                            <i class="fa-solid fa-chart-bar"></i> Grades
                        </a>
                    </div>
                </div>

                <!-- CARD 3: CS 203 -->
                <div class="course-card color-amber" data-code="CS 203" data-name="Web Development">
                    <div class="card-body">
                        <div class="card-top-row">
                            <span class="course-code-badge">CS 203</span>
                            <div class="course-tags">
                                <span class="tag-badge tag-units">3 Units</span>
                                <span class="tag-badge tag-type" style="background: rgba(245, 158, 11, 0.15); color: #B45309;">Lab</span>
                            </div>
                        </div>
                        <h2 class="course-title">Web Development</h2>
                        <div class="course-instructor">
                            <i class="fa-solid fa-chalkboard-user"></i>
                            <span>Prof. Glenn Kennedy Marzo</span>
                        </div>
                        <div class="course-schedule">
                            <i class="fa-regular fa-clock"></i>
                            <span>MWF 1:00-2:00 PM | Lab 102</span>
                        </div>
                        
                        <div class="progress-wrapper">
                            <div class="progress-info">
                                <span>Course Progress</span>
                                <span>72%</span>
                            </div>
                            <div class="progress-bar-bg">
                                <div class="progress-fill amber" style="width: 72%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="quick-link-btn" onclick="openMaterialsModal('CS 203 - Web Development', 'Prof. Glenn Kennedy Marzo')">
                            <i class="fa-solid fa-folder-open"></i> Materials
                        </button>
                        <a href="assignments.php" class="quick-link-btn">
                            <i class="fa-solid fa-tasks"></i> Assignments
                        </a>
                        <a href="grades.php" class="quick-link-btn">
                            <i class="fa-solid fa-chart-bar"></i> Grades
                        </a>
                    </div>
                </div>

                <!-- CARD 4: DTP 101 -->
                <div class="course-card color-red" data-code="DTP 101" data-name="Desktop Publishing">
                    <div class="card-body">
                        <div class="card-top-row">
                            <span class="course-code-badge">DTP 101</span>
                            <div class="course-tags">
                                <span class="tag-badge tag-units">4 Units</span>
                                <span class="tag-badge tag-type" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">Lecture + Lab</span>
                            </div>
                        </div>
                        <h2 class="course-title">Desktop Publishing</h2>
                        <div class="course-instructor">
                            <i class="fa-solid fa-chalkboard-user"></i>
                            <span>Prof. Dale Lyko Abion</span>
                        </div>
                        <div class="course-schedule">
                            <i class="fa-regular fa-clock"></i>
                            <span>TTh 3:00-4:30 PM | Sci Bldg 401</span>
                        </div>
                        
                        <div class="progress-wrapper">
                            <div class="progress-info">
                                <span>Course Progress</span>
                                <span>60%</span>
                            </div>
                            <div class="progress-bar-bg">
                                <div class="progress-fill red" style="width: 60%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="quick-link-btn" onclick="openMaterialsModal('DTP 101 - Desktop Publishing', 'Prof. Dale Lyko Abion')">
                            <i class="fa-solid fa-folder-open"></i> Materials
                        </button>
                        <a href="assignments.php" class="quick-link-btn">
                            <i class="fa-solid fa-tasks"></i> Assignments
                        </a>
                        <a href="grades.php" class="quick-link-btn">
                            <i class="fa-solid fa-chart-bar"></i> Grades
                        </a>
                    </div>
                </div>

                <!-- CARD 5: NET 101 -->
                <div class="course-card color-emerald" data-code="NET 101" data-name="Networking I">
                    <div class="card-body">
                        <div class="card-top-row">
                            <span class="course-code-badge">NET 101</span>
                            <div class="course-tags">
                                <span class="tag-badge tag-units">3 Units</span>
                                <span class="tag-badge tag-type" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">Lecture</span>
                            </div>
                        </div>
                        <h2 class="course-title">Networking I</h2>
                        <div class="course-instructor">
                            <i class="fa-solid fa-chalkboard-user"></i>
                            <span>Prof. Christopher Marmol</span>
                        </div>
                        <div class="course-schedule">
                            <i class="fa-regular fa-clock"></i>
                            <span>MWF 10:00-11:00 AM | Room 108</span>
                        </div>
                        
                        <div class="progress-wrapper">
                            <div class="progress-info">
                                <span>Course Progress</span>
                                <span>70%</span>
                            </div>
                            <div class="progress-bar-bg">
                                <div class="progress-fill emerald" style="width: 70%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="quick-link-btn" onclick="openMaterialsModal('NET 101 - Networking I', 'Prof. Christopher Marmol')">
                            <i class="fa-solid fa-folder-open"></i> Materials
                        </button>
                        <a href="assignments.php" class="quick-link-btn">
                            <i class="fa-solid fa-tasks"></i> Assignments
                        </a>
                        <a href="grades.php" class="quick-link-btn">
                            <i class="fa-solid fa-chart-bar"></i> Grades
                        </a>
                    </div>
                </div>

                <!-- CARD 6: HUM 101 -->
                <div class="course-card color-purple" data-code="HUM 101" data-name="Art Appreciation">
                    <div class="card-body">
                        <div class="card-top-row">
                            <span class="course-code-badge">HUM 101</span>
                            <div class="course-tags">
                                <span class="tag-badge tag-units">3 Units</span>
                                <span class="tag-badge tag-type" style="background: rgba(139, 92, 246, 0.1); color: #7C3AED;">Lecture</span>
                            </div>
                        </div>
                        <h2 class="course-title">Art Appreciation</h2>
                        <div class="course-instructor">
                            <i class="fa-solid fa-chalkboard-user"></i>
                            <span>Prof. Sweetzel Abion</span>
                        </div>
                        <div class="course-schedule">
                            <i class="fa-regular fa-clock"></i>
                            <span>Sat 9:00-12:00 PM | Room 415</span>
                        </div>
                        
                        <div class="progress-wrapper">
                            <div class="progress-info">
                                <span>Course Progress</span>
                                <span>55%</span>
                            </div>
                            <div class="progress-bar-bg">
                                <div class="progress-fill purple" style="width: 55%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="quick-link-btn" onclick="openMaterialsModal('HUM 101 - Art Appreciation', 'Prof. Sweetzel Abion')">
                            <i class="fa-solid fa-folder-open"></i> Materials
                        </button>
                        <a href="assignments.php" class="quick-link-btn">
                            <i class="fa-solid fa-tasks"></i> Assignments
                        </a>
                        <a href="grades.php" class="quick-link-btn">
                            <i class="fa-solid fa-chart-bar"></i> Grades
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Materials Modal -->
    <div class="modal-overlay" id="materialsModal">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title-group">
                    <h3 id="modalCourseTitle">Course Materials</h3>
                    <p id="modalCourseInstructor">Instructor</p>
                </div>
                <button class="modal-close-btn" onclick="closeMaterialsModal()" aria-label="Close Modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="material-item">
                    <div class="material-info">
                        <div class="material-icon"><i class="fa-solid fa-file-pdf"></i></div>
                        <div class="material-details">
                            <h5>Official Course Syllabus &amp; Grading Policy</h5>
                            <span>PDF &bull; 1.4 MB &bull; Updated Aug 28, 2026</span>
                        </div>
                    </div>
                    <button class="download-btn" onclick="alert('Downloading Syllabus...')">
                        <i class="fa-solid fa-download"></i> Download
                    </button>
                </div>

                <div class="material-item">
                    <div class="material-info">
                        <div class="material-icon" style="background: rgba(6, 182, 212, 0.1); color: var(--secondary);"><i class="fa-solid fa-file-powerpoint"></i></div>
                        <div class="material-details">
                            <h5>Week 6-7 Lecture Slides &amp; Notes</h5>
                            <span>PPTX &bull; 8.2 MB &bull; Uploaded Oct 08, 2026</span>
                        </div>
                    </div>
                    <button class="download-btn" onclick="alert('Downloading Lecture Slides...')">
                        <i class="fa-solid fa-download"></i> Download
                    </button>
                </div>

                <div class="material-item">
                    <div class="material-info">
                        <div class="material-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);"><i class="fa-solid fa-file-code"></i></div>
                        <div class="material-details">
                            <h5>Reference Implementations &amp; Lab Exercises</h5>
                            <span>ZIP Archive &bull; 3.6 MB &bull; Uploaded Oct 11, 2026</span>
                        </div>
                    </div>
                    <button class="download-btn" onclick="alert('Downloading Lab Exercises...')">
                        <i class="fa-solid fa-download"></i> Download
                    </button>
                </div>

                <div class="material-item">
                    <div class="material-info">
                        <div class="material-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--accent);"><i class="fa-solid fa-book-bookmark"></i></div>
                        <div class="material-details">
                            <h5>Midterm Exam Review Guide &amp; Sample Questions</h5>
                            <span>PDF &bull; 2.1 MB &bull; Uploaded Oct 12, 2026</span>
                        </div>
                    </div>
                    <button class="download-btn" onclick="alert('Downloading Review Guide...')">
                        <i class="fa-solid fa-download"></i> Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../js/main.js?v=9"></script>
    <script>

        // Grid & List View Toggle
        const gridViewBtn = document.getElementById('gridViewBtn');
        const listViewBtn = document.getElementById('listViewBtn');
        const coursesGrid = document.getElementById('coursesGrid');

        if (gridViewBtn && listViewBtn && coursesGrid) {
            gridViewBtn.addEventListener('click', () => {
                gridViewBtn.classList.add('active');
                listViewBtn.classList.remove('active');
                coursesGrid.classList.remove('list-view');
            });

            listViewBtn.addEventListener('click', () => {
                listViewBtn.classList.add('active');
                gridViewBtn.classList.remove('active');
                coursesGrid.classList.add('list-view');
            });
        }

        // Live Course Search Filter
        const courseSearch = document.getElementById('courseSearch');
        if (courseSearch) {
            courseSearch.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                const cards = document.querySelectorAll('.course-card');

                cards.forEach(card => {
                    const code = card.getAttribute('data-code').toLowerCase();
                    const name = card.getAttribute('data-name').toLowerCase();
                    if (code.includes(query) || name.includes(query)) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }

        // Materials Modal Functions
        function openMaterialsModal(courseTitle, instructor) {
            const modal = document.getElementById('materialsModal');
            document.getElementById('modalCourseTitle').textContent = courseTitle;
            document.getElementById('modalCourseInstructor').textContent = instructor;
            modal.classList.add('active');
        }

        function closeMaterialsModal() {
            const modal = document.getElementById('materialsModal');
            modal.classList.remove('active');
        }

        // Close modal on click outside
        window.addEventListener('click', (e) => {
            const modal = document.getElementById('materialsModal');
            if (e.target === modal) {
                closeMaterialsModal();
            }
        });
    </script>
</body>
</html>
