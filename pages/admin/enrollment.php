<?php
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/admin/enrollment.php';
$user = require_page_access($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enrollment - EduPortal</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    /* Page Specific Styles for Enrollment */
    .enrollment-header-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.35rem 0.85rem;
      border-radius: var(--radius-full);
      font-size: 0.8rem;
      font-weight: 600;
    }
    .checklist-card {
      border-left: 4px solid var(--primary);
    }
    .checklist-item {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      padding: 0.75rem 0;
      border-bottom: 1px solid var(--gray-200);
    }
    .checklist-item:last-child {
      border-bottom: none;
    }
    .check-icon {
      font-size: 1.15rem;
      line-height: 1.2;
    }
    .check-icon.checked {
      color: var(--success);
    }
    .check-icon.pending {
      color: var(--gray-400);
    }
    .slot-bar {
      width: 90px;
      height: 6px;
      background: var(--gray-200);
      border-radius: var(--radius-full);
      overflow: hidden;
      display: inline-block;
      vertical-align: middle;
      margin-left: 0.5rem;
    }
    .slot-fill {
      height: 100%;
      border-radius: var(--radius-full);
    }
    .two-column-layout {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 1.5rem;
      align-items: start;
    }
    @media (max-width: 992px) {
      .two-column-layout {
        grid-template-columns: 1fr;
      }
    }
  </style>
    <?php app_shell_styles(); ?>
</head>
<body>

<?php app_sidebar($user, $page); ?>

  <!-- Main Content Area -->
  <div class="main-content app-main">
