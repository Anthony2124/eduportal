<?php
require __DIR__ . '/../../includes/app_shell.php';
$page = 'pages/admin/scholarships.php';
$user = require_page_access($page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Scholarships & Financial Aid - EduPortal</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    /* Page Specific Styles for Scholarships */
    .scholarship-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2.5rem;
    }
    .scholarship-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      border: 1px solid var(--gray-200);
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: var(--transition);
      position: relative;
    }
    .scholarship-card:hover {
      box-shadow: var(--shadow-md);
      transform: translateY(-2px);
    }
    .scholarship-card.eligible {
      border-top: 4px solid var(--success);
    }
    .scholarship-card.locked {
      border-top: 4px solid var(--gray-400);
      opacity: 0.9;
    }
    .award-tag {
      font-size: 1.25rem;
      font-weight: 800;
      color: var(--primary);
      margin: 0.5rem 0;
    }
    .req-list {
      list-style: none;
      padding: 0;
      margin: 0.75rem 0 1.25rem 0;
      font-size: 0.825rem;
      color: var(--gray-600);
    }
    .req-list li {
      margin-bottom: 0.35rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
  </style>
    <?php app_shell_styles(); ?>
</head>
<body>

<?php app_sidebar($user, $page); ?>

  <!-- Main Content Area -->
  <div class="main-content app-main">
<?php app_topbar($user, ['placeholder' => 'Search grants, sponsors, qualifications...']); ?>

    <!-- Page Content -->
    <div class="content-area">
      <!-- Page Header -->
      <div class="page-header">
        <div>
          <h1>Scholarships & Financial Aid</h1>
          <p class="page-subtitle">Explore institutional grants, private endowments, and manage renewals</p>
        </div>
        <div>
          <button class="btn btn-primary" onclick="openGeneralApplyModal()">
            <i class="fas fa-hand-holding-usd"></i> New Application
          </button>
        </div>
      </div>

      <!-- MY SCHOLARSHIPS BANNER -->
      <div class="banner banner-primary" style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.12) 0%, rgba(16, 185, 129, 0.12) 100%); border-left: 6px solid var(--primary); margin-bottom: 2rem;">
        <div class="banner-info" style="align-items: flex-start;">
          <div class="banner-icon" style="background-color: var(--white); color: var(--primary); font-size: 1.5rem;">
            <i class="fas fa-medal"></i>
          </div>
          <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
              <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--dark);">
                Current Grant: Academic Excellence Scholarship (50% Tuition Discount)
              </h2>
              <span class="badge badge-success">
                <i class="fas fa-check-circle"></i> Active
              </span>
            </div>
            <p style="font-size: 0.875rem; color: var(--gray-700); margin-top: 0.35rem;">
              Beneficiary: <strong data-user="fullName"></strong> • Value: <strong>₱15,000.00 saved</strong> per semester • Term: 1st Sem 2026-2027
            </p>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem; font-size: 0.825rem; color: #92400E; background: #FEF3C7; padding: 0.4rem 0.75rem; border-radius: var(--radius-sm); width: fit-content;">
              <i class="fas fa-bell"></i>
              <span><strong>Action Required:</strong> Submit renewal application by <strong>Dec 2026</strong> to maintain 50% discount for 2nd Semester. Maintain GPA 3.25+.</span>
            </div>
          </div>
        </div>
        <div>
          <button class="btn btn-primary" onclick="openApplyModal('Academic Excellence Scholarship (Renewal)', '50% Tuition Discount')">
            <i class="fas fa-sync-alt"></i> Renew by Dec 2026
          </button>
        </div>
      </div>

      <!-- SECTION: Available Scholarships Grid -->
      <div style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.25rem;">
          <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--dark);">
              <i class="fas fa-graduation-cap text-primary"></i> Available Scholarships
            </h2>
            <p style="font-size: 0.85rem; color: var(--gray-500);">Open financial grants for Academic Year 2026-2027</p>
          </div>
          <span style="font-size: 0.8rem; color: var(--gray-500);"><i class="fas fa-check-circle text-success"></i> Evaluated against current GPA: <strong>3.78</strong></span>
        </div>

        <div class="scholarship-grid">
          <!-- 1. Dean's Lister Scholarship -->
          <div class="scholarship-card eligible">
            <div>
              <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark);">Dean's Lister Scholarship</h3>
                <span class="badge badge-success"><i class="fas fa-check"></i> Eligible ✓</span>
              </div>
              <div class="award-tag">100% Tuition + ₱5,000 Stipend</div>
              <p style="font-size: 0.8rem; color: var(--gray-500);">Funded by University Chancellor Endowment</p>

              <ul class="req-list">
                <li><i class="fas fa-check-circle text-success"></i> <strong>Requirement:</strong> GPA 3.5+ (Your GPA: 3.78)</li>
                <li><i class="fas fa-check-circle text-success"></i> No grade lower than 2.00 in any course</li>
                <li><i class="fas fa-calendar-alt text-primary"></i> <strong>Deadline:</strong> Nov 30, 2026</li>
              </ul>
            </div>
            <div>
              <button class="btn btn-primary" style="width: 100%;" onclick="openApplyModal('Dean\'s Lister Scholarship', '100% Tuition + ₱5,000 monthly stipend')">
                <i class="fas fa-paper-plane"></i> Apply
              </button>
            </div>
          </div>

          <!-- 2. STEM Excellence Grant -->
          <div class="scholarship-card eligible">
            <div>
              <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark);">STEM Excellence Grant</h3>
                <span class="badge badge-success"><i class="fas fa-check"></i> Eligible ✓</span>
              </div>
              <div class="award-tag">₱25,000 / Semester</div>
              <p style="font-size: 0.8rem; color: var(--gray-500);">National Science & Technology Foundation</p>

              <ul class="req-list">
                <li><i class="fas fa-check-circle text-success"></i> <strong>Requirement:</strong> STEM Major (BSCS Enrolled)</li>
                <li><i class="fas fa-check-circle text-success"></i> GPA 3.0+ (Your GPA: 3.78)</li>
                <li><i class="fas fa-calendar-alt text-primary"></i> <strong>Deadline:</strong> Dec 15, 2026</li>
              </ul>
            </div>
            <div>
              <button class="btn btn-primary" style="width: 100%;" onclick="openApplyModal('STEM Excellence Grant', '₱25,000 per semester')">
                <i class="fas fa-paper-plane"></i> Apply
              </button>
            </div>
          </div>

          <!-- 3. Community Service Award -->
          <div class="scholarship-card locked">
            <div>
              <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark);">Community Service Award</h3>
                <span class="badge badge-neutral" style="color: var(--gray-600);"><i class="fas fa-clock"></i> Not Yet Eligible</span>
              </div>
              <div class="award-tag" style="color: var(--gray-700);">25% Tuition Discount</div>
              <p style="font-size: 0.8rem; color: var(--gray-500);">Center for Civic Engagement & Outreach</p>

              <ul class="req-list">
                <li><i class="fas fa-times-circle text-danger"></i> <strong>Requirement:</strong> 100+ volunteer hours (Current: 45 hrs)</li>
                <li><i class="fas fa-check-circle text-success"></i> Endorsement from accredited student organization</li>
                <li><i class="fas fa-calendar-alt text-primary"></i> <strong>Deadline:</strong> Jan 15, 2027</li>
              </ul>
            </div>
            <div>
              <button class="btn btn-outline" style="width: 100%;" onclick="showToast('You currently have 45 / 100 volunteer service hours. Log more hours at Student Life to qualify.', 'warning')">
                <i class="fas fa-info-circle"></i> View Requirements
              </button>
            </div>
          </div>

          <!-- 4. Athletic Scholarship -->
          <div class="scholarship-card locked">
            <div>
              <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark);">Athletic Scholarship</h3>
                <span class="badge badge-neutral" style="color: var(--gray-500);"><i class="fas fa-ban"></i> Not Applicable</span>
              </div>
              <div class="award-tag" style="color: var(--gray-700);">Full Scholarship + Allowance</div>
              <p style="font-size: 0.8rem; color: var(--gray-500);">Varsity Athletics Council</p>

              <ul class="req-list">
                <li><i class="fas fa-times-circle text-danger"></i> <strong>Requirement:</strong> Official University Varsity Member</li>
                <li><i class="fas fa-info-circle text-primary"></i> Head Coach Recommendation</li>
                <li><i class="fas fa-calendar-alt text-primary"></i> <strong>Deadline:</strong> Nov 1, 2026</li>
              </ul>
            </div>
            <div>
              <button class="btn btn-outline" style="width: 100%;" onclick="showToast('Varsity athletic tryouts are managed by the Physical Education Department.', 'info')">
                <i class="fas fa-external-link-alt"></i> Inquire with Athletics
              </button>
            </div>
          </div>

          <!-- 5. Need-Based Financial Aid -->
          <div class="scholarship-card">
            <div>
              <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark);">Need-Based Financial Aid</h3>
                <span class="badge badge-warning"><i class="fas fa-search"></i> Apply to Check</span>
              </div>
              <div class="award-tag" style="color: var(--accent);">Up to 75% Tuition Discount</div>
              <p style="font-size: 0.8rem; color: var(--gray-500);">Socialized Tuition & Social Amelioration</p>

              <ul class="req-list">
                <li><i class="fas fa-file-invoice-dollar text-primary"></i> <strong>Requirement:</strong> Annual family gross income bracket</li>
                <li><i class="fas fa-check-circle text-success"></i> Submission of Parents' ITR or Indigence Form</li>
                <li><i class="fas fa-calendar-alt text-primary"></i> <strong>Deadline:</strong> Rolling Application</li>
              </ul>
            </div>
            <div>
              <button class="btn btn-primary" style="width: 100%;" onclick="openApplyModal('Need-Based Financial Aid', 'Up to 75% Tuition Discount')">
                <i class="fas fa-file-signature"></i> Apply to Check
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION: Application Status Table -->
      <div class="card" id="statusSection">
        <div class="card-header">
          <div>
            <h2 class="card-title"><i class="fas fa-file-contract text-primary"></i> Submitted Scholarship Applications</h2>
            <p class="card-subtitle">Real-time tracking of filed grant petitions and renewal clearances</p>
          </div>
          <span class="badge badge-primary">Active Submissions</span>
        </div>
        <div class="table-responsive">
          <table class="data-table" id="scholarshipStatusTable">
            <thead>
              <tr>
                <th>Scholarship Name</th>
                <th>Date Applied</th>
                <th>Application Status</th>
                <th>Committee Decision</th>
                <th style="text-align: right;">Action</th>
              </tr>
            </thead>
            <tbody>
              <!-- Row 1: Academic Excellence Renewal -->
              <tr id="row-app-renewal">
                <td>
                  <div style="font-weight: 700; color: var(--dark);">Academic Excellence (Renewal)</div>
                  <span style="font-size: 0.75rem; color: var(--gray-500);">Ref: SCH-2026-081 • 50% Tuition Coverage</span>
                </td>
                <td>Sep 1, 2026</td>
                <td>
                  <span class="badge badge-warning">
                    <i class="fas fa-spinner fa-spin"></i> Under Review
                  </span>
                </td>
                <td>
                  <span class="badge badge-neutral" style="font-weight: 600; color: #B45309;">
                    <i class="far fa-hourglass"></i> Pending
                  </span>
                </td>
                <td style="text-align: right;">
                  <button class="btn btn-sm btn-outline" onclick="showToast('Scholarship Committee is verifying final grade sheet with Registrar.', 'info')">
                    <i class="fas fa-eye"></i> View Details
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Scholarship Application Form -->
  <div class="modal-overlay" id="scholarshipApplyModal">
    <div class="modal-container" style="max-width: 660px;">
      <div class="modal-header">
        <h3 class="modal-title"><i class="fas fa-file-signature text-primary"></i> Scholarship Application Form</h3>
        <button class="modal-close" data-modal-close>&times;</button>
      </div>
      <div class="modal-body">
        <!-- Pre-populated Applicant Data -->
        <div style="background: var(--light); padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; border: 1px solid var(--gray-200); display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.825rem;">
          <div><strong>Applicant:</strong> <span data-user="fullName"></span> (<span data-user="studentId"></span>)</div>
          <div><strong>Program:</strong> BS Computer Science (2nd Year)</div>
          <div><strong>Cumulative GPA:</strong> 3.78 (Class Rank: Top 5%)</div>
          <div><strong>Academic Status:</strong> Regular / In Good Standing</div>
        </div>

        <!-- Scholarship Name Field -->
        <div class="form-group">
          <label class="form-label">Scholarship Program</label>
          <input type="text" class="form-control" id="modalScholarshipName" value="Dean's Lister Scholarship" readonly style="font-weight: 700; color: var(--primary);">
        </div>

        <!-- Personal Statement / Essay -->
        <div class="form-group">
          <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
            <label class="form-label" style="margin-bottom: 0;">Personal Statement / Statement of Purpose</label>
            <span style="font-size: 0.75rem; color: var(--gray-500);" id="wordCountLabel">124 / 500 words</span>
          </div>
          <textarea class="form-control" rows="4" id="essayTextArea" oninput="updateWordCount()" placeholder="Describe your academic goals, financial need, and how this scholarship will assist your education...">I am dedicated to excelling in Computer Science and contributing to research in algorithmic optimization. Maintaining a 3.78 GPA reflects my commitment to academic rigor while balancing extracurricular leadership. Receiving this scholarship will enable me to continue my studies unhindered by financial constraints and focus on our university capstone development initiative.</textarea>
        </div>

        <!-- Financial Info Section -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <label class="form-label">Annual Household Gross Income</label>
            <select class="form-select" id="incomeSelect">
              <option value="below300k">Below ₱300,000</option>
              <option value="300k-500k" selected>₱300,000 - ₱500,000</option>
              <option value="500k-800k">₱500,000 - ₱800,000</option>
              <option value="above800k">Above ₱800,000</option>
            </select>
          </div>
          <div>
            <label class="form-label">Number of Family Dependents</label>
            <select class="form-select">
              <option>1 - 2 Dependents</option>
              <option selected>3 - 4 Dependents</option>
              <option>5 or more Dependents</option>
            </select>
          </div>
        </div>

        <!-- Upload Supporting Documents -->
        <div class="form-group">
          <label class="form-label">Upload Supporting Documents</label>
          <div style="border: 2px dashed var(--gray-300); border-radius: var(--radius-md); padding: 1.25rem; text-align: center; background: var(--light); cursor: pointer;" onclick="document.getElementById('scholarshipDocFile').click()">
            <i class="fas fa-folder-plus text-primary" style="font-size: 1.75rem; margin-bottom: 0.35rem;"></i>
            <div style="font-weight: 600; font-size: 0.85rem;" id="docUploadLabel">Upload Latest Grade Slip, ITR & Recommendation Letter</div>
            <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.2rem;">PDF, DOCX, or scanned ZIP bundle (max 15MB)</div>
            <input type="file" id="scholarshipDocFile" style="display: none;" onchange="handleSchFile(event)">
          </div>
        </div>

        <label class="form-check">
          <input type="checkbox" id="schAgreementCheck" checked>
          <span>I certify that the information provided is accurate and grant permission to EduPortal to verify my financial & academic records.</span>
        </label>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" data-modal-close>Cancel</button>
        <button class="btn btn-primary" onclick="submitScholarshipApp()">
          <i class="fas fa-check-circle"></i> Submit Application
        </button>
      </div>
    </div>
  </div>

  <script src="../../js/main.js?v=9"></script>
  <script>
    function openGeneralApplyModal() {
      openApplyModal("Dean's Lister Scholarship", "100% Tuition + ₱5,000 stipend");
    }

    function openApplyModal(name, coverage) {
      document.getElementById('modalScholarshipName').value = name;
      openModal('scholarshipApplyModal');
    }

    function updateWordCount() {
      const text = document.getElementById('essayTextArea').value.trim();
      const words = text ? text.split(/\s+/).length : 0;
      document.getElementById('wordCountLabel').innerText = `${words} / 500 words`;
    }

    function handleSchFile(e) {
      const file = e.target.files[0];
      if (file) {
        document.getElementById('docUploadLabel').innerText = `Uploaded: ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
      }
    }

    function submitScholarshipApp() {
      const agreement = document.getElementById('schAgreementCheck');
      if (!agreement.checked) {
        showToast('Please check the certification box to proceed.', 'warning');
        return;
      }

      const schName = document.getElementById('modalScholarshipName').value;
      const refNo = 'SCH-2026-0' + Math.floor(100 + Math.random() * 900);

      closeModal('scholarshipApplyModal');

      // Add to status table
      const tbody = document.querySelector('#scholarshipStatusTable tbody');
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>
          <div style="font-weight: 700; color: var(--dark);">${schName}</div>
          <span style="font-size: 0.75rem; color: var(--gray-500);">Ref: ${refNo} • Just Submitted</span>
        </td>
        <td>Today</td>
        <td>
          <span class="badge badge-warning">
            <i class="fas fa-spinner fa-spin"></i> Under Review
          </span>
        </td>
        <td>
          <span class="badge badge-neutral" style="font-weight: 600; color: #B45309;">
            <i class="far fa-hourglass"></i> Pending
          </span>
        </td>
        <td style="text-align: right;">
          <button class="btn btn-sm btn-outline" onclick="showToast('Application Ref ${refNo} is queued for Committee evaluation.', 'info')">
            <i class="fas fa-eye"></i> View Details
          </button>
        </td>
      `;
      tbody.insertBefore(tr, tbody.firstChild);

      showToast(`Application for "${schName}" submitted successfully! Application Ref: ${refNo}`, 'success');
      
      const statusSec = document.getElementById('statusSection');
      if (statusSec) statusSec.scrollIntoView({ behavior: 'smooth' });
    }
  </script>
</body>
</html>
