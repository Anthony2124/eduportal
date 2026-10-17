<?php
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/academics/exams.php';
$user = require_page_access($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Examinations - EduPortal</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    .exam-type-badge { font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: var(--radius-full); }
    .exam-type-badge.midterm { background: rgba(79, 70, 229, 0.12); color: var(--primary); }
    .exam-type-badge.final { background: rgba(239, 68, 68, 0.12); color: var(--danger); }
    .exam-type-badge.quiz { background: rgba(6, 182, 212, 0.12); color: var(--secondary); }
    .permit-card { border-left: 4px solid var(--success); }
    .permit-card.pending { border-left-color: var(--warning); }
    .countdown-box { background: linear-gradient(135deg, rgba(79, 70, 229, 0.08), rgba(6, 182, 212, 0.08)); border: 1px solid rgba(79, 70, 229, 0.15); border-radius: var(--radius-md); padding: 1rem; text-align: center; }
    .countdown-num { font-size: 2rem; font-weight: 800; color: var(--primary); }
    .countdown-label { font-size: 0.8rem; color: var(--gray-500); font-weight: 600; }
    .score-circle { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; flex-shrink: 0; }
    .score-circle.high { background: rgba(16, 185, 129, 0.15); color: #047857; }
    .score-circle.mid { background: rgba(6, 182, 212, 0.15); color: #0e7490; }
    .score-circle.low { background: rgba(239, 68, 68, 0.15); color: #b91c1c; }
    .two-col-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start; }
    @media (max-width: 992px) { .two-col-layout { grid-template-columns: 1fr; } }
  </style>
    <?php app_shell_styles(); ?>
</head>
<body>

  <?php app_sidebar($user, $page); ?>

  <div class="main-content app-main">
    <?php app_topbar($user, ['placeholder' => 'Search exam schedules, results, permits...']); ?>

    <div class="content-area">
      <!-- Page Header -->
      <div class="page-header">
        <div>
          <h1>Examinations</h1>
          <p class="page-subtitle">View exam schedules, permits, and results for the current semester</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
          <button class="btn btn-outline" onclick="showToast('Downloading exam schedule as PDF...', 'info')">
            <i class="fas fa-download"></i> Export Schedule
          </button>
          <button class="btn btn-primary" onclick="openModal('examPermitModal')">
            <i class="fas fa-id-badge"></i> View Exam Permit
          </button>
        </div>
      </div>

      <!-- Stats Row -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon primary"><i class="fas fa-calendar-check"></i></div>
          <div class="stat-details">
            <span class="stat-label">Upcoming Exams</span>
            <span class="stat-value">6</span>
            <span class="stat-subtext">Midterm Period</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon success"><i class="fas fa-check-double"></i></div>
          <div class="stat-details">
            <span class="stat-label">Completed Exams</span>
            <span class="stat-value" style="color: var(--success);">8</span>
            <span class="stat-subtext">Quizzes &amp; Prelims</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon secondary"><i class="fas fa-chart-line"></i></div>
          <div class="stat-details">
            <span class="stat-label">Average Score</span>
            <span class="stat-value">89.5%</span>
            <span class="stat-subtext">Across all exams</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background: #D1FAE5; color: var(--success);"><i class="fas fa-shield-alt"></i></div>
          <div class="stat-details">
            <span class="stat-label">Exam Permit</span>
            <span class="stat-value" style="font-size: 1.3rem; color: var(--success);">Cleared</span>
            <span class="stat-subtext">All obligations settled</span>
          </div>
        </div>
      </div>

      <!-- Midterm Countdown Banner -->
      <div class="banner banner-primary" style="margin-bottom: 2rem;">
        <div class="banner-info">
          <div class="banner-icon"><i class="fas fa-hourglass-half"></i></div>
          <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
              <span style="font-size: 1.1rem; font-weight: 700; color: var(--dark);">
                Midterm Examination Period: December 10 – 15, 2026
              </span>
              <span class="badge badge-warning"><i class="fas fa-clock"></i> Upcoming</span>
            </div>
            <p style="font-size: 0.875rem; color: var(--gray-600); margin-top: 0.2rem;">
              Ensure all financial obligations are cleared and exam permits are secured before the examination week.
            </p>
          </div>
        </div>
        <div style="display: flex; gap: 1rem;">
          <div class="countdown-box">
            <div class="countdown-num" id="daysLeft">58</div>
            <div class="countdown-label">Days Left</div>
          </div>
        </div>
      </div>

      <!-- Two-Column Layout -->
      <div class="two-col-layout">
        <!-- Left: Exam Schedule Table -->
        <div>
          <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
              <div>
                <h2 class="card-title"><i class="fas fa-calendar-week text-primary"></i> Midterm Exam Schedule</h2>
                <p class="card-subtitle">Official schedule released by the Office of the University Registrar</p>
              </div>
              <span class="badge badge-primary">6 Subjects</span>
            </div>
            <div class="table-responsive">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Room</th>
                    <th>Type</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><span style="font-weight: 700; color: var(--dark);">CS 201</span></td>
                    <td>
                      <div style="font-weight: 600;">Data Structures &amp; Algorithms</div>
                      <span style="font-size: 0.75rem; color: var(--gray-500);">Prof. Annie Fe Mendez</span>
                    </td>
                    <td><strong>Dec 10, 2026</strong></td>
                    <td><span class="badge badge-neutral"><i class="far fa-clock"></i> 9:00 - 11:00 AM</span></td>
                    <td>ICS-302</td>
                    <td><span class="exam-type-badge midterm">Midterm</span></td>
                    <td><span class="badge badge-warning"><i class="far fa-clock"></i> Scheduled</span></td>
                  </tr>
                  <tr>
                    <td><span style="font-weight: 700; color: var(--dark);">CS 203</span></td>
                    <td>
                      <div style="font-weight: 600;">Object-Oriented Programming</div>
                      <span style="font-size: 0.75rem; color: var(--gray-500);">Dr. Sarah Johnson</span>
                    </td>
                    <td><strong>Dec 11, 2026</strong></td>
                    <td><span class="badge badge-neutral"><i class="far fa-clock"></i> 10:30 AM - 12:30 PM</span></td>
                    <td>Lab 4</td>
                    <td><span class="exam-type-badge midterm">Midterm</span></td>
                    <td><span class="badge badge-warning"><i class="far fa-clock"></i> Scheduled</span></td>
                  </tr>
                  <tr>
                    <td><span style="font-weight: 700; color: var(--dark);">MATH 202</span></td>
                    <td>
                      <div style="font-weight: 600;">Discrete Mathematics</div>
                      <span style="font-size: 0.75rem; color: var(--gray-500);">Prof. David Miller</span>
                    </td>
                    <td><strong>Dec 12, 2026</strong></td>
                    <td><span class="badge badge-neutral"><i class="far fa-clock"></i> 1:00 - 3:00 PM</span></td>
                    <td>Rm 205</td>
                    <td><span class="exam-type-badge midterm">Midterm</span></td>
                    <td><span class="badge badge-warning"><i class="far fa-clock"></i> Scheduled</span></td>
                  </tr>
                  <tr>
                    <td><span style="font-weight: 700; color: var(--dark);">DTP 101</span></td>
                    <td>
                      <div style="font-weight: 600;">Desktop Publishing</div>
                      <span style="font-size: 0.75rem; color: var(--gray-500);">Prof. Dale Lyko Abion</span>
                    </td>
                    <td><strong>Dec 13, 2026</strong></td>
                    <td><span class="badge badge-neutral"><i class="far fa-clock"></i> 8:00 - 10:00 AM</span></td>
                    <td>Sci Hall A</td>
                    <td><span class="exam-type-badge midterm">Midterm</span></td>
                    <td><span class="badge badge-warning"><i class="far fa-clock"></i> Scheduled</span></td>
                  </tr>
                  <tr>
                    <td><span style="font-weight: 700; color: var(--dark);">NET 101</span></td>
                    <td>
                      <div style="font-weight: 600;">Networking I</div>
                      <span style="font-size: 0.75rem; color: var(--gray-500);">Prof. Christopher Marmol</span>
                    </td>
                    <td><strong>Dec 14, 2026</strong></td>
                    <td><span class="badge badge-neutral"><i class="far fa-clock"></i> 2:00 - 4:00 PM</span></td>
                    <td>Rm 310</td>
                    <td><span class="exam-type-badge midterm">Midterm</span></td>
                    <td><span class="badge badge-warning"><i class="far fa-clock"></i> Scheduled</span></td>
                  </tr>
                  <tr>
                    <td><span style="font-weight: 700; color: var(--dark);">HUM 101</span></td>
                    <td>
                      <div style="font-weight: 600;">Art Appreciation</div>
                      <span style="font-size: 0.75rem; color: var(--gray-500);">Prof. Sweetzel Abion</span>
                    </td>
                    <td><strong>Dec 15, 2026</strong></td>
                    <td><span class="badge badge-neutral"><i class="far fa-clock"></i> 9:00 AM - 12:00 PM</span></td>
                    <td>Arts Bldg</td>
                    <td><span class="exam-type-badge midterm">Midterm</span></td>
                    <td><span class="badge badge-warning"><i class="far fa-clock"></i> Scheduled</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Past Exam Results -->
          <div class="card">
            <div class="card-header">
              <div>
                <h2 class="card-title"><i class="fas fa-poll text-success"></i> Recent Exam &amp; Quiz Results</h2>
                <p class="card-subtitle">Graded assessments from quizzes and preliminary exams</p>
              </div>
              <button class="btn btn-outline btn-sm" onclick="showToast('Exporting results to PDF...', 'info')">
                <i class="fas fa-download"></i> Export
              </button>
            </div>
            <div class="table-responsive">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Assessment</th>
                    <th>Course</th>
                    <th>Date Taken</th>
                    <th>Score</th>
                    <th>Class Avg</th>
                    <th>Rank</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div style="font-weight: 600;">Quiz 3: Sorting Algorithms</div>
                      <span class="exam-type-badge quiz">Quiz</span>
                    </td>
                    <td>CS 201</td>
                    <td>Oct 9, 2026</td>
                    <td><strong style="color: var(--success);">95/100</strong></td>
                    <td>82/100</td>
                    <td><span class="badge badge-success"><i class="fas fa-medal"></i> 2nd</span></td>
                  </tr>
                  <tr>
                    <td>
                      <div style="font-weight: 600;">Prelim Exam: OOP Concepts</div>
                      <span class="exam-type-badge midterm">Prelim</span>
                    </td>
                    <td>CS 203</td>
                    <td>Oct 7, 2026</td>
                    <td><strong style="color: var(--success);">88/100</strong></td>
                    <td>78/100</td>
                    <td><span class="badge badge-primary">5th</span></td>
                  </tr>
                  <tr>
                    <td>
                      <div style="font-weight: 600;">Quiz 2: Set Theory &amp; Logic</div>
                      <span class="exam-type-badge quiz">Quiz</span>
                    </td>
                    <td>MATH 202</td>
                    <td>Oct 5, 2026</td>
                    <td><strong style="color: var(--success);">92/100</strong></td>
                    <td>76/100</td>
                    <td><span class="badge badge-success"><i class="fas fa-medal"></i> 1st</span></td>
                  </tr>
                  <tr>
                    <td>
                      <div style="font-weight: 600;">Lab Practical: Mechanics</div>
                      <span class="exam-type-badge midterm">Practical</span>
                    </td>
                    <td>DTP 101</td>
                    <td>Oct 2, 2026</td>
                    <td><strong style="color: var(--primary);">85/100</strong></td>
                    <td>80/100</td>
                    <td><span class="badge badge-neutral">8th</span></td>
                  </tr>
                  <tr>
                    <td>
                      <div style="font-weight: 600;">Essay: Technical Report Writing</div>
                      <span class="exam-type-badge quiz">Written</span>
                    </td>
                    <td>NET 101</td>
                    <td>Sep 30, 2026</td>
                    <td><strong style="color: var(--success);">90/100</strong></td>
                    <td>83/100</td>
                    <td><span class="badge badge-primary">4th</span></td>
                  </tr>
                  <tr>
                    <td>
                      <div style="font-weight: 600;">Quiz 1: Art Movements</div>
                      <span class="exam-type-badge quiz">Quiz</span>
                    </td>
                    <td>HUM 101</td>
                    <td>Sep 25, 2026</td>
                    <td><strong style="color: var(--primary);">86/100</strong></td>
                    <td>79/100</td>
                    <td><span class="badge badge-neutral">6th</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div>
          <!-- Exam Permit Card -->
          <div class="card permit-card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-id-badge text-success"></i> Exam Permit</h3>
              <span class="badge badge-success"><i class="fas fa-check-circle"></i> Cleared</span>
            </div>
            <div class="card-body">
              <div style="text-align: center; padding: 1rem 0;">
                <div style="width: 70px; height: 70px; border-radius: 50%; background: #D1FAE5; color: var(--success); display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 0.75rem;">
                  <i class="fas fa-check-circle"></i>
                </div>
                <div style="font-weight: 700; font-size: 1.1rem; color: var(--dark);">Midterm Permit Cleared</div>
                <div style="font-size: 0.825rem; color: var(--gray-500); margin-top: 0.25rem;">Valid for Dec 10 – 15, 2026</div>
              </div>

              <div style="background: var(--light); padding: 0.85rem; border-radius: var(--radius-md); margin-bottom: 1rem; font-size: 0.825rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                  <span style="color: var(--gray-600);">Student:</span>
                  <strong><span data-user="fullName"></span> (<span data-user="studentId"></span>)</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                  <span style="color: var(--gray-600);">Program:</span>
                  <strong>BSCS - 2nd Year</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                  <span style="color: var(--gray-600);">Financial Status:</span>
                  <span class="badge badge-success"><i class="fas fa-check"></i> Settled</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                  <span style="color: var(--gray-600);">Library Clearance:</span>
                  <span class="badge badge-success"><i class="fas fa-check"></i> Clear</span>
                </div>
              </div>

              <button class="btn btn-primary" style="width: 100%;" onclick="openModal('examPermitModal')">
                <i class="fas fa-print"></i> View &amp; Print Permit
              </button>
            </div>
          </div>

          <!-- Exam Guidelines Card -->
          <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-info-circle text-primary"></i> Exam Guidelines</h3>
            </div>
            <div class="card-body" style="font-size: 0.825rem;">
              <ul style="padding-left: 1.25rem; color: var(--gray-700); line-height: 1.7;">
                <li>Bring your <strong>valid school ID</strong> and <strong>exam permit</strong> to every exam.</li>
                <li>Arrive at least <strong>15 minutes</strong> before the scheduled start.</li>
                <li>No electronic devices allowed unless specified by the instructor.</li>
                <li>Students arriving <strong>30+ minutes late</strong> will not be admitted.</li>
                <li>Special exam arrangements must be filed <strong>5 days in advance</strong>.</li>
              </ul>
            </div>
          </div>

          <!-- Score Summary Card -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-chart-pie text-secondary"></i> Score Summary</h3>
            </div>
            <div class="card-body">
              <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                  <div class="score-circle high">95</div>
                  <div>
                    <div style="font-weight: 600; font-size: 0.85rem;">Highest Score</div>
                    <div style="font-size: 0.75rem; color: var(--gray-500);">CS 201 Quiz 3</div>
                  </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                  <div class="score-circle mid">89</div>
                  <div>
                    <div style="font-weight: 600; font-size: 0.85rem;">Average Score</div>
                    <div style="font-size: 0.75rem; color: var(--gray-500);">Across 8 assessments</div>
                  </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                  <div class="score-circle mid">85</div>
                  <div>
                    <div style="font-weight: 600; font-size: 0.85rem;">Lowest Score</div>
                    <div style="font-size: 0.75rem; color: var(--gray-500);">DTP 101 Lab Practical</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Exam Permit Preview -->
  <div class="modal-overlay" id="examPermitModal">
    <div class="modal-container" style="max-width: 600px;">
      <div class="modal-header">
        <h3 class="modal-title"><i class="fas fa-id-badge text-primary"></i> Midterm Examination Permit</h3>
        <button class="modal-close" data-modal-close>&times;</button>
      </div>
      <div class="modal-body">
        <div style="text-align: center; border-bottom: 2px solid var(--gray-300); padding-bottom: 1rem; margin-bottom: 1.25rem;">
          <h2 style="font-size: 1.3rem; font-weight: 800; color: var(--primary);">EDUPORTAL UNIVERSITY</h2>
          <p style="color: var(--gray-500); font-size: 0.8rem;">Office of the University Registrar • Official Examination Permit</p>
          <p style="font-weight: 700; margin-top: 0.3rem;">Midterm Examinations • 1st Semester, A.Y. 2026-2027</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem; background: var(--light); padding: 1rem; border-radius: var(--radius-md);">
          <div><strong>Student Name:</strong> <span data-user="fullName"></span></div>
          <div><strong>Student ID:</strong> <span data-user="studentId"></span></div>
          <div><strong>Program:</strong> BS Computer Science</div>
          <div><strong>Year Level:</strong> 2nd Year (Regular)</div>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.825rem; margin-bottom: 1rem;">
          <thead>
            <tr style="border-bottom: 1px solid var(--gray-300); text-align: left;">
              <th style="padding: 0.5rem 0;">Course</th>
              <th>Exam Date</th>
              <th>Time</th>
              <th>Room</th>
            </tr>
          </thead>
          <tbody>
            <tr style="border-bottom: 1px solid var(--gray-100);"><td style="padding: 0.4rem 0;">CS 201</td><td>Dec 10</td><td>9:00 - 11:00 AM</td><td>ICS-302</td></tr>
            <tr style="border-bottom: 1px solid var(--gray-100);"><td style="padding: 0.4rem 0;">CS 203</td><td>Dec 11</td><td>10:30 AM - 12:30 PM</td><td>Lab 4</td></tr>
            <tr style="border-bottom: 1px solid var(--gray-100);"><td style="padding: 0.4rem 0;">MATH 202</td><td>Dec 12</td><td>1:00 - 3:00 PM</td><td>Rm 205</td></tr>
            <tr style="border-bottom: 1px solid var(--gray-100);"><td style="padding: 0.4rem 0;">DTP 101</td><td>Dec 13</td><td>8:00 - 10:00 AM</td><td>Sci Hall A</td></tr>
            <tr style="border-bottom: 1px solid var(--gray-100);"><td style="padding: 0.4rem 0;">NET 101</td><td>Dec 14</td><td>2:00 - 4:00 PM</td><td>Rm 310</td></tr>
            <tr><td style="padding: 0.4rem 0;">HUM 101</td><td>Dec 15</td><td>9:00 AM - 12:00 PM</td><td>Arts Bldg</td></tr>
          </tbody>
        </table>

        <div style="text-align: center; font-size: 0.8rem; color: var(--gray-500); border-top: 1px dashed var(--gray-300); padding-top: 0.75rem;">
          <div style="color: var(--success); font-weight: 700; margin-bottom: 0.25rem;">
            <i class="fas fa-check-circle"></i> FINANCIAL CLEARANCE: VERIFIED
          </div>
          Permit Valid: Dec 10 – 15, 2026 • Validation Code: EXAM-2026-MID-00123
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" data-modal-close>Close</button>
        <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print Permit</button>
      </div>
    </div>
  </div>

  <script src="../../js/main.js?v=9"></script>
  <script>
    // Helper functions for modal management
    function openModal(id) {
      const modal = document.getElementById(id);
      if (modal) { modal.classList.add('active'); document.body.style.overflow = 'hidden'; }
    }
    function closeModal(id) {
      const modal = document.getElementById(id);
      if (modal) { modal.classList.remove('active'); document.body.style.overflow = ''; }
    }
  </script>
</body>
</html>
