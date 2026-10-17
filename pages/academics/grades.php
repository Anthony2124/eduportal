<?php
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/academics/grades.php';
$user = require_page_access($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades &amp; Transcript - EduPortal</title>
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

        .page-header-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 28px;
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

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-size: 0.88rem;
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
            background: var(--gray-100);
            color: var(--dark);
            border-color: var(--gray-400);
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        /* Summary Cards Row */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }

        .summary-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 22px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .summary-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .summary-card-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .summary-card-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }

        .icon-indigo { background: rgba(79, 70, 229, 0.1); color: var(--primary); }
        .icon-cyan { background: rgba(6, 182, 212, 0.1); color: var(--secondary); }
        .icon-emerald { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .icon-amber { background: rgba(245, 158, 11, 0.12); color: var(--accent); }

        .summary-card-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--dark);
            letter-spacing: -0.02em;
            line-height: 1;
        }

        .summary-subtext {
            font-size: 0.8rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .subtext-up { color: var(--success); }
        .subtext-info { color: var(--gray-500); }
        .subtext-star { color: #D97706; font-weight: 600; }

        /* Semester Selector Tabs */
        .semester-tabs {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--white);
            padding: 8px 12px;
            border-radius: var(--radius-md);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            margin-bottom: 24px;
            overflow-x: auto;
        }

        .sem-tab-btn {
            background: none;
            border: none;
            padding: 10px 20px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--gray-600);
            cursor: pointer;
            border-radius: var(--radius-sm);
            transition: var(--transition);
            white-space: nowrap;
        }

        .sem-tab-btn:hover {
            background: var(--gray-100);
            color: var(--dark);
        }

        .sem-tab-btn.active {
            background: var(--primary);
            color: var(--white);
            box-shadow: var(--shadow-sm);
        }

        /* Grades Table Card */
        .table-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 32px;
        }

        .table-card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .table-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--dark);
        }

        .term-badge {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .grades-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .grades-table th {
            background-color: var(--gray-100);
            padding: 14px 20px;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--gray-200);
        }

        .grades-table td {
            padding: 16px 20px;
            font-size: 0.9rem;
            color: var(--gray-700);
            border-bottom: 1px solid var(--gray-200);
            vertical-align: middle;
        }

        .grades-table tr:hover td {
            background-color: rgba(248, 250, 252, 0.8);
        }

        .grades-table tr:last-child td {
            border-bottom: none;
        }

        .course-code-cell {
            font-weight: 700;
            color: var(--primary);
        }

        .score-pill {
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            display: inline-block;
        }

        .score-pill.high {
            background: rgba(16, 185, 129, 0.12);
            color: #047857;
        }

        .score-pill.pending {
            background: var(--gray-100);
            color: var(--gray-500);
            font-weight: 500;
        }

        .status-pill {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-pill.progress {
            background: rgba(245, 158, 11, 0.15);
            color: #B45309;
        }

        .table-card-footer {
            padding: 16px 24px;
            background: var(--light);
            border-top: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--gray-600);
            flex-wrap: wrap;
            gap: 12px;
        }

        /* Charts Row (GPA Trend & Distribution) */
        .analytics-row {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 24px;
            margin-bottom: 32px;
        }

        .chart-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
        }

        .chart-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .chart-card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
        }

        .chart-card-desc {
            font-size: 0.8rem;
            color: var(--gray-500);
            margin-top: 2px;
        }

        /* Bar Chart Visual */
        .trend-chart-container {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            height: 220px;
            padding: 20px 10px 10px;
            position: relative;
            border-bottom: 2px solid var(--gray-200);
        }

        .chart-benchmark-line {
            position: absolute;
            left: 0;
            right: 0;
            top: 45px;
            border-top: 1px dashed var(--accent);
            display: flex;
            justify-content: flex-end;
            padding-right: 8px;
        }

        .benchmark-tag {
            font-size: 0.72rem;
            color: #B45309;
            font-weight: 700;
            background: #FEF3C7;
            padding: 2px 6px;
            border-radius: 4px;
            margin-top: -10px;
        }

        .bar-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            flex: 1;
            height: 100%;
            justify-content: flex-end;
        }

        .bar-col {
            width: 38px;
            background: linear-gradient(180deg, var(--primary-light), var(--primary));
            border-radius: 6px 6px 0 0;
            position: relative;
            transition: var(--transition);
            cursor: pointer;
        }

        .bar-col:hover {
            filter: brightness(1.15);
            transform: scaleY(1.02);
            transform-origin: bottom;
        }

        .bar-col.active-term {
            background: linear-gradient(180deg, #10B981, #059669);
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }

        .bar-value {
            position: absolute;
            top: -24px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--dark);
        }

        .bar-label {
            font-size: 0.75rem;
            color: var(--gray-500);
            font-weight: 600;
            text-align: center;
            margin-top: 8px;
        }

        /* Donut Distribution Chart */
        .distribution-body {
            display: flex;
            align-items: center;
            gap: 24px;
            height: 100%;
            justify-content: center;
            padding: 10px 0;
        }

        .pie-graphic {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: conic-gradient(
                var(--primary) 0% 45%,
                var(--secondary) 45% 80%,
                var(--accent) 80% 100%
            );
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-sm);
        }

        .pie-hole {
            width: 84px;
            height: 84px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .pie-hole-num {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--dark);
            line-height: 1;
        }

        .pie-hole-label {
            font-size: 0.65rem;
            color: var(--gray-500);
            text-transform: uppercase;
            font-weight: 600;
            margin-top: 3px;
        }

        .distribution-legend {
            display: flex;
            flex-direction: column;
            gap: 12px;
            flex: 1;
        }

        .legend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.85rem;
        }

        .legend-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-700);
            font-weight: 500;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .legend-dot.primary { background-color: var(--primary); }
        .legend-dot.secondary { background-color: var(--secondary); }
        .legend-dot.accent { background-color: var(--accent); }

        .legend-val {
            font-weight: 700;
            color: var(--dark);
        }

        /* Official Transcript Modal Preview */
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
            max-width: 680px;
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

        .transcript-preview-box {
            padding: 24px;
            background: var(--light);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            margin: 20px 24px;
            font-size: 0.85rem;
            line-height: 1.6;
        }

        .transcript-seal {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid var(--gray-300);
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .modal-footer {
            padding: 16px 24px;
            background: var(--light);
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        /* Responsive */
        @media (max-width: 1100px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .analytics-row {
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
            .summary-grid {
                grid-template-columns: 1fr;
            }
            .header-actions {
                width: 100%;
            }
            .header-actions .btn {
                flex: 1;
                justify-content: center;
            }
            .distribution-body {
                flex-direction: column;
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
            <div class="page-header-flex">
                <div class="page-header" style="margin-bottom: 0;">
                    <h1>Grades &amp; Transcript</h1>
                    <p class="page-subtitle">View your academic performance</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-outline" onclick="window.print()">
                        <i class="fa-solid fa-print"></i> Print Slip
                    </button>
                    <button class="btn btn-primary" onclick="openTranscriptModal()">
                        <i class="fa-solid fa-file-pdf"></i> Download Transcript
                    </button>
                </div>
            </div>

            <!-- Summary Cards Row -->
            <div class="summary-grid">
                <!-- Current GPA -->
                <div class="summary-card">
                    <div class="summary-card-header">
                        <span class="summary-card-title">Current GPA</span>
                        <div class="summary-card-icon icon-indigo"><i class="fa-solid fa-chart-line"></i></div>
                    </div>
                    <div class="summary-card-value">3.75 <span style="font-size: 1.1rem; color: var(--gray-400); font-weight: 500;">/ 4.0</span></div>
                    <div class="summary-subtext subtext-up">
                        <i class="fa-solid fa-arrow-trend-up"></i> +0.07 from previous term
                    </div>
                </div>

                <!-- Cumulative GPA -->
                <div class="summary-card">
                    <div class="summary-card-header">
                        <span class="summary-card-title">Cumulative GPA</span>
                        <div class="summary-card-icon icon-cyan"><i class="fa-solid fa-award"></i></div>
                    </div>
                    <div class="summary-card-value">3.68 <span style="font-size: 1.1rem; color: var(--gray-400); font-weight: 500;">/ 4.0</span></div>
                    <div class="summary-subtext subtext-info">
                        <i class="fa-solid fa-ranking-star"></i> Top 5% in BS Computer Science
                    </div>
                </div>

                <!-- Total Units -->
                <div class="summary-card">
                    <div class="summary-card-header">
                        <span class="summary-card-title">Total Units</span>
                        <div class="summary-card-icon icon-emerald"><i class="fa-solid fa-graduation-cap"></i></div>
                    </div>
                    <div class="summary-card-value">45 <span style="font-size: 1.1rem; color: var(--gray-400); font-weight: 500;">/ 120</span></div>
                    <div class="summary-subtext subtext-info">
                        <i class="fa-solid fa-circle-check" style="color: var(--success);"></i> 37.5% Degree Complete
                    </div>
                </div>

                <!-- Dean's List -->
                <div class="summary-card">
                    <div class="summary-card-header">
                        <span class="summary-card-title">Dean's List</span>
                        <div class="summary-card-icon icon-amber"><i class="fa-solid fa-star"></i></div>
                    </div>
                    <div class="summary-card-value" style="color: #D97706;">Yes ⭐</div>
                    <div class="summary-subtext subtext-star">
                        <i class="fa-solid fa-medal"></i> President's Honor Roll Qualifier
                    </div>
                </div>
            </div>

            <!-- Semester Selector Tabs -->
            <div class="semester-tabs">
                <button class="sem-tab-btn active" data-sem="1st-2026-2027">1st Sem 2026-2027 (Current)</button>
                <button class="sem-tab-btn" data-sem="2nd-2025-2026">2nd Sem 2025-2026</button>
                <button class="sem-tab-btn" data-sem="1st-2025-2026">1st Sem 2025-2026</button>
            </div>

            <!-- Grades Table Card -->
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h2 class="table-title">Academic Record</h2>
                        <span style="font-size: 0.8rem; color: var(--gray-500);">Term: 1st Semester Academic Year 2026-2027</span>
                    </div>
                    <span class="term-badge">Status: Enrolled (Good Standing)</span>
                </div>
                <div class="table-responsive">
                    <table class="grades-table">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Description</th>
                                <th>Units</th>
                                <th>Midterm</th>
                                <th>Final</th>
                                <th>Grade</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="course-code-cell">CS 201</td>
                                <td>Data Structures</td>
                                <td>3</td>
                                <td><span class="score-pill high">92</span></td>
                                <td><span class="score-pill pending">--</span></td>
                                <td><span class="status-pill progress"><i class="fa-solid fa-clock"></i> In Progress</span></td>
                                <td style="color: var(--gray-400);">--</td>
                            </tr>
                            <tr>
                                <td class="course-code-cell">MATH 202</td>
                                <td>Ethics</td>
                                <td>4</td>
                                <td><span class="score-pill high">88</span></td>
                                <td><span class="score-pill pending">--</span></td>
                                <td><span class="status-pill progress"><i class="fa-solid fa-clock"></i> In Progress</span></td>
                                <td style="color: var(--gray-400);">--</td>
                            </tr>
                            <tr>
                                <td class="course-code-cell">CS 203</td>
                                <td>Web Development</td>
                                <td>3</td>
                                <td><span class="score-pill high">95</span></td>
                                <td><span class="score-pill pending">--</span></td>
                                <td><span class="status-pill progress"><i class="fa-solid fa-clock"></i> In Progress</span></td>
                                <td style="color: var(--gray-400);">--</td>
                            </tr>
                            <tr>
                                <td class="course-code-cell">DTP 101</td>
                                <td>Desktop Publishing</td>
                                <td>4</td>
                                <td><span class="score-pill high">85</span></td>
                                <td><span class="score-pill pending">--</span></td>
                                <td><span class="status-pill progress"><i class="fa-solid fa-clock"></i> In Progress</span></td>
                                <td style="color: var(--gray-400);">--</td>
                            </tr>
                            <tr>
                                <td class="course-code-cell">NET 101</td>
                                <td>Networking I</td>
                                <td>3</td>
                                <td><span class="score-pill high">90</span></td>
                                <td><span class="score-pill pending">--</span></td>
                                <td><span class="status-pill progress"><i class="fa-solid fa-clock"></i> In Progress</span></td>
                                <td style="color: var(--gray-400);">--</td>
                            </tr>
                            <tr>
                                <td class="course-code-cell">HUM 101</td>
                                <td>Art Appreciation</td>
                                <td>3</td>
                                <td><span class="score-pill high">88</span></td>
                                <td><span class="score-pill pending">--</span></td>
                                <td><span class="status-pill progress"><i class="fa-solid fa-clock"></i> In Progress</span></td>
                                <td style="color: var(--gray-400);">--</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-card-footer">
                    <div>
                        <span>Enrolled Units: <strong>20 Units</strong></span> &bull;
                        <span>Weighted Midterm Average: <strong>89.8%</strong></span>
                    </div>
                    <div>
                        <span>Projected Term GPA: <strong style="color: var(--primary);">3.75</strong></span>
                    </div>
                </div>
            </div>

            <!-- GPA Trend & Grade Distribution Row -->
            <div class="analytics-row">
                <!-- Column 1: GPA Trend -->
                <div class="chart-card">
                    <div class="chart-card-header">
                        <div>
                            <h2 class="chart-card-title">GPA Performance Trend</h2>
                            <p class="chart-card-desc">Semester trajectory from freshman year to present</p>
                        </div>
                        <span style="font-size: 0.78rem; font-weight: 600; color: var(--success); background: rgba(16, 185, 129, 0.1); padding: 4px 8px; border-radius: 4px;">
                            Upward Trajectory &uarr;
                        </span>
                    </div>
                    <div class="trend-chart-container">
                        <!-- Benchmark line for Dean's list threshold -->
                        <div class="chart-benchmark-line">
                            <span class="benchmark-tag">Dean's List (3.50)</span>
                        </div>

                        <!-- Bar 1 -->
                        <div class="bar-group">
                            <div class="bar-col" style="height: 155px;">
                                <span class="bar-value">3.55</span>
                            </div>
                            <span class="bar-label">1st Sem<br>24-25</span>
                        </div>

                        <!-- Bar 2 -->
                        <div class="bar-group">
                            <div class="bar-col" style="height: 162px;">
                                <span class="bar-value">3.62</span>
                            </div>
                            <span class="bar-label">2nd Sem<br>24-25</span>
                        </div>

                        <!-- Bar 3 -->
                        <div class="bar-group">
                            <div class="bar-col" style="height: 168px;">
                                <span class="bar-value">3.65</span>
                            </div>
                            <span class="bar-label">1st Sem<br>25-26</span>
                        </div>

                        <!-- Bar 4 -->
                        <div class="bar-group">
                            <div class="bar-col" style="height: 175px;">
                                <span class="bar-value">3.70</span>
                            </div>
                            <span class="bar-label">2nd Sem<br>25-26</span>
                        </div>

                        <!-- Bar 5 (Current) -->
                        <div class="bar-group">
                            <div class="bar-col active-term" style="height: 185px;">
                                <span class="bar-value">3.75</span>
                            </div>
                            <span class="bar-label" style="color: var(--success); font-weight: 700;">1st Sem<br>26-27 (Now)</span>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Grade Distribution -->
                <div class="chart-card">
                    <div class="chart-card-header">
                        <div>
                            <h2 class="chart-card-title">Grade Distribution</h2>
                            <p class="chart-card-desc">All-time collegiate grade breakdown</p>
                        </div>
                    </div>
                    <div class="distribution-body">
                        <div class="pie-graphic">
                            <div class="pie-hole">
                                <span class="pie-hole-num">20</span>
                                <span class="pie-hole-label">Courses</span>
                            </div>
                        </div>
                        <div class="distribution-legend">
                            <div class="legend-item">
                                <div class="legend-label">
                                    <span class="legend-dot primary"></span>
                                    <span>Grade A (93-100)</span>
                                </div>
                                <span class="legend-val">45% (9)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-label">
                                    <span class="legend-dot secondary"></span>
                                    <span>Grade B+ (88-92)</span>
                                </div>
                                <span class="legend-val">35% (7)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-label">
                                    <span class="legend-dot accent"></span>
                                    <span>Grade B (83-87)</span>
                                </div>
                                <span class="legend-val">20% (4)</span>
                            </div>
                            <div style="border-top: 1px solid var(--gray-200); padding-top: 8px; margin-top: 4px; font-size: 0.78rem; color: var(--gray-500);">
                                <i class="fa-solid fa-check-double" style="color: var(--success);"></i> 100% Course Completion Rate
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Official Transcript Modal Preview -->
    <div class="modal-overlay" id="transcriptModal">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <h3 style="font-size: 1.2rem; color: var(--dark);">Official Academic Transcript</h3>
                    <p style="font-size: 0.8rem; color: var(--gray-500);">EduPortal University Registrar &bull; Student ID: <span data-user="studentId"></span></p>
                </div>
                <button class="modal-close-btn" onclick="closeTranscriptModal()" aria-label="Close Modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="transcript-preview-box">
                <div class="transcript-seal">
                    <div>
                        <strong>EduPortal University</strong><br>
                        Office of the University Registrar<br>
                        <em>Official Student Academic Record</em>
                    </div>
                    <div style="text-align: right; font-size: 0.8rem; color: var(--gray-500);">
                        Date Generated: Oct 13, 2026<br>
                        Status: Active / Certified
                    </div>
                </div>
                <p><strong>Student Name:</strong> <span data-user="fullName"></span></p>
                <p><strong>Program:</strong> Bachelor of Science in Computer Science</p>
                <p><strong>Cumulative GPA:</strong> 3.68 / 4.00 (Total Units Earned: 45)</p>
                <p><strong>Academic Standing:</strong> Dean's List Honor Roll</p>
                <div style="margin-top: 12px; padding: 8px; background: rgba(79, 70, 229, 0.08); border-radius: 4px; font-size: 0.78rem; color: var(--primary);">
                    <i class="fa-solid fa-certificate"></i> This digital document carries an encrypted registrar signature and is valid for official scholarship and transfer evaluation.
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeTranscriptModal()">Cancel</button>
                <button class="btn btn-primary" onclick="alert('Downloading Official Transcript PDF (AlexMorgan_Transcript_2026.pdf)...'); closeTranscriptModal();">
                    <i class="fa-solid fa-download"></i> Download PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../js/main.js?v=9"></script>
    <script>

        // Semester Tab Switcher
        const semTabs = document.querySelectorAll('.sem-tab-btn');
        semTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                semTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
            });
        });

        // Transcript Modal Functions
        const transcriptModal = document.getElementById('transcriptModal');
        function openTranscriptModal() {
            transcriptModal.classList.add('active');
        }

        function closeTranscriptModal() {
            transcriptModal.classList.remove('active');
        }

        window.addEventListener('click', (e) => {
            if (e.target === transcriptModal) closeTranscriptModal();
        });
    </script>
</body>
</html>
