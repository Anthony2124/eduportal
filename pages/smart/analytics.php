<?php
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/smart/analytics.php';
$user = require_page_access($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Analytics - EduPortal</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Base Stylesheet -->
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        /* Analytics Page-Specific Styles */
        .analytics-grid-top {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
            padding: 1.25rem 1.5rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: transform var(--transition-fast), box-shadow var(--transition-fast);
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .metric-details {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .metric-label {
            font-size: 0.8125rem;
            color: var(--gray-500);
            font-weight: 500;
        }

        .metric-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--dark);
            letter-spacing: -0.02em;
        }

        .metric-sub {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .trend-up { color: var(--success); }
        .trend-neutral { color: var(--gray-500); }

        .metric-icon-box {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .icon-hours { background: rgba(79, 70, 229, 0.1); color: var(--primary); }
        .icon-rate { background: rgba(6, 182, 212, 0.1); color: var(--secondary); }
        .icon-quiz { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .icon-streak { background: rgba(245, 158, 11, 0.1); color: var(--accent); }

        /* Main Analytics Layout */
        .analytics-main-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Chart Area Components */
        .chart-header-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .time-pill-btn {
            padding: 0.35rem 0.75rem;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gray-500);
            background: var(--gray-100);
            transition: all var(--transition-fast);
        }

        .time-pill-btn.active, .time-pill-btn:hover {
            background: var(--primary);
            color: var(--white);
        }

        /* Mock Bar Chart */
        .bar-chart-container {
            margin-top: 1.5rem;
            padding: 1rem 0;
        }

        .chart-benchmark-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            padding: 0.625rem 0.875rem;
            background: #F8FAFC;
            border-radius: var(--radius-md);
            font-size: 0.8125rem;
        }

        .bars-wrapper {
            height: 220px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
            padding: 0 1rem;
            border-bottom: 2px solid var(--gray-200);
            position: relative;
        }

        .benchmark-line {
            position: absolute;
            left: 0;
            right: 0;
            top: 40px; /* corresponds to 4h avg */
            border-top: 2px dashed rgba(245, 158, 11, 0.6);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 0.5rem;
        }

        .benchmark-tag {
            background: rgba(245, 158, 11, 0.15);
            color: #B45309;
            font-size: 0.6875rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            transform: translateY(-50%);
        }

        .bar-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            justify-content: flex-end;
        }

        .bar-fill {
            width: 100%;
            max-width: 36px;
            border-radius: 6px 6px 0 0;
            background: linear-gradient(180deg, var(--primary-light), var(--primary));
            transition: all 0.4s ease;
            position: relative;
            cursor: pointer;
        }

        .bar-fill:hover {
            filter: brightness(1.15);
            transform: scaleY(1.03);
            transform-origin: bottom;
        }

        .bar-fill::after {
            content: attr(data-hours);
            position: absolute;
            top: -24px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--dark);
        }

        .bar-day-label {
            margin-top: 0.75rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--gray-500);
        }

        .chart-legend {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1.25rem;
            font-size: 0.8125rem;
            color: var(--gray-600);
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .legend-indicator {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        /* Course Performance List */
        .course-score-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--gray-100);
        }
        .course-score-row:last-child {
            border-bottom: none;
        }

        .course-info-col {
            flex: 1;
            padding-right: 1.5rem;
        }

        .course-code-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.35rem;
        }

        .course-code {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--dark);
        }

        .course-title {
            font-size: 0.8125rem;
            color: var(--gray-500);
        }

        .score-bar-bg {
            height: 8px;
            background: var(--gray-100);
            border-radius: var(--radius-full);
            overflow: hidden;
        }

        .score-bar-fill {
            height: 100%;
            border-radius: var(--radius-full);
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transition: width 0.6s ease;
        }

        .course-score-badge {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--dark);
            min-width: 50px;
            text-align: right;
        }

        /* Grade Prediction Cards */
        .prediction-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.875rem 1rem;
            background: var(--light);
            border-radius: var(--radius-md);
            margin-bottom: 0.625rem;
            border: 1px solid var(--gray-200);
            transition: background var(--transition-fast);
        }

        .prediction-item:hover {
            background: var(--white);
            border-color: var(--primary-light);
        }

        .pred-course {
            font-weight: 700;
            font-size: 0.875rem;
            color: var(--dark);
        }

        .pred-grade-wrap {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .pred-pill {
            font-size: 0.8125rem;
            font-weight: 700;
            padding: 0.25rem 0.625rem;
            border-radius: var(--radius-full);
        }

        .pred-pill.pred-high {
            background: rgba(16, 185, 129, 0.12);
            color: var(--success);
        }

        .pred-pill.pred-med {
            background: rgba(6, 182, 212, 0.12);
            color: var(--secondary);
        }

        .pred-trend {
            font-size: 0.875rem;
            font-weight: 800;
        }

        .pred-trend.up { color: var(--success); }
        .pred-trend.steady { color: var(--gray-400); }

        .disclaimer-box {
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            padding: 0.75rem 1rem;
            background: rgba(245, 158, 11, 0.08);
            border-radius: var(--radius-md);
            border: 1px solid rgba(245, 158, 11, 0.2);
            font-size: 0.75rem;
            color: #92400E;
            margin-top: 1rem;
        }

        /* Strengths & Improvement Section */
        .strengths-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .strength-col {
            background: #F8FAFC;
            border-radius: var(--radius-md);
            padding: 1rem 1.25rem;
            border: 1px solid var(--gray-200);
        }

        .strength-title {
            font-size: 0.875rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .tag-item {
            padding: 0.35rem 0.75rem;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .tag-success { background: rgba(16, 185, 129, 0.15); color: #047857; }
        .tag-warning { background: rgba(245, 158, 11, 0.15); color: #B45309; }

        .recommendation-banner {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.06), rgba(6, 182, 212, 0.06));
            border: 1px solid rgba(79, 70, 229, 0.2);
            border-radius: var(--radius-md);
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .recom-actions {
            display: flex;
            gap: 0.5rem;
        }

        /* Study Habits Insights */
        .habit-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.875rem 0;
            border-bottom: 1px solid var(--gray-100);
        }
        .habit-row:last-child {
            border-bottom: none;
        }

        .habit-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            background: var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.125rem;
            flex-shrink: 0;
        }

        .habit-content h4 {
            font-size: 0.8125rem;
            color: var(--gray-500);
            font-weight: 500;
        }

        .habit-content p {
            font-size: 0.9375rem;
            color: var(--dark);
            font-weight: 700;
        }

        @media (max-width: 1024px) {
            .analytics-grid-top {
                grid-template-columns: repeat(2, 1fr);
            }
            .analytics-main-grid {
                grid-template-columns: 1fr;
            }
            .strengths-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .analytics-grid-top {
                grid-template-columns: 1fr;
            }
            .recommendation-banner {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
    <?php app_shell_styles(); ?>
</head>
<body>
<?php app_sidebar($user, $page); ?>

    <!-- Main Content -->
    <div class="main-content app-main">
<?php app_topbar($user, ['placeholder' => 'Search analytics, courses, reports...']); ?>

        <!-- Page Content -->
        <div class="content-area">
            <div class="page-header">
                <h1>Learning Analytics</h1>
                <p class="page-subtitle">Data-driven insights into your academic performance</p>
            </div>

            <!-- Performance Overview Cards -->
            <div class="analytics-grid-top">
                <div class="metric-card">
                    <div class="metric-details">
                        <span class="metric-label">Study Hours This Week</span>
                        <div class="metric-value">24h</div>
                        <div class="metric-sub trend-up">
                            <i class="fa-solid fa-arrow-trend-up"></i>
                            <span>+3h vs last week</span>
                        </div>
                    </div>
                    <div class="metric-icon-box icon-hours">
                        <i class="fa-regular fa-clock"></i>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-details">
                        <span class="metric-label">Assignment Completion Rate</span>
                        <div class="metric-value">87%</div>
                        <div class="metric-sub trend-up">
                            <i class="fa-solid fa-check-circle"></i>
                            <span>20 of 23 submitted</span>
                        </div>
                    </div>
                    <div class="metric-icon-box icon-rate">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-details">
                        <span class="metric-label">Average Quiz Score</span>
                        <div class="metric-value">89/100</div>
                        <div class="metric-sub trend-up">
                            <i class="fa-solid fa-arrow-trend-up"></i>
                            <span>Top 10% in class</span>
                        </div>
                    </div>
                    <div class="metric-icon-box icon-quiz">
                        <i class="fa-solid fa-award"></i>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-details">
                        <span class="metric-label">Learning Streak</span>
                        <div class="metric-value">12 days 🔥</div>
                        <div class="metric-sub trend-up">
                            <i class="fa-solid fa-fire"></i>
                            <span>Personal best!</span>
                        </div>
                    </div>
                    <div class="metric-icon-box icon-streak">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                </div>
            </div>

            <!-- Main Charts Row -->
            <div class="analytics-main-grid">
                <!-- Left: Study Time Chart -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fa-solid fa-chart-column" style="color: var(--primary);"></i>
                            Weekly Study Hours
                        </h2>
                        <div class="chart-header-actions">
                            <button class="time-pill-btn active" onclick="setChartRange('thisWeek')">This Week</button>
                            <button class="time-pill-btn" onclick="setChartRange('lastWeek')">Last Week</button>
                            <button class="time-pill-btn" onclick="setChartRange('monthly')">Monthly</button>
                        </div>
                    </div>

                    <div class="chart-benchmark-info">
                        <div>
                            <strong>Total Logged:</strong> <span id="totalLoggedText" style="color: var(--primary); font-weight: 700;">24 Hours</span>
                        </div>
                        <div>
                            <strong>Target Benchmark:</strong> <span style="color: #B45309; font-weight: 700;">28h / week recommended</span>
                        </div>
                    </div>

                    <!-- Styled Div Bar Chart -->
                    <div class="bar-chart-container">
                        <div class="bars-wrapper" id="barsWrapper">
                            <div class="benchmark-line">
                                <span class="benchmark-tag">Goal 4h/day</span>
                            </div>

                            <!-- Mon: 4h (80px height scale: 4h -> 120px) -->
                            <div class="bar-col">
                                <div class="bar-fill" style="height: 120px;" data-hours="4h" title="Monday: 4 Hours"></div>
                                <span class="bar-day-label">Mon</span>
                            </div>

                            <!-- Tue: 3h -->
                            <div class="bar-col">
                                <div class="bar-fill" style="height: 90px;" data-hours="3h" title="Tuesday: 3 Hours"></div>
                                <span class="bar-day-label">Tue</span>
                            </div>

                            <!-- Wed: 5h -->
                            <div class="bar-col">
                                <div class="bar-fill" style="height: 155px; background: linear-gradient(180deg, #6366F1, var(--primary));" data-hours="5h" title="Wednesday: 5 Hours (Peak)"></div>
                                <span class="bar-day-label">Wed</span>
                            </div>

                            <!-- Thu: 3h -->
                            <div class="bar-col">
                                <div class="bar-fill" style="height: 90px;" data-hours="3h" title="Thursday: 3 Hours"></div>
                                <span class="bar-day-label">Thu</span>
                            </div>

                            <!-- Fri: 4h -->
                            <div class="bar-col">
                                <div class="bar-fill" style="height: 120px;" data-hours="4h" title="Friday: 4 Hours"></div>
                                <span class="bar-day-label">Fri</span>
                            </div>

                            <!-- Sat: 3h -->
                            <div class="bar-col">
                                <div class="bar-fill" style="height: 90px;" data-hours="3h" title="Saturday: 3 Hours"></div>
                                <span class="bar-day-label">Sat</span>
                            </div>

                            <!-- Sun: 2h -->
                            <div class="bar-col">
                                <div class="bar-fill" style="height: 60px;" data-hours="2h" title="Sunday: 2 Hours"></div>
                                <span class="bar-day-label">Sun</span>
                            </div>
                        </div>

                        <div class="chart-legend">
                            <div class="legend-item">
                                <div class="legend-indicator" style="background: var(--primary);"></div>
                                <span>Actual Study Time</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-indicator" style="background: #F59E0B;"></div>
                                <span>Recommended (28h/week)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Strengths & Areas for Improvement (below chart) -->
                    <div style="margin-top: 2rem;">
                        <h3 style="font-size: 1rem; font-weight: 700; color: var(--dark); margin-bottom: 1rem;">
                            <i class="fa-solid fa-bullseye" style="color: var(--secondary);"></i>
                            Strengths & Areas for Improvement
                        </h3>
                        <div class="strengths-grid">
                            <div class="strength-col">
                                <div class="strength-title" style="color: #047857;">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Strengths
                                </div>
                                <div class="tag-list">
                                    <span class="tag-item tag-success">Programming concepts</span>
                                    <span class="tag-item tag-success">Written communication</span>
                                    <span class="tag-item tag-success">Object-Oriented Design</span>
                                </div>
                            </div>

                            <div class="strength-col">
                                <div class="strength-title" style="color: #B45309;">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    Areas to Improve
                                </div>
                                <div class="tag-list">
                                    <span class="tag-item tag-warning">Mathematical proofs</span>
                                    <span class="tag-item tag-warning">Lab report formatting</span>
                                    <span class="tag-item tag-warning">Thermodynamics formulas</span>
                                </div>
                            </div>
                        </div>

                        <!-- Recommendations CTA -->
                        <div class="recommendation-banner">
                            <div>
                                <strong style="font-size: 0.875rem; color: var(--dark); display: block;">🎯 AI Academic Recommendations</strong>
                                <span style="font-size: 0.8125rem; color: var(--gray-600);">
                                    • Spend 2 more hours on MATH 202 · • Visit DTP 101 office hours on Thursday
                                </span>
                            </div>
                            <div class="recom-actions">
                                <a href="../../pages/communication/office-hours.php" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-calendar-plus"></i> Book Office Hours
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Grade Predictions & Habits -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Grade Predictions Card -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="fa-solid fa-brain" style="color: var(--primary);"></i>
                                AI Grade Predictions
                            </h2>
                            <span class="badge badge-primary">94% Confidence</span>
                        </div>

                        <div class="prediction-list">
                            <div class="prediction-item">
                                <div>
                                    <div class="pred-course">CS 201: Data Structures</div>
                                    <div style="font-size: 0.75rem; color: var(--gray-500);">Current Average: 91.2%</div>
                                </div>
                                <div class="pred-grade-wrap">
                                    <span class="pred-pill pred-high">Predicted A (92%)</span>
                                    <span class="pred-trend up" title="Trending Up">↑</span>
                                </div>
                            </div>

                            <div class="prediction-item">
                                <div>
                                    <div class="pred-course">MATH 202: Discrete Math</div>
                                    <div style="font-size: 0.75rem; color: var(--gray-500);">Current Average: 86.8%</div>
                                </div>
                                <div class="pred-grade-wrap">
                                    <span class="pred-pill pred-med">Predicted B+ (87%)</span>
                                    <span class="pred-trend steady" title="Steady">→</span>
                                </div>
                            </div>

                            <div class="prediction-item">
                                <div>
                                    <div class="pred-course">CS 203: OOP & Java</div>
                                    <div style="font-size: 0.75rem; color: var(--gray-500);">Current Average: 94.6%</div>
                                </div>
                                <div class="pred-grade-wrap">
                                    <span class="pred-pill pred-high">Predicted A (95%)</span>
                                    <span class="pred-trend up" title="Trending Up">↑</span>
                                </div>
                            </div>

                            <div class="prediction-item">
                                <div>
                                    <div class="pred-course">DTP 101: Univ. Desktop Publishing</div>
                                    <div style="font-size: 0.75rem; color: var(--gray-500);">Current Average: 84.5%</div>
                                </div>
                                <div class="pred-grade-wrap">
                                    <span class="pred-pill pred-med">Predicted B+ (85%)</span>
                                    <span class="pred-trend steady" title="Steady">→</span>
                                </div>
                            </div>

                            <div class="prediction-item">
                                <div>
                                    <div class="pred-course">NET 101: Tech Writing</div>
                                    <div style="font-size: 0.75rem; color: var(--gray-500);">Current Average: 89.0%</div>
                                </div>
                                <div class="pred-grade-wrap">
                                    <span class="pred-pill pred-high">Predicted A- (90%)</span>
                                    <span class="pred-trend up" title="Trending Up">↑</span>
                                </div>
                            </div>

                            <div class="prediction-item">
                                <div>
                                    <div class="pred-course">HUM 101: Ethics & Society</div>
                                    <div style="font-size: 0.75rem; color: var(--gray-500);">Current Average: 87.5%</div>
                                </div>
                                <div class="pred-grade-wrap">
                                    <span class="pred-pill pred-med">Predicted B+ (88%)</span>
                                    <span class="pred-trend steady" title="Steady">→</span>
                                </div>
                            </div>
                        </div>

                        <div class="disclaimer-box">
                            <i class="fa-solid fa-circle-info" style="font-size: 1rem; margin-top: 1px;"></i>
                            <span>Predictions based on current performance trends, historical quiz weighted averages, and attendance markers.</span>
                        </div>
                    </div>

                    <!-- Study Habits Insights Card -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="fa-solid fa-lightbulb" style="color: var(--accent);"></i>
                                Study Habits Insights
                            </h2>
                            <span class="badge badge-warning">Behavioral</span>
                        </div>

                        <div class="habits-list">
                            <div class="habit-row">
                                <div class="habit-icon">
                                    <i class="fa-solid fa-sun"></i>
                                </div>
                                <div class="habit-content">
                                    <h4>Most Productive Time</h4>
                                    <p>9:00 AM – 12:00 PM (Morning Peak)</p>
                                </div>
                            </div>

                            <div class="habit-row">
                                <div class="habit-icon">
                                    <i class="fa-solid fa-building-columns"></i>
                                </div>
                                <div class="habit-content">
                                    <h4>Favorite Study Location</h4>
                                    <p>Library 2nd Floor (Quiet Zone)</p>
                                </div>
                            </div>

                            <div class="habit-row">
                                <div class="habit-icon">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                </div>
                                <div class="habit-content">
                                    <h4>Average Session Length</h4>
                                    <p>1.5 hours (Optimal focus span)</p>
                                </div>
                            </div>

                            <div class="habit-row">
                                <div class="habit-icon">
                                    <i class="fa-solid fa-repeat"></i>
                                </div>
                                <div class="habit-content">
                                    <h4>Best Subject Retention</h4>
                                    <p>After 2 reviews within 48 hours</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Performance Radar / Overview Section -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fa-solid fa-chart-pie" style="color: var(--secondary);"></i>
                        Course Performance Overview
                    </h2>
                    <span class="badge badge-cyan">Semester Benchmark</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: center;">
                    <!-- Visual Radar SVG Simulation -->
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1rem;">
                        <svg viewBox="0 0 300 300" style="max-width: 260px; width: 100%; height: auto;">
                            <!-- Concentric Polygons for Radar Grid -->
                            <polygon points="150,30 254,90 254,210 150,270 46,210 46,90" fill="none" stroke="#E2E8F0" stroke-width="1.5" />
                            <polygon points="150,60 228,105 228,195 150,240 72,195 72,105" fill="none" stroke="#E2E8F0" stroke-width="1.5" />
                            <polygon points="150,90 202,120 202,180 150,210 98,180 98,120" fill="none" stroke="#E2E8F0" stroke-width="1.5" />
                            
                            <!-- Axes -->
                            <line x1="150" y1="150" x2="150" y2="30" stroke="#CBD5E1" stroke-width="1.5" stroke-dasharray="3,3" />
                            <line x1="150" y1="150" x2="254" y2="90" stroke="#CBD5E1" stroke-width="1.5" stroke-dasharray="3,3" />
                            <line x1="150" y1="150" x2="254" y2="210" stroke="#CBD5E1" stroke-width="1.5" stroke-dasharray="3,3" />
                            <line x1="150" y1="150" x2="150" y2="270" stroke="#CBD5E1" stroke-width="1.5" stroke-dasharray="3,3" />
                            <line x1="150" y1="150" x2="46" y2="210" stroke="#CBD5E1" stroke-width="1.5" stroke-dasharray="3,3" />
                            <line x1="150" y1="150" x2="46" y2="90" stroke="#CBD5E1" stroke-width="1.5" stroke-dasharray="3,3" />

                            <!-- Active Data Polygon (Performance points) -->
                            <!-- CS 201: 92%, CS 203: 95%, MATH 202: 87%, DTP 101: 85%, HUM 101: 88%, NET 101: 90% -->
                            <polygon points="150,40 248,96 240,202 150,252 58,202 55,98" 
                                     fill="rgba(79, 70, 229, 0.25)" 
                                     stroke="#4F46E5" 
                                     stroke-width="2.5" />
                            
                            <!-- Vertex Markers -->
                            <circle cx="150" cy="40" r="4" fill="#4F46E5" />
                            <circle cx="248" cy="96" r="4" fill="#4F46E5" />
                            <circle cx="240" cy="202" r="4" fill="#4F46E5" />
                            <circle cx="150" cy="252" r="4" fill="#4F46E5" />
                            <circle cx="58" cy="202" r="4" fill="#4F46E5" />
                            <circle cx="55" cy="98" r="4" fill="#4F46E5" />

                            <!-- Labels -->
                            <text x="150" y="20" text-anchor="middle" font-size="10" font-weight="700" fill="#1E293B">CS 201</text>
                            <text x="268" y="90" text-anchor="start" font-size="10" font-weight="700" fill="#1E293B">CS 203</text>
                            <text x="268" y="215" text-anchor="start" font-size="10" font-weight="700" fill="#1E293B">MATH 202</text>
                            <text x="150" y="288" text-anchor="middle" font-size="10" font-weight="700" fill="#1E293B">DTP 101</text>
                            <text x="32" y="215" text-anchor="end" font-size="10" font-weight="700" fill="#1E293B">HUM 101</text>
                            <text x="32" y="90" text-anchor="end" font-size="10" font-weight="700" fill="#1E293B">NET 101</text>
                        </svg>
                        <span style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.5rem;">Multi-axial Competency Polygon</span>
                    </div>

                    <!-- Scores per course list -->
                    <div class="course-scores-breakdown">
                        <div class="course-score-row">
                            <div class="course-info-col">
                                <div class="course-code-title">
                                    <span class="course-code">CS 201</span>
                                    <span class="course-title">Data Structures</span>
                                </div>
                                <div class="score-bar-bg">
                                    <div class="score-bar-fill" style="width: 92%;"></div>
                                </div>
                            </div>
                            <span class="course-score-badge">92%</span>
                        </div>

                        <div class="course-score-row">
                            <div class="course-info-col">
                                <div class="course-code-title">
                                    <span class="course-code">MATH 202</span>
                                    <span class="course-title">Discrete Mathematics</span>
                                </div>
                                <div class="score-bar-bg">
                                    <div class="score-bar-fill" style="width: 87%; background: linear-gradient(90deg, #F59E0B, #10B981);"></div>
                                </div>
                            </div>
                            <span class="course-score-badge">87%</span>
                        </div>

                        <div class="course-score-row">
                            <div class="course-info-col">
                                <div class="course-code-title">
                                    <span class="course-code">CS 203</span>
                                    <span class="course-title">Object-Oriented Programming</span>
                                </div>
                                <div class="score-bar-bg">
                                    <div class="score-bar-fill" style="width: 95%;"></div>
                                </div>
                            </div>
                            <span class="course-score-badge">95%</span>
                        </div>

                        <div class="course-score-row">
                            <div class="course-info-col">
                                <div class="course-code-title">
                                    <span class="course-code">DTP 101</span>
                                    <span class="course-title">Desktop Publishing</span>
                                </div>
                                <div class="score-bar-bg">
                                    <div class="score-bar-fill" style="width: 85%; background: linear-gradient(90deg, #F59E0B, #6366F1);"></div>
                                </div>
                            </div>
                            <span class="course-score-badge">85%</span>
                        </div>

                        <div class="course-score-row">
                            <div class="course-info-col">
                                <div class="course-code-title">
                                    <span class="course-code">NET 101</span>
                                    <span class="course-title">Networking I</span>
                                </div>
                                <div class="score-bar-bg">
                                    <div class="score-bar-fill" style="width: 90%;"></div>
                                </div>
                            </div>
                            <span class="course-score-badge">90%</span>
                        </div>

                        <div class="course-score-row">
                            <div class="course-info-col">
                                <div class="course-code-title">
                                    <span class="course-code">HUM 101</span>
                                    <span class="course-title">Ethics & Technology</span>
                                </div>
                                <div class="score-bar-bg">
                                    <div class="score-bar-fill" style="width: 88%;"></div>
                                </div>
                            </div>
                            <span class="course-score-badge">88%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../js/main.js?v=9"></script>
    <script>
        function setChartRange(range) {
            document.querySelectorAll('.time-pill-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            const totalText = document.getElementById('totalLoggedText');
            const bars = document.querySelectorAll('.bar-fill');

            if (range === 'thisWeek') {
                totalText.textContent = '24 Hours';
                const heights = ['120px', '90px', '155px', '90px', '120px', '90px', '60px'];
                const hours = ['4h', '3h', '5h', '3h', '4h', '3h', '2h'];
                bars.forEach((b, i) => {
                    b.style.height = heights[i];
                    b.setAttribute('data-hours', hours[i]);
                });
                showToast('Displaying This Week study hours', 'indigo');
            } else if (range === 'lastWeek') {
                totalText.textContent = '21 Hours';
                const heights = ['90px', '90px', '120px', '60px', '90px', '120px', '60px'];
                const hours = ['3h', '3h', '4h', '2h', '3h', '4h', '2h'];
                bars.forEach((b, i) => {
                    b.style.height = heights[i];
                    b.setAttribute('data-hours', hours[i]);
                });
                showToast('Displaying Last Week study hours', 'indigo');
            } else if (range === 'monthly') {
                totalText.textContent = '96 Hours (Avg 24h/wk)';
                const heights = ['110px', '130px', '140px', '115px', '120px', '100px', '85px'];
                const hours = ['3.8h', '4.3h', '4.8h', '3.9h', '4.0h', '3.4h', '2.8h'];
                bars.forEach((b, i) => {
                    b.style.height = heights[i];
                    b.setAttribute('data-hours', hours[i]);
                });
                showToast('Displaying 30-day daily averages', 'indigo');
            }
        }
    </script>
</body>
</html>
