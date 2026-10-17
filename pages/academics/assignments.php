<?php
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/academics/assignments.php';
$user = require_page_access($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - EduPortal</title>
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

        /* Tabs Bar */
        .tabs-container {
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 2px solid var(--gray-200);
            margin-bottom: 24px;
            overflow-x: auto;
            padding-bottom: 2px;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 12px 18px;
            font-size: 0.92rem;
            font-weight: 600;
            color: var(--gray-500);
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            white-space: nowrap;
        }

        .tab-btn:hover {
            color: var(--primary);
        }

        .tab-btn.active {
            color: var(--primary);
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background-color: var(--primary);
            border-radius: 3px 3px 0 0;
        }

        .tab-badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 12px;
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .tab-btn.active .tab-badge {
            background: rgba(79, 70, 229, 0.15);
            color: var(--primary);
        }

        /* Filter Row */
        .filters-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-sm);
        }

        .filter-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            flex: 1;
        }

        .filter-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-600);
            white-space: nowrap;
        }

        .form-select {
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

        .form-select:focus {
            border-color: var(--primary);
        }

        /* Assignment Card Row */
        .assignments-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .assignment-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
            border-left: 6px solid var(--primary);
            box-shadow: var(--shadow-sm);
            padding: 22px 24px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            transition: var(--transition);
            position: relative;
        }

        .assignment-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
            border-color: var(--gray-300);
        }

        /* Course Color Indicators */
        .assignment-card.course-cs201 { border-left-color: var(--primary); }
        .assignment-card.course-math202 { border-left-color: var(--secondary); }
        .assignment-card.course-cs203 { border-left-color: var(--accent); }
        .assignment-card.course-DTP101 { border-left-color: var(--danger); }
        .assignment-card.course-eng101 { border-left-color: var(--success); }
        .assignment-card.course-hum101 { border-left-color: #8B5CF6; }

        .assignment-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .assignment-meta-left {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .course-badge-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .course-tag {
            font-size: 0.78rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            background-color: var(--gray-100);
            color: var(--gray-700);
        }

        .assignment-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--dark);
        }

        .assignment-badges {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Priority Badges */
        .priority-badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .priority-high {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .priority-medium {
            background: rgba(245, 158, 11, 0.1);
            color: #D97706;
        }

        .priority-low {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        /* Status Badges */
        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-in-progress {
            background: rgba(245, 158, 11, 0.15);
            color: #B45309;
        }

        .status-not-started {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .status-submitted {
            background: rgba(16, 185, 129, 0.15);
            color: #047857;
        }

        .status-graded {
            background: rgba(79, 70, 229, 0.12);
            color: var(--primary);
        }

        .status-late {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger);
        }

        .assignment-snippet {
            font-size: 0.88rem;
            color: var(--gray-600);
            line-height: 1.5;
        }

        /* Footer Row */
        .assignment-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding-top: 12px;
            border-top: 1px solid var(--gray-100);
            flex-wrap: wrap;
        }

        .due-info {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 0.82rem;
            color: var(--gray-500);
        }

        .due-date {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .countdown-chip {
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.76rem;
        }

        .countdown-urgent {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .countdown-normal {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
        }

        .countdown-complete {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .actions-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border: 1px solid transparent;
            text-decoration: none;
        }

        .btn-outline {
            background: var(--white);
            border-color: var(--gray-300);
            color: var(--gray-700);
        }

        .btn-outline:hover {
            border-color: var(--gray-400);
            background: var(--gray-100);
            color: var(--dark);
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-success {
            background: var(--success);
            color: var(--white);
        }

        .btn-success:hover {
            background: #059669;
        }

        /* Submission Modal */
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
            max-width: 600px;
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
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        /* File Drop Area */
        .drop-zone {
            border: 2px dashed var(--gray-300);
            border-radius: var(--radius-md);
            padding: 32px 20px;
            text-align: center;
            background-color: var(--gray-100);
            cursor: pointer;
            transition: var(--transition);
        }

        .drop-zone:hover, .drop-zone.dragover {
            border-color: var(--primary);
            background-color: rgba(79, 70, 229, 0.04);
        }

        .drop-zone i {
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .drop-zone h4 {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .drop-zone p {
            font-size: 0.8rem;
            color: var(--gray-500);
        }

        .file-input-hidden {
            display: none;
        }

        .uploaded-file-preview {
            display: none;
            align-items: center;
            justify-content: space-between;
            background: var(--light);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            margin-top: 8px;
        }

        .uploaded-file-preview.active {
            display: flex;
        }

        .file-preview-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .file-preview-left i {
            color: var(--primary);
            font-size: 1.1rem;
        }

        .file-preview-details span {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--dark);
        }

        .file-preview-details small {
            font-size: 0.72rem;
            color: var(--gray-500);
        }

        .remove-file-btn {
            background: none;
            border: none;
            color: var(--danger);
            cursor: pointer;
            font-size: 0.95rem;
            padding: 4px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 6px;
        }

        .form-textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-sm);
            font-size: 0.88rem;
            color: var(--dark);
            outline: none;
            resize: vertical;
            min-height: 80px;
        }

        .form-textarea:focus {
            border-color: var(--primary);
        }

        .modal-footer {
            padding: 16px 24px;
            background: var(--light);
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        /* Empty State */
        .empty-state {
            display: none;
            text-align: center;
            padding: 60px 20px;
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--gray-300);
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 1.2rem;
            color: var(--dark);
            margin-bottom: 6px;
        }

        .empty-state p {
            font-size: 0.88rem;
            color: var(--gray-500);
        }

        /* Responsive */
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

        @media (max-width: 640px) {
            .assignment-header {
                flex-direction: column;
            }
            .assignment-footer {
                flex-direction: column;
                align-items: flex-start;
            }
            .actions-group {
                width: 100%;
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
                <h1>Assignments</h1>
                <p class="page-subtitle">Track and submit your coursework</p>
            </div>

            <!-- Tabs: All | Pending | Submitted | Graded | Late -->
            <div class="tabs-container">
                <button class="tab-btn active" data-tab="all">
                    <span>All</span>
                    <span class="tab-badge">6</span>
                </button>
                <button class="tab-btn" data-tab="pending">
                    <span>Pending</span>
                    <span class="tab-badge">4</span>
                </button>
                <button class="tab-btn" data-tab="submitted">
                    <span>Submitted</span>
                    <span class="tab-badge">1</span>
                </button>
                <button class="tab-btn" data-tab="graded">
                    <span>Graded</span>
                    <span class="tab-badge">1</span>
                </button>
                <button class="tab-btn" data-tab="late">
                    <span>Late</span>
                    <span class="tab-badge">0</span>
                </button>
            </div>

            <!-- Filters Bar -->
            <div class="filters-card">
                <div class="filter-controls">
                    <div class="filter-item">
                        <span class="filter-label">Course:</span>
                        <select id="courseFilter" class="form-select">
                            <option value="all">All Courses</option>
                            <option value="CS 201">CS 201 - Data Structures</option>
                            <option value="MATH 202">MATH 202 - Ethics</option>
                            <option value="CS 203">CS 203 - Web Development</option>
                            <option value="DTP 101">DTP 101 - Desktop Publishing</option>
                            <option value="NET 101">NET 101 - Networking I</option>
                            <option value="HUM 101">HUM 101 - Art Appreciation</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <span class="filter-label">Due Date:</span>
                        <select id="dueDateFilter" class="form-select">
                            <option value="all">All Dates</option>
                            <option value="week">Due This Week</option>
                            <option value="next_week">Due Next Week</option>
                            <option value="past">Past Due / Graded</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <span class="filter-label">Sort by:</span>
                        <select id="sortBy" class="form-select">
                            <option value="due_asc">Due Date (Earliest First)</option>
                            <option value="priority_desc">Priority (High to Low)</option>
                            <option value="course_asc">Course Code</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Assignments List -->
            <div class="assignments-list" id="assignmentsList">

                <!-- 1. Database Design Report - CS 201 -->
                <div class="assignment-card course-cs201" data-course="CS 201" data-status="pending" data-state="in-progress" data-priority="high" data-due="2026-10-15">
                    <div class="assignment-header">
                        <div class="assignment-meta-left">
                            <div class="course-badge-row">
                                <span class="course-tag" style="background: rgba(79, 70, 229, 0.1); color: var(--primary);">CS 201</span>
                                <span style="font-size: 0.82rem; color: var(--gray-500); font-weight: 500;">Data Structures &amp; Algorithms</span>
                            </div>
                            <h2 class="assignment-title">Database Design Report</h2>
                        </div>
                        <div class="assignment-badges">
                            <span class="priority-badge priority-high"><i class="fa-solid fa-triangle-exclamation"></i> High Priority</span>
                            <span class="status-badge status-in-progress"><i class="fa-solid fa-spinner"></i> In Progress</span>
                        </div>
                    </div>
                    <p class="assignment-snippet">
                        Design an entity-relationship (E-R) diagram and implement relational schema normalization (3NF/BCNF) for an enterprise online ordering platform. Include full SQL DDL script attachments and normalization justification.
                    </p>
                    <div class="assignment-footer">
                        <div class="due-info">
                            <div class="due-date">
                                <i class="fa-regular fa-calendar-check"></i>
                                <span>Due: Oct 15, 2026 &bull; 11:59 PM</span>
                            </div>
                            <span class="countdown-chip countdown-urgent"><i class="fa-regular fa-clock"></i> 2 days left</span>
                        </div>
                        <div class="actions-group">
                            <button class="btn btn-outline" onclick="openDetailsModal('Database Design Report', 'CS 201', 'Oct 15, 2026 11:59 PM', 'High', 'Design an entity-relationship schema and relational normalization for an enterprise platform.')">
                                <i class="fa-regular fa-eye"></i> View
                            </button>
                            <button class="btn btn-primary" onclick="openSubmitModal('Database Design Report', 'CS 201', 'Oct 15, 2026 11:59 PM')">
                                <i class="fa-solid fa-cloud-arrow-up"></i> Submit
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 2. Calculus Problem Set 6 - MATH 202 -->
                <div class="assignment-card course-math202" data-course="MATH 202" data-status="pending" data-state="not-started" data-priority="medium" data-due="2026-10-17">
                    <div class="assignment-header">
                        <div class="assignment-meta-left">
                            <div class="course-badge-row">
                                <span class="course-tag" style="background: rgba(6, 182, 212, 0.1); color: var(--secondary);">MATH 202</span>
                                <span style="font-size: 0.82rem; color: var(--gray-500); font-weight: 500;">Ethics</span>
                            </div>
                            <h2 class="assignment-title">Calculus Problem Set 6</h2>
                        </div>
                        <div class="assignment-badges">
                            <span class="priority-badge priority-medium"><i class="fa-solid fa-circle-info"></i> Medium Priority</span>
                            <span class="status-badge status-not-started"><i class="fa-regular fa-circle"></i> Not Started</span>
                        </div>
                    </div>
                    <p class="assignment-snippet">
                        Complete comprehensive problem set covering improper integrals, trigonometric substitutions, and alternating series test approximations from Chapter 8 (Problems 14 through 38, even numbers).
                    </p>
                    <div class="assignment-footer">
                        <div class="due-info">
                            <div class="due-date">
                                <i class="fa-regular fa-calendar-check"></i>
                                <span>Due: Oct 17, 2026 &bull; 11:59 PM</span>
                            </div>
                            <span class="countdown-chip countdown-normal"><i class="fa-regular fa-clock"></i> 4 days left</span>
                        </div>
                        <div class="actions-group">
                            <button class="btn btn-outline" onclick="openDetailsModal('Calculus Problem Set 6', 'MATH 202', 'Oct 17, 2026 11:59 PM', 'Medium', 'Complete problems covering improper integrals, trig substitutions, and series convergence tests.')">
                                <i class="fa-regular fa-eye"></i> View
                            </button>
                            <button class="btn btn-primary" onclick="openSubmitModal('Calculus Problem Set 6', 'MATH 202', 'Oct 17, 2026 11:59 PM')">
                                <i class="fa-solid fa-cloud-arrow-up"></i> Submit
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. Web Portfolio Project - CS 203 -->
                <div class="assignment-card course-cs203" data-course="CS 203" data-status="pending" data-state="in-progress" data-priority="high" data-due="2026-10-20">
                    <div class="assignment-header">
                        <div class="assignment-meta-left">
                            <div class="course-badge-row">
                                <span class="course-tag" style="background: rgba(245, 158, 11, 0.15); color: #B45309;">CS 203</span>
                                <span style="font-size: 0.82rem; color: var(--gray-500); font-weight: 500;">Web Development</span>
                            </div>
                            <h2 class="assignment-title">Web Portfolio Project</h2>
                        </div>
                        <div class="assignment-badges">
                            <span class="priority-badge priority-high"><i class="fa-solid fa-triangle-exclamation"></i> High Priority</span>
                            <span class="status-badge status-in-progress"><i class="fa-solid fa-spinner"></i> In Progress</span>
                        </div>
                    </div>
                    <p class="assignment-snippet">
                        Develop a multi-page responsive personal portfolio website utilizing semantic HTML5, modern CSS custom properties, and modular JavaScript. Must achieve a 95+ score on Lighthouse Accessibility and Performance metrics.
                    </p>
                    <div class="assignment-footer">
                        <div class="due-info">
                            <div class="due-date">
                                <i class="fa-regular fa-calendar-check"></i>
                                <span>Due: Oct 20, 2026 &bull; 11:59 PM</span>
                            </div>
                            <span class="countdown-chip countdown-normal"><i class="fa-regular fa-clock"></i> 7 days left</span>
                        </div>
                        <div class="actions-group">
                            <button class="btn btn-outline" onclick="openDetailsModal('Web Portfolio Project', 'CS 203', 'Oct 20, 2026 11:59 PM', 'High', 'Develop a multi-page responsive portfolio showcasing projects and accessible UI.')">
                                <i class="fa-regular fa-eye"></i> View
                            </button>
                            <button class="btn btn-primary" onclick="openSubmitModal('Web Portfolio Project', 'CS 203', 'Oct 20, 2026 11:59 PM')">
                                <i class="fa-solid fa-cloud-arrow-up"></i> Submit
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 4. Desktop Publishing Lab Report 4 - DTP 101 -->
                <div class="assignment-card course-DTP101" data-course="DTP 101" data-status="pending" data-state="not-started" data-priority="low" data-due="2026-10-22">
                    <div class="assignment-header">
                        <div class="assignment-meta-left">
                            <div class="course-badge-row">
                                <span class="course-tag" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">DTP 101</span>
                                <span style="font-size: 0.82rem; color: var(--gray-500); font-weight: 500;">Desktop Publishing</span>
                            </div>
                            <h2 class="assignment-title">Desktop Publishing Lab Report 4</h2>
                        </div>
                        <div class="assignment-badges">
                            <span class="priority-badge priority-low"><i class="fa-solid fa-check"></i> Low Priority</span>
                            <span class="status-badge status-not-started"><i class="fa-regular fa-circle"></i> Not Started</span>
                        </div>
                    </div>
                    <p class="assignment-snippet">
                        Submit experimental write-up on damped and forced simple harmonic motion. Tabulate angular resonance curves, measure quality factors, and include standard deviation calculations and error graphs.
                    </p>
                    <div class="assignment-footer">
                        <div class="due-info">
                            <div class="due-date">
                                <i class="fa-regular fa-calendar-check"></i>
                                <span>Due: Oct 22, 2026 &bull; 11:59 PM</span>
                            </div>
                            <span class="countdown-chip countdown-normal"><i class="fa-regular fa-clock"></i> 9 days left</span>
                        </div>
                        <div class="actions-group">
                            <button class="btn btn-outline" onclick="openDetailsModal('Desktop Publishing Lab Report 4', 'DTP 101', 'Oct 22, 2026 11:59 PM', 'Low', 'Experimental write-up on harmonic motion with error graphs.')">
                                <i class="fa-regular fa-eye"></i> View
                            </button>
                            <button class="btn btn-primary" onclick="openSubmitModal('Desktop Publishing Lab Report 4', 'DTP 101', 'Oct 22, 2026 11:59 PM')">
                                <i class="fa-solid fa-cloud-arrow-up"></i> Submit
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 5. Essay: Modern Communication - NET 101 -->
                <div class="assignment-card course-eng101" data-course="NET 101" data-status="submitted" data-state="submitted" data-priority="medium" data-due="2026-10-12">
                    <div class="assignment-header">
                        <div class="assignment-meta-left">
                            <div class="course-badge-row">
                                <span class="course-tag" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">NET 101</span>
                                <span style="font-size: 0.82rem; color: var(--gray-500); font-weight: 500;">Networking I</span>
                            </div>
                            <h2 class="assignment-title">Essay: Modern Communication</h2>
                        </div>
                        <div class="assignment-badges">
                            <span class="priority-badge priority-medium"><i class="fa-solid fa-circle-info"></i> Medium Priority</span>
                            <span class="status-badge status-submitted"><i class="fa-solid fa-circle-check"></i> Submitted &check;</span>
                        </div>
                    </div>
                    <p class="assignment-snippet">
                        A 1,500-word critical evaluation of digital communication mediums, rhetorical context, and the cognitive impacts of asynchronous collaboration in distributed engineering teams.
                    </p>
                    <div class="assignment-footer">
                        <div class="due-info">
                            <div class="due-date">
                                <i class="fa-regular fa-calendar-check"></i>
                                <span>Due: Oct 12, 2026 &bull; Submitted Oct 11, 2026 at 9:42 PM</span>
                            </div>
                            <span class="countdown-chip countdown-complete"><i class="fa-solid fa-check"></i> Submitted on time</span>
                        </div>
                        <div class="actions-group">
                            <button class="btn btn-outline" onclick="openDetailsModal('Essay: Modern Communication', 'NET 101', 'Oct 12, 2026', 'Medium', 'Submitted on Oct 11, 2026. File: Modern_Comm_Essay_AlexMorgan.pdf (240 KB)')">
                                <i class="fa-regular fa-eye"></i> View
                            </button>
                            <button class="btn btn-outline" onclick="openSubmitModal('Essay: Modern Communication (Resubmission)', 'NET 101', 'Oct 12, 2026 (Grace Window Active)')">
                                <i class="fa-solid fa-arrows-rotate"></i> Resubmit
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 6. Art Analysis Paper - HUM 101 -->
                <div class="assignment-card course-hum101" data-course="HUM 101" data-status="graded" data-state="graded" data-priority="medium" data-due="2026-10-08">
                    <div class="assignment-header">
                        <div class="assignment-meta-left">
                            <div class="course-badge-row">
                                <span class="course-tag" style="background: rgba(139, 92, 246, 0.1); color: #7C3AED;">HUM 101</span>
                                <span style="font-size: 0.82rem; color: var(--gray-500); font-weight: 500;">Art Appreciation</span>
                            </div>
                            <h2 class="assignment-title">Art Analysis Paper</h2>
                        </div>
                        <div class="assignment-badges">
                            <span class="priority-badge priority-medium"><i class="fa-solid fa-circle-info"></i> Medium Priority</span>
                            <span class="status-badge status-graded"><i class="fa-solid fa-award"></i> Graded (88/100)</span>
                        </div>
                    </div>
                    <p class="assignment-snippet">
                        Formal critique analyzing pictorial composition, chiaroscuro lighting, and nationalistic symbology in Juan Luna's <em>Spoliarium</em>. Graded by Prof. Sweetzel Abion on Oct 10, 2026.
                    </p>
                    <div class="assignment-footer">
                        <div class="due-info">
                            <div class="due-date">
                                <i class="fa-regular fa-calendar-check"></i>
                                <span>Due: Oct 8, 2026 &bull; Graded Oct 10</span>
                            </div>
                            <span class="countdown-chip countdown-complete" style="background: rgba(79, 70, 229, 0.1); color: var(--primary);">
                                <i class="fa-solid fa-star"></i> Score: 88 / 100 (A-)
                            </span>
                        </div>
                        <div class="actions-group">
                            <button class="btn btn-outline" onclick="alert('Instructor Feedback:\n\n“Outstanding visual analysis and well-structured historical contextualization, Alex. Excellent work on the thematic contrast.”\n- Prof. Sweetzel Abion')">
                                <i class="fa-regular fa-comment-dots"></i> Feedback
                            </button>
                            <button class="btn btn-outline" onclick="openDetailsModal('Art Analysis Paper', 'HUM 101', 'Oct 8, 2026', 'Graded: 88/100', 'Formal critique analyzing pictorial composition and nationalistic symbology.')">
                                <i class="fa-regular fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Empty State Container for Tabs like 'Late' -->
            <div class="empty-state" id="emptyState">
                <i class="fa-regular fa-circle-check"></i>
                <h3>No Assignments Found</h3>
                <p>There are no assignments matching the selected tab or filter criteria.</p>
            </div>
        </div>
    </div>

    <!-- Assignment Submission Modal -->
    <div class="modal-overlay" id="submissionModal">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title-group">
                    <h3 id="submitModalTitle">Submit Assignment</h3>
                    <p id="submitModalSubtitle">Course Code &bull; Due Date</p>
                </div>
                <button class="modal-close-btn" onclick="closeSubmitModal()" aria-label="Close Modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="submissionForm" onsubmit="handleAssignmentSubmit(event)">
                <div class="modal-body">
                    <!-- File Drag and Drop Zone -->
                    <div class="drop-zone" id="dropZone" onclick="document.getElementById('fileUploadInput').click()">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <h4>Click or Drag &amp; Drop your submission file</h4>
                        <p>Supported file formats: PDF, DOCX, ZIP, PY, JS, CPP (Max: 25MB)</p>
                        <input type="file" id="fileUploadInput" class="file-input-hidden" onchange="handleFileSelect(event)">
                    </div>

                    <!-- File selected preview -->
                    <div class="uploaded-file-preview" id="filePreview">
                        <div class="file-preview-left">
                            <i class="fa-solid fa-file-lines"></i>
                            <div class="file-preview-details">
                                <span id="fileNameDisplay">document.pdf</span>
                                <small id="fileSizeDisplay">1.2 MB</small>
                            </div>
                        </div>
                        <button type="button" class="remove-file-btn" onclick="removeSelectedFile()" title="Remove file">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>

                    <!-- Notes / Comments Field -->
                    <div class="form-group">
                        <label for="submissionNotes">Submission Notes / Comments for Instructor (Optional):</label>
                        <textarea id="submissionNotes" class="form-textarea" placeholder="Include any special instructions, bibliography links, or comments..."></textarea>
                    </div>

                    <div style="display: flex; align-items: flex-start; gap: 10px; font-size: 0.82rem; color: var(--gray-600);">
                        <input type="checkbox" id="honorPledge" required style="margin-top: 3px; cursor: pointer;">
                        <label for="honorPledge" style="cursor: pointer; line-height: 1.4;">
                            I affirm that this submission is entirely my own original academic work in accordance with EduPortal University's Academic Integrity Policy.
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeSubmitModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-paper-plane"></i> Submit Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assignment View Details Modal -->
    <div class="modal-overlay" id="detailsModal">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title-group">
                    <h3 id="detailsTitle">Assignment Details</h3>
                    <p id="detailsCourseCode">Course</p>
                </div>
                <button class="modal-close-btn" onclick="closeDetailsModal()" aria-label="Close Modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <h5 style="font-size: 0.85rem; color: var(--gray-500); text-transform: uppercase; margin-bottom: 4px;">Deadline</h5>
                    <p id="detailsDueDate" style="font-size: 0.95rem; font-weight: 600; color: var(--dark);"></p>
                </div>
                <div>
                    <h5 style="font-size: 0.85rem; color: var(--gray-500); text-transform: uppercase; margin-bottom: 4px;">Priority</h5>
                    <p id="detailsPriority" style="font-size: 0.95rem; font-weight: 600; color: var(--dark);"></p>
                </div>
                <div>
                    <h5 style="font-size: 0.85rem; color: var(--gray-500); text-transform: uppercase; margin-bottom: 4px;">Instructions &amp; Overview</h5>
                    <p id="detailsDescription" style="font-size: 0.9rem; line-height: 1.5; color: var(--gray-700);"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="closeDetailsModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../js/main.js?v=9"></script>
    <script>
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarClose = document.getElementById('sidebarClose');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }

        if (sidebarClose) {
            sidebarClose.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });
        }

        // Tab Filtering Logic
        const tabBtns = document.querySelectorAll('.tab-btn');
        const cards = document.querySelectorAll('.assignment-card');
        const emptyState = document.getElementById('emptyState');
        const courseFilter = document.getElementById('courseFilter');
        let currentTab = 'all';

        function filterAssignments() {
            const selectedCourse = courseFilter.value;
            let visibleCount = 0;

            cards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                const cardCourse = card.getAttribute('data-course');

                let matchesTab = false;
                if (currentTab === 'all') {
                    matchesTab = true;
                } else if (currentTab === 'pending') {
                    matchesTab = cardStatus === 'pending';
                } else if (currentTab === 'submitted') {
                    matchesTab = cardStatus === 'submitted';
                } else if (currentTab === 'graded') {
                    matchesTab = cardStatus === 'graded';
                } else if (currentTab === 'late') {
                    matchesTab = cardStatus === 'late';
                }

                let matchesCourse = (selectedCourse === 'all' || cardCourse === selectedCourse);

                if (matchesTab && matchesCourse) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                emptyState.style.display = 'block';
            } else {
                emptyState.style.display = 'none';
            }
        }

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                tabBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentTab = btn.getAttribute('data-tab');
                filterAssignments();
            });
        });

        if (courseFilter) {
            courseFilter.addEventListener('change', filterAssignments);
        }

        // Submission Modal
        const submissionModal = document.getElementById('submissionModal');
        const submitModalTitle = document.getElementById('submitModalTitle');
        const submitModalSubtitle = document.getElementById('submitModalSubtitle');
        const filePreview = document.getElementById('filePreview');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const fileSizeDisplay = document.getElementById('fileSizeDisplay');
        const fileUploadInput = document.getElementById('fileUploadInput');

        function openSubmitModal(title, course, due) {
            submitModalTitle.textContent = 'Submit Assignment: ' + title;
            submitModalSubtitle.textContent = course + ' • Due: ' + due;
            submissionModal.classList.add('active');
        }

        function closeSubmitModal() {
            submissionModal.classList.remove('active');
            removeSelectedFile();
            document.getElementById('submissionForm').reset();
        }

        function handleFileSelect(e) {
            if (e.target.files && e.target.files[0]) {
                const file = e.target.files[0];
                fileNameDisplay.textContent = file.name;
                fileSizeDisplay.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                filePreview.classList.add('active');
            }
        }

        function removeSelectedFile() {
            fileUploadInput.value = '';
            filePreview.classList.remove('active');
        }

        // Drag and drop events
        const dropZone = document.getElementById('dropZone');
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.remove('dragover');
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt.files && dt.files[0]) {
                fileUploadInput.files = dt.files;
                handleFileSelect({ target: { files: dt.files } });
            }
        });

        function handleAssignmentSubmit(e) {
            e.preventDefault();
            if (!fileUploadInput.files || fileUploadInput.files.length === 0) {
                alert('Please attach a submission file before submitting.');
                return;
            }
            alert('Success! Your assignment has been securely uploaded and submitted. A confirmation receipt has been sent to your student email.');
            closeSubmitModal();
        }

        // Details Modal
        const detailsModal = document.getElementById('detailsModal');
        function openDetailsModal(title, course, due, priority, desc) {
            document.getElementById('detailsTitle').textContent = title;
            document.getElementById('detailsCourseCode').textContent = course;
            document.getElementById('detailsDueDate').textContent = due;
            document.getElementById('detailsPriority').textContent = priority;
            document.getElementById('detailsDescription').textContent = desc;
            detailsModal.classList.add('active');
        }

        function closeDetailsModal() {
            detailsModal.classList.remove('active');
        }

        window.addEventListener('click', (e) => {
            if (e.target === submissionModal) closeSubmitModal();
            if (e.target === detailsModal) closeDetailsModal();
        });
    </script>
</body>
</html>