<?php app_topbar($user, ['placeholder' => 'Search courses, instructors, sections...']); ?>

    <!-- Page Content -->
    <div class="content-area">
      <!-- Page Header -->
      <div class="page-header">
        <div>
          <h1>Enrollment</h1>
          <p class="page-subtitle">Manage your course registration and academic advising</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
          <button class="btn btn-outline" onclick="openModal('corModal')">
            <i class="fas fa-print"></i> View & Print COR
          </button>
          <a href="#addDrop" class="btn btn-primary" onclick="switchTab('addDropTab')">
            <i class="fas fa-plus"></i> Add Course
          </a>
        </div>
      </div>

      <!-- Enrollment Status Banner -->
      <div class="banner banner-primary">
        <div class="banner-info">
          <div class="banner-icon">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
              <span style="font-size: 1.1rem; font-weight: 700; color: var(--dark);">
                Enrollment Period: Oct 1 - Nov 15, 2026
              </span>
              <span class="badge badge-success">
                <i class="fas fa-circle" style="font-size: 0.5rem;"></i> Open
              </span>
            </div>
            <p style="font-size: 0.875rem; color: var(--gray-600); margin-top: 0.2rem;">
              Regular enrollment window for 1st Semester, A.Y. 2026-2027. Late fees apply after Nov 15.
            </p>
          </div>
        </div>
        <div style="text-align: right;">
          <span style="font-size: 0.8rem; color: var(--gray-500); text-transform: uppercase; font-weight: 600;">Time Remaining</span>
          <div style="font-size: 1.25rem; font-weight: 800; color: var(--primary);">33 Days Left</div>
        </div>
      </div>

      <!-- Two-Column Layout: Main Tabs & Checklist -->
      <div class="two-column-layout">
        <!-- Left: Tabs and Main Tables -->
        <div class="tabs-wrapper">
          <!-- Tab Navigation -->
          <div class="tabs-nav">
            <button class="tab-btn active" data-tab="currentEnrollmentTab">
              <i class="fas fa-list-check"></i> Current Enrollment (6)
            </button>
            <button class="tab-btn" data-tab="addDropTab">
              <i class="fas fa-exchange-alt"></i> Add / Drop Courses
            </button>
            <button class="tab-btn" data-tab="enrollmentHistoryTab">
              <i class="fas fa-history"></i> Enrollment History
            </button>
          </div>

          <!-- TAB 1: Current Enrollment -->
          <div class="tab-pane active" id="currentEnrollmentTab">
            <!-- Units Summary Stat Row -->
            <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 1.25rem;">
              <div class="stat-card">
                <div class="stat-icon primary">
                  <i class="fas fa-book-open"></i>
                </div>
                <div class="stat-details">
                  <span class="stat-label">Enrolled Courses</span>
                  <span class="stat-value" id="totalCoursesCount">6</span>
                  <span class="stat-subtext">All sections confirmed</span>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-icon secondary">
                  <i class="fas fa-layer-group"></i>
                </div>
                <div class="stat-details">
                  <span class="stat-label">Units Registered</span>
                  <span class="stat-value"><span id="enrolledUnitsCount">20</span> / 24</span>
                  <span class="stat-subtext">Regular Academic Load</span>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-icon success">
                  <i class="fas fa-shield-alt"></i>
                </div>
                <div class="stat-details">
                  <span class="stat-label">Registration Status</span>
                  <span class="stat-value" style="font-size: 1.3rem; color: var(--success);">Officially Enrolled</span>
                  <span class="stat-subtext">Cleared by Registrar</span>
                </div>
              </div>
            </div>

            <!-- Current Enrolled Courses Table -->
            <div class="card">
              <div class="card-header">
                <div>
                  <h2 class="card-title"><i class="fas fa-clipboard-list text-primary"></i> Registered Subject List</h2>
                  <p class="card-subtitle">Courses enrolled for 1st Semester 2026-2027</p>
                </div>
                <span class="badge badge-primary">Load: 20.0 Units</span>
              </div>
              <div class="table-responsive">
                <table class="data-table" id="enrolledCoursesTable">
                  <thead>
                    <tr>
                      <th>Course Code</th>
                      <th>Course Title</th>
                      <th>Units</th>
                      <th>Schedule</th>
                      <th>Professor</th>
                      <th>Section</th>
                      <th>Status</th>
                      <th style="text-align: right;">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr id="row-CS201">
                      <td><span style="font-weight: 700; color: var(--dark);">CS 201</span></td>
                      <td>
                        <div style="font-weight: 600;">Data Structures & Algorithms</div>
                        <span style="font-size: 0.75rem; color: var(--gray-500);">Lecture & Lab (Room ICS-302)</span>
                      </td>
                      <td><strong>4.0</strong></td>
                      <td><span class="badge badge-neutral"><i class="far fa-clock"></i> MW 9:00 AM - 11:00 AM</span></td>
                      <td>Prof. Annie Fe Mendez</td>
                      <td><span class="badge badge-neutral">CS-2A</span></td>
                      <td><span class="badge badge-success"><i class="fas fa-check"></i> Enrolled</span></td>
                      <td style="text-align: right;">
                        <button class="btn btn-sm btn-danger-outline" onclick="confirmDropCourse('CS 201', 'Data Structures & Algorithms', 4.0)">
                          <i class="fas fa-trash-alt"></i> Drop
                        </button>
                      </td>
                    </tr>
                    <tr id="row-CS203">
                      <td><span style="font-weight: 700; color: var(--dark);">CS 203</span></td>
                      <td>
                        <div style="font-weight: 600;">Object-Oriented Programming</div>
                        <span style="font-size: 0.75rem; color: var(--gray-500);">Java & Design Patterns (Lab 4)</span>
                      </td>
                      <td><strong>3.0</strong></td>
                      <td><span class="badge badge-neutral"><i class="far fa-clock"></i> TTh 10:30 AM - 12:00 PM</span></td>
                      <td>Dr. Sarah Johnson</td>
                      <td><span class="badge badge-neutral">CS-2A</span></td>
                      <td><span class="badge badge-success"><i class="fas fa-check"></i> Enrolled</span></td>
                      <td style="text-align: right;">
                        <button class="btn btn-sm btn-danger-outline" onclick="confirmDropCourse('CS 203', 'Object-Oriented Programming', 3.0)">
                          <i class="fas fa-trash-alt"></i> Drop
                        </button>
                      </td>
                    </tr>
                    <tr id="row-MATH202">
                      <td><span style="font-weight: 700; color: var(--dark);">MATH 202</span></td>
                      <td>
                        <div style="font-weight: 600;">Discrete Mathematics</div>
                        <span style="font-size: 0.75rem; color: var(--gray-500);">Graph Theory & Combinatorics</span>
                      </td>
                      <td><strong>3.0</strong></td>
                      <td><span class="badge badge-neutral"><i class="far fa-clock"></i> MWF 1:00 PM - 2:00 PM</span></td>
                      <td>Prof. David Miller</td>
                      <td><span class="badge badge-neutral">CS-2A</span></td>
                      <td><span class="badge badge-success"><i class="fas fa-check"></i> Enrolled</span></td>
                      <td style="text-align: right;">
                        <button class="btn btn-sm btn-danger-outline" onclick="confirmDropCourse('MATH 202', 'Discrete Mathematics', 3.0)">
                          <i class="fas fa-trash-alt"></i> Drop
                        </button>
                      </td>
                    </tr>
                    <tr id="row-DTP101">
                      <td><span style="font-weight: 700; color: var(--dark);">DTP 101</span></td>
                      <td>
                        <div style="font-weight: 600;">Desktop Publishing</div>
                        <span style="font-size: 0.75rem; color: var(--gray-500);">Mechanics & Thermodynamics</span>
                      </td>
                      <td><strong>4.0</strong></td>
                      <td><span class="badge badge-neutral"><i class="far fa-clock"></i> TTh 8:00 AM - 10:00 AM</span></td>
                      <td>Prof. Dale Lyko Abion</td>
                      <td><span class="badge badge-neutral">CS-2B</span></td>
                      <td><span class="badge badge-success"><i class="fas fa-check"></i> Enrolled</span></td>
                      <td style="text-align: right;">
                        <button class="btn btn-sm btn-danger-outline" onclick="confirmDropCourse('DTP 101', 'Desktop Publishing', 4.0)">
                          <i class="fas fa-trash-alt"></i> Drop
                        </button>
                      </td>
                    </tr>
                    <tr id="row-ENG101">
                      <td><span style="font-weight: 700; color: var(--dark);">NET 101</span></td>
                      <td>
                        <div style="font-weight: 600;">Networking I</div>
                        <span style="font-size: 0.75rem; color: var(--gray-500);">Scientific Writing & Presentation</span>
                      </td>
                      <td><strong>3.0</strong></td>
                      <td><span class="badge badge-neutral"><i class="far fa-clock"></i> MWF 2:00 PM - 3:00 PM</span></td>
                      <td>Prof. Christopher Marmol</td>
                      <td><span class="badge badge-neutral">CS-2A</span></td>
                      <td><span class="badge badge-success"><i class="fas fa-check"></i> Enrolled</span></td>
                      <td style="text-align: right;">
                        <button class="btn btn-sm btn-danger-outline" onclick="confirmDropCourse('NET 101', 'Networking I', 3.0)">
                          <i class="fas fa-trash-alt"></i> Drop
                        </button>
                      </td>
                    </tr>
                    <tr id="row-HUM101">
                      <td><span style="font-weight: 700; color: var(--dark);">HUM 101</span></td>
                      <td>
                        <div style="font-weight: 600;">Art Appreciation</div>
                        <span style="font-size: 0.75rem; color: var(--gray-500);">Visual & Performing Arts</span>
                      </td>
                      <td><strong>3.0</strong></td>
                      <td><span class="badge badge-neutral"><i class="far fa-clock"></i> Fri 9:00 AM - 12:00 PM</span></td>
                      <td>Prof. Sweetzel Abion</td>
                      <td><span class="badge badge-neutral">CS-2C</span></td>
                      <td><span class="badge badge-success"><i class="fas fa-check"></i> Enrolled</span></td>
                      <td style="text-align: right;">
                        <button class="btn btn-sm btn-danger-outline" onclick="confirmDropCourse('HUM 101', 'Art Appreciation', 3.0)">
                          <i class="fas fa-trash-alt"></i> Drop
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- TAB 2: Add/Drop -->
          <div class="tab-pane" id="addDropTab">
            <div class="card">
              <div class="card-header">
                <div>
                  <h2 class="card-title"><i class="fas fa-search-plus text-primary"></i> Course Catalog & Available Slots</h2>
                  <p class="card-subtitle">Search open course sections to add to your study load</p>
                </div>
              </div>
              <div class="card-body">
                <!-- Search & Filters -->
                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                  <div style="flex: 1; min-width: 240px; position: relative;">
                    <input type="text" id="courseSearchInput" class="form-control" placeholder="Search by course code, title, or instructor..." onkeyup="filterAvailableCourses()">
                  </div>
                  <div style="width: 180px;">
                    <select class="form-select" id="departmentFilter" onchange="filterAvailableCourses()">
                      <option value="">All Departments</option>
                      <option value="CS">Computer Science</option>
                      <option value="MATH">Mathematics</option>
                      <option value="PE">Physical Education</option>
                      <option value="GE">General Education</option>
                    </select>
                  </div>
                  <button class="btn btn-primary" onclick="filterAvailableCourses()">
                    <i class="fas fa-search"></i> Search
                  </button>
                </div>

                <!-- Available Courses Table -->
                <div class="table-responsive">
                  <table class="data-table" id="availableCoursesTable">
                    <thead>
                      <tr>
                        <th>Code</th>
                        <th>Title</th>
                        <th>Units</th>
                        <th>Schedule</th>
                        <th>Slots Available</th>
                        <th>Professor</th>
                        <th style="text-align: right;">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr data-code="PE 101" data-dept="PE">
                        <td><strong>PE 101</strong></td>
                        <td>
                          <div style="font-weight: 600;">Physical Education</div>
                          <span style="font-size: 0.75rem; color: var(--gray-500);">Gymnasium / Fitness Center</span>
                        </td>
                        <td>2.0</td>
                        <td><span class="badge badge-neutral"><i class="far fa-clock"></i> MWF 7:00 AM - 8:00 AM</span></td>
                        <td>
                          <span><strong>28</strong>/40</span>
                          <span class="slot-bar"><span class="slot-fill" style="width: 70%; background-color: var(--success); display: block;"></span></span>
                        </td>
                        <td>Coach Marcus Rivera</td>
                        <td style="text-align: right;">
                          <button class="btn btn-sm btn-primary" onclick="addCourse('PE 101', 'Physical Education', 2.0, 'MWF 7:00 AM - 8:00 AM', 'Coach Marcus Rivera', 'PE-1A')">
                            <i class="fas fa-plus"></i> Add
                          </button>
                        </td>
                      </tr>
                      <tr data-code="CS 204" data-dept="CS">
                        <td><strong>CS 204</strong></td>
                        <td>
                          <div style="font-weight: 600;">Database Systems</div>
                          <span style="font-size: 0.75rem; color: var(--gray-500);">Relational DBMS & SQL Lab</span>
                        </td>
                        <td>3.0</td>
                        <td><span class="badge badge-neutral"><i class="far fa-clock"></i> TTh 1:00 PM - 2:30 PM</span></td>
                        <td>
                          <span><strong>35</strong>/40</span>
                          <span class="slot-bar"><span class="slot-fill" style="width: 87.5%; background-color: var(--warning); display: block;"></span></span>
                        </td>
                        <td>Dr. Cynthia Gomez</td>
                        <td style="text-align: right;">
                          <button class="btn btn-sm btn-primary" onclick="addCourse('CS 204', 'Database Systems', 3.0, 'TTh 1:00 PM - 2:30 PM', 'Dr. Cynthia Gomez', 'CS-2B')">
                            <i class="fas fa-plus"></i> Add
                          </button>
                        </td>
                      </tr>
                      <tr data-code="MATH 203" data-dept="MATH">
                        <td><strong>MATH 203</strong></td>
                        <td>
                          <div style="font-weight: 600;">Linear Algebra</div>
                          <span style="font-size: 0.75rem; color: var(--gray-500);">Matrices, Vectors & Transformations</span>
                        </td>
                        <td>3.0</td>
                        <td><span class="badge badge-neutral"><i class="far fa-clock"></i> MWF 3:00 PM - 4:00 PM</span></td>
                        <td>
                          <span><strong>38</strong>/40</span>
                          <span class="slot-bar"><span class="slot-fill" style="width: 95%; background-color: var(--danger); display: block;"></span></span>
                        </td>
                        <td>Prof. Benjamin Cruz</td>
                        <td style="text-align: right;">
                          <button class="btn btn-sm btn-primary" onclick="addCourse('MATH 203', 'Linear Algebra', 3.0, 'MWF 3:00 PM - 4:00 PM', 'Prof. Benjamin Cruz', 'MATH-2C')">
                            <i class="fas fa-plus"></i> Add
                          </button>
                        </td>
                      </tr>
                      <tr data-code="CS 205" data-dept="CS">
                        <td><strong>CS 205</strong></td>
                        <td>
                          <div style="font-weight: 600;">Web Development Technologies</div>
                          <span style="font-size: 0.75rem; color: var(--gray-500);">Frontend & Backend Frameworks</span>
                        </td>
                        <td>3.0</td>
                        <td><span class="badge badge-neutral"><i class="far fa-clock"></i> TTh 4:00 PM - 5:30 PM</span></td>
                        <td>
                          <span><strong>25</strong>/35</span>
                          <span class="slot-bar"><span class="slot-fill" style="width: 71%; background-color: var(--success); display: block;"></span></span>
                        </td>
                        <td>Prof. Glenn Kennedy Marzo</td>
                        <td style="text-align: right;">
                          <button class="btn btn-sm btn-primary" onclick="addCourse('CS 205', 'Web Development Technologies', 3.0, 'TTh 4:00 PM - 5:30 PM', 'Prof. Glenn Kennedy Marzo', 'CS-2C')">
                            <i class="fas fa-plus"></i> Add
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- TAB 3: Enrollment History -->
          <div class="tab-pane" id="enrollmentHistoryTab">
            <div class="card">
              <div class="card-header">
                <div>
                  <h2 class="card-title"><i class="fas fa-history text-primary"></i> Previous Semesters</h2>
                  <p class="card-subtitle">Historical registration records and units credited</p>
                </div>
              </div>
              <div class="table-responsive">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th>Academic Year & Term</th>
                      <th>Year Level</th>
                      <th>Total Units</th>
                      <th>Subjects</th>
                      <th>Term GPA</th>
                      <th>Registration Status</th>
                      <th style="text-align: right;">Documents</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><strong>A.Y. 2025-2026, 2nd Sem</strong></td>
                      <td>1st Year</td>
                      <td>21.0 Units</td>
                      <td>7 Courses</td>
                      <td><span class="badge badge-success">1.38 (GPA 3.62)</span></td>
                      <td><span class="badge badge-neutral">Completed</span></td>
                      <td style="text-align: right;">
                        <button class="btn btn-sm btn-outline" onclick="showToast('Loading Official Registration Record for 2025-2026 2nd Sem', 'info')">
                          <i class="fas fa-file-pdf"></i> Download COR
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td><strong>A.Y. 2025-2026, 1st Sem</strong></td>
                      <td>1st Year</td>
                      <td>20.0 Units</td>
                      <td>6 Courses</td>
                      <td><span class="badge badge-success">1.45 (GPA 3.55)</span></td>
                      <td><span class="badge badge-neutral">Completed</span></td>
                      <td style="text-align: right;">
                        <button class="btn btn-sm btn-outline" onclick="showToast('Loading Official Registration Record for 2025-2026 1st Sem', 'info')">
                          <i class="fas fa-file-pdf"></i> Download COR
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Enrollment Checklist Card -->
        <div>
          <div class="card checklist-card">
            <div class="card-header">
              <div>
                <h3 class="card-title"><i class="fas fa-tasks text-primary"></i> Enrollment Checklist</h3>
                <p class="card-subtitle">Requirements for regular status</p>
              </div>
              <span class="badge badge-success">3 of 4</span>
            </div>
            <div class="card-body">
              <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: 75%; background-color: var(--success);"></div>
              </div>
              <div style="font-size: 0.8rem; color: var(--gray-500); margin-bottom: 1rem; text-align: right;">
                75% Completed
              </div>

              <div class="checklist-item">
                <span class="check-icon checked"><i class="fas fa-check-circle"></i></span>
                <div>
                  <div style="font-weight: 600; font-size: 0.875rem; color: var(--dark);">Cleared financial obligations</div>
                  <div style="font-size: 0.775rem; color: var(--gray-500);">Tuition down payment verified on Oct 1, 2026</div>
                </div>
              </div>

              <div class="checklist-item">
                <span class="check-icon checked"><i class="fas fa-check-circle"></i></span>
                <div>
                  <div style="font-weight: 600; font-size: 0.875rem; color: var(--dark);">Academic advising completed</div>
                  <div style="font-size: 0.775rem; color: var(--gray-500);">Advised by Dr. Alan Turing (Approved Oct 2)</div>
                </div>
              </div>

              <div class="checklist-item">
                <span class="check-icon checked"><i class="fas fa-check-circle"></i></span>
                <div>
                  <div style="font-weight: 600; font-size: 0.875rem; color: var(--dark);">Prerequisites verified</div>
                  <div style="font-size: 0.775rem; color: var(--gray-500);">All foundation prerequisites passed</div>
                </div>
              </div>

              <div class="checklist-item">
                <span class="check-icon pending"><i class="far fa-circle"></i></span>
                <div>
                  <div style="font-weight: 600; font-size: 0.875rem; color: var(--gray-600);">Dean's approval</div>
                  <div style="font-size: 0.775rem; color: var(--gray-400);">Required only if enrolling overload (> 21 units)</div>
                </div>
              </div>

              <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--gray-200);">
                <div style="font-size: 0.8rem; color: var(--gray-600); margin-bottom: 0.75rem;">
                  <i class="fas fa-info-circle text-primary"></i> <strong>Adviser Notes:</strong> Student eligible for regular 20-unit load.
                </div>
                <button class="btn btn-outline" style="width: 100%; font-size: 0.825rem;" onclick="openModal('overloadModal')">
                  <i class="fas fa-file-signature"></i> Request Overload Permit
                </button>
              </div>
            </div>
          </div>

          <!-- Academic Calendar Dates -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-calendar-alt text-secondary"></i> Key Deadlines</h3>
            </div>
            <div class="card-body" style="font-size: 0.85rem;">
              <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="color: var(--gray-600);">Last Day of Adding/Dropping:</span>
                <strong>Nov 15, 2026</strong>
              </div>
              <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="color: var(--gray-600);">Midterm Examinations:</span>
                <strong>Dec 10 - 15, 2026</strong>
              </div>
              <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--gray-600);">Last Day of Withdrawal:</span>
                <strong>Jan 15, 2027</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Certificate of Registration (COR) Preview -->
  <div class="modal-overlay" id="corModal">
    <div class="modal-container" style="max-width: 720px;">
      <div class="modal-header">
        <h3 class="modal-title"><i class="fas fa-file-invoice text-primary"></i> Certificate of Registration (COR)</h3>
        <button class="modal-close" data-modal-close>&times;</button>
      </div>
      <div class="modal-body" id="corPrintArea" style="font-size: 0.875rem;">
        <div style="text-align: center; border-bottom: 2px solid var(--gray-300); padding-bottom: 1rem; margin-bottom: 1.25rem;">
          <h2 style="font-size: 1.3rem; font-weight: 800; color: var(--primary);">EDUPORTAL UNIVERSITY</h2>
          <p style="color: var(--gray-500); font-size: 0.8rem;">Office of the University Registrar • Official Registration Form</p>
          <p style="font-weight: 700; margin-top: 0.3rem;">1st Semester, Academic Year 2026-2027</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem; background: var(--light); padding: 1rem; border-radius: var(--radius-md);">
          <div><strong>Student Name:</strong> <span data-user="fullName"></span></div>
          <div><strong>Student ID:</strong> <span data-user="studentId"></span></div>
          <div><strong>Degree Program:</strong> BS Computer Science</div>
          <div><strong>Year Level:</strong> 2nd Year (Regular)</div>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.825rem; margin-bottom: 1.25rem;">
          <thead>
            <tr style="border-bottom: 1px solid var(--gray-300); text-align: left;">
              <th style="padding: 0.5rem 0;">Course</th>
              <th>Description</th>
              <th>Units</th>
              <th>Schedule</th>
              <th>Section</th>
            </tr>
          </thead>
          <tbody>
            <tr style="border-bottom: 1px solid var(--gray-100);">
              <td style="padding: 0.4rem 0;">CS 201</td>
              <td>Data Structures & Algorithms</td>
              <td>4.0</td>
              <td>MW 9:00 - 11:00 AM</td>
              <td>CS-2A</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--gray-100);">
              <td style="padding: 0.4rem 0;">CS 203</td>
              <td>Object-Oriented Programming</td>
              <td>3.0</td>
              <td>TTh 10:30 - 12:00 PM</td>
              <td>CS-2A</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--gray-100);">
              <td style="padding: 0.4rem 0;">MATH 202</td>
              <td>Discrete Mathematics</td>
              <td>3.0</td>
              <td>MWF 1:00 - 2:00 PM</td>
              <td>CS-2A</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--gray-100);">
              <td style="padding: 0.4rem 0;">DTP 101</td>
              <td>Desktop Publishing</td>
              <td>4.0</td>
              <td>TTh 8:00 - 10:00 AM</td>
              <td>CS-2B</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--gray-100);">
              <td style="padding: 0.4rem 0;">NET 101</td>
              <td>Networking I</td>
              <td>3.0</td>
              <td>MWF 2:00 - 3:00 PM</td>
              <td>CS-2A</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--gray-100);">
              <td style="padding: 0.4rem 0;">HUM 101</td>
              <td>Art Appreciation</td>
              <td>3.0</td>
              <td>Fri 9:00 - 12:00 PM</td>
              <td>CS-2C</td>
            </tr>
          </tbody>
          <tfoot>
            <tr style="border-top: 2px solid var(--gray-300); font-weight: 700;">
              <td colspan="2" style="padding-top: 0.5rem;">TOTAL ENROLLED LOAD</td>
              <td style="padding-top: 0.5rem;">20.0 Units</td>
              <td colspan="2"></td>
            </tr>
          </tfoot>
        </table>

        <div style="font-size: 0.75rem; color: var(--gray-500); border-top: 1px dashed var(--gray-300); padding-top: 0.75rem;">
          <i class="fas fa-certificate text-success"></i> Digitally Signed & Authenticated by EduPortal Student Information System. Registrar Ref: COR-2026-88491.
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" data-modal-close>Close</button>
        <button class="btn btn-primary" onclick="window.print()">
          <i class="fas fa-print"></i> Print Document
        </button>
      </div>
    </div>
  </div>

  <!-- Modal: Confirm Drop Course -->
  <div class="modal-overlay" id="dropCourseModal">
    <div class="modal-container" style="max-width: 480px;">
      <div class="modal-header">
        <h3 class="modal-title" style="color: var(--danger);"><i class="fas fa-exclamation-triangle"></i> Confirm Drop Course</h3>
        <button class="modal-close" data-modal-close>&times;</button>
      </div>
      <div class="modal-body">
        <p style="margin-bottom: 1rem; color: var(--gray-700);">
          Are you sure you want to drop <strong id="dropCourseCode">CS 201</strong> (<span id="dropCourseTitle">Data Structures</span>)?
        </p>
        <div class="alert alert-warning" style="margin-bottom: 1rem;">
          <i class="fas fa-info-circle"></i>
          <div>Dropping will deduct <strong id="dropCourseUnits">4.0</strong> units from your current semester load. No fee penalty before Nov 15.</div>
        </div>
        <div class="form-group">
          <label class="form-label">Reason for dropping:</label>
          <select class="form-select" id="dropReasonSelect">
            <option value="Schedule conflict">Schedule conflict</option>
            <option value="Academic load adjustment">Academic load adjustment</option>
            <option value="Instructor preference">Instructor preference</option>
            <option value="Personal reasons">Personal reasons</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" data-modal-close>Cancel</button>
        <button class="btn btn-danger-outline" style="background: var(--danger); color: white;" onclick="executeDropCourse()">
          <i class="fas fa-trash-alt"></i> Confirm Drop
        </button>
      </div>
    </div>
  </div>

  <!-- Modal: Overload Request -->
  <div class="modal-overlay" id="overloadModal">
    <div class="modal-container" style="max-width: 520px;">
      <div class="modal-header">
        <h3 class="modal-title"><i class="fas fa-file-signature text-primary"></i> Request Unit Overload Permit</h3>
        <button class="modal-close" data-modal-close>&times;</button>
      </div>
      <div class="modal-body">
        <p style="font-size: 0.85rem; color: var(--gray-600); margin-bottom: 1rem;">
          Dean's approval is required to exceed the 21-unit maximum load limit up to 24 units.
        </p>
        <div class="form-group">
          <label class="form-label">Current Academic Load</label>
          <input type="text" class="form-control" value="20.0 Units" readonly>
        </div>
        <div class="form-group">
          <label class="form-label">Requested Maximum Load</label>
          <select class="form-select">
            <option value="23">23.0 Units (+3 units)</option>
            <option value="24">24.0 Units (+4 units)</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Justification / Reason for Overload</label>
          <textarea class="form-control" rows="3" placeholder="Provide reason (e.g. Graduating status, prerequisite sequencing)..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" data-modal-close>Cancel</button>
        <button class="btn btn-primary" onclick="closeModal('overloadModal'); showToast('Overload request submitted to the College Dean for review.', 'success');">
          Submit Permit Request
        </button>
      </div>
    </div>
  </div>

  <script src="../../js/main.js?v=9"></script>
  <script>
    // Tab switching helper
    function switchTab(tabId) {
      const btn = document.querySelector(`[data-tab="${tabId}"]`);
      if (btn) btn.click();
    }

    // Filter available courses in catalog
    function filterAvailableCourses() {
      const query = document.getElementById('courseSearchInput').value.toLowerCase();
      const dept = document.getElementById('departmentFilter').value;
      const rows = document.querySelectorAll('#availableCoursesTable tbody tr');

      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const rowDept = row.getAttribute('data-dept') || '';
        const matchesQuery = text.includes(query);
        const matchesDept = !dept || rowDept === dept;

        if (matchesQuery && matchesDept) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    let currentDropTarget = null;

    function confirmDropCourse(code, title, units) {
      currentDropTarget = { code, title, units };
      document.getElementById('dropCourseCode').innerText = code;
      document.getElementById('dropCourseTitle').innerText = title;
      document.getElementById('dropCourseUnits').innerText = units.toFixed(1);
      openModal('dropCourseModal');
    }

    function executeDropCourse() {
      if (!currentDropTarget) return;
      const rowId = 'row-' + currentDropTarget.code.replace(/\s+/g, '');
      const row = document.getElementById(rowId);
      if (row) {
        row.remove();
      }
      
      // Update counters
      const enrolledUnitsEl = document.getElementById('enrolledUnitsCount');
      const coursesCountEl = document.getElementById('totalCoursesCount');
      if (enrolledUnitsEl && coursesCountEl) {
        const curUnits = parseFloat(enrolledUnitsEl.innerText) || 20;
        const newUnits = Math.max(0, curUnits - currentDropTarget.units);
        enrolledUnitsEl.innerText = newUnits.toFixed(0);
        
        const curCount = parseInt(coursesCountEl.innerText) || 6;
        coursesCountEl.innerText = Math.max(0, curCount - 1);
      }

      closeModal('dropCourseModal');
      showToast(`Successfully dropped ${currentDropTarget.code}. Your schedule and assessment have been updated.`, 'warning');
      currentDropTarget = null;
    }

    function addCourse(code, title, units, schedule, professor, section) {
      // Check if already in enrolled list
      const rowId = 'row-' + code.replace(/\s+/g, '');
      if (document.getElementById(rowId)) {
        showToast(`${code} is already in your enrolled courses!`, 'warning');
        return;
      }

      // Check max units
      const enrolledUnitsEl = document.getElementById('enrolledUnitsCount');
      const curUnits = parseFloat(enrolledUnitsEl.innerText) || 20;
      if (curUnits + units > 24) {
        showToast(`Cannot add ${code}. Maximum limit is 24 units. Request Dean's overload first.`, 'danger');
        return;
      }

      // Add to table
      const tbody = document.querySelector('#enrolledCoursesTable tbody');
      const tr = document.createElement('tr');
      tr.id = rowId;
      tr.innerHTML = `
        <td><span style="font-weight: 700; color: var(--dark);">${code}</span></td>
        <td>
          <div style="font-weight: 600;">${title}</div>
          <span style="font-size: 0.75rem; color: var(--gray-500);">Newly Enrolled</span>
        </td>
        <td><strong>${units.toFixed(1)}</strong></td>
        <td><span class="badge badge-neutral"><i class="far fa-clock"></i> ${schedule}</span></td>
        <td>${professor}</td>
        <td><span class="badge badge-neutral">${section}</span></td>
        <td><span class="badge badge-success"><i class="fas fa-check"></i> Enrolled</span></td>
        <td style="text-align: right;">
          <button class="btn btn-sm btn-danger-outline" onclick="confirmDropCourse('${code}', '${title}', ${units})">
            <i class="fas fa-trash-alt"></i> Drop
          </button>
        </td>
      `;
      tbody.appendChild(tr);

      // Update counters
      const newUnits = curUnits + units;
      enrolledUnitsEl.innerText = newUnits.toFixed(0);
      const coursesCountEl = document.getElementById('totalCoursesCount');
      coursesCountEl.innerText = (parseInt(coursesCountEl.innerText) || 6) + 1;

      showToast(`Successfully added ${code} (${title}) to your schedule!`, 'success');
      switchTab('currentEnrollmentTab');
    }
  </script>
</body>
</html>
