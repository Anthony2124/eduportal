<?php
// Student dashboard. Signed-out visitors go to sign in; faculty and admins go to their own dashboards.
require __DIR__ . '/api/bootstrap.php';
require __DIR__ . '/includes/app_shell.php';

$user = require_page_role('student');

$e = fn($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$fullName = $user['first_name'] . ' ' . $user['last_name'];
$initials = strtoupper(mb_substr($user['first_name'], 0, 1) . mb_substr($user['last_name'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EduPortal</title>
    <!-- Google Fonts & Font Awesome CDN -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        /* DESIGN SYSTEM VARIABLES & BASE STYLES */
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
            --sidebar-hover: #334155;
            --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: var(--light);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }

        .sidebar-header {
            padding: 1.5rem 1.4rem;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.25rem;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.35);
        }

        .sidebar-brand-name {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--white);
            letter-spacing: -0.02em;
        }

        .sidebar-brand-name span {
            color: var(--secondary);
        }

        .sidebar-nav {
            padding: 1.25rem 0.85rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .nav-section {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .nav-section-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--gray-500);
            padding: 0.5rem 0.75rem 0.25rem 0.75rem;
        }

        .nav-item {
            list-style: none;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.65rem 0.85rem;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .nav-link i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
            color: var(--gray-400);
            transition: color 0.2s ease;
        }

        .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: var(--white);
        }

        .nav-link:hover i {
            color: var(--primary-light);
        }

        .nav-link.active {
            background-color: var(--sidebar-active);
            color: var(--white);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        .nav-link.active i {
            color: var(--white);
        }

        .sidebar-footer {
            padding: 1rem 0.85rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .sidebar-footer .nav-link.logout-link:hover {
            background-color: rgba(239, 68, 68, 0.15);
            color: #FCA5A5;
        }

        .sidebar-footer .nav-link.logout-link:hover i {
            color: #F87171;
        }

        /* MAIN CONTENT WRAPPER */
        .main-content {
            margin-left: 260px;
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - 260px);
            transition: margin-left 0.3s ease;
        }

        /* TOP NAVIGATION */
        .top-nav {
            height: 72px;
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-200);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 99;
            box-shadow: var(--shadow-xs);
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            flex: 1;
            max-width: 520px;
        }

        .menu-toggle {
            display: none;
            background: transparent;
            border: none;
            font-size: 1.25rem;
            color: var(--gray-700);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: var(--radius-sm);
        }

        .menu-toggle:hover {
            background-color: var(--gray-100);
        }

        .search-bar {
            position: relative;
            width: 100%;
        }

        .search-bar i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 0.95rem;
        }

        .search-input {
            width: 100%;
            height: 42px;
            background: var(--light);
            border: 1px solid var(--gray-200);
            border-radius: 9999px;
            padding: 0 1rem 0 2.6rem;
            font-size: 0.885rem;
            color: var(--dark);
            outline: none;
            transition: var(--transition);
        }

        .search-input:focus {
            background: var(--white);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .academic-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #EEF2FF;
            color: var(--primary);
            padding: 0.45rem 0.9rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-icon-btn {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid var(--gray-200);
            background: var(--white);
            color: var(--gray-600);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .nav-icon-btn:hover {
            background-color: var(--gray-100);
            color: var(--dark);
        }

        .nav-icon-btn .badge-count {
            position: absolute;
            top: -3px;
            right: -3px;
            background: var(--danger);
            color: var(--white);
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.15rem 0.35rem;
            border-radius: 9999px;
            border: 2px solid var(--white);
        }

        .nav-icon-btn .badge-count.info {
            background: var(--primary);
        }

        .user-profile-menu {
            position: relative;
        }

        .user-profile-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0.35rem 0.5rem;
            border-radius: var(--radius-md);
            transition: var(--transition);
        }

        .user-profile-btn:hover {
            background-color: var(--gray-100);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4F46E5, #06B6D4);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
            position: relative;
        }

        .online-dot {
            width: 10px;
            height: 10px;
            background-color: var(--success);
            border: 2px solid var(--white);
            border-radius: 50%;
            position: absolute;
            bottom: 0;
            right: 0;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .user-name {
            font-size: 0.885rem;
            font-weight: 600;
            color: var(--dark);
            line-height: 1.2;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        /* Dropdown Menus */
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            width: 280px;
            padding: 0.5rem 0;
            z-index: 1000;
            display: none;
            animation: dropdownFade 0.2s ease;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--gray-100);
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--gray-700);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dropdown-item {
            padding: 0.65rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--gray-700);
            text-decoration: none;
            font-size: 0.85rem;
            transition: background 0.15s ease;
        }

        .dropdown-item:hover {
            background: var(--gray-100);
            color: var(--primary);
        }

        .dropdown-item i {
            width: 18px;
            color: var(--gray-400);
        }

        .dropdown-divider {
            height: 1px;
            background: var(--gray-100);
            margin: 0.35rem 0;
        }

        /* CONTENT AREA */
        .content-area {
            padding: 2rem 2.25rem;
            flex: 1;
        }

        .page-header {
            margin-bottom: 1.75rem;
        }

        .page-header h1 {
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }

        .page-subtitle {
            font-size: 0.95rem;
            color: var(--gray-500);
        }

        /* WELCOME BANNER */
        .welcome-banner {
            background: linear-gradient(135deg, #1E293B 0%, #312E81 30%, #4F46E5 70%, #06B6D4 100%);
            border-radius: var(--radius-xl);
            padding: 2.25rem 2.5rem;
            color: var(--white);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.25);
        }

        .banner-glow {
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(6, 182, 212, 0.3);
            filter: blur(80px);
            border-radius: 50%;
            right: 10%;
            top: -80px;
            pointer-events: none;
        }

        .banner-shapes {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.12) 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.4;
            pointer-events: none;
        }

        .banner-text-wrap {
            position: relative;
            z-index: 2;
            max-width: 620px;
        }

        .banner-date-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #E0E7FF;
            margin-bottom: 0.85rem;
        }

        .banner-title {
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 0.65rem;
            color: var(--white);
        }

        .banner-desc {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.5;
            margin-bottom: 1.5rem;
            font-weight: 400;
        }

        .banner-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.7rem 1.35rem;
            border-radius: var(--radius-md);
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }

        .btn-white {
            background: var(--white);
            color: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-white:hover {
            background: var(--light);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        .btn-glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: var(--white);
        }

        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        .banner-graphic {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-left: 2rem;
        }

        .banner-stat-bubble {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: var(--radius-lg);
            padding: 1.25rem 1.5rem;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            animation: floatBubble 5s ease-in-out infinite alternate;
        }

        .bubble-number {
            font-size: 2.2rem;
            font-weight: 800;
            color: #67E8F9;
        }

        .bubble-label {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.85);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        /* STATS ROW (4 CARDS) */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 1.4rem;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            box-shadow: var(--shadow-xs);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--gray-300);
        }

        .stat-info {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .stat-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .stat-count {
            font-size: 1.9rem;
            font-weight: 800;
            color: var(--dark);
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .stat-subtext {
            font-size: 0.775rem;
            color: var(--gray-500);
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .stat-subtext.positive {
            color: var(--success);
            font-weight: 600;
        }

        .stat-subtext.warning {
            color: #D97706;
            font-weight: 600;
        }

        .stat-icon-box {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .stat-icon-box.primary {
            background: #EEF2FF;
            color: var(--primary);
        }

        .stat-icon-box.warning {
            background: #FEF3C7;
            color: #D97706;
        }

        .stat-icon-box.success {
            background: #D1FAE5;
            color: var(--success);
        }

        .stat-icon-box.secondary {
            background: #E0F2FE;
            color: var(--secondary);
        }

        /* MAIN GRID (2/3 + 1/3 LAYOUT) */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.75rem;
        }

        .grid-column {
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
        }

        /* GENERAL CARD STYLING */
        .content-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-xs);
            transition: var(--transition);
        }

        .content-card:hover {
            box-shadow: var(--shadow-sm);
        }

        .card-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid var(--gray-100);
        }

        .card-header-left {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .card-header-icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
            letter-spacing: -0.01em;
        }

        .card-action-link {
            font-size: 0.825rem;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: color 0.2s ease;
        }

        .card-action-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* CARD 1: UPCOMING SCHEDULE (TIMELINE FORMAT) */
        .timeline-list {
            display: flex;
            flex-direction: column;
            gap: 1.15rem;
            position: relative;
            padding-left: 1.2rem;
        }

        .timeline-list::before {
            content: '';
            position: absolute;
            top: 10px;
            bottom: 10px;
            left: 21px;
            width: 2px;
            background: var(--gray-200);
            z-index: 1;
        }

        .timeline-item {
            display: flex;
            align-items: flex-start;
            gap: 1.25rem;
            position: relative;
            z-index: 2;
        }

        .timeline-node {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--white);
            border: 3px solid var(--gray-300);
            margin-top: 0.35rem;
            flex-shrink: 0;
            transition: var(--transition);
        }

        .timeline-item.completed .timeline-node {
            border-color: var(--success);
            background: var(--success);
        }

        .timeline-item.active .timeline-node {
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.2);
            animation: pulseDot 2s infinite;
        }

        .timeline-item.upcoming .timeline-node {
            border-color: var(--secondary);
            background: var(--white);
        }

        .timeline-content {
            background: var(--light);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            padding: 1rem 1.25rem;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: var(--transition);
        }

        .timeline-content:hover {
            background: var(--white);
            border-color: var(--primary-light);
            box-shadow: var(--shadow-sm);
        }

        .timeline-main-info {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .timeline-time-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.775rem;
            font-weight: 700;
            color: var(--primary);
        }

        .timeline-subject {
            font-size: 1rem;
            font-weight: 700;
            color: var(--dark);
        }

        .timeline-meta {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            font-size: 0.825rem;
            color: var(--gray-500);
        }

        .timeline-meta span {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .status-pill {
            padding: 0.3rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .status-pill.completed {
            background: #D1FAE5;
            color: #065F46;
        }

        .status-pill.in-progress {
            background: #EEF2FF;
            color: var(--primary-dark);
            animation: pulseBg 2s infinite;
        }

        .status-pill.upcoming {
            background: #E0F2FE;
            color: #0369A1;
        }

        /* CARD 2: RECENT GRADES TABLE */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .grades-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            text-align: left;
        }

        .grades-table th {
            background: var(--light);
            color: var(--gray-500);
            font-size: 0.775rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .grades-table th:first-child {
            border-top-left-radius: var(--radius-sm);
            border-bottom-left-radius: var(--radius-sm);
        }

        .grades-table th:last-child {
            border-top-right-radius: var(--radius-sm);
            border-bottom-right-radius: var(--radius-sm);
        }

        .grades-table td {
            padding: 1rem 1rem;
            border-bottom: 1px solid var(--gray-100);
            font-size: 0.885rem;
            color: var(--gray-700);
        }

        .grades-table tr:last-child td {
            border-bottom: none;
        }

        .grades-table tr:hover td {
            background-color: var(--light);
        }

        .course-cell {
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .course-tag {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .grade-badge {
            display: inline-block;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.3rem 0.65rem;
            border-radius: var(--radius-sm);
        }

        .grade-badge.grade-a {
            background: #D1FAE5;
            color: #065F46;
        }

        .grade-badge.grade-b {
            background: #E0E7FF;
            color: var(--primary-dark);
        }

        /* CARD 3: ASSIGNMENT DEADLINES */
        .deadlines-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .deadline-item {
            background: var(--light);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            padding: 1.15rem 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            transition: var(--transition);
        }

        .deadline-item:hover {
            background: var(--white);
            border-color: var(--primary-light);
            box-shadow: var(--shadow-sm);
        }

        .deadline-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .deadline-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--dark);
        }

        .deadline-due {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--danger);
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .deadline-progress-wrap {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .progress-bar-container {
            flex: 1;
            height: 8px;
            background: var(--gray-200);
            border-radius: 9999px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.8s ease;
        }

        .progress-fill.in-progress {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .progress-fill.not-started {
            background: var(--gray-400);
        }

        .deadline-percent {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--gray-700);
            min-width: 40px;
            text-align: right;
        }

        /* RIGHT COLUMN CARDS */

        /* ANNOUNCEMENTS CARD */
        .announcements-list {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .announcement-item {
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
            padding: 0.85rem;
            border-radius: var(--radius-md);
            background: var(--light);
            border: 1px solid var(--gray-100);
            text-decoration: none;
            transition: var(--transition);
        }

        .announcement-item:hover {
            background: #EEF2FF;
            border-color: var(--primary-light);
            transform: translateX(3px);
        }

        .announcement-dot {
            font-size: 0.8rem;
            margin-top: 0.2rem;
        }

        .announcement-details {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            flex: 1;
        }

        .announcement-text {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--dark);
            line-height: 1.35;
        }

        .announcement-date {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        /* QUICK LINKS GRID */
        .quick-links-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.85rem;
        }

        .quick-link-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            padding: 1rem 0.5rem;
            background: var(--light);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            text-decoration: none;
            color: var(--gray-700);
            transition: var(--transition);
            cursor: pointer;
        }

        .quick-link-btn:hover {
            background: var(--white);
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-3px);
            box-shadow: var(--shadow-sm);
        }

        .quick-link-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #EEF2FF;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .quick-link-btn:hover .quick-link-icon {
            background: var(--primary);
            color: var(--white);
        }

        .quick-link-label {
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* COURSE PROGRESS */
        .progress-list {
            display: flex;
            flex-direction: column;
            gap: 1.15rem;
        }

        .course-progress-item {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
        }

        .course-progress-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .course-progress-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--dark);
        }

        .course-progress-val {
            font-size: 0.825rem;
            font-weight: 700;
            color: var(--primary);
        }

        .course-progress-track {
            height: 7px;
            background: var(--gray-200);
            border-radius: 9999px;
            overflow: hidden;
        }

        .course-progress-fill {
            height: 100%;
            border-radius: 9999px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
        }

        /* UPCOMING EVENTS */
        .events-list {
            display: flex;
            flex-direction: column;
            gap: 0.95rem;
        }

        .event-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            border-radius: var(--radius-md);
            background: var(--light);
            border: 1px solid var(--gray-100);
            transition: var(--transition);
        }

        .event-item:hover {
            background: var(--white);
            border-color: var(--secondary);
            transform: translateX(3px);
            box-shadow: var(--shadow-xs);
        }

        .event-date-box {
            width: 48px;
            height: 48px;
            background: var(--white);
            border: 1.5px solid var(--gray-200);
            border-radius: var(--radius-sm);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .event-month {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--primary);
        }

        .event-day {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--dark);
            line-height: 1;
        }

        .event-info {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .event-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--dark);
        }

        .event-meta {
            font-size: 0.75rem;
            color: var(--gray-500);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* MOBILE SIDEBAR OVERLAY */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* QUICK MODAL (FOR SUBMIT ASSIGNMENT) */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .modal-backdrop.active {
            display: flex;
            animation: fadeIn 0.2s ease;
        }

        .modal-box {
            background: var(--white);
            border-radius: var(--radius-xl);
            width: 100%;
            max-width: 520px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            animation: modalScale 0.25s ease;
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            background: var(--light);
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--dark);
        }

        .modal-close-btn {
            background: transparent;
            border: none;
            font-size: 1.2rem;
            color: var(--gray-500);
            cursor: pointer;
            padding: 0.25rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            background: var(--light);
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        /* ANIMATIONS */
        @keyframes floatBubble {
            0% { transform: translateY(0px); }
            100% { transform: translateY(-8px); }
        }

        @keyframes pulseDot {
            0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(79, 70, 229, 0); }
            100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
        }

        @keyframes pulseBg {
            0% { opacity: 0.85; }
            50% { opacity: 1; }
            100% { opacity: 0.85; }
        }

        @keyframes dropdownFade {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes modalScale {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* RESPONSIVE BREAKPOINTS */
        @media (max-width: 1200px) {
            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 992px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .menu-toggle {
                display: block;
            }

            .welcome-banner {
                flex-direction: column;
                align-items: flex-start;
                gap: 1.5rem;
            }

            .banner-graphic {
                padding-left: 0;
                width: 100%;
                justify-content: flex-start;
            }
        }

        @media (max-width: 640px) {
            .top-nav {
                padding: 0 1rem;
            }

            .content-area {
                padding: 1.25rem 1rem;
            }

            .stats-row {
                grid-template-columns: 1fr;
            }

            .academic-badge {
                display: none;
            }

            .user-info {
                display: none;
            }

            .banner-title {
                font-size: 1.5rem;
            }

            .quick-links-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
    <?php app_shell_styles(); ?>
</head>
<body>


<?php app_sidebar($user, 'index.php'); ?>

    <!-- MAIN CONTENT -->
    <div class="main-content app-main">
<?php app_topbar($user, ['id' => 'globalSearch', 'placeholder' => 'Search courses, assignments, professors...']); ?>

        <!-- PAGE CONTENT -->
        <main class="content-area">
            <!-- PAGE HEADER -->
            <div class="page-header">
                <h1>Dashboard</h1>
                <p class="page-subtitle">Welcome back to your academic overview and student portal.</p>
            </div>

            <!-- WELCOME BANNER -->
            <section class="welcome-banner">
                <div class="banner-shapes"></div>
                <div class="banner-glow"></div>
                
                <div class="banner-text-wrap">
                    <div class="banner-date-pill">
                        <i class="fa-regular fa-calendar"></i>
                        <span id="currentDateDisplay">Tuesday, October 13, 2026</span>
                    </div>
                    <h2 class="banner-title" id="greetingMessage">Welcome back, <?= $e($user['first_name']) ?>! 👋</h2>
                    <p class="banner-desc">
                        You have <strong>3 assignments due this week</strong> and <strong>1 exam coming up</strong>. Stay on top of your deadlines and prepare for the upcoming midterms!
                    </p>
                    <div class="banner-actions">
                        <a href="pages/academics/schedule.php" class="btn btn-white">
                            <i class="fa-solid fa-calendar-week"></i>
                            <span>View Schedule</span>
                        </a>
                        <button type="button" class="btn btn-glass" id="openSubmitModalBtn">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Submit Assignment</span>
                        </button>
                    </div>
                </div>

                <div class="banner-graphic">
                    <div class="banner-stat-bubble">
                        <div class="bubble-number">3.75</div>
                        <div class="bubble-label">Semester GPA</div>
                        <div style="font-size: 0.75rem; color: #6EE7B7; margin-top: 0.25rem;">
                            <i class="fa-solid fa-arrow-trend-up"></i> Top 5% Rank
                        </div>
                    </div>
                </div>
            </section>

            <!-- STATS ROW (4 STAT-CARDS) -->
            <section class="stats-row">
                <!-- Stat 1: Courses Enrolled -->
                <div class="stat-card">
                    <div class="stat-info">
                        <span class="stat-title">Courses Enrolled</span>
                        <span class="stat-count">6</span>
                        <span class="stat-subtext">
                            <i class="fa-solid fa-book-bookmark"></i> 18 Total Credit Units
                        </span>
                    </div>
                    <div class="stat-icon-box primary">
                        <i class="fa-solid fa-book"></i>
                    </div>
                </div>

                <!-- Stat 2: Assignments Due -->
                <div class="stat-card">
                    <div class="stat-info">
                        <span class="stat-title">Assignments Due</span>
                        <span class="stat-count">3</span>
                        <span class="stat-subtext warning">
                            <i class="fa-solid fa-clock"></i> 2 Due this week
                        </span>
                    </div>
                    <div class="stat-icon-box warning">
                        <i class="fa-solid fa-tasks"></i>
                    </div>
                </div>

                <!-- Stat 3: Current GPA -->
                <div class="stat-card">
                    <div class="stat-info">
                        <span class="stat-title">Current GPA</span>
                        <span class="stat-count">3.75</span>
                        <span class="stat-subtext positive">
                            <i class="fa-solid fa-award"></i> Dean's Lister Candidate
                        </span>
                    </div>
                    <div class="stat-icon-box success">
                        <i class="fa-solid fa-chart-bar"></i>
                    </div>
                </div>

                <!-- Stat 4: Upcoming Exams -->
                <div class="stat-card">
                    <div class="stat-info">
                        <span class="stat-title">Upcoming Exams</span>
                        <span class="stat-count">1</span>
                        <span class="stat-subtext">
                            <i class="fa-solid fa-calendar-day"></i> Midterms Oct 20 - 24
                        </span>
                    </div>
                    <div class="stat-icon-box secondary">
                        <i class="fa-solid fa-file-pen"></i>
                    </div>
                </div>
            </section>

            <!-- MAIN GRID (2/3 + 1/3 LAYOUT) -->
            <div class="dashboard-grid">
                
                <!-- LEFT COLUMN (2/3 WIDTH) -->
                <div class="grid-column">
                    
                    <!-- 1. UPCOMING SCHEDULE CARD -->
                    <div class="content-card">
                        <div class="card-header-row">
                            <div class="card-header-left">
                                <div class="card-header-icon" style="background: #EEF2FF; color: var(--primary);">
                                    <i class="fa-solid fa-calendar-day"></i>
                                </div>
                                <div>
                                    <h3 class="card-title">Upcoming Schedule</h3>
                                    <div style="font-size: 0.775rem; color: var(--gray-500);">Today's Scheduled Lectures & Laboratories</div>
                                </div>
                            </div>
                            <a href="pages/academics/schedule.php" class="card-action-link">
                                Full Schedule <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>

                        <!-- Schedule Timeline -->
                        <div class="timeline-list">
                            <!-- Class 1: 8:00 AM -->
                            <div class="timeline-item completed">
                                <div class="timeline-node" title="Completed"></div>
                                <div class="timeline-content">
                                    <div class="timeline-main-info">
                                        <div class="timeline-time-badge">
                                            <i class="fa-solid fa-clock"></i> 8:00 AM - 9:30 AM
                                        </div>
                                        <div class="timeline-subject">Data Structures & Algorithms</div>
                                        <div class="timeline-meta">
                                            <span><i class="fa-solid fa-door-open"></i> Room 301</span>
                                            <span><i class="fa-solid fa-chalkboard-user"></i> Prof. Mendez</span>
                                        </div>
                                    </div>
                                    <span class="status-pill completed">Completed</span>
                                </div>
                            </div>

                            <!-- Class 2: 10:00 AM -->
                            <div class="timeline-item completed">
                                <div class="timeline-node" title="Completed"></div>
                                <div class="timeline-content">
                                    <div class="timeline-main-info">
                                        <div class="timeline-time-badge">
                                            <i class="fa-solid fa-clock"></i> 10:00 AM - 11:30 AM
                                        </div>
                                        <div class="timeline-subject">Ethics (Differential Equations)</div>
                                        <div class="timeline-meta">
                                            <span><i class="fa-solid fa-door-open"></i> Room 205</span>
                                            <span><i class="fa-solid fa-chalkboard-user"></i> Prof. Picart</span>
                                        </div>
                                    </div>
                                    <span class="status-pill completed">Completed</span>
                                </div>
                            </div>

                            <!-- Class 3: 1:00 PM -->
                            <div class="timeline-item active">
                                <div class="timeline-node" title="In Progress"></div>
                                <div class="timeline-content">
                                    <div class="timeline-main-info">
                                        <div class="timeline-time-badge" style="color: var(--primary);">
                                            <i class="fa-solid fa-circle-play"></i> 1:00 PM - 2:30 PM
                                        </div>
                                        <div class="timeline-subject">Web Development & Architecture</div>
                                        <div class="timeline-meta">
                                            <span><i class="fa-solid fa-desktop"></i> Lab 102</span>
                                            <span><i class="fa-solid fa-chalkboard-user"></i> Prof. Marzo</span>
                                        </div>
                                    </div>
                                    <span class="status-pill in-progress">In Progress</span>
                                </div>
                            </div>

                            <!-- Class 4: 3:00 PM -->
                            <div class="timeline-item upcoming">
                                <div class="timeline-node" title="Upcoming"></div>
                                <div class="timeline-content">
                                    <div class="timeline-main-info">
                                        <div class="timeline-time-badge" style="color: var(--secondary);">
                                            <i class="fa-solid fa-hourglass-start"></i> 3:00 PM - 4:30 PM
                                        </div>
                                        <div class="timeline-subject">Desktop Publishing Laboratory</div>
                                        <div class="timeline-meta">
                                            <span><i class="fa-solid fa-flask"></i> Sci Bldg 401</span>
                                            <span><i class="fa-solid fa-chalkboard-user"></i> Prof. Abion</span>
                                        </div>
                                    </div>
                                    <span class="status-pill upcoming">Upcoming</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. RECENT GRADES CARD -->
                    <div class="content-card">
                        <div class="card-header-row">
                            <div class="card-header-left">
                                <div class="card-header-icon" style="background: #D1FAE5; color: var(--success);">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </div>
                                <div>
                                    <h3 class="card-title">Recent Grades</h3>
                                    <div style="font-size: 0.775rem; color: var(--gray-500);">Evaluations from the last 14 days</div>
                                </div>
                            </div>
                            <a href="pages/academics/grades.php" class="card-action-link">
                                View All Grades <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>

                        <!-- Grades Table -->
                        <div class="table-responsive">
                            <table class="grades-table">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Assignment</th>
                                        <th>Grade</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="course-cell">
                                                <span class="course-tag" style="background: #4F46E5;"></span>
                                                Data Structures
                                            </div>
                                        </td>
                                        <td>Quiz 3: Binary Trees</td>
                                        <td><span class="grade-badge grade-a">92 / 100</span></td>
                                        <td>Oct 10, 2026</td>
                                        <td><span style="color: var(--success); font-weight: 600; font-size: 0.8rem;"><i class="fa-solid fa-check"></i> Recorded</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="course-cell">
                                                <span class="course-tag" style="background: #06B6D4;"></span>
                                                Ethics
                                            </div>
                                        </td>
                                        <td>Problem Set 5: Integration</td>
                                        <td><span class="grade-badge grade-b">88 / 100</span></td>
                                        <td>Oct 8, 2026</td>
                                        <td><span style="color: var(--success); font-weight: 600; font-size: 0.8rem;"><i class="fa-solid fa-check"></i> Recorded</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="course-cell">
                                                <span class="course-tag" style="background: #10B981;"></span>
                                                Web Development
                                            </div>
                                        </td>
                                        <td>Project 1: Responsive Portfolio</td>
                                        <td><span class="grade-badge grade-a">95 / 100</span></td>
                                        <td>Oct 5, 2026</td>
                                        <td><span style="color: var(--success); font-weight: 600; font-size: 0.8rem;"><i class="fa-solid fa-check"></i> Recorded</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="course-cell">
                                                <span class="course-tag" style="background: #F59E0B;"></span>
                                                Desktop Publishing Lab
                                            </div>
                                        </td>
                                        <td>Lab Report 3: Thermodynamics</td>
                                        <td><span class="grade-badge grade-a">90 / 100</span></td>
                                        <td>Oct 3, 2026</td>
                                        <td><span style="color: var(--success); font-weight: 600; font-size: 0.8rem;"><i class="fa-solid fa-check"></i> Recorded</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 3. ASSIGNMENT DEADLINES CARD -->
                    <div class="content-card">
                        <div class="card-header-row">
                            <div class="card-header-left">
                                <div class="card-header-icon" style="background: #FEF3C7; color: #D97706;">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                </div>
                                <div>
                                    <h3 class="card-title">Assignment Deadlines</h3>
                                    <div style="font-size: 0.775rem; color: var(--gray-500);">Current tasks requiring submission</div>
                                </div>
                            </div>
                            <a href="pages/academics/assignments.php" class="card-action-link">
                                All Assignments <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>

                        <!-- Assignment Deadlines List -->
                        <div class="deadlines-list">
                            <!-- Deadline 1 -->
                            <div class="deadline-item">
                                <div class="deadline-top">
                                    <div>
                                        <div class="deadline-title">Database Design Report</div>
                                        <div style="font-size: 0.775rem; color: var(--gray-500);">Course: Database Management Systems</div>
                                    </div>
                                    <div class="deadline-due">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        <span>Due Oct 15 (2 days left)</span>
                                    </div>
                                </div>
                                <div class="deadline-progress-wrap">
                                    <div class="progress-bar-container">
                                        <div class="progress-fill in-progress" style="width: 60%;"></div>
                                    </div>
                                    <div class="deadline-percent">60%</div>
                                    <span class="status-pill in-progress" style="font-size: 0.7rem; padding: 0.2rem 0.6rem;">In Progress</span>
                                </div>
                            </div>

                            <!-- Deadline 2 -->
                            <div class="deadline-item">
                                <div class="deadline-top">
                                    <div>
                                        <div class="deadline-title">Calculus Problem Set 6</div>
                                        <div style="font-size: 0.775rem; color: var(--gray-500);">Course: Ethics</div>
                                    </div>
                                    <div class="deadline-due" style="color: var(--gray-600);">
                                        <i class="fa-regular fa-clock"></i>
                                        <span>Due Oct 17 (4 days left)</span>
                                    </div>
                                </div>
                                <div class="deadline-progress-wrap">
                                    <div class="progress-bar-container">
                                        <div class="progress-fill not-started" style="width: 0%;"></div>
                                    </div>
                                    <div class="deadline-percent">0%</div>
                                    <span class="status-pill" style="background: var(--gray-200); color: var(--gray-600); font-size: 0.7rem; padding: 0.2rem 0.6rem;">Not Started</span>
                                </div>
                            </div>

                            <!-- Deadline 3 -->
                            <div class="deadline-item">
                                <div class="deadline-top">
                                    <div>
                                        <div class="deadline-title">Web Dev Project 2: Interactive SPA</div>
                                        <div style="font-size: 0.775rem; color: var(--gray-500);">Course: Web Development</div>
                                    </div>
                                    <div class="deadline-due" style="color: var(--gray-600);">
                                        <i class="fa-regular fa-clock"></i>
                                        <span>Due Oct 20 (7 days left)</span>
                                    </div>
                                </div>
                                <div class="deadline-progress-wrap">
                                    <div class="progress-bar-container">
                                        <div class="progress-fill in-progress" style="width: 30%;"></div>
                                    </div>
                                    <div class="deadline-percent">30%</div>
                                    <span class="status-pill in-progress" style="font-size: 0.7rem; padding: 0.2rem 0.6rem;">In Progress</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN (1/3 WIDTH) -->
                <div class="grid-column">
                    
                    <!-- 1. ANNOUNCEMENTS CARD -->
                    <div class="content-card">
                        <div class="card-header-row">
                            <div class="card-header-left">
                                <div class="card-header-icon" style="background: #FEE2E2; color: var(--danger);">
                                    <i class="fa-solid fa-bullhorn"></i>
                                </div>
                                <h3 class="card-title">Announcements</h3>
                            </div>
                            <a href="pages/communication/announcements.php" class="card-action-link">
                                View All <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="announcements-list">
                            <a href="pages/communication/announcements.php" class="announcement-item">
                                <span class="announcement-dot" title="Urgent">🔴</span>
                                <div class="announcement-details">
                                    <div class="announcement-text">Midterm Schedule Released</div>
                                    <div class="announcement-date">Oct 12, 2026 • Office of the Registrar</div>
                                </div>
                            </a>

                            <a href="pages/communication/announcements.php" class="announcement-item">
                                <span class="announcement-dot" title="Notice">🟡</span>
                                <div class="announcement-details">
                                    <div class="announcement-text">Library Hours Extended for Exams</div>
                                    <div class="announcement-date">Oct 10, 2026 • University Library</div>
                                </div>
                            </a>

                            <a href="pages/communication/announcements.php" class="announcement-item">
                                <span class="announcement-dot" title="Opportunity">🔵</span>
                                <div class="announcement-details">
                                    <div class="announcement-text">Career Fair Oct 25 Registration</div>
                                    <div class="announcement-date">Oct 9, 2026 • Career Center</div>
                                </div>
                            </a>

                            <a href="pages/communication/announcements.php" class="announcement-item">
                                <span class="announcement-dot" title="Scholarship">🟢</span>
                                <div class="announcement-details">
                                    <div class="announcement-text">New Scholarship Available</div>
                                    <div class="announcement-date">Oct 7, 2026 • Financial Aid Office</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- 2. QUICK LINKS CARD -->
                    <div class="content-card">
                        <div class="card-header-row">
                            <div class="card-header-left">
                                <div class="card-header-icon" style="background: #E0E7FF; color: var(--primary);">
                                    <i class="fa-solid fa-bolt"></i>
                                </div>
                                <h3 class="card-title">Quick Links</h3>
                            </div>
                        </div>

                        <div class="quick-links-grid">
                            <!-- Library -->
                            <a href="https://library.eduportal.edu" target="_blank" class="quick-link-btn" title="Digital Library">
                                <div class="quick-link-icon">
                                    <i class="fa-solid fa-book-reader"></i>
                                </div>
                                <span class="quick-link-label">Library</span>
                            </a>

                            <!-- Email -->
                            <a href="pages/communication/messaging.php" class="quick-link-btn" title="Webmail">
                                <div class="quick-link-icon">
                                    <i class="fa-solid fa-envelope-open-text"></i>
                                </div>
                                <span class="quick-link-label">Email</span>
                            </a>

                            <!-- Calendar -->
                            <a href="pages/academics/schedule.php" class="quick-link-btn" title="Academic Calendar">
                                <div class="quick-link-icon">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </div>
                                <span class="quick-link-label">Calendar</span>
                            </a>

                            <!-- Grades -->
                            <a href="pages/academics/grades.php" class="quick-link-btn" title="Grade Transcripts">
                                <div class="quick-link-icon">
                                    <i class="fa-solid fa-chart-line"></i>
                                </div>
                                <span class="quick-link-label">Grades</span>
                            </a>

                            <!-- Scholarships -->
                            <a href="pages/admin/scholarships.php" class="quick-link-btn" title="Scholarships & Financial Aid">
                                <div class="quick-link-icon">
                                    <i class="fa-solid fa-award"></i>
                                </div>
                                <span class="quick-link-label">Scholarships</span>
                            </a>

                            <!-- Office Hours -->
                            <a href="pages/communication/office-hours.php" class="quick-link-btn" title="Book a consultation with a professor">
                                <div class="quick-link-icon">
                                    <i class="fa-solid fa-video"></i>
                                </div>
                                <span class="quick-link-label">Office Hours</span>
                            </a>
                        </div>
                    </div>

                    <!-- 3. COURSE PROGRESS CARD -->
                    <div class="content-card">
                        <div class="card-header-row">
                            <div class="card-header-left">
                                <div class="card-header-icon" style="background: #E0F2FE; color: var(--secondary);">
                                    <i class="fa-solid fa-bars-progress"></i>
                                </div>
                                <h3 class="card-title">Course Progress</h3>
                            </div>
                            <a href="pages/academics/courses.php" class="card-action-link">
                                Courses <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="progress-list">
                            <!-- Course 1 -->
                            <div class="course-progress-item">
                                <div class="course-progress-info">
                                    <span class="course-progress-name">Data Structures</span>
                                    <span class="course-progress-val">65%</span>
                                </div>
                                <div class="course-progress-track">
                                    <div class="course-progress-fill" style="width: 65%;"></div>
                                </div>
                            </div>

                            <!-- Course 2 -->
                            <div class="course-progress-item">
                                <div class="course-progress-info">
                                    <span class="course-progress-name">Ethics</span>
                                    <span class="course-progress-val">58%</span>
                                </div>
                                <div class="course-progress-track">
                                    <div class="course-progress-fill" style="width: 58%;"></div>
                                </div>
                            </div>

                            <!-- Course 3 -->
                            <div class="course-progress-item">
                                <div class="course-progress-info">
                                    <span class="course-progress-name">Web Development</span>
                                    <span class="course-progress-val">72%</span>
                                </div>
                                <div class="course-progress-track">
                                    <div class="course-progress-fill" style="width: 72%;"></div>
                                </div>
                            </div>

                            <!-- Course 4 -->
                            <div class="course-progress-item">
                                <div class="course-progress-info">
                                    <span class="course-progress-name">Desktop Publishing Lab</span>
                                    <span class="course-progress-val">60%</span>
                                </div>
                                <div class="course-progress-track">
                                    <div class="course-progress-fill" style="width: 60%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. UPCOMING EVENTS CARD -->
                    <div class="content-card">
                        <div class="card-header-row">
                            <div class="card-header-left">
                                <div class="card-header-icon" style="background: #FEF3C7; color: var(--accent);">
                                    <i class="fa-solid fa-calendar-star"></i>
                                </div>
                                <h3 class="card-title">Upcoming Events</h3>
                            </div>
                            <a href="pages/student-life/events.php" class="card-action-link">
                                View Events <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="events-list">
                            <!-- Event 1 -->
                            <div class="event-item">
                                <div class="event-date-box">
                                    <span class="event-month">Oct</span>
                                    <span class="event-day">15</span>
                                </div>
                                <div class="event-info">
                                    <div class="event-title">Tech Talk: AI in Education</div>
                                    <div class="event-meta">
                                        <i class="fa-solid fa-location-dot"></i> Audi Hall A • 4:00 PM
                                    </div>
                                </div>
                            </div>

                            <!-- Event 2 -->
                            <div class="event-item">
                                <div class="event-date-box">
                                    <span class="event-month">Oct</span>
                                    <span class="event-day">18</span>
                                </div>
                                <div class="event-info">
                                    <div class="event-title">Basketball Game vs State U</div>
                                    <div class="event-meta">
                                        <i class="fa-solid fa-trophy"></i> University Coliseum • 6:30 PM
                                    </div>
                                </div>
                            </div>

                            <!-- Event 3 -->
                            <div class="event-item">
                                <div class="event-date-box">
                                    <span class="event-month">Oct</span>
                                    <span class="event-day">25</span>
                                </div>
                                <div class="event-info">
                                    <div class="event-title">Career Fair 2026</div>
                                    <div class="event-meta">
                                        <i class="fa-solid fa-building"></i> University Sports Complex • 9 AM
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <!-- QUICK MODAL: SUBMIT ASSIGNMENT -->
    <div class="modal-backdrop" id="submitModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3><i class="fa-solid fa-cloud-arrow-up" style="color: var(--primary); margin-right: 0.5rem;"></i> Submit Assignment</h3>
                <button class="modal-close-btn" id="closeSubmitModalBtn" aria-label="Close dialog">&times;</button>
            </div>
            <div class="modal-body">
                <form id="quickSubmissionForm" onsubmit="event.preventDefault(); handleModalSubmit();">
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem;">Select Course Assignment</label>
                        <select style="width: 100%; height: 44px; border: 1.5px solid var(--gray-200); border-radius: 8px; padding: 0 0.85rem; font-size: 0.9rem;" required>
                            <option value="">-- Choose an open assignment --</option>
                            <option value="db">Database Design Report (Due Oct 15)</option>
                            <option value="calc">Calculus Problem Set 6 (Due Oct 17)</option>
                            <option value="web">Web Dev Project 2: SPA (Due Oct 20)</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem;">Upload Deliverable (PDF, ZIP, or DOCX)</label>
                        <div style="border: 2px dashed var(--gray-300); border-radius: 12px; padding: 1.5rem; text-align: center; background: var(--light); cursor: pointer;" onclick="document.getElementById('fileUploadInput').click()">
                            <i class="fa-solid fa-file-arrow-up" style="font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem;"></i>
                            <div style="font-size: 0.85rem; font-weight: 600;">Click to upload or drag files here</div>
                            <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem;">Maximum file size: 50MB</div>
                            <input type="file" id="fileUploadInput" style="display: none;" onchange="updateFileName(this)">
                        </div>
                        <div id="selectedFileName" style="font-size: 0.8rem; color: var(--success); font-weight: 600; margin-top: 0.5rem; display: none;"></div>
                    </div>

                    <div style="margin-bottom: 0.5rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem;">Submission Note (Optional)</label>
                        <textarea rows="2" style="width: 100%; border: 1.5px solid var(--gray-200); border-radius: 8px; padding: 0.5rem 0.85rem; font-size: 0.9rem; font-family: inherit;" placeholder="Add remarks or instructions for the instructor..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" style="border: 1px solid var(--gray-200);" id="cancelModalBtn">Cancel</button>
                <button type="button" class="btn" style="background: var(--primary); color: var(--white);" onclick="handleModalSubmit()">
                    <i class="fa-solid fa-paper-plane"></i> Submit File
                </button>
            </div>
        </div>
    </div>

    <!-- MAIN JAVASCRIPT BUNDLE -->
    <script src="js/main.js?v=9"></script>

    <!-- DASHBOARD SPECIFIC SCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Signed-in Student (from the PHP session)
            const firstName = <?= json_encode($user['first_name'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

            // Dynamic Time-Based Greeting
            const greetingEl = document.getElementById('greetingMessage');
            const currentDateEl = document.getElementById('currentDateDisplay');

            function updateTimeAndGreeting() {
                const now = new Date();
                const hours = now.getHours();
                let timeGreeting = 'Welcome back';

                if (hours >= 5 && hours < 12) {
                    timeGreeting = 'Good morning';
                } else if (hours >= 12 && hours < 17) {
                    timeGreeting = 'Good afternoon';
                } else if (hours >= 17 && hours < 22) {
                    timeGreeting = 'Good evening';
                } else {
                    timeGreeting = 'Hello night owl';
                }

                if (greetingEl) {
                    greetingEl.textContent = `${timeGreeting}, ${firstName}! 👋`;
                }

                // Format current date
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                if (currentDateEl) {
                    currentDateEl.textContent = now.toLocaleDateString('en-US', options);
                }
            }

            updateTimeAndGreeting();

            // Quick Submit Assignment Modal Handling
            const submitModal = document.getElementById('submitModal');
            const openSubmitModalBtn = document.getElementById('openSubmitModalBtn');
            const closeSubmitModalBtn = document.getElementById('closeSubmitModalBtn');
            const cancelModalBtn = document.getElementById('cancelModalBtn');

            if (openSubmitModalBtn && submitModal) {
                openSubmitModalBtn.addEventListener('click', () => {
                    submitModal.classList.add('active');
                });
            }

            function closeModal() {
                if (submitModal) submitModal.classList.remove('active');
            }

            if (closeSubmitModalBtn) closeSubmitModalBtn.addEventListener('click', closeModal);
            if (cancelModalBtn) cancelModalBtn.addEventListener('click', closeModal);

            if (submitModal) {
                submitModal.addEventListener('click', (e) => {
                    if (e.target === submitModal) closeModal();
                });
            }

            // Global search keyboard shortcut ('/')
            const globalSearch = document.getElementById('globalSearch');
            document.addEventListener('keydown', (e) => {
                if (e.key === '/' && document.activeElement !== globalSearch && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    if (globalSearch) {
                        globalSearch.focus();
                        globalSearch.select();
                    }
                }
            });
        });

        // File upload helper
        function updateFileName(input) {
            const fileNameDisplay = document.getElementById('selectedFileName');
            if (input.files && input.files[0]) {
                fileNameDisplay.textContent = `Attached: ${input.files[0].name} (${Math.round(input.files[0].size / 1024)} KB)`;
                fileNameDisplay.style.display = 'block';
            }
        }

        // Handle modal submission
        function handleModalSubmit() {
            alert('Assignment uploaded and recorded successfully! Notification dispatched to course professor.');
            const modal = document.getElementById('submitModal');
            if (modal) modal.classList.remove('active');
        }
    </script>
</body>
</html>
